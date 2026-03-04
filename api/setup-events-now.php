<?php
/**
 * Setup Events Now - Complete Event System Setup
 * Creates tables and populates with real event data
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
    
    // Step 1: Create event_gallery table if it doesn't exist
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
    
    if ($db->getConnection()->query($createGalleryQuery)) {
        $messages[] = '✓ event_gallery table created/verified';
    } else {
        throw new Exception('Failed to create event_gallery table: ' . $db->getConnection()->error);
    }
    
    // Step 2: Create event_translations table if it doesn't exist
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
    
    if ($db->getConnection()->query($createTranslationsQuery)) {
        $messages[] = '✓ event_translations table created/verified';
    } else {
        throw new Exception('Failed to create event_translations table: ' . $db->getConnection()->error);
    }
    
    // Step 3: Migrate existing events to translations table
    $migrateQuery = '
        INSERT INTO event_translations (event_id, language, title, description, location, slug)
        SELECT 
            e.id,
            "en" as language,
            e.title,
            e.description,
            e.location,
            LOWER(REPLACE(REPLACE(REPLACE(e.title, " ", "-"), ".", ""), ",", "")) as slug
        FROM events e
        WHERE NOT EXISTS (
            SELECT 1 FROM event_translations et 
            WHERE et.event_id = e.id AND et.language = "en"
        )
        ON DUPLICATE KEY UPDATE
            title = VALUES(title),
            description = VALUES(description),
            location = VALUES(location),
            slug = VALUES(slug)
    ';
    
    if ($db->getConnection()->query($migrateQuery)) {
        $affectedRows = $db->getConnection()->affected_rows;
        $messages[] = "✓ Migrated $affectedRows events to translations table";
    } else {
        throw new Exception('Migration failed: ' . $db->getConnection()->error);
    }
    
    // Step 4: Get count of events
    $countQuery = 'SELECT COUNT(*) as total FROM events';
    $result = $db->getConnection()->query($countQuery);
    $row = $result->fetch_assoc();
    $totalEvents = $row['total'];
    
    // Step 5: Get count of translations
    $translationCountQuery = 'SELECT COUNT(*) as total FROM event_translations';
    $result = $db->getConnection()->query($translationCountQuery);
    $row = $result->fetch_assoc();
    $totalTranslations = $row['total'];
    
    $messages[] = "✓ Total events in database: $totalEvents";
    $messages[] = "✓ Total translations: $totalTranslations";
    
    // Step 6: List all events
    $listQuery = '
        SELECT e.id, e.title, e.event_date, t.slug
        FROM events e
        LEFT JOIN event_translations t ON e.id = t.event_id AND t.language = "en"
        ORDER BY e.id ASC
    ';
    $result = $db->getConnection()->query($listQuery);
    $events = [];
    while ($row = $result->fetch_assoc()) {
        $events[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'messages' => $messages,
        'total_events' => $totalEvents,
        'total_translations' => $totalTranslations,
        'events' => $events
    ], JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'error' => $e->getMessage()
    ], JSON_PRETTY_PRINT);
}
?>

