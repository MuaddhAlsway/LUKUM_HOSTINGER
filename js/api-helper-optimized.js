/**
 * Optimized API Helper
 * Integrates with APICacheManager for automatic caching and deduplication
 */

class APIHelper {
    constructor(baseUrl = '/api', cacheManager = null) {
        this.baseUrl = baseUrl;
        this.cache = cacheManager || window.apiCache;
        this.language = this.getLanguage();
    }

    /**
     * Get current language
     */
    getLanguage() {
        return localStorage.getItem('language') || 'en';
    }

    /**
     * Build API URL with language parameter
     */
    buildUrl(endpoint, params = {}) {
        const url = new URL(this.baseUrl + endpoint, window.location.origin);
        
        // Add language parameter
        url.searchParams.set('lang', this.language);
        
        // Add other parameters
        Object.entries(params).forEach(([key, value]) => {
            if (value !== null && value !== undefined) {
                url.searchParams.set(key, value);
            }
        });
        
        return url.toString();
    }

    /**
     * GET request with caching
     */
    async get(endpoint, params = {}, ttl = 5 * 60 * 1000) {
        const url = this.buildUrl(endpoint, params);
        
        try {
            return await this.cache.fetch(url, { method: 'GET' }, ttl);
        } catch (error) {
            console.error(`API Error (GET ${endpoint}):`, error);
            throw error;
        }
    }

    /**
     * POST request (no caching)
     */
    async post(endpoint, data = {}) {
        const url = this.buildUrl(endpoint);
        
        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            });
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            return await response.json();
        } catch (error) {
            console.error(`API Error (POST ${endpoint}):`, error);
            throw error;
        }
    }

    /**
     * PUT request (no caching)
     */
    async put(endpoint, data = {}) {
        const url = this.buildUrl(endpoint);
        
        try {
            const response = await fetch(url, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            });
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            return await response.json();
        } catch (error) {
            console.error(`API Error (PUT ${endpoint}):`, error);
            throw error;
        }
    }

    /**
     * DELETE request (no caching)
     */
    async delete(endpoint, params = {}) {
        const url = this.buildUrl(endpoint, params);
        
        try {
            const response = await fetch(url, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                }
            });
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            return await response.json();
        } catch (error) {
            console.error(`API Error (DELETE ${endpoint}):`, error);
            throw error;
        }
    }

    /**
     * Invalidate cache for endpoint
     */
    invalidateCache(endpoint) {
        this.cache.invalidate(endpoint);
    }

    /**
     * Get cache statistics
     */
    getCacheStats() {
        return this.cache.getStats();
    }

    /**
     * Common API methods with optimized caching
     */

    async getEvents(params = {}) {
        return this.get('/get_events.php', params, 10 * 60 * 1000); // 10 min cache
    }

    async getEventDetails(eventId) {
        return this.get('/get_event_details.php', { id: eventId }, 15 * 60 * 1000); // 15 min cache
    }

    async getBlogs(params = {}) {
        return this.get('/get_blogs.php', params, 10 * 60 * 1000); // 10 min cache
    }

    async getBlogDetails(blogId) {
        return this.get('/get_blog_details.php', { id: blogId }, 15 * 60 * 1000); // 15 min cache
    }

    async getPress(params = {}) {
        return this.get('/get_press.php', params, 10 * 60 * 1000); // 10 min cache
    }

    async getPricing(params = {}) {
        return this.get('/get_pricing.php', params, 30 * 60 * 1000); // 30 min cache (pricing changes less often)
    }

    async getSettings() {
        return this.get('/get_settings.php', {}, 60 * 60 * 1000); // 1 hour cache
    }

    async getTranslations() {
        return this.get('/get_translations.php', {}, 60 * 60 * 1000); // 1 hour cache
    }

    async getLegalPage(page) {
        return this.get('/get_legal_page.php', { page }, 60 * 60 * 1000); // 1 hour cache
    }

    /**
     * Admin/mutation methods (no caching)
     */

    async addEvent(data) {
        const result = await this.post('/add_event.php', data);
        this.invalidateCache('/get_events');
        return result;
    }

    async editEvent(eventId, data) {
        const result = await this.put('/edit_event.php', { id: eventId, ...data });
        this.invalidateCache('/get_event');
        this.invalidateCache('/get_events');
        return result;
    }

    async deleteEvent(eventId) {
        const result = await this.delete('/delete_event.php', { id: eventId });
        this.invalidateCache('/get_events');
        return result;
    }

    async addBlog(data) {
        const result = await this.post('/add_blog.php', data);
        this.invalidateCache('/get_blogs');
        return result;
    }

    async editBlog(blogId, data) {
        const result = await this.put('/edit_blog.php', { id: blogId, ...data });
        this.invalidateCache('/get_blog');
        this.invalidateCache('/get_blogs');
        return result;
    }

    async deleteBlog(blogId) {
        const result = await this.delete('/delete_blog.php', { id: blogId });
        this.invalidateCache('/get_blogs');
        return result;
    }
}

// Global instance
window.api = new APIHelper('/api');
