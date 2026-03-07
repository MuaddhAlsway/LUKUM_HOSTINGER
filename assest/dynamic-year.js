/**
 * LAKUM Artspace - Dynamic Year Footer
 * Automatically updates the copyright year in the footer
 * Runs immediately and after DOM loads for maximum compatibility
 * 
 * EXPLANATION:
 * - new Date().getFullYear() returns the current year from the client's system clock
 * - This is future-proof: it will automatically update every January 1st
 * - No hardcoded years means no manual updates needed
 * - Runs in multiple ways to ensure it executes even if DOM is already loaded
 */

// Function to set the year
function setFooterYear() {
    const yearElement = document.getElementById('year');
    if (yearElement) {
        const currentYear = new Date().getFullYear();
        yearElement.textContent = currentYear;
    }
}

// Try immediately (in case DOM is already loaded)
if (document.readyState === 'loading') {
    // DOM is still loading, wait for DOMContentLoaded
    document.addEventListener('DOMContentLoaded', setFooterYear);
} else {
    // DOM is already loaded, run immediately
    setFooterYear();
}

// Also run on load event as a fallback
window.addEventListener('load', setFooterYear);
