// Event Form JavaScript

// Initialize gallery files BEFORE DOMContentLoaded to ensure it's available globally
window.galleryFiles = [];

// Wait for DOM to be ready before accessing elements
document.addEventListener('DOMContentLoaded', function() {

// Cover Image Upload
const coverPreview = document.getElementById('coverPreview');
const coverInput = document.getElementById('cover_image');
const coverImg = document.getElementById('coverImg');
const removeCoverBtn = document.getElementById('removeCover');

if (coverPreview && coverInput) {
    coverPreview.addEventListener('click', () => {
        coverInput.click();
    });

    coverInput.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                coverImg.src = e.target.result;
                coverImg.style.display = 'block';
                if (removeCoverBtn) {
                    removeCoverBtn.style.display = 'flex';
                }
                const placeholder = coverPreview.querySelector('.cover-placeholder');
                if (placeholder) {
                    placeholder.style.display = 'none';
                }
            };
            reader.readAsDataURL(file);
        }
    });

    if (removeCoverBtn) {
        removeCoverBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            coverInput.value = '';
            coverImg.style.display = 'none';
            coverImg.src = '';
            removeCoverBtn.style.display = 'none';
            const placeholder = coverPreview.querySelector('.cover-placeholder');
            if (placeholder) {
                placeholder.style.display = 'flex';
            }
            // Mark cover for removal
            const removeCoverField = document.getElementById('remove_cover');
            if (removeCoverField) {
                removeCoverField.value = '1';
            }
        });
    }
}

// Reset remove_cover flag when new image is selected
if (coverInput) {
    coverInput.addEventListener('change', () => {
        const removeCoverField = document.getElementById('remove_cover');
        if (removeCoverField) {
            removeCoverField.value = '0';
        }
    });
}

// Character Count
const descriptionTextarea_en = document.getElementById('description_en');
const charCount_en = document.getElementById('charCount_en');
const descriptionTextarea_ar = document.getElementById('description_ar');
const charCount_ar = document.getElementById('charCount_ar');

if (descriptionTextarea_en && charCount_en) {
    descriptionTextarea_en.addEventListener('input', () => {
        charCount_en.textContent = descriptionTextarea_en.value.length;
    });
}

if (descriptionTextarea_ar && charCount_ar) {
    descriptionTextarea_ar.addEventListener('input', () => {
        charCount_ar.textContent = descriptionTextarea_ar.value.length;
    });
}

// Date & Time Validation
const eventDate = document.getElementById('event_date');
const eventTime = document.getElementById('event_time');
const eventEndTime = document.getElementById('event_end_time');
const endDate = document.getElementById('end_date');
const endDateStartTime = document.getElementById('end_date_start_time');
const endTime = document.getElementById('end_time');
const dateValidation = document.getElementById('dateValidation');

// Allow any date (including past dates for historical events)
if (eventDate) {
    eventDate.addEventListener('change', () => {
        if (endDate) {
            // Set end date minimum to start date (end must be after start)
            endDate.setAttribute('min', eventDate.value);
        }
        validateDates();
    });
}

if (endDate) {
    endDate.addEventListener('change', validateDates);
}

if (eventTime) {
    eventTime.addEventListener('change', validateDates);
}

if (eventEndTime) {
    eventEndTime.addEventListener('change', validateDates);
}

if (endDateStartTime) {
    endDateStartTime.addEventListener('change', validateDates);
}

if (endTime) {
    endTime.addEventListener('change', validateDates);
}

function validateDates() {
    if (!eventDate || !dateValidation) return;

    const startDate = eventDate.value;
    const startTime = eventTime ? eventTime.value : '';
    const startEndTime = eventEndTime ? eventEndTime.value : '';
    const finishDate = endDate ? endDate.value : '';
    const finishStartTime = endDateStartTime ? endDateStartTime.value : '';
    const finishTime = endTime ? endTime.value : '';

    // Validate start date times (if both provided)
    if (startTime && startEndTime) {
        if (startEndTime <= startTime) {
            dateValidation.className = 'date-validation-msg error';
            dateValidation.innerHTML = '<i class="ri-error-warning-line"></i> Start date: End time must be after start time';
            return;
        }
    }

    // Check if end date is provided
    if (!finishDate) {
        // Single day event - show duration if times provided
        if (startTime && startEndTime) {
            const start = new Date('2000-01-01 ' + startTime);
            const end = new Date('2000-01-01 ' + startEndTime);
            const diffMs = end - start;
            const diffHours = Math.floor(diffMs / (1000 * 60 * 60));
            const diffMinutes = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));

            let durationText = '';
            if (diffHours > 0) {
                durationText = `${diffHours} hour${diffHours > 1 ? 's' : ''}`;
                if (diffMinutes > 0) {
                    durationText += ` and ${diffMinutes} minute${diffMinutes > 1 ? 's' : ''}`;
                }
            } else {
                durationText = `${diffMinutes} minute${diffMinutes > 1 ? 's' : ''}`;
            }

            dateValidation.className = 'date-validation-msg success';
            dateValidation.innerHTML = `<i class="ri-checkbox-circle-line"></i> Event duration: ${durationText}`;
        } else {
            dateValidation.className = 'date-validation-msg';
            dateValidation.textContent = '';
        }
        return;
    }

    // Check if end date is before start date
    if (finishDate < startDate) {
        dateValidation.className = 'date-validation-msg error';
        dateValidation.innerHTML = '<i class="ri-error-warning-line"></i> End date cannot be before start date';
        return;
    }

    // Validate end date times (if both provided)
    if (finishStartTime && finishTime) {
        if (finishTime <= finishStartTime) {
            dateValidation.className = 'date-validation-msg error';
            dateValidation.innerHTML = '<i class="ri-error-warning-line"></i> End date: End time must be after start time';
            return;
        }
    }

    // If same date, validate that it makes sense
    if (finishDate === startDate) {
        dateValidation.className = 'date-validation-msg error';
        dateValidation.innerHTML = '<i class="ri-error-warning-line"></i> For same-day events, leave end date empty and use start date times only';
        return;
    }

    // Calculate multi-day duration
    const start = new Date(startDate + ' ' + (startTime || '00:00'));
    const end = new Date(finishDate + ' ' + (finishTime || '23:59'));
    const diffMs = end - start;
    const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));
    const diffHours = Math.floor((diffMs % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));

    let durationText = '';
    if (diffDays > 0) {
        durationText = `${diffDays} day${diffDays > 1 ? 's' : ''}`;
        if (diffHours > 0) {
            durationText += ` and ${diffHours} hour${diffHours > 1 ? 's' : ''}`;
        }
    } else if (diffHours > 0) {
        durationText = `${diffHours} hour${diffHours > 1 ? 's' : ''}`;
    } else {
        const diffMinutes = Math.floor(diffMs / (1000 * 60));
        durationText = `${diffMinutes} minute${diffMinutes > 1 ? 's' : ''}`;
    }

    dateValidation.className = 'date-validation-msg success';
    dateValidation.innerHTML = `<i class="ri-checkbox-circle-line"></i> Multi-day event duration: ${durationText}`;
}

// Gallery Images Upload
const galleryUploadArea = document.getElementById('galleryUploadArea');
const galleryInput = document.getElementById('event_images');
const galleryPreview = document.getElementById('galleryPreview');

console.log('=== GALLERY INITIALIZATION ===');
console.log('galleryUploadArea found:', !!galleryUploadArea);
console.log('galleryInput found:', !!galleryInput);
console.log('galleryPreview found:', !!galleryPreview);

if (galleryUploadArea && galleryInput) {
    console.log('Gallery upload area initialized - attaching event listeners');
    
    galleryUploadArea.addEventListener('click', () => {
        console.log('Gallery upload area clicked');
        galleryInput.click();
    });

    galleryInput.addEventListener('change', (e) => {
        console.log('Gallery input change event fired');
        console.log('Files selected:', e.target.files.length);
        const files = Array.from(e.target.files);
        files.forEach(file => {
            console.log('Processing file:', file.name, 'size:', file.size, 'type:', file.type);
            if (!window.galleryFiles.find(f => f.name === file.name && f.size === file.size)) {
                console.log('Adding file to window.galleryFiles:', file.name);
                window.galleryFiles.push(file);
            } else {
                console.log('File already exists, skipping:', file.name);
            }
        });
        console.log('Total files in window.galleryFiles:', window.galleryFiles.length);
        updateGalleryPreview();
    });

    // Drag and drop
    galleryUploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        galleryUploadArea.style.borderColor = '#000';
        galleryUploadArea.style.background = '#e9ecef';
    });

    galleryUploadArea.addEventListener('dragleave', () => {
        galleryUploadArea.style.borderColor = '#dee2e6';
        galleryUploadArea.style.background = '#f8f9fa';
    });

    galleryUploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        console.log('Drop event fired');
        galleryUploadArea.style.borderColor = '#dee2e6';
        galleryUploadArea.style.background = '#f8f9fa';

        const files = Array.from(e.dataTransfer.files).filter(file => file.type.startsWith('image/'));
        console.log('Files dropped:', files.length);
        files.forEach(file => {
            console.log('Processing dropped file:', file.name);
            if (!window.galleryFiles.find(f => f.name === file.name && f.size === file.size)) {
                console.log('Adding dropped file to window.galleryFiles:', file.name);
                window.galleryFiles.push(file);
            }
        });
        console.log('Total files in window.galleryFiles after drop:', window.galleryFiles.length);
        updateGalleryPreview();
    });
} else {
    console.error('❌ Gallery elements NOT found!');
    console.error('galleryUploadArea:', galleryUploadArea);
    console.error('galleryInput:', galleryInput);
    console.error('galleryPreview:', galleryPreview);
}

function updateGalleryPreview() {
    if (!galleryPreview) return;

    galleryPreview.innerHTML = '';

    window.galleryFiles.forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = (e) => {
            const div = document.createElement('div');
            div.className = 'gallery-item-preview';
            div.innerHTML = `
                <img src="${e.target.result}" alt="Gallery">
                <button type="button" class="remove-gallery-item" data-index="${index}">
                    <i class="ri-close-line"></i>
                </button>
            `;
            galleryPreview.appendChild(div);

            // Add remove functionality
            div.querySelector('.remove-gallery-item').addEventListener('click', () => {
                removeGalleryItem(index);
            });
        };
        reader.readAsDataURL(file);
    });

    // Update file input
    updateFileInput();
}

function removeGalleryItem(index) {
    window.galleryFiles.splice(index, 1);
    updateGalleryPreview();
}

function updateFileInput() {
    if (!galleryInput) return;

    const dataTransfer = new DataTransfer();
    window.galleryFiles.forEach(file => {
        dataTransfer.items.add(file);
    });
    galleryInput.files = dataTransfer.files;
}

// Form Submission Validation
const eventForm = document.getElementById('eventForm');
if (eventForm) {
    eventForm.addEventListener('submit', (e) => {
        // Check date validation
        if (dateValidation && dateValidation.classList.contains('error')) {
            e.preventDefault();
            alert('Please fix the date/time errors before submitting');
            return false;
        }

        // Show loading state
        const submitBtn = document.getElementById('submitBtn');
        if (submitBtn) {
            submitBtn.innerHTML = '<i class="ri-loader-4-line"></i> Creating Event...';
            submitBtn.disabled = true;
        }
    });
}

// Initialize character count if editing
if (descriptionTextarea_en && charCount_en) {
    charCount_en.textContent = descriptionTextarea_en.value.length;
}
if (descriptionTextarea_ar && charCount_ar) {
    charCount_ar.textContent = descriptionTextarea_ar.value.length;
}

// Initialize date validation if editing
if (eventDate && eventDate.value) {
    validateDates();
}

// ===== 12-HOUR TIME FORMAT DISPLAY =====
// Add helper text to show 12-hour format below time inputs
function add12HourFormatHelper() {
    const timeInputs = document.querySelectorAll('input[type="time"]');

    timeInputs.forEach(input => {
        // Create helper text element
        const helper = document.createElement('small');
        helper.style.display = 'block';
        helper.style.marginTop = '4px';
        helper.style.color = '#6c757d';
        helper.style.fontSize = '0.875rem';

        // Function to update helper text
        function updateHelper() {
            if (input.value) {
                const [hours, minutes] = input.value.split(':');
                const hour = parseInt(hours);
                const ampm = hour >= 12 ? 'PM' : 'AM';
                const displayHour = hour % 12 || 12;
                helper.textContent = `${displayHour}:${minutes} ${ampm}`;
            } else {
                helper.textContent = 'Format: 12-hour (AM/PM)';
            }
        }

        // Add event listener
        input.addEventListener('change', updateHelper);
        input.addEventListener('input', updateHelper);

        // Insert helper after input
        if (input.parentNode) {
            input.parentNode.insertBefore(helper, input.nextSibling);
        }

        // Initial update
        updateHelper();
    });
}

// Initialize on page load
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', add12HourFormatHelper);
} else {
    add12HourFormatHelper();
}

// ===== FORM RESET ON PAGE SHOW =====
// Ensures form fields are properly reset when navigating back to the page
window.addEventListener('pageshow', function(event) {
    // Check if page was loaded from cache (back/forward navigation)
    if (event.persisted || (window.performance && window.performance.navigation.type === 2)) {
        // Force reload to get fresh data
        window.location.reload();
    }
});

// Alternative: Reset form fields on page load
window.addEventListener('load', function() {
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        // Don't reset if form has data-no-reset attribute
        if (!form.hasAttribute('data-no-reset')) {
            // Store original values
            const inputs = form.querySelectorAll('input, textarea, select');
            inputs.forEach(input => {
                if (input.type !== 'hidden' && input.type !== 'submit' && input.type !== 'button') {
                    // Store the server-provided value as the default
                    if (!input.hasAttribute('data-original-value')) {
                        input.setAttribute('data-original-value', input.value || '');
                    }
                }
            });
        }
    });
});

}); // Close DOMContentLoaded
