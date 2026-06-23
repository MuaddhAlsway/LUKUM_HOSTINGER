# CRITICAL DATABASE SCHEMA FIX

## Problem Identified

✅ **Issue Found**: The `event_video` and `video_url` columns are **MISSING from the database tables**!

- **exhibitions table**: Missing `event_video` column
- **events table**: Missing `video_url` column

This is why all videos show as NULL in the console.

---

## The Fix (2 Options)

### OPTION 1: Automatic Migration (Recommended)

1. **Run Migration Script** (One-time only):
   ```
   Visit: http://yourdomain.com/api/MIGRATION_VIDEO_COLUMNS.php
   ```

2. **You'll see JSON response**:
   ```json
   {
     "success": true,
     "message": "Migration completed successfully",
     "details": {
       "exhibitions_event_video": {
         "status": "added",
         "message": "event_video column added to exhibitions table"
       },
       "events_video_url": {
         "status": "added",
         "message": "video_url column added to events table"
       }
     },
     "verification": {
       "exhibitions_event_video_exists": true,
       "events_video_url_exists": true
     }
   }
   ```

3. **Done!** Both columns are now in the database.

### OPTION 2: Manual SQL (If you prefer)

Run these SQL commands in phpMyAdmin:

```sql
-- Add event_video to exhibitions table
ALTER TABLE exhibitions 
ADD COLUMN event_video VARCHAR(500) 
COMMENT 'Event video URL (YouTube or Vimeo)' 
AFTER cover_image;

-- Add video_url to events table
ALTER TABLE events 
ADD COLUMN video_url VARCHAR(500) 
COMMENT 'Event video URL (YouTube or Vimeo)' 
AFTER cover_image;
```

---

## After Migration

### Step 1: Clear Browser Cache
```
Ctrl+Shift+Delete → All time → Delete
```

### Step 2: Test with Fresh Exhibition
1. Admin → Add Exhibition
2. Fill all required fields
3. **Add Video URL**: `https://youtu.be/dQw4w9WgXcQ`
4. Submit form
5. Go to Spaces → Click exhibition
6. **Video should now show!** ✅

### Step 3: Test with Fresh Event
1. Admin → Add Event
2. Fill all required fields
3. **Add Video URL**: `https://youtu.be/dQw4w9WgXcQ`
4. Submit form
5. Go to Calendar → Click event
6. **Video should now show!** ✅

### Step 4: Check Console
```
F12 → Console → Look for:
✅ event.video_url: "https://youtu.be/..."
✅ event.event_video: "https://youtu.be/..."
✅ Final videoUrl: "https://youtu.be/..."
```

---

## Database Schema After Fix

### exhibitions table
```sql
...
`cover_image` VARCHAR(500),
`event_video` VARCHAR(500),          ← NEW!
`gallery_images` LONGTEXT,
...
```

### events table
```sql
...
`cover_image` VARCHAR(500),
`video_url` VARCHAR(500),            ← NEW!
`category` VARCHAR(50),
...
```

---

## Complete System Flow After Fix

```
UPCOMING EVENT (events table):
Admin adds video → events.video_url ← NEW COLUMN! ✅
  ↓
API get_event_details.php
  ├─ Selects: video_url, video_url as event_video
  └─ Returns BOTH fields ✅
  ↓
event.php checks: video_url || event_video
  └─ Finds value ✅
  ↓
displayVideo() embeds player ✅

PAST EXHIBITION (exhibitions table):
Admin adds video → exhibitions.event_video ← NEW COLUMN! ✅
  ↓
API get_event_details.php
  ├─ Selects: event_video as video_url, event_video
  └─ Returns BOTH fields ✅
  ↓
event.php checks: video_url || event_video
  └─ Finds value ✅
  ↓
displayVideo() embeds player ✅
```

---

## Files Created

1. **MIGRATION_VIDEO_COLUMNS.php** - Automatic migration script
   - Run once via: http://yourdomain.com/api/MIGRATION_VIDEO_COLUMNS.php
   - Adds missing columns to both tables
   - Verifies columns were added successfully

2. **migrate_add_video_column.php** - Alternative migration script
   - Adds only event_video to exhibitions

3. **ADD_EVENT_VIDEO_COLUMN.sql** - SQL file for manual migration
   - Can be imported in phpMyAdmin

---

## Timeline

1. ✅ Run migration (1 minute)
2. ✅ Clear browser cache (1 minute)
3. ✅ Add new exhibition with video (2 minutes)
4. ✅ Test on event page (2 minutes)
5. ✅ Add new event with video (2 minutes)
6. ✅ Test on event page (2 minutes)

**Total: ~10 minutes** ⏱️

---

## What Happens to Existing Data?

- ✅ No existing data is lost
- ✅ Old events/exhibitions still display
- ✅ New events/exhibitions with videos will display videos
- ✅ Old events/exhibitions without videos will have empty video section (hidden)

---

## Verification After Migration

### Check 1: Verify Columns Exist
```sql
DESCRIBE exhibitions;     -- Should show event_video column
DESCRIBE events;          -- Should show video_url column
```

### Check 2: Test Form Submission
1. Admin → Add Exhibition
2. Add video URL
3. Submit
4. Check database:
   ```sql
   SELECT event_video FROM exhibitions ORDER BY id DESC LIMIT 1;
   ```
   Should show your URL, not NULL

### Check 3: Test API
```
http://yourdomain.com/api/get_event_details.php?id=LAST_ID&lang=en
```
Should return:
```json
{
  "event": {
    "video_url": "https://youtu.be/...",
    "event_video": "https://youtu.be/..."
  }
}
```

### Check 4: Test Event Page
1. Go to Spaces or Calendar
2. Click event/exhibition
3. F12 → Console
4. Should show "VIDEO URL CHECK" section with values
5. Should see "Event Video" heading on page
6. YouTube/Vimeo player should be embedded

---

## Summary

**Root Cause**: Database schema was missing the video columns

**Solution**: Run MIGRATION_VIDEO_COLUMNS.php once

**Result**: Both tables now have video columns, system works! ✅

🎉 **Ready to use after migration!**

---

## Rollback (If Needed)

If something goes wrong, you can remove the columns:

```sql
ALTER TABLE exhibitions DROP COLUMN event_video;
ALTER TABLE events DROP COLUMN video_url;
```

But this shouldn't be necessary - the migration is safe and non-destructive.
