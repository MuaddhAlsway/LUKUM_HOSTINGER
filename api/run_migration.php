<?php
/**
 * LAKUM Artspace - Database Migration Runner
 * Adds bilingual columns to all tables
 */

header('Content-Type: application/json');

try {
    $conn = new mysqli('localhost', 'root', '', 'lakum_artspace');
    
    if ($conn->connect_error) {
        throw new Exception('Database connection failed: ' . $conn->connect_error);
    }
    
    // Read the migration SQL file
    $sqlFile = __DIR__ . '/ADD_BILINGUAL_COLUMNS.sql';
    
    if (!file_exists($sqlFile)) {
        throw new Exception('Migration file not found: ' . $sqlFile);
    }
    
    $sql = file_get_contents($sqlFile);
    
    // Split by semicolon and execute each statement
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    $executed = 0;
    $errors = [];
    
    foreach ($statements as $statement) {
        if (empty($statement)) {
            continue;
        }
        
        if (!$conn->query($statement)) {
            $errors[] = [
                'statement' => substr($statement, 0, 100) . '...',
                'error' => $conn->error
            ];
        } else {
            $executed++;
        }
    }
    
    $conn->close();
    
    if (!empty($errors)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Migration completed with errors',
            'executed' => $executed,
            'errors' => $errors
        ]);
    } else {
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Migration completed successfully',
            'executed' => $executed,
            'statements' => count($statements)
        ]);
    }
    
} catch (Exception $e) {
    error_log('Migration Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>


