<?php
/**
 * LAKUM Artspace - Complete Status Report
 * Comprehensive diagnostic and status report
 */

header('Content-Type: application/json');

$report = [
    'timestamp' => date('Y-m-d H:i:s'),
    'environment' => [],
    'configuration' => [],
    'database' => [],
    'api_endpoints' => [],
    'issues' => [],
    'recommendations' => []
];

try {
    // Environment Information
    $report['environment'] = [
        'php_version' => phpversion(),
        'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
        'os' => php_uname(),
        'memory_limit' => ini_get('memory_limit'),
        'max_execution_time' => ini_get('max_execution_time'),
        'upload_max_filesize' => ini_get('upload_max_filesize')
    ];
    
    // Configuration Check
    $report['configuration']['env_file'] = [
        'exists' => file_exists(__DIR__ . '/.env'),
        'readable' => is_readable(__DIR__ . '/.env'),
        'path' => __DIR__ . '/.env'
    ];
    
    $report['configuration']['config_php'] = [
        'exists' => file_exists(__DIR__ . '/config.php'),
        'readable' => is_readable(__DIR__ . '/config.php'),
        'path' => __DIR__ . '/config.php'
    ];
    
    // Load configuration
    require_once 'config.php';
    
    $report['configuration']['database_settings'] = [
        'DB_HOST' => DB_HOST,
        'DB_USER' => DB_USER,
        'DB_NAME' => DB_NAME,
        'DB_PORT' => DB_PORT,
        'API_URL' => API_URL,
        'SITE_URL' => SITE_URL
    ];
    
    // Database Connection Test
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    if ($db->isConnected()) {
        $report['database']['connection'] = 'SUCCESS';
        $report['database']['message'] = 'Connected to database successfully';
        
        // Get database info
        $infoResult = $conn->query("SELECT DATABASE() as db_name, VERSION() as version");
        if ($infoResult) {
            $info = $infoResult->fetch_assoc();
            $report['database']['info'] = $info;
        }
        
        // Check tables
        $tablesResult = $conn->query("SHOW TABLES");
        $tables = [];
        while ($row = $tablesResult->fetch_row()) {
            $tables[] = $row[0];
        }
        $report['database']['tables'] = $tables;
        
        // Count data in each table
        $report['database']['data_counts'] = [];
        foreach ($tables as $table) {
            $countResult = $conn->query("SELECT COUNT(*) as count FROM `$table`");
            if ($countResult) {
                $row = $countResult->fetch_assoc();
                $report['database']['data_counts'][$table] = (int)$row['count'];
            }
        }
        
        // Check for required tables
        $requiredTables = ['events', 'blogs', 'press', 'pricing', 'event_gallery', 'admins'];
        $missingTables = array_diff($requiredTables, $tables);
        
        if (!empty($missingTables)) {
            $report['issues'][] = [
                'severity' => 'HIGH',
                'issue' => 'Missing database tables',
                'details' => 'The following required tables are missing: ' . implode(', ', $missingTables),
                'solution' => 'Run the database setup script at /api/setup-database.php'
            ];
        }
        
        // Check for data
        $hasData = false;
        foreach ($report['database']['data_counts'] as $count) {
            if ($count > 0) {
                $hasData = true;
                break;
            }
        }
        
        if (!$hasData) {
            $report['issues'][] = [
                'severity' => 'MEDIUM',
                'issue' => 'No data in database',
                'details' => 'All tables are empty. The website will show no content.',
                'solution' => 'Insert sample data using INSERT_MOCK_DATA.sql or create data through the admin panel'
            ];
        }
        
    } else {
        $report['database']['connection'] = 'FAILED';
        $report['database']['message'] = 'Failed to connect to database';
        $report['issues'][] = [
            'severity' => 'CRITICAL',
            'issue' => 'Database connection failed',
            'details' => 'Cannot connect to the database. Check your configuration.',
            'solution' => 'Verify DB_HOST, DB_USER, DB_PASS, and DB_NAME in .env file'
        ];
    }
    
    // Check API endpoints
    $apiEndpoints = [
        'get_events.php',
        'create_event.php',
        'update_event.php',
        'delete_event.php',
        'get_blogs.php',
        'create_blog.php',
        'update_blog.php',
        'delete_blog.php',
        'get_press.php',
        'create_press.php',
        'update_press.php',
        'delete_press.php',
        'get_pricing.php',
        'create_pricing.php',
        'update_pricing.php',
        'delete_pricing.php',
        'auth.php'
    ];
    
    $report['api_endpoints']['total'] = count($apiEndpoints);
    $report['api_endpoints']['files'] = [];
    
    foreach ($apiEndpoints as $endpoint) {
        $path = __DIR__ . '/' . $endpoint;
        $report['api_endpoints']['files'][$endpoint] = [
            'exists' => file_exists($path),
            'readable' => is_readable($path),
            'size' => file_exists($path) ? filesize($path) : 0
        ];
        
        if (!file_exists($path)) {
            $report['issues'][] = [
                'severity' => 'HIGH',
                'issue' => 'Missing API endpoint',
                'details' => "API endpoint $endpoint is missing",
                'solution' => 'Recreate the missing API endpoint'
            ];
        }
    }
    
    // Check logs directory
    $logsDir = __DIR__ . '/../logs';
    if (!is_dir($logsDir)) {
        $report['issues'][] = [
            'severity' => 'LOW',
            'issue' => 'Logs directory missing',
            'details' => 'The logs directory does not exist',
            'solution' => 'Create the logs directory: mkdir -p ' . $logsDir
        ];
    } else {
        $report['configuration']['logs_directory'] = [
            'exists' => true,
            'writable' => is_writable($logsDir),
            'path' => $logsDir
        ];
    }
    
    // Check uploads directory
    $uploadsDir = __DIR__ . '/../uploads';
    if (!is_dir($uploadsDir)) {
        $report['issues'][] = [
            'severity' => 'LOW',
            'issue' => 'Uploads directory missing',
            'details' => 'The uploads directory does not exist',
            'solution' => 'Create the uploads directory: mkdir -p ' . $uploadsDir
        ];
    } else {
        $report['configuration']['uploads_directory'] = [
            'exists' => true,
            'writable' => is_writable($uploadsDir),
            'path' => $uploadsDir
        ];
    }
    
    // Recommendations
    if (empty($report['issues'])) {
        $report['recommendations'][] = 'All systems operational! The website should be working correctly.';
    } else {
        $criticalIssues = array_filter($report['issues'], function($issue) {
            return $issue['severity'] === 'CRITICAL';
        });
        
        if (!empty($criticalIssues)) {
            $report['recommendations'][] = 'CRITICAL ISSUES DETECTED: Please resolve critical issues immediately.';
        }
        
        $report['recommendations'][] = 'Review the issues list above and follow the recommended solutions.';
    }
    
    // Overall status
    $report['overall_status'] = empty($report['issues']) ? 'HEALTHY' : (
        count(array_filter($report['issues'], function($i) { return $i['severity'] === 'CRITICAL'; })) > 0 ? 'CRITICAL' : 'WARNING'
    );
    
} catch (Exception $e) {
    $report['error'] = $e->getMessage();
    $report['overall_status'] = 'ERROR';
}

http_response_code(200);
echo json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
?>

