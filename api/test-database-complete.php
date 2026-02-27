<?php
/**
 * LAKUM Artspace - Complete Database Connection & Data Test
 * Comprehensive diagnostic tool to verify database and data
 */

header('Content-Type: application/json; charset=utf-8');

$report = [
    'timestamp' => date('Y-m-d H:i:s'),
    'php_version' => phpversion(),
    'server' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
    'tests' => [],
    'database' => [],
    'tables' => [],
    'data' => [],
    'issues' => [],
    'status' => 'INITIALIZING'
];

try {
    // Test 1: Load Configuration
    $report['tests'][] = [
        'name' => 'Load Configuration',
        'status' => 'RUNNING'
    ];
    
    require_once 'config.php';
    
    $report['tests'][0]['status'] = 'SUCCESS';
    $report['tests'][0]['config'] = [
        'DB_HOST' => DB_HOST,
        'DB_USER' => DB_USER,
        'DB_NAME' => DB_NAME,
        'DB_PORT' => DB_PORT,
        'API_URL' => API_URL,
        'SITE_URL' => SITE_URL
    ];
    
    // Test 2: Connect to MySQL (without database first)
    $report['tests'][] = [
        'name' => 'Connect to MySQL Server',
        'status' => 'RUNNING'
    ];
    
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, 'mysql');
    
    if ($conn->connect_error) {
        $report['tests'][1]['status'] = 'FAILED';
        $report['tests'][1]['error'] = $conn->connect_error;
        $report['issues'][] = [
            'severity' => 'CRITICAL',
            'issue' => 'Cannot connect to MySQL server',
            'details' => $conn->connect_error,
            'solution' => 'Check MySQL is running and credentials are correct'
        ];
        throw new Exception('MySQL connection failed: ' . $conn->connect_error);
    }
    
    $report['tests'][1]['status'] = 'SUCCESS';
    $report['tests'][1]['message'] = 'Connected to MySQL server';
    
    // Test 3: Check if database exists
    $report['tests'][] = [
        'name' => 'Check Database Existence',
        'status' => 'RUNNING'
    ];
    
    $dbCheckResult = $conn->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '" . DB_NAME . "'");
    $dbExists = $dbCheckResult->num_rows > 0;
    
    $report['tests'][2]['status'] = 'SUCCESS';
    $report['tests'][2]['database_exists'] = $dbExists;
    $report['tests'][2]['database_name'] = DB_NAME;
    
    if (!$dbExists) {
        $report['issues'][] = [
            'severity' => 'CRITICAL',
            'issue' => 'Database does not exist',
            'details' => "Database '{$DB_NAME}' not found",
            'solution' => 'Run setup-database.php to create the database'
        ];
    }
    
    // Test 4: Select database
    $report['tests'][] = [
        'name' => 'Select Database',
        'status' => 'RUNNING'
    ];
    
    if (!$conn->select_db(DB_NAME)) {
        $report['tests'][3]['status'] = 'FAILED';
        $report['tests'][3]['error'] = $conn->error;
        $report['issues'][] = [
            'severity' => 'CRITICAL',
            'issue' => 'Cannot select database',
            'details' => $conn->error,
            'solution' => 'Database may not exist or user lacks permissions'
        ];
        throw new Exception('Cannot select database: ' . $conn->error);
    }
    
    $report['tests'][3]['status'] = 'SUCCESS';
    $report['tests'][3]['message'] = "Selected database: {$DB_NAME}";
    
    // Test 5: List all tables
    $report['tests'][] = [
        'name' => 'List Tables',
        'status' => 'RUNNING'
    ];
    
    $tablesResult = $conn->query("SHOW TABLES");
    $tables = [];
    while ($row = $tablesResult->fetch_row()) {
        $tables[] = $row[0];
    }
    
    $report['tests'][4]['status'] = 'SUCCESS';
    $report['tests'][4]['tables_found'] = count($tables);
    $report['tests'][4]['tables'] = $tables;
    
    $requiredTables = ['events', 'blogs', 'press', 'pricing', 'event_gallery', 'admins'];
    $missingTables = array_diff($requiredTables, $tables);
    
    if (!empty($missingTables)) {
        $report['issues'][] = [
            'severity' => 'HIGH',
            'issue' => 'Missing required tables',
            'details' => 'Missing: ' . implode(', ', $missingTables),
            'solution' => 'Run setup-database.php to create tables'
        ];
    }
    
    // Test 6: Check data in each table
    $report['tests'][] = [
        'name' => 'Check Data in Tables',
        'status' => 'RUNNING'
    ];
    
    $dataCounts = [];
    $sampleData = [];
    
    foreach ($tables as $table) {
        // Count rows
        $countResult = $conn->query("SELECT COUNT(*) as count FROM `$table`");
        if ($countResult) {
            $row = $countResult->fetch_assoc();
            $count = (int)$row['count'];
            $dataCounts[$table] = $count;
            
            // Get sample data
            $sampleResult = $conn->query("SELECT * FROM `$table` LIMIT 1");
            if ($sampleResult && $sampleResult->num_rows > 0) {
                $sampleData[$table] = $sampleResult->fetch_assoc();
            }
        }
    }
    
    $report['tests'][5]['status'] = 'SUCCESS';
    $report['tests'][5]['data_counts'] = $dataCounts;
    
    // Check if any table has data
    $totalRecords = array_sum($dataCounts);
    if ($totalRecords === 0) {
        $report['issues'][] = [
            'severity' => 'MEDIUM',
            'issue' => 'No data in database',
            'details' => 'All tables are empty',
            'solution' => 'Insert mock data using INSERT_MOCK_DATA.sql or create data through admin panel'
        ];
    }
    
    // Test 7: Test Database class
    $report['tests'][] = [
        'name' => 'Test Database Class',
        'status' => 'RUNNING'
    ];
    
    $db = Database::getInstance();
    if ($db->isConnected()) {
        $report['tests'][6]['status'] = 'SUCCESS';
        $report['tests'][6]['message'] = 'Database class working correctly';
    } else {
        $report['tests'][6]['status'] = 'FAILED';
        $report['tests'][6]['message'] = 'Database class connection failed';
        $report['issues'][] = [
            'severity' => 'HIGH',
            'issue' => 'Database class not working',
            'details' => 'Database::getInstance() failed',
            'solution' => 'Check config.php and db.php files'
        ];
    }
    
    // Test 8: Test specific queries
    $report['tests'][] = [
        'name' => 'Test Sample Queries',
        'status' => 'RUNNING'
    ];
    
    $queryTests = [];
    
    // Test events query
    if (in_array('events', $tables)) {
        $eventsResult = $conn->query("SELECT * FROM events LIMIT 3");
        if ($eventsResult) {
            $queryTests['events'] = [
                'status' => 'SUCCESS',
                'count' => $eventsResult->num_rows,
                'sample' => []
            ];
            while ($row = $eventsResult->fetch_assoc()) {
                $queryTests['events']['sample'][] = [
                    'id' => $row['id'],
                    'title' => $row['title'],
                    'event_date' => $row['event_date']
                ];
            }
        }
    }
    
    // Test blogs query
    if (in_array('blogs', $tables)) {
        $blogsResult = $conn->query("SELECT * FROM blogs LIMIT 3");
        if ($blogsResult) {
            $queryTests['blogs'] = [
                'status' => 'SUCCESS',
                'count' => $blogsResult->num_rows,
                'sample' => []
            ];
            while ($row = $blogsResult->fetch_assoc()) {
                $queryTests['blogs']['sample'][] = [
                    'id' => $row['id'],
                    'title' => $row['title'],
                    'author' => $row['author']
                ];
            }
        }
    }
    
    $report['tests'][7]['status'] = 'SUCCESS';
    $report['tests'][7]['query_tests'] = $queryTests;
    
    // Populate database info
    $report['database'] = [
        'host' => DB_HOST,
        'port' => DB_PORT,
        'user' => DB_USER,
        'database' => DB_NAME,
        'exists' => $dbExists,
        'connected' => true
    ];
    
    $report['tables'] = [
        'total' => count($tables),
        'list' => $tables,
        'required' => $requiredTables,
        'missing' => $missingTables
    ];
    
    $report['data'] = [
        'total_records' => $totalRecords,
        'counts' => $dataCounts,
        'sample' => $sampleData
    ];
    
    // Determine overall status
    $criticalIssues = array_filter($report['issues'], function($i) { return $i['severity'] === 'CRITICAL'; });
    
    if (!empty($criticalIssues)) {
        $report['status'] = 'CRITICAL';
    } elseif (count($report['issues']) > 0) {
        $report['status'] = 'WARNING';
    } else {
        $report['status'] = 'SUCCESS';
    }
    
    $report['summary'] = [
        'database_connected' => true,
        'database_exists' => $dbExists,
        'tables_created' => count($tables) > 0,
        'has_data' => $totalRecords > 0,
        'all_required_tables' => empty($missingTables),
        'ready_for_use' => empty($criticalIssues) && $totalRecords > 0
    ];
    
    $conn->close();
    
} catch (Exception $e) {
    $report['status'] = 'ERROR';
    $report['error'] = $e->getMessage();
    $report['summary'] = [
        'database_connected' => false,
        'database_exists' => false,
        'tables_created' => false,
        'has_data' => false,
        'all_required_tables' => false,
        'ready_for_use' => false
    ];
}

http_response_code($report['status'] === 'SUCCESS' ? 200 : 500);
echo json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
?>
