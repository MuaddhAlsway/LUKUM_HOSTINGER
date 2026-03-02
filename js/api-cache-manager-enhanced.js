/**
 * Enhanced API Cache Manager
 * Production-ready request deduplication and response caching
 * 
 * Features:
 * - Request deduplication (prevents duplicate in-flight requests)
 * - Response caching with TTL
 * - Cache statistics and monitoring
 * - Automatic cache invalidation
 * - Language-aware caching
 */

class APICacheManager {
    constructor(options = {}) {
        this.cache = new Map();
        this.inFlight = new Map();
        this.stats = {
            hits: 0,
            misses: 0,
            requests: 0
        };
        this.ttl = options.ttl || 5 * 60 * 1000; // 5 minutes default
        this.maxCacheSize = options.maxCacheSize || 50; // Max cache entries
        this.debug = options.debug || false;
    }

    /**
     * Generate cache key from URL and options
     */
    getCacheKey(url, options = {}) {
        const key = url + JSON.stringify(options);
        return btoa(key); // Base64 encode for safe key
    }

    /**
     * Log debug messages
     */
    log(message, data = null) {
        if (this.debug) {
            console.log(`[APICacheManager] ${message}`, data || '');
        }
    }

    /**
     * Fetch with deduplication and caching
     * @param {string} url - API endpoint URL
     * @param {object} options - Fetch options
     * @param {number} ttl - Cache TTL in milliseconds
     * @returns {Promise} API response
     */
    async fetch(url, options = {}, ttl = this.ttl) {
        const cacheKey = this.getCacheKey(url, options);
        this.stats.requests++;

        // 1. Check if response is cached and valid
        if (this.cache.has(cacheKey)) {
            const cached = this.cache.get(cacheKey);
            if (Date.now() - cached.timestamp < ttl) {
                this.stats.hits++;
                this.log(`Cache HIT (${this.stats.hits}/${this.stats.requests})`, url);
                return Promise.resolve(cached.data);
            } else {
                // Cache expired
                this.cache.delete(cacheKey);
            }
        }

        // 2. Check if request is already in-flight
        if (this.inFlight.has(cacheKey)) {
            this.log(`Deduplication: Waiting for in-flight request`, url);
            return this.inFlight.get(cacheKey);
        }

        // 3. Make new request
        this.stats.misses++;
        this.log(`Cache MISS (${this.stats.misses}/${this.stats.requests})`, url);
        
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
                
                // Enforce max cache size
                if (this.cache.size > this.maxCacheSize) {
                    const firstKey = this.cache.keys().next().value;
                    this.cache.delete(firstKey);
                }
                
                // Remove from in-flight
                this.inFlight.delete(cacheKey);
                
                return data;
            })
            .catch(error => {
                // Remove from in-flight on error
                this.inFlight.delete(cacheKey);
                this.log(`Fetch error: ${error.message}`);
                throw error;
            });

        // Store in-flight promise
        this.inFlight.set(cacheKey, fetchPromise);
        return fetchPromise;
    }

    /**
     * Invalidate cache by pattern
     */
    invalidate(pattern) {
        let count = 0;
        for (const key of this.cache.keys()) {
            if (key.includes(pattern)) {
                this.cache.delete(key);
                count++;
            }
        }
        this.log(`Invalidated ${count} cache entries matching: ${pattern}`);
    }

    /**
     * Clear all cache
     */
    clear() {
        const size = this.cache.size;
        this.cache.clear();
        this.inFlight.clear();
        this.log(`Cleared ${size} cache entries`);
    }

    /**
     * Get cache statistics
     */
    getStats() {
        const hitRate = this.stats.requests > 0 
            ? ((this.stats.hits / this.stats.requests) * 100).toFixed(2)
            : 0;
        
        return {
            ...this.stats,
            hitRate: `${hitRate}%`,
            cacheSize: this.cache.size,
            inFlightRequests: this.inFlight.size
        };
    }

    /**
     * Reset statistics
     */
    resetStats() {
        this.stats = { hits: 0, misses: 0, requests: 0 };
    }
}

// Global instance
window.apiCache = new APICacheManager({ debug: false });
