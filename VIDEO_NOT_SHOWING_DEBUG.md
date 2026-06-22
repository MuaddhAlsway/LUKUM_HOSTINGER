# Video Not Showing on Event Page - Complete Debugging Guide

## Problem Statement
Videos are being saved to the exhibitions table in the database, but they're NOT displaying on the event.php page when you click on a past exhibition.

## Root Cause Analysis Path

The flow is:
1. User adds exhibition with video in admin panel
2. Video URL saves to exhibitions table → `event_video` column
3. User goes to Spaces page and clicks past exhibition
4. spaces.php loads exhibitions from `api/get_exhibitions.php`
5. spaces.php displays carousel and links to `event.php?title=SLUG&lang=en`
6. event.php loads event details from `api/get_event_details.php?title=SLUG&lang=en`
7. event.php calls `displayVideo()` function to show video
8. **VIDEO SHOULD APPEAR** but it doesn't

## Debugging Steps

### Step 1: Verify Video is in Database
Run this command or query:
```sql
SELECT id, title_en, event_video FROM exhibitions WHERE event_video IS NOT NULL LIMIT 5;
```

Expected: You should see video URLs like `https://www.youtube.com/watch?v=...`
If empty: Videos aren't being saved at all

### Step 2: Check API Response
1. Open browser DevTools (F12)
2. Go to Network tab
3. Visit: https://yourdomain.com/api/get_event_details.php?id=54&lang=en
4. Look at Response - it should include:
   ```json
   {
     "event": {
       "id": 54,
       "event_video": "https://www.youtube.com/watch?v=...",
       "video_url": "https://www.youtube.com/watch?v=...",
       ...
     }
   }
   ```

Expected fields:
- `event.event_video` - Should have the URL
- `event.video_url` - Alias, should also have the URL

If missing: API is not returning video field

### Step 3: Check Browser Console on Event Page
1. Go to past exhibition on event page
2. Open Browser DevTools (F12)
3. Click Console tab
4. Look for these logs:

**Expected Output:**
```
=== VIDEO URL CHECK ===
event.video_url: null
event.event_video: "https://www.youtube.com/watch?v=..."
Final videoUrl: "https://www.youtube.com/watch?v=..."
Video URL found, calling displayVideo with: https://www.youtube.com/watch?v=...

=== displayVideo DEBUG ===
videoUrl: https://www.youtube.com/watch?v=...
videoSection element: <section class="event-section event-section--video" ...>
videoFrame element: <iframe id="event-video" ...>
Generated YouTube embed URL: https://www.youtube.com/embed/JH3zXmuFARw?autoplay=0&controls=1
Setting iframe src to: https://www.youtube.com/embed/JH3zXmuFARw?autoplay=0&controls=1
BEFORE adding active class - classList: ["event-section", "event-section--video"]
AFTER adding active class - classList: ["event-section", "event-section--video", "active"]
Video section display style: block
Video section displayed
```

### Possible Issues & Solutions

#### Issue 1: No logs appear at all
**Problem**: JavaScript isn't running or event page isn't loading
**Solution**:
- Clear browser cache (Ctrl+Shift+Delete)
- Refresh page (F5 or Ctrl+F5)
- Check for JavaScript errors in console
- Check Network tab for failed resources

#### Issue 2: "event_video: null" in logs
**Problem**: API is not returning the video field
**Solution**:
- Check database query: `SELECT event_video FROM exhibitions WHERE id=54;`
- If NULL in database, video wasn't saved to DB
- If has value in DB, check get_event_details.php is selecting it
- Verify API URL: `/api/get_event_details.php?id=54&lang=en`

#### Issue 3: "No video URL found in event object"
**Problem**: Both video_url and event_video are NULL
**Solution**:
- Check API response completely: Look at raw JSON in Network tab
- Verify the exhibition ID is correct
- Check if data is coming from events table instead of exhibitions table
- Make sure you're using past exhibitions (requires past dates)

#### Issue 4: Video URL shows but no embed URL generated
**Problem**: displayVideo() can't parse the URL
**Solution**:
- Log shows: "No embed URL generated from: https://..."
- Check URL format:
  - ✓ YouTube: `https://www.youtube.com/watch?v=VIDEO_ID`
  - ✓ YouTube: `https://youtu.be/VIDEO_ID`
  - ✓ Vimeo: `https://vimeo.com/VIDEO_ID`
  - ✗ Other platforms: Not supported
- Verify URL is valid and accessible
- Check if there are query parameters causing issues

#### Issue 5: Active class added but video still not showing
**Problem**: CSS not working or section still hidden
**Solution**:
- Check if event-detail.css is loaded (Network tab)
- Verify CSS rule exists: `.event-section--video.active { display: block !important; }`
- Check for inline styles: `style="display: none;"`
- Use browser inspector (F12) and click on video section
- Check computed styles to see what CSS is applied
- Look for CSS conflicts in other stylesheets

#### Issue 6: Video shows but doesn't play
**Problem**: Iframe src is set but YouTube/Vimeo embed is broken
**Solution**:
- Check iframe src URL in inspector: `https://www.youtube.com/embed/...`
- Test embed URL directly in browser address bar
- Verify YouTube/Vimeo allows embedding
- Check for CSP (Content Security Policy) errors in console
- Look for CORS or iframe restrictions

## Complete Debug Information Checklist

When reporting an issue, provide:

- [ ] **Database Check**: Video URL in exhibitions table? (yes/no)
- [ ] **API Response**: Contains event_video field? (yes/no)
- [ ] **Console Logs**: Show "VIDEO URL CHECK" section? (yes/no)
- [ ] **displayVideo Logs**: Show "=== displayVideo DEBUG ===" section? (yes/no)
- [ ] **Active Class**: Shows in classList after calling add? (yes/no)
- [ ] **Video Plays**: If showing, does it play when clicked? (yes/no)
- [ ] **Browser**: Chrome/Firefox/Safari/Edge? Which version?
- [ ] **Exhibition ID**: Which exhibition ID are you testing?
- [ ] **Video URL Format**: YouTube/Vimeo? Full URL pasted in console?

## Quick Test URLs

Use these for testing:

**YouTube**:
```
https://www.youtube.com/watch?v=JH3zXmuFARw
https://youtu.be/JH3zXmuFARw
```

**Vimeo**:
```
https://vimeo.com/76979871
```

## Files to Check

- `event.php` - Main event display page with displayVideo() function
- `api/get_event_details.php` - API returning event and video data
- `event-detail.css` - CSS for video section styling
- `admin/add-exhibition.html` - Form saving video URL
- `admin/edit-exhibition.html` - Form editing video URL

## API Endpoints

**Get Event Details** (used by event.php):
```
GET /api/get_event_details.php?id=EXHIBITION_ID&lang=en
GET /api/get_event_details.php?title=exhibition-slug&lang=en
```

Returns: `{ "success": true, "event": {...}, "gallery": [...] }`

**Get All Exhibitions** (used by spaces.php):
```
GET /api/get_exhibitions.php?type=all&limit=1000&lang=en
```

Returns: `{ "success": true, "data": [...] }`

## Video URL Requirements

✅ **Valid**:
- YouTube URLs with video ID: `v=JH3zXmuFARw`
- Vimeo URLs with video ID: `vimeo.com/76979871`
- Must be publicly accessible
- No playlist or channel links

❌ **Invalid**:
- Playlist URLs
- Channel URLs
- Private videos
- Embedded HTML (only URLs)

## CSS for Video Section

The video section HTML:
```html
<section class="event-section event-section--video" id="videoSection" style="display: none;">
    <iframe id="event-video" class="event-video"></iframe>
</section>
```

The CSS (in event-detail.css):
```css
.event-section--video {
    background: #ffffff !important;
    padding: clamp(60px, 8vw, 100px) 0 !important;
    display: none;  /* Hidden by default */
}

.event-section--video.active {
    display: block !important;  /* Shown when active class added */
}
```

When `displayVideo()` is called with a valid URL, it adds the `active` class which makes the section visible.

## Testing Steps (Complete Flow)

1. Open Browser DevTools (F12)
2. Go to Console tab
3. Navigate to event page for past exhibition with video
4. You should see these logs in order:
   - "=== VIDEO URL CHECK ===" section
   - "=== displayVideo DEBUG ===" section
   - "Setting iframe src to:" message
   - "AFTER adding active class" message
5. Video section should be visible on page
6. Video should be embedded and playable

## Performance Notes

- Video section loads with page (not lazy-loaded)
- Iframe loading doesn't block page rendering
- Console logs help identify exactly where video display breaks

## Need More Help?

1. Open event.php in browser
2. Go to past exhibition with video
3. Open DevTools Console tab
4. Copy all console output
5. Share screenshot showing the issue
6. Include which exhibition ID you're testing
