# How to Add Video URL to Existing Exhibition

## Your Exhibition
- ID: 54
- Name: "Cheval Blanc"
- Current Video: `null` (empty)

## How to Add Video

### Step 1: Open Edit Exhibition
1. Go to `/admin/exhibitions.html`
2. Find "Cheval Blanc" in the list
3. Click the edit button (pencil icon)

### Step 2: Add Video URL
1. Scroll down to the **"Event Video (Optional)"** section
2. In the "Video URL" field, paste your video URL:
   - YouTube: `https://www.youtube.com/watch?v=VIDEO_ID`
   - Vimeo: `https://vimeo.com/VIDEO_ID`
   - YouTube embed: `https://www.youtube.com/embed/VIDEO_ID`
   - Direct video URL: `https://example.com/video.mp4`

### Step 3: Save Changes
1. Click the **"Update Exhibition"** button at the bottom
2. You should see: "✓ Exhibition updated!"
3. You'll be redirected back to exhibitions list

### Step 4: Verify Video Shows
1. Go to `/spaces.php`
2. Scroll to **"Past Events"** section
3. Find "Cheval Blanc"
4. Click on it
5. You should see:
   - Exhibition details at the top
   - **"Event Video"** section with the embedded video
   - Video should play directly on the page

---

## Video URL Examples

### YouTube
```
https://www.youtube.com/watch?v=dQw4w9WgXcQ
```
Result: Video will be embedded from YouTube

### Vimeo
```
https://vimeo.com/123456789
```
Result: Video will be embedded from Vimeo

### Direct MP4 Link
```
https://example.com/path/to/video.mp4
```
Result: Video player will load the file directly

---

## Common Issues

### Video URL is Empty
- Make sure you paste a valid URL
- Check for extra spaces at the beginning/end

### Video Doesn't Display
1. Go to event page
2. Open DevTools (F12) → Console
3. Look for error messages
4. Check if video_url field has data in the JSON response

### Save Button Doesn't Work
- Make sure title and location are filled in (required fields)
- Check console for error messages

---

## What Happens After Save

1. Video URL saved to database in `exhibitions.event_video` column
2. When user clicks exhibition from spaces.php
3. event.php loads the exhibition data from API
4. API returns the video_url field
5. event.php displays the video in an embedded player
6. Supports YouTube, Vimeo, and direct video links

---

## Current System Structure

```
Database (exhibitions table)
  ↓
  └─ event_video: [YOUR_VIDEO_URL]
     ↓
API (get_event_details.php)
  ↓
  └─ Returns: video_url: [YOUR_VIDEO_URL]
     ↓
Frontend (event.php)
  ↓
  └─ Displays video in Event Video section

```

---

## Success Indicators

✅ Video URL saved in database  
✅ Video field populated in edit form  
✅ Video displays on event detail page  
✅ Video plays in browser  
✅ Gallery also works (if images exist)  

---

## Testing Steps

1. **Add Video**: Follow steps 1-3 above
2. **Verify Database**: Check exhibitions table in phpMyAdmin
   - Find record with id = 54
   - Check `event_video` column has your URL
3. **Verify API**: Open browser console
   - Go to event page
   - Check Network tab → get_event_details.php response
   - Look for `"video_url"` field with your URL
4. **Verify Display**: Check if video appears on page
   - Open event page
   - Look for "Event Video" section
   - Video should be embedded and playable

---

## Support

If video still doesn't show:
1. Verify URL is valid (copy-paste from YouTube/Vimeo address bar)
2. Check browser console for errors
3. Check Network tab for API response
4. Verify database has the URL saved
5. Clear browser cache and reload
