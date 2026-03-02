/**
 * Settings Loader - Dynamically loads booking and shop links from settings
 * This script should be loaded early in the page to update links before user interaction
 * Uses APICacheManager for deduplication and caching
 */

(function() {
    'use strict';

    // Load settings and update links
    async function loadAndApplySettings() {
        try {
            let result;
            
            // Use cache manager if available
            if (window.apiCacheManager) {
                result = await window.apiCacheManager.fetch('./api/get_settings.php', {}, 10 * 60 * 1000); // 10 min TTL
            } else {
                const response = await fetch('./api/get_settings.php');
                result = await response.json();
            }

            if (result.success && result.data) {
                const { booking_link, shop_link } = result.data;

                // Update all booking links
                if (booking_link) {
                    document.querySelectorAll('[data-link-type="booking"]').forEach(el => {
                        if (el.tagName === 'A') {
                            el.href = booking_link;
                        }
                    });
                }

                // Update all shop links
                if (shop_link) {
                    document.querySelectorAll('[data-link-type="shop"]').forEach(el => {
                        if (el.tagName === 'A') {
                            el.href = shop_link;
                        }
                    });
                }
            }
        } catch (error) {
            console.warn('Could not load settings:', error);
            // Silently fail - use hardcoded links as fallback
        }
    }

    // Load settings when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', loadAndApplySettings);
    } else {
        loadAndApplySettings();
    }
})();
