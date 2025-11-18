<?php include __DIR__ . '/../header.php'; ?>
<?php
require_once __DIR__ . '/../config.php';
$msg=''; $err='';

if ($_SERVER['REQUEST_METHOD']==='POST'){
  if (!csrf_verify($_POST['_csrf'] ?? null)) $err='Invalid session.';
  $email=trim($_POST['email'] ?? '');
  if (!$err && !filter_var($email, FILTER_VALIDATE_EMAIL)) $err='Invalid email.';
  if (!$err){
    $stmt=db()->prepare("SELECT * FROM users WHERE email=? LIMIT 1");
    $stmt->execute([$email]); $u=$stmt->fetch();
    if ($u){
      $token=bin2hex(random_bytes(16));
      $exp=(new DateTime('+30 minutes'))->format('Y-m-d H:i:s');
      db()->prepare("UPDATE users SET reset_token=?, reset_expires_at=? WHERE id=?")
          ->execute([$token,$exp,$u['id']]);
      $link = BASE_URL.'?p=reset&token='.$token;
      send_mail($email,'Password Reset',"Click link to reset: <a href=\"$link\">$link</a>");
      $msg='Instructions email sent (if email exists).';
    } else {
      $msg='Instructions email sent (if email exists).';
    }
  }
}
?>
<div class="container py-5">
  <h2>Forgot Password</h2>
  <?php if ($err): ?><div class="alert alert-danger"><?= htmlspecialchars($err) ?></div><?php endif; ?>
  <?php if ($msg): ?><div class="alert alert-info"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
  <form method="post" class="mt-3">
    <input type="hidden" name="_csrf" value="<?= csrf_token() ?>">
    <label class="form-label">Email</label>
    <input name="email" type="email" class="form-control" required>
    <button class="btn btn-primary mt-3">Send reset link</button>
  </form>
</div>
<?php include __DIR__ . '/../footer.php'; ?>
