/**
 * LAKUM Header - Mobile Menu & Navigation Handler
 * Handles mobile menu toggle, active link detection, and smooth interactions
 */

(function() {
    'use strict';

    // Configuration
    const config = {
        headerSelector: '.lakum-header',
        mobileToggleSelector: '.lakum-header__mobile-toggle',
        navSelector: '.lakum-nav',
        navLinkSelector: '.lakum-nav__link',
        langLinkSelector: '.lakum-lang-link',
        activeClass: 'lakum-nav--active',
        activeLinkClass: 'lakum-nav__link--active',
        mobileBreakpoint: 768
    };

    // State
    let state = {
        mobileMenuOpen: false,
        currentPage: getCurrentPage()
    };

    /**
     * Initialize header functionality
     */
    function init() {
        const header = document.querySelector(config.headerSelector);
        if (!header) return;

        setupMobileToggle();
        setupActiveLinks();
        setupLanguageSwitcher();
        setupClickOutside();
        setupWindowResize();
        setupScrollBehavior();
    }

    /**
     * Get current page filename
     */
    function getCurrentPage() {
        const path = window.location.pathname;
        const filename = path.substring(path.lastIndexOf('/') + 1) || 'index.php';
        return filename;
    }

    /**
     * Setup mobile menu toggle
     */
    function setupMobileToggle() {
        const toggle = document.querySelector(config.mobileToggleSelector);
        const nav = document.querySelector(config.navSelector);

        if (!toggle || !nav) return;

        toggle.addEventListener('click', function(e) {
            e.stopPropagation();
            toggleMobileMenu();
        });
    }

    /**
     * Toggle mobile menu open/close
     */
    function toggleMobileMenu() {
        const toggle = document.querySelector(config.mobileToggleSelector);
        const nav = document.querySelector(config.navSelector);

        if (!toggle || !nav) return;

        state.mobileMenuOpen = !state.mobileMenuOpen;

        toggle.setAttribute('aria-expanded', state.mobileMenuOpen);
        nav.classList.toggle(config.activeClass, state.mobileMenuOpen);

        // Prevent body scroll when menu is open
        if (state.mobileMenuOpen) {
            document.body.style.overflow = 'hidden';
        } else {
            document.body.style.overflow = '';
        }
    }

    /**
     * Close mobile menu
     */
    function closeMobileMenu() {
        if (!state.mobileMenuOpen) return;

        const toggle = document.querySelector(config.mobileToggleSelector);
        const nav = document.querySelector(config.navSelector);

        state.mobileMenuOpen = false;

        if (toggle) toggle.setAttribute('aria-expanded', 'false');
        if (nav) nav.classList.remove(config.activeClass);

        document.body.style.overflow = '';
    }

    /**
     * Setup active link detection
     */
    function setupActiveLinks() {
        const links = document.querySelectorAll(config.navLinkSelector);

        links.forEach(link => {
            // Remove existing active class
            link.classList.remove(config.activeLinkClass);

            // Check if link matches current page
            const href = link.getAttribute('href');
            if (isCurrentPage(href)) {
                link.classList.add(config.activeLinkClass);
            }

            // Add click handler to close mobile menu
            link.addEventListener('click', function() {
                closeMobileMenu();
            });
        });
    }

    /**
     * Check if link matches current page
     */
    function isCurrentPage(href) {
        if (!href) return false;

        // Normalize URLs
        const currentPath = window.location.pathname;
        const currentFile = currentPath.substring(currentPath.lastIndexOf('/') + 1) || 'index.php';

        // Handle different URL formats
        if (href === './' || href === 'index.php' || href === '/') {
            return currentFile === 'index.php' || currentFile === '';
        }

        // Direct filename match
        if (href === currentFile) {
            return true;
        }

        // Handle relative paths
        const hrefFile = href.substring(href.lastIndexOf('/') + 1);
        return hrefFile === currentFile;
    }

    /**
     * Setup language switcher
     */
    function setupLanguageSwitcher() {
        const langLink = document.querySelector(config.langLinkSelector);

        if (!langLink) return;

        langLink.addEventListener('click', function(e) {
            // Allow default behavior for language switching
            closeMobileMenu();
        });
    }

    /**
     * Setup click outside to close menu
     */
    function setupClickOutside() {
        document.addEventListener('click', function(e) {
            const header = document.querySelector(config.headerSelector);
            const toggle = document.querySelector(config.mobileToggleSelector);

            if (!header || !toggle) return;

            // Close menu if click is outside header
            if (!header.contains(e.target) && state.mobileMenuOpen) {
                closeMobileMenu();
            }
        });
    }

    /**
     * Setup window resize handler
     */
    function setupWindowResize() {
        let resizeTimer;

        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                // Close mobile menu if resizing to desktop
                if (window.innerWidth > config.mobileBreakpoint && state.mobileMenuOpen) {
                    closeMobileMenu();
                }
            }, 250);
        });
    }

    /**
     * Setup scroll behavior
     */
    function setupScrollBehavior() {
        let lastScrollTop = 0;
        const header = document.querySelector(config.headerSelector);

        if (!header) return;

        window.addEventListener('scroll', function() {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

            // Add shadow on scroll
            if (scrollTop > 10) {
                header.style.boxShadow = '0 4px 12px rgba(0, 0, 0, 0.12)';
            } else {
                header.style.boxShadow = '';
            }

            lastScrollTop = scrollTop;
        }, { passive: true });
    }

    /**
     * Update active link when page changes (for SPA or dynamic navigation)
     */
    window.updateActiveLink = function(page) {
        state.currentPage = page;
        setupActiveLinks();
    };

    /**
     * Expose close menu function globally
     */
    window.closeLakumMenu = closeMobileMenu;

    /**
     * Initialize when DOM is ready
     */
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    /**
     * Re-initialize on language change
     */
    document.addEventListener('languageChanged', function() {
        setupActiveLinks();
    });

})();
