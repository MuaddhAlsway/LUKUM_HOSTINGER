/**
 * Language Switcher - Handle language switching on all pages
 * Works with the PHP language system
 */

document.addEventListener('DOMContentLoaded', function() {
    // Get the language switcher link
    const langSwitcher = document.querySelector('.lakum-language-switcher a');
    
    if (!langSwitcher) return;
    
    // Get current language from PHP (set in index.php)
    const currentLang = window.LAKUM_LANG || 'en';
    
    // Update switcher display
    updateLanguageSwitcherDisplay(currentLang);
    
    // Handle language switcher click
    langSwitcher.addEventListener('click', function(e) {
        // The link href already has the correct language parameter
        // Just let it navigate naturally
        // The page will reload with the new language
    });
});

/**
 * Update language switcher display based on current language
 */
function updateLanguageSwitcherDisplay(currentLang) {
    const langSwitcher = document.querySelector('.lakum-language-switcher a');
    if (!langSwitcher) return;
    
    const langText = langSwitcher.querySelector('.lakum-lang-text');
    
    if (currentLang === 'ar') {
        // Currently Arabic, show English option
        langText.textContent = 'EN';
        langSwitcher.setAttribute('title', 'Switch to English');
    } else {
        // Currently English, show Arabic option
        langText.textContent = 'AR';
        langSwitcher.setAttribute('title', 'التبديل إلى العربية');
    }
}

/**
 * Alternative: Direct language switch without page reload (optional)
 * Uncomment to use AJAX-based switching instead of page reload
 */
/*
document.addEventListener('DOMContentLoaded', function() {
    const langSwitcher = document.querySelector('.lakum-language-switcher a');
    
    if (!langSwitcher) return;
    
    langSwitcher.addEventListener('click', function(e) {
        e.preventDefault();
        
        // Get target language
        const currentLang = window.LAKUM_LANG || 'en';
        const targetLang = currentLang === 'en' ? 'ar' : 'en';
        
        // Update URL with new language parameter
        const url = new URL(window.location);
        url.searchParams.set('lang', targetLang);
        
        // Navigate to new URL
        window.location.href = url.toString();
    });
});
*/
