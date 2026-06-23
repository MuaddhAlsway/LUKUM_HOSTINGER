# EXHIBITIONS TABLE IS EMPTY - Migration Required

## Problem
The exhibitions table has **0 rows**. That's why nothing is being found - there's no data to search!

The data exists in the **events table** but not in the **exhibitions table**.

---

## Solution: Migrate Exhibition Data

### Option 1: Automatic Migration (Recommended)

Visit this URL: `http://localhost/api/migrate_exhibitions.php`

This will:
1. Find all events with `category='exhibition'` in events table
2. Copy them to exhibitions table with correct columns
3. Map `video_url` → `event_video`
4. Preserve all event data

**Expected response:**
```json
{
  "success": true,
  "migrated_count": X,
  "message": "Successfully migrated X exhibition events to exhibitions table"
}
```

### Option 2: Check Data First

Before migrating, see what exists:
Visit: `http://localhost/api/check_all_data.php`

This shows:
- How many events exist
- How many exhibitions exist
- Sample of both tables
- Recommendations

---

## Step-by-Step Guide

### Step 1: Check Current Data
```
Visit: http://localhost/api/check_all_data.php
```

**Look for:**
- `"exhibitions": { "total_count": 0 }` ← Empty (problem)
- `"events": { "total_count": X }` ← Has data

### Step 2: Migrate if Empty
```
If exhibitions is empty:
Visit: http://localhost/api/migrate_exhibitions.php
```

**Wait for:**
- `"success": true`
- `"migrated_count": X` (should be > 0)

### Step 3: Verify Migration
```
Visit: http://localhost/api/check_all_data.php
```

**Look for:**
- `"exhibitions": { "total_count": X }` ← Should now have data!

### Step 4: Test event.php
```
Visit: http://localhost/event.php?title=ampa
```

**Should now:** Display the exhibition ✅

---

## What Gets Migrated

| Field | From | To |
|-------|------|-----|
| id | events.id | exhibitions.id |
| title | events.title_en | exhibitions.title_en |
| description | events.description_en | exhibitions.description_en |
| location | events.location_en | exhibitions.location_en |
| date | events.event_date | exhibitions.exhibition_date |
| time | events.event_time | exhibitions.exhibition_time |
| video | events.video_url | exhibitions.event_video |
| image | events.cover_image | exhibitions.cover_image |
| language fields | events.title_ar, description_ar, location_ar | exhibitions (same) |

---

## Why This Happened

### Old System:
- All events/exhibitions stored in `events` table
- Column `category='exhibition'` marks them as exhibitions

### New System:
- Exhibitions moved to separate `exhibitions` table
- Both tables still needed for backward compatibility
- Data needs to be migrated to exhibitions table

### Solution:
- Migration script copies exhibition events to exhibitions table
- Both tables can coexist
- API checks both tables (exhibitions first)

---

## Automatic Actions

The migration script will:
1. Find events with `category='exhibition'`
2. Skip if already in exhibitions table (no duplicates)
3. Copy all data to exhibitions table
4. Preserve IDs so URLs still work
5. Map video fields correctly

---

## What Happens After Migration

### Before:
```
events table (contains 22 events, 5 are exhibitions)
exhibitions table (empty - 0 rows)
```

### After Migration:
```
events table (still contains 22 events - unchanged)
exhibitions table (now contains 5 exhibitions - copied from events)
```

### URLs Work Like:
```
/ampa → /event.php?title=ampa
  ↓
API checks exhibitions table FIRST
  ↓
Finds exhibition "AMPM" (ID 5)
  ↓
Displays exhibition ✅
```

---

## If Migration Fails

**Error: "Exhibitions table does not exist"**
- Run: `api/EXHIBITIONS_TABLE_FINAL.sql`
- This creates the exhibitions table

**Error: "Bind error" or "Execute error"**
- Check error message for specific issue
- Verify exhibitions table has all required columns
- Try creating fresh exhibitions table and running migration again

**Error: "0 rows migrated"**
- Check `api/check_all_data.php`
- Look for events with `category='exhibition'`
- If none found, exhibitions don't exist to migrate
- Need to create them via admin panel

---

## Testing After Migration

### Test 1: Check Data
```
Visit: http://localhost/api/check_all_data.php
```
Should show exhibitions count > 0

### Test 2: Test Slug
```
Visit: http://localhost/event.php?title=ampa
```
Should display exhibition

### Test 3: Console Check
F12 → Console → Look for:
```
DEBUG: FOUND in exhibitions table with ID: 5
```
OR
```
DEBUG: Found exhibition via PHP strpos fuzzy match: 5
```

Either means it's working!

---

## Recovery if Something Goes Wrong

If migration causes issues:

**1. Rollback: Delete from exhibitions table**
```sql
DELETE FROM exhibitions;
```

**2. API will fall back to events table**
- Events table still has original data
- System keeps working

**3. Try migration again**
- Or manually add exhibitions via admin panel

---

## Performance Impact

- Migration: ~1 second for 100 records
- One-time operation
- No ongoing performance impact
- System works same speed after

---

## Summary

**Current State:**
- exhibitions table: 0 rows ❌
- events table: X rows ✅
- Result: "Exhibition not found" errors

**After Migration:**
- exhibitions table: X rows ✅
- events table: X rows ✅ (unchanged)
- Result: event.php works perfectly ✅

**Next Action:** 
1. Visit `http://localhost/api/check_all_data.php`
2. If exhibitions = 0, visit `http://localhost/api/migrate_exhibitions.php`
3. Test: `http://localhost/event.php?title=ampa`

🚀 **3 clicks to fix!**

