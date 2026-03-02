/**
 * API Cache Manager
 * Prevents duplicate API calls and caches responses
 * 
 * Features:
 * - Request deduplication (prevents duplicate in-flight requests)
 * - Response caching (stores responses in memory)
 * - Cache invalidation (clear cache when needed)
 * - TTL support (auto-expire cache after time)
 */

class APICacheManager {
    constructor() {
        this.cache = new Map();
        this.inFlight = new Map();
        this.ttl = 5 * 60 * 1000; // 5 minutes default TTL
    }

    /**
     * Generate cache key from URL
     */
    getCacheKey(url) {
        return url.toString();
    }

    /**
     * Fetch with deduplication and caching
     * @param {string} url - API endpoint URL
     * @param {object} options - Fetch options
     * @param {number} ttl - Cache TTL in milliseconds (default: 5 minutes)
     * @returns {Promise} API response
     */
    async fetch(url, options = {}, ttl = this.ttl) {
        const cacheKey = this.getCacheKey(url);

        // 1. Check if response is cached
        if (this.cache.has(cacheKey)) {
            const cached = this.cache.get(cacheKey);
            if (Date.now() - cached.timestamp < ttl) {
                console.log(`[Cache HIT] ${url}`);
                return cached.data;
            } else {
                // Cache expired
                this.cache.delete(cacheKey);
            }
        }

        // 2. Check if request is already in-flight
        if (this.inFlight.has(cacheKey)) {
            console.log(`[Dedup] Waiting for in-flight request: ${url}`);
            return this.inFlight.get(cacheKey);
        }

        // 3. Make new request
        console.log(`[Fetch] ${url}`);
        const fetchPromise = fetch(url, options)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                // Cache the response
                this.cache.set(cacheKey, {
                    data: data,
                    timestamp: Date.now()
                });
                
                // Remove from in-flight
                this.inFlight.delete(cacheKey);
                
                return data;
            })
            .catch(error => {
                // Remove from in-flight on error
                this.inFlight.delete(cacheKey);
                throw error;
            });

        // 4. Add to in-flight map
        this.inFlight.set(cacheKey, fetchPromise);

        return fetchPromise;
    }

    /**
     * Clear specific cache entry
     */
    clearCache(url) {
        const cacheKey = this.getCacheKey(url);
        this.cache.delete(cacheKey);
        console.log(`[Cache Clear] ${url}`);
    }

    /**
     * Clear all cache
     */
    clearAllCache() {
        this.cache.clear();
        console.log('[Cache Clear] All cache cleared');
    }

    /**
     * Get cache stats
     */
    getStats() {
        return {
            cached: this.cache.size,
            inFlight: this.inFlight.size,
            totalSize: this.cache.size + this.inFlight.size
        };
    }
}

// Create global instance
window.apiCacheManager = new APICacheManager();
