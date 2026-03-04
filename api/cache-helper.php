<?php
/**
 * LAKUM Artspace - Cache Helper
 * Provides caching utilities for API endpoints
 */

class CacheHelper {
    private $cacheDir;
    private $enabled = true;
    
    public function __construct($cacheDir = null) {
        $this->cacheDir = $cacheDir ?: __DIR__ . '/cache/';
        
        // Create cache directory if it doesn't exist
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0755, true);
        }
    }
    
    /**
     * Get cached data
     */
    public function get($key, $ttl = 3600) {
        if (!$this->enabled) return null;
        
        $file = $this->getCacheFile($key);
        
        if (file_exists($file)) {
            $age = time() - filemtime($file);
            if ($age < $ttl) {
                return json_decode(file_get_contents($file), true);
            } else {
                unlink($file);
            }
        }
        
        return null;
    }
    
    /**
     * Set cached data
     */
    public function set($key, $data) {
        if (!$this->enabled) return false;
        
        $file = $this->getCacheFile($key);
        return file_put_contents($file, json_encode($data)) !== false;
    }
    
    /**
     * Invalidate cache by pattern
     */
    public function invalidate($pattern) {
        if (!$this->enabled) return;
        
        $files = glob($this->cacheDir . '*' . $pattern . '*.cache');
        foreach ($files as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }
    }
    
    /**
     * Clear all cache
     */
    public function clear() {
        if (!$this->enabled) return;
        
        $files = glob($this->cacheDir . '*.cache');
        foreach ($files as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }
    }
    
    /**
     * Get cache file path
     */
    private function getCacheFile($key) {
        return $this->cacheDir . md5($key) . '.cache';
    }
    
    /**
     * Set HTTP cache headers
     */
    public static function setHeaders($ttl = 3600) {
        header('Cache-Control: public, max-age=' . $ttl);
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $ttl) . ' GMT');
        header('Pragma: public');
    }
    
    /**
     * Set ETag header
     */
    public static function setETag($data) {
        $etag = md5(json_encode($data));
        header('ETag: "' . $etag . '"');
        
        // Check If-None-Match
        if (isset($_SERVER['HTTP_IF_NONE_MATCH'])) {
            if ($_SERVER['HTTP_IF_NONE_MATCH'] === '"' . $etag . '"') {
                http_response_code(304);
                exit;
            }
        }
    }
    
    /**
     * Set Last-Modified header
     */
    public static function setLastModified($timestamp) {
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $timestamp) . ' GMT');
        
        // Check If-Modified-Since
        if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
            $ifModifiedSince = strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']);
            if ($ifModifiedSince >= $timestamp) {
                http_response_code(304);
                exit;
            }
        }
    }
}

?>

