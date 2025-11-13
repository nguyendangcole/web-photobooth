<?php
require __DIR__ . '/config/db.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);
ini_set('memory_limit', '512M');
set_time_limit(600);

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Import Location Data (Fixed)</title>";
echo "<style>body{font-family:monospace;background:#1e1e1e;color:#00ff00;padding:20px;} h2{color:#00ffff;} .success{color:#00ff00;} .error{color:#ff0000;} .info{color:#ffaa00;}</style></head><body>";

echo "<h2>🌍 Import Location Data (Fixed Mapping)</h2>";

try {
    $pdo = db();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Define column mapping: SQL file columns => Database table columns
    // Based on CREATE TABLE in countries.sql
    $countrySqlColumns = [
        'id', 'name', 'iso3', 'numeric_code', 'iso2', 'phonecode', 'capital', 
        'currency', 'currency_name', 'currency_symbol', 'tld', 'native',
        'population', 'gdp', 'region', 'region_id', 'subregion', 'subregion_id',
        'nationality', 'timezones', 'translations', 'latitude', 'longitude',
        'emoji', 'emojiU', 'created_at', 'updated_at', 'flag', 'wikiDataId'
    ];
    
    // Get actual table columns
    $stmt = $pdo->query("SHOW COLUMNS FROM countries");
    $tableColumns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<h3>📋 Countries Table Mapping</h3>";
    echo "<p class='info'>SQL file has " . count($countrySqlColumns) . " columns</p>";
    echo "<p class='info'>Database table has " . count($tableColumns) . " columns</p>";
    
    // Create mapping: which SQL column index maps to which table column
    $columnMapping = [];
    foreach ($tableColumns as $tableCol) {
        $sqlIndex = array_search($tableCol, $countrySqlColumns);
        if ($sqlIndex !== false) {
            $columnMapping[$tableCol] = $sqlIndex;
        }
    }
    
    echo "<p class='success'>Mapped " . count($columnMapping) . " columns</p>";
    
    // Step 1: Import countries
    echo "<h3>📋 STEP 1: Import Countries</h3>";
    
    $countriesSql = __DIR__ . '/config/countries.sql';
    if (!file_exists($countriesSql)) {
        throw new Exception("File not found: $countriesSql");
    }
    
    echo "<p class='info'>⏳ Reading and parsing countries.sql...</p>";
    flush();
    
    $pdo->exec("SET FOREIGN_KEY_CHECKS=0");
    $pdo->exec("TRUNCATE TABLE countries");
    
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
        throw new Exception("No INSERT statement found");
    }
    
    // Extract VALUES
    if (preg_match('/VALUES\s+(.+);/is', $insertLine, $matches)) {
        $valuesPart = $matches[1];
        $rows = preg_split('/\)\s*,\s*\(/', $valuesPart);
        
        $rows[0] = preg_replace('/^\(/', '', $rows[0]);
        $lastIdx = count($rows) - 1;
        $rows[$lastIdx] = preg_replace('/\)\s*;?\s*$/', '', $rows[$lastIdx]);
        
        echo "<p class='info'>Found " . count($rows) . " country rows</p>";
        flush();
        
        // Prepare INSERT with column names
        $columnsList = '`' . implode('`, `', $tableColumns) . '`';
        $placeholders = '(' . str_repeat('?,', count($tableColumns) - 1) . '?)';
        $insertSql = "INSERT INTO `countries` ($columnsList) VALUES $placeholders";
        $stmt = $pdo->prepare($insertSql);
        
        $inserted = 0;
        $errors = 0;
        
        foreach ($rows as $idx => $row) {
            // Parse all values from row
            $allValues = [];
            $current = '';
            $inQuotes = false;
            $quoteChar = '';
            $parenDepth = 0;
            
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
                } elseif (!$inQuotes && $char === ',' && $parenDepth === 0) {
                    $allValues[] = trim($current);
                    $current = '';
                } else {
                    if (!$inQuotes) {
                        if ($char === '(') $parenDepth++;
                        elseif ($char === ')') $parenDepth--;
                    }
                    $current .= $char;
                }
            }
            if (strlen($current) > 0) {
                $allValues[] = trim($current);
            }
            
            // Map values to table columns
            $mappedValues = [];
            foreach ($tableColumns as $tableCol) {
                if (isset($columnMapping[$tableCol])) {
                    $sqlIndex = $columnMapping[$tableCol];
                    $value = isset($allValues[$sqlIndex]) ? $allValues[$sqlIndex] : 'NULL';
                } else {
                    $value = 'NULL';
                }
                
                // Clean value
                $value = trim($value);
                if (($value[0] === '"' && substr($value, -1) === '"') || ($value[0] === "'" && substr($value, -1) === "'")) {
                    $value = substr($value, 1, -1);
                }
                if ($value === 'NULL' || $value === '') {
                    $mappedValues[] = null;
                } else {
                    $mappedValues[] = $value;
                }
            }
            
            try {
                $stmt->execute($mappedValues);
                $inserted++;
                
                if ($inserted % 50 === 0) {
                    echo "<p class='info'>⏳ Inserted $inserted / " . count($rows) . " countries...</p>";
                    flush();
                }
            } catch (PDOException $e) {
                $errors++;
                if ($errors <= 5) {
                    echo "<p class='error'>⚠ Row " . ($idx + 1) . ": " . htmlspecialchars(substr($e->getMessage(), 0, 150)) . "</p>";
                }
            }
        }
        
        echo "<p class='success'>✓ Inserted $inserted countries (errors: $errors)</p>";
    }
    
    $pdo->exec("SET FOREIGN_KEY_CHECKS=1");
    
    $countryCount = $pdo->query("SELECT COUNT(*) FROM countries")->fetchColumn();
    echo "<p class='success'>✓ Total countries: $countryCount</p>";
    
    // Step 2: Import states
    echo "<h3>📋 STEP 2: Import States</h3>";
    
    // States mapping
    $stateSqlColumns = [
        'id', 'name', 'country_id', 'country_code', 'fips_code', 'iso2', 'iso3166_2',
        'type', 'level', 'parent_id', 'native', 'latitude', 'longitude', 'timezone',
        'created_at', 'updated_at', 'flag', 'wikiDataId'
    ];
    
    $stmt = $pdo->query("SHOW COLUMNS FROM states");
    $stateTableColumns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<p class='info'>SQL file has " . count($stateSqlColumns) . " columns</p>";
    echo "<p class='info'>Database table has " . count($stateTableColumns) . " columns</p>";
    
    $stateColumnMapping = [];
    foreach ($stateTableColumns as $tableCol) {
        $sqlIndex = array_search($tableCol, $stateSqlColumns);
        if ($sqlIndex !== false) {
            $stateColumnMapping[$tableCol] = $sqlIndex;
        }
    }
    
    echo "<p class='success'>Mapped " . count($stateColumnMapping) . " columns</p>";
    
    $statesSql = __DIR__ . '/config/states.sql';
    if (!file_exists($statesSql)) {
        throw new Exception("File not found: $statesSql");
    }
    
    echo "<p class='info'>⏳ Reading and parsing states.sql...</p>";
    flush();
    
    $pdo->exec("SET FOREIGN_KEY_CHECKS=0");
    $pdo->exec("TRUNCATE TABLE states");
    
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
        throw new Exception("No INSERT statement found");
    }
    
    if (preg_match('/VALUES\s+(.+);/is', $insertLine, $matches)) {
        $valuesPart = $matches[1];
        $rows = preg_split('/\)\s*,\s*\(/', $valuesPart);
        
        $rows[0] = preg_replace('/^\(/', '', $rows[0]);
        $lastIdx = count($rows) - 1;
        $rows[$lastIdx] = preg_replace('/\)\s*;?\s*$/', '', $rows[$lastIdx]);
        
        echo "<p class='info'>Found " . count($rows) . " state rows</p>";
        flush();
        
        $columnsList = '`' . implode('`, `', $stateTableColumns) . '`';
        $placeholders = '(' . str_repeat('?,', count($stateTableColumns) - 1) . '?)';
        $insertSql = "INSERT INTO `states` ($columnsList) VALUES $placeholders";
        $stmt = $pdo->prepare($insertSql);
        
        $inserted = 0;
        $errors = 0;
        
        foreach ($rows as $idx => $row) {
            // Parse all values
            $allValues = [];
            $current = '';
            $inQuotes = false;
            $quoteChar = '';
            $parenDepth = 0;
            
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
                } elseif (!$inQuotes && $char === ',' && $parenDepth === 0) {
                    $allValues[] = trim($current);
                    $current = '';
                } else {
                    if (!$inQuotes) {
                        if ($char === '(') $parenDepth++;
                        elseif ($char === ')') $parenDepth--;
                    }
                    $current .= $char;
                }
            }
            if (strlen($current) > 0) {
                $allValues[] = trim($current);
            }
            
            // Map values
            $mappedValues = [];
            foreach ($stateTableColumns as $tableCol) {
                if (isset($stateColumnMapping[$tableCol])) {
                    $sqlIndex = $stateColumnMapping[$tableCol];
                    $value = isset($allValues[$sqlIndex]) ? $allValues[$sqlIndex] : 'NULL';
                } else {
                    $value = 'NULL';
                }
                
                $value = trim($value);
                if (($value[0] === '"' && substr($value, -1) === '"') || ($value[0] === "'" && substr($value, -1) === "'")) {
                    $value = substr($value, 1, -1);
                }
                if ($value === 'NULL' || $value === '') {
                    $mappedValues[] = null;
                } else {
                    $mappedValues[] = $value;
                }
            }
            
            try {
                $stmt->execute($mappedValues);
                $inserted++;
                
                if ($inserted % 100 === 0) {
                    echo "<p class='info'>⏳ Inserted $inserted / " . count($rows) . " states...</p>";
                    flush();
                }
            } catch (PDOException $e) {
                $errors++;
                if ($errors <= 5) {
                    echo "<p class='error'>⚠ Row " . ($idx + 1) . ": " . htmlspecialchars(substr($e->getMessage(), 0, 150)) . "</p>";
                }
            }
        }
        
        echo "<p class='success'>✓ Inserted $inserted states (errors: $errors)</p>";
    }
    
    $pdo->exec("SET FOREIGN_KEY_CHECKS=1");
    
    $stateCount = $pdo->query("SELECT COUNT(*) FROM states")->fetchColumn();
    echo "<p class='success'>✓ Total states: $stateCount</p>";
    
    // Final summary
    echo "<h3>✅ IMPORT COMPLETE!</h3>";
    echo "<p class='success'>Countries: $countryCount</p>";
    echo "<p class='success'>States: $stateCount</p>";
    
    if ($countryCount > 0 && $stateCount > 0) {
        echo "<h3 class='success'>🎉 SUCCESS!</h3>";
        echo "<p>You can now test the registration form!</p>";
        echo "<p><strong>DELETE THIS FILE</strong> for security: <code>import_location_fixed.php</code></p>";
    }
    
} catch (Exception $e) {
    echo "<h3 class='error'>❌ ERROR</h3>";
    echo "<p class='error'>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<pre class='error'>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}

echo "</body></html>";
?>

