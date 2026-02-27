/**
 * Admin Panel Configuration
 * Centralized configuration for API endpoints and settings
 */

const ADMIN_CONFIG = {
    // API Configuration
    API_BASE: '../api/',
    
    // For production, change to:
    // API_BASE: '/api/',
    
    // Timeouts (in milliseconds)
    API_TIMEOUT: 10000,
    
    // Image Upload Settings
    MAX_IMAGE_SIZE: 5 * 1024 * 1024, // 5MB
    ALLOWED_IMAGE_TYPES: ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
    
    // Form Settings
    FORM_RESET_DELAY: 500,
    
    // Session Settings
    SESSION_TIMEOUT: 30 * 60 * 1000, // 30 minutes
    
    // Notification Settings
    NOTIFICATION_DURATION: 5000,
    
    // Pagination
    ITEMS_PER_PAGE: 20,
    
    // Feature Flags
    FEATURES: {
        ENABLE_BULK_DELETE: true,
        ENABLE_EXPORT: true,
        ENABLE_AUDIT_LOG: true,
        ENABLE_SEARCH: true
    }
};

/**
 * Get API endpoint URL
 */
function getApiUrl(endpoint) {
    return ADMIN_CONFIG.API_BASE + endpoint;
}

/**
 * Get image upload max size in MB
 */
function getMaxImageSizeMB() {
    return ADMIN_CONFIG.MAX_IMAGE_SIZE / (1024 * 1024);
}

/**
 * Check if file type is allowed
 */
function isAllowedImageType(mimeType) {
    return ADMIN_CONFIG.ALLOWED_IMAGE_TYPES.includes(mimeType);
}

/**
 * Check if feature is enabled
 */
function isFeatureEnabled(featureName) {
    return ADMIN_CONFIG.FEATURES[featureName] || false;
}

// Export for use in other scripts
window.ADMIN_CONFIG = ADMIN_CONFIG;
window.getApiUrl = getApiUrl;
window.getMaxImageSizeMB = getMaxImageSizeMB;
window.isAllowedImageType = isAllowedImageType;
window.isFeatureEnabled = isFeatureEnabled;
