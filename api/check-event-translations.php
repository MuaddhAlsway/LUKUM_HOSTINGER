<?php
/**
 * Diagnostic script to check event translations in database
 */

header('Content-Type: application/json');
require_once 'db.php';

try {
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        throw new Exception('Database connection failed');
    }
    
    // Check if event_translations table exists
    $tableCheckQuery = "SHOW TABLES LIKE 'event_translations'";
    $tableResult = $db->getConnection()->query($tableCheckQuery);
    $translationsTableExists = $tableResult && $tableResult->num_rows > 0;
    
    if (!$translationsTableExists) {
        throw new Exception('event_translations table does not exist');
    }
    
    // Get event ID from parameter
    $eventId = $_GET['id'] ?? 58;
    
    // Check translations for this event
    $query = '
        SELECT 
            event_id,
            language,
            title,
            description,
            location,
            slug,
            created_at,
            updated_at
        FROM event_translations
        WHERE event_id = ?
        ORDER BY language ASC
    ';
    
    $stmt = $db->prepare($query);
    if (!$stmt) {
        throw new Exception('Prepare failed: ' . $db->getConnection()->error);
    }
    
    $stmt->bind_param('i', $eventId);
    if (!$stmt->execute()) {
        throw new Exception('Execute failed: ' . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $translations = [];
    
    while ($row = $result->fetch_assoc()) {
        $translations[] = $row;
    }
    
    // Also get the base event data
    $eventQuery = 'SELECT id, title, description, location FROM events WHERE id = ?';
    $eventStmt = $db->prepare($eventQuery);
    $eventStmt->bind_param('i', $eventId);
    $eventStmt->execute();
    $eventResult = $eventStmt->get_result();
    $event = $eventResult->fetch_assoc();
    
    echo json_encode([
        'success' => true,
        'event_id' => $eventId,
        'base_event' => $event,
        'translations' => $translations,
        'translation_count' => count($translations),
        'has_english' => count(array_filter($translations, fn($t) => $t['language'] === 'en')) > 0,
        'has_arabic' => count(array_filter($translations, fn($t) => $t['language'] === 'ar')) > 0
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>


