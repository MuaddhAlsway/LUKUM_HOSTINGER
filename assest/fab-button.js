/**
 * FAB Button - Floating Action Button
 * Handles menu open/close and interactions
 */

(function() {
    'use strict';

    const fabButton = document.getElementById('fabButton');
    const fabTrigger = document.getElementById('fabTrigger');
    const fabMenu = document.getElementById('fabMenu');

    if (!fabButton || !fabTrigger || !fabMenu) {
        console.warn('FAB button elements not found');
        return;
    }

    // Toggle menu on button click
    fabTrigger.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const isExpanded = fabTrigger.getAttribute('aria-expanded') === 'true';
        fabTrigger.setAttribute('aria-expanded', !isExpanded);
        fabButton.classList.toggle('fab-button--active');
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
})();
