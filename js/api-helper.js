/**
 * API Helper - Fetch with Language Support & Caching
 * Automatically adds language parameter to all API calls
 * Uses APICacheManager for deduplication and caching
 */

/**
 * Fetch API endpoint with language parameter and caching
 * @param {string} endpoint - API endpoint path (e.g., '/api/get_events.php')
 * @param {object} params - Additional query parameters
 * @param {number} ttl - Cache TTL in milliseconds (default: 5 minutes)
 * @returns {Promise} API response
 */
async function fetchWithLanguage(endpoint, params = {}, ttl = 5 * 60 * 1000) {
    try {
        // Get current language
        const lang = LanguageManager.getLanguage();
        
        // Build URL
        const url = new URL(endpoint, window.location.origin);
        
        // Add language parameter
        url.searchParams.append('lang', lang);
        
        // Add other parameters
        Object.entries(params).forEach(([key, value]) => {
            if (value !== null && value !== undefined) {
                url.searchParams.append(key, value);
            }
        });
        
        const urlString = url.toString();
        
        // Use cache manager if available
        if (window.apiCacheManager) {
            const data = await window.apiCacheManager.fetch(urlString, {}, ttl);
            
            // Log if fallback occurred
            if (data.language && data.language !== lang) {
                console.warn(`Requested ${lang}, got ${data.language} (fallback)`);
            }
            
            return data;
        }
        
        // Fallback if cache manager not loaded
        const response = await fetch(urlString);
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const data = await response.json();
        
        // Log if fallback occurred
        if (data.language && data.language !== lang) {
            console.warn(`Requested ${lang}, got ${data.language} (fallback)`);
        }
        
        return data;
    } catch (error) {
        console.error('API Error:', error);
        return {
            success: false,
            data: [],
            error: error.message
        };
    }
}

/**
 * Escape HTML to prevent XSS
 * @param {string} text - Text to escape
 * @returns {string} Escaped text
 */
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

/**
 * Get API base URL
 * @returns {string} API base URL
 */
function getApiBaseUrl() {
    return window.location.origin + '/api/';
}
