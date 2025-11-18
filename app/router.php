<?php
// app/router.php

// Define absolute paths to avoid path confusion
define('APP_PATH', __DIR__);                 // .../WEB-PHOTOBOOTH/app
define('ROOT_PATH', dirname(APP_PATH));      // .../WEB-PHOTOBOOTH
define('PUBLIC_PATH', ROOT_PATH . '/public');

// (Optional) BASE_URL if view needs to use
if (!defined('BASE_URL')) {
  $base = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? '/public/index.php'), '/\\') . '/';
  define('BASE_URL', $base === '//' ? '/' : $base);
}

// Route map → file
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

// Requested page (?p=...)
$page = $_GET['p'] ?? 'studio';

// 404 file (if exists)
$notFoundFile = APP_PATH . '/404.php';

// Safe render function
function render_file($filePath) {
  if (is_string($filePath) && is_file($filePath)) {
    require $filePath;
    return true;
  }
  return false;
}

// Routing
if (isset($routes[$page]) && render_file($routes[$page])) {
  // ok
} else {
  http_response_code(404);
  if (!render_file($notFoundFile)) {
    // Minimal 404 fallback when app/404.php is missing
    $home = htmlspecialchars(BASE_URL, ENT_QUOTES);
    echo "<!doctype html><meta charset='utf-8'><title>404</title>";
    echo "<div style='font-family:system-ui;max-width:680px;margin:48px auto;text-align:center'>";
    echo "<h1>404 – Not Found</h1>";
    echo "<p>The requested page could not be found.</p>";
    echo "<p><a href='{$home}'>Go home</a></p>";
    echo "</div>";
  }
}
