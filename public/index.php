<?php
define('BASE_URL', rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\') . '/');
require __DIR__ . '/../app/config.php';
require __DIR__ . '/../app/router.php';
