<?php
/**
 * Debug API Error - Check what's going wrong with get_event_details
 */

header('Content-Type: application/json; charset=utf-8');

require_once 'config.php';

try {
    $db = Database::getInstance();
    
    // Test 1: Check exhibitions table exists
    $exhibitionsTableCheck = $db->getConnection()->query("SHOW TABLES LIKE 'exhibitions'");
    $exhibitionsExists = $exhibitionsTableCheck && $exhibitionsTableCheck->num_rows > 0;
    
    // Test 2: Check events table exists
    $eventsTableCheck = $db->getConnection()->query("SHOW TABLES LIKE 'events'");
    $eventsExists = $eventsTableCheck && $eventsTableCheck->num_rows > 0;
    
    // Test 3: Get sample IDs from exhibitions
    $exhibitionIds = [];
    if ($exhibitionsExists) {
        $result = $db->getConnection()->query("SELECT id, title_en FROM exhibitions LIMIT 3");
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $exhibitionIds[] = $row;
            }
        }
    }
    
    // Test 4: Get sample IDs from events
    $eventIds = [];
    if ($eventsExists) {
        $result = $db->getConnection()->query("SELECT id, title FROM events LIMIT 3");
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $eventIds[] = $row;
            }
        }
    }
    
    // Test 5: Try fetching an exhibition directly
    $testExhibitionId = isset($exhibitionIds[0]['id']) ? $exhibitionIds[0]['id'] : null;
    $testExhibition = null;
    
    if ($testExhibitionId) {
        $testQuery = "SELECT id, title_en, event_video FROM exhibitions WHERE id = ? LIMIT 1";
        $testStmt = $db->prepare($testQuery);
        if ($testStmt) {
            $testStmt->bind_param('i', $testExhibitionId);
            if ($testStmt->execute()) {
                $testExhibition = $testStmt->get_result()->fetch_assoc();
            }
        }
    }
    
    echo json_encode([
        'success' => true,
        'tables' => [
            'exhibitions' => $exhibitionsExists ? 'EXISTS' : 'MISSING',
            'events' => $eventsExists ? 'EXISTS' : 'MISSING'
        ],
        'sample_exhibitions' => $exhibitionIds,
        'sample_events' => $eventIds,
        'test_exhibition_fetch' => $testExhibition,
        'next_steps' => 'Try: /api/get_event_details.php?id=' . ($exhibitionIds[0]['id'] ?? '1')
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
}
?>
