/**
 * Responsive Navbar Mobile Toggle
 * Handles mobile menu open/close for all devices
 */

document.addEventListener('DOMContentLoaded', function() {
    const mobileToggle = document.querySelector('.lakum-header__mobile-toggle');
    const nav = document.querySelector('.lakum-nav');
    const navLinks = document.querySelectorAll('.lakum-nav__link');

    if (!mobileToggle || !nav) {
        console.warn('Mobile toggle or nav element not found');
        return;
    }

    // Toggle menu on button click
    mobileToggle.addEventListener('click', function() {
        mobileToggle.classList.toggle('lakum-header__mobile-toggle--active');
        nav.classList.toggle('lakum-nav--active');
        document.body.style.overflow = nav.classList.contains('lakum-nav--active') ? 'hidden' : '';
    });

    // Close menu when a link is clicked
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            mobileToggle.classList.remove('lakum-header__mobile-toggle--active');
            nav.classList.remove('lakum-nav--active');
            document.body.style.overflow = '';
        });
    });

    // Close menu when clicking outside
    document.addEventListener('click', function(event) {
        const isClickInsideNav = nav.contains(event.target);
        const isClickOnToggle = mobileToggle.contains(event.target);

        if (!isClickInsideNav && !isClickOnToggle && nav.classList.contains('lakum-nav--active')) {
            mobileToggle.classList.remove('lakum-header__mobile-toggle--active');
            nav.classList.remove('lakum-nav--active');
            document.body.style.overflow = '';
        }
    });

    // Close menu on window resize if screen becomes large
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
            mobileToggle.classList.remove('lakum-header__mobile-toggle--active');
            nav.classList.remove('lakum-nav--active');
            document.body.style.overflow = '';
        }
    });

    // Handle escape key to close menu
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && nav.classList.contains('lakum-nav--active')) {
            mobileToggle.classList.remove('lakum-header__mobile-toggle--active');
            nav.classList.remove('lakum-nav--active');
            document.body.style.overflow = 'auto';
        }
    });
});
