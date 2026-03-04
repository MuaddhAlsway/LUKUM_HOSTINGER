<?php
/**
 * Test Blog API - Debug endpoint
 */

header('Content-Type: application/json');

// Test 1: Check if we can output JSON
echo json_encode(['test' => 'success', 'timestamp' => time()]);
?>


