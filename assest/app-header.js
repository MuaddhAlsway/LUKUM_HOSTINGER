/**
 * App Header - Responsive Navigation Handler
 * Manages mobile menu toggle, overlay, and keyboard interactions
 * Includes accessibility features: focus management, focus trap, scroll lock
 */

class AppHeader {
    constructor() {
        this.menuToggle = document.getElementById('menuToggle');
        this.appNav = document.getElementById('appNav');
        this.isOpen = false;
        this.focusableElements = [];

        if (this.menuToggle && this.appNav) {
            // Ensure menu is closed on page load
            this.closeMenu();
            this.init();
        }
    }

    init() {
        // Menu toggle click
        this.menuToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            this.toggleMenu();
        });

        // Close menu when clicking on a link
        const navLinks = this.appNav.querySelectorAll('.app-nav__link');
        navLinks.forEach(link => {
            link.addEventListener('click', () => this.closeMenu());
        });

        // Close menu when clicking outside
        document.addEventListener('click', (e) => {
            if (this.isOpen && 
                !this.menuToggle.contains(e.target) && 
                !this.appNav.contains(e.target)) {
                this.closeMenu();
            }
        });

        // Close menu on Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.isOpen) {
                this.closeMenu();
            }
        });

        // Handle window resize with debounce
        let resizeTimeout;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(() => {
                if (window.innerWidth > 768 && this.isOpen) {
                    this.closeMenu();
                }
            }, 250);
        });

        // Trap focus within menu when open
        this.appNav.addEventListener('keydown', (e) => {
            if (e.key === 'Tab' && this.isOpen) {
                this.handleTabKey(e);
            }
        });
    }

    toggleMenu() {
        if (this.isOpen) {
            this.closeMenu();
        } else {
            this.openMenu();
        }
    }

    openMenu() {
        this.isOpen = true;
        this.menuToggle.setAttribute('aria-expanded', 'true');
        this.menuToggle.classList.add('app-header__menu-toggle--active');
        this.appNav.setAttribute('aria-expanded', 'true');
        this.appNav.classList.add('is-open');
        this.appNav.classList.add('app-nav--active');
        
        // Lock scroll on iOS Safari
        document.body.style.position = 'fixed';
        document.body.style.width = '100%';
        document.body.style.overflow = 'hidden';
        
        // Focus first menu item for accessibility
        setTimeout(() => {
            const firstLink = this.appNav.querySelector('.app-nav__link');
            if (firstLink) {
                firstLink.focus();
            }
        }, 100);
        
        // Get all focusable elements in menu
        this.updateFocusableElements();
    }

    closeMenu() {
        this.isOpen = false;
        this.menuToggle.setAttribute('aria-expanded', 'false');
        this.menuToggle.classList.remove('app-header__menu-toggle--active');
        this.appNav.setAttribute('aria-expanded', 'false');
        this.appNav.classList.remove('is-open');
        this.appNav.classList.remove('app-nav--active');
        
        // Restore scroll
        document.body.style.position = '';
        document.body.style.width = '';
        document.body.style.overflow = '';
        
        // Focus menu toggle for accessibility
        this.menuToggle.focus();
    }

    updateFocusableElements() {
        // Get all focusable elements within the menu
        this.focusableElements = Array.from(
            this.appNav.querySelectorAll(
                'a, button, [tabindex]:not([tabindex="-1"])'
            )
        );
    }

    handleTabKey(e) {
        if (this.focusableElements.length === 0) {
            this.updateFocusableElements();
        }

        const firstElement = this.focusableElements[0];
        const lastElement = this.focusableElements[this.focusableElements.length - 1];
        const activeElement = document.activeElement;

        if (e.shiftKey) {
            // Shift + Tab
            if (activeElement === firstElement) {
                e.preventDefault();
                lastElement.focus();
            }
        } else {
            // Tab
            if (activeElement === lastElement) {
                e.preventDefault();
                firstElement.focus();
            }
        }
    }
}

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        new AppHeader();
    });
} else {
    new AppHeader();
}
