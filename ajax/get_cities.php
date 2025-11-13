<?php
// ajax/get_cities.php
// API: Lấy danh sách cities theo state_id hoặc country_id từ JSON file

ini_set('display_errors', '0');
error_reporting(E_ALL);

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/db.php';

$stateId = isset($_GET['state_id']) ? (int)$_GET['state_id'] : 0;
$countryId = isset($_GET['country_id']) ? (int)$_GET['country_id'] : 0;

if ($stateId <= 0 && $countryId <= 0) {
  http_response_code(400);
  echo json_encode([
    'success' => false,
    'error' => 'state_id or country_id is required'
  ]);
  exit;
}

try {
  // Đọc file cities.json
  $jsonFile = __DIR__ . '/../config/cities.json';
  
  if (!file_exists($jsonFile)) {
    throw new Exception('cities.json file not found');
  }
  
  // Đọc và decode JSON
  $jsonContent = file_get_contents($jsonFile);
  $allCities = json_decode($jsonContent, true);
  
  if ($allCities === null) {
    throw new Exception('Invalid JSON in cities.json');
  }
  
  $filteredCities = [];
  
  if ($stateId > 0) {
    // Filter cities theo state_id
    $filteredCities = array_filter($allCities, function($city) use ($stateId) {
      return isset($city['state_id']) && (int)$city['state_id'] === $stateId;
    });
  } elseif ($countryId > 0) {
    // Nếu không có state_id, lấy cities từ country_id
    // Cần lấy tất cả state_ids của country này
    $pdo = db();
    $stmt = $pdo->prepare("SELECT id FROM states WHERE country_id = ?");
    $stmt->execute([$countryId]);
    $stateIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (empty($stateIds)) {
      // Nếu không có states, có thể cities.json có country_id trực tiếp
      // Hoặc lấy tất cả cities (nếu cities.json có country_id field)
      $filteredCities = array_filter($allCities, function($city) use ($countryId) {
        // Thử filter theo country_id nếu có
        if (isset($city['country_id'])) {
          return (int)$city['country_id'] === $countryId;
        }
        // Nếu không có country_id trong city, return false
        return false;
      });
    } else {
      // Filter cities theo các state_ids của country
      $filteredCities = array_filter($allCities, function($city) use ($stateIds) {
        return isset($city['state_id']) && in_array((int)$city['state_id'], $stateIds);
      });
    }
  }
  
  // Re-index array và chỉ lấy các field cần thiết
  $cities = array_values(array_map(function($city) {
    return [
      'id' => $city['id'],
      'name' => $city['name'],
      'state_id' => $city['state_id'] ?? null,
      'latitude' => $city['latitude'] ?? null,
      'longitude' => $city['longitude'] ?? null
    ];
  }, $filteredCities));
  
  // Sort theo tên
  usort($cities, function($a, $b) {
    return strcmp($a['name'], $b['name']);
  });
  
  echo json_encode([
    'success' => true,
    'data' => $cities,
    'total' => count($cities)
  ]);
  
} catch (Exception $e) {
  http_response_code(500);
  echo json_encode([
    'success' => false,
    'error' => $e->getMessage()
  ]);
}

