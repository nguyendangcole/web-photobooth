<?php
// app/auth/login.php
require_once __DIR__ . '/../config.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}

// ---- Flash error (nếu có) ----
$err = $_SESSION['_flash_err'] ?? '';
unset($_SESSION['_flash_err']);

// ---- Lấy & chuẩn hoá 'next' (điểm đến sau khi login) ----
$next = $_GET['next'] ?? $_POST['next'] ?? '?p=photobook';

// Chỉ chấp nhận URL nội bộ dạng:
//   1) "?p=..." hoặc "index.php?p=..."
//   2) "/?p=..." hoặc "/index.php?p=..." (bắt đầu bằng "/")
//   + cho phép thêm tham số [&=a-z0-9_-% ,] an toàn
$allow1 = '/^(?:\?p=[a-z0-9_\-]+(?:[&=a-z0-9_\-%,]*)?|index\.php\?p=[a-z0-9_\-]+(?:[&=a-z0-9_\-%,]*)?)$/i';
$allow2 = '#^/(?:index\.php)?\?p=[a-z0-9_\-]+(?:[&=a-z0-9_\-%,]*)?$#i';

if (!preg_match($allow1, $next) && !preg_match($allow2, $next)) {
  $next = '?p=photobook';
}

// ---- Chuẩn hoá BASE_URL ----
if (!defined('BASE_URL')) {
  $base = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? '/public/index.php'), '/\\') . '/';
  define('BASE_URL', $base === '//' ? '/' : $base);
}

// (Tùy chọn nhưng khuyến nghị) tránh cache cho trang đăng nhập
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
      // Đăng nhập thành công
      // (Nếu login_user đã có regenerate_id thì có thể bỏ dòng dưới)
      if (session_status() !== PHP_SESSION_ACTIVE) session_start();
      session_regenerate_id(true);

      login_user($user);

      // Ưu tiên quay về URL guard lưu; nếu không có dùng $next
      $to = $_SESSION['return_to'] ?? $next;
      unset($_SESSION['return_to']);

      // Chuẩn hoá URL đích để luôn là nội bộ
      if (strpos($to, '?p=') === 0) {
        $to = BASE_URL . $to; // "?p=..."
      } elseif (stripos($to, 'index.php?p=') === 0) {
        $to = rtrim(BASE_URL, '/') . '/' . $to; // "index.php?p=..."
      } elseif (preg_match($allow2, $to)) {
        // "/?p=..." hoặc "/index.php?p=..."
        // Có thể giữ nguyên; nếu muốn ép về BASE_URL:
        // $to = rtrim(BASE_URL, '/') . $to;
      } else {
        // fallback an toàn
        $to = BASE_URL . '?p=home';
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
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Đăng nhập | PhotoBooth</title>
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
            <!-- Bảo toàn 'next' qua POST -->
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
