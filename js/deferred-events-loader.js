/**
 * Deferred Events Loader
 * Loads events AFTER first paint to avoid blocking LCP
 * Replaces synchronous API calls with deferred loading
 */

(function() {
    'use strict';

    class DeferredEventsLoader {
        constructor() {
            this.cache = new Map();
            this.isLoading = false;
        }

        /**
         * Load events after first paint
         */
        loadAfterPaint() {
            if (this.isLoading) return;
            this.isLoading = true;

            // Use requestIdleCallback if available (best for performance)
            if ('requestIdleCallback' in window) {
                requestIdleCallback(() => {
                    this.fetchAndRenderEvents();
                }, { timeout: 2000 });
            } else {
                // Fallback: load after DOMContentLoaded
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', () => {
                        setTimeout(() => this.fetchAndRenderEvents(), 100);
                    });
                } else {
                    setTimeout(() => this.fetchAndRenderEvents(), 100);
                }
            }
        }

        /**
         * Fetch events from API
         */
        async fetchAndRenderEvents() {
            try {
                const lang = window.LAKUM_LANG || 'en';
                const url = `api/get_events.php?type=all&limit=1000&lang=${lang}&t=${Date.now()}`;

                const response = await fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) throw new Error(`HTTP ${response.status}`);

                const data = await response.json();

                if (data.success && data.data) {
                    // Render events
                    this.renderUpcomingEvents(data.data);
                    this.renderRecentEvents(data.data);
                    console.log('✓ Events loaded and rendered');
                }
            } catch (error) {
                console.error('✗ Error loading events:', error);
            }
        }

        /**
         * Render upcoming events
         */
        renderUpcomingEvents(events) {
            const container = document.getElementById('nextTwoEvents');
            if (!container) return;

            // Get next 2 upcoming events
            const upcoming = events.filter(e => new Date(e.event_date) > new Date()).slice(0, 2);

            if (upcoming.length === 0) {
                container.innerHTML = '<p>No upcoming events</p>';
                return;
            }

            container.innerHTML = upcoming.map(event => this.createEventCard(event)).join('');
        }

        /**
         * Render recent events
         */
        renderRecentEvents(events) {
            const container = document.getElementById('recentEvents');
            if (!container) return;

            // Get past events (reverse chronological)
            const recent = events.filter(e => new Date(e.event_date) <= new Date()).reverse().slice(0, 3);

            if (recent.length === 0) {
                container.innerHTML = '<p>No past events</p>';
                return;
            }

            container.innerHTML = recent.map(event => this.createEventCard(event)).join('');
        }

        /**
         * Create event card HTML
         */
        createEventCard(event) {
            const eventDate = new Date(event.event_date);
            const month = eventDate.toLocaleString('en-US', { month: 'short' }).toUpperCase();
            const day = eventDate.getDate();
            const title = event.title || 'Untitled Event';
            const time = event.event_time || 'TBD';
            const image = event.cover_image || 'heroImage/img-4.webp';

            return `
                <div class="lakum-event-card">
                    <div class="lakum-event-card__image">
                        <img src="${image}" alt="${title}" loading="lazy" decoding="async" width="768" height="512" style="width: 100%; height: 100%; object-fit: cover;">
                        <div class="lakum-event-card__date">
                            <span class="lakum-event-card__date-month">${month}</span>
                            <span class="lakum-event-card__date-day">${day}</span>
                        </div>
                    </div>
                    <div class="lakum-event-card__content">
                        <h3 class="lakum-event-card__title">${title}</h3>
                        <p class="lakum-event-card__time"><i class="ri-clock-line"></i> ${time}</p>
                        <a href="event.php?id=${event.id}" class="lakum-event-card__link">View Details →</a>
                    </div>
                </div>
            `;
        }
    }

    // Initialize and load events after first paint
    window.deferredEventsLoader = new DeferredEventsLoader();
    
    // Safe load function with error handling
    window.deferredEventsLoader.safeLoad = function() {
        if (this.loadAfterPaint) {
            this.loadAfterPaint();
        }
    };
    
    // Start loading when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            if (window.deferredEventsLoader?.safeLoad) {
                window.deferredEventsLoader.safeLoad();
            }
        });
    } else {
        if (window.deferredEventsLoader?.safeLoad) {
            window.deferredEventsLoader.safeLoad();
        }
    }

    console.log('✓ Deferred Events Loader initialized');
})();
