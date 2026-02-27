/**
 * Universal Form Reset Script for Admin Panel
 * Prevents form caching issues when navigating back to forms
 */

(function() {
    'use strict';

    // Don't reset forms on edit pages - they need to load data from API
    const isEditPage = window.location.pathname.includes('edit-');
    
    if (isEditPage) {
        console.log('Edit page detected - skipping form reset');
        return;
    }

    // Force reload on back/forward navigation to prevent cached form data
    window.addEventListener('pageshow', function(event) {
        // Check if page was loaded from browser cache
        if (event.persisted) {
            console.log('Page loaded from cache, reloading...');
            window.location.reload();
        }
    });

    // Additional check for browser back button
    if (window.performance && window.performance.navigation.type === 2) {
        console.log('Back button detected, reloading...');
        window.location.reload();
    }

    // Reset all forms on page load to prevent cached values
    window.addEventListener('DOMContentLoaded', function() {
        const forms = document.querySelectorAll('form');

        forms.forEach(function(form) {
            // Skip forms that explicitly don't want reset
            if (form.hasAttribute('data-no-reset')) {
                return;
            }

            // Get all form inputs
            const inputs = form.querySelectorAll('input, textarea, select');

            inputs.forEach(function(input) {
                // Skip certain input types
                if (input.type === 'hidden' ||
                    input.type === 'submit' ||
                    input.type === 'button' ||
                    input.type === 'reset' ||
                    input.type === 'file') {  // Don't clear file inputs - they're managed by event-form.js
                    return;
                }

                // Store the original server-provided value
                if (!input.hasAttribute('data-original-value')) {
                    input.setAttribute('data-original-value', input.value || '');
                }
            });
        });
    });

    // Prevent form autocomplete from interfering
    window.addEventListener('load', function() {
        const forms = document.querySelectorAll('form');
        forms.forEach(function(form) {
            if (!form.hasAttribute('autocomplete')) {
                form.setAttribute('autocomplete', 'off');
            }
        });
    });

    // Clear form data when leaving the page
    window.addEventListener('beforeunload', function() {
        const forms = document.querySelectorAll('form');
        forms.forEach(function(form) {
            if (!form.hasAttribute('data-no-reset')) {
                // Don't actually reset, just mark for reload
                sessionStorage.setItem('formNeedsReload', 'true');
            }
        });
    });

    // Check if form needs reload
    if (sessionStorage.getItem('formNeedsReload') === 'true') {
        sessionStorage.removeItem('formNeedsReload');
        // Form was marked for reload
        console.log('Form marked for reload');
    }

})();
