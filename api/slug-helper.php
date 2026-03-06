<?php
/**
 * LAKUM Artspace - Slug Helper Utility
 * Handles slug generation, validation, and conversion
 */

class SlugHelper {
    
    /**
     * Generate URL-friendly slug from text
     * Supports special characters, emojis, and international characters
     * 
     * @param string $text Input text
     * @return string URL-friendly slug
     */
    public static function generate($text) {
        if (empty($text)) {
            return 'untitled';
        }
        
        // Convert to lowercase
        $slug = strtolower($text);
        
        // Remove emojis (Unicode range for emojis)
        $slug = preg_replace('/[\x{1F300}-\x{1F9FF}]/u', '', $slug);
        $slug = preg_replace('/[\x{1F600}-\x{1F64F}]/u', '', $slug); // Emoticons
        $slug = preg_replace('/[\x{1F900}-\x{1F9FF}]/u', '', $slug); // Supplemental Symbols
        $slug = preg_replace('/[\x{2600}-\x{27BF}]/u', '', $slug);   // Miscellaneous Symbols
        $slug = preg_replace('/[\x{2300}-\x{23FF}]/u', '', $slug);   // Miscellaneous Technical
        
        // Remove special characters (keep only alphanumeric, hyphens, spaces, underscores)
        $slug = preg_replace('/[^\w\s\-]/u', '', $slug);
        
        // Convert underscores to hyphens
        $slug = str_replace('_', '-', $slug);
        
        // Convert spaces to hyphens
        $slug = preg_replace('/\s+/', '-', $slug);
        
        // Remove consecutive hyphens
        $slug = preg_replace('/-+/', '-', $slug);
        
        // Trim hyphens from start and end
        $slug = trim($slug, '-');
        
        // Ensure not empty
        return !empty($slug) ? $slug : 'untitled';
    }
    
    /**
     * Validate slug format
     * 
     * @param string $slug Slug to validate
     * @return bool True if valid
     */
    public static function validate($slug) {
        // Slug should only contain lowercase letters, numbers, and hyphens
        // Should not start or end with hyphen
        return preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug) === 1;
    }
    
    /**
     * Sanitize slug (ensure it's valid)
     * 
     * @param string $slug Slug to sanitize
     * @return string Sanitized slug
     */
    public static function sanitize($slug) {
        // If not valid, regenerate from slug
        if (!self::validate($slug)) {
            return self::generate($slug);
        }
        return $slug;
    }
    
    /**
     * Convert old title-based URL to slug
     * Used for backward compatibility
     * 
     * @param string $title Old title parameter
     * @return string Slug
     */
    public static function fromTitle($title) {
        return self::generate($title);
    }
    
    /**
     * Get slug from various sources (slug, title, id)
     * Used in detail pages for backward compatibility
     * 
     * @param array $params URL parameters ($_GET)
     * @return string|null Slug or null if not found
     */
    public static function extract($params) {
        // Priority: slug > title > id
        if (!empty($params['slug'])) {
            return self::sanitize($params['slug']);
        }
        
        if (!empty($params['title'])) {
            return self::fromTitle($params['title']);
        }
        
        if (!empty($params['id'])) {
            return null; // ID-based lookup handled separately
        }
        
        return null;
    }
    
    /**
     * Generate unique slug with counter if duplicate
     * 
     * @param string $text Base text for slug
     * @param string $table Database table name
     * @param string $column Column name (default: 'slug')
     * @param int $excludeId ID to exclude from duplicate check (for updates)
     * @return string Unique slug
     */
    public static function generateUnique($text, $table, $column = 'slug', $excludeId = null) {
        global $db;
        
        $baseSlug = self::generate($text);
        $slug = $baseSlug;
        $counter = 1;
        
        // Check if slug exists
        while (self::slugExists($slug, $table, $column, $excludeId)) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }
    
    /**
     * Check if slug already exists in database
     * 
     * @param string $slug Slug to check
     * @param string $table Table name
     * @param string $column Column name
     * @param int $excludeId ID to exclude (for updates)
     * @return bool True if exists
     */
    public static function slugExists($slug, $table, $column = 'slug', $excludeId = null) {
        global $db;
        
        if (!$db || !$db->isConnected()) {
            return false;
        }
        
        $query = "SELECT id FROM $table WHERE $column = ?";
        $params = [$slug];
        $types = 's';
        
        if ($excludeId !== null) {
            $query .= " AND id != ?";
            $params[] = $excludeId;
            $types .= 'i';
        }
        
        $stmt = $db->prepare($query);
        if (!$stmt) {
            return false;
        }
        
        call_user_func_array([$stmt, 'bind_param'], array_merge([$types], array_map(function(&$v) { return $v; }, $params)));
        $stmt->execute();
        $result = $stmt->get_result();
        $exists = $result->num_rows > 0;
        $stmt->close();
        
        return $exists;
    }
    
    /**
     * Test slug generation with various inputs
     * 
     * @return array Test results
     */
    public static function test() {
        $testCases = [
            'My Blog Post' => 'my-blog-post',
            'Dior Exhibition' => 'dior-exhibition',
            'Press Release #1' => 'press-release-1',
            'Art & Culture 🎨' => 'art-culture',
            'Event (2025)' => 'event-2025',
            '50% Off Sale!' => '50-off-sale',
            'Dior\'s Exhibition' => 'diors-exhibition',
            'Multiple   Spaces' => 'multiple-spaces',
            '---Leading Hyphens---' => 'leading-hyphens',
            'UPPERCASE TEXT' => 'uppercase-text',
            'مرحبا بك' => '', // Arabic (will be empty after removing non-ASCII)
            'Hello_World' => 'hello-world',
            '123 Numbers' => '123-numbers',
            'Special@#$%Chars' => 'specialchars',
        ];
        
        $results = [];
        foreach ($testCases as $input => $expected) {
            $generated = self::generate($input);
            $results[] = [
                'input' => $input,
                'expected' => $expected,
                'generated' => $generated,
                'match' => $generated === $expected,
                'valid' => self::validate($generated)
            ];
        }
        
        return $results;
    }
}

// Example usage:
/*
// Generate slug
$slug = SlugHelper::generate('My Blog Post');
// Output: 'my-blog-post'

// Validate slug
$isValid = SlugHelper::validate('my-blog-post');
// Output: true

// Generate unique slug
$uniqueSlug = SlugHelper::generateUnique('My Blog Post', 'blogs');
// Output: 'my-blog-post' or 'my-blog-post-1' if duplicate

// Extract from URL parameters
$slug = SlugHelper::extract($_GET);
// Returns slug from ?slug=... or converts ?title=... to slug

// Test
$tests = SlugHelper::test();
// Returns array of test results
*/
?>
