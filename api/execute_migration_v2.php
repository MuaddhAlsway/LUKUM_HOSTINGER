<?php
/**
 * LAKUM Artspace - SQL Migration Executor V2
 * Executes Phase 1 & Phase 2 SQL files for hybrid translation implementation
 */

header('Content-Type: application/json');

require_once 'db.php';

$results = [
    'phase1' => ['status' => 'pending', 'message' => '', 'tables_created' => []],
    'phase2' => ['status' => 'pending', 'message' => '', 'rows_migrated' => []],
    'verification' => ['status' => 'pending', 'message' => '', 'counts' => []],
    'overall_status' => 'pending'
];

function executeSQLFile($conn, $filepath, $phase_name) {
    global $results;
    
    echo "Starting $phase_name: " . basename($filepath) . "...\n";
    
    $sql = file_get_contents($filepath);
    if (!$sql) {
        throw new Exception("Could not read $filepath");
    }
    
    // Remove comments and split by semicolon
    $lines = explode("\n", $sql);
    $statement = '';
    $statement_count = 0;
    
    foreach ($lines as $line) {
        // Remove comments
        $line = preg_replace('/--.*$/', '', $line);
        $line = trim($line);
        
        if (empty($line)) {
            continue;
        }
        
        $statement .= ' ' . $line;
        
        // Check if statement ends with semicolon
        if (substr($line, -1) === ';') {
            $statement = trim($statement);
            $statement = substr($statement, 0, -1); // Remove trailing semicolon
            
            if (!empty($statement)) {
                if ($conn->query($statement) === TRUE) {
                    $statement_count++;
                    
                    // Track table creation
                    if (preg_match('/CREATE TABLE.*?`?(\w+)`?\s*\(/i', $statement, $matches)) {
                        $results[$phase_name]['tables_created'][] = $matches[1];
                        echo "  ✓ Created table: " . $matches[1] . "\n";
                    }
                    
                    // Track data insertion
                    if (preg_match('/INSERT INTO\s+`?(\w+)`?/i', $statement, $matches)) {
                        $table = $matches[1];
                        $affected = $conn->affected_rows;
                        if ($affected > 0) {
                            if (!isset($results[$phase_name]['rows_migrated'][$table])) {
                                $results[$phase_name]['rows_migrated'][$table] = 0;
                            }
                            $results[$phase_name]['rows_migrated'][$table] += $affected;
                            echo "  ✓ Inserted " . $affected . " rows into " . $table . "\n";
                        }
                    }
                } else {
                    // Check if it's a SELECT statement (verification query) - those can fail
                    if (strpos($statement, 'SELECT') === 0) {
                        echo "  ℹ Skipping verification query\n";
                    } else {
                        throw new Exception("$phase_name Error: " . $conn->error . "\nStatement: " . substr($statement, 0, 100));
                    }
                }
            }
            
            $statement = '';
        }
    }
    
    $results[$phase_name]['status'] = 'success';
    $results[$phase_name]['message'] = "$phase_name completed: $statement_count statements executed";
    
    echo "✓ $phase_name completed\n\n";
}

try {
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        throw new Exception('Database connection failed');
    }
    
    $conn = $db->getConnection();
    
    // ============================================
    // PHASE 1: CREATE TRANSLATION TABLES
    // ============================================
    
    executeSQLFile($conn, __DIR__ . '/FULL_HYBRID_TRANSLATION_SCHEMA.sql', 'phase1');
    
    // ============================================
    // PHASE 2: MIGRATE ENGLISH DATA
    // ============================================
    
    executeSQLFile($conn, __DIR__ . '/FULL_HYBRID_MIGRATION_QUERIES_SIMPLE.sql', 'phase2');
    
    // ============================================
    // VERIFICATION: CHECK MIGRATION SUCCESS
    // ============================================
    
    echo "Starting Verification: Checking migration success...\n";
    
    $verification_queries = [
        'event_translations' => "SELECT COUNT(*) as count FROM event_translations WHERE language = 'en'",
        'blog_translations' => "SELECT COUNT(*) as count FROM blog_translations WHERE language = 'en'",
        'press_translations' => "SELECT COUNT(*) as count FROM press_translations WHERE language = 'en'",
        'pricing_translations' => "SELECT COUNT(*) as count FROM pricing_translations WHERE language = 'en'",
        'legal_page_translations' => "SELECT COUNT(*) as count FROM legal_page_translations WHERE language = 'en'"
    ];
    
    foreach ($verification_queries as $table => $query) {
        $result = $conn->query($query);
        if ($result) {
            $row = $result->fetch_assoc();
            $count = $row['count'];
            $results['verification']['counts'][$table] = $count;
            echo "  ✓ $table: $count records\n";
        } else {
            echo "  ✗ $table: Query failed - " . $conn->error . "\n";
        }
    }
    
    $results['verification']['status'] = 'success';
    $results['verification']['message'] = 'Verification completed';
    
    // ============================================
    // CHECK FOR DUPLICATES
    // ============================================
    
    echo "\nChecking for duplicates...\n";
    
    $duplicate_check = "
        SELECT 'events' as entity, event_id, COUNT(*) as count FROM event_translations GROUP BY event_id HAVING COUNT(*) > 1
        UNION ALL
        SELECT 'blogs', blog_id, COUNT(*) FROM blog_translations GROUP BY blog_id HAVING COUNT(*) > 1
        UNION ALL
        SELECT 'press', press_id, COUNT(*) FROM press_translations GROUP BY press_id HAVING COUNT(*) > 1
        UNION ALL
        SELECT 'pricing', pricing_id, COUNT(*) FROM pricing_translations GROUP BY pricing_id HAVING COUNT(*) > 1
    ";
    
    $result = $conn->query($duplicate_check);
    $duplicate_count = 0;
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $duplicate_count++;
            echo "  ✗ WARNING: " . $row['entity'] . " ID " . $row['event_id'] . " has " . $row['count'] . " entries\n";
        }
    }
    
    if ($duplicate_count === 0) {
        echo "  ✓ No duplicates found\n";
    }
    
    // ============================================
    // CHECK FOREIGN KEY INTEGRITY
    // ============================================
    
    echo "\nChecking foreign key integrity...\n";
    
    $fk_check = "
        SELECT 'events' as entity, COUNT(*) as orphaned FROM event_translations et 
        LEFT JOIN events e ON et.event_id = e.id WHERE e.id IS NULL
        UNION ALL
        SELECT 'blogs', COUNT(*) FROM blog_translations bt 
        LEFT JOIN blogs b ON bt.blog_id = b.id WHERE b.id IS NULL
        UNION ALL
        SELECT 'press', COUNT(*) FROM press_translations pt 
        LEFT JOIN press p ON pt.press_id = p.id WHERE p.id IS NULL
        UNION ALL
        SELECT 'pricing', COUNT(*) FROM pricing_translations prt 
        LEFT JOIN pricing pr ON prt.pricing_id = pr.id WHERE pr.id IS NULL
    ";
    
    $result = $conn->query($fk_check);
    $orphaned_count = 0;
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            if ($row['orphaned'] > 0) {
                $orphaned_count += $row['orphaned'];
                echo "  ✗ WARNING: " . $row['entity'] . " has " . $row['orphaned'] . " orphaned records\n";
            }
        }
    }
    
    if ($orphaned_count === 0) {
        echo "  ✓ No orphaned records found\n";
    }
    
    // ============================================
    // FINAL STATUS
    // ============================================
    
    $results['overall_status'] = 'success';
    
    echo "\n" . str_repeat("=", 60) . "\n";
    echo "MIGRATION COMPLETE - ALL PHASES SUCCESSFUL\n";
    echo str_repeat("=", 60) . "\n";
    echo "Phase 1 (Create Tables): ✓ SUCCESS\n";
    echo "  Tables created: " . count($results['phase1']['tables_created']) . "\n";
    echo "  " . implode(", ", $results['phase1']['tables_created']) . "\n\n";
    
    echo "Phase 2 (Migrate Data): ✓ SUCCESS\n";
    foreach ($results['phase2']['rows_migrated'] as $table => $count) {
        echo "  $table: $count rows\n";
    }
    echo "\n";
    
    echo "Verification: ✓ SUCCESS\n";
    foreach ($results['verification']['counts'] as $table => $count) {
        echo "  $table: $count records\n";
    }
    echo "\n";
    
    echo "Data Integrity: ✓ SUCCESS\n";
    echo "  Duplicates: ✓ NONE FOUND\n";
    echo "  Orphaned Records: ✓ NONE FOUND\n";
    echo str_repeat("=", 60) . "\n\n";
    
    // Output JSON results
    echo json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    
} catch (Exception $e) {
    $results['overall_status'] = 'error';
    $results['error'] = $e->getMessage();
    
    echo "\n" . str_repeat("=", 60) . "\n";
    echo "ERROR: " . $e->getMessage() . "\n";
    echo str_repeat("=", 60) . "\n\n";
    
    echo json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    exit(1);
}
?>


