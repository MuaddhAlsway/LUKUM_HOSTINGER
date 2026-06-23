<?php
/**
 * Migrate exhibition events from events table to exhibitions table
 * This moves all events with category='exhibition' to the exhibitions table
 */

header('Content-Type: application/json; charset=utf-8');

require_once 'config.php';

try {
    $db = Database::getInstance();
    
    // Get all events marked as exhibitions
    $exhibitionEventsResult = $db->getConnection()->query(
        "SELECT * FROM events WHERE category = 'exhibition' ORDER BY id"
    );
    
    if (!$exhibitionEventsResult) {
        throw new Exception('Query failed: ' . $db->getConnection()->error);
    }
    
    $migratedCount = 0;
    $errors = [];
    
    // For each exhibition event, insert into exhibitions table
    while ($event = $exhibitionEventsResult->fetch_assoc()) {
        $id = $event['id'];
        $title_en = $event['title_en'] ?? $event['title'];
        $title_ar = $event['title_ar'] ?? '';
        $description_en = $event['description_en'] ?? $event['description'];
        $description_ar = $event['description_ar'] ?? '';
        $location_en = $event['location_en'] ?? $event['location'];
        $location_ar = $event['location_ar'] ?? '';
        $exhibition_date = $event['event_date'];
        $exhibition_time = $event['event_time'];
        $exhibition_end_time = $event['event_end_time'];
        $end_date = $event['end_date'];
        $cover_image = $event['cover_image'];
        $event_video = $event['video_url'] ?? null;
        $is_featured = $event['is_featured'] ?? 0;
        
        // Check if already exists in exhibitions
        $checkQuery = "SELECT id FROM exhibitions WHERE id = ?";
        $checkStmt = $db->prepare($checkQuery);
        $checkStmt->bind_param('i', $id);
        $checkStmt->execute();
        $exists = $checkStmt->get_result()->num_rows > 0;
        
        if ($exists) {
            error_log("Exhibition ID $id already exists, skipping");
            continue;
        }
        
        // Try to insert into exhibitions table
        $insertQuery = "INSERT INTO exhibitions (
            id, title_en, title_ar, description_en, description_ar,
            location_en, location_ar, exhibition_date, exhibition_time,
            exhibition_end_time, end_date, cover_image, event_video,
            category, is_featured, created_at, updated_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
        
        $insertStmt = $db->prepare($insertQuery);
        if (!$insertStmt) {
            $errors[] = "Prepare error for ID $id: " . $db->getConnection()->error;
            continue;
        }
        
        $category = 'exhibition';
        
        $bindResult = $insertStmt->bind_param(
            'issssssssssssii',
            $id, $title_en, $title_ar, $description_en, $description_ar,
            $location_en, $location_ar, $exhibition_date, $exhibition_time,
            $exhibition_end_time, $end_date, $cover_image, $event_video,
            $category, $is_featured
        );
        
        if (!$bindResult) {
            $errors[] = "Bind error for ID $id: " . $insertStmt->error;
            continue;
        }
        
        if ($insertStmt->execute()) {
            $migratedCount++;
            error_log("Migrated exhibition ID $id: $title_en");
        } else {
            $errors[] = "Execute error for ID $id: " . $insertStmt->error;
        }
    }
    
    echo json_encode([
        'success' => true,
        'migrated_count' => $migratedCount,
        'errors' => $errors,
        'message' => "Successfully migrated $migratedCount exhibition events to exhibitions table"
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
