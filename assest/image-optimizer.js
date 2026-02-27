/**
 * LAKUM Artspace - Advanced Image Optimizer
 * Handles lazy loading, caching, and progressive image loading
 */

class ImageOptimizer {
    constructor() {
        this.observer = null;
        this.cache = new Map();
        this.loadedImages = new Set();
        this.init();
    }

    init() {
        // Initialize Intersection Observer for lazy loading
        this.setupLazyLoading();

        // Setup image caching
        this.setupImageCaching();

        // Preload critical images
        this.preloadCriticalImages();

        // Handle dynamic images
        this.observeDynamicImages();
    }

    /**
     * Setup Intersection Observer for lazy loading
     */
    setupLazyLoading() {
        const options = {
            root: null,
            rootMargin: '50px', // Start loading 50px before entering viewport
            threshold: 0.01
        };

        this.observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    this.loadImage(entry.target);
                    this.observer.unobserve(entry.target);
                }
            });
        }, options);

        // Observe all images with loading="lazy"
        this.observeImages();
    }

    /**
     * Observe all lazy-loadable images
     */
    observeImages() {
        const images = document.querySelectorAll('img[loading="lazy"]');
        images.forEach(img => {
            if (!this.loadedImages.has(img)) {
                this.observer.observe(img);
            }
        });
    }

    /**
     * Load image with progressive enhancement
     */
    loadImage(img) {
        if (this.loadedImages.has(img)) {
            return;
        }

        const src = img.dataset.src || img.src;
        const srcset = img.dataset.srcset || img.srcset;

        // Check cache first
        if (this.cache.has(src)) {
            this.applyImage(img, src, srcset);
            return;
        }

        // Show loading state
        img.classList.add('lakum-img-loading');

        // Create new image to preload
        const tempImg = new Image();

        tempImg.onload = () => {
            this.cache.set(src, true);
            this.applyImage(img, src, srcset);
            img.classList.remove('lakum-img-loading');
            img.classList.add('lakum-img-loaded');
            this.loadedImages.add(img);
        };

        tempImg.onerror = () => {
            img.classList.remove('lakum-img-loading');
            img.classList.add('lakum-img-error');
            console.error('Failed to load image:', src);
        };

        if (srcset) {
            tempImg.srcset = srcset;
        }
        tempImg.src = src;
    }

    /**
     * Apply image source
     */
    applyImage(img, src, srcset) {
        if (srcset && !img.srcset) {
            img.srcset = srcset;
        }
        if (!img.src || img.dataset.src) {
            img.src = src;
        }
        if (img.dataset.src) {
            delete img.dataset.src;
        }
        if (img.dataset.srcset) {
            delete img.dataset.srcset;
        }
    }

    /**
     * Preload critical above-the-fold images
     */
    preloadCriticalImages() {
        const criticalImages = document.querySelectorAll('img[fetchpriority="high"]');
        criticalImages.forEach(img => {
            const src = img.dataset.src || img.src;
            if (src && !this.cache.has(src)) {
                const link = document.createElement('link');
                link.rel = 'preload';
                link.as = 'image';
                link.href = src;
                if (img.srcset || img.dataset.srcset) {
                    link.imageSrcset = img.srcset || img.dataset.srcset;
                }
                document.head.appendChild(link);
                this.cache.set(src, true);
            }
        });
    }

    /**
     * Setup image caching with Service Worker
     */
    setupImageCaching() {
        // Service Worker disabled - not needed for this project
        // if ('serviceWorker' in navigator) {
        //     navigator.serviceWorker.register('/LUKUM(main)/sw.js').catch(err => {
        //         console.log('Service Worker registration failed:', err);
        //     });
        // }

        // Use localStorage for image metadata caching
        this.loadCacheFromStorage();
    }

    /**
     * Load cache metadata from localStorage
     */
    loadCacheFromStorage() {
        try {
            const cached = localStorage.getItem('lakum_image_cache');
            if (cached) {
                const data = JSON.parse(cached);
                const now = Date.now();
                // Cache valid for 7 days
                if (now - data.timestamp < 7 * 24 * 60 * 60 * 1000) {
                    data.images.forEach(src => this.cache.set(src, true));
                }
            }
        } catch (e) {
            console.error('Failed to load image cache:', e);
        }
    }

    /**
     * Save cache metadata to localStorage
     */
    saveCacheToStorage() {
        try {
            const data = {
                timestamp: Date.now(),
                images: Array.from(this.cache.keys())
            };
            localStorage.setItem('lakum_image_cache', JSON.stringify(data));
        } catch (e) {
            console.error('Failed to save image cache:', e);
        }
    }

    /**
     * Observe dynamically added images
     */
    observeDynamicImages() {
        const mutationObserver = new MutationObserver((mutations) => {
            mutations.forEach(mutation => {
                mutation.addedNodes.forEach(node => {
                    if (node.nodeType === 1) { // Element node
                        if (node.tagName === 'IMG' && node.loading === 'lazy') {
                            this.observer.observe(node);
                        }
                        // Check for images in added subtree
                        const images = node.querySelectorAll ? node.querySelectorAll('img[loading="lazy"]') : [];
                        images.forEach(img => this.observer.observe(img));
                    }
                });
            });
        });

        mutationObserver.observe(document.body, {
            childList: true,
            subtree: true
        });
    }

    /**
     * Prefetch images for next page
     */
    prefetchImages(urls) {
        urls.forEach(url => {
            if (!this.cache.has(url)) {
                const link = document.createElement('link');
                link.rel = 'prefetch';
                link.as = 'image';
                link.href = url;
                document.head.appendChild(link);
            }
        });
    }

    /**
     * Clear cache
     */
    clearCache() {
        this.cache.clear();
        this.loadedImages.clear();
        localStorage.removeItem('lakum_image_cache');
    }

    /**
     * Get cache statistics
     */
    getCacheStats() {
        return {
            cachedImages: this.cache.size,
            loadedImages: this.loadedImages.size
        };
    }
}

// Initialize on DOM ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.lakumImageOptimizer = new ImageOptimizer();
    });
} else {
    window.lakumImageOptimizer = new ImageOptimizer();
}

// Save cache before page unload
window.addEventListener('beforeunload', () => {
    if (window.lakumImageOptimizer) {
        window.lakumImageOptimizer.saveCacheToStorage();
    }
});