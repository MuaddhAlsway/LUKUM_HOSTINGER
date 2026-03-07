/**
 * LAKUM Artspace - Dynamic Year Footer
 * Automatically updates the copyright year in the footer
 * Runs after DOM is fully loaded
 */

document.addEventListener('DOMContentLoaded', function() {
    const yearElement = document.getElementById('year');
    if (yearElement) {
        yearElement.textContent = new Date().getFullYear();
    }
});
