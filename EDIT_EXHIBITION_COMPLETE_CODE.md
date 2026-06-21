# Complete Corrected JavaScript Code for edit-exhibition.html

This is the complete, corrected JavaScript section for the edit-exhibition.html file with all fixes applied.

---

## Full JavaScript Code Block

```javascript
<script>
    // ============================================================================
    // HANDLE LOCATION CHANGE
    // ============================================================================
    function handleLocationChange(select, lang) {
        const customInput = document.getElementById('location_' + lang);
        if (select.value === 'other') {
            customInput.style.display = 'block';
            customInput.required = true;
            customInput.value = '';
        } else {
            customInput.style.display = 'none';
            customInput.required = false;
            customInput.value = select.value;
        }
    }

    // ============================================================================
    // SYNC LOCATION ARABIC
    // ============================================================================
    function syncLocationAr() {
        const locationSelectEn = document.getElementById('location_select_en');
        const locationSelectAr = document.getElementById('location_select_ar');
        if (!locationSelectEn || !locationSelectAr) return;
        
        const enValue = locationSelectEn.value;
        const mapping = {
            'Hall 1': 'Hall 1',
            'Hall 2': 'Hall 2',
            'Café': 'Café',
            'Meeting Room': 'Meeting Room',
            'other': 'other',
            '': ''
        };
        
        const arValue = mapping[enValue] || enValue;
        locationSelectAr.value = arValue;
        handleLocationChange(locationSelectAr, 'ar');
    }

    // ============================================================================
    // MAIN DOM READY - INITIALIZATION
    // ============================================================================
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize location handlers
        const selectEn = document.getElementById('location_select_en');
        const selectAr = document.getElementById('location_select_ar');
        if (selectEn) handleLocationChange(selectEn, 'en');
        if (selectAr) handleLocationChange(selectAr, 'ar');

        // Load exhibition data
        loadExhibition();

        // ====================================================================
        // FORM SUBMISSION HANDLER
        // ====================================================================
        const exhibitionForm = document.getElementById('exhibitionForm');
        if (exhibitionForm) {
            exhibitionForm.addEventListener('submit', async (e) => {
                e.preventDefault();

                const submitBtn = document.getElementById('submitBtn');
                const originalBtnText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="ri-loader-4-line" style="animation: spin 1s linear infinite;"></i> Updating...';

                try {
                    const id = document.getElementById('exhibitionId').value;
                    let coverImagePath = null;
                    const fileInput = document.getElementById('cover_image');
                    
                    // Upload cover image if selected
                    if (fileInput.files && fileInput.files[0]) {
                        const file = fileInput.files[0];
                        const formData = new FormData();
                        formData.append('file', file);
                        
                        const uploadResponse = await fetch('../api/upload_cover_image.php', {
                            method: 'POST',
                            body: formData
                        });
                        
                        const uploadResult = await uploadResponse.json();
                        if (uploadResult.success) {
                            coverImagePath = uploadResult.path;
                        }
                    }

                    // Build update data object
                    const updateData = {
                        id: id,
                        title_en: document.getElementById('title_en').value.trim(),
                        description_en: document.getElementById('description_en').value.trim(),
                        location_en: document.getElementById('location_en').value.trim(),
                        title_ar: document.getElementById('title_ar').value.trim(),
                        description_ar: document.getElementById('description_ar').value.trim(),
                        location_ar: document.getElementById('location_ar').value.trim(),
                        exhibition_date: document.getElementById('exhibition_date').value,
                        exhibition_time: (document.getElementById('exhibition_time').value || '10:00') + ':00',
                        exhibition_end_time: (document.getElementById('exhibition_end_time').value || '18:00') + ':00',
                        end_date: document.getElementById('end_date').value || null,
                        event_video: document.getElementById('event_video').value.trim() || null
                    };

                    // Upload new gallery images if any are selected
                    const galleryInput = document.getElementById('gallery_images');
                    if (galleryInput.files && galleryInput.files.length > 0) {
                        let newGalleryImages = [];
                        for (let i = 0; i < galleryInput.files.length; i++) {
                            const file = galleryInput.files[i];
                            const formData = new FormData();
                            formData.append('file', file);
                            
                            const uploadResponse = await fetch('../api/upload_cover_image.php', {
                                method: 'POST',
                                body: formData
                            });
                            
                            const uploadResult = await uploadResponse.json();
                            if (uploadResult.success) {
                                newGalleryImages.push(uploadResult.path);
                            }
                        }
                        if (newGalleryImages.length > 0) {
                            updateData.gallery_images = JSON.stringify(newGalleryImages);
                        }
                    }

                    // Add cover image to update data if uploaded
                    if (coverImagePath) {
                        updateData.cover_image = coverImagePath;
                    }

                    // Send update to API
                    const response = await fetch('../api/edit_exhibition.php', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json'},
                        body: JSON.stringify(updateData)
                    });

                    const result = await response.json();
                    if (result.success) {
                        showNotification('Exhibition updated successfully!', 'success');
                        setTimeout(() => {
                            window.location.href = 'exhibitions.html';
                        }, 1500);
                    } else {
                        throw new Error(result.message || 'Failed to update exhibition');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showNotification(error.message, 'error');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnText;
                }
            });
        }

        // ====================================================================
        // MULTI-DAY EXHIBITION TOGGLE
        // ====================================================================
        const sameDayRadio = document.getElementById('sameDayRadio');
        const multiDayRadio = document.getElementById('multiDayRadio');
        const endDateSection = document.getElementById('endDateSection');
        const endDateInput = document.getElementById('end_date');
        const endDateStartTime = document.getElementById('end_date_start_time');
        const endTime = document.getElementById('end_time');

        function toggleEndDateSection() {
            if (multiDayRadio.checked) {
                endDateSection.style.display = 'block';
            } else {
                endDateSection.style.display = 'none';
                endDateInput.value = '';
                endDateStartTime.value = '';
                endTime.value = '';
            }
        }

        sameDayRadio.addEventListener('change', toggleEndDateSection);
        multiDayRadio.addEventListener('change', toggleEndDateSection);

        // ====================================================================
        // COVER IMAGE UPLOAD
        // ====================================================================
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
                        if (removeCoverBtn) removeCoverBtn.style.display = 'flex';
                        const placeholder = coverPreview.querySelector('.cover-placeholder');
                        if (placeholder) placeholder.style.display = 'none';
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
                    if (placeholder) placeholder.style.display = 'flex';
                });
            }
        }

        // ====================================================================
        // GALLERY IMAGES PREVIEW (NEW UPLOADS)
        // ====================================================================
        const galleryInput = document.getElementById('gallery_images');
        const newGalleryPreview = document.getElementById('newGalleryPreview');

        if (galleryInput) {
            galleryInput.addEventListener('change', function(e) {
                newGalleryPreview.innerHTML = '';
                
                const files = e.target.files;
                for (let i = 0; i < files.length; i++) {
                    const file = files[i];
                    const reader = new FileReader();
                    
                    reader.onload = function(event) {
                        const img = document.createElement('img');
                        img.src = event.target.result;
                        img.style.cssText = 'width: 100px; height: 100px; object-fit: cover; border-radius: 4px; border: 2px solid #ddd;';
                        newGalleryPreview.appendChild(img);
                    };
                    
                    reader.readAsDataURL(file);
                }
            });
        }
    });

    // ============================================================================
    // LOAD EXHIBITION DATA
    // ============================================================================
    async function loadExhibition() {
        const params = new URLSearchParams(window.location.search);
        const id = params.get('id');
        
        if (!id) {
            alert('No exhibition ID provided');
            window.location.href = 'exhibitions.html';
            return;
        }

        try {
            const response = await fetch(`../api/get_exhibition.php?id=${id}`);
            const data = await response.json();
            
            if (data.success && data.data) {
                populateForm(data.data);
                checkMultiDay(data.data);
            } else {
                alert('Exhibition not found');
                window.location.href = 'exhibitions.html';
            }
        } catch (error) {
            console.error('Error loading exhibition:', error);
            alert('Error loading exhibition');
            window.location.href = 'exhibitions.html';
        }
    }

    // ============================================================================
    // POPULATE FORM WITH EXHIBITION DATA (FIXED VERSION)
    // ============================================================================
    function populateForm(exhibition) {
        // Set basic fields
        document.getElementById('exhibitionId').value = exhibition.id;
        document.getElementById('title_en').value = exhibition.title_en || '';
        document.getElementById('description_en').value = exhibition.description_en || '';
        document.getElementById('title_ar').value = exhibition.title_ar || '';
        document.getElementById('description_ar').value = exhibition.description_ar || '';
        document.getElementById('exhibition_date').value = exhibition.exhibition_date || '';
        document.getElementById('exhibition_time').value = (exhibition.exhibition_time || '10:00:00').substring(0, 5);
        document.getElementById('exhibition_end_time').value = (exhibition.exhibition_end_time || '18:00:00').substring(0, 5);
        document.getElementById('end_date').value = exhibition.end_date || '';
        document.getElementById('event_video').value = exhibition.event_video || '';
        
        // ====================================================================
        // HANDLE LOCATION FIELDS (FIXED)
        // ====================================================================
        const location_en = exhibition.location_en || '';
        const location_ar = exhibition.location_ar || '';
        
        // Set location input values directly (works for both select and custom)
        document.getElementById('location_en').value = location_en;
        document.getElementById('location_ar').value = location_ar;
        
        // Try to set select dropdown if location matches a predefined option
        const locationSelectEn = document.getElementById('location_select_en');
        const locationSelectAr = document.getElementById('location_select_ar');
        
        if (locationSelectEn) {
            const options = Array.from(locationSelectEn.options).map(opt => opt.value);
            if (options.includes(location_en)) {
                locationSelectEn.value = location_en;
            } else if (location_en) {
                locationSelectEn.value = 'other';
                document.getElementById('location_en').style.display = 'block';
                document.getElementById('location_en').value = location_en;
            }
        }
        
        if (locationSelectAr) {
            const options = Array.from(locationSelectAr.options).map(opt => opt.value);
            if (options.includes(location_ar)) {
                locationSelectAr.value = location_ar;
            } else if (location_ar) {
                locationSelectAr.value = 'other';
                document.getElementById('location_ar').style.display = 'block';
                document.getElementById('location_ar').value = location_ar;
            }
        }
        
        // ====================================================================
        // HANDLE COVER IMAGE
        // ====================================================================
        if (exhibition.cover_image) {
            const coverImg = document.getElementById('coverImg');
            const coverPreview = document.getElementById('coverPreview');
            const placeholder = coverPreview.querySelector('.cover-placeholder');
            coverImg.src = exhibition.cover_image;
            coverImg.style.display = 'block';
            placeholder.style.display = 'none';
            document.getElementById('removeCover').style.display = 'flex';
        }

        // ====================================================================
        // HANDLE EXISTING GALLERY IMAGES (FIXED - NO LOOP)
        // ====================================================================
        if (exhibition.gallery_images) {
            try {
                // IMPORTANT: Clear any existing gallery images first
                const currentGalleryPreview = document.getElementById('currentGalleryPreview');
                currentGalleryPreview.innerHTML = '';
                
                const galleryImages = typeof exhibition.gallery_images === 'string' 
                    ? JSON.parse(exhibition.gallery_images) 
                    : exhibition.gallery_images;
                
                if (Array.isArray(galleryImages) && galleryImages.length > 0) {
                    galleryImages.forEach(imagePath => {
                        const img = document.createElement('img');
                        img.src = imagePath;
                        img.alt = 'Gallery image';
                        img.style.cssText = 'width: 100px; height: 100px; object-fit: cover; border-radius: 4px; border: 2px solid #ddd; cursor: pointer;';
                        img.title = imagePath;
                        
                        // Add click handler to remove image if needed
                        img.addEventListener('click', function() {
                            this.remove();
                        });
                        
                        currentGalleryPreview.appendChild(img);
                    });
                }
            } catch (e) {
                console.error('Could not parse gallery images:', e);
            }
        }

        // Set character counts
        document.getElementById('charCount_en').textContent = (exhibition.description_en || '').length;
        document.getElementById('charCount_ar').textContent = (exhibition.description_ar || '').length;
    }

    // ============================================================================
    // CHECK IF MULTI-DAY EXHIBITION
    // ============================================================================
    function checkMultiDay(exhibition) {
        if (exhibition.end_date) {
            document.getElementById('multiDayRadio').checked = true;
            document.getElementById('endDateSection').style.display = 'block';
        }
    }

    // ============================================================================
    // SHOW NOTIFICATION
    // ============================================================================
    function showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.textContent = message;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            background: ${type === 'success' ? '#4caf50' : '#f44336'};
            color: white;
            border-radius: 4px;
            z-index: 10000;
        `;
        document.body.appendChild(notification);
        setTimeout(() => notification.remove(), 3000);
    }
</script>
```

---

## Key Fixes Applied

### ✅ Fix 1: Gallery Loop
**Before:** Gallery images appended without clearing = duplicates
**After:** `currentGalleryPreview.innerHTML = '';` clears before appending

### ✅ Fix 2: Location Handling
**Before:** Only set text input, dropdown not updated
**After:** Detects predefined vs custom location, sets both select and input

### ✅ Fix 3: Array Validation
**Before:** No check for array length
**After:** `if (Array.isArray(galleryImages) && galleryImages.length > 0)`

### ✅ Fix 4: Error Logging
**Before:** `console.log()`
**After:** `console.error()` for better debugging

---

## Implementation Steps

1. **Copy this entire code block**
2. **Locate the first `<script>` tag** in edit-exhibition.html (around line 305)
3. **Replace the entire script section** with this corrected version
4. **Save the file**
5. **Test in browser:**
   - Open an exhibition in edit mode
   - Gallery images load WITHOUT duplicating
   - Location field displays correctly
   - Can add new gallery images
   - Click "Update" - works smoothly

---

## What This Code Does

| Function | Purpose |
|----------|---------|
| `handleLocationChange()` | Show/hide custom location input based on selection |
| `syncLocationAr()` | Keep Arabic location in sync with English |
| `loadExhibition()` | Fetch exhibition data from API |
| `populateForm()` | Load all exhibition data into form fields (FIXED) |
| `checkMultiDay()` | Show end date fields if multi-day exhibition |
| `showNotification()` | Display success/error messages |

---

## No Breaking Changes

✅ All existing functionality preserved
✅ Only internal logic fixes applied  
✅ API calls unchanged
✅ Form fields unchanged
✅ Backward compatible

---

**Status:** Ready to Use
**Date:** 2026-06-21
**All Fixes:** Applied ✅
