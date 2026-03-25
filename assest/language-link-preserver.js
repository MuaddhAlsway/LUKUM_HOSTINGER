/**
 * Language Link Preserver
 * Automatically adds language parameter to all internal links
 * Ensures language is preserved when navigating between pages
 */

document.addEventListener('DOMContentLoaded', function() {
    // Get current language from URL only — URL is the single source of truth
    const urlParams = new URLSearchParams(window.location.search);
    const currentLang = urlParams.get('lang') || 'en';
    
    // Find all internal links and add language parameter
    const links = document.querySelectorAll('a[href]');
    
    links.forEach(link => {
        // Skip language switcher buttons — PHP already sets their correct target lang
        if (link.closest('.lakum-language-switcher')) return;

        const href = link.getAttribute('href');
        
        // Skip external links, anchors, and special links
        if (!href || href.startsWith('http') || href.startsWith('mailto:') || href.startsWith('tel:') || href.startsWith('whatsapp:') || href.startsWith('#')) {
            return;
        }
        
        // Skip if it's a relative path to a file (like .js, .css, .png)
        if (href.includes('.') && !href.includes('.php')) {
            return;
        }
        
        // Only process PHP files and root paths
        if (href.includes('.php') || href === '/' || href === './') {
            const url = new URL(href, window.location.origin);
            url.searchParams.set('lang', currentLang);
            link.setAttribute('href', url.pathname + url.search);
        }
    });
});
