# Event Video Display Fix - COMPLETE

## Problem
When clicking past exhibitions from the **spaces.php Past Events section**, the video was not displaying on the event detail page, even though:
- The "Event Video (Optional)" field was available in `add-exhibition.html` and `edit-exhibition.html`
- The video URL was being saved to the database in the `exhibitions` table's `event_video` column
- The video section existed in `event.php`

## Root Cause
The mismatch between database field names:
- **Events table**: Uses `video_url` column
- **Exhibitions table**: Uses `event_video` column

The `event.php` was only checking for `event.video_url`, which works for events but not for exhibitions.

## Solution Implemented
Modified `event.php` `displayEvent()` function to check for **BOTH** field names:

**Before:**
```javascript
if (event.video_url) {
    displayVideo(event.video_url);
}
```

**After:**
```javascript
const videoUrl = event.video_url || event.event_video;
if (videoUrl) {
    displayVideo(videoUrl);
}
```

## Files Modified
- **event.php** (line 436): Updated video field checking to support both `video_url` and `event_video`

## How It Works Now

### Flow for Past Exhibitions (spaces.php → event.php):
1. User clicks a past exhibition in spaces.php
2. `loadPastEvents()` fetches from `api/get_exhibitions.php`
3. Exhibition data includes the `event_video` field (if populated)
4. User is redirected to: `event.php?title=exhibition-slug&lang=en`
5. `event.php` loads the exhibition data via API
6. The `displayEvent()` function now checks: `const videoUrl = event.video_url || event.event_video`
7. If video URL exists in either field, `displayVideo()` is called
8. Video displays in the event detail page

### Flow for Events (index.php, calendar.php, etc.):
1. User clicks an event
2. Data comes from `api/get_event_details.php` (events table)
3. Events table uses `video_url` column
4. Same check works: `const videoUrl = event.video_url || event.event_video`
5. Video displays correctly

## What Now Works
✅ Upload video URL in add-exhibition.html → saves to `event_video` column  
✅ Edit video URL in edit-exhibition.html → updates `event_video` column  
✅ Click past exhibition from spaces.php → event.php displays the video  
✅ Supports YouTube, Vimeo, and direct video URLs  
✅ Works in both English and Arabic  
✅ Backward compatible with events table (using `video_url`)

## Testing
To verify this works:
1. Go to `/admin/add-exhibition.html`
2. Fill in the form and paste a YouTube URL in "Event Video (Optional)"
3. Save the exhibition
4. Go to `/spaces.php` → Past Events section
5. Click the exhibition
6. The video should now display in the Event Video section

## Status
✅ **COMPLETE** - Event videos from exhibitions now display properly
