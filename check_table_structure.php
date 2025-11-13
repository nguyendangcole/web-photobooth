<?php
require __DIR__ . '/config/db.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Check Table Structure</title>";
echo "<style>body{font-family:monospace;background:#1e1e1e;color:#00ff00;padding:20px;} h2{color:#00ffff;} .success{color:#00ff00;} .error{color:#ff0000;} .info{color:#ffaa00;} pre{background:#000;padding:10px;overflow:auto;}</style></head><body>";

echo "<h2>🔍 Check Table Structure</h2>";

try {
    $pdo = db();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check countries table
    echo "<h3>📋 Countries Table</h3>";
    $stmt = $pdo->query("SHOW COLUMNS FROM countries");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<p class='success'>Total columns: " . count($columns) . "</p>";
    echo "<pre>";
    foreach ($columns as $col) {
        echo $col['Field'] . " (" . $col['Type'] . ")\n";
    }
    echo "</pre>";
    
    // Check first INSERT statement
    echo "<h3>📋 First INSERT Statement (first 500 chars)</h3>";
    $countriesSql = __DIR__ . '/config/countries.sql';
    $handle = fopen($countriesSql, 'r');
    while (($line = fgets($handle)) !== false) {
        if (stripos($line, 'INSERT INTO') !== false) {
            echo "<pre>" . htmlspecialchars(substr($line, 0, 500)) . "...</pre>";
            
            // Count values in first row
            if (preg_match('/VALUES\s*\((.*?)\)/', $line, $matches)) {
                $firstRow = $matches[1];
                // Count commas (rough estimate)
                $valueCount = substr_count($firstRow, ',') + 1;
                echo "<p class='info'>Estimated values in first row: $valueCount</p>";
            }
            break;
        }
    }
    fclose($handle);
    
    // Check states table
    echo "<h3>📋 States Table</h3>";
    $stmt = $pdo->query("SHOW COLUMNS FROM states");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<p class='success'>Total columns: " . count($columns) . "</p>";
    echo "<pre>";
    foreach ($columns as $col) {
        echo $col['Field'] . " (" . $col['Type'] . ")\n";
    }
    echo "</pre>";
    
    // Check first INSERT statement for states
    echo "<h3>📋 First INSERT Statement for States (first 500 chars)</h3>";
    $statesSql = __DIR__ . '/config/states.sql';
    $handle = fopen($statesSql, 'r');
    while (($line = fgets($handle)) !== false) {
        if (stripos($line, 'INSERT INTO') !== false) {
            echo "<pre>" . htmlspecialchars(substr($line, 0, 500)) . "...</pre>";
            
            // Count values in first row
            if (preg_match('/VALUES\s*\((.*?)\)/', $line, $matches)) {
                $firstRow = $matches[1];
                $valueCount = substr_count($firstRow, ',') + 1;
                echo "<p class='info'>Estimated values in first row: $valueCount</p>";
            }
            break;
        }
    }
    fclose($handle);
    
} catch (Exception $e) {
    echo "<h3 class='error'>❌ ERROR</h3>";
    echo "<p class='error'>" . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "</body></html>";
?>

