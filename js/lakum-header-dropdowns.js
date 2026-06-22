/**
 * LAKUM ARTSPACE - Dropdown Navigation Handler
 * Simple click-based dropdown toggle for mobile/tablet
 */

(function() {
    'use strict';

    let dropdownToggles = null;
    let dropdownItems = null;

    /**
     * Initialize dropdown functionality
     */
    function init() {
        dropdownToggles = document.querySelectorAll('.lakum-nav__dropdown-toggle');
        dropdownItems = document.querySelectorAll('.lakum-nav__item--dropdown');

        if (!dropdownToggles.length) {
            console.warn('⚠️ No dropdown toggles found!');
            return;
        }

        attachEventListeners();
        console.log('✅ Dropdown listeners attached');
    }

    /**
     * Attach event listeners
     */
    function attachEventListeners() {
        // Toggle dropdown on click - CRITICAL: touchend for mobile, click for desktop
        dropdownToggles.forEach(toggle => {
            // Handle both touch and click events
            toggle.addEventListener('touchend', handleToggleClick);
            toggle.addEventListener('click', handleToggleClick);
            
            // Make sure it's clickable
            toggle.style.pointerEvents = 'auto';
        });

        // Close dropdown when clicking link
        document.querySelectorAll('.lakum-nav__dropdown-link').forEach(link => {
            link.addEventListener('touchend', handleDropdownLinkClick);
            link.addEventListener('click', handleDropdownLinkClick);
        });

        // Close dropdowns on outside click
        document.addEventListener('click', handleOutsideClick);
        document.addEventListener('touchend', handleOutsideClick);

        // Close dropdowns on ESC key
        document.addEventListener('keydown', handleEscapeKey);
    }

    /**
     * Handle dropdown toggle click/touch
     */
    function handleToggleClick(event) {
        event.preventDefault();
        event.stopPropagation();
        event.stopImmediatePropagation();

        const toggle = event.currentTarget;
        const dropdownItem = toggle.closest('.lakum-nav__item--dropdown');
        
        if (!dropdownItem) {
            console.warn('⚠️ Could not find dropdown item parent');
            return;
        }

        const isActive = dropdownItem.classList.contains('active');

        // Close all other dropdowns
        closeAllDropdowns();

        // Toggle this dropdown
        if (!isActive) {
            dropdownItem.classList.add('active');
            toggle.setAttribute('aria-expanded', 'true');
            console.log('✅ Dropdown opened');
        } else {
            dropdownItem.classList.remove('active');
            toggle.setAttribute('aria-expanded', 'false');
            console.log('✅ Dropdown closed');
        }
    }

    /**
     * Handle dropdown link click/touch
     */
    function handleDropdownLinkClick(event) {
        // Prevent event from bubbling to parent nav item
        event.stopPropagation();
        event.stopImmediatePropagation();
        
        // Close dropdown after navigation
        setTimeout(() => {
            closeAllDropdowns();
        }, 100);
    }

    /**
     * Close all open dropdowns
     */
    function closeAllDropdowns() {
        dropdownItems.forEach(item => {
            item.classList.remove('active');
        });

        dropdownToggles.forEach(toggle => {
            toggle.setAttribute('aria-expanded', 'false');
        });
    }

    /**
     * Handle outside click/touch
     */
    function handleOutsideClick(event) {
        const clickedInsideDropdown = Array.from(dropdownItems).some(item => {
            return item.contains(event.target);
        });

        if (!clickedInsideDropdown) {
            closeAllDropdowns();
        }
    }

    /**
     * Handle ESC key
     */
    function handleEscapeKey(event) {
        if (event.key === 'Escape') {
            closeAllDropdowns();
        }
    }

    /**
     * Auto-initialize when DOM is ready
     */
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
