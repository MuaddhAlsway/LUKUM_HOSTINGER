/**
 * Form Reset Utility
 * Prevents form reset on certain elements with data-no-reset attribute
 */

document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form[data-no-reset]');
    
    forms.forEach(form => {
        // Store original form values
        const originalValues = new FormData(form);
        
        // Prevent accidental resets
        form.addEventListener('reset', function(e) {
            if (!confirm('Are you sure you want to reset the form?')) {
                e.preventDefault();
            }
        });
    });
});
