<?php
/**
 * LAKUM Artspace - Slug Utility Functions
 * Centralized slug generation and validation
 * 
 * ARCHITECTURE:
 * - Single slug per entity (language-independent)
 * - Generated from English title only
 * - Stored in base table (events, blogs, press)
 * - Used for all languages
 */

/**
 * Generate slug from text
 * 
 * Rules:
 * - Convert to lowercase
 * - Replace spaces with hyphens
 * - Remove special characters
 * - Collapse multiple hyphens
 * - Trim leading/trailing hyphens
 * 
 * @param string $text The text to slugify
 * @return string The generated slug
 */
function generateSlug($text) {
    if (empty($text)) {
        return '';
    }
    
    // Convert to lowercase
    $slug = strtolower($text);
    
    // Remove non-alphanumeric characters except spaces and hyphens
    $slug = preg_replace('/[^\w\s-]/', '', $slug);
    
    // Replace spaces with hyphens
    $slug = preg_replace('/\s+/', '-', $slug);
    
    // Collapse multiple consecutive hyphens
    $slug = preg_replace('/-+/', '-', $slug);
    
    // Remove leading/trailing hyphens
    $slug = trim($slug, '-');
    
    return $slug;
}

/**
 * Check if slug already exists in database
 * 
 * @param string $slug The slug to check
 * @param mysqli $conn Database connection
 * @param string $table The table to check (events, blogs, press)
 * @param int|null $excludeId Optional ID to exclude (for updates)
 * @return bool True if slug exists, false otherwise
 */
function slugExists($slug, $conn, $table, $excludeId = null) {
    $query = "SELECT id FROM $table WHERE slug = ?";
    $params = [$slug];
    $types = 's';
    
    if ($excludeId !== null) {
        $query .= " AND id != ?";
        $params[] = $excludeId;
        $types .= 'i';
    }
    
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        return false;
    }
    
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    $exists = $result->num_rows > 0;
    $stmt->close();
    
    return $exists;
}

/**
 * Generate unique slug
 * 
 * If slug already exists, appends -1, -2, etc. until unique
 * 
 * @param string $slug The base slug
 * @param mysqli $conn Database connection
 * @param string $table The table to check (events, blogs, press)
 * @param int|null $excludeId Optional ID to exclude (for updates)
 * @return string The unique slug
 */
function makeUniqueSlug($slug, $conn, $table, $excludeId = null) {
    if (!slugExists($slug, $conn, $table, $excludeId)) {
        return $slug;
    }
    
    $original = $slug;
    $count = 1;
    
    while (slugExists($slug, $conn, $table, $excludeId)) {
        $slug = $original . '-' . $count;
        $count++;
    }
    
    return $slug;
}

/**
 * Get event by slug
 * 
 * @param string $slug The slug to search for
 * @param mysqli $conn Database connection
 * @return array|null Event data or null if not found
 */
function getEventBySlug($slug, $conn) {
    $query = 'SELECT id FROM events WHERE slug = ? LIMIT 1';
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        return null;
    }
    
    $stmt->bind_param('s', $slug);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    
    return $row;
}

/**
 * Get blog by slug
 * 
 * @param string $slug The slug to search for
 * @param mysqli $conn Database connection
 * @return array|null Blog data or null if not found
 */
function getBlogBySlug($slug, $conn) {
    $query = 'SELECT id FROM blogs WHERE slug = ? LIMIT 1';
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        return null;
    }
    
    $stmt->bind_param('s', $slug);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    
    return $row;
}

/**
 * Get press by slug
 * 
 * @param string $slug The slug to search for
 * @param mysqli $conn Database connection
 * @return array|null Press data or null if not found
 */
function getPressbySlug($slug, $conn) {
    $query = 'SELECT id FROM press WHERE slug = ? LIMIT 1';
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        return null;
    }
    
    $stmt->bind_param('s', $slug);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    
    return $row;
}
?>


