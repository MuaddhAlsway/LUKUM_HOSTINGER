/**
 * Deferred API Loader
 * Loads API data AFTER first paint to avoid blocking LCP
 * 
 * Usage:
 * window.deferredAPILoader.loadAfterPaint(url, callback, timeout);
 */

(function() {
    'use strict';

    class DeferredAPILoader {
        constructor() {
            this.cache = new Map();
            this.pendingRequests = new Map();
            this.requestTimeout = 5000; // 5 second timeout
        }

        /**
         * Load API data after first paint
         * @param {string} url - API endpoint
         * @param {function} callback - Callback when data loads (data, error)
         * @param {number} timeout - Max wait time (ms) before loading
         */
        loadAfterPaint(url, callback, timeout = 2000) {
            if (!url || typeof callback !== 'function') {
                console.error('DeferredAPILoader: Invalid arguments');
                return;
            }

            // Check cache first
            if (this.cache.has(url)) {
                const cachedData = this.cache.get(url);
                callback(cachedData, null);
                return;
            }

            // Check if already pending
            if (this.pendingRequests.has(url)) {
                this.pendingRequests.get(url).push(callback);
                return;
            }

            // Mark as pending
            this.pendingRequests.set(url, [callback]);

            // Defer to after first paint
            this._scheduleLoad(url, timeout);
        }

        /**
         * Schedule load after first paint
         */
        _scheduleLoad(url, timeout) {
            if ('requestIdleCallback' in window) {
                // Use requestIdleCallback if available (best for performance)
                requestIdleCallback(() => {
                    this._fetchAndCache(url);
                }, { timeout });
            } else if ('requestAnimationFrame' in window) {
                // Fallback to requestAnimationFrame
                requestAnimationFrame(() => {
                    setTimeout(() => {
                        this._fetchAndCache(url);
                    }, timeout);
                });
            } else {
                // Fallback to setTimeout
                setTimeout(() => {
                    this._fetchAndCache(url);
                }, timeout);
            }
        }

        /**
         * Fetch and cache data
         */
        async _fetchAndCache(url) {
            try {
                // Add timestamp to bypass cache
                const separator = url.includes('?') ? '&' : '?';
                const urlWithTimestamp = `${url}${separator}t=${Date.now()}`;

                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), this.requestTimeout);

                const response = await fetch(urlWithTimestamp, {
                    signal: controller.signal,
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                clearTimeout(timeoutId);

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }

                const data = await response.json();

                // Cache result
                this.cache.set(url, data);

                // Call all pending callbacks
                const callbacks = this.pendingRequests.get(url) || [];
                callbacks.forEach(cb => {
                    try {
                        cb(data, null);
                    } catch (e) {
                        console.error('Error in callback:', e);
                    }
                });

                // Clear pending
                this.pendingRequests.delete(url);

                // Log success
                console.log('✓ API loaded:', url);
            } catch (error) {
                console.error('✗ Error loading API:', url, error);

                // Call callbacks with error
                const callbacks = this.pendingRequests.get(url) || [];
                callbacks.forEach(cb => {
                    try {
                        cb(null, error);
                    } catch (e) {
                        console.error('Error in error callback:', e);
                    }
                });

                // Clear pending
                this.pendingRequests.delete(url);
            }
        }

        /**
         * Clear cache
         */
        clearCache() {
            this.cache.clear();
        }

        /**
         * Get cached data
         */
        getCached(url) {
            return this.cache.get(url) || null;
        }

        /**
         * Prefetch data (load immediately, don't wait for idle)
         */
        prefetch(url) {
            if (!this.cache.has(url)) {
                this._fetchAndCache(url);
            }
        }
    }

    // Export as global singleton
    window.deferredAPILoader = new DeferredAPILoader();

    // Log initialization
    console.log('✓ DeferredAPILoader initialized');
})();
