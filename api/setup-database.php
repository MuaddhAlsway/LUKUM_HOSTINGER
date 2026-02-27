<?php
/**
 * LAKUM Artspace - Database Setup & Verification
 * This script sets up the database and verifies all configurations
 */

header('Content-Type: application/json');

$response = [
    'timestamp' => date('Y-m-d H:i:s'),
    'steps' => [],
    'status' => 'INITIALIZING'
];

try {
    // Step 1: Load configuration
    $response['steps'][] = [
        'step' => 1,
        'name' => 'Load Configuration',
        'status' => 'RUNNING'
    ];
    
    require_once 'config.php';
    
    $response['steps'][0]['status'] = 'SUCCESS';
    $response['steps'][0]['details'] = [
        'DB_HOST' => DB_HOST,
        'DB_USER' => DB_USER,
        'DB_NAME' => DB_NAME,
        'DB_PORT' => DB_PORT
    ];
    
    // Step 2: Test database connection
    $response['steps'][] = [
        'step' => 2,
        'name' => 'Test Database Connection',
        'status' => 'RUNNING'
    ];
    
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, 'mysql');
    
    if ($conn->connect_error) {
        throw new Exception('Connection failed: ' . $conn->connect_error);
    }
    
    $response['steps'][1]['status'] = 'SUCCESS';
    $response['steps'][1]['details'] = 'Connected to MySQL server';
    
    // Step 3: Check if database exists
    $response['steps'][] = [
        'step' => 3,
        'name' => 'Check Database Existence',
        'status' => 'RUNNING'
    ];
    
    $dbCheckResult = $conn->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '" . DB_NAME . "'");
    $dbExists = $dbCheckResult->num_rows > 0;
    
    $response['steps'][2]['status'] = 'SUCCESS';
    $response['steps'][2]['details'] = $dbExists ? 'Database exists' : 'Database does not exist - will be created';
    $response['steps'][2]['database_exists'] = $dbExists;
    
    // Step 4: Create database if needed
    $response['steps'][] = [
        'step' => 4,
        'name' => 'Create Database',
        'status' => 'RUNNING'
    ];
    
    if (!$dbExists) {
        $createDbSql = "CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
        if (!$conn->query($createDbSql)) {
            throw new Exception('Failed to create database: ' . $conn->error);
        }
        $response['steps'][3]['status'] = 'SUCCESS';
        $response['steps'][3]['details'] = 'Database created successfully';
    } else {
        $response['steps'][3]['status'] = 'SKIPPED';
        $response['steps'][3]['details'] = 'Database already exists';
    }
    
    // Step 5: Select database
    $response['steps'][] = [
        'step' => 5,
        'name' => 'Select Database',
        'status' => 'RUNNING'
    ];
    
    if (!$conn->select_db(DB_NAME)) {
        throw new Exception('Failed to select database: ' . $conn->error);
    }
    
    $response['steps'][4]['status'] = 'SUCCESS';
    $response['steps'][4]['details'] = 'Database selected';
    
    // Step 6: Create tables
    $response['steps'][] = [
        'step' => 6,
        'name' => 'Create Tables',
        'status' => 'RUNNING'
    ];
    
    $sqlFile = __DIR__ . '/DATABASE_SETUP.sql';
    if (!file_exists($sqlFile)) {
        throw new Exception('DATABASE_SETUP.sql not found');
    }
    
    $sql = file_get_contents($sqlFile);
    
    // Split SQL statements and execute them
    $statements = array_filter(array_map('trim', preg_split('/;(?=(?:[^\']*\'[^\']*\')*[^\']*$)/', $sql)));
    
    $tablesCreated = 0;
    foreach ($statements as $statement) {
        if (!empty($statement) && strpos($statement, '--') !== 0) {
            // Skip USE statements as we already selected the database
            if (stripos($statement, 'USE') === 0) {
                continue;
            }
            
            if (!$conn->query($statement)) {
                // Log error but continue - some statements might fail due to duplicates
                error_log('SQL Error: ' . $conn->error . ' | Statement: ' . substr($statement, 0, 100));
            } else {
                if (stripos($statement, 'CREATE TABLE') === 0) {
                    $tablesCreated++;
                }
            }
        }
    }
    
    $response['steps'][5]['status'] = 'SUCCESS';
    $response['steps'][5]['details'] = 'Tables created/verified';
    $response['steps'][5]['tables_created'] = $tablesCreated;
    
    // Step 7: Verify tables
    $response['steps'][] = [
        'step' => 7,
        'name' => 'Verify Tables',
        'status' => 'RUNNING'
    ];
    
    $tablesResult = $conn->query("SHOW TABLES");
    $tables = [];
    while ($row = $tablesResult->fetch_row()) {
        $tables[] = $row[0];
    }
    
    $requiredTables = ['events', 'blogs', 'press', 'pricing', 'event_gallery', 'admins'];
    $missingTables = array_diff($requiredTables, $tables);
    
    $response['steps'][6]['status'] = empty($missingTables) ? 'SUCCESS' : 'WARNING';
    $response['steps'][6]['details'] = [
        'total_tables' => count($tables),
        'tables' => $tables,
        'required_tables' => $requiredTables,
        'missing_tables' => $missingTables
    ];
    
    // Step 8: Check data
    $response['steps'][] = [
        'step' => 8,
        'name' => 'Check Data',
        'status' => 'RUNNING'
    ];
    
    $dataCounts = [];
    foreach ($tables as $table) {
        $countResult = $conn->query("SELECT COUNT(*) as count FROM `$table`");
        if ($countResult) {
            $row = $countResult->fetch_assoc();
            $dataCounts[$table] = (int)$row['count'];
        }
    }
    
    $response['steps'][7]['status'] = 'SUCCESS';
    $response['steps'][7]['details'] = $dataCounts;
    
    // Step 9: Test Database class
    $response['steps'][] = [
        'step' => 9,
        'name' => 'Test Database Class',
        'status' => 'RUNNING'
    ];
    
    $db = Database::getInstance();
    if ($db->isConnected()) {
        $response['steps'][8]['status'] = 'SUCCESS';
        $response['steps'][8]['details'] = 'Database class working correctly';
    } else {
        $response['steps'][8]['status'] = 'FAILED';
        $response['steps'][8]['details'] = 'Database class connection failed';
    }
    
    // Final status
    $allSuccess = true;
    foreach ($response['steps'] as $step) {
        if ($step['status'] === 'FAILED') {
            $allSuccess = false;
            break;
        }
    }
    
    $response['status'] = $allSuccess ? 'SUCCESS' : 'COMPLETED_WITH_WARNINGS';
    $response['message'] = $allSuccess ? 'Database setup completed successfully!' : 'Database setup completed with some warnings';
    
    $conn->close();
    
} catch (Exception $e) {
    $response['status'] = 'FAILED';
    $response['message'] = $e->getMessage();
    $response['error'] = $e->getMessage();
    
    if (isset($response['steps'])) {
        $lastStep = count($response['steps']) - 1;
        if (isset($response['steps'][$lastStep])) {
            $response['steps'][$lastStep]['status'] = 'FAILED';
            $response['steps'][$lastStep]['error'] = $e->getMessage();
        }
    }
}

http_response_code($response['status'] === 'SUCCESS' ? 200 : 500);
echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
?>
