/**
 * LAKUM ARTSPACE - Dropdown Navigation Handler
 * Handles mobile dropdown toggle, smooth scrolling, and keyboard accessibility
 * NOW WITH PROPER POSITIONING BELOW NAV ITEMS
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

        console.log('🔍 Dropdown Init:', {
            togglesFound: dropdownToggles.length,
            itemsFound: dropdownItems.length,
            mobileNavFound: !!mobileNav
        });

        if (!dropdownToggles.length) {
            console.warn('⚠️ No dropdown toggles found!');
            return;
        }

        // Attach event listeners
        attachEventListeners();
        console.log('✅ Dropdown listeners attached');
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

        // Reposition dropdowns on window resize
        window.addEventListener('resize', debounce(() => {
            repositionAllDropdowns();
        }, 100));
    }

    /**
     * Handle dropdown toggle click
     */
    function handleToggleClick(event) {
        event.preventDefault();
        event.stopPropagation();

        const dropdownItem = event.currentTarget.closest('.lakum-nav__item--dropdown');
        if (!dropdownItem) {
            console.error('❌ Could not find dropdown item parent');
            return;
        }

        const isActive = dropdownItem.classList.contains('active');
        
        console.log('🖱️ Dropdown clicked:', {
            isCurrentlyActive: isActive,
            itemElement: dropdownItem.querySelector('.lakum-nav__link')?.textContent.trim()
        });
        
        // Close all other dropdowns
        closeAllDropdowns();
        
        // Toggle this dropdown
        if (!isActive) {
            dropdownItem.classList.add('active');
            event.currentTarget.setAttribute('aria-expanded', 'true');
            console.log('✅ Dropdown opened');
            
            // Position dropdown below this item
            setTimeout(() => {
                positionDropdown(dropdownItem);
            }, 10);
        } else {
            dropdownItem.classList.remove('active');
            event.currentTarget.setAttribute('aria-expanded', 'false');
            console.log('✅ Dropdown closed');
        }
    }

    /**
     * Position dropdown below the nav item
     */
    function positionDropdown(dropdownItem) {
        const dropdown = dropdownItem.querySelector('.lakum-nav__dropdown');
        if (!dropdown) return;

        const rect = dropdownItem.getBoundingClientRect();
        const headerHeight = document.querySelector('.lakum-header')?.offsetHeight || 80;
        
        // Position below the nav item
        const top = rect.bottom + window.scrollY + 5; // 5px gap
        const left = rect.left + (rect.width / 2) - (dropdown.offsetWidth / 2);

        dropdown.style.position = 'fixed';
        dropdown.style.top = (top - window.scrollY) + 'px';
        dropdown.style.left = left + 'px';
        dropdown.style.right = 'auto';

        console.log('📍 Positioned dropdown at:', { top: top - window.scrollY, left });
    }

    /**
     * Reposition all active dropdowns (on window resize)
     */
    function repositionAllDropdowns() {
        document.querySelectorAll('.lakum-nav__item--dropdown.active').forEach(item => {
            positionDropdown(item);
        });
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
     * Handle outside click (close dropdowns) - NOW WORKS ON DESKTOP AND MOBILE
     */
    function handleOutsideClick(event) {
        // Check if click is outside any dropdown item
        const clickedInsideDropdown = Array.from(dropdownItems).some(item => {
            return item.contains(event.target);
        });

        // If clicked outside all dropdowns, close them
        if (!clickedInsideDropdown) {
            closeAllDropdowns();
            console.log('🔴 Closed dropdown (clicked outside)');
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
