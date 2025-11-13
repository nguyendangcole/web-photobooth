<?php
// ajax/photobook_add.php
declare(strict_types=1);

// Tắt hiển thị lỗi để tránh HTML error xuất hiện trong JSON response
ini_set('display_errors', '0');
error_reporting(E_ALL);

require __DIR__ . '/../app/includes/auth_guard.php';
// chặn cache cho nội dung riêng tư
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');


header('Content-Type: application/json; charset=utf-8');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success'=>false,'error'=>'Method not allowed']); exit;
    }

    // Kết nối DB (từ /ajax → /config/db.php)
    require __DIR__ . '/../config/db.php';
    $pdo = db();

    // Body JSON
    $raw  = file_get_contents('php://input');
    $body = json_decode($raw, true);
    if (!is_array($body)) throw new Exception('Invalid JSON body');

    $dataURL = $body['image'] ?? '';
    $layout  = strtolower(trim($body['layout'] ?? 'square'));
    if (!in_array($layout, ['square','vertical'], true)) $layout = 'square';

    if (!preg_match('#^data:image/png;base64,#', $dataURL)) {
        throw new Exception('Image must be PNG dataURL');
    }

    $base64 = substr($dataURL, strpos($dataURL, ',') + 1);
    $bin = base64_decode($base64);
    if ($bin === false) throw new Exception('Cannot decode image');

    // Lưu file: /public/photobook/YYYY/MM/pb_....
    $root = dirname(__DIR__); // /WEB-PHOTOBOOTH
    $dir  = $root . '/public/photobook/' . date('Y/m');
    if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
        throw new Exception('Cannot create directory');
    }

    $fname = 'pb_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.png';
    $path  = $dir . '/' . $fname;
    if (file_put_contents($path, $bin) === false) throw new Exception('Cannot save image');

    // Lưu DB (image_path lưu kèm prefix 'public/')
    $relPath = 'public/photobook/' . date('Y/m') . '/' . $fname;
    $stmt = $pdo->prepare("INSERT INTO photobook_pages(image_path, layout) VALUES (?, ?)");
    $stmt->execute([$relPath, $layout]);
    $id = (string)$pdo->lastInsertId();

    // Trả thêm url (đã bỏ prefix public/)
    $url = preg_replace('#^public/#','', $relPath);

    echo json_encode(['success'=>true, 'id'=>$id, 'path'=>$relPath, 'url'=>$url]);
} catch (Throwable $e) {
    http_response_code(400);
    echo json_encode(['success'=>false,'error'=>$e->getMessage()]);
}
