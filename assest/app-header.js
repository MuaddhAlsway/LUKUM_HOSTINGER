/**
 * App Header - Responsive Navigation Handler
 * Manages mobile menu toggle, overlay, and keyboard interactions
 */

class AppHeader {
    constructor() {
        this.menuToggle = document.getElementById('menuToggle');
        this.appNav = document.getElementById('appNav');
        this.isOpen = false;

        if (this.menuToggle && this.appNav) {
            this.init();
        }
    }

    init() {
        // Menu toggle click
        this.menuToggle.addEventListener('click', () => this.toggleMenu());

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

        // Handle window resize
        window.addEventListener('resize', () => {
            if (window.innerWidth > 768 && this.isOpen) {
                this.closeMenu();
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
        document.body.style.overflow = 'hidden';
    }

    closeMenu() {
        this.isOpen = false;
        this.menuToggle.setAttribute('aria-expanded', 'false');
        this.menuToggle.classList.remove('app-header__menu-toggle--active');
        this.appNav.setAttribute('aria-expanded', 'false');
        this.appNav.classList.remove('is-open');
        this.appNav.classList.remove('app-nav--active');
        document.body.style.overflow = '';
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
