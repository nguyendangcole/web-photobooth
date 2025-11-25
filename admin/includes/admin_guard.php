<?php
// admin/includes/admin_guard.php
// Protect admin pages - only admins can access

if (session_status() === PHP_SESSION_NONE) {
  if (function_exists('init_photobooth_session')) {
    init_photobooth_session();
  } else {
    session_name('PHOTOBOOTH_SESSION');
    $scriptPath = dirname($_SERVER['SCRIPT_NAME'] ?? '/');
    if (preg_match('#/(web-photobooth|Web-photobooth)(/.*)?$#i', $scriptPath, $matches)) {
      $cookiePath = '/' . $matches[1] . '/';
    } else {
      $cookiePath = rtrim($scriptPath, '/') . '/';
      if ($cookiePath === '//') $cookiePath = '/';
    }
    ini_set('session.cookie_path', $cookiePath);
    session_start();
  }
}

// Check if logged in
if (empty($_SESSION['user']['id'])) {
  header('Location: ../public/?p=login');
  exit;
}

// Check admin role
require_once __DIR__ . '/../../config/db.php';
$pdo = db();

$userId = $_SESSION['user']['id'];
$stmt = $pdo->prepare("SELECT is_admin FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || empty($user['is_admin'])) {
  // Not an admin
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
           <p class="text-muted mb-4">You do not have permission to access this page. Only admins can access the Admin Panel.</p>
            <a href="../public/?p=studio" class="btn btn-primary">← Back to Studio</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>';
  exit;
}

// Admin OK - allow to continue
?>

