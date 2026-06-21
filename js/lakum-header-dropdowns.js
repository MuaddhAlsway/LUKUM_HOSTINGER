/**
 * LAKUM ARTSPACE - Dropdown Navigation Handler
 * Simple click-based dropdown toggle
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
        // Toggle dropdown on click
        dropdownToggles.forEach(toggle => {
            toggle.addEventListener('click', handleToggleClick);
        });

        // Close dropdown when clicking link
        document.querySelectorAll('.lakum-nav__dropdown-link').forEach(link => {
            link.addEventListener('click', handleDropdownLinkClick);
        });

        // Close dropdowns on outside click
        document.addEventListener('click', handleOutsideClick);

        // Close dropdowns on ESC key
        document.addEventListener('keydown', handleEscapeKey);
    }

    /**
     * Handle dropdown toggle click
     */
    function handleToggleClick(event) {
        event.preventDefault();
        event.stopPropagation();

        const dropdownItem = event.currentTarget.closest('.lakum-nav__item--dropdown');
        if (!dropdownItem) return;

        const isActive = dropdownItem.classList.contains('active');

        // Close all other dropdowns
        closeAllDropdowns();

        // Toggle this dropdown
        if (!isActive) {
            dropdownItem.classList.add('active');
            event.currentTarget.setAttribute('aria-expanded', 'true');
            console.log('✅ Dropdown opened');
        } else {
            dropdownItem.classList.remove('active');
            event.currentTarget.setAttribute('aria-expanded', 'false');
            console.log('✅ Dropdown closed');
        }
    }

    /**
     * Handle dropdown link click
     */
    function handleDropdownLinkClick(event) {
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
     * Handle outside click
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
