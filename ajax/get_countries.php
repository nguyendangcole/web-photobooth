<?php
// ajax/get_countries.php
// API: Lấy danh sách tất cả countries

ini_set('display_errors', '0');
error_reporting(E_ALL);

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/db.php';

try {
  $pdo = db();
  
  // Lấy tất cả countries, sắp xếp theo tên
  $stmt = $pdo->query("
    SELECT id, name, iso2, iso3, phonecode 
    FROM countries 
    ORDER BY name ASC
  ");
  
  $countries = $stmt->fetchAll(PDO::FETCH_ASSOC);
  
  echo json_encode([
    'success' => true,
    'data' => $countries,
    'total' => count($countries)
  ]);
  
} catch (Exception $e) {
  http_response_code(500);
  echo json_encode([
    'success' => false,
    'error' => $e->getMessage()
  ]);
}

