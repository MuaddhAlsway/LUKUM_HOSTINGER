# FIX: Exhibitions Table Missing Columns

## Problem
Error: `Unknown column 'event_video' in 'INSERT INTO'`

The exhibitions table is missing required columns:
- ❌ `event_video` (for storing video URLs)
- ❌ `gallery_images` (for storing gallery image JSON)

---

## Solution: Add Missing Columns

### Automated Fix (Recommended)

Visit: `http://localhost/api/add_missing_exhibition_columns.php`

This will:
1. Check exhibitions table structure
2. Add `event_video` column if missing
3. Add `gallery_images` column if missing
4. Verify all columns exist
5. Show next steps

**Expected response:**
```json
{
  "success": true,
  "added_columns": ["event_video", "gallery_images"],
  "all_columns": [...],
  "message": "Successfully added 2 column(s)",
  "next_step": "Run migration: /api/migrate_exhibitions.php"
}
```

---

## Step-by-Step Fix

### Step 1: Check Table Structure
Visit: `http://localhost/api/check_exhibitions_structure.php`

Shows:
- All columns in exhibitions table
- Whether `event_video` column exists
- What columns are missing

### Step 2: Add Missing Columns
Visit: `http://localhost/api/add_missing_exhibition_columns.php`

Adds:
- `event_video` - VARCHAR(500) for video URLs
- `gallery_images` - LONGTEXT for JSON gallery array

### Step 3: Verify Columns Added
Visit: `http://localhost/api/check_exhibitions_structure.php` again

Should now show:
- event_video column exists ✅
- gallery_images column exists ✅

### Step 4: Migrate Exhibition Data
Visit: `http://localhost/api/migrate_exhibitions.php`

This will:
- Copy events marked as exhibitions to exhibitions table
- Show how many were migrated
- Ready for event.php to use

### Step 5: Test
Visit: `http://localhost/event.php?title=ampa`

Should display exhibition ✅

---

## What Columns Are Needed

| Column | Type | Purpose | Required |
|--------|------|---------|----------|
| id | INT | Primary key | ✅ |
| title_en | VARCHAR(255) | English title | ✅ |
| title_ar | VARCHAR(255) | Arabic title | ✅ |
| description_en | LONGTEXT | English description | ✅ |
| description_ar | LONGTEXT | Arabic description | ✅ |
| location_en | VARCHAR(255) | English location | ✅ |
| location_ar | VARCHAR(255) | Arabic location | ✅ |
| exhibition_date | DATE | Start date | ✅ |
| exhibition_time | TIME | Start time | ✅ |
| exhibition_end_time | TIME | End time | ✅ |
| end_date | DATE | Multi-day end date | ✅ |
| cover_image | VARCHAR(500) | Cover image path | ✅ |
| **event_video** | VARCHAR(500) | Video URL | ✅ Missing! |
| **gallery_images** | LONGTEXT | JSON gallery array | ✅ Missing! |
| category | VARCHAR(50) | Type (exhibition) | ✅ |
| is_featured | TINYINT(1) | Featured flag | ✅ |
| created_at | TIMESTAMP | Creation date | ✅ |
| updated_at | TIMESTAMP | Update date | ✅ |

---

## Why Columns Are Missing

### Old Table Structure:
- Created without video and gallery columns
- Manual addition needed

### New Requirements:
- `event_video` - Store video URLs from admin panel
- `gallery_images` - Store gallery images as JSON array

### Solution:
- Add columns dynamically via script
- Maintain backward compatibility
- No data loss

---

## Verification

### Before Fix:
```
exhibitions table columns: [id, title_en, title_ar, description_en, description_ar, location_en, location_ar, exhibition_date, exhibition_time, exhibition_end_time, end_date, cover_image, category, is_featured, created_at, updated_at]
❌ Missing: event_video, gallery_images
```

### After Fix:
```
exhibitions table columns: [id, title_en, title_ar, description_en, description_ar, location_en, location_ar, exhibition_date, exhibition_time, exhibition_end_time, end_date, cover_image, event_video, gallery_images, category, is_featured, created_at, updated_at]
✅ All columns present
```

---

## Complete Workflow

```
1. Check structure
   ↓
2. Add missing columns
   ↓
3. Verify columns added
   ↓
4. Migrate exhibition data from events table
   ↓
5. Test on event.php
   ↓
SUCCESS! ✅
```

---

## If Manual Column Addition Needed

If automated script doesn't work, run these SQL commands directly:

```sql
-- Add event_video column
ALTER TABLE exhibitions 
ADD COLUMN event_video VARCHAR(500) NULL DEFAULT NULL 
AFTER cover_image;

-- Add gallery_images column  
ALTER TABLE exhibitions
ADD COLUMN gallery_images LONGTEXT NULL DEFAULT NULL
AFTER event_video;
```

---

## Troubleshooting

### Error: "Table exhibitions doesn't exist"
- First create exhibitions table
- Use: `http://localhost/api/EXHIBITIONS_TABLE_FINAL.sql`

### Error: "Column already exists"
- Column is already there, you can proceed to migration
- Visit: `http://localhost/api/migrate_exhibitions.php`

### Error: "Access denied"
- Database user doesn't have ALTER TABLE permission
- Contact hosting provider or use phpMyAdmin

### Migration shows "0 rows"
- No events with category='exhibition' found
- Create exhibitions via admin panel first
- Then they'll be available for event.php

---

## Testing After Fix

### Test 1: Check Columns
```
Visit: http://localhost/api/check_exhibitions_structure.php
Look for: "has_event_video": true
```

### Test 2: Add Exhibition
```
Admin Panel → Exhibitions → Add Exhibition
Fill all fields including video URL
Click Save
```

### Test 3: View on event.php
```
Visit: http://localhost/event.php?title=<exhibition-name>
Should display with video if added ✅
```

---

## Summary

**Current State:**
- exhibitions table exists ✅
- Missing event_video column ❌
- Missing gallery_images column ❌
- Can't save exhibitions with videos ❌

**After Fix:**
- exhibitions table exists ✅
- event_video column added ✅
- gallery_images column added ✅
- Can save exhibitions with videos ✅
- event.php displays them ✅

**Quick Fix:** 
Visit: `http://localhost/api/add_missing_exhibition_columns.php`

Then test: `http://localhost/event.php?title=ampa`

✅ **Done!**

