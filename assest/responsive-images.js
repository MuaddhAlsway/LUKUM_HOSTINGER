// Responsive Images Helper
// Provides utilities for lazy loading and responsive image handling
(function() {
  'use strict';
  
  // Lazy load images with Intersection Observer
  function initLazyLoading() {
    if ('IntersectionObserver' in window) {
      const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            const img = entry.target;
            
            // Load image
            if (img.dataset.src) {
              img.src = img.dataset.src;
            }
            
            // Load srcset
            if (img.dataset.srcset) {
              img.srcset = img.dataset.srcset;
            }
            
            // Load picture sources
            const picture = img.closest('picture');
            if (picture) {
              picture.querySelectorAll('source').forEach(source => {
                if (source.dataset.srcset) {
                  source.srcset = source.dataset.srcset;
                }
              });
            }
            
            img.classList.add('loaded');
            observer.unobserve(img);
          }
        });
      }, {
        rootMargin: '50px'
      });
      
      // Observe all lazy images
      document.querySelectorAll('img[data-src], img[data-srcset]').forEach(img => {
        imageObserver.observe(img);
      });
    }
  }
  
  // Initialize on DOM ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initLazyLoading);
  } else {
    initLazyLoading();
  }
})();
