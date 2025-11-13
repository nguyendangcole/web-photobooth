<?php
// ajax/premium_request.php
// Xử lý request nâng cấp premium

ini_set('display_errors', '0');
error_reporting(E_ALL);

require __DIR__ . '/../config/db.php';
require __DIR__ . '/../app/includes/auth_guard.php';

session_start();
header('Content-Type: application/json; charset=utf-8');

try {
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
  }

  $pdo = db();
  $userId = $_SESSION['user']['id'] ?? null;

  if (!$userId) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
  }

  $raw = file_get_contents('php://input');
  $data = json_decode($raw, true);
  $action = $data['action'] ?? '';

  if ($action === 'request_premium') {
    // Kiểm tra xem đã có request pending chưa
    $stmt = $pdo->prepare("SELECT id FROM premium_requests WHERE user_id = ? AND status = 'pending' LIMIT 1");
    $stmt->execute([$userId]);
    if ($stmt->fetch()) {
      echo json_encode(['success' => false, 'error' => 'Bạn đã có yêu cầu đang chờ xử lý.']);
      exit;
    }

    // Kiểm tra xem user đã là premium chưa
    $stmt = $pdo->prepare("SELECT is_premium, premium_until FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $userInfo = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($userInfo && $userInfo['is_premium']) {
      $isExpired = false;
      if ($userInfo['premium_until']) {
        $expiryDate = new DateTime($userInfo['premium_until']);
        $now = new DateTime();
        $isExpired = $now > $expiryDate;
      }
      
      if (!$isExpired) {
        echo json_encode(['success' => false, 'error' => 'Bạn đã là Premium user rồi!']);
        exit;
      }
    }

    // Tạo request mới
    $stmt = $pdo->prepare("INSERT INTO premium_requests (user_id, status) VALUES (?, 'pending')");
    $stmt->execute([$userId]);

    echo json_encode(['success' => true, 'message' => 'Yêu cầu đã được gửi thành công!']);
  } else {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid action']);
  }
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

