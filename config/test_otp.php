<?php
/**
 * Test script để kiểm tra OTP trong database
 * Truy cập: http://localhost/WEB-PHOTOBOOTH/config/test_otp.php
 */

require_once __DIR__ . '/../app/config.php';

header('Content-Type: text/html; charset=utf-8');

$email = $_GET['email'] ?? $_POST['email'] ?? '';

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>OTP Test</title>";
echo "<style>
body{font-family:Arial,sans-serif;padding:20px;max-width:800px;margin:0 auto;}
pre{background:#f5f5f5;padding:10px;border:1px solid #ddd;border-radius:4px;overflow-x:auto;}
form{background:#f9f9f9;padding:20px;border:1px solid #ddd;border-radius:4px;margin:20px 0;}
input[type='email']{width:100%;padding:10px;margin:10px 0;border:1px solid #ddd;border-radius:4px;font-size:14px;}
button{background:#007bff;color:white;padding:10px 20px;border:none;border-radius:4px;cursor:pointer;font-size:14px;}
button:hover{background:#0056b3;}
.error{color:red;background:#ffe6e6;padding:10px;border-radius:4px;margin:10px 0;}
.success{color:green;background:#e6ffe6;padding:10px;border-radius:4px;margin:10px 0;}
</style>";
echo "</head><body>";
echo "<h1>🔍 OTP Debug Test</h1>";

// Form để nhập email
if (empty($email)) {
    echo "<form method='get'>";
    echo "<h3>Nhập email để kiểm tra OTP:</h3>";
    echo "<input type='email' name='email' placeholder='your-email@example.com' required>";
    echo "<button type='submit'>Kiểm tra OTP</button>";
    echo "</form>";
    echo "</body></html>";
    exit;
}

echo "<p><strong>Email:</strong> " . htmlspecialchars($email) . "</p>";
echo "<p><a href='?'>← Nhập email khác</a></p>";

try {
    $stmt = db()->prepare("SELECT id, email, reset_token, reset_expires_at, created_at FROM users WHERE email=? LIMIT 1");
    $stmt->execute([$email]);
    $u = $stmt->fetch();
    
    if (!$u) {
        echo "<p style='color:red;'>❌ User not found!</p>";
    } else {
        echo "<h2>User Info:</h2>";
        echo "<pre>";
        echo "ID: " . $u['id'] . "\n";
        echo "Email: " . $u['email'] . "\n";
        echo "Reset Token (OTP): " . ($u['reset_token'] ?? 'NULL') . "\n";
        echo "Reset Token Length: " . strlen($u['reset_token'] ?? '') . "\n";
        echo "Reset Expires At: " . ($u['reset_expires_at'] ?? 'NULL') . "\n";
        echo "Created At: " . $u['created_at'] . "\n";
        echo "</pre>";
        
        if (!empty($u['reset_token'])) {
            $otp = $u['reset_token'];
            echo "<h2>OTP Analysis:</h2>";
            echo "<pre>";
            echo "Raw OTP: '" . $otp . "'\n";
            echo "Trimmed OTP: '" . trim($otp) . "'\n";
            echo "OTP Length: " . strlen($otp) . "\n";
            echo "Trimmed Length: " . strlen(trim($otp)) . "\n";
            echo "Is Numeric: " . (ctype_digit($otp) ? 'YES' : 'NO') . "\n";
            echo "Is Numeric (trimmed): " . (ctype_digit(trim($otp)) ? 'YES' : 'NO') . "\n";
            echo "Hex: " . bin2hex($otp) . "\n";
            echo "ASCII: ";
            for ($i = 0; $i < strlen($otp); $i++) {
                echo ord($otp[$i]) . " ";
            }
            echo "\n";
            echo "</pre>";
            
            // Check expiration
            if (!empty($u['reset_expires_at'])) {
                $now = new DateTime();
                $expiresAt = new DateTime($u['reset_expires_at']);
                $isExpired = $expiresAt < $now;
                echo "<h2>Expiration Check:</h2>";
                echo "<pre>";
                echo "Now: " . $now->format('Y-m-d H:i:s') . "\n";
                echo "Expires: " . $expiresAt->format('Y-m-d H:i:s') . "\n";
                echo "Is Expired: " . ($isExpired ? 'YES ❌' : 'NO ✅') . "\n";
                echo "</pre>";
            }
        } else {
            echo "<p style='color:orange;'>⚠️ No OTP found in database</p>";
        }
    }
    
    echo "<hr>";
    
    // Test verify form
    if (!empty($u['reset_token'])) {
        echo "<h2>🧪 Test Verify OTP:</h2>";
        echo "<form method='post' style='background:#fff3cd;padding:15px;border-radius:4px;'>";
        echo "<input type='hidden' name='email' value='" . htmlspecialchars($email) . "'>";
        echo "<p><strong>OTP trong DB:</strong> <code style='background:#fff;padding:5px 10px;border-radius:3px;'>" . htmlspecialchars($u['reset_token']) . "</code></p>";
        echo "<label><strong>Nhập OTP để test verify:</strong></label><br>";
        echo "<input type='text' name='test_otp' placeholder='Nhập OTP (179154)' style='width:200px;padding:8px;margin:10px 0;font-size:18px;text-align:center;letter-spacing:5px;' maxlength='6' pattern='[0-9]{6}'>";
        echo "<button type='submit' style='margin-left:10px;'>Test Verify</button>";
        echo "</form>";
        
        // Process test verify
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['test_otp'])) {
            $testOtp = preg_replace('/\D/', '', $_POST['test_otp']);
            $testOtp = str_pad($testOtp, 6, '0', STR_PAD_LEFT);
            $dbOtp = trim($u['reset_token']);
            
            echo "<div style='margin-top:15px;padding:15px;background:" . ($dbOtp === $testOtp ? '#d4edda' : '#f8d7da') . ";border-radius:4px;'>";
            echo "<h3>" . ($dbOtp === $testOtp ? "✅ MATCH!" : "❌ NO MATCH") . "</h3>";
            echo "<pre>";
            echo "Input OTP: '{$testOtp}' (length: " . strlen($testOtp) . ")\n";
            echo "DB OTP:    '{$dbOtp}' (length: " . strlen($dbOtp) . ")\n";
            echo "Match: " . ($dbOtp === $testOtp ? 'YES ✅' : 'NO ❌') . "\n";
            if ($dbOtp !== $testOtp) {
                echo "\nHex comparison:\n";
                echo "Input hex: " . bin2hex($testOtp) . "\n";
                echo "DB hex:    " . bin2hex($dbOtp) . "\n";
            }
            echo "</pre>";
            echo "</div>";
        }
    }
    
    echo "<hr>";
    echo "<p><a href='?email=" . urlencode($email) . "'>🔄 Refresh</a> | ";
    echo "<a href='../app/auth/verify_otp.php?email=" . urlencode($email) . "'>🔐 Go to Verify OTP Page</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color:red;'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "</body></html>";
?>

