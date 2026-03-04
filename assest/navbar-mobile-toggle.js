/**
 * Responsive Navbar Mobile Toggle
 * Handles mobile menu open/close for all devices
 * Supports both .app-* and .lakum-* class names
 */

document.addEventListener('DOMContentLoaded', function() {
    // Try to find toggle button - support both class names
    let mobileToggle = document.querySelector('.app-header__menu-toggle');
    if (!mobileToggle) {
        mobileToggle = document.querySelector('.lakum-header__mobile-toggle');
    }
    
    // Try to find nav - support both class names
    let nav = document.querySelector('.app-nav');
    if (!nav) {
        nav = document.querySelector('.lakum-nav');
    }
    
    // Get nav links
    let navLinks = document.querySelectorAll('.app-nav__link');
    if (navLinks.length === 0) {
        navLinks = document.querySelectorAll('.lakum-nav__link');
    }

    if (!mobileToggle || !nav) {
        console.warn('Mobile toggle or nav element not found');
        return;
    }

    // Get header element
    const header = document.querySelector('.app-header') || document.querySelector('.lakum-header');

    // Toggle menu on button click
    mobileToggle.addEventListener('click', function(e) {
        e.stopPropagation();
        mobileToggle.classList.toggle('app-header__menu-toggle--active');
        mobileToggle.classList.toggle('lakum-header__mobile-toggle--active');
        nav.classList.toggle('app-nav--active');
        nav.classList.toggle('lakum-nav--active');
        if (header) {
            header.classList.toggle('app-header--menu-open');
            header.classList.toggle('lakum-header--menu-open');
        }
        document.body.style.overflow = nav.classList.contains('app-nav--active') || nav.classList.contains('lakum-nav--active') ? 'hidden' : '';
    });

    // Close menu when a link is clicked
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            mobileToggle.classList.remove('app-header__menu-toggle--active');
            mobileToggle.classList.remove('lakum-header__mobile-toggle--active');
            nav.classList.remove('app-nav--active');
            nav.classList.remove('lakum-nav--active');
            if (header) {
                header.classList.remove('app-header--menu-open');
                header.classList.remove('lakum-header--menu-open');
            }
            document.body.style.overflow = '';
        });
    });

    // Close menu when clicking outside
    document.addEventListener('click', function(event) {
        const isClickInsideNav = nav.contains(event.target);
        const isClickOnToggle = mobileToggle.contains(event.target);
        const isMenuOpen = nav.classList.contains('app-nav--active') || nav.classList.contains('lakum-nav--active');

        if (!isClickInsideNav && !isClickOnToggle && isMenuOpen) {
            mobileToggle.classList.remove('app-header__menu-toggle--active');
            mobileToggle.classList.remove('lakum-header__mobile-toggle--active');
            nav.classList.remove('app-nav--active');
            nav.classList.remove('lakum-nav--active');
            if (header) {
                header.classList.remove('app-header--menu-open');
                header.classList.remove('lakum-header--menu-open');
            }
            document.body.style.overflow = '';
        }
    });

    // Close menu on window resize if screen becomes large
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
            mobileToggle.classList.remove('app-header__menu-toggle--active');
            mobileToggle.classList.remove('lakum-header__mobile-toggle--active');
            nav.classList.remove('app-nav--active');
            nav.classList.remove('lakum-nav--active');
            if (header) {
                header.classList.remove('app-header--menu-open');
                header.classList.remove('lakum-header--menu-open');
            }
            document.body.style.overflow = '';
        }
    });

    // Handle escape key to close menu
    document.addEventListener('keydown', function(event) {
        const isMenuOpen = nav.classList.contains('app-nav--active') || nav.classList.contains('lakum-nav--active');
        if (event.key === 'Escape' && isMenuOpen) {
            mobileToggle.classList.remove('app-header__menu-toggle--active');
            mobileToggle.classList.remove('lakum-header__mobile-toggle--active');
            nav.classList.remove('app-nav--active');
            nav.classList.remove('lakum-nav--active');
            if (header) {
                header.classList.remove('app-header--menu-open');
                header.classList.remove('lakum-header--menu-open');
            }
            document.body.style.overflow = 'auto';
        }
    });
});
