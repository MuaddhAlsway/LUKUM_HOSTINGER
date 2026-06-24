# Event Image Upload - Quick Test Guide

## Before You Start
Ensure you completed the previous fixes:
- ✅ Add event API is working (fixed bind_param mismatch)
- ✅ Event database table exists
- ✅ `event_gallery` table exists (for gallery storage)

## Quick Test Steps

### 1. Create a Test Event with Cover Image

```
URL: http://localhost/admin/add-event.html

1. Fill in the form:
   - Event Title (EN): "Test Event with Image"
   - Description: "Testing image upload"
   - Location: "Hall 1"
   - Event Date: Today or past date

2. COVER IMAGE UPLOAD:
   - Click on the cover image area (shows placeholder)
   - Select a JPG/PNG/GIF/WebP image (~2MB or less)
   - Image preview should appear

3. Click "Create Event"
   - Should see success message
   - Should redirect to events list
   - New event should appear in the list
```

### 2. Test Gallery Images

```
1. In the same "Add Event" form:

2. GALLERY UPLOAD:
   - Scroll to gallery section
   - Click to add gallery images
   - Select multiple images (JPG/PNG/GIF/WebP)
   - Thumbnails should appear

3. Submit the form
   - Should see success message
   - Gallery images should be processed
```

### 3. Verify Images Were Uploaded

Check these directories exist and contain files:
```
✓ c:\assest\event-uploads\        (has event-cover-*.jpg files)
✓ c:\assest\event-gallery\         (has event_*_*.jpg files)
```

### 4. Check Event Page

```
1. Go to Events page: http://localhost/event.php
2. Click on your test event
3. Should see:
   - Cover image displayed
   - Gallery images in lightbox/carousel
```

## Debugging If Upload Fails

### Step 1: Check Browser Console
Open F12 → Console tab to see:
```javascript
// Should show:
✓ Uploading image...
✓ Image uploaded successfully: assest/event-uploads/event-cover-...jpg
✓ Uploading gallery images: 3
✓ Gallery upload response: {success: true, ...}
```

### Step 2: Check PHP Error Log
```bash
tail -20 c:\logs\error.log

# Should show:
Event cover image uploaded successfully: assest/event-uploads/event-cover-...jpg
Uploading to: /var/www/assest/event-gallery/event_123_...jpg
Image saved to database: assest/event-gallery/event_123_...jpg
```

### Step 3: Check Directories Exist
```bash
# Windows:
dir c:\assest\event-uploads\
dir c:\assest\event-gallery\

# Should exist and be empty or have files
```

### Step 4: Test API Directly

**Cover Image Upload:**
```javascript
// In browser console:
const formData = new FormData();
const file = new File(['test'], 'test.jpg', {type: 'image/jpeg'});
formData.append('file', file);

fetch('/api/upload_event_cover_image.php', {
    method: 'POST',
    body: formData
})
.then(r => r.json())
.then(d => console.log(d));

// Should return: {success: true, path: "assest/event-uploads/..."}
```

**Gallery Upload:**
```javascript
// In browser console:
const formData = new FormData();
formData.append('event_id', '1');
const file = new File(['test'], 'test.jpg', {type: 'image/jpeg'});
formData.append('images', file);

fetch('/api/upload_event_gallery.php', {
    method: 'POST',
    body: formData
})
.then(r => r.json())
.then(d => console.log(d));

// Should return: {success: true, images: [...], count: 1}
```

## Common Issues & Solutions

| Issue | Cause | Solution |
|-------|-------|----------|
| "No file uploaded" | Form not sending file | Check browser file input element |
| "Invalid file type" | Wrong extension | Use JPG, PNG, GIF, or WebP only |
| "File size exceeds" | File > 5MB | Compress image to < 5MB |
| "Upload directory not writable" | Permissions issue | Ensure assest/ is writable |
| Empty response from API | Output buffer issue | Check logs for PHP errors |
| "Event not found" | Event ID mismatch | Ensure event was created first |
| Gallery not in database | Table issue | Verify event_gallery table exists |

## What Gets Saved Where

### Cover Images
- **Uploaded to:** `assest/event-uploads/event-cover-TIMESTAMP-RANDOM.jpg`
- **Stored in DB:** In `events` table, `cover_image` column
- **URL pattern:** `assest/event-uploads/event-cover-*.jpg`

### Gallery Images
- **Uploaded to:** `assest/event-gallery/event_EVENTID_TIMESTAMP_RANDOM.jpg`
- **Stored in DB:** In `event_gallery` table with `image_url` column
- **Linked by:** `event_id` (foreign key to events table)

## Successful Upload Signs

✅ Browser shows success notification  
✅ Page redirects to events list  
✅ No error messages in console  
✅ Files exist in upload directories  
✅ Event page displays images  
✅ Error log shows "upload successfully"  

## Next Steps If Upload Works

1. Test editing an event
2. Test deleting an event (should delete images too)
3. Test with different image formats
4. Test with multiple gallery images
5. Monitor error log for any warnings

## Support

If uploads still fail after all fixes:
1. Check `/logs/error.log` for exact error message
2. Look for "Cover Image Upload Error:" or "Gallery Upload Exception:"
3. Verify all directories exist and are writable
4. Check event_gallery table exists in database
5. Look for any PHP warnings about missing functions or classes
