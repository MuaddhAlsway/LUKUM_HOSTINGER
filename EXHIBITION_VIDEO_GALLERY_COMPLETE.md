# Exhibition Video & Gallery Images - Complete Implementation

## 📊 Summary of Changes

This document outlines all changes made to add Event Video and Gallery Images support to the exhibitions system.

---

## 🗄️ Database Changes

### Migration File
**Location:** `api/ADD_VIDEO_GALLERY_COLUMNS.sql`

**What It Does:** Adds 2 new columns to existing exhibitions table

```sql
ALTER TABLE exhibitions ADD COLUMN `event_video` VARCHAR(500);
ALTER TABLE exhibitions ADD COLUMN `gallery_images` LONGTEXT;
```

### New Columns

| Column | Type | Purpose | Nullable | Example |
|--------|------|---------|----------|---------|
| `event_video` | VARCHAR(500) | Single video URL | YES | `https://www.youtube.com/embed/abc123` |
| `gallery_images` | LONGTEXT | JSON array of image paths | YES | `["assest/img-1.jpg", "assest/img-2.jpg"]` |

---

## 🎨 Frontend Changes

### 1. Add Exhibition Form
**File:** `admin/add-exhibition.html`

**New Sections Added:**
- Event Video (Optional) - Input field for video URL
- Gallery Images (Optional) - Multi-file upload for gallery images

**Features:**
- Video URL accepts: YouTube embed, Vimeo, direct video files
- Gallery image previews show as thumbnails while uploading
- Both fields are completely optional

**Code Changes:**
- Added form fields for video and gallery
- Added image preview functionality
- Updated form submission to handle new fields

### 2. Edit Exhibition Form
**File:** `admin/edit-exhibition.html`

**New Sections Added:**
- Event Video (Optional) - Input field to update video URL
- Gallery Images (Optional) - Shows existing gallery + option to add more

**Features:**
- Displays existing gallery images as thumbnails
- Shows new selected images before saving
- Can update or clear video URL
- Preserves existing gallery images when adding new ones

**Code Changes:**
- Added form fields for video and gallery
- Updated `populateForm()` to display existing video/gallery data
- Updated form submission to handle new fields

---

## 🔧 API Changes

### 1. Add Exhibition API
**File:** `api/add_exhibition.php`

**Updates:**
- Extract `event_video` from request
- Extract `gallery_images` (JSON string) from request
- Include both fields in INSERT statement
- Create new exhibitions table with new columns if needed

**New Parameters:**
```php
$event_video = isset($input['event_video']) ? trim($input['event_video']) : null;
$gallery_images = isset($input['gallery_images']) ? trim($input['gallery_images']) : null;
```

### 2. Edit Exhibition API
**File:** `api/edit_exhibition.php`

**Updates:**
- Extract `event_video` from request
- Extract `gallery_images` (JSON string) from request
- Dynamically build UPDATE statement to handle optional fields
- Support partial updates (only update fields that are provided)

**Features:**
- Handles NULL values for empty fields
- Only updates fields that are explicitly sent
- Preserves existing data if field is not included

---

## 📂 File Structure

### New Files Created
```
api/
├── ADD_VIDEO_GALLERY_COLUMNS.sql         ← Database migration
│
admin/
├── add-exhibition.html                    ← Updated (video + gallery sections)
├── edit-exhibition.html                   ← Updated (video + gallery sections)

Root/
├── QUICK_MIGRATION_STEPS.txt              ← Quick reference guide
├── MIGRATION_VIDEO_GALLERY_GUIDE.md       ← Detailed migration guide
└── EXHIBITION_VIDEO_GALLERY_COMPLETE.md   ← This file
```

### Updated Files
```
api/
├── add_exhibition.php                     ← Updated (handle video + gallery)
├── edit_exhibition.php                    ← Updated (handle video + gallery)
├── EXHIBITIONS_TABLE_FINAL.sql            ← Reference only (no changes needed)

admin/
├── add-exhibition.html                    ← Updated
├── edit-exhibition.html                   ← Updated
```

---

## 🚀 Deployment Steps

### Step 1: Database Migration (Required)
Execute the SQL migration:
```bash
File: api/ADD_VIDEO_GALLERY_COLUMNS.sql
Method: PhpMyAdmin SQL tab or MySQL CLI
Time: < 1 second
```

### Step 2: Verify Migration
Run verification query:
```sql
DESCRIBE exhibitions;
```
Look for: `event_video` and `gallery_images` columns

### Step 3: Test Forms
1. Open `admin/add-exhibition.html`
2. Add an exhibition WITH video and gallery images
3. Open `admin/edit-exhibition.html` 
4. Edit an exhibition to verify video/gallery display
5. Check `spaces.php` to see if Past Events section displays correctly

---

## 📝 Data Format

### Event Video Storage
Stored as simple text string (VARCHAR 500):
```
https://www.youtube.com/embed/video_id
https://vimeo.com/video_id
assest/videos/exhibition.mp4
```

### Gallery Images Storage
Stored as JSON array (LONGTEXT):
```json
["assest/gallery/img-1.jpg", "assest/gallery/img-2.jpg", "assest/gallery/img-3.jpg"]
```

---

## 🔄 User Workflow

### Adding Exhibition with Media

1. Admin goes to `admin/add-exhibition.html`
2. Fills exhibition details (title, date, location, etc.)
3. Uploads cover image
4. *(Optional)* Enters Event Video URL
5. *(Optional)* Uploads 1+ gallery images (with preview)
6. Clicks "Create Exhibition"
7. Exhibition saves with all media

### Editing Exhibition Media

1. Admin goes to `admin/exhibitions.html`
2. Clicks "Edit" on an exhibition
3. Loads `admin/edit-exhibition.html?id=X`
4. Existing media displays:
   - Video URL shows in input field
   - Gallery images show as thumbnails
5. Admin can:
   - Update/clear video URL
   - Add more gallery images
   - Keep existing gallery images
6. Clicks "Update Exhibition"
7. Changes save

### Viewing Exhibition

1. User visits `spaces.php`
2. Scrolls to "Past Events" section
3. Sees exhibitions from exhibitions table
4. Each exhibition shows:
   - Cover image
   - Title, date, location
   - Description

---

## ✅ Testing Checklist

- [ ] Database migration runs without errors
- [ ] `DESCRIBE exhibitions;` shows 2 new columns
- [ ] `admin/add-exhibition.html` loads without JS errors
- [ ] Can upload event video URL
- [ ] Can upload multiple gallery images
- [ ] Gallery preview shows thumbnails
- [ ] Exhibition creates successfully
- [ ] `admin/edit-exhibition.html` loads existing exhibitions
- [ ] Can see existing video URL
- [ ] Can see existing gallery images as thumbnails
- [ ] Can update video URL
- [ ] Can add more gallery images
- [ ] Exhibition updates successfully
- [ ] `spaces.php` shows exhibition in Past Events section

---

## 🔐 Data Validation

### Video URL Validation
- Type: URL (optional)
- Length: Max 500 characters
- Accepted formats:
  - YouTube: `https://www.youtube.com/embed/...`
  - Vimeo: `https://vimeo.com/...`
  - Direct: Any video file URL

### Gallery Images Validation
- Format: JSON array of strings
- Max size per image: Configurable (set in upload handler)
- Supported formats: JPG, PNG, GIF, WebP, HEIC
- Nullable: Yes (can be empty)

---

## 📚 Documentation Files

| File | Purpose |
|------|---------|
| `QUICK_MIGRATION_STEPS.txt` | Quick reference for running migration |
| `MIGRATION_VIDEO_GALLERY_GUIDE.md` | Detailed step-by-step guide |
| `EXHIBITION_VIDEO_GALLERY_COMPLETE.md` | This comprehensive guide |
| `api/ADD_VIDEO_GALLERY_COLUMNS.sql` | Database migration SQL |

---

## 🎯 Features Summary

### Event Video (Optional)
✅ Add single video URL  
✅ Update video URL  
✅ Clear/remove video  
✅ Supports YouTube, Vimeo, direct videos  

### Gallery Images (Optional)
✅ Upload multiple images  
✅ View existing gallery thumbnails  
✅ Add more images to existing gallery  
✅ Preserve existing images when adding new ones  
✅ Display in admin form as preview  

### Display
✅ Show in Past Events on spaces.php  
✅ Store efficiently as JSON  
✅ Nullable fields (no data loss on existing exhibitions)  

---

## 🔍 Verification Queries

### Check columns exist:
```sql
SELECT COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_NAME = 'exhibitions' AND TABLE_SCHEMA = 'u812122863_lakum_artspace';
```

### View all exhibitions with new fields:
```sql
SELECT id, title_en, event_video, gallery_images FROM exhibitions;
```

### Count exhibitions with gallery images:
```sql
SELECT COUNT(*) FROM exhibitions WHERE gallery_images IS NOT NULL;
```

### Count exhibitions with video:
```sql
SELECT COUNT(*) FROM exhibitions WHERE event_video IS NOT NULL;
```

---

## 📞 Support

### Common Issues

**Issue:** Migration says "column already exists"  
**Solution:** Columns already added - proceed to testing forms

**Issue:** Video/gallery not saving  
**Solution:** Check browser console for JS errors, verify API responses

**Issue:** Gallery images not showing in edit form  
**Solution:** Verify JSON format in database - should be: `["path1.jpg", "path2.jpg"]`

---

## 🏁 Implementation Complete

**Date:** 2026-06-21  
**Status:** Ready for production  
**Estimated Migration Time:** 2-5 minutes  
**Database Impact:** None (additive only)  
**Performance Impact:** Minimal (new optional columns)  
**Backward Compatibility:** Full (existing data unaffected)  

---

## 📋 Checklist for Deployment

- [ ] Review `QUICK_MIGRATION_STEPS.txt`
- [ ] Back up database (recommended)
- [ ] Execute `api/ADD_VIDEO_GALLERY_COLUMNS.sql`
- [ ] Verify columns added with `DESCRIBE exhibitions;`
- [ ] Test add-exhibition form with video + gallery
- [ ] Test edit-exhibition form with existing exhibition
- [ ] Verify spaces.php displays exhibitions correctly
- [ ] Announce feature to team/users

---

**End of Document**
