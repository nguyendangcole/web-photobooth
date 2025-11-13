<?php
// admin/includes/admin_guard.php
// Bảo vệ trang admin - chỉ admin mới vào được

if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}

// Kiểm tra đã login chưa
if (empty($_SESSION['user']['id'])) {
  header('Location: ../public/?p=login');
  exit;
}

// Kiểm tra role admin
require_once __DIR__ . '/../../config/db.php';
$pdo = db();

$userId = $_SESSION['user']['id'];
$stmt = $pdo->prepare("SELECT is_admin FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || empty($user['is_admin'])) {
  // Không phải admin
  header('HTTP/1.1 403 Forbidden');
  echo '<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>403 - Access Denied</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container">
    <div class="row justify-content-center align-items-center" style="min-height: 100vh;">
      <div class="col-md-6 text-center">
        <div class="card shadow">
          <div class="card-body py-5">
            <h1 class="display-1 text-danger">403</h1>
            <h2 class="mb-3">Access Denied</h2>
            <p class="text-muted mb-4">Bạn không có quyền truy cập trang này. Chỉ admin mới có thể vào Admin Panel.</p>
            <a href="../public/?p=home" class="btn btn-primary">← Về trang chủ</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>';
  exit;
}

// Admin OK - cho phép tiếp tục
?>

