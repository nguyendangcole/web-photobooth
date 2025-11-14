<?php
// app/includes/auth_guard.php
// BẮT BUỘC: include file này ở đầu các trang/endpoint riêng tư (trước mọi output)

if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}

// Nếu đã đăng nhập thì cho qua
if (!empty($_SESSION['user']['id'])) {
  return;
}

// Kiểm tra xem có phải AJAX request không
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
$isJsonRequest = isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false;
$isAjaxEndpoint = strpos($_SERVER['REQUEST_URI'] ?? '', '/ajax/') !== false;

// Nếu là AJAX request hoặc JSON request, trả về JSON error
if ($isAjax || $isJsonRequest || $isAjaxEndpoint) {
  http_response_code(401);
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode(['success' => false, 'error' => 'Unauthorized. Please login.']);
  exit;
}

// Chưa đăng nhập → lưu trang đích & điều hướng về login
// Tối giản: chỉ lưu lại phần query "?p=...&..." để hợp với router
$qs = $_SERVER['QUERY_STRING'] ?? '';
$currP = null;
parse_str($qs, $qsArr);
if (isset($qsArr['p'])) {
  $currP = $qsArr['p'];
}

// Đừng ghi đè return_to khi đang ở login/register để tránh vòng lặp
$skipPages = ['login', 'register', 'oauth-google', 'oauth-facebook'];
if (!in_array($currP, $skipPages, true)) {
  $_SESSION['return_to'] = $qs ? ('?' . $qs) : '?p=studio';
}

// Điều hướng về trang login, kèm tham số next để form login giữ lại
$next = $_SESSION['return_to'] ?? '?p=studio';

// Lưu ý: vì đây là trang HTML/endpoint riêng tư, các header no-store
// hãy set tại chính trang/endpoint sau khi require guard (để dễ kiểm soát Content-Type)

http_response_code(302);
header('Location: ?p=login&next=' . urlencode($next));
exit;
