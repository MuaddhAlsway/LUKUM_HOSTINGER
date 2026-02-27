/**
 * LAKUM Admin Panel - Main JavaScript
 * Handles common admin functionality
 */

// Configuration
const API_BASE = '../api/';
const ADMIN_NAME_KEY = 'admin_name';

// Initialize admin panel
document.addEventListener('DOMContentLoaded', () => {
    checkAuthentication();
    setupEventListeners();
    loadAdminInfo();
});

/**
 * Check if user is authenticated
 */
async function checkAuthentication() {
    try {
        const response = await fetch(API_BASE + 'auth.php?action=check');
        const data = await response.json();

        if (!data.success) {
            window.location.href = 'login.html';
        }
    } catch (error) {
        console.error('Authentication check failed:', error);
        window.location.href = 'login.html';
    }
}

/**
 * Load admin information
 */
async function loadAdminInfo() {
    try {
        const response = await fetch(API_BASE + 'auth.php?action=check');
        const data = await response.json();

        if (data.success && data.admin) {
            const adminNameElement = document.getElementById('adminName');
            if (adminNameElement) {
                adminNameElement.textContent = data.admin.name;
            }
            localStorage.setItem(ADMIN_NAME_KEY, data.admin.name);
        }
    } catch (error) {
        console.error('Error loading admin info:', error);
    }
}

/**
 * Setup event listeners
 */
function setupEventListeners() {
    // Logout button
    const logoutBtn = document.getElementById('logoutBtn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', logout);
    }

    // Sidebar toggle
    const sidebarToggle = document.querySelector('.sidebar-toggle');
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', toggleSidebar);
    }

    // Profile button
    const profileBtn = document.getElementById('profileBtn');
    if (profileBtn) {
        profileBtn.addEventListener('click', showProfileMenu);
    }
}

/**
 * Logout user
 */
async function logout() {
    if (!confirm('Are you sure you want to logout?')) {
        return;
    }

    try {
        const response = await fetch(API_BASE + 'auth.php?action=logout', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            }
        });

        const data = await response.json();

        if (data.success) {
            window.location.href = 'login.html';
        }
    } catch (error) {
        console.error('Logout error:', error);
        window.location.href = 'login.html';
    }
}

/**
 * Toggle sidebar on mobile
 */
function toggleSidebar() {
    const sidebar = document.querySelector('.sidebar');
    if (sidebar) {
        sidebar.classList.toggle('active');
    }
}

/**
 * Show profile menu
 */
function showProfileMenu() {
    alert('Profile menu - Coming soon!');
}

/**
 * Format date
 */
function formatDate(dateString) {
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('en-US', options);
}

/**
 * Format time
 */
function formatTime(timeString) {
    if (!timeString) return '-';
    const [hours, minutes] = timeString.split(':');
    return `${hours}:${minutes}`;
}

/**
 * Show notification
 */
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        background: ${type === 'success' ? '#48bb78' : type === 'error' ? '#f56565' : '#4299e1'};
        color: white;
        border-radius: 5px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        z-index: 9999;
        animation: slideIn 0.3s ease;
    `;

    document.body.appendChild(notification);

    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

/**
 * Show confirmation dialog
 */
function showConfirmation(message) {
    return confirm(message);
}

/**
 * Validate email
 */
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

/**
 * Validate URL
 */
function validateURL(url) {
    try {
        new URL(url);
        return true;
    } catch (error) {
        return false;
    }
}

/**
 * Sanitize HTML
 */
function sanitizeHTML(html) {
    const div = document.createElement('div');
    div.textContent = html;
    return div.innerHTML;
}

/**
 * Get query parameter
 */
function getQueryParam(param) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(param);
}

/**
 * Upload file
 */
async function uploadFile(file, type = 'general') {
    const formData = new FormData();
    formData.append('file', file);
    formData.append('type', type);

    try {
        const response = await fetch(API_BASE + 'upload.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            return data.data;
        } else {
            throw new Error(data.message);
        }
    } catch (error) {
        console.error('Upload error:', error);
        throw error;
    }
}

/**
 * Delete item with confirmation
 */
async function deleteItem(type, id, name) {
    if (!showConfirmation(`Are you sure you want to delete "${name}"?`)) {
        return false;
    }

    try {
        const endpoint = `${API_BASE}delete_${type}.php`;
        const response = await fetch(endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id: id })
        });

        const data = await response.json();

        if (data.success) {
            showNotification(`${type} deleted successfully`, 'success');
            return true;
        } else {
            showNotification(data.message || 'Failed to delete', 'error');
            return false;
        }
    } catch (error) {
        console.error('Delete error:', error);
        showNotification('Error deleting item', 'error');
        return false;
    }
}

/**
 * Add CSS animations
 */
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }

    @keyframes fadeOut {
        from {
            opacity: 1;
        }
        to {
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

// Export functions for use in other scripts
window.adminPanel = {
    checkAuthentication,
    logout,
    showNotification,
    showConfirmation,
    validateEmail,
    validateURL,
    sanitizeHTML,
    getQueryParam,
    uploadFile,
    deleteItem,
    formatDate,
    formatTime
};
