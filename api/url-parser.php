<?php
/**
 * LAKUM Artspace - URL Parser Helper
 * Parses clean URLs without query parameter names
 * 
 * Supports formats:
 * - event.php?dior-exhibition&lang=en
 * - blogPageDetails.php?my-blog-post&lang=en
 * - pressPageDetails.php?press-release-1&lang=en
 */

class URLParser {
    
    /**
     * Parse URL parameters from query string
     * Handles both traditional and clean URL formats
     * 
     * @return array ['slug' => string, 'lang' => string]
     */
    public static function parse() {
        $slug = null;
        $lang = 'en';
        
        // Check for traditional query string: ?slug=...&lang=...
        if (isset($_GET['slug'])) {
            $slug = $_GET['slug'];
            $lang = $_GET['lang'] ?? 'en';
            return ['slug' => $slug, 'lang' => $lang];
        }
        
        // Check for clean query string: ?dior-exhibition&lang=en
        $queryString = $_SERVER['QUERY_STRING'] ?? '';
        
        if (empty($queryString)) {
            return ['slug' => null, 'lang' => 'en'];
        }
        
        $parts = explode('&', $queryString);
        
        foreach ($parts as $part) {
            $part = trim($part);
            
            if (empty($part)) {
                continue;
            }
            
            // Check if this part has an equals sign
            if (strpos($part, '=') === false) {
                // This is the slug (no = sign)
                $slug = urldecode($part);
            } elseif (strpos($part, 'lang=') === 0) {
                // This is the language parameter
                $lang = substr($part, 5);
            } elseif (strpos($part, 'id=') === 0) {
                // Legacy ID parameter (for backward compatibility)
                // Don't set slug, let caller handle ID
            }
        }
        
        return ['slug' => $slug, 'lang' => $lang];
    }
    
    /**
     * Parse URL for events
     * 
     * @return array ['slug' => string, 'lang' => string]
     */
    public static function parseEvent() {
        return self::parse();
    }
    
    /**
     * Parse URL for blogs
     * 
     * @return array ['slug' => string, 'lang' => string]
     */
    public static function parseBlog() {
        return self::parse();
    }
    
    /**
     * Parse URL for press
     * 
     * @return array ['slug' => string, 'lang' => string]
     */
    public static function parsePress() {
        return self::parse();
    }
    
    /**
     * Generate clean event URL
     * 
     * @param string $slug Event slug
     * @param string $lang Language code (en/ar)
     * @return string Clean URL
     */
    public static function generateEventUrl($slug, $lang = 'en') {
        return "event.php?{$slug}&lang={$lang}";
    }
    
    /**
     * Generate clean blog URL
     * 
     * @param string $slug Blog slug
     * @param string $lang Language code (en/ar)
     * @return string Clean URL
     */
    public static function generateBlogUrl($slug, $lang = 'en') {
        return "blogPageDetails.php?{$slug}&lang={$lang}";
    }
    
    /**
     * Generate clean press URL
     * 
     * @param string $slug Press slug
     * @param string $lang Language code (en/ar)
     * @return string Clean URL
     */
    public static function generatePressUrl($slug, $lang = 'en') {
        return "pressPageDetails.php?{$slug}&lang={$lang}";
    }
    
    /**
     * Get language from URL or default
     * 
     * @return string Language code (en/ar)
     */
    public static function getLanguage() {
        $data = self::parse();
        return $data['lang'] ?? 'en';
    }
    
    /**
     * Get slug from URL
     * 
     * @return string|null Slug or null
     */
    public static function getSlug() {
        $data = self::parse();
        return $data['slug'] ?? null;
    }
    
    /**
     * Check if URL has valid slug
     * 
     * @return bool True if slug exists
     */
    public static function hasSlug() {
        $slug = self::getSlug();
        return !empty($slug);
    }
    
    /**
     * Test URL parsing
     * 
     * @return array Test results
     */
    public static function test() {
        $tests = [
            'dior-exhibition&lang=en' => ['slug' => 'dior-exhibition', 'lang' => 'en'],
            'my-blog-post&lang=ar' => ['slug' => 'my-blog-post', 'lang' => 'ar'],
            'press-release-1&lang=en' => ['slug' => 'press-release-1', 'lang' => 'en'],
            'slug=dior-exhibition&lang=en' => ['slug' => 'dior-exhibition', 'lang' => 'en'],
            'art-culture&lang=en' => ['slug' => 'art-culture', 'lang' => 'en'],
        ];
        
        $results = [];
        foreach ($tests as $queryString => $expected) {
            // Simulate query string
            $_SERVER['QUERY_STRING'] = $queryString;
            $_GET = [];
            
            $parsed = self::parse();
            $results[] = [
                'query_string' => $queryString,
                'expected' => $expected,
                'parsed' => $parsed,
                'match' => $parsed === $expected
            ];
        }
        
        return $results;
    }
}

// Example usage:
/*
// Parse URL
$urlData = URLParser::parse();
$slug = $urlData['slug'];
$lang = $urlData['lang'];

// Or use shortcuts
$slug = URLParser::getSlug();
$lang = URLParser::getLanguage();

// Generate URLs
$eventUrl = URLParser::generateEventUrl('dior-exhibition', 'en');
// Output: event.php?dior-exhibition&lang=en

$blogUrl = URLParser::generateBlogUrl('my-blog-post', 'en');
// Output: blogPageDetails.php?my-blog-post&lang=en

// Test
$tests = URLParser::test();
*/
?>
