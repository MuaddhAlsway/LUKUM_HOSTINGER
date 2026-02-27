/**
 * Dynamic Content Loader
 * Loads bilingual content from database based on language preference
 * 
 * Features:
 * - Loads content in selected language
 * - Caches content in memory
 * - Supports multiple content types (blog, events, press, pricing)
 * - Integrates with language switcher
 * - Fast performance
 */

class DynamicContentLoader {
    constructor() {
        this.currentLang = this.getCurrentLanguage();
        this.cache = {};
        this.isLoading = false;
    }

    /**
     * Get current language from localStorage or HTML
     */
    getCurrentLanguage() {
        // Check localStorage first
        const saved = localStorage.getItem('lakum_language');
        if (saved) return saved;
        
        // Check HTML lang attribute
        const htmlLang = document.documentElement.lang;
        if (htmlLang) return htmlLang;
        
        // Default to English
        return 'en';
    }

    /**
     * Load dynamic content from API
     */
    async loadContent(contentType, contentId) {
        // Check cache first
        const cacheKey = `${contentType}_${contentId}_${this.currentLang}`;
        if (this.cache[cacheKey]) {
            return this.cache[cacheKey];
        }

        try {
            const response = await fetch(`api/get-dynamic-content.php?type=${contentType}&id=${contentId}&lang=${this.currentLang}`);
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            const data = await response.json();

            if (!data.success) {
                throw new Error(data.error || 'Failed to load content');
            }

            // Cache the content
            this.cache[cacheKey] = data.content;
            return data.content;
        } catch (error) {
            console.error('Failed to load dynamic content:', error);
            return null;
        }
    }

    /**
     * Load all content for a page
     */
    async loadPageContent(contentType) {
        try {
            const response = await fetch(`api/get-dynamic-content.php?type=${contentType}&lang=${this.currentLang}`);
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            const data = await response.json();

            if (!data.success) {
                throw new Error(data.error || 'Failed to load page content');
            }

            return data.content || [];
        } catch (error) {
            console.error('Failed to load page content:', error);
            return [];
        }
    }

    /**
     * Render content to page
     */
    renderContent(content, selector) {
        const element = document.querySelector(selector);
        if (!element || !content) return;

        // Determine how to render based on content type
        if (typeof content === 'string') {
            element.textContent = content;
        } else if (typeof content === 'object') {
            // For complex objects, render as HTML
            element.innerHTML = this.buildContentHTML(content);
        }
    }

    /**
     * Build HTML for content object
     */
    buildContentHTML(content) {
        // This will be customized per page
        return JSON.stringify(content);
    }

    /**
     * Set language and reload content
     */
    async setLanguage(lang) {
        if (lang === this.currentLang) return;

        this.currentLang = lang;
        
        // Reload content for current page
        const contentType = this.detectContentType();
        if (contentType) {
            await this.reloadPageContent(contentType);
        }
    }

    /**
     * Detect content type from page
     */
    detectContentType() {
        const url = window.location.pathname;
        
        if (url.includes('blog')) return 'blog';
        if (url.includes('event')) return 'event';
        if (url.includes('press')) return 'press';
        if (url.includes('spaces') || url.includes('pricing')) return 'pricing';
        
        return null;
    }

    /**
     * Reload page content
     */
    async reloadPageContent(contentType) {
        const content = await this.loadPageContent(contentType);
        
        // Dispatch event for page-specific handlers
        document.dispatchEvent(new CustomEvent('lakum-content-loaded', {
            detail: { contentType, content, lang: this.currentLang }
        }));
    }

    /**
     * Initialize loader
     */
    async init() {
        // Listen for language changes
        this.setupLanguageListener();
        
        // Load initial content
        const contentType = this.detectContentType();
        if (contentType) {
            await this.reloadPageContent(contentType);
        }
    }

    /**
     * Setup listener for language changes
     */
    setupLanguageListener() {
        // Listen for custom language change events
        document.addEventListener('lakum-language-changed', (e) => {
            const lang = e.detail?.lang || this.currentLang;
            this.setLanguage(lang);
        });

        // Listen for storage changes (multi-tab sync)
        window.addEventListener('storage', (e) => {
            if (e.key === 'lakum_language' && e.newValue) {
                this.setLanguage(e.newValue);
            }
        });
    }
}

// Create global instance
window.dynamicContentLoader = new DynamicContentLoader();

// Initialize on page load
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.dynamicContentLoader.init();
    });
} else {
    window.dynamicContentLoader.init();
}
