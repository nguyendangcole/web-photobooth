<?php
/**
 * Script kiểm tra trạng thái admin của user hiện tại
 * Chạy file này để debug
 */

session_start();
require_once __DIR__ . '/config/db.php';

echo "<h2>🔍 Kiểm tra Admin Status</h2>";
echo "<hr>";

// Kiểm tra session
echo "<h3>1. Session Info:</h3>";
if (empty($_SESSION['user'])) {
  echo "<p style='color:red;'>❌ Chưa login! Hãy <a href='public/?p=login'>login</a> trước.</p>";
  exit;
}

$sessionUser = $_SESSION['user'];
echo "<pre style='background:#f5f5f5; padding:10px; border-radius:5px;'>";
echo "User ID: " . ($sessionUser['id'] ?? 'N/A') . "\n";
echo "Email: " . ($sessionUser['email'] ?? 'N/A') . "\n";
echo "Name: " . ($sessionUser['name'] ?? 'N/A') . "\n";
echo "</pre>";

// Kiểm tra database
echo "<h3>2. Database Info:</h3>";
try {
  $pdo = db();
  $userId = $sessionUser['id'] ?? null;
  
  if (!$userId) {
    echo "<p style='color:red;'>❌ Không có user ID trong session!</p>";
    exit;
  }
  
  $stmt = $pdo->prepare("SELECT id, name, email, is_admin, is_premium FROM users WHERE id = ?");
  $stmt->execute([$userId]);
  $dbUser = $stmt->fetch(PDO::FETCH_ASSOC);
  
  if (!$dbUser) {
    echo "<p style='color:red;'>❌ Không tìm thấy user trong database với ID: $userId</p>";
    exit;
  }
  
  echo "<table border='1' cellpadding='8' style='border-collapse:collapse;'>";
  echo "<tr><th>Field</th><th>Value</th></tr>";
  echo "<tr><td>ID</td><td>{$dbUser['id']}</td></tr>";
  echo "<tr><td>Name</td><td>" . htmlspecialchars($dbUser['name']) . "</td></tr>";
  echo "<tr><td>Email</td><td>" . htmlspecialchars($dbUser['email']) . "</td></tr>";
  echo "<tr><td>is_admin</td><td><strong>" . ($dbUser['is_admin'] ? '✅ YES (1)' : '❌ NO (0)') . "</strong></td></tr>";
  echo "<tr><td>is_premium</td><td>" . ($dbUser['is_premium'] ? '⭐ YES' : '❌ NO') . "</td></tr>";
  echo "</table>";
  
  echo "<hr>";
  
  if (empty($dbUser['is_admin'])) {
    echo "<h3 style='color:red;'>❌ User này CHƯA có quyền admin!</h3>";
    echo "<p>Hãy chạy SQL sau để cấp quyền admin:</p>";
    echo "<pre style='background:#fff3cd; padding:10px; border-radius:5px; border-left:4px solid #ffc107;'>";
    echo "UPDATE users SET is_admin = 1 WHERE email = '" . htmlspecialchars($dbUser['email']) . "';";
    echo "</pre>";
  } else {
    echo "<h3 style='color:green;'>✅ User này ĐÃ có quyền admin!</h3>";
    echo "<p>Nếu vẫn bị denied, có thể do:</p>";
    echo "<ul>";
    echo "<li>Session cũ chưa được update</li>";
    echo "<li>Cần logout và login lại</li>";
    echo "</ul>";
    echo "<p><a href='?logout=1' style='background:#dc3545; color:white; padding:8px 16px; text-decoration:none; border-radius:5px;'>🔓 Logout và Login lại</a></p>";
  }
  
} catch (Exception $e) {
  echo "<p style='color:red;'>❌ Lỗi: " . htmlspecialchars($e->getMessage()) . "</p>";
}

// Xử lý logout
if (isset($_GET['logout'])) {
  session_destroy();
  echo "<script>alert('Đã logout! Hãy login lại.'); window.location.href='public/?p=login';</script>";
  exit;
}

echo "<hr>";
echo "<p><a href='admin/index.php'>🔐 Thử truy cập Admin Panel</a></p>";
?>

