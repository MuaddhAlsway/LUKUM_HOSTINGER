/**
 * Logout Confirmation Popup
 * Shows a styled confirmation dialog before logging out
 */

function showLogoutConfirmation() {
    // Create overlay
    const overlay = document.createElement('div');
    overlay.className = 'logout-confirmation-overlay';
    overlay.id = 'logoutConfirmationOverlay';
    
    // Create popup
    const popup = document.createElement('div');
    popup.className = 'logout-confirmation-popup';
    
    // Get current language
    const isArabic = document.documentElement.lang === 'ar' || document.documentElement.dir === 'rtl';
    
    // Popup content
    const title = isArabic ? 'تسجيل الخروج' : 'Logout';
    const message = isArabic ? 'هل أنت متأكد من رغبتك في تسجيل الخروج؟' : 'Are you sure you want to logout?';
    const cancelBtn = isArabic ? 'إلغاء' : 'Cancel';
    const logoutBtn = isArabic ? 'تسجيل الخروج' : 'Logout';
    
    popup.innerHTML = `
        <div class="logout-confirmation-icon">
            <i class="ri-logout-box-line"></i>
        </div>
        <h2 class="logout-confirmation-title">${title}</h2>
        <p class="logout-confirmation-message">${message}</p>
        <div class="logout-confirmation-actions">
            <button class="logout-confirmation-btn logout-confirmation-btn-cancel" onclick="closeLogoutConfirmation()">
                ${cancelBtn}
            </button>
            <button class="logout-confirmation-btn logout-confirmation-btn-logout" onclick="confirmLogout()">
                ${logoutBtn}
            </button>
        </div>
    `;
    
    overlay.appendChild(popup);
    document.body.appendChild(overlay);
    
    // Close on overlay click
    overlay.addEventListener('click', function(e) {
        if (e.target === overlay) {
            closeLogoutConfirmation();
        }
    });
    
    // Close on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeLogoutConfirmation();
        }
    });
}

function closeLogoutConfirmation() {
    const overlay = document.getElementById('logoutConfirmationOverlay');
    if (overlay) {
        overlay.classList.add('removing');
        setTimeout(() => {
            overlay.remove();
        }, 300);
    }
}

function confirmLogout() {
    // Clear session storage
    sessionStorage.removeItem('admin_logged_in');
    sessionStorage.removeItem('admin_email');
    sessionStorage.removeItem('admin_name');
    sessionStorage.removeItem('admin_login_time');
    
    // Redirect to login
    window.location.href = 'login.html';
}

// Make logout function use the new popup
function logout() {
    showLogoutConfirmation();
}
