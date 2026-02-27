# Edit Blog Troubleshooting - Quick Guide

## The Issue
Form is empty when editing a blog (e.g., `edit-blog.html?id=6`)

## Quick Fix Steps

### Step 1: Check if Database Has Blogs
Go to: `http://localhost/LUKUM(main)/api/check-blogs.php`

Look for:
- `"total_blogs": 24` (or any number > 0)
- `"blog_id_6": { ... }` (should have blog data)

**If total_blogs is 0**: Run this to populate:
```
http://localhost/LUKUM(main)/api/update-blogs.php
```

### Step 2: Test API Directly
Go to: `http://localhost/LUKUM(main)/api/get_blogs.php?id=6`

Should show blog data like:
```json
{
  "success": true,
  "data": {
    "id": 6,
    "title": "Blog Title Here",
    "author": "Author Name",
    "category": "Art & Culture",
    ...
  }
}
```

### Step 3: Check Browser Console
1. Open browser (Chrome/Firefox)
2. Press `F12` to open Developer Tools
3. Go to `Console` tab
4. Navigate to: `http://localhost/LUKUM(main)/admin/edit-blog.html?id=6`
5. Look for debug messages starting with `=== EDIT BLOG DEBUG ===`

**Expected console output**:
```
=== EDIT BLOG DEBUG ===
Blog ID from URL: 6
Fetching from: http://localhost/LUKUM(main)/api/get_blogs.php?id=6
Response status: 200
API Response: {success: true, data: {...}}
Blog data received: {id: 6, title: "...", ...}
=== POPULATE FORM DEBUG ===
Form fields found:
- blogId: YES
- title: YES
- author: YES
- category: YES
- excerpt: YES
Form fields populated
TinyMCE editor found: YES
TinyMCE content set
Form population complete
```

### Step 4: Verify Form Fields
In browser console, run:
```javascript
console.log('Title:', document.getElementById('title').value)
console.log('Author:', document.getElementById('author').value)
console.log('Category:', document.getElementById('category').value)
```

Should show blog data, not empty strings.

## Common Problems & Solutions

| Problem | Solution |
|---------|----------|
| "Blog not found" error | Database is empty. Run `update-blogs.php` to populate |
| Form fields empty, no error | Check console for JavaScript errors. Verify form field IDs |
| TinyMCE content not loading | Wait a moment, editor might still be initializing |
| API returns 404 | Check URL is correct: `http://localhost/LUKUM(main)/api/...` |
| Database connection error | Verify database is running and credentials are correct |

## Test Tools Available

### 1. Database Status Check
```
http://localhost/LUKUM(main)/api/check-blogs.php
```
Shows: database connection, blog count, sample blog data

### 2. API Test Page
```
http://localhost/LUKUM(main)/admin/test-blog-api.html
```
Test all blog API endpoints

### 3. Edit Blog Page
```
http://localhost/LUKUM(main)/admin/edit-blog.html?id=6
```
With detailed console logging

## What Was Fixed

1. **API Enhancement**: `get_blogs.php` now supports `?id=6` parameter
2. **Better Debugging**: Added console logs to track form population
3. **Error Handling**: Better error messages and redirects
4. **Simple API**: Created `add_blog_simple.php` for testing without auth
5. **Diagnostic Tools**: Created check and test pages

## Files to Check

- `api/get_blogs.php` - Returns single blog by ID
- `api/add_blog_simple.php` - Add blog without authentication
- `api/check-blogs.php` - Database diagnostic tool
- `admin/test-blog-api.html` - API test page
- `admin/edit-blog.html` - Edit form with debugging
- `admin/add-blog.html` - Add form with debugging

## Next Steps

1. ✅ Check database has blogs (use check-blogs.php)
2. ✅ Test API directly (use get_blogs.php?id=6)
3. ✅ Open browser console (F12)
4. ✅ Navigate to edit page
5. ✅ Check console for debug logs
6. ✅ Verify form fields populate
7. ✅ Edit and save blog

## Still Having Issues?

1. Check browser console for error messages (F12)
2. Check Network tab to see API responses
3. Verify database connection with check-blogs.php
4. Ensure blogs table is populated with update-blogs.php
5. Check file permissions on API files
6. Verify database credentials in api/db.php

## Database Population

If database is empty, populate with 24 blogs:
```
http://localhost/LUKUM(main)/api/update-blogs.php
```

Expected response:
```json
{
  "success": true,
  "message": "Successfully inserted 24 blogs",
  "inserted": 24,
  "breakdown": {
    "Art & Culture": 4,
    "Exhibition": 4,
    "Community": 4,
    "News": 4,
    "Tutorial": 4,
    "Behind the Scenes": 4
  }
}
```

## Status

✅ API updated to support single blog fetching
✅ Form enhanced with debugging
✅ Error handling improved
✅ Diagnostic tools created
✅ Ready for testing
