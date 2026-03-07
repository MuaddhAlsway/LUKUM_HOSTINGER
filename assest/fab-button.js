/**
 * FAB Button - Floating Action Button
 * Handles menu open/close and interactions
 * CRITICAL: Works on all pages with proper initialization
 */

(function() {
    'use strict';

    function initFabButton() {
        const fabButton = document.getElementById('fabButton');
        const fabTrigger = document.getElementById('fabTrigger');
        const fabMenu = document.getElementById('fabMenu');

        if (!fabButton || !fabTrigger || !fabMenu) {
            console.warn('FAB button elements not found:', {
                fabButton: !!fabButton,
                fabTrigger: !!fabTrigger,
                fabMenu: !!fabMenu
            });
            // Retry after a short delay
            setTimeout(initFabButton, 500);
            return;
        }

        console.log('FAB button initialized successfully');

        // Toggle menu on button click
        fabTrigger.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const isExpanded = fabTrigger.getAttribute('aria-expanded') === 'true';
            fabTrigger.setAttribute('aria-expanded', !isExpanded);
            fabButton.classList.toggle('fab-button--active');
            console.log('FAB button toggled:', !isExpanded);
        });

        // Close menu when clicking outside
        document.addEventListener('click', function(e) {
            if (!fabButton.contains(e.target)) {
                fabTrigger.setAttribute('aria-expanded', 'false');
                fabButton.classList.remove('fab-button--active');
            }
        });

        // Close menu when clicking on a menu item
        const fabItems = fabMenu.querySelectorAll('.fab-button__item');
        fabItems.forEach(item => {
            item.addEventListener('click', function() {
                fabTrigger.setAttribute('aria-expanded', 'false');
                fabButton.classList.remove('fab-button--active');
            });
        });

        // Close menu on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && fabTrigger.getAttribute('aria-expanded') === 'true') {
                fabTrigger.setAttribute('aria-expanded', 'false');
                fabButton.classList.remove('fab-button--active');
            }
        });
    }

    // Wait for DOM to be ready - multiple strategies
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initFabButton);
    } else {
        // DOM already loaded
        initFabButton();
    }
    
    // Also try on window load as fallback
    window.addEventListener('load', initFabButton);
    
    // Retry after a delay to ensure all elements are present
    setTimeout(initFabButton, 100);
})();
