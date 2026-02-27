/**
 * Popup Notification System
 * Replaces all alert() calls with styled popup messages
 */

function showPopup(message, type = 'info') {
    // Remove existing popup if any
    const existingPopup = document.getElementById('popupNotification');
    if (existingPopup) {
        existingPopup.remove();
    }

    // Create popup container
    const popup = document.createElement('div');
    popup.id = 'popupNotification';
    popup.className = `popup-notification popup-${type}`;
    
    // Create popup content
    popup.innerHTML = `
        <div class="popup-content">
            <div class="popup-message">${escapeHtml(message)}</div>
            <button class="popup-close" onclick="this.parentElement.parentElement.remove()">×</button>
        </div>
    `;
    
    document.body.appendChild(popup);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (popup.parentElement) {
            popup.remove();
        }
    }, 5000);
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Override native alert
window.alert = function(message) {
    showPopup(message, 'info');
};
