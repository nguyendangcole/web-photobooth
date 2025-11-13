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

    $raw  = file_get_contents('php://input');
    $data = json_decode($raw, true);
    $id   = (int)($data['id'] ?? 0);
    if ($id <= 0) {
        throw new Exception('Invalid id');
    }

    // 3) Lấy đường dẫn ảnh để xoá file
    $stmt = $pdo->prepare('SELECT image_path FROM photobook_pages WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        throw new Exception('Not found');
    }

    // 4) Xoá record
    $pdo->prepare('DELETE FROM photobook_pages WHERE id = :id')->execute([':id' => $id]);

    // 5) Xoá file vật lý (best-effort)
    // Trong photobook_add.php bạn lưu kiểu: public/photobook/2025/11/xxx.png
    // => absolute path = ROOT / (image_path)
    $abs = dirname(__DIR__) . '/' . $row['image_path'];
    if (is_file($abs)) {
        @unlink($abs);
    }

    echo json_encode(['success' => true]);
} catch (Throwable $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
