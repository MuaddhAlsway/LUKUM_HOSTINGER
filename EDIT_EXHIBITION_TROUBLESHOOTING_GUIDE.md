# Edit Exhibition - Complete Troubleshooting & Setup Guide

## Quick Summary of Fixes Applied

### ✅ Fixed Issues (June 23, 2026)

1. **API CORS Headers** - Added missing headers to `get_exhibition.php`
2. **JavaScript Error** - Fixed variable reference from `event` to `exhibition`
3. **Arabic Location Handling** - Added proper custom location support for Arabic
4. **Missing JS Files** - Created `form-reset.js` and `event-form.js`

---

## Step-by-Step Testing

### Test 1: Can I Access the Edit Page?
1. Go to Admin → Exhibitions
2. Click "Edit" on any exhibition
3. **Expected**: Page loads without showing blank screen

**If page is blank:**
- Open browser DevTools (F12)
- Check Console tab for errors
- Common issues:
  - API returning 404 (exhibition not found)
  - CORS errors (should be fixed now)
  - Network error timeout

### Test 2: Does Data Load?
When edit page opens, verify:
- [ ] Title (English) appears
- [ ] Description appears
- [ ] Dates are populated
- [ ] Location selector has correct value
- [ ] Cover image shows if one exists

**If data doesn't load:**
- Check Network tab in DevTools (F12)
- Look for request to `../api/get_exhibition.php?id=X`
- Response should show JSON with exhibition data

### Test 3: Can I Edit and Save?
1. Change one field (e.g., description)
2. Click "Update Exhibition"
3. **Expected**: "Exhibition updated!" message appears
4. Redirects back to exhibitions list

**If save fails:**
- Check Network tab for POST to `../api/edit_exhibition.php`
- Response should have `"success": true`

### Test 4: Images and Videos
1. Try uploading a new cover image
2. Try adding gallery images
3. Try adding/removing a video URL
4. Save and verify changes persist

---

## Database Setup (If Starting Fresh)

### Run These in Order:

1. **Create exhibitions table:**
   - Visit: `http://localhost/admin/api/create_exhibitions_table.php`
   - Should show: `{"success": true}`

2. **Add missing columns:**
   - Visit: `http://localhost/admin/api/add_missing_exhibition_columns.php`
   - Should show: `{"success": true, "added_columns": [...]}`

3. **Migrate data (if from events table):**
   - Visit: `http://localhost/admin/api/migrate_exhibitions.php`
   - Should show: `{"success": true, "migrated_count": X}`

---

## File Structure

```
admin/
├── edit-exhibition.html       ← Main edit form (FIXED)
├── exhibitions.html           ← List of exhibitions (WORKS)
├── add-exhibition.html        ← Add new exhibition
├── form-reset.js              ← Created (was missing)
├── event-form.js              ← Created (was missing)
├── config.js                  ← API configuration (OK)
└── admin-style.css            ← Styling (OK)

api/
├── get_exhibition.php         ← Fetch single (FIXED - added CORS)
├── get_exhibitions.php        ← Fetch all (OK)
├── edit_exhibition.php        ← Update exhibition (OK)
├── add_exhibition.php         ← Create new (OK)
├── delete_exhibition.php      ← Delete (OK)
├── remove_exhibition_video.php ← Remove video (OK)
└── other utilities...
```

---

## Common Errors & Solutions

### Error: "No exhibition ID provided"
- **Cause**: URL doesn't have `?id=X` parameter
- **Fix**: Make sure you're clicking the Edit link from exhibitions.html

### Error: "Exhibition not found"
- **Cause**: Exhibition ID doesn't exist in database
- **Fix**: 
  - Verify ID in URL matches a real exhibition
  - Check database: exhibitions table has records

### Error: "Network Error" in browser
- **Cause**: API endpoint not responding
- **Fix**:
  - Check if Apache/PHP is running
  - Verify API file exists: `api/get_exhibition.php`
  - Check PHP error logs

### Error: "CORS error" (was fixed, but if it appears again)
- **Cause**: Headers missing from API response
- **Fix**: Verify `get_exhibition.php` has these headers:
  ```php
  header('Access-Control-Allow-Origin: *');
  header('Access-Control-Allow-Methods: GET, OPTIONS');
  ```

### Form doesn't show data after loading
- **Cause**: JavaScript error in populateForm()
- **Fix**: 
  - Check DevTools Console for errors
  - Should have been fixed (variable name issue)
  - Verify all form field IDs match the code

### Video preview not showing
- **Cause**: URL format not recognized
- **Fix**: 
  - Use YouTube or Vimeo links
  - Format: https://www.youtube.com/watch?v=VIDEO_ID
  - Or: https://vimeo.com/VIDEO_ID

---

## How the Edit Page Works

### 1. Page Loads
```javascript
// URL: edit-exhibition.html?id=5
// Extracts ID from URL parameter
```

### 2. Fetches Exhibition Data
```javascript
// Calls: ../api/get_exhibition.php?id=5
// Response: JSON with all exhibition details
```

### 3. Populates Form
```javascript
// Sets values in form fields from fetched data
// Handles bilingual content (EN/AR)
// Shows images and videos
```

### 4. User Edits & Submits
```javascript
// Collects form data
// Uploads new images if selected
// Sends POST to ../api/edit_exhibition.php
```

### 5. API Updates Database
```php
// Updates exhibitions table with new values
// Returns success/error response
```

### 6. Shows Result
```javascript
// On success: Shows "Updated!" message
// Redirects to exhibitions.html
```

---

## API Endpoints Reference

### GET /api/get_exhibition.php
**Purpose:** Fetch single exhibition by ID
```
URL: ?id=5
Response: {
  "success": true,
  "data": {
    "id": 5,
    "title_en": "...",
    "title_ar": "...",
    "exhibition_date": "2026-06-15",
    "event_video": "https://...",
    ...
  }
}
```

### POST /api/edit_exhibition.php
**Purpose:** Update exhibition data
```
Body: {
  "id": 5,
  "title_en": "New Title",
  "exhibition_date": "2026-06-15",
  ...
}
Response: {
  "success": true,
  "message": "Exhibition updated successfully"
}
```

---

## Debugging Tips

### Enable Console Logging
Open DevTools (F12) → Console tab:
1. Errors appear in red
2. Warnings appear in yellow
3. Look for "=== DEBUG ===" messages

### Network Requests
1. F12 → Network tab
2. Reload page
3. Look for requests to API endpoints
4. Click to see request/response details

### Check Database Directly
```sql
SELECT id, title_en, exhibition_date FROM exhibitions;
```

### PHP Error Logs
Check server error logs:
- Apache: `C:\xampp\apache\logs\error.log`
- PHP: `C:\xampp\php\logs\php_error.log`

---

## Performance Notes

- Exhibitions load via API (not hardcoded)
- Images are shown as previews before save
- Video URLs are validated before saving
- Gallery images are compressed on server
- All data is UTF-8 encoded for Arabic support

---

## Future Enhancements

Placeholder files created for future features:
- `form-reset.js` - Can add intelligent form reset logic
- `event-form.js` - Can add shared form utilities

---

## Support Checklist

Before reporting issues, verify:
- [ ] Database is set up (run add_missing_exhibition_columns.php)
- [ ] Server is running (Apache + PHP)
- [ ] All API files exist and have correct paths
- [ ] No JavaScript errors in console (F12)
- [ ] Network requests show 200 status codes
- [ ] Using Chrome/Firefox/Edge (modern browser)

---

**Last Updated:** June 23, 2026
**Status:** All critical issues fixed ✓
