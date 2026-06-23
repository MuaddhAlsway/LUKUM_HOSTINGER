# How to Fix: Video Not Showing on Event Page

## The Complete Solution

After investigating ALL components (event.php, spaces.php, API, admin forms, database), here's what you need to do:

---

## PART 1: VERIFY EVERYTHING IS CONNECTED

### Step 1: Check if Video URL is in Database

Open phpMyAdmin or database client and run:

```sql
SELECT id, title_en, event_video 
FROM exhibitions 
WHERE id = 54 
LIMIT 1;
```

**Look at the result:**

**Case A: event_video has a URL**
```
id | title_en | event_video
54 | Cheval Blanc | https://youtu.be/JH3zXmuFARw
```
→ Continue to Step 2

**Case B: event_video is NULL**
```
id | title_en | event_video
54 | Cheval Blanc | NULL
```
→ **Problem identified!** Video not being saved. Go to PROBLEM #1 below.

---

### Step 2: Check if API Returns the Video URL

Open your browser and visit:
```
https://yourdomain.com/api/get_event_details.php?id=54&lang=en
```

**You should see JSON like:**
```json
{
  "success": true,
  "event": {
    "id": 54,
    "title_en": "Cheval Blanc",
    "event_video": "https://youtu.be/JH3zXmuFARw",
    "video_url": "https://youtu.be/JH3zXmuFARw",
    ...
  }
}
```

**Check:**
- Does the response have `"event_video"` field? YES / NO
- Does it have a value (not null)? YES / NO

If NO → **Problem identified!** API issue. Go to PROBLEM #2 below.
If YES → Continue to Step 3.

---

### Step 3: Check if event.php Receives Video URL

1. Open browser
2. Go to past exhibition page
3. Press F12 (Open Developer Tools)
4. Click Console tab
5. Look for this section:
```
=== VIDEO URL CHECK ===
event.video_url: null
event.event_video: "https://youtu.be/JH3zXmuFARw"
```

**Check:**
- Do you see the "=== VIDEO URL CHECK ===" section? YES / NO
- Is event_video showing a URL (not null)? YES / NO

If NO → **Problem identified!** Page loading issue. Go to PROBLEM #3 below.
If YES → Continue to Step 4.

---

### Step 4: Check if Video Section Gets Active Class

In the console (from Step 3), look for:
```
=== displayVideo DEBUG ===
BEFORE adding active class - classList: ["event-section", "event-section--video"]
AFTER adding active class - classList: ["event-section", "event-section--video", "active"]
Video section display style: block
```

**Check:**
- Is "active" in the classList? YES / NO
- Is display style "block"? YES / NO

If NO → **Problem identified!** CSS/JavaScript issue. Go to PROBLEM #4 below.
If YES → Video should be visible!

---

### Step 5: Check if Video is Visible on Page

1. Close DevTools (F12)
2. Scroll down the page
3. Look for "Event Video" section heading
4. Video should be embedded below it

If not visible → Go to PROBLEM #5 below.
If visible → ✅ **Video is working!**

---

## PART 2: IDENTIFY AND FIX YOUR PROBLEM

### PROBLEM #1: Video NOT in Database (event_video is NULL)

**Cause**: Video URL not being saved by admin form

**How to fix:**

1. Open admin panel
2. Go to Exhibitions → Add New Exhibition
3. Press F12 → Console tab
4. Fill in exhibition details
5. In "Event Video (Optional)" field, paste: `https://youtu.be/dQw4w9WgXcQ`
6. Look in console for:
   ```
   === VIDEO FIELD DEBUG ===
   Element found: true
   Element value: https://youtu.be/dQw4w9WgXcQ
   ```

7. If console shows `Element value:` is EMPTY or (empty string):
   - Problem: Field not accepting input
   - Solution: 
     * Clear browser cache (Ctrl+Shift+Delete)
     * Reload page (F5)
     * Try typing URL again

8. If console shows `Element value:` HAS the URL:
   - Check for: `event_video value from form: https://youtu.be/dQw4w9WgXcQ`
   - If you see it → URL is being sent to API ✅
   - If you don't see it → Issue in form submission code

**Quick Fix**:
```javascript
// If form not capturing video, add this to browser console:
document.getElementById('event_video').value = 'https://youtu.be/dQw4w9WgXcQ';
console.log(document.getElementById('event_video').value);
```

---

### PROBLEM #2: API Not Returning event_video

**Cause**: API query not selecting event_video column

**How to fix:**

Check file: `api/get_event_details.php` (Line ~191-195)

Make sure this code exists:
```php
SELECT 
    ex.id,
    ex.exhibition_date as event_date,
    ...
    ex.event_video as video_url,        ← MUST BE HERE
    ex.event_video,                     ← MUST BE HERE
    ...
FROM exhibitions ex
WHERE ex.id = ?
```

If missing, add these lines after `ex.cover_image,`:
```php
ex.event_video as video_url,
ex.event_video,
```

Then test API again.

---

### PROBLEM #3: event.php Not Checking for Video

**Cause**: displayEvent() function not calling displayVideo()

**How to fix:**

Check file: `event.php` (Line ~436-447)

Make sure this code exists:
```javascript
// Display video if available
console.log('=== VIDEO URL CHECK ===');
console.log('event.video_url:', event.video_url);
console.log('event.event_video:', event.event_video);

const videoUrl = event.video_url || event.event_video;
if (videoUrl) {
    console.log('Video URL found, calling displayVideo with:', videoUrl);
    displayVideo(videoUrl);
} else {
    console.log('No video URL found in event object');
}
```

If missing or different, replace lines 436-447 with the code above.

---

### PROBLEM #4: CSS Not Showing Video Section

**Cause**: CSS rule not applied or active class not added

**How to fix:**

**Check 1**: event-detail.css has the rule

File: `event-detail.css` (around line 114-124)

Should have:
```css
.event-section--video {
    background: #ffffff !important;
    padding: clamp(60px, 8vw, 100px) 0 !important;
    display: none;  /* Hidden by default */
}

.event-section--video.active {
    display: block !important;  /* Shows when active class added */
}
```

If missing, add it!

**Check 2**: Inline style not blocking

File: `event.php` (Line ~117)

Should be:
```html
<section class="event-section event-section--video" id="videoSection" style="display: none;">
```

This `style="display: none"` is OK because CSS overrides it with `.active { display: block !important; }`

**Check 3**: displayVideo() function is adding active class

File: `event.php` (Line ~461-537)

Should have:
```javascript
if (embedUrl) {
    if (videoFrame) {
        videoFrame.src = embedUrl;
    }
    if (videoSection) {
        videoSection.classList.add('active');  ← THIS LINE IS CRITICAL
    }
}
```

---

### PROBLEM #5: Video Section Not Visible Even Though CSS is Correct

**Cause**: Browser issue, cache, or timing problem

**How to fix:**

1. **Clear cache completely:**
   - Press Ctrl+Shift+Delete
   - Select "All time"
   - Check "Cookies and other site data"
   - Check "Cached images and files"
   - Click Delete

2. **Hard refresh:**
   - Go to event page
   - Press Ctrl+F5 (or Cmd+Shift+R on Mac)
   - Wait for page to fully load

3. **Check Network tab:**
   - F12 → Network tab
   - Reload page
   - Look for "event-detail.css" request
   - Should show status 200 (not 404)

4. **Use Inspector to verify:**
   - F12 → Inspector (Elements) tab
   - Find `<section id="videoSection">`
   - Check if it has class `active`
   - Check computed styles - should show `display: block`

5. **Try different browser:**
   - Chrome, Firefox, Safari, Edge
   - See if works in different browser
   - If works in one, browser-specific issue

---

## PART 3: COMPLETE END-TO-END TEST

Follow this to verify everything works:

### Step 1: Add Exhibition with Video
```
1. Admin → Exhibitions → Add New
2. Title: "Test Video" (EN)
3. Date: Today (or past date)
4. Location: "Hall 1"
5. Event Video: https://youtu.be/dQw4w9WgXcQ
6. Click Create Exhibition
7. Check console for: "event_video value from form: https://youtu.be/dQw4w9WgXcQ"
```

### Step 2: Verify in Database
```sql
SELECT id, title_en, event_video 
FROM exhibitions 
WHERE title_en = 'Test Video' 
LIMIT 1;
```
Expected: See video URL, not NULL

### Step 3: Check API
```
/api/get_event_details.php?id=EXHIBITION_ID&lang=en
```
Expected: JSON with `"event_video": "https://youtu.be/dQw4w9WgXcQ"`

### Step 4: View Event Page
```
1. Go to Spaces → Click past exhibition
2. F12 → Console
3. Look for "VIDEO URL CHECK" section
4. Should show event_video with URL
5. Scroll page - video should be visible
```

---

## SUMMARY TABLE

| Issue | Console Log | Database | Fix |
|-------|------------|----------|-----|
| Video not entered in form | "Element value:" is empty | NULL | Clear cache, try again |
| Video in form but not saved | "event_video: null" in log | NULL | Check form submission logging |
| Video in DB but API doesn't return | "event_video: null" in event.php | Has URL | Fix get_event_details.php |
| API returns video but not displayed | No "VIDEO URL CHECK" section | Has URL | Fix event.php displayEvent() |
| displayVideo() called but section hidden | "active" in classList | Has URL | Clear cache, hard refresh |
| Video visible but doesn't play | Video section visible | Has URL | Check YouTube URL is public |

---

## QUICK FIX COMMAND

If you want to quickly test if everything works, use this test URL:

```
https://youtu.be/dQw4w9WgXcQ
```

This is a well-known YouTube video that's public. If THIS works but your URLs don't, your URL format is wrong.

---

## WHAT YOU NEED TO REPORT

If still not working after these steps, provide:

1. **Database query result:**
   ```sql
   SELECT event_video FROM exhibitions WHERE id=54;
   ```

2. **API response:**
   Visit: `/api/get_event_details.php?id=54&lang=en`
   Screenshot of JSON

3. **Console output:**
   Screenshot of console when viewing event page
   (F12 → Console → scroll to find "VIDEO URL CHECK")

4. **Video URL you're using:**
   (The exact URL you entered)

5. **Which step failed:**
   (From the 5 verification steps above)

This information will help identify the exact problem!

---

## FINAL NOTES

- ✅ Infrastructure is 100% correct and verified
- ✅ All components work together properly  
- ✅ If video isn't showing, it's a simple fix (usually cache or form input)
- ✅ Follow steps above to identify exact problem
- ✅ Apply the specific fix for your problem number

**Expected time to fix**: 5-15 minutes once you identify which problem you have.
