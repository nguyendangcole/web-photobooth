<?php
// ajax/photobook_list.php
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
    require __DIR__ . '/../config/db.php';
    $pdo = db();

    $page  = max(1, (int)($_GET['page'] ?? 1));
    $limit = max(1, min(100, (int)($_GET['limit'] ?? 50)));
    $offset = ($page - 1) * $limit;

    $layout = $_GET['layout'] ?? null;
    $params = [];
    $where  = '';

    if ($layout === 'square' || $layout === 'vertical') {
        $where = 'WHERE layout = :l';
        $params[':l'] = $layout;
    }

    // total
    $sqlCount = "SELECT COUNT(*) FROM photobook_pages $where";
    $stmtC = $pdo->prepare($sqlCount);
    foreach ($params as $k=>$v) $stmtC->bindValue($k, $v);
    $stmtC->execute();
    $total = (int)$stmtC->fetchColumn();

    // data
    $sql = "
        SELECT id, layout, image_path, created_at
        FROM photobook_pages
        $where
        ORDER BY id DESC
        LIMIT :lim OFFSET :off
    ";
    $stmt = $pdo->prepare($sql);
    foreach ($params as $k=>$v) $stmt->bindValue($k, $v);
    $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
    $stmt->execute();

    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $root = dirname(__DIR__);

    // Lọc chỉ các record có file tồn tại và chuẩn hoá url cho FE
    $validRows = [];
    foreach ($rows as $r) {
        $imagePath = (string)$r['image_path'];
        $fullPath = $root . '/' . $imagePath;
        
        // Chỉ thêm vào kết quả nếu file tồn tại
        if (file_exists($fullPath)) {
            $r['url'] = preg_replace('#^public/#','', $imagePath);
            $validRows[] = $r;
        } else {
            // Log warning nếu file không tồn tại
            error_log("photobook_list: File not found - {$fullPath} (ID: {$r['id']})");
        }
    }
    
    $rows = $validRows;

    echo json_encode([
        'success' => true,
        'page'    => $page,
        'limit'   => $limit,
        'total'   => $total,
        'data'    => $rows,
    ]);
} catch (Throwable $e) {
    http_response_code(400);
    echo json_encode(['success'=>false,'error'=>$e->getMessage()]);
}
