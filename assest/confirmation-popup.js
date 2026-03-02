/**
 * Generic Confirmation Popup
 * Shows a styled confirmation dialog for various actions
 */

function showConfirmation(options = {}) {
    const {
        title = 'Confirm Action',
        message = 'Are you sure?',
        submessage = '',
        confirmText = 'Confirm',
        cancelText = 'Cancel',
        type = 'danger', // 'danger', 'warning', 'info'
        onConfirm = null,
        onCancel = null
    } = options;

    // Get current language
    const isArabic = document.documentElement.lang === 'ar' || document.documentElement.dir === 'rtl';
    
    // Default translations
    const defaultCancelText = isArabic ? 'إلغاء' : 'Cancel';
    const defaultConfirmText = isArabic ? 'تأكيد' : 'Confirm';

    // Create overlay
    const overlay = document.createElement('div');
    overlay.className = 'confirmation-overlay';
    overlay.id = 'confirmationOverlay';
    
    // Create popup
    const popup = document.createElement('div');
    popup.className = 'confirmation-popup';
    
    // Icon mapping
    const iconMap = {
        danger: 'ri-alert-line',
        warning: 'ri-alert-fill',
        info: 'ri-information-line'
    };
    
    const icon = iconMap[type] || iconMap.danger;
    
    // Popup content
    popup.innerHTML = `
        <div class="confirmation-icon ${type}">
            <i class="${icon}"></i>
        </div>
        <h2 class="confirmation-title">${escapeHtml(title)}</h2>
        <p class="confirmation-message">${escapeHtml(message)}</p>
        ${submessage ? `<p class="confirmation-submessage">${escapeHtml(submessage)}</p>` : ''}
        <div class="confirmation-actions">
            <button class="confirmation-btn confirmation-btn-cancel" onclick="closeConfirmation()">
                ${escapeHtml(cancelText || defaultCancelText)}
            </button>
            <button class="confirmation-btn confirmation-btn-confirm ${type}" onclick="confirmAction()">
                ${escapeHtml(confirmText || defaultConfirmText)}
            </button>
        </div>
    `;
    
    overlay.appendChild(popup);
    document.body.appendChild(overlay);
    
    // Store callbacks
    window._confirmationCallbacks = {
        onConfirm: onConfirm,
        onCancel: onCancel
    };
    
    // Close on overlay click
    overlay.addEventListener('click', function(e) {
        if (e.target === overlay) {
            closeConfirmation();
        }
    });
    
    // Close on Escape key
    const escapeHandler = function(e) {
        if (e.key === 'Escape') {
            closeConfirmation();
        }
    };
    document.addEventListener('keydown', escapeHandler);
    
    // Store escape handler for cleanup
    window._confirmationEscapeHandler = escapeHandler;
}

function closeConfirmation() {
    const overlay = document.getElementById('confirmationOverlay');
    if (overlay) {
        overlay.classList.add('removing');
        
        // Call cancel callback
        if (window._confirmationCallbacks && window._confirmationCallbacks.onCancel) {
            window._confirmationCallbacks.onCancel();
        }
        
        setTimeout(() => {
            overlay.remove();
            // Remove escape handler
            if (window._confirmationEscapeHandler) {
                document.removeEventListener('keydown', window._confirmationEscapeHandler);
            }
        }, 300);
    }
}

function confirmAction() {
    const overlay = document.getElementById('confirmationOverlay');
    if (overlay) {
        overlay.classList.add('removing');
        
        // Call confirm callback
        if (window._confirmationCallbacks && window._confirmationCallbacks.onConfirm) {
            window._confirmationCallbacks.onConfirm();
        }
        
        setTimeout(() => {
            overlay.remove();
            // Remove escape handler
            if (window._confirmationEscapeHandler) {
                document.removeEventListener('keydown', window._confirmationEscapeHandler);
            }
        }, 300);
    }
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Specific confirmation functions

function showDeleteAllEventsConfirmation(callback) {
    const isArabic = document.documentElement.lang === 'ar' || document.documentElement.dir === 'rtl';
    
    showConfirmation({
        title: isArabic ? 'حذف جميع الأحداث' : 'Delete All Events',
        message: isArabic ? 'هل أنت متأكد من رغبتك في حذف جميع الأحداث؟' : 'Are you sure you want to delete ALL events?',
        submessage: isArabic ? 'لا يمكن التراجع عن هذا الإجراء!' : 'This cannot be undone!',
        confirmText: isArabic ? 'حذف الكل' : 'Delete All',
        cancelText: isArabic ? 'إلغاء' : 'Cancel',
        type: 'danger',
        onConfirm: callback
    });
}

function showDeleteAllBlogsConfirmation(callback) {
    const isArabic = document.documentElement.lang === 'ar' || document.documentElement.dir === 'rtl';
    
    showConfirmation({
        title: isArabic ? 'حذف جميع المقالات' : 'Delete All Blogs',
        message: isArabic ? 'هل أنت متأكد من رغبتك في حذف جميع المقالات؟' : 'Are you sure you want to delete ALL blogs?',
        submessage: isArabic ? 'لا يمكن التراجع عن هذا الإجراء!' : 'This cannot be undone!',
        confirmText: isArabic ? 'حذف الكل' : 'Delete All',
        cancelText: isArabic ? 'إلغاء' : 'Cancel',
        type: 'danger',
        onConfirm: callback
    });
}

function showDeleteAllPricingConfirmation(callback) {
    const isArabic = document.documentElement.lang === 'ar' || document.documentElement.dir === 'rtl';
    
    showConfirmation({
        title: isArabic ? 'حذف جميع الأسعار' : 'Delete All Pricing',
        message: isArabic ? 'هل أنت متأكد من رغبتك في حذف جميع الأسعار؟' : 'Are you sure you want to delete ALL pricing?',
        submessage: isArabic ? 'لا يمكن التراجع عن هذا الإجراء!' : 'This cannot be undone!',
        confirmText: isArabic ? 'حذف الكل' : 'Delete All',
        cancelText: isArabic ? 'إلغاء' : 'Cancel',
        type: 'danger',
        onConfirm: callback
    });
}

function showDeleteAllPressConfirmation(callback) {
    const isArabic = document.documentElement.lang === 'ar' || document.documentElement.dir === 'rtl';
    
    showConfirmation({
        title: isArabic ? 'حذف جميع الأخبار' : 'Delete All Press',
        message: isArabic ? 'هل أنت متأكد من رغبتك في حذف جميع الأخبار؟' : 'Are you sure you want to delete ALL press?',
        submessage: isArabic ? 'لا يمكن التراجع عن هذا الإجراء!' : 'This cannot be undone!',
        confirmText: isArabic ? 'حذف الكل' : 'Delete All',
        cancelText: isArabic ? 'إلغاء' : 'Cancel',
        type: 'danger',
        onConfirm: callback
    });
}

// Individual item deletion confirmations

function showDeleteEventConfirmation(callback) {
    const isArabic = document.documentElement.lang === 'ar' || document.documentElement.dir === 'rtl';
    
    showConfirmation({
        title: isArabic ? 'حذف الحدث' : 'Delete Event',
        message: isArabic ? 'هل أنت متأكد من رغبتك في حذف هذا الحدث؟' : 'Are you sure you want to delete this event?',
        submessage: isArabic ? 'لا يمكن التراجع عن هذا الإجراء!' : 'This action cannot be undone.',
        confirmText: isArabic ? 'حذف' : 'Delete',
        cancelText: isArabic ? 'إلغاء' : 'Cancel',
        type: 'danger',
        onConfirm: callback
    });
}

function showDeleteBlogConfirmation(callback) {
    const isArabic = document.documentElement.lang === 'ar' || document.documentElement.dir === 'rtl';
    
    showConfirmation({
        title: isArabic ? 'حذف المقالة' : 'Delete Blog',
        message: isArabic ? 'هل أنت متأكد من رغبتك في حذف هذه المقالة؟' : 'Are you sure you want to delete this blog?',
        submessage: isArabic ? 'لا يمكن التراجع عن هذا الإجراء!' : 'This action cannot be undone.',
        confirmText: isArabic ? 'حذف' : 'Delete',
        cancelText: isArabic ? 'إلغاء' : 'Cancel',
        type: 'danger',
        onConfirm: callback
    });
}

function showDeletePressConfirmation(callback) {
    const isArabic = document.documentElement.lang === 'ar' || document.documentElement.dir === 'rtl';
    
    showConfirmation({
        title: isArabic ? 'حذف الخبر' : 'Delete Press',
        message: isArabic ? 'هل أنت متأكد من رغبتك في حذف هذا الخبر؟' : 'Are you sure you want to delete this press?',
        submessage: isArabic ? 'لا يمكن التراجع عن هذا الإجراء!' : 'This action cannot be undone.',
        confirmText: isArabic ? 'حذف' : 'Delete',
        cancelText: isArabic ? 'إلغاء' : 'Cancel',
        type: 'danger',
        onConfirm: callback
    });
}
