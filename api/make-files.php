<?php
/**
 * LAKUM Artspace - Auto-generate clean URL files for Hostinger
 * Run once: https://yoursite.com/api/make-files.php
 * Then delete this file
 */

require_once 'config.php';

try {
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        throw new Exception('Database connection failed');
    }
    
    $filesCreated = 0;
    $errors = [];
    
    // ========================================================================
    // CREATE EVENT FILES
    // ========================================================================
    
    $result = $db->query('SELECT slug FROM events WHERE slug IS NOT NULL');
    
    if (!$result) {
        throw new Exception('Failed to query events: ' . $db->getConnection()->error);
    }
    
    $eventDir = __DIR__ . '/../event';
    if (!is_dir($eventDir)) {
        if (!mkdir($eventDir, 0755, true)) {
            throw new Exception('Failed to create /event directory');
        }
    }
    
    while ($row = $result->fetch_assoc()) {
        $slug = $row['slug'];
        $filePath = $eventDir . '/' . $slug . '.php';
        
        $code = '<?php require_once __DIR__ . "/../event.php"; ?>';
        
        if (file_put_contents($filePath, $code) === false) {
            $errors[] = "Failed to create: /event/$slug.php";
        } else {
            chmod($filePath, 0644);
            $filesCreated++;
            echo "✓ Created: /event/$slug.php<br>";
        }
    }
    
    // ========================================================================
    // CREATE BLOG FILES
    // ========================================================================
    
    $result = $db->query('SELECT slug FROM blogs WHERE slug IS NOT NULL');
    
    if (!$result) {
        throw new Exception('Failed to query blogs: ' . $db->getConnection()->error);
    }
    
    $blogDir = __DIR__ . '/../blog';
    if (!is_dir($blogDir)) {
        if (!mkdir($blogDir, 0755, true)) {
            throw new Exception('Failed to create /blog directory');
        }
    }
    
    while ($row = $result->fetch_assoc()) {
        $slug = $row['slug'];
        $filePath = $blogDir . '/' . $slug . '.php';
        
        $code = '<?php require_once __DIR__ . "/../blogPageDetails.php"; ?>';
        
        if (file_put_contents($filePath, $code) === false) {
            $errors[] = "Failed to create: /blog/$slug.php";
        } else {
            chmod($filePath, 0644);
            $filesCreated++;
            echo "✓ Created: /blog/$slug.php<br>";
        }
    }
    
    // ========================================================================
    // CREATE PRESS FILES
    // ========================================================================
    
    $result = $db->query('SELECT slug FROM press WHERE slug IS NOT NULL');
    
    if (!$result) {
        throw new Exception('Failed to query press: ' . $db->getConnection()->error);
    }
    
    $pressDir = __DIR__ . '/../press';
    if (!is_dir($pressDir)) {
        if (!mkdir($pressDir, 0755, true)) {
            throw new Exception('Failed to create /press directory');
        }
    }
    
    while ($row = $result->fetch_assoc()) {
        $slug = $row['slug'];
        $filePath = $pressDir . '/' . $slug . '.php';
        
        $code = '<?php require_once __DIR__ . "/../pressPageDetails.php"; ?>';
        
        if (file_put_contents($filePath, $code) === false) {
            $errors[] = "Failed to create: /press/$slug.php";
        } else {
            chmod($filePath, 0644);
            $filesCreated++;
            echo "✓ Created: /press/$slug.php<br>";
        }
    }
    
    // ========================================================================
    // SUMMARY
    // ========================================================================
    
    echo "<br><hr><br>";
    echo "<h2 style='color: green;'>✅ SUCCESS!</h2>";
    echo "<p><strong>Total files created: $filesCreated</strong></p>";
    
    if (!empty($errors)) {
        echo "<h3 style='color: red;'>Errors:</h3>";
        foreach ($errors as $error) {
            echo "<p style='color: red;'>✗ $error</p>";
        }
    }
    
    echo "<br><hr><br>";
    echo "<h3>Next Steps:</h3>";
    echo "<ol>";
    echo "<li>Delete this file (api/make-files.php)</li>";
    echo "<li>Test URLs: /event/dior-exhibition?lang=en</li>";
    echo "</ol>";
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>❌ ERROR</h2>";
    echo "<p style='color: red;'>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>Make sure:</p>";
    echo "<ul>";
    echo "<li>Database connection is working</li>";
    echo "<li>config.php is properly configured</li>";
    echo "<li>You have permission to create directories</li>";
    echo "</ul>";
}
?>
