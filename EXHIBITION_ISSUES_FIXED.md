# Exhibition Issues - Image Paths & ID Counter

## Issue 1: Broken Image Paths ✅ FIXED

### Problem
When editing an exhibition, cover images and gallery images showed **broken paths** - images didn't load.

### Root Cause
Image paths are stored **relative** to the root (e.g., `assest/blog-uploads/image.jpg`).  
When viewing from `admin/edit-exhibition.html`, the path needs to go **up one level** (`../`).

### Solution Applied
Added path fixing logic in `populateForm()`:

```javascript
// For cover image
if (!imagePath.startsWith('http') && !imagePath.startsWith('/')) {
    imagePath = '../' + imagePath;  // Go up from admin folder
}
coverImg.src = imagePath;

// For gallery images
galleryImages.forEach(imagePath => {
    if (!imagePath.startsWith('http') && !imagePath.startsWith('/')) {
        imagePath = '../' + imagePath;  // Go up from admin folder
    }
    // Add to gallery preview
});
```

### Result
✅ Cover image displays correctly  
✅ Gallery images display correctly  
✅ Images load from correct paths  

---

## Issue 2: ID Counter (Expected Behavior)

### Problem
After deleting all exhibitions and adding a new one, the ID shows `id=3` instead of `id=1`.

### Root Cause
**This is NORMAL MySQL behavior!**

When you delete records, MySQL's `AUTO_INCREMENT` counter doesn't reset. It continues from the last used value.

**Why?**
- Prevents ID reuse (data integrity)
- Maintains referential integrity
- It's a feature, not a bug!

### Example Timeline
```
1. Add Exhibition 1 → ID = 1 ✓
2. Add Exhibition 2 → ID = 2 ✓
3. Add Exhibition 3 → ID = 3 ✓
4. Delete all exhibitions → IDs 1,2,3 deleted
5. Add new exhibition → ID = 4 (NOT id=1) ← EXPECTED
```

### How to Reset AUTO_INCREMENT (Optional)

If you REALLY need to reset the ID counter back to 1:

**File:** `api/RESET_AUTOINCREMENT.sql`

**Steps:**
1. Delete all exhibitions via admin panel (or run `DELETE FROM exhibitions;`)
2. Go to PhpMyAdmin
3. Open the SQL tab
4. Paste and execute:
```sql
ALTER TABLE exhibitions AUTO_INCREMENT = 1;
```
5. Add a new exhibition - it will have ID = 1

**⚠️ WARNING:** Only reset when table is completely empty!

---

## What's Now Fixed

### ✅ Image Paths
- Cover images load correctly
- Gallery images load correctly
- Paths work from admin folder

### ℹ️ ID Counter
- Working as designed
- Sequential numbering maintained
- No data loss

---

## Testing

### Test Cover Image
1. Go to `admin/exhibitions.html`
2. Add new exhibition with a cover image
3. Click Edit
4. ✅ Cover image displays

### Test Gallery Images
1. Add exhibition with gallery images
2. Click Edit
3. ✅ Gallery images show as thumbnails

### Test ID Reset (Optional)
1. Delete all exhibitions
2. Run RESET_AUTOINCREMENT.sql
3. Add new exhibition
4. ✅ ID = 1

---

## File Changes

| File | Change | Why |
|------|--------|-----|
| `admin/edit-exhibition.html` | Added path fixing (../) for images | Fix broken image links |
| `api/RESET_AUTOINCREMENT.sql` | New file | Optional ID reset |

---

## FAQ

### Q: Why are my images broken after editing?
**A:** The image paths are relative to the root, but edit-exhibition.html is in the admin folder. Fixed by prepending `../`.

### Q: Why does my new exhibition have ID 4 instead of 1?
**A:** MySQL doesn't reset AUTO_INCREMENT when you delete records. It's designed this way for data integrity.

### Q: How do I reset the ID to 1?
**A:** Run the query: `ALTER TABLE exhibitions AUTO_INCREMENT = 1;` (only when table is empty)

### Q: Is this a bug?
**A:** No, this is standard MySQL behavior across all databases.

---

## Status

✅ **Image paths fixed**  
ℹ️ **ID counter working as designed**  
✅ **Ready for production**

---

**Date:** 2026-06-21  
**Both Issues:** Resolved
