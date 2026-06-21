/**
 * LAKUM ARTSPACE - Dropdown Navigation Handler
 * Handles mobile dropdown toggle, smooth scrolling, and keyboard accessibility
 */

(function() {
    'use strict';

    // Configuration
    const MOBILE_BREAKPOINT = 1024;
    const SCROLL_OFFSET = 80; // Header height

    // DOM Elements
    let dropdownToggles = null;
    let dropdownItems = null;
    let mobileNav = null;

    /**
     * Initialize dropdown functionality
     */
    function init() {
        dropdownToggles = document.querySelectorAll('.lakum-nav__dropdown-toggle');
        dropdownItems = document.querySelectorAll('.lakum-nav__item--dropdown');
        mobileNav = document.getElementById('lakum-nav-mobile');

        if (!dropdownToggles.length) return;

        // Attach event listeners
        attachEventListeners();
    }

    /**
     * Attach event listeners to dropdown elements
     */
    function attachEventListeners() {
        // Dropdown toggle click handler
        dropdownToggles.forEach(toggle => {
            toggle.addEventListener('click', handleToggleClick);
            toggle.addEventListener('keydown', handleToggleKeydown);
        });

        // Dropdown link click handler (close dropdown after navigation)
        document.querySelectorAll('.lakum-nav__dropdown-link').forEach(link => {
            link.addEventListener('click', handleDropdownLinkClick);
        });

        // Close dropdowns on outside click
        document.addEventListener('click', handleOutsideClick);

        // Close dropdowns on ESC key
        document.addEventListener('keydown', handleEscapeKey);

        // Handle smooth scroll for anchor links
        handleSmoothScroll();
    }

    /**
     * Handle dropdown toggle click
     */
    function handleToggleClick(event) {
        event.preventDefault();
        event.stopPropagation();

        const isMobile = window.innerWidth <= MOBILE_BREAKPOINT;
        if (!isMobile) return; // Only handle on mobile

        const dropdownItem = event.currentTarget.closest('.lakum-nav__item--dropdown');
        if (!dropdownItem) return;

        const isActive = dropdownItem.classList.contains('active');
        
        // Close all other dropdowns
        closeAllDropdowns();
        
        // Toggle this dropdown
        if (!isActive) {
            dropdownItem.classList.add('active');
            event.currentTarget.setAttribute('aria-expanded', 'true');
        }
    }

    /**
     * Handle keyboard navigation on toggle button
     */
    function handleToggleKeydown(event) {
        if (event.key === 'Enter' || event.key === ' ') {
            event.preventDefault();
            event.currentTarget.click();
        }
    }

    /**
     * Handle dropdown link click (navigate and close)
     */
    function handleDropdownLinkClick(event) {
        // Allow default navigation (href will handle it)
        // Just close the dropdown after a small delay to allow link to navigate
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
     * Handle outside click (close dropdowns)
     */
    function handleOutsideClick(event) {
        const isMobile = window.innerWidth <= MOBILE_BREAKPOINT;
        if (!isMobile) return;

        const nav = document.querySelector('.lakum-nav--mobile');
        const toggle = document.querySelector('.lakum-header__mobile-toggle');

        if (!nav || !toggle) return;

        // If click is outside nav and not on mobile toggle, close dropdowns
        if (!nav.contains(event.target) && !toggle.contains(event.target)) {
            closeAllDropdowns();
        }
    }

    /**
     * Handle ESC key to close dropdowns
     */
    function handleEscapeKey(event) {
        if (event.key === 'Escape') {
            closeAllDropdowns();
        }
    }

    /**
     * Handle smooth scroll for anchor links
     * Offset scroll by header height
     */
    function handleSmoothScroll() {
        // Listen for hash changes
        window.addEventListener('hashchange', smoothScrollToAnchor);
        
        // Also handle direct clicks on dropdown links
        document.querySelectorAll('.lakum-nav__dropdown-link[href*="#"]').forEach(link => {
            link.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                const hashIndex = href.indexOf('#');
                if (hashIndex > -1) {
                    const hash = href.substring(hashIndex);
                    setTimeout(() => {
                        scrollToAnchor(hash);
                    }, 100);
                }
            });
        });

        // Check if page loaded with hash
        if (window.location.hash) {
            setTimeout(() => {
                smoothScrollToAnchor();
            }, 500);
        }
    }

    /**
     * Smooth scroll to anchor with offset
     */
    function smoothScrollToAnchor() {
        const hash = window.location.hash;
        if (hash) {
            scrollToAnchor(hash);
        }
    }

    /**
     * Scroll to specific anchor ID
     */
    function scrollToAnchor(hash) {
        const element = document.querySelector(hash);
        if (!element) return;

        const elementPosition = element.getBoundingClientRect().top + window.scrollY;
        const offsetPosition = elementPosition - SCROLL_OFFSET;

        window.scrollTo({
            top: offsetPosition,
            behavior: 'smooth'
        });
    }

    /**
     * Handle responsive behavior (close dropdowns when resizing to desktop)
     */
    window.addEventListener('resize', debounce(() => {
        if (window.innerWidth > MOBILE_BREAKPOINT) {
            closeAllDropdowns();
        }
    }, 250));

    /**
     * Debounce utility
     */
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    /**
     * Public API
     */
    window.LakumDropdowns = {
        closeAll: closeAllDropdowns,
        init: init
    };

    /**
     * Auto-initialize when DOM is ready
     */
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
