<?php
// app/auth/login.php
require_once __DIR__ . '/../config.php';

// Session đã được init trong config.php, chỉ cần check
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

// ---- Flash error (if any) ----
$err = $_SESSION['_flash_err'] ?? '';
unset($_SESSION['_flash_err']);

// ---- Get & normalize 'next' (destination after login) ----
$next = $_GET['next'] ?? $_POST['next'] ?? '?p=photobook';

// Only accept internal URL format:
//   1) "?p=..." or "index.php?p=..."
//   2) "/?p=..." or "/index.php?p=..." (starts with "/")
//   + allow additional parameters [&=a-z0-9_-% ,] safely
$allow1 = '/^(?:\?p=[a-z0-9_\-]+(?:[&=a-z0-9_\-%,]*)?|index\.php\?p=[a-z0-9_\-]+(?:[&=a-z0-9_\-%,]*)?)$/i';
$allow2 = '#^/(?:index\.php)?\?p=[a-z0-9_\-]+(?:[&=a-z0-9_\-%,]*)?$#i';

if (!preg_match($allow1, $next) && !preg_match($allow2, $next)) {
  $next = '?p=photobook';
}

// ---- Normalize BASE_URL ----
if (!defined('BASE_URL')) {
  $base = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? '/public/index.php'), '/\\') . '/';
  define('BASE_URL', $base === '//' ? '/' : $base);
}

// (Optional but recommended) avoid cache for login page
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

// ---- Submit form ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // CSRF
  if (!csrf_verify($_POST['_csrf'] ?? null)) {
    $_SESSION['_flash_err'] = 'Invalid session. Please try again.';
    header('Location: ' . BASE_URL . '?p=login&next=' . urlencode($next));
    exit;
  }

  $email = trim($_POST['email'] ?? '');
  $pass  = $_POST['password'] ?? '';

  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $err = 'Invalid email address.';
  } elseif (strlen($pass) < 6) {
    $err = 'Password must be at least 6 characters.';
  }

  if (!$err) {
    $stmt = db()->prepare("SELECT * FROM users WHERE email=? LIMIT 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && !empty($user['password_hash']) && password_verify($pass, $user['password_hash'])) {
      // Login successful
      // (If login_user already has regenerate_id, can remove line below)
      // Session đã được init trong config.php
      session_regenerate_id(true);

      login_user($user);

      // Priority return to URL saved by guard; if not available use $next
      $to = $_SESSION['return_to'] ?? $next;
      unset($_SESSION['return_to']);

      // Normalize destination URL to always be internal
      if (strpos($to, '?p=') === 0) {
        $to = BASE_URL . $to; // "?p=..."
      } elseif (stripos($to, 'index.php?p=') === 0) {
        $to = rtrim(BASE_URL, '/') . '/' . $to; // "index.php?p=..."
      } elseif (preg_match($allow2, $to)) {
        // "/?p=..." or "/index.php?p=..."
        // Can keep as is; if want to force to BASE_URL:
        // $to = rtrim(BASE_URL, '/') . $to;
      } else {
        // safe fallback
        $to = BASE_URL . '?p=studio';
      }

      header('Location: ' . $to);
      exit;
    } else {
      $err = 'Incorrect email or password.';
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
  <title>Login | PhotoBooth</title>
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
            <!-- Preserve 'next' through POST -->
            <input type="hidden" name="next" value="<?= htmlspecialchars($next, ENT_QUOTES) ?>">

            <h3 class="fw-normal mb-3 pb-1">Login</h3>

            <?php if (!empty($err)): ?>
              <div class="alert alert-danger"><?= htmlspecialchars($err) ?></div>
            <?php endif; ?>

            <div class="mb-4">
              <label class="form-label">Email</label>
              <input
                name="email"
                type="email"
                class="form-control form-control-lg"
                required
                value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES) ?>"
              >
            </div>

            <div class="mb-4">
              <label class="form-label">Password</label>
              <input
                name="password"
                type="password"
                class="form-control form-control-lg"
                required
              >
            </div>

            <div class="pt-1 mb-4 d-grid">
              <button class="btn btn-info btn-lg" type="submit">Login</button>
            </div>

            <p class="small mb-3">
              <a href="<?= BASE_URL ?>?p=forgot&next=<?= urlencode($next) ?>" class="text-white">Forgot your password?</a>
            </p>

            <p>
              New account?
              <a href="<?= BASE_URL ?>?p=register&next=<?= urlencode($next) ?>" class="link-info">Sign up</a>
            </p>

            <hr class="my-3">
            <div class="d-flex gap-2">
              <a class="btn btn-danger"
                 href="<?= BASE_URL ?>?p=oauth-google&next=<?= urlencode($next) ?>">Google</a>
              <a class="btn btn-primary" style="background:#1877F2;border-color:#1877F2"
                 href="<?= BASE_URL ?>?p=oauth-facebook&next=<?= urlencode($next) ?>">Facebook</a>
            </div>
          </form>
        </div>
      </div>

      <!-- Right (slideshow) -->
      <div class="col-12 col-md-6 d-none d-md-block position-relative">
        <div class="slideshow-container h-100">
          <div class="slide" style="background-image:url('<?= asset('images/background-gradient-1.png') ?>');"></div>
          <div class="slide" style="background-image:url('<?= asset('images/background-gradient-2.png') ?>');"></div>
          <div class="slide" style="background-image:url('<?= asset('images/background-gradient-3.png') ?>');"></div>
          <div class="slide" style="background-image:url('<?= asset('images/background-gradient-4.png') ?>');"></div>
          <div class="slide" style="background-image:url('<?= asset('images/background-gradient-5.png') ?>');"></div>
        </div>
      </div>
    </div>
  </div>
</section>

<script src="<?= asset('js/auth.js') ?>"></script>
</body>
</html>
