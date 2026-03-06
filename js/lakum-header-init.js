/**
 * LAKUM Header Initialization
 * Handles mobile menu toggle, active link detection, and scroll effects
 */

(function() {
    'use strict';

    const Header = {
        // DOM elements
        header: null,
        toggle: null,
        mobileNav: null,
        navLinks: null,
        
        /**
         * Initialize header functionality
         */
        init() {
            this.cacheElements();
            if (!this.header) return;
            
            this.setupEventListeners();
            this.setActiveLink();
            this.setupScrollEffect();
        },
        
        /**
         * Cache DOM elements
         */
        cacheElements() {
            this.header = document.querySelector('.lakum-header');
            this.toggle = document.querySelector('.lakum-header__mobile-toggle');
            this.mobileNav = document.querySelector('.lakum-nav--mobile');
            this.navLinks = document.querySelectorAll('.lakum-nav__link, .lakum-nav--mobile .lakum-nav__link');
        },
        
        /**
         * Setup event listeners
         */
        setupEventListeners() {
            // Mobile toggle click
            if (this.toggle) {
                this.toggle.addEventListener('click', () => this.toggleMobileNav());
            }
            
            // Close mobile nav when link clicked
            if (this.navLinks) {
                this.navLinks.forEach(link => {
                    link.addEventListener('click', () => this.closeMobileNav());
                });
            }
            
            // Close mobile nav on outside click
            document.addEventListener('click', (e) => {
                if (this.mobileNav && this.mobileNav.classList.contains('lakum-nav--active')) {
                    if (!this.mobileNav.contains(e.target) && !this.toggle.contains(e.target)) {
                        this.closeMobileNav();
                    }
                }
            });
            
            // Close mobile nav on ESC key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && this.mobileNav && this.mobileNav.classList.contains('lakum-nav--active')) {
                    this.closeMobileNav();
                }
            });
        },
        
        /**
         * Toggle mobile navigation
         */
        toggleMobileNav() {
            if (!this.mobileNav || !this.toggle) return;
            
            const isActive = this.mobileNav.classList.contains('lakum-nav--active');
            
            if (isActive) {
                this.closeMobileNav();
            } else {
                this.openMobileNav();
            }
        },
        
        /**
         * Open mobile navigation
         */
        openMobileNav() {
            if (!this.mobileNav || !this.toggle) return;
            
            this.mobileNav.classList.add('lakum-nav--active');
            this.toggle.classList.add('lakum-header__mobile-toggle--active');
            this.toggle.setAttribute('aria-expanded', 'true');
            
            // Prevent body scroll
            document.body.style.overflow = 'hidden';
        },
        
        /**
         * Close mobile navigation
         */
        closeMobileNav() {
            if (!this.mobileNav || !this.toggle) return;
            
            this.mobileNav.classList.remove('lakum-nav--active');
            this.toggle.classList.remove('lakum-header__mobile-toggle--active');
            this.toggle.setAttribute('aria-expanded', 'false');
            
            // Restore body scroll
            document.body.style.overflow = '';
        },
        
        /**
         * Set active link based on current page
         */
        setActiveLink() {
            if (!this.navLinks) return;
            
            const currentPage = this.getCurrentPage();
            
            this.navLinks.forEach(link => {
                const href = link.getAttribute('href');
                const page = this.getPageFromHref(href);
                
                if (page === currentPage) {
                    link.classList.add('lakum-nav__link--active');
                    link.setAttribute('aria-current', 'page');
                } else {
                    link.classList.remove('lakum-nav__link--active');
                    link.removeAttribute('aria-current');
                }
            });
        },
        
        /**
         * Get current page name
         */
        getCurrentPage() {
            const path = window.location.pathname;
            const filename = path.split('/').pop() || 'index.php';
            return filename.replace('.php', '');
        },
        
        /**
         * Extract page name from href
         */
        getPageFromHref(href) {
            if (!href) return '';
            const filename = href.split('/').pop() || 'index.php';
            return filename.replace('.php', '');
        },
        
        /**
         * Setup scroll effect for header shadow
         */
        setupScrollEffect() {
            if (!this.header) return;
            
            let ticking = false;
            
            window.addEventListener('scroll', () => {
                if (!ticking) {
                    window.requestAnimationFrame(() => {
                        const scrolled = window.scrollY > 10;
                        
                        if (scrolled) {
                            this.header.classList.add('lakum-header--scrolled');
                        } else {
                            this.header.classList.remove('lakum-header--scrolled');
                        }
                        
                        ticking = false;
                    });
                    
                    ticking = true;
                }
            });
        }
    };
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => Header.init());
    } else {
        Header.init();
    }
    
    // Expose to window for debugging
    window.LakumHeader = Header;
})();
