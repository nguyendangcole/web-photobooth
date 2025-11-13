<?php include __DIR__ . '/../header.php'; ?>
<?php
require_once __DIR__ . '/../config.php';
$token = $_GET['token'] ?? '';
$err=''; $ok='';

if ($_SERVER['REQUEST_METHOD']==='POST'){
  if (!csrf_verify($_POST['_csrf'] ?? null)) $err='Phiên không hợp lệ.';
  $token = $_POST['token'] ?? '';
  $pass  = $_POST['password'] ?? '';
  $pass2 = $_POST['password2'] ?? '';
  if (!$err && strlen($pass)<6) $err='Mật khẩu tối thiểu 6 ký tự.';
  if (!$err && $pass!==$pass2)  $err='Xác nhận mật khẩu không khớp.';
  if (!$err){
    $stmt=db()->prepare("SELECT * FROM users WHERE reset_token=? AND reset_expires_at>NOW() LIMIT 1");
    $stmt->execute([$token]); $u=$stmt->fetch();
    if (!$u){ $err='Token không hợp lệ hoặc đã hết hạn.'; }
    else{
      $hash=password_hash($pass,PASSWORD_DEFAULT);
      db()->prepare("UPDATE users SET password_hash=?, reset_token=NULL, reset_expires_at=NULL WHERE id=?")
         ->execute([$hash,$u['id']]);
      $ok='Đặt lại mật khẩu thành công. Mời bạn đăng nhập.';
    }
  }
}
?>
<div class="container py-5">
  <h2>Đặt lại mật khẩu</h2>
  <?php if ($err): ?><div class="alert alert-danger"><?= htmlspecialchars($err) ?></div><?php endif; ?>
  <?php if ($ok): ?><div class="alert alert-success"><?= htmlspecialchars($ok) ?></div><?php endif; ?>
  <form method="post" class="mt-3">
    <input type="hidden" name="_csrf" value="<?= csrf_token() ?>">
    <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
    <div class="mb-3">
      <label class="form-label">Mật khẩu mới</label>
      <input name="password" type="password" class="form-control" minlength="6" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Nhập lại mật khẩu</label>
      <input name="password2" type="password" class="form-control" minlength="6" required>
    </div>
    <button class="btn btn-success">Đổi mật khẩu</button>
  </form>
</div>
<?php include __DIR__ . '/../footer.php'; ?>
