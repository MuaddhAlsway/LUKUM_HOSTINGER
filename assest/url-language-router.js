/**
 * URL-Based Language Router
 * Converts language system from localStorage to URL parameters
 * 
 * This script:
 * - Reads language from URL parameter (?lang=en or ?lang=ar)
 * - Sets HTML attributes (lang, dir, class)
 * - Provides global window.currentLanguage variable
 * - Does NOT use localStorage
 * - Does NOT use data-lang-switch
 * 
 * Must be loaded in <head> BEFORE other scripts
 */

(function() {
    'use strict';
    
    // ============ LANGUAGE DETECTION ============
    
    /**
     * Get language from URL parameter
     */
    function getLanguageFromUrl() {
        const params = new URLSearchParams(window.location.search);
        const lang = params.get('lang');
        
        // Only allow 'en' or 'ar'
        if (lang === 'en' || lang === 'ar') {
            return lang;
        }
        
        // Default to English
        return 'en';
    }
    
    /**
     * Get language from HTML lang attribute
     * (fallback for pages that already have it set)
     */
    function getLanguageFromHtml() {
        const htmlLang = document.documentElement.lang;
        if (htmlLang === 'en' || htmlLang === 'ar') {
            return htmlLang;
        }
        return 'en';
    }
    
    // ============ INITIALIZATION ============
    
    // Detect language from URL first, then HTML
    const lang = getLanguageFromUrl() || getLanguageFromHtml() || 'en';
    
    // Set global variable for other scripts
    window.currentLanguage = lang;
    
    // Set HTML attributes
    document.documentElement.lang = lang;
    document.documentElement.dir = lang === 'ar' ? 'rtl' : 'ltr';
    document.documentElement.className = lang === 'ar' ? 'lang-ar rtl' : 'lang-en ltr';
    
    // ============ LANGUAGE SWITCHER SETUP ============
    
    /**
     * Update language switcher links to use URL parameters
     * Handles both app-lang-link and lakum-lang-link classes
     */
    function setupLanguageSwitcher() {
        // Get all language switcher links (both old and new class names)
        const switchers = document.querySelectorAll('.app-lang-link, .lakum-lang-link');
        
        switchers.forEach(switcher => {
            // Get current page URL without query string
            const currentPath = window.location.pathname;
            const currentParams = new URLSearchParams(window.location.search);
            
            // Determine target language (toggle)
            const targetLang = lang === 'ar' ? 'en' : 'ar';
            
            // Build new URL with language parameter
            currentParams.set('lang', targetLang);
            const newUrl = currentPath + '?' + currentParams.toString();
            
            // Update link
            switcher.href = newUrl;
            switcher.title = targetLang === 'ar' ? 'Language: العربية' : 'Language: English';
            
            // Update text
            const langText = switcher.querySelector('.app-lang-text, .lakum-lang-text');
            if (langText) {
                langText.textContent = targetLang === 'ar' ? 'Ar' : 'En';
            }
            
            // Remove data-lang-switch attribute (no longer needed)
            switcher.removeAttribute('data-lang-switch');
        });
        
        // Remove duplicate lakum-language-switcher divs if they exist
        const lakumSwitchers = document.querySelectorAll('.lakum-language-switcher');
        if (lakumSwitchers.length > 1) {
            // Keep only the first one, remove duplicates
            for (let i = 1; i < lakumSwitchers.length; i++) {
                lakumSwitchers[i].remove();
            }
        }
    }
    
    // Setup switcher when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', setupLanguageSwitcher);
    } else {
        setupLanguageSwitcher();
    }
    
    // ============ NAVIGATION LINK UPDATES ============
    
    /**
     * Update internal navigation links to include language parameter
     */
    function updateNavigationLinks() {
        const links = document.querySelectorAll('a[href]');
        
        links.forEach(link => {
            const href = link.getAttribute('href');
            
            // Skip external links, anchors, and special links
            if (!href || href.startsWith('http') || href.startsWith('//') || 
                href.startsWith('#') || href.startsWith('javascript:') ||
                href.startsWith('mailto:') || href.startsWith('tel:')) {
                return;
            }
            
            // Skip if already has lang parameter
            if (href.includes('?lang=') || href.includes('&lang=')) {
                return;
            }
            
            // Skip language switcher links (they handle their own URLs)
            if (link.classList.contains('app-lang-link') || link.classList.contains('lakum-lang-link')) {
                return;
            }
            
            // Add language parameter
            const separator = href.includes('?') ? '&' : '?';
            const newHref = href + separator + 'lang=' + lang;
            link.setAttribute('href', newHref);
        });
    }
    
    // Update navigation links when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', updateNavigationLinks);
    } else {
        updateNavigationLinks();
    }
    
    // ============ TRANSLATION SYSTEM INTEGRATION ============
    
    /**
     * Notify translation system of current language
     * This allows static-json-translator to use URL language
     */
    window.addEventListener('load', function() {
        // Dispatch custom event for translation system
        const event = new CustomEvent('lakum-language-ready', {
            detail: { lang: lang }
        });
        document.dispatchEvent(event);
    });
    
})();
