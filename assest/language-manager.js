/**
 * DEPRECATED: Language Manager
 * This file is kept for backward compatibility but is no longer used.
 * All language management is now handled by global-language-persistence.js
 * 
 * This stub prevents errors if any code references window.languageManager
 */

window.languageManager = {
    getCurrentLanguage: () => window.getLakumLanguage ? window.getLakumLanguage() : 'en',
    setLanguage: (lang) => window.setLakumLanguage ? window.setLakumLanguage(lang) : null,
    isArabic: () => (window.getLakumLanguage ? window.getLakumLanguage() : 'en') === 'ar'
};
