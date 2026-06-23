# Fix Event 76 (AMPM) - Add Video & Verify Display

## Problem Found

Event ID 76 (AMPM) doesn't have a video URL. The `add_video_to_ampm.php` script couldn't find it because it might be in the **events** table instead of **exhibitions** table.

**Fixed:** Updated script now checks BOTH tables automatically and updates whichever one contains ID 76.

---

## Solution - 3 Steps

### Step 1: Identify Which Table Has ID 76

**Visit:** `http://localhost/api/check_event_76.php`

This will show:
- Which table contains ID 76 (events or exhibitions)
- The current data in that record
- Table schemas
- Total records in each table

**Example output:**
```json
{
  "checks": [
    {
      "table": "events",
      "found": true,
      "data": {
        "id": 76,
        "title": "AMPM",
        "video_url": null,
        "category": "exhibition"
      }
    }
  ],
  "table_counts": {
    "events": 22,
    "exhibitions": 2
  }
}
```

---

### Step 2: Add Video to Event 76

**Visit:** `http://localhost/api/add_video_to_ampm.php`

This script will:
- Check if ID 76 exists in events table → Update events table
- OR if ID 76 exists in exhibitions table → Update exhibitions table
- Add video URL: `https://youtu.be/JH3zXmuFARw?si=D7Drn3PjWR-uQdpm`

**Expected response:**
```json
{
  "success": true,
  "message": "Video added to Event ID 76",
  "table": "events",
  "event_id": 76,
  "video_url": "https://youtu.be/JH3zXmuFARw?si=D7Drn3PjWR-uQdpm",
  "affected_rows": 1
}
```

---

### Step 3: Verify Video Displays

**Clear cache:** `Ctrl+Shift+R`

**Visit:** `http://localhost/event.php?id=76&lang=en`

**Expected:**
- ✅ Title: "AMPM"
- ✅ Description displays
- ✅ Gallery visible
- ✅ **Video section appears below gallery**
- ✅ YouTube player visible
- ✅ Can play/pause video

**Open F12 Console:**
- ✅ Should see: `✅ VIDEO FOUND!`
- ✅ Should see: `📺 Detected YouTube URL`
- ✅ Should see: `✅ Video section now visible`
- ❌ Should NOT see: Red error messages

---

## If Still Not Working

### Scenario 1: Script says "not found in either table"
- ID 76 doesn't exist in database
- Check your database - may use different ID system
- Contact support with ID number

### Scenario 2: Page shows "Loading..." forever
- Clear browser cache completely
- Check F12 Console for error messages
- Report error messages

### Scenario 3: Video added but not displaying
- Reload with `Ctrl+Shift+R`
- Check console for "✅ VIDEO FOUND!"
- If not found: video URL might not have saved properly
- Try adding manually via admin form

---

## Alternative: Add Video Manually

If the script doesn't work, add video through admin:

1. **Go to:** Admin Dashboard → Events (or Exhibitions)
2. **Find:** Event/Exhibition ID 76 (AMPM)
3. **Click:** Edit
4. **Find:** Event Video section
5. **Paste:** `https://youtu.be/JH3zXmuFARw?si=D7Drn3PjWR-uQdpm`
6. **Click:** Preview (should show video in preview)
7. **Click:** Save
8. **Test:** Reload event page - video should display

---

## Complete Workflow

```
1. Run: http://localhost/api/check_event_76.php
   ↓
2. Note which table has ID 76
   ↓
3. Run: http://localhost/api/add_video_to_ampm.php
   ↓
4. Verify: {"success": true}
   ↓
5. Clear cache: Ctrl+Shift+R
   ↓
6. Visit: http://localhost/event.php?id=76&lang=en
   ↓
7. Verify: Title, description, gallery, VIDEO all display
   ↓
8. ✅ SUCCESS!
```

---

## Verification Commands

### Check if video was added:
```sql
SELECT id, title, video_url FROM events WHERE id = 76;
-- OR
SELECT id, title_en, event_video FROM exhibitions WHERE id = 76;
```

### Verify video is not null:
```sql
SELECT id, title, video_url FROM events WHERE id = 76 AND video_url IS NOT NULL;
```

### Count events with videos:
```sql
SELECT COUNT(*) FROM events WHERE video_url IS NOT NULL;
```

---

## Expected Final Result

### Before:
```
Event ID 76 (AMPM):
  Title: "AMPM" ✅
  Description: "An everyday luxury lifestyle brand..." ✅
  Location: "Hall 1" ✅
  Cover image: Shows ✅
  Gallery: Images display ✅
  Video: ❌ NOT VISIBLE (video_url was null)
```

### After (Following This Guide):
```
Event ID 76 (AMPM):
  Title: "AMPM" ✅
  Description: "An everyday luxury lifestyle brand..." ✅
  Location: "Hall 1" ✅
  Cover image: Shows ✅
  Gallery: Images display ✅
  Video: ✅ VISIBLE & PLAYABLE (video_url set to YouTube URL)
```

---

## Files Created

- `api/check_event_76.php` - Identifies which table has ID 76
- `api/add_video_to_ampm.php` - Adds video to whichever table has ID 76 (UPDATED)

---

## Next Steps

1. **Now:** Run `http://localhost/api/check_event_76.php`
2. **Then:** Run `http://localhost/api/add_video_to_ampm.php`
3. **Test:** Visit `http://localhost/event.php?id=76&lang=en`
4. **Verify:** Video displays with rest of content
5. **Deploy:** Upload event.php to Hostinger

---

**You're 3 clicks away from a fully working event page with video!** 🚀
