<?php
// app/includes/auth_guard.php
// REQUIRED: include this file at the beginning of private pages/endpoints (before any output)

if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}

// If already logged in, allow through
if (!empty($_SESSION['user']['id'])) {
  return;
}

// Check if AJAX request
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

// Check Content-Type header - try multiple ways
$contentType = '';
if (isset($_SERVER['CONTENT_TYPE'])) {
  $contentType = $_SERVER['CONTENT_TYPE'];
} elseif (isset($_SERVER['HTTP_CONTENT_TYPE'])) {
  $contentType = $_SERVER['HTTP_CONTENT_TYPE'];
} elseif (function_exists('getallheaders')) {
  $headers = getallheaders();
  $contentType = $headers['Content-Type'] ?? $headers['content-type'] ?? '';
}
$isJsonRequest = strpos(strtolower($contentType), 'application/json') !== false;

$isAjaxEndpoint = strpos($_SERVER['REQUEST_URI'] ?? '', '/ajax/') !== false;

// If AJAX request or JSON request, return JSON error
if ($isAjax || $isJsonRequest || $isAjaxEndpoint) {
  // Clear any previous output before setting headers
  if (ob_get_level() > 0) {
    ob_clean();
  }
  http_response_code(401);
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode(['success' => false, 'error' => 'Unauthorized. Please login.']);
  exit;
}

// Not logged in → save destination page & redirect to login
// Minimal: only save query part "?p=...&..." to match with router
$qs = $_SERVER['QUERY_STRING'] ?? '';
$currP = null;
parse_str($qs, $qsArr);
if (isset($qsArr['p'])) {
  $currP = $qsArr['p'];
}

// Don't overwrite return_to when on login/register to avoid loop
$skipPages = ['login', 'register', 'oauth-google', 'oauth-facebook'];
if (!in_array($currP, $skipPages, true)) {
  $_SESSION['return_to'] = $qs ? ('?' . $qs) : '?p=studio';
}

// Redirect to login page, with next parameter for login form to keep
$next = $_SESSION['return_to'] ?? '?p=studio';

// Note: since this is private HTML page/endpoint, no-store headers
// should be set in the page/endpoint itself after requiring guard (for easier Content-Type control)

http_response_code(302);
header('Location: ?p=login&next=' . urlencode($next));
exit;
