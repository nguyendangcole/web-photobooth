<?php
function db() {
  $host = 'localhost';
  $port = 8889;
  $dbname = 'myapp';
  $username = 'root';
  $password = 'root';
  $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $username, $password);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  return $pdo;
}

function importSQLFile(PDO $pdo, string $filePath)
{
    if (!file_exists($filePath)) {
        echo "⚠️ File not found: $filePath\n";
        return false;
    }

    echo "📦 Importing " . basename($filePath) . "...\n";
    $sql = file_get_contents($filePath);
    $sql = preg_replace('/\/\*![0-9]+ .*?\*\//s', '', $sql);
    $sql = preg_replace('/--.*\n/', '', $sql);
    $queries = array_filter(array_map('trim', explode(';', $sql)));

    $ok = $fail = 0;
    foreach ($queries as $query) {
        try {
            if ($query !== '') {
                $pdo->exec($query);
                $ok++;
            }
        } catch (PDOException $e) {
            echo "⚠️ SQL error: " . $e->getMessage() . "\n";
            $fail++;
        }
    }
    echo "✔️ Done: $ok successful, $fail failed\n";
    return $fail === 0;
}

try {
    $pdo = db();
    echo "✅ Connected to MySQL successfully\n";

    $pdo->exec("SET NAMES utf8mb4; SET FOREIGN_KEY_CHECKS=0;");

    // Xóa bảng cũ nếu có
    $pdo->exec("DROP TABLE IF EXISTS states;");
    $pdo->exec("DROP TABLE IF EXISTS countries;");

    // ---------------------------
    // TẠO BẢNG COUNTRIES
    // ---------------------------
    echo "📋 Creating table countries...\n";
    $pdo->exec("
        CREATE TABLE countries (
          id INT UNSIGNED NOT NULL AUTO_INCREMENT,
          name VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
          iso3 CHAR(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
          numeric_code CHAR(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
          iso2 CHAR(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
          phonecode VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
          capital VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
          currency VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
          currency_name VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
          currency_symbol VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
          region VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
          subregion VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
          latitude DECIMAL(10,8) DEFAULT NULL,
          longitude DECIMAL(11,8) DEFAULT NULL,
          created_at TIMESTAMP NULL DEFAULT NULL,
          updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // ---------------------------
    // TẠO BẢNG STATES (CHƯA CÓ FK)
    // ---------------------------
    echo "📋 Creating table states (without FK)...\n";
    $pdo->exec("
        CREATE TABLE states (
          id INT UNSIGNED NOT NULL AUTO_INCREMENT,
          name VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
          country_id INT UNSIGNED NOT NULL,
          country_code CHAR(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
          iso2 VARCHAR(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
          type VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
          latitude DECIMAL(10,8) DEFAULT NULL,
          longitude DECIMAL(11,8) DEFAULT NULL,
          created_at TIMESTAMP NULL DEFAULT NULL,
          updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          PRIMARY KEY (id),
          KEY (country_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // ---------------------------
    // THÊM KHÓA NGOẠI SAU KHI CẢ HAI ĐÃ TẠO
    // ---------------------------
    echo "🔗 Adding foreign key (states → countries)...\n";
    try {
        $pdo->exec("
            ALTER TABLE states
            ADD CONSTRAINT fk_country FOREIGN KEY (country_id)
            REFERENCES countries(id)
            ON DELETE CASCADE ON UPDATE CASCADE;
        ");
        echo "✅ Foreign key added successfully.\n";
    } catch (PDOException $e) {
        echo "⚠️ FK error: " . $e->getMessage() . "\n";
    }

    // ---------------------------
    // IMPORT DỮ LIỆU
    // ---------------------------
    $countriesFile = __DIR__ . '/countries.sql';
    $statesFile = __DIR__ . '/states.sql';

    importSQLFile($pdo, $countriesFile);
    importSQLFile($pdo, $statesFile);

    $pdo->exec("SET FOREIGN_KEY_CHECKS=1;");
    echo "✅ Database setup complete.\n";

} catch (PDOException $e) {
    echo "❌ Fatal error: " . $e->getMessage() . "\n";
}
