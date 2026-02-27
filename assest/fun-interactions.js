/**
 * Fun Interactions
 * Adds interactive elements and animations
 */

(function() {
    'use strict';

    // Smooth scroll behavior
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            // Only prevent default and scroll if href is a valid selector (not just "#")
            if (href && href !== '#') {
                e.preventDefault();
                try {
                    const target = document.querySelector(href);
                    if (target) {
                        target.scrollIntoView({ behavior: 'smooth' });
                    }
                } catch (err) {
                    // Invalid selector, ignore
                }
            }
        });
    });

    // Add hover effects to buttons
    document.querySelectorAll('button, .btn').forEach(btn => {
        btn.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
        });
        btn.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });

    // Fade in elements on scroll
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in');
                observer.unobserve(entry.target);
            }
        });
    });

    document.querySelectorAll('[data-fade-in]').forEach(el => {
        observer.observe(el);
    });

    // FAB (Floating Action Button) Initialization
    const fabTrigger = document.getElementById('fabTrigger');
    const fabMenu = document.getElementById('fabMenu');
    const lakumContactFab = document.getElementById('lakumContactFab');

    if (fabTrigger && fabMenu && lakumContactFab) {
        // Toggle FAB menu on click
        fabTrigger.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            lakumContactFab.classList.toggle('lakum-contact-fab--active');
        });

        // Close FAB menu when clicking outside
        document.addEventListener('click', function(e) {
            if (!lakumContactFab.contains(e.target)) {
                lakumContactFab.classList.remove('lakum-contact-fab--active');
            }
        });

        // Close FAB menu when clicking on a menu item
        const fabItems = fabMenu.querySelectorAll('.lakum-contact-fab__item');
        fabItems.forEach(item => {
            item.addEventListener('click', function() {
                lakumContactFab.classList.remove('lakum-contact-fab--active');
            });
        });
    }
})();
