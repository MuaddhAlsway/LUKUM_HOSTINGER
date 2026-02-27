/**
 * FINAL TRANSLATION ARCHITECTURE CONSOLIDATION
 * Global Language Persistence - URL Management Only
 * 
 * CRITICAL: This script ONLY manages:
 * - Reading current language from URL
 * - Updating language switcher links
 * - Persisting language preference to localStorage
 * 
 * REMOVED:
 * - All client-side translation rendering
 * - All DOM text replacement
 * - All API translation calls
 * - All MyMemory API integration
 * 
 * Server-side rendering (PHP) is the ONLY authority for translations.
 */

(function() {
    const LANG_KEY = 'lakum_language';
    
    // ============ INITIALIZATION ============
    
    function getCurrentLanguage() {
        // Read from URL parameter (primary authority)
        const params = new URLSearchParams(window.location.search);
        const urlLang = params.get('lang');
        if (urlLang === 'en' || urlLang === 'ar') {
            return urlLang;
        }
        
        // Fallback to localStorage
        return localStorage.getItem(LANG_KEY) || 'en';
    }
    
    function saveLanguage(lang) {
        localStorage.setItem(LANG_KEY, lang);
    }
    
    // ============ LANGUAGE SWITCHER MANAGEMENT ============
    
    function updateLanguageSwitcher(lang) {
        const switchers = document.querySelectorAll('.lakum-language-switcher a');
        
        switchers.forEach(switcher => {
            const langText = switcher.querySelector('.lakum-lang-text');
            
            // Build URL for language switch
            const currentPath = window.location.pathname;
            const currentParams = new URLSearchParams(window.location.search);
            const targetLang = lang === 'ar' ? 'en' : 'ar';
            
            // Update language parameter
            currentParams.set('lang', targetLang);
            
            // Determine the correct file extension
            let targetPath = currentPath;
            
            // If on an HTML page, convert to PHP equivalent
            if (currentPath.endsWith('.html')) {
                targetPath = currentPath.replace('.html', '.php');
            }
            
            // Build the new URL
            const newUrl = targetPath + '?' + currentParams.toString();
            switcher.href = newUrl;
            
            // Update switcher UI
            if (lang === 'ar') {
                switcher.setAttribute('data-lang-switch', 'en');
                switcher.title = 'Language: English';
                if (langText) langText.textContent = 'EN';
            } else {
                switcher.setAttribute('data-lang-switch', 'ar');
                switcher.title = 'Language: العربية';
                if (langText) langText.textContent = 'AR';
            }
        });
    }
    
    // ============ INITIALIZATION ============
    
    function init() {
        const currentLang = getCurrentLanguage();
        saveLanguage(currentLang);
        updateLanguageSwitcher(currentLang);
    }
    
    // Run on page load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
    
    // ============ EVENT LISTENERS ============
    
    // Language switcher clicks - navigate to new URL
    document.addEventListener('click', function(e) {
        const switcher = e.target.closest('[data-lang-switch]');
        if (switcher && switcher.href && switcher.href !== '#') {
            // Let the link navigate naturally
            return;
        }
    });
    
    // Multi-tab sync
    window.addEventListener('storage', function(e) {
        if (e.key === LANG_KEY && e.newValue) {
            updateLanguageSwitcher(e.newValue);
        }
    });
    
    // ============ GLOBAL API ============
    
    window.getLakumLanguage = () => getCurrentLanguage();
})();
