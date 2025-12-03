<?php
// ajax/photobook_delete.php
declare(strict_types=1);
require __DIR__ . '/../app/includes/auth_guard.php';
// chặn cache cho nội dung riêng tư
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');


ini_set('display_errors', '0');
error_reporting(E_ALL);
header('Content-Type: application/json; charset=utf-8');

try {
    // 1) Kết nối DB: đi từ /ajax/ -> /config/db.php
    require __DIR__ . '/../config/db.php';
    $pdo = db();

    // 2) Chỉ cho POST JSON
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'error' => 'Method not allowed']);
        exit;
    }

    // Get current user ID from session
    $userId = null;
    if (isset($_SESSION['user']['id'])) {
        $userId = (int)$_SESSION['user']['id'];
    }
    
    if (!$userId) {
        throw new Exception('User not authenticated');
    }

    $raw  = file_get_contents('php://input');
    $data = json_decode($raw, true);
    $id   = (int)($data['id'] ?? 0);
    if ($id <= 0) {
        throw new Exception('Invalid id');
    }

    // Check if user_id column exists
    $checkColumn = $pdo->query("SHOW COLUMNS FROM photobook_pages LIKE 'user_id'")->fetch();
    
    // 3) Lấy đường dẫn ảnh và verify ownership
    if ($checkColumn) {
        // Only allow deleting own photos
        $stmt = $pdo->prepare('SELECT image_path, user_id FROM photobook_pages WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            throw new Exception('Photo not found');
        }
        if ((int)$row['user_id'] !== $userId) {
            throw new Exception('You do not have permission to delete this photo');
        }
    } else {
        // Backward compatibility: allow delete if no user_id column
        $stmt = $pdo->prepare('SELECT image_path FROM photobook_pages WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            throw new Exception('Photo not found');
        }
    }

    // 4) Xoá record (with user_id check if column exists)
    if ($checkColumn) {
        $pdo->prepare('DELETE FROM photobook_pages WHERE id = :id AND user_id = :user_id')
            ->execute([':id' => $id, ':user_id' => $userId]);
    } else {
        $pdo->prepare('DELETE FROM photobook_pages WHERE id = :id')->execute([':id' => $id]);
    }

    // 5) Xoá file vật lý và update storage_used (best-effort)
    // Trong photobook_add.php bạn lưu kiểu: public/photobook/2025/11/xxx.png
    // => absolute path = ROOT / (image_path)
    $abs = dirname(__DIR__) . '/' . $row['image_path'];
    $fileSize = 0;
    if (is_file($abs)) {
        $fileSize = filesize($abs); // Get file size before deleting
        @unlink($abs);
        
        // Update user storage_used (decrease by file size)
        if ($fileSize > 0 && $checkColumn) {
            try {
                $updateStmt = $pdo->prepare("UPDATE users SET storage_used = GREATEST(0, storage_used - ?) WHERE id = ?");
                $updateStmt->execute([$fileSize, $userId]);
            } catch (Throwable $e) {
                error_log('photobook_delete: Failed to update storage_used: ' . $e->getMessage());
                // Don't throw - deletion succeeded, storage update is secondary
            }
        }
    }

    echo json_encode(['success' => true]);
} catch (Throwable $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
