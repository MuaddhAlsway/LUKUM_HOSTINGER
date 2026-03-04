<?php
/**
 * Create Event Translations Table
 * Adds bilingual support to events
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once 'db.php';

try {
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        throw new Exception('Database connection failed');
    }
    
    // Create event_translations table
    $createTableQuery = '
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
    
    if (!$db->getConnection()->query($createTableQuery)) {
        throw new Exception('Create table failed: ' . $db->getConnection()->error);
    }
    
    // Create event_gallery table if it doesn't exist
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
    
    if (!$db->getConnection()->query($createGalleryQuery)) {
        throw new Exception('Create gallery table failed: ' . $db->getConnection()->error);
    }
    
    // Migrate existing event data to translations table
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
    ';
    
    if (!$db->getConnection()->query($migrateQuery)) {
        throw new Exception('Migration failed: ' . $db->getConnection()->error);
    }
    
    // Get count of migrated records
    $countQuery = 'SELECT COUNT(*) as count FROM event_translations';
    $result = $db->getConnection()->query($countQuery);
    $row = $result->fetch_assoc();
    $count = $row['count'];
    
    echo json_encode([
        'success' => true,
        'message' => 'Event translations table created and data migrated successfully',
        'translations_count' => $count
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>

