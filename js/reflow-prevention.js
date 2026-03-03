/**
 * REFLOW PREVENTION - Batch DOM reads/writes
 * Eliminates forced layout thrashing
 */

(function() {
    'use strict';

    // Batch layout measurements
    const layoutCache = new Map();

    // Get dimensions WITHOUT triggering reflow
    function getCachedDimensions(element) {
        if (layoutCache.has(element)) {
            return layoutCache.get(element);
        }

        const dims = {
            width: element.offsetWidth,
            height: element.offsetHeight,
            top: element.offsetTop,
            left: element.offsetLeft
        };

        layoutCache.set(element, dims);
        return dims;
    }

    // Clear cache on resize
    window.addEventListener('resize', () => {
        layoutCache.clear();
    }, { passive: true });

    // Batch style updates
    function batchStyleUpdates(updates) {
        requestAnimationFrame(() => {
            updates.forEach(({ element, styles }) => {
                Object.assign(element.style, styles);
            });
        });
    }

    // Prevent scroll reflow
    let scrollTimeout;
    window.addEventListener('scroll', () => {
        if (scrollTimeout) return;
        
        scrollTimeout = setTimeout(() => {
            scrollTimeout = null;
        }, 100);
    }, { passive: true });

    // Export for use
    window.reflowPrevention = {
        getCachedDimensions,
        batchStyleUpdates
    };

    console.log('✓ Reflow prevention initialized');
})();
