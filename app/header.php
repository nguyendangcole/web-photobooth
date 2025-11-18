<?php
// Always ensure BASE_URL when rendering from public/index.php
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

  <!-- With <base>, links like "css/..." and "js/..." will automatically point to /public/... -->
  <base href="<?= htmlspecialchars(BASE_URL, ENT_QUOTES) ?>">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/photobooth.css">
  <link rel="stylesheet" href="css/menu.css">
</head>
<body>

<?php require __DIR__ . '/menu.php'; ?>
