# NEXT STEP: Run Migration NOW ✅

## Status: Columns Added Successfully! 

✅ event_video column added
✅ gallery_images column added
✅ All 18 columns now present

**Next Action:** Migrate exhibition data from events table to exhibitions table

---

## Run Migration

Visit: `http://localhost/api/migrate_exhibitions.php`

This will:
1. Find all events with `category='exhibition'`
2. Copy them to exhibitions table
3. Map video_url → event_video
4. Show results

**Expected Response:**
```json
{
  "success": true,
  "migrated_count": X,
  "message": "Successfully migrated X exhibition events to exhibitions table"
}
```

---

## Then Test

### Test 1: Verify Data Migrated
```
Visit: http://localhost/api/check_all_data.php
```
Should show exhibitions count > 0 ✅

### Test 2: Test event.php
```
Visit: http://localhost/event.php?title=ampa
```
Should display the AMPM exhibition ✅

### Test 3: Check Console
F12 → Console → Look for:
```
DEBUG: FOUND in exhibitions table with ID: 5
```
Or:
```
DEBUG: Found exhibition via PHP strpos fuzzy match: 5
```

---

## Complete Workflow Summary

```
✅ Step 1: Added event_video column
✅ Step 2: Added gallery_images column
⏭️  Step 3: RUN MIGRATION NOW
   → Visit: /api/migrate_exhibitions.php
⏭️  Step 4: Verify migration
   → Visit: /api/check_all_data.php
⏭️  Step 5: Test event.php
   → Visit: /event.php?title=ampa
```

---

## GO NOW 🚀

**Click here:** `http://localhost/api/migrate_exhibitions.php`

Then everything will work! ✅

