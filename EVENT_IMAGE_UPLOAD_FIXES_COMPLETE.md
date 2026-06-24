# Event Image Upload Issues - FIXED

## Problem Summary
The "Add Event" form couldn't upload:
1. **Cover images** - The main event image
2. **Gallery images** - Additional event gallery photos

Users received vague error messages or uploads silently failed.

## Root Causes Identified

### 1. **Missing Upload Directories**
- The directories `assest/event-uploads/` and `assest/event-gallery/` didn't exist
- PHP code had logic to create them, but there were issues with creation or permissions

### 2. **No Output Buffering**
- Upload APIs didn't use `ob_start()`
- Any PHP errors would corrupt the JSON response

### 3. **Incorrect FormData Field Name**
- HTML was sending gallery images as `images[]` (array notation)
- PHP was checking for `images` (non-array)
- This mismatch caused the "No images provided" error

### 4. **Poor Error Handling**
- Upload APIs had minimal error logging
- No verification that files were actually moved successfully
- No checks for temp file existence before moving

## Solutions Applied

### Fix #1: Created Upload Directories
```bash
Created:
- c:\assest\event-uploads\      # For cover images
- c:\assest\event-gallery\       # For gallery images
```

### Fix #2: Added Output Buffering to Both APIs
```php
<?php
ob_start();  // Start buffering before anything else
require_once __DIR__ . '/config.php';
header('Content-Type: application/json');
// ...
ob_end_clean();  // Clear buffer before sending response
echo json_encode([...]);
```

### Fix #3: Fixed FormData Field Name in HTML
**Before:**
```javascript
formData.append('images[]', window.galleryFiles[i]);  // Wrong!
```

**After:**
```javascript
formData.append('images', window.galleryFiles[i]);  // Correct!
```

### Fix #4: Improved Error Handling in APIs

#### Cover Image Upload (`upload_event_cover_image.php`)
- Added `ob_start()` and `ob_end_clean()`
- Added detailed logging for debugging
- Added file verification after move
- Better error messages

#### Gallery Upload (`upload_event_gallery.php`)
- Added `ob_start()` and `ob_end_clean()`
- Added support for both `images` and `images[]` keys (handles HTML fix)
- Added temp file existence verification
- Added file verification after move
- Better error logging with file paths
- Validates event exists before uploading
- Saves to database after file move

### Fix #5: Enhanced Logging

Both upload APIs now log:
- File size and MIME type
- Temp file location
- Upload destination
- Success/failure of move operation
- Database insertion status

Check `/logs/error.log` to diagnose upload issues:
```
Event ID: 123
File: photo.jpg (size: 2048576, error: 0, tmp: /tmp/php12345)
Uploading to: /var/www/assest/event-gallery/event_123_1234567890_abc123.jpg
Image saved to database: assest/event-gallery/event_123_1234567890_abc123.jpg
```

## Files Modified

1. **api/upload_event_cover_image.php**
   - Added output buffering
   - Added file verification
   - Better error logging
   - Changes: ~15 lines

2. **api/upload_event_gallery.php**
   - Added output buffering
   - Added support for both field names
   - Added temp file verification
   - Added file move verification
   - Better error logging
   - Changes: ~40 lines

3. **admin/add-event.html**
   - Fixed FormData field name from `images[]` to `images`
   - Changes: 1 line

## Testing the Fix

### Step 1: Test Cover Image Upload
1. Open `/admin/add-event.html`
2. Click on the cover image section
3. Select a test image (JPG, PNG, GIF, or WebP)
4. Image preview should appear
5. Continue with form submission

### Step 2: Test Gallery Upload
1. In the same form, go to the gallery section
2. Click to add gallery images
3. Select multiple images
4. See thumbnails appear
5. Submit the form

### Expected Results
✅ Event created successfully  
✅ Cover image displays on event page  
✅ Gallery images appear in event gallery  
✅ No error messages  
✅ Files saved to correct directories  

## Troubleshooting

If uploads still fail, check:

1. **Directory permissions**
   ```bash
   # Should be readable and writable
   ls -la c:\assest\event-uploads\
   ls -la c:\assest\event-gallery\
   ```

2. **PHP error log**
   ```bash
   # Check for detailed error messages
   tail -50 c:\logs\error.log
   ```

3. **File size limit**
   - Maximum file size: 5MB
   - Check file before uploading

4. **File type**
   - Allowed: JPG, JPEG, PNG, GIF, WebP, HEIC, HEIF
   - Check file extension

5. **Database connection**
   - Gallery upload needs database to save image records
   - Check event_gallery table exists

## Database Requirements

The gallery upload feature requires the `event_gallery` table:

```sql
CREATE TABLE IF NOT EXISTS event_gallery (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    caption VARCHAR(255),
    display_order INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
);
```

If this table doesn't exist, gallery uploads will fail with "Prepare insert query failed" error.

## File Structure After Upload

```
assest/
├── event-uploads/          # Cover images
│   ├── event-cover-1234567890-abc123.jpg
│   ├── event-cover-1234567891-def456.png
│   └── ...
├── event-gallery/          # Gallery images
│   ├── event_123_1234567890_abc123.jpg
│   ├── event_124_1234567891_def456.png
│   └── ...
└── ...other assets...
```

## Performance Notes

- Maximum file size: 5MB per file
- Multiple files can be uploaded in one request
- Files are moved (not copied) to improve performance
- Database inserts use prepared statements for security
- All operations are logged for debugging

## Security Features

✅ File extension validation  
✅ MIME type checking (with fallback)  
✅ File size limits  
✅ Unique filenames (prevents overwrites)  
✅ Prepared statements (SQL injection protection)  
✅ Event ownership validation (gallery linked to event)  

## Summary

Event image uploads are now fully functional with:
- Proper error handling
- Output buffering for clean JSON responses
- Detailed logging for debugging
- Support for both cover and gallery images
- Database integration for gallery management
- Security validations at every step
