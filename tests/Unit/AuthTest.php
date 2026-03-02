<?php

use PHPUnit\Framework\TestCase;

class AuthTest extends TestCase
{
    public function testPasswordHashing()
    {
        $password = 'test123';
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        
        $this->assertTrue(password_verify($password, $hashed));
        $this->assertNotEquals($password, $hashed);
    }
    
    public function testEmailValidation()
    {
        $validEmails = [
            'test@example.com',
            'user.name@domain.co.uk',
            'user+tag@example.org'
        ];
        
        $invalidEmails = [
            'invalid-email',
            '@example.com',
            'test@',
            'test..test@example.com'
        ];
        
        foreach ($validEmails as $email) {
            $this->assertTrue(filter_var($email, FILTER_VALIDATE_EMAIL) !== false, 
                "Valid email failed: $email");
        }
        
        foreach ($invalidEmails as $email) {
            $this->assertFalse(filter_var($email, FILTER_VALIDATE_EMAIL) !== false, 
                "Invalid email passed: $email");
        }
    }
    
    public function testCsrfTokenGeneration()
    {
        // Mock session
        $_SESSION = [];
        
        function csrf_token() {
            if (empty($_SESSION['_csrf'])) {
                $_SESSION['_csrf'] = bin2hex(random_bytes(16));
            }
            return $_SESSION['_csrf'];
        }
        
        $token1 = csrf_token();
        $token2 = csrf_token();
        
        $this->assertEquals($token1, $token2);
        $this->assertEquals(32, strlen($token1));
    }
}
