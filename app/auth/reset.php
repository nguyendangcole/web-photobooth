<?php
// app/auth/reset.php
require_once __DIR__ . '/../config.php';

// Session đã được init trong config.php
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

$token = $_GET['token'] ?? '';
$email = $_GET['email'] ?? '';
$err = '';
$ok = '';

// Normalize BASE_URL
if (!defined('BASE_URL')) {
  $base = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? '/public/index.php'), '/\\') . '/';
  define('BASE_URL', $base === '//' ? '/' : $base);
}

// Get & normalize 'next' (destination after reset)
$next = $_GET['next'] ?? $_POST['next'] ?? '?p=photobook';
$allow1 = '/^(?:\?p=[a-z0-9_\-]+(?:[&=a-z0-9_\-%,]*)?|index\.php\?p=[a-z0-9_\-]+(?:[&=a-z0-9_\-%,]*)?)$/i';
$allow2 = '#^/(?:index\.php)?\?p=[a-z0-9_\-]+(?:[&=a-z0-9_\-%,]*)?$#i';
if (!preg_match($allow1, $next) && !preg_match($allow2, $next)) {
  $next = '?p=photobook';
}

// Avoid cache
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

// If no token/email, redirect to forgot page
if (empty($token) || empty($email)) {
  redirect('?p=forgot&next=' . urlencode($next));
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!csrf_verify($_POST['_csrf'] ?? null)) {
    $err = 'Invalid session. Please try again.';
  } else {
    $token = $_POST['token'] ?? '';
    $email = $_POST['email'] ?? '';
    $pass = $_POST['password'] ?? '';
    $pass2 = $_POST['password2'] ?? '';
    
    if (empty($pass) || strlen($pass) < 6) {
      $err = 'Password must be at least 6 characters.';
    } elseif ($pass !== $pass2) {
      $err = 'Password confirmation does not match.';
    } else {
      // Verify OTP token with email
      $stmt = db()->prepare("SELECT * FROM users WHERE email=? AND reset_token=? AND reset_expires_at>NOW() LIMIT 1");
      $stmt->execute([$email, $token]);
      $u = $stmt->fetch();
      
      if (!$u) {
        $err = 'Invalid or expired OTP code. Please request a new one.';
      } else {
        $hash = password_hash($pass, PASSWORD_DEFAULT);
        db()->prepare("UPDATE users SET password_hash=?, reset_token=NULL, reset_expires_at=NULL WHERE id=?")
           ->execute([$hash, $u['id']]);
        $ok = 'Password reset successfully. Redirecting to login...';
        // Clear reset email from session
        unset($_SESSION['reset_email']);
        
        // Redirect to login after 2 seconds
        header("Refresh: 2; url=" . BASE_URL . "?p=login&next=" . urlencode($next));
      }
    }
  }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <link rel="icon" type="image/png" href="<?= asset('images/S.png') ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Reset Password | PhotoBooth</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="<?= asset('css/auth.css') ?>?v=<?= time() ?>">
</head>
<body>

<section class="auth-page auth-container">
  <div class="container-fluid g-0">
    <div class="row g-0">
      <!-- Left -->
      <div class="col-12 col-md-6 auth-left bg-left">
        <div class="auth-left-inner px-4 px-lg-5">
          <form method="post" style="width:23rem" autocomplete="off">
            <input type="hidden" name="_csrf" value="<?= csrf_token() ?>">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
            <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
            <input type="hidden" name="next" value="<?= htmlspecialchars($next, ENT_QUOTES) ?>">

            <h3 class="fw-normal mb-3 pb-1">Reset Password</h3>
            <p class="small mb-4 text-white-50">Enter your new password below.</p>

            <?php if (!empty($err)): ?>
              <div class="alert alert-danger"><?= htmlspecialchars($err) ?></div>
            <?php endif; ?>

            <?php if (!empty($ok)): ?>
              <div class="alert alert-success"><?= htmlspecialchars($ok) ?></div>
            <?php endif; ?>

            <?php if (empty($ok)): ?>
            <div class="mb-4">
              <label class="form-label">New Password</label>
              <input
                name="password"
                type="password"
                class="form-control form-control-lg"
                minlength="6"
                required
                placeholder="Enter new password (min 6 characters)"
                autocomplete="new-password"
              >
            </div>

            <div class="mb-4">
              <label class="form-label">Confirm Password</label>
              <input
                name="password2"
                type="password"
                class="form-control form-control-lg"
                minlength="6"
                required
                placeholder="Re-enter new password"
                autocomplete="new-password"
              >
            </div>

            <div class="pt-1 mb-4 d-grid">
              <button class="btn btn-info btn-lg" type="submit">Reset Password</button>
            </div>
            <?php endif; ?>

            <p class="small mb-3">
              <a href="<?= BASE_URL ?>?p=login&next=<?= urlencode($next) ?>" class="text-white">Back to Login</a>
            </p>

            <p>
              Don't have an account?
              <a href="<?= BASE_URL ?>?p=register&next=<?= urlencode($next) ?>" class="link-info">Sign up</a>
            </p>
          </form>
        </div>
      </div>

      <!-- Right (slideshow) -->
      <div class="col-12 col-md-6 d-none d-md-block position-relative">
        <div class="slideshow-container h-100">
          <div class="slide" style="background-image:url('<?= asset('images/bg1.png') ?>');"></div>
          <div class="slide" style="background-image:url('<?= asset('images/bg2.png') ?>');"></div>
          <div class="slide" style="background-image:url('<?= asset('images/bg3.png') ?>');"></div>
          <div class="slide" style="background-image:url('<?= asset('images/bg4.png') ?>');"></div>
          <div class="slide" style="background-image:url('<?= asset('images/bg5.png') ?>');"></div>
        </div>
      </div>
    </div>
  </div>
</section>

<script src="<?= asset('js/auth.js') ?>"></script>
</body>
</html>
