# Migration Guide: Add Video & Gallery Columns to Exhibitions Table

## Overview
This guide explains how to add Event Video and Gallery Images support to your existing exhibitions table in MySQL.

**Files Needed:**
- `api/ADD_VIDEO_GALLERY_COLUMNS.sql` - Contains the SQL migration commands

---

## ⚡ Quick Steps

### Step 1: Access Your Database
Choose ONE method:

#### Method A: PhpMyAdmin (Easiest)
1. Open PhpMyAdmin in your browser
2. Log in with your database credentials:
   - Host: `localhost`
   - Username: `u812122863_neama`
   - Password: `Nema202610!LakumDB`
3. Select database: `u812122863_lakum_artspace`
4. Click the **SQL** tab at the top

#### Method B: MySQL Command Line
```bash
mysql -h localhost -u u812122863_neama -p u812122863_lakum_artspace
# Enter password when prompted: Nema202610!LakumDB
```

---

### Step 2: Run the Migration SQL

#### Via PhpMyAdmin:
1. Open `api/ADD_VIDEO_GALLERY_COLUMNS.sql` in a text editor
2. Copy ALL the SQL code (from the first ALTER TABLE to the end)
3. Paste into the PhpMyAdmin SQL Query box
4. Click the **Go** button
5. Wait for success message ✓

#### Via MySQL CLI:
```bash
mysql -h localhost -u u812122863_neama -p u812122863_lakum_artspace < api/ADD_VIDEO_GALLERY_COLUMNS.sql
# Enter password when prompted
```

---

### Step 3: Verify the Migration

Run this verification query in PhpMyAdmin SQL tab:

```sql
DESCRIBE exhibitions;
```

**Expected Output:** You should see these new columns at the end:
- `event_video` - VARCHAR(500)
- `gallery_images` - LONGTEXT

---

## 📋 What Gets Added

### New Column 1: `event_video`
- **Type:** VARCHAR(500)
- **Purpose:** Store a single video URL (optional)
- **Accepts:**
  - YouTube embed URLs: `https://www.youtube.com/embed/VIDEO_ID`
  - Vimeo URLs: `https://vimeo.com/VIDEO_ID`
  - Direct video files: `assest/video.mp4`
- **Nullable:** YES (can be empty)

### New Column 2: `gallery_images`
- **Type:** LONGTEXT
- **Purpose:** Store multiple gallery images as JSON array (optional)
- **Format Example:** `["assest/img-1.jpg", "assest/img-2.jpg", "assest/img-3.jpg"]`
- **Capacity:** Supports 100+ images per exhibition
- **Nullable:** YES (can be empty)

---

## ✅ Verification Queries

Run these in PhpMyAdmin to confirm everything works:

### Check if columns exist:
```sql
SELECT COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_NAME = 'exhibitions' 
AND TABLE_SCHEMA = 'u812122863_lakum_artspace' 
AND COLUMN_NAME IN ('event_video', 'gallery_images');
```

**Expected Result:** 2 rows showing event_video and gallery_images

### View table structure:
```sql
DESCRIBE exhibitions;
```

### View a specific exhibition with new columns:
```sql
SELECT id, title_en, event_video, gallery_images FROM exhibitions LIMIT 1;
```

---

## 🔄 How the Forms Work Now

### Adding Exhibition (`admin/add-exhibition.html`)
1. Fill in exhibition details (title, date, location, etc.)
2. **Optional:** Add Event Video URL in the "Event Video (Optional)" section
3. **Optional:** Upload multiple images for the gallery in the "Gallery Images (Optional)" section
4. Click "Create Exhibition"

**Behind the scenes:**
- Video URL is stored as-is in `event_video` column
- Gallery images are uploaded and stored as JSON array in `gallery_images` column

### Editing Exhibition (`admin/edit-exhibition.html`)
1. All existing exhibition data loads (including new video/gallery fields if present)
2. **Existing gallery images** display as thumbnails in the "Current Gallery" section
3. **Optional:** Add more images to the "Upload Additional Images" section
4. **Optional:** Update or clear the video URL
5. Click "Update Exhibition"

**Behind the scenes:**
- New gallery images are uploaded and appended to the JSON array
- Video field is updated or cleared based on input
- Old gallery images are preserved unless explicitly replaced

---

## ⚠️ Important Notes

- **Existing exhibitions:** Will have `NULL` for video and gallery columns (no data loss)
- **No data migration needed:** This is purely adding new columns
- **Performance:** No performance impact - these are optional columns
- **Backward compatible:** Old exhibitions work exactly the same

---

## 🆘 Troubleshooting

### Problem: "Column already exists" error
**Solution:** The columns are already added. Skip this migration and use the forms.

### Problem: "Access denied" error
**Solution:** Check your database credentials:
- Host: `localhost`
- User: `u812122863_neama`
- Password: `Nema202610!LakumDB`
- Database: `u812122863_lakum_artspace`

### Problem: Migration doesn't run
**Solution:** Make sure you:
1. Copied the ENTIRE SQL file (not just one line)
2. Are connected to the correct database
3. Have proper database permissions

---

## 🔄 Rollback (Undo Migration)

If you need to remove these columns:

```sql
ALTER TABLE exhibitions DROP COLUMN event_video;
ALTER TABLE exhibitions DROP COLUMN gallery_images;
```

⚠️ **Warning:** This will delete any video URLs and gallery image data already saved!

---

## 📞 Support

If you encounter issues:
1. Check the Troubleshooting section above
2. Verify database connection details
3. Run verification queries to confirm migration success
4. Check that `api/ADD_VIDEO_GALLERY_COLUMNS.sql` file exists

---

## ✨ What's Next

After migration:
1. Open `admin/exhibitions.html` in your browser
2. Try adding a new exhibition with video and gallery images
3. Test editing an existing exhibition
4. Check that images appear on `spaces.php` in the Past Events section

---

**Migration Status:** Ready to deploy
**Date:** 2026-06-21
**Affected Table:** exhibitions
**New Columns:** 2 (event_video, gallery_images)
