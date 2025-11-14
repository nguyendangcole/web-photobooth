<?php
// app/router.php

// Định nghĩa đường dẫn tuyệt đối để tránh lạc đường dẫn
define('APP_PATH', __DIR__);                 // .../WEB-PHOTOBOOTH/app
define('ROOT_PATH', dirname(APP_PATH));      // .../WEB-PHOTOBOOTH
define('PUBLIC_PATH', ROOT_PATH . '/public');

// (Tuỳ chọn) BASE_URL nếu view cần dùng
if (!defined('BASE_URL')) {
  $base = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? '/public/index.php'), '/\\') . '/';
  define('BASE_URL', $base === '//' ? '/' : $base);
}

// Bản đồ route → file
$routes = [
  'studio'                => APP_PATH . '/main_menu.php',
  'home'                  => APP_PATH . '/main_menu.php', // Alias for backward compatibility
  'landing'               => APP_PATH . '/landing.php',
  'photobooth'            => APP_PATH . '/photobooth.php',
  'frame'                 => APP_PATH . '/frame.php',
  'frame-sidebar'         => APP_PATH . '/frame_sidebar.php',
  'photobook'             => APP_PATH . '/photobook.php',

  // auth
  'login'                 => APP_PATH . '/auth/login.php',
  'register'              => APP_PATH . '/auth/register.php',
  'logout'                => APP_PATH . '/auth/logout.php',
  'forgot'                => APP_PATH . '/auth/forgot.php',
  'reset'                 => APP_PATH . '/auth/reset.php',

  // oauth
  'oauth-google'          => APP_PATH . '/auth/oauth_google.php',
  'oauth-google-callback' => APP_PATH . '/auth/oauth_google_callback.php',
  'oauth-facebook'        => APP_PATH . '/auth/oauth_facebook.php',
  'oauth-facebook-callback'=> APP_PATH . '/auth/oauth_facebook_callback.php',
  
  // premium
  'premium-upgrade'       => APP_PATH . '/premium_upgrade.php',
  
  // profile
  'change-avatar'         => APP_PATH . '/change_avatar.php',
  
  // info pages
  'info'                  => APP_PATH . '/info.php',
  'service'               => APP_PATH . '/service.php',
  'qa'                    => APP_PATH . '/qa.php',
  'contact'               => APP_PATH . '/contact.php',
  'terms'                 => APP_PATH . '/terms.php',
  'privacy'               => APP_PATH . '/privacy.php',
];

// Trang được yêu cầu (?p=...)
$page = $_GET['p'] ?? 'studio';

// File 404 (nếu có)
$notFoundFile = APP_PATH . '/404.php';

// Hàm render an toàn
function render_file($filePath) {
  if (is_string($filePath) && is_file($filePath)) {
    require $filePath;
    return true;
  }
  return false;
}

// Điều hướng
if (isset($routes[$page]) && render_file($routes[$page])) {
  // ok
} else {
  http_response_code(404);
  if (!render_file($notFoundFile)) {
    // Fallback 404 tối thiểu khi thiếu app/404.php
    $home = htmlspecialchars(BASE_URL, ENT_QUOTES);
    echo "<!doctype html><meta charset='utf-8'><title>404</title>";
    echo "<div style='font-family:system-ui;max-width:680px;margin:48px auto;text-align:center'>";
    echo "<h1>404 – Not Found</h1>";
    echo "<p>The requested page could not be found.</p>";
    echo "<p><a href='{$home}'>Go home</a></p>";
    echo "</div>";
  }
}
