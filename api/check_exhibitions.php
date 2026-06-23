<?php
/**
 * Check exhibitions in database - verify they exist and can be found
 */

header('Content-Type: application/json; charset=utf-8');

require_once 'config.php';

try {
    $db = Database::getInstance();
    
    // Get all exhibitions
    $result = $db->getConnection()->query("SELECT id, title_en, title_ar FROM exhibitions ORDER BY id DESC");
    
    $exhibitions = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $exhibitions[] = $row;
        }
    }
    
    // Test fuzzy match on first exhibition
    $testMatch = null;
    if (!empty($exhibitions)) {
        $firstTitle = $exhibitions[0]['title_en'];
        // Try matching partial title like "ampa" for "AMPM"
        $partialMatch = substr(strtolower($firstTitle), 0, 4); // Get first 4 chars
        
        $fuzzyQuery = "SELECT id, title_en FROM exhibitions WHERE title_en LIKE ? LIMIT 1";
        $fuzzyStmt = $db->prepare($fuzzyQuery);
        if ($fuzzyStmt) {
            $searchTerm = "%" . $partialMatch . "%";
            $fuzzyStmt->bind_param('s', $searchTerm);
            $fuzzyStmt->execute();
            $fuzzyResult = $fuzzyStmt->get_result();
            $testMatch = $fuzzyResult->fetch_assoc();
        }
    }
    
    echo json_encode([
        'success' => true,
        'count' => count($exhibitions),
        'exhibitions' => $exhibitions,
        'fuzzy_match_test' => [
            'tested_partial' => isset($partialMatch) ? $partialMatch : null,
            'result' => $testMatch
        ],
        'next_step' => 'Try: /event.php?title=' . (isset($exhibitions[0]['title_en']) ? strtolower(str_replace(' ', '-', $exhibitions[0]['title_en'])) : 'ampm')
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
