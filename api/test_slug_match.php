<?php
/**
 * Test slug matching - verify fuzzy match works
 */

header('Content-Type: application/json; charset=utf-8');

require_once 'config.php';

try {
    $db = Database::getInstance();
    
    // Test 1: Get all exhibitions
    $result = $db->getConnection()->query("SELECT id, title_en FROM exhibitions");
    $exhibitions = [];
    while ($row = $result->fetch_assoc()) {
        $exhibitions[] = $row;
    }
    
    // Test 2: Try matching "ampa" against titles using LOWER() and LIKE
    $testMatches = [];
    foreach ($exhibitions as $ex) {
        $titleLower = strtolower($ex['title_en']);
        $searchTerm = "ampa";
        if (strpos($titleLower, $searchTerm) !== false) {
            $testMatches[] = [
                'id' => $ex['id'],
                'title' => $ex['title_en'],
                'title_lower' => $titleLower,
                'match_type' => 'PHP strpos match'
            ];
        }
    }
    
    // Test 3: Try database LIKE match
    $likeQuery = "SELECT id, title_en FROM exhibitions WHERE LOWER(title_en) LIKE LOWER(?) LIMIT 5";
    $likeStmt = $db->prepare($likeQuery);
    $databaseMatches = [];
    if ($likeStmt) {
        $searchTerm = "%ampa%";
        $likeStmt->bind_param('s', $searchTerm);
        if ($likeStmt->execute()) {
            $likeResult = $likeStmt->get_result();
            while ($row = $likeResult->fetch_assoc()) {
                $databaseMatches[] = $row;
            }
        }
    }
    
    echo json_encode([
        'success' => true,
        'exhibitions_count' => count($exhibitions),
        'all_exhibitions' => $exhibitions,
        'php_strpos_matches' => $testMatches,
        'database_like_matches' => $databaseMatches,
        'test_search_term' => 'ampa',
        'message' => 'If database_like_matches is empty, LOWER() might not be working in your MySQL'
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
