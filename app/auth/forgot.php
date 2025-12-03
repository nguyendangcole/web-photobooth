<?php
// app/auth/forgot.php
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

$msg = '';
$err = '';

// ---- Get & normalize 'next' (destination after reset) ----
$next = $_GET['next'] ?? $_POST['next'] ?? '?p=photobook';

// Only accept internal URL format:
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

// (Optional but recommended) avoid cache for forgot page
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

// ---- Submit form ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!csrf_verify($_POST['_csrf'] ?? null)) {
    $err = 'Invalid session. Please try again.';
  } else {
    $email = trim($_POST['email'] ?? '');
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $err = 'Invalid email address.';
    } else {
      $stmt = db()->prepare("SELECT * FROM users WHERE email=? LIMIT 1");
      $stmt->execute([$email]);
      $u = $stmt->fetch();
      if ($u) {
        // Generate 6-digit OTP
        $otp = str_pad((string)rand(100000, 999999), 6, '0', STR_PAD_LEFT);
        $exp = (new DateTime('+15 minutes'))->format('Y-m-d H:i:s');
        
        // Store OTP in reset_token field (we'll use it for OTP)
        db()->prepare("UPDATE users SET reset_token=?, reset_expires_at=? WHERE id=?")
            ->execute([$otp, $exp, $u['id']]);
        
        // Send OTP via email
        $otpLink = BASE_URL . '?p=verify-otp&email=' . urlencode($email);
        $emailBody = "
          <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;'>
            <h2 style='color: #333;'>Password Reset Request</h2>
            <p>Hello {$u['name']},</p>
            <p>You have requested to reset your password. Please use the following OTP code:</p>
            <div style='background-color: #f4f4f4; padding: 20px; text-align: center; margin: 20px 0; border-radius: 5px;'>
              <h1 style='color: #007bff; font-size: 32px; letter-spacing: 5px; margin: 0;'>{$otp}</h1>
            </div>
            <p>This code will expire in 15 minutes.</p>
            <p>If you did not request this password reset, please ignore this email.</p>
            <p style='color: #666; font-size: 12px; margin-top: 30px;'>Or click this link to verify: <a href='{$otpLink}'>{$otpLink}</a></p>
          </div>
        ";
        
        $emailSent = send_mail($email, 'Password Reset OTP - PhotoBooth', $emailBody);
        
        if ($emailSent) {
          // Store email in session for OTP verification page
          $_SESSION['reset_email'] = $email;
          // Redirect to verify OTP page
          redirect('?p=verify-otp&email=' . urlencode($email) . '&next=' . urlencode($next));
          exit;
        } else {
          // In development, show error if email failed to send
          $isDev = strtolower(envx('APP_ENV', 'dev')) === 'dev';
          if ($isDev) {
            $err = 'Failed to send email. Please check SMTP configuration in .env file. See config/ENV_EMAIL_SETUP.md for help.';
          } else {
            // In production, still show success for security
            $msg = 'If an account with that email exists, we have sent password reset instructions.';
          }
        }
      } else {
        // Always show success message (security: don't reveal if email exists)
        $msg = 'If an account with that email exists, we have sent password reset instructions.';
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
  <title>Forgot Password | PhotoBooth</title>
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

            <h3 class="fw-normal mb-3 pb-1">Forgot Password</h3>
            <p class="small mb-4 text-white-50">Enter your email address and we'll send you an OTP code to reset your password.</p>

            <?php if (!empty($err)): ?>
              <div class="alert alert-danger"><?= htmlspecialchars($err) ?></div>
            <?php endif; ?>

            <?php if (!empty($msg)): ?>
              <div class="alert alert-info"><?= htmlspecialchars($msg) ?></div>
            <?php endif; ?>

            <div class="mb-4">
              <label class="form-label">Email</label>
              <input
                name="email"
                type="email"
                class="form-control form-control-lg"
                required
                value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES) ?>"
                placeholder="Enter your email address"
              >
            </div>

            <div class="pt-1 mb-4 d-grid">
              <button class="btn btn-info btn-lg" type="submit">Send OTP Code</button>
            </div>

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
