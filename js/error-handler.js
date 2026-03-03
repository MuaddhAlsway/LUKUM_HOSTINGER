/**
 * ERROR HANDLER - Catch and prevent console errors
 * Defensive programming for missing resources
 */

(function() {
    'use strict';

    // Prevent 404 errors from breaking functionality
    window.addEventListener('error', (event) => {
        if (event.filename && event.filename.includes('img-4-')) {
            console.warn('⚠️ Image variant not found, using fallback:', event.filename);
            event.preventDefault();
        }
    }, true);

    // Prevent missing script errors
    window.addEventListener('error', (event) => {
        if (event.target.tagName === 'SCRIPT') {
            console.warn('⚠️ Script failed to load:', event.target.src);
            event.preventDefault();
        }
    }, true);

    // Safe function calls
    window.safeCall = function(fn, context = window) {
        try {
            if (typeof fn === 'function') {
                return fn.call(context);
            }
        } catch (e) {
            console.warn('⚠️ Function call failed:', e.message);
        }
    };

    // Safe property access
    window.safeGet = function(obj, path, defaultValue = null) {
        try {
            return path.split('.').reduce((acc, part) => acc?.[part], obj) ?? defaultValue;
        } catch (e) {
            return defaultValue;
        }
    };

    // Suppress non-critical errors
    window.addEventListener('unhandledrejection', (event) => {
        if (event.reason?.message?.includes('404')) {
            event.preventDefault();
            console.warn('⚠️ Resource not found (non-critical)');
        }
    });

    console.log('✓ Error handler initialized');
})();
