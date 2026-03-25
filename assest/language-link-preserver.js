/**
 * Language Link Preserver
 * Automatically adds language parameter to all internal links
 * Ensures language is preserved when navigating between pages
 */

document.addEventListener('DOMContentLoaded', function() {
    // Get current language from URL or localStorage
    const urlParams = new URLSearchParams(window.location.search);
    const currentLang = urlParams.get('lang') || localStorage.getItem('lakum_language') || 'en';
    
    // Save language to localStorage for persistence
    localStorage.setItem('lakum_language', currentLang);
    
    // Find all internal links and add language parameter
    const links = document.querySelectorAll('a[href]');
    
    links.forEach(link => {
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
            // Parse the URL
            const url = new URL(href, window.location.origin);
            
            // Add or update lang parameter
            url.searchParams.set('lang', currentLang);
            
            // Update the link
            link.setAttribute('href', url.pathname + url.search);
        }
    });
});
