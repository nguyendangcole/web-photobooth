<?php
/**
 * app/config.php
 * - Load ENV (.env qua phpdotenv nếu có, fallback parser tay nếu không)
 * - DB (PDO)
 * - Session & helpers (csrf, redirect, current_user, login/logout)
 * - OAuth constants (Google/Facebook) lấy từ ENV
 * - GOOGLE_REDIRECT_URI / FB_REDIRECT_URI: ENV override, DEFAULT = localhost:8888/WEB-PHOTOBOOTH/...
 * - send_mail(): ưu tiên SMTP/PHPMailer nếu bật, fallback mail()
 */

if (session_status() === PHP_SESSION_NONE) {
  ini_set('session.use_strict_mode', '1');
  ini_set('session.cookie_httponly', '1');
  if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
    ini_set('session.cookie_secure', '1');
  }
  session_start();
}

/* ========= Project root & vendor ========= */
$PROJECT_ROOT = dirname(__DIR__);
$autoloads = [
  $PROJECT_ROOT . '/vendor/autoload.php',
  dirname($PROJECT_ROOT) . '/vendor/autoload.php'
];
foreach ($autoloads as $auto) {
  if (file_exists($auto)) { require_once $auto; break; }
}

/* ========= Load .env ========= */
if (!function_exists('envx')) {
  function _fallback_load_dotenv($root) {
    $envFile = $root . '/.env';
    if (!is_file($envFile)) return;
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
      $line = trim($line);
      if ($line === '' || $line[0] === '#') continue;
      if (!str_contains($line, '=')) continue;
      [$k, $v] = explode('=', $line, 2);
      $k = trim($k); $v = trim($v);
      if ((str_starts_with($v, '"') && str_ends_with($v, '"')) ||
          (str_starts_with($v, "'") && str_ends_with($v, "'"))) {
        $v = substr($v, 1, -1);
      }
      $_ENV[$k] = $v;
      $_SERVER[$k] = $v;
      putenv("$k=$v");
    }
  }
  if (class_exists(\Dotenv\Dotenv::class)) {
    \Dotenv\Dotenv::createImmutable($PROJECT_ROOT)->safeLoad();
  } else {
    _fallback_load_dotenv($PROJECT_ROOT);
  }
  function envx(string $key, $default = null, bool $required = false) {
    $v = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);
    if ($v === false || $v === null || $v === '') {
      if ($required) throw new RuntimeException("Missing env: $key");
      return $default;
    }
    return $v;
  }
  function bool_env(string $key, $default = false): bool {
    $v = envx($key, $default ? '1' : '0');
    if (is_bool($v)) return $v;
    $v = strtolower((string)$v);
    return in_array($v, ['1','on','true','yes'], true);
  }
}

/* ========= Timezone ========= */
date_default_timezone_set(envx('APP_TZ', 'Asia/Ho_Chi_Minh'));

/* ========= BASE_URL ========= */
if (!defined('BASE_URL')) {
  $autoBase = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? '/'), '/\\') . '/';
  define('BASE_URL', rtrim(envx('BASE_URL', $autoBase), '/') . '/');
}

/* ========= DB Config ========= */
define('DB_HOST', envx('DB_HOST', '127.0.0.1'));
define('DB_NAME', envx('DB_NAME', 'myapp'));
define('DB_USER', envx('DB_USER', 'root'));
define('DB_PASS', envx('DB_PASS', ''));
define('DB_PORT', envx('DB_PORT', '3306'));
define('DB_CHARSET', envx('DB_CHARSET', 'utf8mb4'));

function db(): PDO {
  static $pdo;
  if ($pdo) return $pdo;
  $dsn = "mysql:host=".DB_HOST.";port=".DB_PORT.";dbname=".DB_NAME.";charset=".DB_CHARSET;
  $pdo = new PDO($dsn, DB_USER, DB_PASS, [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
  ]);
  return $pdo;
}

/* ========= Auth / OAuth ========= */
$requiredOauth = strtolower(envx('APP_ENV', 'dev')) === 'prod';

define('GOOGLE_CLIENT_ID',     envx('GOOGLE_CLIENT_ID',     null, $requiredOauth));
define('GOOGLE_CLIENT_SECRET', envx('GOOGLE_CLIENT_SECRET', null, $requiredOauth));
define('FB_APP_ID',            envx('FB_APP_ID',            null, $requiredOauth));
define('FB_APP_SECRET',        envx('FB_APP_SECRET',        null, $requiredOauth));

/* ENV override, DEFAULT = các URL bạn yêu cầu */
define('GOOGLE_REDIRECT_URI',
  envx('GOOGLE_REDIRECT_URI', 'http://localhost:8888/WEB-PHOTOBOOTH/public/index.php?p=oauth-google-callback')
);
define('FB_REDIRECT_URI',
  envx('FB_REDIRECT_URI', 'http://localhost:8888/WEB-PHOTOBOOTH/public/index.php?p=oauth-facebook-callback')
);

/* ========= CSRF & Session helpers ========= */
function csrf_token(): string {
  if (empty($_SESSION['_csrf'])) $_SESSION['_csrf'] = bin2hex(random_bytes(16));
  return $_SESSION['_csrf'];
}
function csrf_verify($token): bool {
  return isset($_SESSION['_csrf']) && is_string($token) && hash_equals($_SESSION['_csrf'], $token);
}
function redirect(string $path): void {
  if (!preg_match('~^https?://~', $path)) {
    if (str_starts_with($path, '?')) $path = BASE_URL . 'index.php' . $path;
    else $path = BASE_URL . ltrim($path, '/');
  }
  header("Location: ".$path);
  exit;
}
function current_user(): ?array {
  $user = $_SESSION['user'] ?? null;
  if (!$user) return null;
  
  // Load lại từ database để đảm bảo có avatar_url mới nhất (nếu có từ OAuth)
  if (!empty($user['id'])) {
    try {
      $stmt = db()->prepare("SELECT avatar_url, provider FROM users WHERE id = ? LIMIT 1");
      $stmt->execute([$user['id']]);
      $dbUser = $stmt->fetch();
      if ($dbUser) {
        // Nếu database có avatar_url, dùng nó (ưu tiên avatar từ OAuth như Gmail)
        if (!empty($dbUser['avatar_url'])) {
          $user['avatar_url'] = $dbUser['avatar_url'];
          $_SESSION['user']['avatar_url'] = $dbUser['avatar_url']; // Cập nhật session
        } elseif (empty($user['avatar_url']) && !empty($user['email'])) {
          // Nếu không có avatar_url trong database, tạo Gravatar từ email
          $emailHash = md5(strtolower(trim($user['email'])));
          $user['avatar_url'] = "https://www.gravatar.com/avatar/{$emailHash}?d=identicon&s=200";
        }
      }
    } catch (Exception $e) {
      // Silent fail, dùng session data
    }
  }
  
  // Fallback: nếu vẫn không có avatar_url, tạo Gravatar từ email
  if (empty($user['avatar_url']) && !empty($user['email'])) {
    $emailHash = md5(strtolower(trim($user['email'])));
    $user['avatar_url'] = "https://www.gravatar.com/avatar/{$emailHash}?d=identicon&s=200";
  }
  
  return $user;
}
function login_user(array $user): void {
  // Giữ nguyên avatar_url từ database (nếu có từ OAuth như Google/Facebook)
  // Chỉ tạo Gravatar nếu không có avatar_url và có email
  $avatarUrl = $user['avatar_url'] ?? null;
  if (!$avatarUrl && !empty($user['email'])) {
    // Tạo Gravatar URL từ email (fallback cho local accounts hoặc OAuth không có avatar)
    $emailHash = md5(strtolower(trim($user['email'])));
    $avatarUrl = "https://www.gravatar.com/avatar/{$emailHash}?d=identicon&s=200";
  }
  
  $_SESSION['user'] = [
    'id'         => $user['id'],
    'name'       => $user['name'],
    'email'      => $user['email'],
    'avatar_url' => $avatarUrl, // Giữ nguyên avatar từ OAuth nếu có
    'provider'   => $user['provider'] ?? 'local',
  ];
}
function logout_user(): void { unset($_SESSION['user']); }

/* ========= Email (SMTP optional) ========= */
function send_mail(string $to, string $subject, string $html): bool {
  $useSmtp = bool_env('SMTP_ENABLED', false);
  if ($useSmtp && class_exists(\PHPMailer\PHPMailer\PHPMailer::class)) {
    try {
      $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
      $mail->isSMTP();
      $mail->Host       = envx('SMTP_HOST', 'smtp.gmail.com');
      $mail->SMTPAuth   = true;
      $mail->Username   = envx('SMTP_USER', '');
      $mail->Password   = envx('SMTP_PASS', '');
      $secure           = strtolower(envx('SMTP_SECURE', 'tls'));
      if ($secure === 'ssl') {
        $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = (int) envx('SMTP_PORT', '465');
      } else {
        $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = (int) envx('SMTP_PORT', '587');
      }
      $fromEmail = envx('SMTP_FROM', 'no-reply@example.com');
      $fromName  = envx('SMTP_FROM_NAME', 'Photobooth');
      $mail->setFrom($fromEmail, $fromName);
      $mail->addAddress($to);
      $mail->isHTML(true);
      $mail->Subject = $subject;
      $mail->Body    = $html;
      $mail->send();
      return true;
    } catch (\Throwable $e) {
      error_log('[SMTP] '.$e->getMessage());
      return false;
    }
  }
  $headers = "MIME-Version: 1.0\r\nContent-type:text/html;charset=UTF-8\r\n";
  $headers .= "From: ". (envx('SMTP_FROM_NAME', 'Photobooth')) ." <". (envx('SMTP_FROM','no-reply@example.com')) .">\r\n";
  return @mail($to, $subject, $html, $headers);
}

/* ========= Polyfills ========= */
if (!function_exists('str_starts_with')) {
  function str_starts_with($haystack, $needle){ return (string)$needle !== '' && strncmp($haystack,$needle,strlen($needle))===0; }
}
if (!function_exists('str_ends_with')) {
  function str_ends_with($haystack, $needle){ return $needle === '' || substr($haystack, -strlen($needle)) === (string)$needle; }
}
if (!function_exists('str_contains')) {
  function str_contains($haystack, $needle){ return $needle !== '' && mb_strpos($haystack, $needle) !== false; }
}

/* ========= Asset helper ========= */
if (!function_exists('asset')) {
    function asset(string $path): string {
      return BASE_URL . ltrim($path, '/');
    }
  }

  /* ========= Extra auth helpers (optional) ========= */
/**
 * Trả về user hiện tại nếu có (ưu tiên $_SESSION['user'], fallback $_SESSION['auth_user'])
 * Dùng song song với current_user() đang có, không xung đột.
 */
if (!function_exists('current_user_or_null')) {
  function current_user_or_null(): ?array {
    return $_SESSION['user'] ?? ($_SESSION['auth_user'] ?? null);
  }
}

/**
 * Yêu cầu đăng nhập cho các trang cần bảo vệ.
 * Gọi ở đầu mỗi page cần login: require_login_or_redirect();
 */
if (!function_exists('require_login_or_redirect')) {
  function require_login_or_redirect(): void {
    if (!current_user_or_null()) {
      // (tuỳ chọn) có thể set flash để báo lý do
      $_SESSION['_flash_err'] = $_SESSION['_flash_err'] ?? 'Vui lòng đăng nhập để tiếp tục.';
      redirect('?p=login');
      exit;
    }
  }
}

  