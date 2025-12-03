<?php
// ajax/photobook_add.php
declare(strict_types=1);

// Enable error logging
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

// Increase PHP limits for large image uploads
ini_set('post_max_size', '20M');
ini_set('upload_max_filesize', '20M');
ini_set('memory_limit', '256M');
ini_set('max_execution_time', '60');

// Require auth guard FIRST (nó sẽ tự start session và check login)
// auth_guard sẽ tự output JSON và exit nếu chưa login
require __DIR__ . '/../app/includes/auth_guard.php';

// Chặn cache cho nội dung riêng tư
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

// Set JSON header
header('Content-Type: application/json; charset=utf-8');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success'=>false,'error'=>'Method not allowed']); 
        exit;
    }

    // Kết nối DB - sử dụng config/db.php giống các AJAX file khác (port 8889 cho MAMP)
    require __DIR__ . '/../config/db.php';
    $pdo = db();
    
    if (!$pdo) {
        throw new Exception('Database connection failed');
    }

    // Body JSON
    $raw  = file_get_contents('php://input');
    if ($raw === false || empty($raw)) {
        throw new Exception('Empty request body');
    }
    
    $body = json_decode($raw, true);
    if (!is_array($body)) {
        error_log('photobook_add: Invalid JSON body. Raw length: ' . strlen($raw));
        throw new Exception('Invalid JSON body');
    }

    $dataURL = $body['image'] ?? '';
    if (empty($dataURL)) {
        throw new Exception('Image data is required');
    }
    
    $layout  = strtolower(trim($body['layout'] ?? 'square'));
    if (!in_array($layout, ['square','vertical'], true)) $layout = 'square';
    
    error_log('photobook_add: Processing request. Layout: ' . $layout . ', Image data length: ' . strlen($dataURL));

    // Support both PNG and JPEG dataURL formats
    if (!preg_match('#^data:image/(png|jpeg|jpg);base64,#', $dataURL)) {
        throw new Exception('Image must be PNG or JPEG dataURL');
    }

    $base64 = substr($dataURL, strpos($dataURL, ',') + 1);
    $bin = base64_decode($base64);
    if ($bin === false) throw new Exception('Cannot decode image');

    // Lưu file: /public/photobook/YYYY/MM/pb_....
    $root = dirname(__DIR__); // /WEB-PHOTOBOOTH
    $dir  = $root . '/public/photobook/' . date('Y/m');
    
    // Create directory if it doesn't exist
    if (!is_dir($dir)) {
        $created = @mkdir($dir, 0775, true);
        if (!$created && !is_dir($dir)) {
            throw new Exception('Cannot create directory: ' . $dir);
        }
    }
    
    // Check if directory is writable
    if (!is_writable($dir)) {
        throw new Exception('Directory is not writable: ' . $dir);
    }

    // Detect image type from binary data
    $imageType = 'jpg'; // default to jpg for JPEG images
    if (substr($bin, 0, 8) === "\x89\x50\x4E\x47\x0D\x0A\x1A\x0A") {
        $imageType = 'png';
    }
    
    $fname = 'pb_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $imageType;
    $path  = $dir . '/' . $fname;
    
    $saved = @file_put_contents($path, $bin);
    if ($saved === false) {
        error_log('photobook_add: Failed to save file to: ' . $path);
        throw new Exception('Cannot save image to: ' . $path);
    }
    
    error_log('photobook_add:File saved successfully. Path: ' . $path . ', Size: ' . $saved . ' bytes');

    // Get current user ID from session (auth_guard.php already checked login)
    $userId = null;
    if (isset($_SESSION['user']['id'])) {
        $userId = (int)$_SESSION['user']['id'];
    }
    
    if (!$userId) {
        throw new Exception('User not authenticated');
    }

    // ===== STORAGE LIMIT CHECK =====
    // Get user info (is_premium, storage_used)
    $userStmt = $pdo->prepare("SELECT is_premium, COALESCE(storage_used, 0) as storage_used FROM users WHERE id = ?");
    $userStmt->execute([$userId]);
    $userInfo = $userStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$userInfo) {
        throw new Exception('User not found');
    }
    
    $isPremium = !empty($userInfo['is_premium']);
    $currentStorage = (int)($userInfo['storage_used'] ?? 0);
    $fileSize = strlen($bin); // Size in bytes
    
    // Define limits: Free = 50MB or 50 photos, Premium = 500MB or 500 photos
    $maxStorageBytes = $isPremium ? (500 * 1024 * 1024) : (50 * 1024 * 1024); // 500MB or 50MB
    $maxPhotos = $isPremium ? 500 : 50;
    
    // Check photo count limit
    $photoCountStmt = $pdo->prepare("SELECT COUNT(*) FROM photobook_pages WHERE user_id = ?");
    $photoCountStmt->execute([$userId]);
    $photoCount = (int)$photoCountStmt->fetchColumn();
    
    if ($photoCount >= $maxPhotos) {
        throw new Exception('You have reached the maximum number of photos (' . $maxPhotos . '). Please upgrade to Premium for more storage.');
    }
    
    // Check storage limit (bytes)
    if (($currentStorage + $fileSize) > $maxStorageBytes) {
        $usedMB = round($currentStorage / (1024 * 1024), 1);
        $maxMB = round($maxStorageBytes / (1024 * 1024), 0);
        throw new Exception('Storage limit exceeded. You are using ' . $usedMB . 'MB of ' . $maxMB . 'MB. Please upgrade to Premium for more storage.');
    }
    
    // Calculate storage warning threshold (80% = warning, 90% = critical)
    $newStorage = $currentStorage + $fileSize;
    $storagePercent = ($newStorage / $maxStorageBytes) * 100;
    $needsWarning = $storagePercent >= 80;

    // Lưu DB (image_path lưu kèm prefix 'public/')
    $relPath = 'public/photobook/' . date('Y/m') . '/' . $fname;
    try {
        // Check if user_id column exists, if not use old query (backward compatibility)
        $checkColumn = $pdo->query("SHOW COLUMNS FROM photobook_pages LIKE 'user_id'")->fetch();
        if ($checkColumn) {
            $stmt = $pdo->prepare("INSERT INTO photobook_pages(image_path, layout, user_id) VALUES (?, ?, ?)");
            $stmt->execute([$relPath, $layout, $userId]);
        } else {
            // Fallback for old database structure
            $stmt = $pdo->prepare("INSERT INTO photobook_pages(image_path, layout) VALUES (?, ?)");
            $stmt->execute([$relPath, $layout]);
        }
        $id = (string)$pdo->lastInsertId();
        error_log('photobook_add: Database insert successful. ID: ' . $id . ', User ID: ' . $userId);
        
        // Update user storage_used
        $updateStorageStmt = $pdo->prepare("UPDATE users SET storage_used = ? WHERE id = ?");
        $updateStorageStmt->execute([$newStorage, $userId]);
        error_log('photobook_add: Updated storage_used to ' . $newStorage . ' bytes for user ' . $userId);
        
    } catch (Throwable $e) {
        error_log('photobook_add: Database insert failed: ' . $e->getMessage());
        throw new Exception('Database insert failed: ' . $e->getMessage());
    }

    // Trả thêm url (đã bỏ prefix public/)
    $url = preg_replace('#^public/#','', $relPath);

    // Return success with warning flag if storage is getting full
    echo json_encode([
        'success'=>true, 
        'id'=>$id, 
        'path'=>$relPath, 
        'url'=>$url,
        'storage_warning'=>$needsWarning,
        'storage_percent'=>round($storagePercent, 1),
        'is_premium'=>$isPremium
    ]);
} catch (Throwable $e) {
    // Log error for debugging
    error_log('photobook_add ERROR: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
    
    // Use 400 for client errors, 500 for server errors
    $code = strpos($e->getMessage(), 'Database') !== false || 
            strpos($e->getMessage(), 'Cannot create') !== false ||
            strpos($e->getMessage(), 'not writable') !== false ||
            strpos($e->getMessage(), 'Cannot save') !== false 
            ? 500 : 400;
    
    http_response_code($code);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
    exit;
}
