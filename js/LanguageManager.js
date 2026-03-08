/**
 * LanguageManager - Global Language State Management
 * Handles language detection, persistence, and UI updates
 */

const LanguageManager = {
    // Current language state - read from HTML element first (set by PHP)
    currentLanguage: document.documentElement.lang || localStorage.getItem('language') || 'en',
    
    /**
     * Initialize LanguageManager
     * Sets up language from HTML element (PHP), URL parameter, or localStorage
     */
    init() {
        // 1️⃣ PRIORITY: HTML element dir attribute (set by PHP from URL parameter)
        const htmlDir = document.documentElement.dir;
        const htmlLang = document.documentElement.lang;
        
        if (htmlLang && ['en', 'ar'].includes(htmlLang)) {
            this.currentLanguage = htmlLang;
            localStorage.setItem('language', htmlLang);
            return; // PHP already set it correctly, don't override
        }
        
        // 2️⃣ Check URL parameter
        const urlParams = new URLSearchParams(window.location.search);
        const urlLang = urlParams.get('lang');
        
        if (urlLang && ['en', 'ar'].includes(urlLang)) {
            this.setLanguage(urlLang);
        } else {
            // 3️⃣ Use stored language or default to English
            this.applyLanguage(this.currentLanguage);
        }
        
        // Mark as ready
        window.LAKUM_LANGUAGE_MANAGER_READY = true;
        window.dispatchEvent(new CustomEvent('languageManagerReady', {
            detail: { language: this.currentLanguage }
        }));
    },
    
    /**
     * Set language and persist it
     * @param {string} lang - Language code ('en' or 'ar')
     */
    setLanguage(lang) {
        if (!['en', 'ar'].includes(lang)) {
            console.warn(`Invalid language: ${lang}, defaulting to 'en'`);
            lang = 'en';
        }
        
        this.currentLanguage = lang;
        localStorage.setItem('language', lang);
        this.applyLanguage(lang);
        
        // Dispatch custom event for components to listen
        window.dispatchEvent(new CustomEvent('languageChanged', {
            detail: { language: lang }
        }));
    },
    
    /**
     * Apply language to document and UI
     * @param {string} lang - Language code
     */
    applyLanguage(lang) {
        // Set document language and direction
        document.documentElement.lang = lang;
        document.documentElement.dir = lang === 'ar' ? 'rtl' : 'ltr';
        
        // Update body class
        document.body.classList.remove('lang-en', 'lang-ar', 'ltr', 'rtl');
        document.body.classList.add(`lang-${lang}`, lang === 'ar' ? 'rtl' : 'ltr');
        
        // Update language switcher button
        this.updateLanguageSwitcher(lang);
    },
    
    /**
     * Get current language
     * @returns {string} Current language code
     */
    getLanguage() {
        return this.currentLanguage;
    },
    
    /**
     * Update language switcher UI
     * @param {string} lang - Current language
     */
    updateLanguageSwitcher(lang) {
        const switcher = document.querySelector('.lakum-language-switcher');
        if (!switcher) return;
        
        const link = switcher.querySelector('[data-lang-switch]');
        if (link) {
            const targetLang = lang === 'en' ? 'ar' : 'en';
            link.setAttribute('data-lang-switch', targetLang);
            link.textContent = targetLang === 'ar' ? 'العربية' : 'English';
        }
    },
    
    /**
     * Get localized text
     * @param {string} key - Text key
     * @returns {string} Localized text
     */
    getLocalizedText(key) {
        const texts = {
            'en': {
                'read_more': 'Read More',
                'book_now': 'Book Now',
                'details': 'Details',
                'view_details': 'View Details',
                'no_data': 'No data available',
                'loading': 'Loading...',
                'error': 'Error loading data'
            },
            'ar': {
                'read_more': 'اقرأ المزيد',
                'book_now': 'احجز الآن',
                'details': 'التفاصيل',
                'view_details': 'عرض التفاصيل',
                'no_data': 'لا توجد بيانات متاحة',
                'loading': 'جاري التحميل...',
                'error': 'خطأ في تحميل البيانات'
            }
        };
        
        const lang = this.currentLanguage;
        return texts[lang]?.[key] || texts['en'][key] || key;
    },
    
    /**
     * Format date with current language locale
     * @param {string} dateString - Date string (YYYY-MM-DD)
     * @returns {string} Formatted date
     */
    formatDate(dateString) {
        const date = new Date(dateString);
        const options = {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        };
        
        const locale = this.currentLanguage === 'ar' ? 'ar-SA' : 'en-US';
        return date.toLocaleDateString(locale, options);
    },
    
    /**
     * Format time to 12-hour format
     * @param {string} time24h - Time in 24-hour format (HH:MM)
     * @returns {string} Time in 12-hour format with AM/PM
     */
    formatTime(time24h) {
        if (!time24h) return '10:00 AM';
        
        const [hours, minutes] = time24h.substring(0, 5).split(':');
        let hour = parseInt(hours);
        const ampm = hour >= 12 ? 'PM' : 'AM';
        hour = hour % 12 || 12;
        
        return `${hour}:${minutes} ${ampm}`;
    }
};

// Initialize immediately (synchronous)
// This ensures LanguageManager is ready before any inline scripts run
LanguageManager.init();

// Also initialize on DOMContentLoaded as backup
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        LanguageManager.init();
    });
}
