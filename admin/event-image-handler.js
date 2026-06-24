/**
 * Event Image Handler
 * Manages cover image and gallery image uploads for events
 */

// Configuration
const IMAGE_CONFIG = {
    maxSize: 5 * 1024 * 1024, // 5MB
    allowedExtensions: ['jpg', 'jpeg', 'png', 'gif', 'webp', 'heic', 'heif'],
    allowedMimeTypes: ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/heic', 'image/heif']
};

// Global gallery files storage
window.galleryFiles = [];

// Initialize image handlers when DOM is ready
document.addEventListener('DOMContentLoaded', initializeImageHandlers);

function initializeImageHandlers() {
    // Cover image handler
    const coverPreview = document.getElementById('coverPreview');
    const coverInput = document.getElementById('cover_image');
    const coverImg = document.getElementById('coverImg');
    const removeCover = document.getElementById('removeCover');
    
    if (coverPreview && coverInput) {
        // Click to upload
        coverPreview.addEventListener('click', () => {
            console.log('Cover preview clicked');
            coverInput.click();
        });
        
        // File selection
        coverInput.addEventListener('change', handleCoverImageSelect);
        
        // Remove button
        if (removeCover) {
            removeCover.addEventListener('click', (e) => {
                e.stopPropagation();
                removeCoverImage();
            });
        }
        
        // Drag and drop
        coverPreview.addEventListener('dragover', (e) => {
            e.preventDefault();
            coverPreview.style.backgroundColor = '#f0f0f0';
        });
        
        coverPreview.addEventListener('dragleave', () => {
            coverPreview.style.backgroundColor = '';
        });
        
        coverPreview.addEventListener('drop', (e) => {
            e.preventDefault();
            coverPreview.style.backgroundColor = '';
            if (e.dataTransfer.files.length > 0) {
                const file = e.dataTransfer.files[0];
                coverInput.files = e.dataTransfer.files;
                handleCoverImageSelect();
            }
        });
    }
    
    // Gallery images handler
    const galleryUploadArea = document.getElementById('galleryUploadArea');
    const galleryInput = document.getElementById('event_images');
    
    if (galleryUploadArea && galleryInput) {
        // Click to upload
        galleryUploadArea.addEventListener('click', () => {
            console.log('Gallery upload area clicked');
            galleryInput.click();
        });
        
        // File selection
        galleryInput.addEventListener('change', handleGalleryImageSelect);
        
        // Drag and drop
        galleryUploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            galleryUploadArea.style.backgroundColor = '#f0f0f0';
        });
        
        galleryUploadArea.addEventListener('dragleave', () => {
            galleryUploadArea.style.backgroundColor = '';
        });
        
        galleryUploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            galleryUploadArea.style.backgroundColor = '';
            if (e.dataTransfer.files.length > 0) {
                galleryInput.files = e.dataTransfer.files;
                handleGalleryImageSelect();
            }
        });
    }
}

function handleCoverImageSelect() {
    const coverInput = document.getElementById('cover_image');
    const file = coverInput.files[0];
    
    if (!file) {
        console.log('No file selected for cover');
        return;
    }
    
    console.log('Cover image selected:', file.name);
    
    // Validate file
    if (!validateImageFile(file)) {
        showPopup('Invalid image file. Please use JPG, PNG, GIF, or WebP', 'error');
        coverInput.value = '';
        return;
    }
    
    // Show preview
    const reader = new FileReader();
    reader.onload = (e) => {
        const coverImg = document.getElementById('coverImg');
        const coverPlaceholder = document.querySelector('.cover-placeholder');
        const removeCover = document.getElementById('removeCover');
        
        coverImg.src = e.target.result;
        coverImg.style.display = 'block';
        if (coverPlaceholder) coverPlaceholder.style.display = 'none';
        if (removeCover) removeCover.style.display = 'block';
        
        console.log('Cover preview displayed');
    };
    reader.readAsDataURL(file);
}

function removeCoverImage() {
    const coverInput = document.getElementById('cover_image');
    const coverImg = document.getElementById('coverImg');
    const coverPlaceholder = document.querySelector('.cover-placeholder');
    const removeCover = document.getElementById('removeCover');
    
    coverInput.value = '';
    coverImg.style.display = 'none';
    if (coverPlaceholder) coverPlaceholder.style.display = 'block';
    if (removeCover) removeCover.style.display = 'none';
    
    console.log('Cover image removed');
}

function handleGalleryImageSelect() {
    const galleryInput = document.getElementById('event_images');
    const files = galleryInput.files;
    
    if (!files.length) {
        console.log('No gallery files selected');
        return;
    }
    
    console.log('Gallery files selected:', files.length);
    
    // Validate and store files
    window.galleryFiles = [];
    for (let i = 0; i < files.length; i++) {
        const file = files[i];
        
        if (!validateImageFile(file)) {
            console.warn('Invalid file skipped:', file.name);
            continue;
        }
        
        window.galleryFiles.push(file);
    }
    
    console.log('Valid gallery files stored:', window.galleryFiles.length);
    
    if (window.galleryFiles.length === 0) {
        showPopup('No valid image files selected', 'error');
        return;
    }
    
    // Display preview
    displayGalleryPreview();
}

function displayGalleryPreview() {
    const galleryPreview = document.getElementById('galleryPreview');
    
    if (!galleryPreview) return;
    
    // Clear existing preview
    galleryPreview.innerHTML = '';
    
    console.log('Displaying gallery preview for', window.galleryFiles.length, 'files');
    
    window.galleryFiles.forEach((file, index) => {
        const reader = new FileReader();
        
        reader.onload = (e) => {
            const itemDiv = document.createElement('div');
            itemDiv.className = 'gallery-item';
            itemDiv.style.position = 'relative';
            itemDiv.style.display = 'inline-block';
            itemDiv.style.margin = '10px';
            itemDiv.style.borderRadius = '8px';
            itemDiv.style.overflow = 'hidden';
            
            const img = document.createElement('img');
            img.src = e.target.result;
            img.style.width = '150px';
            img.style.height = '150px';
            img.style.objectFit = 'cover';
            img.style.borderRadius = '8px';
            
            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.style.position = 'absolute';
            removeBtn.style.top = '5px';
            removeBtn.style.right = '5px';
            removeBtn.style.background = 'rgba(0,0,0,0.7)';
            removeBtn.style.color = 'white';
            removeBtn.style.border = 'none';
            removeBtn.style.borderRadius = '50%';
            removeBtn.style.width = '30px';
            removeBtn.style.height = '30px';
            removeBtn.style.cursor = 'pointer';
            removeBtn.style.display = 'flex';
            removeBtn.style.alignItems = 'center';
            removeBtn.style.justifyContent = 'center';
            removeBtn.innerHTML = '<i class="ri-close-line"></i>';
            removeBtn.onclick = (e) => {
                e.preventDefault();
                removeGalleryFile(index);
            };
            
            itemDiv.appendChild(img);
            itemDiv.appendChild(removeBtn);
            galleryPreview.appendChild(itemDiv);
        };
        
        reader.readAsDataURL(file);
    });
}

function removeGalleryFile(index) {
    console.log('Removing gallery file at index:', index);
    
    // Create new array without the removed file
    window.galleryFiles = window.galleryFiles.filter((_, i) => i !== index);
    
    // Update file input
    const galleryInput = document.getElementById('event_images');
    if (galleryInput) {
        const dataTransfer = new DataTransfer();
        window.galleryFiles.forEach(file => {
            dataTransfer.items.add(file);
        });
        galleryInput.files = dataTransfer.files;
    }
    
    // Redisplay preview
    displayGalleryPreview();
    
    console.log('Gallery files remaining:', window.galleryFiles.length);
}

function validateImageFile(file) {
    // Check file size
    if (file.size > IMAGE_CONFIG.maxSize) {
        console.warn('File too large:', file.name, file.size);
        return false;
    }
    
    // Check extension
    const ext = file.name.split('.').pop().toLowerCase();
    if (!IMAGE_CONFIG.allowedExtensions.includes(ext)) {
        console.warn('Invalid extension:', ext);
        return false;
    }
    
    // Check MIME type if available
    if (file.type && !IMAGE_CONFIG.allowedMimeTypes.includes(file.type)) {
        console.warn('Invalid MIME type:', file.type);
        // Don't reject - MIME type can be unreliable
    }
    
    return true;
}

// Helper function to get API URL
function getApiUrl(endpoint) {
    return '../api/' + endpoint;
}

// Helper function to show notification
function showPopup(message, type = 'info') {
    if (typeof showNotification === 'function') {
        showNotification(message, type);
    } else if (typeof alert === 'function') {
        alert(message);
    }
}
