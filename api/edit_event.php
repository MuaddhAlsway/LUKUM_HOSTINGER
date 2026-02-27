<?php
/**
 * LAKUM Artspace - Edit Event API
 * Updates an event in the database with bilingual support
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');

require_once 'db.php';
require_once 'slug-utils.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (empty($input['id'])) {
        echo json_encode(['success' => false, 'message' => 'Event ID is required']);
        exit;
    }
    
    $event_id = (int)$input['id'];
    
    // Extract English fields (optional - only update if provided)
    $title_en = $input['title_en'] ?? $input['title'] ?? null;
    $description_en = $input['description_en'] ?? $input['description'] ?? null;
    $location_en = $input['location_en'] ?? $input['location'] ?? null;
    
    // Check if slug column exists in events table
    $columnCheckQuery = "SHOW COLUMNS FROM events LIKE 'slug'";
    $columnResult = $db->getConnection()->query($columnCheckQuery);
    $slugColumnExists = $columnResult && $columnResult->num_rows > 0;
    
    // Generate slug from English title if provided (language-independent, stored in base table)
    $slug = null;
    if ($slugColumnExists && $title_en !== null) {
        $slug = generateSlug($title_en);
        // Ensure uniqueness (exclude current event)
        $slug = makeUniqueSlug($slug, $db->getConnection(), 'events', $event_id);
    }
    
    // Extract Arabic fields (optional - only update if provided)
    $title_ar = $input['title_ar'] ?? null;
    $description_ar = $input['description_ar'] ?? null;
    $location_ar = $input['location_ar'] ?? null;
    
    // Extract base fields (optional)
    $event_date = $input['event_date'] ?? null;
    $event_time = $input['event_time'] ?? null;
    $event_end_time = $input['event_end_time'] ?? null;
    $end_date = $input['end_date'] ?? null;
    $cover_image = $input['cover_image'] ?? null;
    $video_url = $input['video_url'] ?? null;
    $is_featured = isset($input['is_featured']) ? (int)$input['is_featured'] : null;
    $category = $input['category'] ?? null;
    
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        echo json_encode(['success' => false, 'message' => 'Database connection failed']);
        exit;
    }
    
    // Step 1: Update base event fields (only if provided)
    $updateFields = [];
    $updateParams = [];
    $updateTypes = '';
    
    if ($event_date !== null) {
        $updateFields[] = 'event_date = ?';
        $updateParams[] = $event_date;
        $updateTypes .= 's';
    }
    if ($event_time !== null) {
        $updateFields[] = 'event_time = ?';
        $updateParams[] = $event_time;
        $updateTypes .= 's';
    }
    if ($event_end_time !== null) {
        $updateFields[] = 'event_end_time = ?';
        $updateParams[] = $event_end_time;
        $updateTypes .= 's';
    }
    if ($end_date !== null) {
        $updateFields[] = 'end_date = ?';
        $updateParams[] = $end_date;
        $updateTypes .= 's';
    }
    if ($cover_image !== null) {
        $updateFields[] = 'cover_image = ?';
        $updateParams[] = $cover_image;
        $updateTypes .= 's';
    }
    if ($video_url !== null) {
        $updateFields[] = 'video_url = ?';
        $updateParams[] = $video_url;
        $updateTypes .= 's';
    }
    if ($is_featured !== null) {
        $updateFields[] = 'is_featured = ?';
        $updateParams[] = $is_featured;
        $updateTypes .= 'i';
    }
    if ($category !== null) {
        $updateFields[] = 'category = ?';
        $updateParams[] = $category;
        $updateTypes .= 's';
    }
    if ($slugColumnExists && $slug !== null) {
        $updateFields[] = 'slug = ?';
        $updateParams[] = $slug;
        $updateTypes .= 's';
    }
    
    if (!empty($updateFields)) {
        $updateEventQuery = 'UPDATE events SET ' . implode(', ', $updateFields) . ' WHERE id = ?';
        $updateParams[] = $event_id;
        $updateTypes .= 'i';
        
        $stmt = $db->prepare($updateEventQuery);
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $db->getConnection()->error);
        }
        
        $stmt->bind_param($updateTypes, ...$updateParams);
        if (!$stmt->execute()) {
            throw new Exception('Execute failed: ' . $stmt->error);
        }
    }
    
    // Step 2: Update English translation (UPSERT - only if provided, NO SLUG)
    if ($title_en !== null || $description_en !== null || $location_en !== null) {
        $updateEnglishQuery = '
            INSERT INTO event_translations (event_id, language, title, description, location)
            VALUES (?, "en", ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                title = IF(VALUES(title) IS NOT NULL, VALUES(title), title),
                description = IF(VALUES(description) IS NOT NULL, VALUES(description), description),
                location = IF(VALUES(location) IS NOT NULL, VALUES(location), location),
                updated_at = CURRENT_TIMESTAMP
        ';
        
        $stmt = $db->prepare($updateEnglishQuery);
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $db->getConnection()->error);
        }
        
        $stmt->bind_param('isss', $event_id, $title_en, $description_en, $location_en);
        if (!$stmt->execute()) {
            throw new Exception('Execute failed: ' . $stmt->error);
        }
    }
    
    // Step 3: Update Arabic translation (UPSERT - only if provided, NO SLUG)
    $arabicUpdated = false;
    if ($title_ar !== null || $description_ar !== null || $location_ar !== null) {
        $updateArabicQuery = '
            INSERT INTO event_translations (event_id, language, title, description, location)
            VALUES (?, "ar", ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                title = IF(VALUES(title) IS NOT NULL, VALUES(title), title),
                description = IF(VALUES(description) IS NOT NULL, VALUES(description), description),
                location = IF(VALUES(location) IS NOT NULL, VALUES(location), location),
                updated_at = CURRENT_TIMESTAMP
        ';
        
        $stmt = $db->prepare($updateArabicQuery);
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $db->getConnection()->error);
        }
        
        $stmt->bind_param('isss', $event_id, $title_ar, $description_ar, $location_ar);
        if (!$stmt->execute()) {
            throw new Exception('Execute failed: ' . $stmt->error);
        }
        $arabicUpdated = true;
    }
    
    // Handle gallery images if provided
    $galleryImages = $input['gallery_images'] ?? [];
    
    if (!empty($galleryImages)) {
        // Insert gallery images
        $galleryQuery = 'INSERT INTO event_gallery (event_id, image_url, display_order) VALUES (?, ?, ?)';
        $galleryStmt = $db->prepare($galleryQuery);
        
        if ($galleryStmt) {
            $displayOrder = 1;
            foreach ($galleryImages as $image) {
                $imageUrl = $image['image_url'] ?? '';
                $galleryStmt->bind_param('isi', $event_id, $imageUrl, $displayOrder);
                $galleryStmt->execute();
                $displayOrder++;
            }
        }
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Event updated successfully',
        'event_id' => $event_id,
        'slug' => $slug,
        'updates' => [
            'base_fields' => !empty($updateFields),
            'english' => ($title_en !== null || $description_en !== null || $location_en !== null),
            'arabic' => $arabicUpdated
        ]
    ]);
    
} catch (Exception $e) {
    error_log('Edit Event Error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
