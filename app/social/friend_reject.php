<?php
require_once dirname(__DIR__) . '/config.php';
header('Content-Type: application/json; charset=utf-8');


try {
require_login_or_redirect();
$meId = (int)(current_user_or_null()['id'] ?? 0);
$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
if ($id <= 0) throw new RuntimeException('Invalid id');


$sql = "UPDATE friendships SET status='rejected' WHERE id=:id AND addressee_id=:me";
$st = db()->prepare($sql);
$st->execute([':id'=>$id, ':me'=>$meId]);
if ($st->rowCount() === 0) throw new RuntimeException('Not allowed or already handled');


echo json_encode(['ok'=>true]);
} catch (Throwable $e) {
http_response_code(400);
echo json_encode(['ok'=>false, 'error'=>$e->getMessage()]);
}