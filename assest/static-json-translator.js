/**
 * Static JSON Translator
 * Translates static content using JSON files
 * 
 * Features:
 * - Loads translations from JSON files
 * - Applies translations to elements with data-i18n attribute
 * - Caches translations in memory
 * - Fast performance (no API calls)
 * - Supports multiple languages
 */

class StaticJsonTranslator {
    constructor() {
        this.translations = {};
        this.currentLang = this.getCurrentLanguage();
        this.isLoading = false;
        this.cache = {};
    }

    /**
     * Get current language from URL parameter or HTML attribute
     * PRODUCTION ARCHITECTURE: URL is primary authority
     * Priority: URL parameter (?lang=en/ar) > HTML lang attribute > Default (en)
     * 
     * IMPORTANT: Does NOT read from localStorage
     * IMPORTANT: Does NOT read from window.currentLanguage
     * IMPORTANT: Only reads from URL or HTML (set by PHP)
     */
    getCurrentLanguage() {
        // 1️⃣ URL PARAMETER IS PRIMARY AUTHORITY
        const params = new URLSearchParams(window.location.search);
        const urlLang = params.get('lang');
        if (urlLang === 'en' || urlLang === 'ar') {
            return urlLang;
        }
        
        // 2️⃣ HTML LANG ATTRIBUTE (set by PHP)
        const htmlLang = document.documentElement.lang;
        if (htmlLang === 'en' || htmlLang === 'ar') {
            return htmlLang;
        }
        
        // 3️⃣ DEFAULT TO ENGLISH
        return 'en';
    }

    /**
     * Load translations from server
     */
    async loadTranslations(lang) {
        // Check cache first
        if (this.cache[lang]) {
            this.translations = this.cache[lang];
            return true;
        }

        if (this.isLoading) return false;
        this.isLoading = true;

        try {
            const response = await fetch(`api/get-translations.php?lang=${lang}`);
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            const data = await response.json();

            if (!data.success) {
                throw new Error(data.error || 'Failed to load translations');
            }

            this.translations = data.translations;
            this.cache[lang] = data.translations;
            this.currentLang = lang;

            console.log(`Loaded ${data.count} translations for language: ${lang}`);
            return true;
        } catch (error) {
            console.error('Failed to load translations:', error);
            return false;
        } finally {
            this.isLoading = false;
        }
    }

    /**
     * Get translation for key
     */
    get(key, defaultValue = null) {
        if (this.translations[key]) {
            return this.translations[key];
        }
        return defaultValue || key;
    }

    /**
     * Apply translations to page
     */
    applyTranslations() {
        const elements = document.querySelectorAll('[data-i18n]');
        
        elements.forEach(element => {
            const key = element.getAttribute('data-i18n');
            const translation = this.get(key);
            
            // Determine where to apply translation
            const target = element.getAttribute('data-i18n-target') || 'text';
            
            if (target === 'text') {
                element.textContent = translation;
            } else if (target === 'html') {
                element.innerHTML = translation;
            } else if (target === 'placeholder') {
                element.placeholder = translation;
            } else if (target === 'title') {
                element.title = translation;
            } else if (target === 'value') {
                element.value = translation;
            } else if (target === 'alt') {
                element.alt = translation;
            }
        });

        console.log(`Applied translations to ${elements.length} elements`);
    }

    /**
     * Set language and apply translations
     */
    async setLanguage(lang) {
        if (lang === this.currentLang && Object.keys(this.translations).length > 0) {
            return; // Already loaded
        }

        const loaded = await this.loadTranslations(lang);
        if (loaded) {
            this.applyTranslations();
        }
    }

    /**
     * Initialize translator
     */
    async init() {
        // Load translations for current language
        await this.loadTranslations(this.currentLang);
        
        // Apply translations
        this.applyTranslations();

        // Listen for language changes
        this.setupLanguageListener();
    }

    /**
     * Setup listener for language changes
     * PRODUCTION ARCHITECTURE: Only listens to URL changes
     * Does NOT listen to localStorage
     */
    setupLanguageListener() {
        // Listen for URL changes (if using History API)
        window.addEventListener('popstate', () => {
            const newLang = this.getCurrentLanguage();
            if (newLang !== this.currentLang) {
                this.setLanguage(newLang);
            }
        });
    }
}

// Create global instance
window.staticJsonTranslator = new StaticJsonTranslator();

// Initialize on page load
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.staticJsonTranslator.init();
    });
} else {
    window.staticJsonTranslator.init();
}
