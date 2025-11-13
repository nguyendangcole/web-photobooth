<?php
// Đảm bảo luôn có BASE_URL khi render từ public/index.php
if (!defined('BASE_URL')) {
    define('BASE_URL', rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\') . '/');
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <link rel="icon" type="image/png" href="images/S.png">

  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Photobooth</title>

  <!-- Nhờ <base>, các link "css/..." và "js/..." sẽ tự trỏ đúng tới /public/... -->
  <base href="<?= htmlspecialchars(BASE_URL, ENT_QUOTES) ?>">

  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/photobooth.css">
  <link rel="stylesheet" href="css/menu.css">
</head>
<body>

<?php require __DIR__ . '/menu.php'; ?>
