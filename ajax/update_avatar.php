<?php
/**
 * ajax/update_avatar.php
 * Update user avatar
 */
require_once __DIR__ . '/../app/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(['success' => false, 'error' => 'Method not allowed']);
  exit;
}

$user = current_user();
if (!$user) {
  http_response_code(401);
  echo json_encode(['success' => false, 'error' => 'Not authenticated']);
  exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$avatarUrl = $data['avatar_url'] ?? null;

if (!$avatarUrl) {
  http_response_code(400);
  echo json_encode(['success' => false, 'error' => 'Avatar URL is required']);
  exit;
}

// Validate URL format - accept valid URLs, data URLs, relative paths, or absolute paths
$isValidUrl = filter_var($avatarUrl, FILTER_VALIDATE_URL);
$isDataUrl = strpos($avatarUrl, 'data:image') === 0;
$isRelativePath = preg_match('#^(images/|/images/)#', $avatarUrl);
$isBaseUrlPath = strpos($avatarUrl, BASE_URL) === 0; // Path starting with BASE_URL
$isAbsolutePath = preg_match('#^/.*images/avatars/#', $avatarUrl); // Path like /WEB-PHOTOBOOTH/public/images/avatars/
$isGravatar = strpos($avatarUrl, 'gravatar.com') !== false; // Gravatar URLs

if (!$isValidUrl && !$isDataUrl && !$isRelativePath && !$isBaseUrlPath && !$isAbsolutePath && !$isGravatar) {
  http_response_code(400);
  echo json_encode(['success' => false, 'error' => 'Invalid avatar URL: ' . $avatarUrl]);
  exit;
}

try {
  $stmt = db()->prepare("UPDATE users SET avatar_url = ? WHERE id = ?");
  $stmt->execute([$avatarUrl, $user['id']]);
  
  // Update session
  $_SESSION['user']['avatar_url'] = $avatarUrl;
  
  echo json_encode(['success' => true, 'message' => 'Avatar updated successfully']);
} catch (Exception $e) {
  http_response_code(500);
  echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}

