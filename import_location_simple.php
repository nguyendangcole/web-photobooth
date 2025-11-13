<?php
require __DIR__ . '/config/db.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);
ini_set('memory_limit', '512M');
set_time_limit(600);

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Import Location Data (Simple)</title>";
echo "<style>body{font-family:monospace;background:#1e1e1e;color:#00ff00;padding:20px;} h2{color:#00ffff;} .success{color:#00ff00;} .error{color:#ff0000;} .info{color:#ffaa00;}</style></head><body>";

echo "<h2>🌍 Import Location Data (Simple Method)</h2>";

try {
    $pdo = db();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get table columns
    echo "<h3>📋 Getting table structures...</h3>";
    
    $stmt = $pdo->query("SHOW COLUMNS FROM countries");
    $countryColumns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $countryColCount = count($countryColumns);
    echo "<p class='info'>Countries table: $countryColCount columns</p>";
    
    $stmt = $pdo->query("SHOW COLUMNS FROM states");
    $stateColumns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $stateColCount = count($stateColumns);
    echo "<p class='info'>States table: $stateColCount columns</p>";
    
    // Step 1: Import countries using prepared statements
    echo "<h3>📋 STEP 1: Import Countries</h3>";
    
    $countriesSql = __DIR__ . '/config/countries.sql';
    if (!file_exists($countriesSql)) {
        throw new Exception("File not found: $countriesSql");
    }
    
    echo "<p class='info'>⏳ Reading and parsing countries.sql...</p>";
    flush();
    
    $pdo->exec("SET FOREIGN_KEY_CHECKS=0");
    $pdo->exec("TRUNCATE TABLE countries"); // Clear existing data
    
    $handle = fopen($countriesSql, 'r');
    if (!$handle) {
        throw new Exception("Cannot open countries.sql");
    }
    
    $insertLine = '';
    while (($line = fgets($handle)) !== false) {
        $line = trim($line);
        if (stripos($line, 'INSERT INTO') !== false) {
            $insertLine = $line;
            break;
        }
    }
    fclose($handle);
    
    if (empty($insertLine)) {
        throw new Exception("No INSERT statement found in countries.sql");
    }
    
    // Extract VALUES part
    if (preg_match('/VALUES\s+(.+);/is', $insertLine, $matches)) {
        $valuesPart = $matches[1];
        
        // Split by ),( to get individual rows
        $rows = preg_split('/\)\s*,\s*\(/', $valuesPart);
        
        // Clean first and last row
        $rows[0] = preg_replace('/^\(/', '', $rows[0]);
        $lastIdx = count($rows) - 1;
        $rows[$lastIdx] = preg_replace('/\)\s*;?\s*$/', '', $rows[$lastIdx]);
        
        echo "<p class='info'>Found " . count($rows) . " country rows to import</p>";
        flush();
        
        // Prepare INSERT statement with column names
        $columnsList = '`' . implode('`, `', $countryColumns) . '`';
        $placeholders = '(' . str_repeat('?,', $countryColCount - 1) . '?)';
        $insertSql = "INSERT INTO `countries` ($columnsList) VALUES $placeholders";
        $stmt = $pdo->prepare($insertSql);
        
        $inserted = 0;
        $errors = 0;
        
        foreach ($rows as $idx => $row) {
            // Parse values - simple CSV-like parsing
            $values = [];
            $current = '';
            $inQuotes = false;
            $quoteChar = '';
            
            for ($i = 0; $i < strlen($row); $i++) {
                $char = $row[$i];
                
                if (($char === '"' || $char === "'") && ($i === 0 || $row[$i-1] !== '\\')) {
                    if (!$inQuotes) {
                        $inQuotes = true;
                        $quoteChar = $char;
                    } elseif ($char === $quoteChar) {
                        $inQuotes = false;
                    }
                    $current .= $char;
                } elseif (!$inQuotes && $char === ',') {
                    $values[] = trim($current);
                    $current = '';
                } else {
                    $current .= $char;
                }
            }
            if (strlen($current) > 0) {
                $values[] = trim($current);
            }
            
            // Take only first N values
            if (count($values) >= $countryColCount) {
                $selectedValues = array_slice($values, 0, $countryColCount);
                
                // Clean values (remove quotes)
                foreach ($selectedValues as &$val) {
                    $val = trim($val);
                    if (($val[0] === '"' && substr($val, -1) === '"') || ($val[0] === "'" && substr($val, -1) === "'")) {
                        $val = substr($val, 1, -1);
                    }
                    if ($val === 'NULL') {
                        $val = null;
                    }
                }
                
                try {
                    $stmt->execute($selectedValues);
                    $inserted++;
                    
                    if ($inserted % 50 === 0) {
                        echo "<p class='info'>⏳ Inserted $inserted / " . count($rows) . " countries...</p>";
                        flush();
                    }
                } catch (PDOException $e) {
                    $errors++;
                    if ($errors <= 5) {
                        echo "<p class='error'>⚠ Row " . ($idx + 1) . ": " . htmlspecialchars(substr($e->getMessage(), 0, 100)) . "</p>";
                    }
                }
            }
        }
        
        echo "<p class='success'>✓ Inserted $inserted countries (errors: $errors)</p>";
    }
    
    $pdo->exec("SET FOREIGN_KEY_CHECKS=1");
    
    // Check count
    $countryCount = $pdo->query("SELECT COUNT(*) FROM countries")->fetchColumn();
    echo "<p class='success'>✓ Total countries in database: $countryCount</p>";
    
    // Step 2: Import states
    echo "<h3>📋 STEP 2: Import States</h3>";
    
    $statesSql = __DIR__ . '/config/states.sql';
    if (!file_exists($statesSql)) {
        throw new Exception("File not found: $statesSql");
    }
    
    echo "<p class='info'>⏳ Reading and parsing states.sql...</p>";
    flush();
    
    $pdo->exec("SET FOREIGN_KEY_CHECKS=0");
    $pdo->exec("TRUNCATE TABLE states"); // Clear existing data
    
    $handle = fopen($statesSql, 'r');
    if (!$handle) {
        throw new Exception("Cannot open states.sql");
    }
    
    $insertLine = '';
    while (($line = fgets($handle)) !== false) {
        $line = trim($line);
        if (stripos($line, 'INSERT INTO') !== false) {
            $insertLine = $line;
            break;
        }
    }
    fclose($handle);
    
    if (empty($insertLine)) {
        throw new Exception("No INSERT statement found in states.sql");
    }
    
    // Extract VALUES part
    if (preg_match('/VALUES\s+(.+);/is', $insertLine, $matches)) {
        $valuesPart = $matches[1];
        
        // Split by ),( to get individual rows
        $rows = preg_split('/\)\s*,\s*\(/', $valuesPart);
        
        // Clean first and last row
        $rows[0] = preg_replace('/^\(/', '', $rows[0]);
        $lastIdx = count($rows) - 1;
        $rows[$lastIdx] = preg_replace('/\)\s*;?\s*$/', '', $rows[$lastIdx]);
        
        echo "<p class='info'>Found " . count($rows) . " state rows to import</p>";
        flush();
        
        // Prepare INSERT statement
        $columnsList = '`' . implode('`, `', $stateColumns) . '`';
        $placeholders = '(' . str_repeat('?,', $stateColCount - 1) . '?)';
        $insertSql = "INSERT INTO `states` ($columnsList) VALUES $placeholders";
        $stmt = $pdo->prepare($insertSql);
        
        $inserted = 0;
        $errors = 0;
        
        foreach ($rows as $idx => $row) {
            // Parse values
            $values = [];
            $current = '';
            $inQuotes = false;
            $quoteChar = '';
            
            for ($i = 0; $i < strlen($row); $i++) {
                $char = $row[$i];
                
                if (($char === '"' || $char === "'") && ($i === 0 || $row[$i-1] !== '\\')) {
                    if (!$inQuotes) {
                        $inQuotes = true;
                        $quoteChar = $char;
                    } elseif ($char === $quoteChar) {
                        $inQuotes = false;
                    }
                    $current .= $char;
                } elseif (!$inQuotes && $char === ',') {
                    $values[] = trim($current);
                    $current = '';
                } else {
                    $current .= $char;
                }
            }
            if (strlen($current) > 0) {
                $values[] = trim($current);
            }
            
            // Take only first N values
            if (count($values) >= $stateColCount) {
                $selectedValues = array_slice($values, 0, $stateColCount);
                
                // Clean values
                foreach ($selectedValues as &$val) {
                    $val = trim($val);
                    if (($val[0] === '"' && substr($val, -1) === '"') || ($val[0] === "'" && substr($val, -1) === "'")) {
                        $val = substr($val, 1, -1);
                    }
                    if ($val === 'NULL') {
                        $val = null;
                    }
                }
                
                try {
                    $stmt->execute($selectedValues);
                    $inserted++;
                    
                    if ($inserted % 100 === 0) {
                        echo "<p class='info'>⏳ Inserted $inserted / " . count($rows) . " states...</p>";
                        flush();
                    }
                } catch (PDOException $e) {
                    $errors++;
                    if ($errors <= 5) {
                        echo "<p class='error'>⚠ Row " . ($idx + 1) . ": " . htmlspecialchars(substr($e->getMessage(), 0, 100)) . "</p>";
                    }
                }
            }
        }
        
        echo "<p class='success'>✓ Inserted $inserted states (errors: $errors)</p>";
    }
    
    $pdo->exec("SET FOREIGN_KEY_CHECKS=1");
    
    // Check count
    $stateCount = $pdo->query("SELECT COUNT(*) FROM states")->fetchColumn();
    echo "<p class='success'>✓ Total states in database: $stateCount</p>";
    
    // Final summary
    echo "<h3>✅ IMPORT COMPLETE!</h3>";
    echo "<p class='success'>Countries: $countryCount</p>";
    echo "<p class='success'>States: $stateCount</p>";
    
    if ($countryCount > 0 && $stateCount > 0) {
        echo "<h3 class='success'>🎉 SUCCESS!</h3>";
        echo "<p>You can now test the registration form!</p>";
        echo "<p><strong>DELETE THIS FILE</strong> for security: <code>import_location_simple.php</code></p>";
    }
    
} catch (Exception $e) {
    echo "<h3 class='error'>❌ ERROR</h3>";
    echo "<p class='error'>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<pre class='error'>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}

echo "</body></html>";
?>

