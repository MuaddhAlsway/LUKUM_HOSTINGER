# Diagnostic: Video URL Saved But Not Showing on Event Page

## Problem
- Video URL entered in exhibition admin form
- Exhibition shows on spaces.php Past Events ✓
- But clicking it doesn't show video on event page ✗

## Root Cause Analysis

### Hypothesis 1: Video NOT Being Saved to Database
**Check this first:**
1. Open phpMyAdmin
2. Go to `exhibitions` table
3. Find the exhibition you just created
4. Check the `event_video` column
5. Is the URL there?

**If YES**: Go to Hypothesis 2  
**If NO**: The save is failing

### Hypothesis 2: Video Saved But API Not Returning It
**To check:**
1. Open the event page with video
2. F12 → Network tab
3. Find request: `get_event_details.php`
4. Click on it
5. Check Response tab
6. Search for: `"video_url"` or `"event_video"`
7. Is the URL there?

**If YES**: Go to Hypothesis 3  
**If NO**: API not returning it

### Hypothesis 3: Video Returned But event.php Not Using It
**To check:**
1. Event page with video
2. F12 → Console
3. Look for logs:
   - "Checking for video_url..."
   - "Video URL found, calling displayVideo with: https://..."
4. Do you see these logs?

**If YES**: Video is displaying (check if it's off-screen)  
**If NO**: event.php not running the code

### Hypothesis 4: Browser Cache
**Most likely culprit!**

**Solution:**
1. Hard refresh: Ctrl+Shift+R (Windows/Linux) or Cmd+Shift+R (Mac)
2. Or Ctrl+F5
3. Or Shift+Click refresh button
4. Wait 5 seconds
5. Try again

---

## Step-by-Step Diagnostic

### Step 1: Verify Database
```sql
-- Run this in phpMyAdmin
SELECT id, title_en, event_video FROM exhibitions WHERE id = 54;

Expected result:
id | title_en         | event_video
54 | Cheval Blanc     | https://youtu.be/JH3zXmuFARw?si=...
```

**Result**: `event_video` is NULL or has URL?

### Step 2: Test API Directly
**Open browser address bar and go to:**
```
http://localhost/api/get_event_details.php?id=54&lang=en
```

**You should see JSON with:**
```json
{
  "success": true,
  "event": {
    "id": 54,
    "title": "Cheval Blanc",
    "video_url": "https://youtu.be/JH3zXmuFARw?si=...",
    "event_video": "https://youtu.be/JH3zXmuFARw?si=...",
    ...
  },
  "gallery": [...]
}
```

**Check**: Is `video_url` or `event_video` present?

### Step 3: Check Browser Console
1. Go to event page
2. F12 → Console tab
3. Look for logs:
   ```
   Checking for video_url...
   Video URL found, calling displayVideo with: https://youtu.be/...
   ```

**Check**: Are these logs appearing?

### Step 4: Check Network Tab
1. Go to event page
2. F12 → Network tab
3. Reload page
4. Look for: `get_event_details.php`
5. Click on it
6. See Response tab
7. Search for `"video_url"`

**Check**: Is it in the response?

---

## Most Common Causes & Fixes

### Cause 1: Browser Cache (80% probability)
**Fix**: Hard refresh
- Windows: Ctrl+Shift+R
- Mac: Cmd+Shift+R
- Firefox: Ctrl+F5

### Cause 2: PHP Code Not Updated
**Fix**: Check if file was really saved
- Open `event.php`
- Search for: `const videoUrl = event.video_url || event.event_video;`
- Is it there?

### Cause 3: Video URL Not Saved to Database
**Fix**: Add video again and check database
- Go to admin
- Edit exhibition
- Paste video URL
- Click Update
- Check phpMyAdmin

### Cause 4: API Not Returning Field
**Fix**: Check if query includes `event_video`
- Open `api/get_event_details.php`
- Search for: `ex.event_video`
- Should see: `ex.event_video as video_url,` and `ex.event_video,`

### Cause 5: JavaScript Error
**Fix**: Check console for errors
- F12 → Console
- Any red error messages?
- Check Network tab for failed requests

---

## Quick Fix Checklist

- [ ] Hard refresh browser (Ctrl+Shift+R)
- [ ] Check database has video URL
- [ ] Check API returns video_url
- [ ] Check console for errors
- [ ] Check Network tab
- [ ] Verify event.php has the code
- [ ] Try different exhibition
- [ ] Try different video URL

---

## Specific Tests

### Test 1: Create New Exhibition with Video
1. Go to `/admin/add-exhibition.html`
2. Create brand new exhibition with video
3. Name it: "TEST_VIDEO_" + timestamp
4. Save
5. Check database
6. Go to spaces.php, click it
7. Check if video shows

### Test 2: Manually Query Database
```sql
SELECT * FROM exhibitions WHERE id = 54;
```
Copy the `event_video` value. Is it NULL or a URL?

### Test 3: Test API Response
Open in browser:
```
http://localhost/api/get_event_details.php?id=54&lang=en
```

Search page for `"video_url"`. Is it there?

### Test 4: Check Console Logs
1. Go to `/event.php?title=cheval-blanc&lang=en`
2. F12 → Console
3. Type: `console.log(currentEvent)`
4. Should show the event object with `video_url` field

---

## If Nothing Works

1. **Check if exhibitiontable has `event_video` column**:
   ```sql
   SHOW COLUMNS FROM exhibitions LIKE 'event_video';
   ```
   If empty, column doesn't exist - need to add it

2. **Check if database column is actually storing data**:
   ```sql
   SELECT event_video FROM exhibitions WHERE event_video IS NOT NULL LIMIT 5;
   ```
   Should show some URLs if data exists

3. **Check API SQL query**:
   - Open `api/get_event_details.php`
   - Verify query includes: `ex.event_video as video_url,`

4. **Check JavaScript for errors**:
   - F12 → Console
   - Any red errors?
   - Check Network for 404s

---

## Expected vs Actual

### Expected Flow
```
User enters video URL
    ↓
Click Create/Update
    ↓
Form sends event_video: "https://..."
    ↓
API saves to database
    ↓
User goes to event page
    ↓
API returns video_url field
    ↓
event.php checks video_url
    ↓
Video displays ✓
```

### Actual Flow (Broken)
```
Video URL entered
    ↓
Saved to database (?)
    ↓
Event page loads
    ↓
Video doesn't show ✗
```

---

## Information to Provide

When reporting the issue, please provide:

1. **Exhibition ID** (e.g., 54)
2. **Exhibition name** (e.g., "Cheval Blanc")
3. **Video URL** you entered (e.g., "https://youtu.be/...")
4. **Database check**: Is URL in `event_video` column? YES/NO
5. **API check**: Go to API URL, is URL in response? YES/NO
6. **Console logs**: Do you see "Video URL found" log? YES/NO
7. **Browser**: Chrome/Firefox/Safari
8. **Tried hard refresh**: YES/NO

---

## Solution Summary

**If video URL is in database AND API returns it BUT event page doesn't show it:**
- Most likely: Browser cache
- Fix: Hard refresh (Ctrl+Shift+R)

**If video URL is NOT in database:**
- Problem: Save is failing
- Check: Look at admin form, is URL being sent to API?
- Check: Open browser Network tab when saving, what response do you get?

**If API doesn't return it:**
- Problem: get_event_details.php not returning event_video field
- Check: Is the SELECT statement including ex.event_video?

---

**Use this diagnostic guide to find the exact cause.**

Report back with:
1. Is video in database? (YES/NO)
2. Is video in API response? (YES/NO)
3. Are console logs showing? (YES/NO)
4. Browser (Chrome/Firefox/etc)

Then I can pinpoint the exact issue.
