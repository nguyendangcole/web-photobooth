<?php
if (!function_exists('db')) {
  function db() {
    static $pdo;

    if (!$pdo) {
      $host = 'localhost';
      $port = '8889';                  // 🔹 MAMP MySQL port
      $dbname = 'myapp';      // 🔹 đổi đúng tên DB bạn đã tạo trong phpMyAdmin MAMP
      $username = 'root';
      $password = 'root';              // 🔹 MAMP có password mặc định là "root"

      try {
        $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      } catch (PDOException $e) {
        // Log error instead of outputting directly (let caller handle error response)
        error_log('db() PDO Exception: ' . $e->getMessage());
        // Throw exception so caller can handle it properly
        throw $e;
      }
    }

    return $pdo;
  }
}
