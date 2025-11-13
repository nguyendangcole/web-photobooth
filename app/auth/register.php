<?php
require_once __DIR__ . '/../config.php';

$err = '';
$name = $email = ''; // giữ lại giá trị đã nhập (tránh mất form khi có lỗi)

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // 0) CSRF check
  if (!csrf_verify($_POST['_csrf'] ?? null)) {
    $err = 'Phiên không hợp lệ.';
  }

  // 1) Lấy dữ liệu & trim
  $name  = trim($_POST['name'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $pass  = $_POST['password']  ?? '';
  $pass2 = $_POST['password2'] ?? '';

  // 2) Validate cơ bản
  if (!$err && strlen($name) < 2) {
    $err = 'Tên quá ngắn.';
  }
  if (!$err && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $err = 'Email không hợp lệ.';
  }

  // 3) Kiểm tra độ mạnh mật khẩu (mục 1)
  if (!$err) {
    if (strlen($pass) < 8) {
      $err = 'Mật khẩu phải có ít nhất 8 ký tự.';
    } elseif (
      !preg_match('/[A-Z]/', $pass) ||      // ít nhất 1 chữ hoa
      !preg_match('/[a-z]/', $pass) ||      // ít nhất 1 chữ thường
      !preg_match('/[0-9]/', $pass) ||      // ít nhất 1 số
      !preg_match('/[^A-Za-z0-9]/', $pass)  // ít nhất 1 ký tự đặc biệt
    ) {
      $err = 'Mật khẩu phải bao gồm chữ hoa, chữ thường, số và ký tự đặc biệt.';
    }
  }

  // 4) Khớp mật khẩu
  if (!$err && $pass !== $pass2) {
    $err = 'Mật khẩu xác nhận không khớp.';
  }

  // 5) Email đã tồn tại?
  if (!$err) {
    $stmt = db()->prepare("SELECT 1 FROM users WHERE email=? LIMIT 1");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
      $err = 'Email đã tồn tại.';
    }
  }

  // 6) Giới hạn đăng ký theo IP trong 1 giờ (mục 4)
  //    Cần cột users.ip_address và users.created_at
  if (!$err) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? null;
    try {
      $stmt = db()->prepare("SELECT COUNT(*) FROM users WHERE ip_address = ? AND created_at >= NOW() - INTERVAL 1 HOUR");
      $stmt->execute([$ip]);
      $countRecent = (int)$stmt->fetchColumn();
      if ($countRecent >= 5) {
        $err = 'Bạn đã đăng ký quá nhiều lần. Vui lòng thử lại sau.';
      }
    } catch (Exception $e) {
      // Không lộ thông tin nhạy cảm; bỏ qua nếu schema chưa nâng cấp
      // error_log('[register-ip-limit] ' . $e->getMessage());
    }
  }

  // 7) Tạo user
  if (!$err) {
    // Hash mật khẩu (mục 2: ưu tiên Argon2ID nếu có)
    if (defined('PASSWORD_ARGON2ID')) {
      $hash = password_hash($pass, PASSWORD_ARGON2ID);
    } else {
      $hash = password_hash($pass, PASSWORD_DEFAULT);
    }

    $ip = $_SERVER['REMOTE_ADDR'] ?? null;

    // Cố gắng insert kèm ip_address; nếu chưa có cột thì fallback
    try {
      $stmt = db()->prepare("
        INSERT INTO users(name,email,password_hash,provider,ip_address)
        VALUES(?,?,?, 'local', ?)
      ");
      $stmt->execute([$name, $email, $hash, $ip]);
    } catch (Exception $e) {
      // Fallback nếu chưa có cột ip_address
      // error_log('[register-insert] ' . $e->getMessage());
      $stmt = db()->prepare("
        INSERT INTO users(name,email,password_hash,provider)
        VALUES(?,?,?, 'local')
      ");
      $stmt->execute([$name, $email, $hash]);
    }

    $id = db()->lastInsertId();
    $user = ['id'=>$id,'name'=>$name,'email'=>$email,'avatar_url'=>null,'provider'=>'local'];

    if (session_status() !== PHP_SESSION_ACTIVE) {
      session_start();
    }
    session_regenerate_id(true);

    login_user($user);
    redirect('?p=home');
  }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Đăng ký | PhotoBooth</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="<?= asset('css/auth.css') ?>?v=<?= time() ?>">
<style>
  .form-text.small-muted { font-size: .9rem; color: #6c757d; }
</style>
</head>
<body>

<section class="auth-page auth-container">
  <div class="container-fluid g-0">
    <div class="row g-0">
      <!-- Left -->
      <div class="col-12 col-md-6 auth-left bg-left">
        <div class="auth-left-inner px-4 px-lg-5">
          <form method="post" style="width:23rem" autocomplete="off" novalidate>
            <input type="hidden" name="_csrf" value="<?= csrf_token() ?>">
            <h3 class="fw-normal mb-3 pb-1">Create new account</h3>

            <?php if ($err): ?>
              <div class="alert alert-danger"><?= htmlspecialchars($err, ENT_QUOTES) ?></div>
            <?php endif; ?>

            <div class="mb-4">
              <label class="form-label">Username</label>
              <input name="name" type="text" class="form-control form-control-lg"
                     value="<?= htmlspecialchars($name ?? '', ENT_QUOTES) ?>" required minlength="2">
            </div>

            <div class="mb-4">
              <label class="form-label">Email</label>
              <input name="email" type="email" class="form-control form-control-lg"
                     value="<?= htmlspecialchars($email ?? '', ENT_QUOTES) ?>" required>
            </div>

            <div class="mb-2">
              <label class="form-label">Password</label>
              <input id="password" name="password" type="password" class="form-control form-control-lg"
                     required minlength="8" autocomplete="new-password"
                   >
            </div>
            <div class="mb-4">
            <small class="form-text small-muted">
  Password must be <strong>at least 8 characters</strong> and include <strong>uppercase letters</strong>, <strong>lowercase letters</strong>, <strong>numbers</strong>, and <strong>special characters</strong>.
</small>

            </div>

            <div class="mb-4">
              <label class="form-label">Re-enter password</label>
              <input id="password2" name="password2" type="password" class="form-control form-control-lg"
                     required autocomplete="new-password">
            </div>

            <div class="pt-1 mb-4 d-grid">
              <button class="btn btn-info btn-lg btn-block" type="submit">Sign up</button>
            </div>

            <p>Do you already have an account?
              <a href="?p=login" class="link-info">Login</a>
            </p>
          </form>
        </div>
      </div>

      <!-- Right -->
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
