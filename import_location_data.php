<?php
require __DIR__ . '/config/db.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);
ini_set('memory_limit', '512M'); // Increase memory limit
set_time_limit(300); // 5 minutes

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Import Location Data</title>";
echo "<style>body{font-family:monospace;background:#1e1e1e;color:#00ff00;padding:20px;} h2{color:#00ffff;} .success{color:#00ff00;} .error{color:#ff0000;} .info{color:#ffaa00;}</style></head><body>";

echo "<h2>🌍 Import Location Data</h2>";

try {
    $pdo = db();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Step 1: Import countries.sql
    echo "<h3>📋 STEP 1: Import Countries</h3>";
    
    // Get table columns
    $stmt = $pdo->query("SHOW COLUMNS FROM countries");
    $tableColumns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $columnCount = count($tableColumns);
    echo "<p class='info'>Table has $columnCount columns: " . implode(', ', $tableColumns) . "</p>";
    
    $countriesSql = __DIR__ . '/config/countries.sql';
    if (!file_exists($countriesSql)) {
        throw new Exception("File not found: $countriesSql");
    }
    
    echo "<p class='info'>⏳ Reading SQL file and parsing INSERT statements...</p>";
    flush();
    
    $pdo->exec("SET FOREIGN_KEY_CHECKS=0");
    
    $handle = fopen($countriesSql, 'r');
    if (!$handle) {
        throw new Exception("Cannot open countries.sql");
    }
    
    $currentStatement = '';
    $executed = 0;
    $inserted = 0;
    $lineNum = 0;
    
    while (($line = fgets($handle)) !== false) {
        $lineNum++;
        $line = trim($line);
        
        // Skip empty lines and comments
        if (empty($line) || substr($line, 0, 2) === '--' || substr($line, 0, 2) === '/*' || substr($line, 0, 2) === '//') {
            continue;
        }
        
        $currentStatement .= ' ' . $line;
        
        // Check if statement is complete (ends with semicolon)
        if (substr($line, -1) === ';') {
            $currentStatement = trim($currentStatement);
            
            // Process INSERT statements
            if (stripos($currentStatement, 'INSERT INTO') !== false) {
                // Parse VALUES and extract only needed columns
                if (preg_match('/INSERT INTO\s+`?countries`?\s+VALUES\s+(.+);/is', $currentStatement, $matches)) {
                    $valuesPart = $matches[1];
                    
                    // Parse all value rows
                    $rows = [];
                    $inQuotes = false;
                    $quoteChar = '';
                    $currentRow = '';
                    $parenDepth = 0;
                    
                    for ($i = 0; $i < strlen($valuesPart); $i++) {
                        $char = $valuesPart[$i];
                        
                        if (($char === '"' || $char === "'") && ($i === 0 || $valuesPart[$i-1] !== '\\')) {
                            if (!$inQuotes) {
                                $inQuotes = true;
                                $quoteChar = $char;
                            } elseif ($char === $quoteChar) {
                                $inQuotes = false;
                            }
                        }
                        
                        if (!$inQuotes) {
                            if ($char === '(') {
                                $parenDepth++;
                                if ($parenDepth === 1) {
                                    $currentRow = '';
                                    continue;
                                }
                            } elseif ($char === ')') {
                                $parenDepth--;
                                if ($parenDepth === 0) {
                                    $rows[] = $currentRow;
                                    $currentRow = '';
                                    continue;
                                }
                            }
                        }
                        
                        if ($parenDepth > 0) {
                            $currentRow .= $char;
                        }
                    }
                    
                    // Process each row
                    foreach ($rows as $row) {
                        // Parse values from row
                        $values = [];
                        $currentValue = '';
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
                            }
                            
                            if (!$inQuotes && $char === ',' && $parenDepth === 0) {
                                $values[] = trim($currentValue);
                                $currentValue = '';
                                continue;
                            }
                            
                            if (!$inQuotes) {
                                if ($char === '(') $parenDepth++;
                                elseif ($char === ')') $parenDepth--;
                            }
                            
                            $currentValue .= $char;
                        }
                        if (strlen($currentValue) > 0) {
                            $values[] = trim($currentValue);
                        }
                        
                        // Take only first N values matching column count
                        if (count($values) >= $columnCount) {
                            $selectedValues = array_slice($values, 0, $columnCount);
                            $columnsList = '`' . implode('`, `', $tableColumns) . '`';
                            $valuesList = implode(', ', $selectedValues);
                            
                            $insertSql = "INSERT INTO `countries` ($columnsList) VALUES ($valuesList)";
                            
                            try {
                                $pdo->exec($insertSql);
                                $inserted++;
                                
                                if ($inserted % 50 === 0) {
                                    echo "<p class='info'>⏳ Inserted $inserted countries...</p>";
                                    flush();
                                }
                            } catch (PDOException $e) {
                                // Skip duplicate key errors
                                if (strpos($e->getMessage(), 'Duplicate entry') === false) {
                                    echo "<p class='error'>⚠ Error: " . htmlspecialchars(substr($e->getMessage(), 0, 100)) . "</p>";
                                }
                            }
                        }
                    }
                    
                    $executed++;
                } else {
                    // Not an INSERT, execute as-is
                    try {
                        $pdo->exec($currentStatement);
                        $executed++;
                    } catch (PDOException $e) {
                        if (strpos($e->getMessage(), 'already exists') === false) {
                            echo "<p class='error'>⚠ Line $lineNum: " . htmlspecialchars(substr($e->getMessage(), 0, 100)) . "</p>";
                        }
                    }
                }
            } else {
                // Not an INSERT statement
                if (strlen($currentStatement) > 10) {
                    try {
                        $pdo->exec($currentStatement);
                        $executed++;
                    } catch (PDOException $e) {
                        if (strpos($e->getMessage(), 'already exists') === false) {
                            echo "<p class='error'>⚠ Line $lineNum: " . htmlspecialchars(substr($e->getMessage(), 0, 100)) . "</p>";
                        }
                    }
                }
            }
            
            $currentStatement = '';
        }
    }
    
    fclose($handle);
    $pdo->exec("SET FOREIGN_KEY_CHECKS=1");
    
    echo "<p class='success'>✓ Processed $executed statements, inserted $inserted countries</p>";
    
    // Check countries count
    $countryCount = $pdo->query("SELECT COUNT(*) FROM countries")->fetchColumn();
    echo "<p class='success'>✓ Total countries in database: $countryCount</p>";
    
    if ($countryCount == 0) {
        echo "<p class='error'>❌ No countries imported! Checking file content...</p>";
        $lines = file($countriesSql);
        $insertLines = array_filter($lines, function($line) {
            return stripos($line, 'INSERT INTO') !== false;
        });
        echo "<p class='info'>Found " . count($insertLines) . " INSERT statements in file</p>";
    }
    
    // Step 2: Import states.sql
    echo "<h3>📋 STEP 2: Import States</h3>";
    
    $statesSql = __DIR__ . '/config/states.sql';
    if (!file_exists($statesSql)) {
        throw new Exception("File not found: $statesSql");
    }
    
    echo "<p class='info'>⏳ Reading SQL file line by line...</p>";
    flush();
    
    $pdo->exec("SET FOREIGN_KEY_CHECKS=0");
    
    $handle = fopen($statesSql, 'r');
    if (!$handle) {
        throw new Exception("Cannot open states.sql");
    }
    
    $currentStatement = '';
    $executed = 0;
    $lineNum = 0;
    
    while (($line = fgets($handle)) !== false) {
        $lineNum++;
        $line = trim($line);
        
        // Skip empty lines and comments
        if (empty($line) || substr($line, 0, 2) === '--' || substr($line, 0, 2) === '/*' || substr($line, 0, 2) === '//') {
            continue;
        }
        
        $currentStatement .= ' ' . $line;
        
        // Check if statement is complete (ends with semicolon)
        if (substr($line, -1) === ';') {
            $currentStatement = trim($currentStatement);
            
            if (strlen($currentStatement) > 10) {
                try {
                    $pdo->exec($currentStatement);
                    $executed++;
                    
                    // Show progress every 100 statements
                    if ($executed % 100 === 0) {
                        echo "<p class='info'>⏳ Processed $executed statements (line $lineNum)...</p>";
                        flush();
                    }
                } catch (PDOException $e) {
                    if (strpos($e->getMessage(), 'already exists') === false) {
                        echo "<p class='error'>⚠ Line $lineNum: " . htmlspecialchars(substr($e->getMessage(), 0, 100)) . "</p>";
                    }
                }
            }
            
            $currentStatement = '';
        }
    }
    
    fclose($handle);
    $pdo->exec("SET FOREIGN_KEY_CHECKS=1");
    
    echo "<p class='success'>✓ Executed $executed SQL statements</p>";
    
    // Check states count
    $stateCount = $pdo->query("SELECT COUNT(*) FROM states")->fetchColumn();
    echo "<p class='success'>✓ Total states in database: $stateCount</p>";
    
    // Step 3: Verify cities.json exists
    echo "<h3>📋 STEP 3: Verify Cities JSON</h3>";
    
    $citiesJson = __DIR__ . '/config/cities.json';
    if (!file_exists($citiesJson)) {
        echo "<p class='error'>❌ File not found: cities.json</p>";
    } else {
        $citiesData = json_decode(file_get_contents($citiesJson), true);
        if ($citiesData === null) {
            echo "<p class='error'>❌ Invalid JSON in cities.json</p>";
        } else {
            $cityCount = count($citiesData);
            echo "<p class='success'>✓ Cities JSON found with $cityCount cities</p>";
        }
    }
    
    // Final summary
    echo "<h3>✅ IMPORT COMPLETE!</h3>";
    echo "<p class='success'>Countries: $countryCount</p>";
    echo "<p class='success'>States: $stateCount</p>";
    
    if ($countryCount > 0 && $stateCount > 0) {
        echo "<h3 class='success'>🎉 SUCCESS! You can now:</h3>";
        echo "<ol>";
        echo "<li>Go to <a href='app/?page=register' style='color:#00ffff'>Register Page</a></li>";
        echo "<li>Test the Country, State, City dropdowns</li>";
        echo "<li><strong>DELETE THIS FILE</strong> for security: <code>import_location_data.php</code></li>";
        echo "</ol>";
    } else {
        echo "<h3 class='error'>⚠ ISSUE DETECTED</h3>";
        echo "<p>Data was not imported properly. Please:</p>";
        echo "<ol>";
        echo "<li>Check that <code>config/countries.sql</code> contains INSERT statements</li>";
        echo "<li>Check that <code>config/states.sql</code> contains INSERT statements</li>";
        echo "<li>Try running this script again</li>";
        echo "</ol>";
    }
    
} catch (Exception $e) {
    echo "<h3 class='error'>❌ ERROR</h3>";
    echo "<p class='error'>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<pre class='error'>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}

echo "</body></html>";
?>

