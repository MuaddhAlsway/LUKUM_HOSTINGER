/**
 * LAKUM Artspace - Settings Links Loader
 * Dynamically loads booking and shop links from settings
 */

class SettingsLinksLoader {
    constructor() {
        this.bookingLink = null;
        this.shopLink = null;
        this.init();
    }

    /**
     * Initialize and load settings
     */
    async init() {
        try {
            await this.loadSettings();
            this.updateLinks();
        } catch (error) {
            console.error('Error loading settings links:', error);
        }
    }

    /**
     * Load settings from API
     */
    async loadSettings() {
        try {
            const response = await fetch('api/get_settings.php');
            const result = await response.json();

            if (result.success && result.data) {
                this.bookingLink = result.data.booking_link;
                this.shopLink = result.data.shop_link;
            }
        } catch (error) {
            console.error('Error fetching settings:', error);
        }
    }

    /**
     * Update all links on the page
     */
    updateLinks() {
        // Update booking links
        if (this.bookingLink) {
            const bookingLinks = document.querySelectorAll('[data-link-type="booking"]');
            bookingLinks.forEach(link => {
                link.href = this.bookingLink;
            });
        }

        // Update shop links
        if (this.shopLink) {
            const shopLinks = document.querySelectorAll('[data-link-type="shop"]');
            shopLinks.forEach(link => {
                link.href = this.shopLink;
            });
        }
    }

    /**
     * Get booking link
     */
    getBookingLink() {
        return this.bookingLink;
    }

    /**
     * Get shop link
     */
    getShopLink() {
        return this.shopLink;
    }
}

// Create global instance
window.settingsLinksLoader = new SettingsLinksLoader();
