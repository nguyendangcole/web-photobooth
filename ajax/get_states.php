<?php
// ajax/get_states.php
// API: Lấy danh sách states theo country_id

ini_set('display_errors', '0');
error_reporting(E_ALL);

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/db.php';

$countryId = isset($_GET['country_id']) ? (int)$_GET['country_id'] : 0;

if ($countryId <= 0) {
  http_response_code(400);
  echo json_encode([
    'success' => false,
    'error' => 'country_id is required'
  ]);
  exit;
}

try {
  $pdo = db();
  
  // Lấy states của country được chọn
  $stmt = $pdo->prepare("
    SELECT id, name, country_id, country_code, iso2 
    FROM states 
    WHERE country_id = ?
    ORDER BY name ASC
  ");
  
  $stmt->execute([$countryId]);
  $states = $stmt->fetchAll(PDO::FETCH_ASSOC);
  
  echo json_encode([
    'success' => true,
    'data' => $states,
    'total' => count($states)
  ]);
  
} catch (Exception $e) {
  http_response_code(500);
  echo json_encode([
    'success' => false,
    'error' => $e->getMessage()
  ]);
}

