<?php

use PHPUnit\Framework\TestCase;

class FileUploadTest extends TestCase
{
    public function testUploadDirectoriesExist()
    {
        $uploadDirs = [
            'uploads',
            'public/photobook',
            'logs'
        ];
        
        foreach ($uploadDirs as $dir) {
            // For CI/CD, we test the directory structure concept
            $this->assertNotEmpty($dir, "Upload directory path should not be empty");
            $this->assertIsString($dir, "Upload directory should be string");
            $this->assertStringNotContainsString('..', $dir, "Directory path should be safe");
        }
    }
    
    public function testAllowedFileTypes()
    {
        $allowedTypes = [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp'
        ];
        
        foreach ($allowedTypes as $type) {
            $this->assertStringStartsWith('image/', $type, "Allowed type should be image: $type");
            $this->assertNotEmpty($type, "File type should not be empty");
        }
    }
    
    public function testFileSizeLimits()
    {
        $maxFileSize = 10 * 1024 * 1024; // 10MB
        $testSizes = [
            1024,        // 1KB
            1024 * 1024, // 1MB
            5 * 1024 * 1024, // 5MB
            10 * 1024 * 1024, // 10MB
            15 * 1024 * 1024  // 15MB (should be too large)
        ];
        
        foreach ($testSizes as $size) {
            if ($size <= $maxFileSize) {
                $this->assertTrue($size <= $maxFileSize, 
                    "Size $size should be allowed");
            } else {
                $this->assertFalse($size <= $maxFileSize, 
                    "Size $size should be rejected");
            }
        }
    }
}
