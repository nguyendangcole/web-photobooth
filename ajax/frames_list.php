<?php
// ajax/frames_list.php

// Tắt hiển thị lỗi để tránh HTML error xuất hiện trong JSON response
ini_set('display_errors', '0');
error_reporting(E_ALL);

require __DIR__ . '/../config/db.php';
session_start();

header('Content-Type: application/json; charset=utf-8');

try {
  $pdo = db();

  // Kiểm tra xem user có premium không
  $isPremiumUser = false;
  if (!empty($_SESSION['user']['id'])) {
    $stmt = $pdo->prepare("SELECT is_premium, premium_until FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user']['id']]);
    $userInfo = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($userInfo) {
      // Kiểm tra premium còn hạn không (nếu có set premium_until)
      $isPremiumUser = (bool)$userInfo['is_premium'];
      if ($isPremiumUser && $userInfo['premium_until']) {
        $expiryDate = new DateTime($userInfo['premium_until']);
        $now = new DateTime();
        if ($now > $expiryDate) {
          $isPremiumUser = false; // hết hạn
        }
      }
    }
  }

  $q = isset($_GET['q']) ? trim($_GET['q']) : '';
  $layout = isset($_GET['layout']) ? trim($_GET['layout']) : '';

  // Chỉ cho phép 2 layout hợp lệ
  $allowedLayouts = ['vertical', 'square'];
  $hasLayout = in_array($layout, $allowedLayouts, true);

  $sql = "SELECT id, name, src, layout, is_premium FROM frames";
  $where = [];
  $params = [];

  if ($q !== '') {
    $where[] = "name LIKE :q";
    $params[':q'] = "%$q%";
  }

  if ($hasLayout) {
    $where[] = "layout = :layout";
    $params[':layout'] = $layout;
  }

  // THAY ĐỔI: Free user VẪN THẤY tất cả frame (bao gồm premium)
  // Nhưng sẽ không dùng được frame premium (check ở frontend)
  // Premium user thì dùng được tất cả

  if ($where) {
    $sql .= " WHERE " . implode(" AND ", $where);
  }

  $sql .= " ORDER BY is_premium DESC, id DESC"; // premium frames lên đầu

  $stmt = $pdo->prepare($sql);
  $stmt->execute($params);
  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

  echo json_encode([
    'success' => true, 
    'data' => $rows,
    'user_premium' => $isPremiumUser
  ]);
} catch (Throwable $e) {
  echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
