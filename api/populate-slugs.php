<?php
/**
 * LAKUM Artspace - Auto-Populate Slugs
 * Generates and populates slug column from event titles
 * Run once to initialize slugs
 */

header('Content-Type: application/json');
require_once 'config.php';

try {
    $db = Database::getInstance();
    
    // Step 1: Check if slug column exists
    $columnCheckQuery = "SHOW COLUMNS FROM events LIKE 'slug'";
    $columnResult = $db->getConnection()->query($columnCheckQuery);
    
    if (!$columnResult || $columnResult->num_rows === 0) {
        // Create slug column
        $createColumnQuery = "ALTER TABLE events ADD COLUMN slug VARCHAR(255) UNIQUE NULL AFTER title";
        if (!$db->getConnection()->query($createColumnQuery)) {
            throw new Exception("Failed to create slug column: " . $db->getConnection()->error);
        }
        echo json_encode(['success' => true, 'message' => 'Slug column created']);
    }
    
    // Step 2: Get all events without slugs
    $getEventsQuery = "SELECT id, title, title_en FROM events WHERE slug IS NULL OR slug = ''";
    $result = $db->getConnection()->query($getEventsQuery);
    
    if (!$result) {
        throw new Exception("Failed to fetch events: " . $db->getConnection()->error);
    }
    
    $updated = 0;
    $errors = [];
    
    while ($row = $result->fetch_assoc()) {
        // Generate slug from title_en or title
        $title = $row['title_en'] ?: $row['title'];
        
        // Normalize slug
        $slug = strtolower(trim($title));
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        $slug = preg_replace('/\s+/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');
        
        if (empty($slug)) {
            $slug = 'event-' . $row['id'];
        }
        
        // Update event with slug
        $updateQuery = "UPDATE events SET slug = ? WHERE id = ?";
        $stmt = $db->prepare($updateQuery);
        
        if (!$stmt) {
            $errors[] = "Event {$row['id']}: Failed to prepare statement";
            continue;
        }
        
        $stmt->bind_param('si', $slug, $row['id']);
        
        if (!$stmt->execute()) {
            $errors[] = "Event {$row['id']}: " . $stmt->error;
            continue;
        }
        
        $updated++;
    }
    
    // Step 3: Create index if not exists
    $indexQuery = "CREATE INDEX IF NOT EXISTS idx_events_slug ON events(slug)";
    $db->getConnection()->query($indexQuery);
    
    // Step 4: Verify
    $verifyQuery = "SELECT COUNT(*) as total, COUNT(slug) as with_slug FROM events";
    $verifyResult = $db->getConnection()->query($verifyQuery);
    $verifyRow = $verifyResult->fetch_assoc();
    
    echo json_encode([
        'success' => true,
        'message' => "Slugs populated successfully",
        'updated' => $updated,
        'total_events' => $verifyRow['total'],
        'events_with_slug' => $verifyRow['with_slug'],
        'errors' => $errors
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
