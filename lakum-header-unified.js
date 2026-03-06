/**
 * LAKUM ARTSPACE - Unified Header Navigation
 * Mobile toggle, accessibility, and nav management
 * 
 * Features:
 * - Mobile nav toggle with aria-expanded
 * - Close on outside click
 * - Close on ESC key
 * - Prevent body scroll when nav open
 * - Smooth transitions
 */

(function() {
    'use strict';

    // ===== CONFIGURATION =====
    const CONFIG = {
        toggleSelector: '.lakum-header__mobile-toggle',
        navSelector: '.lakum-nav--mobile',
        navListSelector: '.lakum-nav__list',
        navLinkSelector: '.lakum-nav__link',
        headerSelector: '.lakum-header',
        breakpoint: 768, // Mobile breakpoint in pixels
    };

    // ===== STATE =====
    let isNavOpen = false;

    // ===== DOM ELEMENTS =====
    let toggle = null;
    let nav = null;
    let navLinks = null;
    let header = null;

    /**
     * Initialize the header navigation system
     */
    function init() {
        // Get DOM elements
        toggle = document.querySelector(CONFIG.toggleSelector);
        nav = document.querySelector(CONFIG.navSelector);
        navLinks = document.querySelectorAll(CONFIG.navLinkSelector);
        header = document.querySelector(CONFIG.headerSelector);

        if (!toggle || !nav) {
            console.warn('Lakum Header: Required elements not found');
            return;
        }

        // Set initial aria-expanded state
        toggle.setAttribute('aria-expanded', 'false');
        toggle.setAttribute('aria-label', 'Toggle navigation menu');
        toggle.setAttribute('aria-controls', 'lakum-nav-mobile');

        // Add ID to nav if not present
        if (!nav.id) {
            nav.id = 'lakum-nav-mobile';
        }

        // Bind events
        bindEvents();

        // Set up initial state
        closeNav();
    }

    /**
     * Bind all event listeners
     */
    function bindEvents() {
        // Toggle button click
        toggle.addEventListener('click', handleToggleClick);

        // Nav links click
        navLinks.forEach(link => {
            link.addEventListener('click', handleNavLinkClick);
        });

        // Outside click (on overlay)
        nav.addEventListener('click', handleNavBackdropClick);

        // ESC key
        document.addEventListener('keydown', handleEscKey);

        // Window resize (close nav on desktop)
        window.addEventListener('resize', handleWindowResize);
    }

    /**
     * Handle toggle button click
     */
    function handleToggleClick(e) {
        e.preventDefault();
        e.stopPropagation();

        if (isNavOpen) {
            closeNav();
        } else {
            openNav();
        }
    }

    /**
     * Handle nav link click
     */
    function handleNavLinkClick(e) {
        // Don't close if it's the active link (allow re-click)
        const isActive = e.target.classList.contains('lakum-nav__link--active');
        
        // Close nav after clicking a link (unless it's the same page)
        if (!isActive) {
            closeNav();
        }
    }

    /**
     * Handle backdrop/overlay click
     */
    function handleNavBackdropClick(e) {
        // Only close if clicking the overlay (::before pseudo-element area)
        // The overlay is the entire fixed area behind the nav
        if (e.target === nav) {
            closeNav();
        }
    }

    /**
     * Handle ESC key press
     */
    function handleEscKey(e) {
        if (e.key === 'Escape' && isNavOpen) {
            closeNav();
            // Return focus to toggle button
            toggle.focus();
        }
    }

    /**
     * Handle window resize
     */
    function handleWindowResize() {
        // Close nav if resizing to desktop
        if (window.innerWidth > CONFIG.breakpoint && isNavOpen) {
            closeNav();
        }
    }

    /**
     * Open mobile navigation
     */
    function openNav() {
        isNavOpen = true;

        // Update toggle state
        toggle.setAttribute('aria-expanded', 'true');
        toggle.classList.add('lakum-header__mobile-toggle--active');

        // Show nav
        nav.classList.add('lakum-nav--active');

        // Prevent body scroll
        document.body.style.overflow = 'hidden';
        document.body.style.position = 'fixed';
        document.body.style.width = '100%';

        // Focus first nav link for accessibility
        const firstLink = nav.querySelector(CONFIG.navLinkSelector);
        if (firstLink) {
            setTimeout(() => firstLink.focus(), 100);
        }
    }

    /**
     * Close mobile navigation
     */
    function closeNav() {
        isNavOpen = false;

        // Update toggle state
        toggle.setAttribute('aria-expanded', 'false');
        toggle.classList.remove('lakum-header__mobile-toggle--active');

        // Hide nav
        nav.classList.remove('lakum-nav--active');

        // Restore body scroll
        document.body.style.overflow = '';
        document.body.style.position = '';
        document.body.style.width = '';
    }

    /**
     * Set active nav link based on current page
     */
    function setActiveNavLink() {
        const currentPath = window.location.pathname;
        const currentLang = document.documentElement.lang || 'en';

        navLinks.forEach(link => {
            const href = link.getAttribute('href');
            
            // Remove active class
            link.classList.remove('lakum-nav__link--active');
            link.removeAttribute('aria-current');

            // Check if link matches current path
            if (href && (
                currentPath.includes(href) ||
                currentPath.endsWith(href) ||
                (href === '/' && currentPath === '/') ||
                (href === '/ar' && currentPath === '/ar')
            )) {
                link.classList.add('lakum-nav__link--active');
                link.setAttribute('aria-current', 'page');
            }
        });
    }

    /**
     * Update header on scroll (optional shadow effect)
     */
    function handleScroll() {
        if (window.scrollY > 0) {
            header.style.boxShadow = '0 4px 16px rgba(0, 0, 0, 0.12)';
        } else {
            header.style.boxShadow = '';
        }
    }

    // ===== INITIALIZATION =====
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Optional: Add scroll listener for enhanced shadow
    window.addEventListener('scroll', handleScroll, { passive: true });

    // ===== PUBLIC API (for external use) =====
    window.LakumHeader = {
        openNav: openNav,
        closeNav: closeNav,
        toggleNav: () => isNavOpen ? closeNav() : openNav(),
        isOpen: () => isNavOpen,
        setActiveLink: setActiveNavLink,
    };

})();
