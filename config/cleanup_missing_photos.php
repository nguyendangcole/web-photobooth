<?php
/**
 * Script dọn dẹp các record photobook_pages không có file tương ứng
 * Chạy script này để xóa các record trong database mà file ảnh không tồn tại
 */

require __DIR__ . '/db.php';

$pdo = db();
$root = dirname(__DIR__);

echo "=== Dọn dẹp photobook_pages không có file ===\n\n";

// Lấy tất cả records
$stmt = $pdo->query("SELECT id, image_path FROM photobook_pages ORDER BY id");
$all = $stmt->fetchAll(PDO::FETCH_ASSOC);

$missing = [];
$exists = [];

foreach ($all as $row) {
    $imagePath = $row['image_path'];
    $fullPath = $root . '/' . $imagePath;
    
    if (!file_exists($fullPath)) {
        $missing[] = $row;
        echo "❌ ID {$row['id']}: File không tồn tại - {$imagePath}\n";
    } else {
        $exists[] = $row;
        echo "✅ ID {$row['id']}: File tồn tại - {$imagePath}\n";
    }
}

echo "\n=== Tổng kết ===\n";
echo "Tổng số records: " . count($all) . "\n";
echo "File tồn tại: " . count($exists) . "\n";
echo "File thiếu: " . count($missing) . "\n";

if (count($missing) > 0) {
    echo "\n=== Xóa các record không có file? ===\n";
    echo "Nhập 'yes' để xóa, hoặc bất kỳ gì khác để hủy: ";
    
    // Nếu chạy từ command line
    if (php_sapi_name() === 'cli') {
        $handle = fopen("php://stdin", "r");
        $line = trim(fgets($handle));
        fclose($handle);
        
        if (strtolower($line) === 'yes') {
            $ids = array_column($missing, 'id');
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $stmt = $pdo->prepare("DELETE FROM photobook_pages WHERE id IN ($placeholders)");
            $stmt->execute($ids);
            
            echo "\n✅ Đã xóa " . count($ids) . " record(s): " . implode(', ', $ids) . "\n";
        } else {
            echo "\n❌ Đã hủy. Không xóa gì cả.\n";
        }
    } else {
        // Nếu chạy từ web browser
        echo "\n⚠️  Vui lòng chạy script này từ command line để xóa.\n";
        echo "Hoặc xóa thủ công các ID sau:\n";
        foreach ($missing as $row) {
            echo "  DELETE FROM photobook_pages WHERE id = {$row['id']};\n";
        }
    }
} else {
    echo "\n✅ Tất cả file đều tồn tại. Không cần dọn dẹp.\n";
}

