// Deferred API Loader - Loads API data after LCP
// Prevents API calls from blocking initial page render
(function() {
  'use strict';
  
  // Store API loading functions
  window.LAKUM_API = window.LAKUM_API || {};
  
  // Defer API calls until page is fully loaded
  function deferAPILoad() {
    // Only load if functions exist (from main page script)
    if (typeof window.loadFeaturedEvent === 'function') {
      window.loadFeaturedEvent();
    }
    if (typeof window.loadUpcomingEvents === 'function') {
      window.loadUpcomingEvents();
    }
    if (typeof window.loadPreviousEvents === 'function') {
      window.loadPreviousEvents();
    }
  }
  
  // Wait for page load event
  if (document.readyState === 'complete') {
    // Page already loaded
    setTimeout(deferAPILoad, 100);
  } else {
    // Wait for load event
    window.addEventListener('load', function() {
      setTimeout(deferAPILoad, 100);
    });
  }
})();
