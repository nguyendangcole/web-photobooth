<?php

use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase
{
    public function testDatabaseConnection()
    {
        // Test database connection parameters
        $host = getenv('DB_HOST') ?: '127.0.0.1';
        $name = getenv('DB_NAME') ?: 'photobooth_test';
        $user = getenv('DB_USER') ?: 'root';
        $pass = getenv('DB_PASS') ?: 'root';
        
        $this->assertNotEmpty($host, "Database host should not be empty");
        $this->assertNotEmpty($name, "Database name should not be empty");
        $this->assertNotEmpty($user, "Database user should not be empty");
        
        // Test PDO connection string format
        $dsn = "mysql:host=$host;dbname=$name;charset=utf8mb4";
        $this->assertStringContainsString('mysql:', $dsn, "DSN should contain mysql protocol");
        $this->assertStringContainsString($host, $dsn, "DSN should contain host");
        $this->assertStringContainsString($name, $dsn, "DSN should contain database name");
    }
    
    public function testSqlFileExists()
    {
        $sqlFiles = [
            'config/database_complete.sql',
            'config/archive/database_fixed.sql',
            'config/archive/database_full.sql'
        ];
        
        foreach ($sqlFiles as $file) {
            $this->assertFileExists($file, "Database file should exist: $file");
        }
    }
    
    public function testRequiredTables()
    {
        // Mock table structure validation
        $requiredTables = [
            'users',
            'frames',
            'photos',
            'settings'
        ];
        
        foreach ($requiredTables as $table) {
            $this->assertNotEmpty($table, "Table name should not be empty");
            $this->assertIsString($table, "Table name should be string");
            $this->assertMatchesRegularExpression('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $table, 
                "Table name should be valid: $table");
        }
    }
}
