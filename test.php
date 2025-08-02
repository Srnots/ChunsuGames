<?php
echo "🧪 Testing Universal Game Library Server\n\n";

// Test 1: Check PHP version
echo "1. PHP Version: " . phpversion() . "\n";

// Test 2: Check file permissions
$databaseFile = 'database.json';
echo "2. Database file exists: " . (file_exists($databaseFile) ? '✅ Yes' : '❌ No') . "\n";
echo "3. Database file readable: " . (is_readable($databaseFile) ? '✅ Yes' : '❌ No') . "\n";
echo "4. Database file writable: " . (is_writable($databaseFile) ? '✅ Yes' : '❌ No') . "\n";

// Test 3: Check directory permissions
echo "5. Current directory writable: " . (is_writable('.') ? '✅ Yes' : '❌ No') . "\n";

// Test 4: Test JSON functions
echo "6. JSON functions available: " . (function_exists('json_encode') ? '✅ Yes' : '❌ No') . "\n";

// Test 5: Test file operations
try {
    $testFile = 'test_write.tmp';
    $result = file_put_contents($testFile, 'test');
    if ($result !== false) {
        unlink($testFile);
        echo "7. File write test: ✅ Success\n";
    } else {
        echo "7. File write test: ❌ Failed\n";
    }
} catch (Exception $e) {
    echo "7. File write test: ❌ Error - " . $e->getMessage() . "\n";
}

// Test 6: Test server API
echo "\n🔧 Testing API endpoints:\n";

// Test status endpoint
$statusUrl = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . "/server.php?action=status";
echo "Status endpoint: $statusUrl\n";

// Test basic functionality
try {
    if (file_exists($databaseFile)) {
        $content = file_get_contents($databaseFile);
        $data = json_decode($content, true);
        if ($data) {
            echo "8. Database format: ✅ Valid JSON\n";
            echo "9. Games count: " . count($data['games']) . "\n";
        } else {
            echo "8. Database format: ❌ Invalid JSON\n";
        }
    }
} catch (Exception $e) {
    echo "8. Database test: ❌ Error - " . $e->getMessage() . "\n";
}

echo "\n🎯 Setup Instructions:\n";
echo "1. Make sure all files are uploaded to your web server\n";
echo "2. Ensure PHP is enabled on your hosting\n";
echo "3. Set proper file permissions (644 for files, 755 for directories)\n";
echo "4. Access via HTTP/HTTPS (not file:// protocol)\n";
echo "5. Check browser console for any JavaScript errors\n";

echo "\n📁 Required files:\n";
echo "- index.html (main page)\n";
echo "- server.php (API backend)\n";
echo "- database.json (game storage)\n";
echo "- test.php (this test file)\n";

$baseUrl = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']);
echo "\n🌐 Access your game library at:\n";
echo "$baseUrl/index.html\n";
?>