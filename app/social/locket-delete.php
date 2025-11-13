<?php
require_once dirname(__DIR__) . '/config.php';
require_login_or_redirect();
header('Content-Type: application/json');

try {
  $me = current_user_or_null();
  $meId = (int)($me['id'] ?? 0);

  if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception('Invalid request method');

  $id = (int)($_POST['id'] ?? 0);
  if ($id <= 0) throw new Exception('Invalid photo ID');

  $st = db()->prepare("SELECT filename FROM shared_photos WHERE id = :id AND user_id = :uid");
  $st->execute([':id' => $id, ':uid' => $meId]);
  $file = $st->fetchColumn();
  if (!$file) throw new Exception('Photo not found or permission denied');

  $path = dirname(__DIR__, 2) . '/public/uploads/' . $file;
  if (is_file($path)) unlink($path);

  $st = db()->prepare("DELETE FROM shared_photos WHERE id = :id AND user_id = :uid");
  $st->execute([':id' => $id, ':uid' => $meId]);

  echo json_encode(['ok' => true]);
} catch (Throwable $e) {
  http_response_code(400);
  echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
}
