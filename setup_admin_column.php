<?php
/**
 * Script tạm thời để thêm cột is_admin vào database
 * Chạy file này MỘT LẦN rồi XÓA NGAY!
 */

require_once __DIR__ . '/config/db.php';

try {
  $pdo = db();
  
  echo "<h2>🔧 Setup Admin Column</h2>";
  echo "<hr>";
  
  // Kiểm tra xem cột đã tồn tại chưa
  $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'is_admin'");
  $exists = $stmt->fetch();
  
  if ($exists) {
    echo "<p>✅ Cột <code>is_admin</code> đã tồn tại!</p>";
  } else {
    // Thêm cột is_admin
    $pdo->exec("ALTER TABLE users 
                ADD COLUMN is_admin TINYINT(1) NOT NULL DEFAULT 0 
                COMMENT 'Admin role flag' 
                AFTER is_premium");
    echo "<p>✅ Đã thêm cột <code>is_admin</code> vào bảng <code>users</code>!</p>";
  }
  
  // Kiểm tra index
  $stmt = $pdo->query("SHOW INDEX FROM users WHERE Key_name = 'idx_is_admin'");
  $indexExists = $stmt->fetch();
  
  if (!$indexExists) {
    $pdo->exec("ALTER TABLE users ADD INDEX idx_is_admin (is_admin)");
    echo "<p>✅ Đã thêm index <code>idx_is_admin</code>!</p>";
  } else {
    echo "<p>✅ Index <code>idx_is_admin</code> đã tồn tại!</p>";
  }
  
  echo "<hr>";
  echo "<h3>📋 Danh sách Admin hiện tại:</h3>";
  
  $stmt = $pdo->query("SELECT id, name, email, is_admin, is_premium FROM users WHERE is_admin = 1");
  $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
  
  if (empty($admins)) {
    echo "<p>⚠️ Chưa có admin nào. Hãy chạy SQL sau để cấp quyền admin:</p>";
    echo "<pre style='background:#f5f5f5; padding:10px; border-radius:5px;'>";
    echo "UPDATE users SET is_admin = 1 WHERE email = 'your_email@example.com';";
    echo "</pre>";
  } else {
    echo "<table border='1' cellpadding='8' style='border-collapse:collapse;'>";
    echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Admin</th><th>Premium</th></tr>";
    foreach ($admins as $admin) {
      echo "<tr>";
      echo "<td>{$admin['id']}</td>";
      echo "<td>" . htmlspecialchars($admin['name']) . "</td>";
      echo "<td>" . htmlspecialchars($admin['email']) . "</td>";
      echo "<td>" . ($admin['is_admin'] ? '✅' : '❌') . "</td>";
      echo "<td>" . ($admin['is_premium'] ? '⭐' : '-') . "</td>";
      echo "</tr>";
    }
    echo "</table>";
  }
  
  echo "<hr>";
  echo "<h3>✅ Setup hoàn tất!</h3>";
  echo "<p>🔐 Bạn có thể truy cập Admin Panel tại: <a href='admin/index.php'>admin/index.php</a></p>";
  echo "<p><strong>⚠️ QUAN TRỌNG:</strong> Hãy <strong>XÓA FILE NÀY</strong> ngay sau khi setup xong!</p>";
  
} catch (PDOException $e) {
  echo "<p style='color:red;'>❌ Lỗi: " . htmlspecialchars($e->getMessage()) . "</p>";
  echo "<p>Hãy kiểm tra lại kết nối database trong file <code>config/db.php</code></p>";
}
?>

