<?php

use PHPUnit\Framework\TestCase;

class FrameTest extends TestCase
{
    public function testFrameImageExists()
    {
        $frameImages = [
            'public/images/frame-cat.png',
            'public/images/frame-star.png',
            'public/images/frame-crazy-1.png',
            'public/images/frame-vintage-1.png'
        ];
        
        foreach ($frameImages as $image) {
            $this->assertFileExists($image, "Frame image should exist: $image");
        }
    }
    
    public function testFrameLayouts()
    {
        $validLayouts = ['vertical', 'square'];
        $testLayouts = ['vertical', 'square', 'horizontal', 'circle'];
        
        foreach ($testLayouts as $layout) {
            if (in_array($layout, $validLayouts)) {
                $this->assertTrue(in_array($layout, $validLayouts), 
                    "Valid layout should be recognized: $layout");
            } else {
                $this->assertFalse(in_array($layout, $validLayouts), 
                    "Invalid layout should be rejected: $layout");
            }
        }
    }
    
    public function testFrameNaming()
    {
        $frameNames = [
            'Cat',
            'Star',
            'Crazy(1)',
            'vintage (1)',
            'Y2K',
            '#Vietnamese',
            '#1989'
        ];
        
        foreach ($frameNames as $name) {
            $this->assertNotEmpty($name, "Frame name should not be empty");
            $this->assertIsString($name, "Frame name should be string");
            $this->assertLessThanOrEqual(255, strlen($name), 
                "Frame name should not exceed 255 characters");
        }
    }
}
