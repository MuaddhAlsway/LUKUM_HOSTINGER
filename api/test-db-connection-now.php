<?php
/**
 * Database Connection Test
 */

require_once __DIR__ . '/config.php';

header('Content-Type: application/json');

try {
    echo "Testing database connection...\n\n";
    
    // Test 1: Check constants
    echo "Constants defined:\n";
    echo "DB_HOST: " . DB_HOST . "\n";
    echo "DB_USER: " . DB_USER . "\n";
    echo "DB_NAME: " . DB_NAME . "\n";
    echo "DB_PORT: " . DB_PORT . "\n\n";
    
    // Test 2: Try direct connection
    echo "Attempting direct mysqli connection...\n";
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
    
    if ($conn->connect_error) {
        echo "❌ Direct connection failed: " . $conn->connect_error . "\n";
    } else {
        echo "✅ Direct connection successful\n";
        $conn->close();
    }
    
    // Test 3: Try Database singleton
    echo "\nAttempting Database singleton connection...\n";
    $db = Database::getInstance();
    
    if ($db->isConnected()) {
        echo "✅ Database singleton connected\n";
        
        // Test 4: Try a simple query
        echo "\nTesting simple query...\n";
        $result = $db->query("SELECT 1 as test");
        if ($result) {
            echo "✅ Query successful\n";
        } else {
            echo "❌ Query failed\n";
        }
    } else {
        echo "❌ Database singleton failed to connect\n";
    }
    
    echo "\n✅ All tests completed\n";
    
} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n";
}
?>

