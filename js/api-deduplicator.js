/**
 * AGGRESSIVE API DEDUPLICATOR
 * Prevents duplicate API calls across entire page load
 * 
 * CRITICAL FIXES:
 * - Single API call per endpoint per page load
 * - Cross-page cache using sessionStorage
 * - Automatic deduplication of in-flight requests
 * - Cache invalidation on language change
 */

class AggressiveAPIDeduplicator {
    constructor() {
        this.inFlight = new Map();
        this.cache = new Map();
        this.sessionCache = this.initSessionCache();
    }

    /**
     * Initialize session cache from sessionStorage
     */
    initSessionCache() {
        try {
            const cached = sessionStorage.getItem('lakum_api_cache');
            return cached ? new Map(JSON.parse(cached)) : new Map();
        } catch (e) {
            return new Map();
        }
    }

    /**
     * Save session cache to sessionStorage
     */
    saveSessionCache() {
        try {
            sessionStorage.setItem('lakum_api_cache', JSON.stringify(Array.from(this.sessionCache)));
        } catch (e) {
            console.warn('Failed to save session cache:', e);
        }
    }

    /**
     * Fetch with aggressive deduplication
     * 
     * @param {string} url - API endpoint
     * @param {object} options - Fetch options
     * @returns {Promise} API response
     */
    async fetch(url, options = {}) {
        const cacheKey = this.getCacheKey(url);

        // 1. Check session cache (survives page navigation)
        if (this.sessionCache.has(cacheKey)) {
            const cached = this.sessionCache.get(cacheKey);
            if (Date.now() - cached.timestamp < 5 * 60 * 1000) { // 5 min TTL
                console.log(`[Session Cache HIT] ${url}`);
                return cached.data;
            } else {
                this.sessionCache.delete(cacheKey);
            }
        }

        // 2. Check memory cache (current page load)
        if (this.cache.has(cacheKey)) {
            const cached = this.cache.get(cacheKey);
            if (Date.now() - cached.timestamp < 5 * 60 * 1000) {
                console.log(`[Memory Cache HIT] ${url}`);
                return cached.data;
            } else {
                this.cache.delete(cacheKey);
            }
        }

        // 3. Check if request is already in-flight
        if (this.inFlight.has(cacheKey)) {
            console.log(`[Dedup] Waiting for in-flight request: ${url}`);
            return this.inFlight.get(cacheKey);
        }

        // 4. Make new request
        console.log(`[API Call] ${url}`);
        const promise = this.makeRequest(url, options)
            .then(data => {
                // Cache response
                this.cache.set(cacheKey, { data, timestamp: Date.now() });
                this.sessionCache.set(cacheKey, { data, timestamp: Date.now() });
                this.saveSessionCache();
                return data;
            })
            .finally(() => {
                // Remove from in-flight
                this.inFlight.delete(cacheKey);
            });

        // Store in-flight promise
        this.inFlight.set(cacheKey, promise);

        return promise;
    }

    /**
     * Make actual fetch request
     */
    async makeRequest(url, options) {
        const response = await fetch(url, {
            ...options,
            headers: {
                'Content-Type': 'application/json',
                ...options.headers
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }

        return response.json();
    }

    /**
     * Generate cache key
     */
    getCacheKey(url) {
        return url.toString();
    }

    /**
     * Clear cache on language change
     */
    clearCache() {
        this.cache.clear();
        this.sessionCache.clear();
        sessionStorage.removeItem('lakum_api_cache');
        console.log('[Cache Cleared]');
    }

    /**
     * Get cache stats
     */
    getStats() {
        return {
            memoryCache: this.cache.size,
            sessionCache: this.sessionCache.size,
            inFlight: this.inFlight.size
        };
    }
}

// Create global instance
window.apiDeduplicator = new AggressiveAPIDeduplicator();

/**
 * USAGE:
 * 
 * // Fetch with automatic deduplication
 * const events = await window.apiDeduplicator.fetch('/api/get_events.php?lang=en');
 * 
 * // Clear cache on language change
 * window.apiDeduplicator.clearCache();
 * 
 * // Get stats
 * console.log(window.apiDeduplicator.getStats());
 */
