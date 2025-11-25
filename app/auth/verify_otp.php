<?php
// app/auth/verify_otp.php
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

// Get email from GET or session
$email = $_GET['email'] ?? $_SESSION['reset_email'] ?? '';
$email = trim($email);

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

// If no email, redirect to forgot page
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
  redirect('?p=forgot');
  exit;
}

// Store email in session
$_SESSION['reset_email'] = $email;

// ---- Submit form ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!csrf_verify($_POST['_csrf'] ?? null)) {
    $err = 'Invalid session. Please try again.';
  } else {
    $action = $_POST['action'] ?? 'verify';
    
    // Debug: Log action
    $isDev = strtolower(envx('APP_ENV', 'dev')) === 'dev';
    if ($isDev) {
      error_log("[OTP Debug] ========== POST REQUEST ==========");
      error_log("[OTP Debug] Action received: " . $action);
      error_log("[OTP Debug] POST data: " . print_r($_POST, true));
    }
    
    // IMPORTANT: Only process resend if action is explicitly 'resend'
    if ($action === 'resend') {
      // Resend OTP
      $stmt = db()->prepare("SELECT * FROM users WHERE email=? LIMIT 1");
      $stmt->execute([$email]);
      $u = $stmt->fetch();
      if ($u) {
        // Generate new 6-digit OTP
        $newOtp = str_pad((string)rand(100000, 999999), 6, '0', STR_PAD_LEFT);
        $exp = (new DateTime('+15 minutes'))->format('Y-m-d H:i:s');
        
        db()->prepare("UPDATE users SET reset_token=?, reset_expires_at=? WHERE id=?")
            ->execute([$newOtp, $exp, $u['id']]);
        
        $otpLink = BASE_URL . '?p=verify-otp&email=' . urlencode($email);
        $emailBody = "
          <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;'>
            <h2 style='color: #333;'>Password Reset Request</h2>
            <p>Hello {$u['name']},</p>
            <p>You have requested a new OTP code. Please use the following code:</p>
            <div style='background-color: #f4f4f4; padding: 20px; text-align: center; margin: 20px 0; border-radius: 5px;'>
              <h1 style='color: #007bff; font-size: 32px; letter-spacing: 5px; margin: 0;'>{$newOtp}</h1>
            </div>
            <p>This code will expire in 15 minutes.</p>
            <p>If you did not request this password reset, please ignore this email.</p>
            <p style='color: #666; font-size: 12px; margin-top: 30px;'>Or click this link to verify: <a href='{$otpLink}'>{$otpLink}</a></p>
          </div>
        ";
        
        send_mail($email, 'Password Reset OTP - PhotoBooth', $emailBody);
        $msg = 'A new OTP code has been sent to your email.';
      }
    } else {
      // Verify OTP - only process if action is 'verify' (default)
      $otp = trim($_POST['otp'] ?? '');
      
      if ($isDev) {
        error_log("[OTP Verify] Starting verification process");
        error_log("[OTP Verify] Raw OTP input: '" . ($_POST['otp'] ?? '') . "'");
      }
      
      // Normalize OTP: remove all non-numeric characters and ensure 6 digits
      $otp = preg_replace('/\D/', '', $otp); // Remove all non-digits
      $otp = str_pad($otp, 6, '0', STR_PAD_LEFT); // Ensure 6 digits
      
      if ($isDev) {
        error_log("[OTP Verify] Normalized OTP: '{$otp}'");
      }
      
      if (empty($otp) || strlen($otp) !== 6 || !ctype_digit($otp)) {
        if ($isDev) {
          error_log("[OTP Verify] Invalid OTP format");
        }
        $err = 'Please enter a valid 6-digit OTP code.';
      } else {
        // First, get user to check current OTP in database
        $stmt = db()->prepare("SELECT id, reset_token, reset_expires_at FROM users WHERE email=? LIMIT 1");
        $stmt->execute([$email]);
        $u = $stmt->fetch();
        
        if (!$u) {
          $err = 'Email not found. Please request a new OTP code.';
        } elseif (empty($u['reset_token'])) {
          $err = 'No OTP code found. Please request a new OTP code.';
        } else {
          // Check if OTP matches (exact string comparison)
          $dbOtp = trim($u['reset_token']);
          $inputOtp = trim($otp);
          
          // Debug logging
          if ($isDev) {
            error_log("[OTP Verify] Email: {$email}");
            error_log("[OTP Verify] Input OTP (raw): '" . ($_POST['otp'] ?? '') . "'");
            error_log("[OTP Verify] Input OTP (normalized): '{$inputOtp}'");
            error_log("[OTP Verify] DB OTP: '{$dbOtp}'");
            error_log("[OTP Verify] DB OTP length: " . strlen($dbOtp));
            error_log("[OTP Verify] Input OTP length: " . strlen($inputOtp));
            error_log("[OTP Verify] Match: " . ($dbOtp === $inputOtp ? 'YES' : 'NO'));
            error_log("[OTP Verify] DB OTP hex: " . bin2hex($dbOtp));
            error_log("[OTP Verify] Input OTP hex: " . bin2hex($inputOtp));
          }
          
          // Check expiration time
          $now = new DateTime();
          $expiresAt = $u['reset_expires_at'] ? new DateTime($u['reset_expires_at']) : null;
          
          if ($expiresAt && $expiresAt < $now) {
            if ($isDev) {
              error_log("[OTP Verify] Expired - Now: " . $now->format('Y-m-d H:i:s') . ", Expires: " . $expiresAt->format('Y-m-d H:i:s'));
            }
            $err = 'OTP code has expired. Please request a new code.';
          } elseif ($dbOtp !== $inputOtp) {
            $err = 'Invalid OTP code. Please check and try again.';
          } else {
            // OTP verified - redirect to reset password page with token
            if ($isDev) {
              error_log("[OTP Verify] SUCCESS - Redirecting to reset page");
            }
            redirect('?p=reset&token=' . urlencode($otp) . '&email=' . urlencode($email) . '&next=' . urlencode($next));
            exit;
          }
        }
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
  <title>Verify OTP | PhotoBooth</title>
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
          <form method="post" style="width:23rem" autocomplete="off" id="verify-form">
            <input type="hidden" name="_csrf" value="<?= csrf_token() ?>">
            <input type="hidden" name="next" value="<?= htmlspecialchars($next, ENT_QUOTES) ?>">
            <input type="hidden" name="action" value="verify" id="action-verify">

            <h3 class="fw-normal mb-3 pb-1">Verify OTP Code</h3>
            <p class="small mb-4 text-white-50">We've sent a 6-digit OTP code to <strong><?= htmlspecialchars($email) ?></strong>. Please enter it below.</p>

            <?php if (!empty($err)): ?>
              <div class="alert alert-danger"><?= htmlspecialchars($err) ?></div>
            <?php endif; ?>

            <?php if (!empty($msg)): ?>
              <div class="alert alert-info">
                <?= htmlspecialchars($msg) ?>
                <?php if (strpos($msg, 'new OTP') !== false): ?>
                  <br><small>Please check your email for the new code.</small>
                <?php endif; ?>
              </div>
            <?php endif; ?>

            <div class="mb-4">
              <label class="form-label">OTP Code</label>
              <input
                name="otp"
                type="text"
                class="form-control form-control-lg text-center"
                required
                maxlength="6"
                pattern="[0-9]{6}"
                placeholder="000000"
                style="font-size: 24px; letter-spacing: 8px;"
                autocomplete="off"
                value=""
                id="otp-input"
              >
              <small class="text-white-50">Enter the 6-digit code sent to your email</small>
            </div>

            <div class="pt-1 mb-4 d-grid">
              <button class="btn btn-info btn-lg" type="submit" name="verify_btn" id="verify-btn" value="verify">Verify OTP</button>
            </div>
          </form>

          <div class="text-center mb-3">
            <form method="post" style="display: inline;" id="resend-form">
              <input type="hidden" name="_csrf" value="<?= csrf_token() ?>">
              <input type="hidden" name="action" value="resend" id="action-resend">
              <input type="hidden" name="next" value="<?= htmlspecialchars($next, ENT_QUOTES) ?>">
              <button type="submit" class="btn btn-link text-white-50" style="text-decoration: none;" id="resend-btn">Resend OTP Code</button>
            </form>
          </div>

          <p class="small mb-3">
            <a href="<?= BASE_URL ?>?p=forgot&next=<?= urlencode($next) ?>" class="text-white">Back to Forgot Password</a>
          </p>

          <p>
            Remember your password?
            <a href="<?= BASE_URL ?>?p=login&next=<?= urlencode($next) ?>" class="link-info">Sign in</a>
          </p>
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
<script>
// Auto-focus and format OTP input
document.addEventListener('DOMContentLoaded', function() {
  const otpInput = document.querySelector('input[name="otp"]');
  const verifyForm = document.querySelector('form[method="post"]:not(#resend-form)');
  const resendForm = document.getElementById('resend-form');
  const verifyBtn = document.getElementById('verify-btn');
  const resendBtn = document.getElementById('resend-btn');
  
  if (otpInput) {
    otpInput.focus();
    otpInput.addEventListener('input', function(e) {
      // Only allow numbers and limit to 6 digits
      e.target.value = e.target.value.replace(/\D/g, '').substring(0, 6);
    });
    
    // Clear input when page loads if there's a resend message
    <?php if (!empty($msg) && strpos($msg, 'new OTP') !== false): ?>
      otpInput.value = '';
      otpInput.focus();
    <?php endif; ?>
  }
  
  // Prevent double submission and ensure correct form submission
  if (verifyForm) {
    verifyForm.addEventListener('submit', function(e) {
      // Ensure action is verify
      const actionInput = document.getElementById('action-verify');
      if (actionInput) {
        actionInput.value = 'verify';
      }
      
      // Ensure OTP input has value
      if (!otpInput || !otpInput.value || otpInput.value.length !== 6) {
        e.preventDefault();
        alert('Please enter a valid 6-digit OTP code.');
        return false;
      }
      
      // Log for debugging
      console.log('[Verify Form] Submitting with action:', actionInput ? actionInput.value : 'unknown');
      console.log('[Verify Form] OTP value:', otpInput ? otpInput.value : 'empty');
      console.log('[Verify Form] Form ID:', verifyForm.id);
      
      if (verifyBtn && !verifyBtn.disabled) {
        verifyBtn.disabled = true;
        verifyBtn.textContent = 'Verifying...';
      }
    });
  }
  
  if (resendForm) {
    resendForm.addEventListener('submit', function(e) {
      // Ensure action is resend
      const actionInput = document.getElementById('action-resend');
      if (actionInput) {
        actionInput.value = 'resend';
      }
      
      console.log('[Resend Form] Submitting with action:', actionInput ? actionInput.value : 'unknown');
      console.log('[Resend Form] Form ID:', resendForm.id);
      
      if (resendBtn && !resendBtn.disabled) {
        resendBtn.disabled = true;
        resendBtn.textContent = 'Sending...';
      }
    });
  }
  
  // Prevent form resend from being triggered when clicking verify button
  if (verifyBtn) {
    verifyBtn.addEventListener('click', function(e) {
      console.log('[Verify Button] Clicked');
      const actionInput = document.getElementById('action-verify');
      if (actionInput) {
        actionInput.value = 'verify';
        console.log('[Verify Button] Action set to:', actionInput.value);
      }
    });
  }
});
</script>
</body>
</html>

