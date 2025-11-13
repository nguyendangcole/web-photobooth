<?php
require_once dirname(__DIR__) . '/config.php';
require_login_or_redirect();

$me = current_user_or_null();
$meId = (int)($me['id'] ?? 0);
header('Content-Type: application/json');

try {
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    throw new Exception('Invalid request method');
  }

  if (empty($_FILES['image']['tmp_name'])) {
    throw new Exception('No file uploaded');
  }

  $file = $_FILES['image'];
  $allowed = ['jpg','jpeg','png','webp'];
  $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

  if (!in_array($ext, $allowed)) {
    throw new Exception('Invalid file type');
  }

  $uploadsDir = dirname(__DIR__, 2) . '/public/uploads';
  if (!is_dir($uploadsDir)) mkdir($uploadsDir, 0775, true);

  $newName = 'locket_' . date('Ymd_His') . '_' . bin2hex(random_bytes(3)) . '.' . $ext;
  $dest = $uploadsDir . '/' . $newName;
  if (!move_uploaded_file($file['tmp_name'], $dest)) {
    throw new Exception('Failed to save file');
  }

  $layout = $_POST['layout'] ?? '1x4';
  $caption = $_POST['caption'] ?? '';

  $sql = "INSERT INTO shared_photos (user_id, filename, caption, layout, created_at)
          VALUES (:u, :f, :c, :l, NOW())";
  $st = db()->prepare($sql);
  $st->execute([
    ':u' => $meId,
    ':f' => $newName,
    ':c' => $caption,
    ':l' => $layout,
  ]);

  echo json_encode([
    'ok' => true,
    'redirect' => BASE_URL . 'index.php?p=locket'
  ]);
} catch (Throwable $e) {
  http_response_code(400);
  echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
}
