<?php
/**
 * Execute SQL file
 */

require_once __DIR__ . '/../config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    $conn = new mysqli($host, $user, $pass, $db);
    
    if ($conn->connect_error) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'DB Error: ' . $conn->connect_error]);
        exit;
    }
    
    $input = json_decode(file_get_contents('php://input'), true);
    $sqlFile = $input['sql_file'] ?? '';
    
    if (empty($sqlFile)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'SQL file not specified']);
        exit;
    }
    
    // Security: only allow files in root directory
    $filePath = dirname(__DIR__) . '/' . basename($sqlFile);
    
    if (!file_exists($filePath)) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'SQL file not found: ' . $filePath]);
        exit;
    }
    
    $sql = file_get_contents($filePath);
    
    // Execute multiple statements
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    $executed = 0;
    $errors = [];
    
    foreach ($statements as $statement) {
        if (empty($statement)) continue;
        
        if (!$conn->query($statement)) {
            $errors[] = $conn->error;
        } else {
            $executed++;
        }
    }
    
    $conn->close();
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Migration executed',
        'statements_executed' => $executed,
        'errors' => $errors,
        'has_errors' => count($errors) > 0
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>

