<?php
/**
 * Fix Events Immediately - Direct Database Fix
 * Creates tables and populates with real event data from database
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once 'db.php';

try {
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        throw new Exception('Database connection failed');
    }
    
    $messages = [];
    
    // Step 1: Create event_gallery table
    $createGalleryQuery = '
        CREATE TABLE IF NOT EXISTS event_gallery (
            id INT PRIMARY KEY AUTO_INCREMENT,
            event_id INT NOT NULL,
            image_url VARCHAR(255) NOT NULL,
            display_order INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
            INDEX idx_event (event_id),
            INDEX idx_order (display_order)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ';
    
    $db->getConnection()->query($createGalleryQuery);
    $messages[] = '✓ event_gallery table ready';
    
    // Step 2: Create event_translations table
    $createTranslationsQuery = '
        CREATE TABLE IF NOT EXISTS event_translations (
            id INT PRIMARY KEY AUTO_INCREMENT,
            event_id INT NOT NULL,
            language VARCHAR(5) NOT NULL,
            title VARCHAR(255),
            description LONGTEXT,
            location VARCHAR(255),
            slug VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY unique_event_lang (event_id, language),
            FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
            INDEX idx_language (language),
            INDEX idx_slug (slug)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ';
    
    $db->getConnection()->query($createTranslationsQuery);
    $messages[] = '✓ event_translations table ready';
    
    // Step 3: Clear existing translations (to avoid duplicates)
    $db->getConnection()->query('TRUNCATE TABLE event_translations');
    $messages[] = '✓ Cleared existing translations';
    
    // Step 4: Get all events and create translations
    $eventsQuery = 'SELECT id, title, description, location FROM events ORDER BY id ASC';
    $result = $db->getConnection()->query($eventsQuery);
    
    if (!$result) {
        throw new Exception('Failed to query events: ' . $db->getConnection()->error);
    }
    
    $insertedCount = 0;
    $events = [];
    
    while ($event = $result->fetch_assoc()) {
        $eventId = $event['id'];
        $title = $event['title'];
        $description = $event['description'];
        $location = $event['location'];
        
        // Generate slug from title - proper slug generation
        $slug = strtolower($title);
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);  // Remove special chars
        $slug = preg_replace('/\s+/', '-', $slug);           // Replace spaces with hyphens
        $slug = preg_replace('/-+/', '-', $slug);            // Replace multiple hyphens with single
        $slug = trim($slug, '-');                            // Remove leading/trailing hyphens
        
        // Insert English translation - use INSERT IGNORE to skip duplicates
        $insertQuery = '
            INSERT IGNORE INTO event_translations (event_id, language, title, description, location, slug)
            VALUES (?, ?, ?, ?, ?, ?)
        ';
        
        $stmt = $db->prepare($insertQuery);
        if ($stmt) {
            $language = 'en';
            $stmt->bind_param('isssss', $eventId, $language, $title, $description, $location, $slug);
            if ($stmt->execute()) {
                $insertedCount++;
                $events[] = [
                    'id' => $eventId,
                    'title' => $title,
                    'slug' => $slug
                ];
            }
        }
    }
    
    $messages[] = "✓ Created $insertedCount event translations";
    
    // Step 5: Get count of events
    $countQuery = 'SELECT COUNT(*) as total FROM events';
    $result = $db->getConnection()->query($countQuery);
    $row = $result->fetch_assoc();
    $totalEvents = $row['total'];
    
    $messages[] = "✓ Total events in database: $totalEvents";
    
    echo json_encode([
        'success' => true,
        'messages' => $messages,
        'total_events' => $totalEvents,
        'total_translations' => $insertedCount,
        'events' => $events
    ], JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ], JSON_PRETTY_PRINT);
}
?>


