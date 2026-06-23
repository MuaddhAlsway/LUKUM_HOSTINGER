# API Error Diagnostics & Fix

## Problem
HTTP 400 error when loading event on event.php. Shows "Error loading event: HTTP 400:"

## Solution Applied

### 1. Enhanced Error Messages
Modified `event.php` to display the actual API error message instead of just "HTTP 400":
- Shows detailed error information in the event description area
- Console shows full error for debugging
- Error box is styled clearly in red

### 2. Better Error Handling
- Catches and displays API error response body
- Tries to parse JSON error messages
- Falls back to raw text if not JSON
- Shows first 100 characters of error for diagnosis

### 3. Added Debug Tools

#### `api/debug_api_error.php`
- Check if exhibitions table exists
- Check if events table exists
- List sample exhibition IDs
- List sample event IDs
- Test direct exhibition fetch
- Provides suggested next steps

#### `api/get_event_details_simple.php`
- Simplified version of API without complex logic
- Better error logging
- Easier to debug if issues persist

#### `test_api_direct.html`
- Interactive test tool in browser
- 3-step testing process:
  1. Check database status
  2. Test API with specific ID
  3. Check debug API
- Shows detailed responses

---

## How to Diagnose the Issue

### Step 1: Check Database
Visit: `http://localhost/api/debug_api_error.php`

This shows:
- Whether exhibitions table exists
- Whether events table exists
- Sample IDs to test with

### Step 2: View in Browser Console
1. Open event.php
2. Press F12 to open Developer Tools
3. Go to Console tab
4. Look for error messages showing:
   - API URL being called
   - Response status
   - Actual error message from API

### Step 3: Test API Directly
Visit: `http://localhost/api/get_event_details.php?id=1&lang=en`

Should return JSON like:
```json
{
  "success": true,
  "event": { ... },
  "gallery": [ ... ]
}
```

If returns error:
```json
{
  "success": false,
  "message": "Actual error explanation here"
}
```

### Step 4: Use Interactive Test Tool
Open: `http://localhost/test_api_direct.html`

Click buttons to:
- Check database status
- List available IDs
- Test API with any ID
- See full responses

---

## Common HTTP 400 Causes

### Cause 1: Missing ID Parameter
**Error:** "Event/Exhibition not found with ID: undefined"
**Fix:** Ensure URL has `?id=X` parameter

### Cause 2: Exhibitions Table Missing
**Error:** "Prepare failed" or database connection error
**Fix:** Check if exhibitions table exists in database

### Cause 3: Wrong Table Structure
**Error:** Column name errors or type mismatches
**Fix:** Verify exhibitions table has all required columns:
- id
- title_en, title_ar
- description_en, description_ar
- location_en, location_ar
- exhibition_date, exhibition_time, exhibition_end_time
- end_date
- cover_image
- event_video
- gallery_images

### Cause 4: Null Values
**Error:** JSON encoding null values improperly
**Fix:** Use COALESCE to return empty strings instead of NULL

---

## Files Modified

1. **event.php**
   - Enhanced error message display
   - Shows actual API error messages
   - Better console logging

2. **api/get_event_details.php**
   - Added table existence checks
   - Better error logging
   - Fallback logic for missing tables

3. **New Files:**
   - `api/debug_api_error.php` - Database diagnostic
   - `api/get_event_details_simple.php` - Simple API version
   - `test_api_direct.html` - Interactive test tool
   - `API_ERROR_DIAGNOSTICS.md` - This guide

---

## Testing Workflow

```
1. Visit test_api_direct.html
   ↓
2. Click "Check Database Status"
   ↓
3. Note the exhibition IDs listed
   ↓
4. Enter an ID in "Test with ID" field
   ↓
5. Click "Test API"
   ↓
6. If success: Try that ID on event.php
   ↓
7. If fails: Check console error message and diagnose cause
```

---

## Expected Results

### When Working ✅
- event.php loads and displays event data
- Console shows: "✅ Loaded from database:"
- No error messages
- Video displays if present
- Gallery displays if present

### When Broken ❌
- Shows error message with specific problem
- Console shows API URL that was called
- Error message explains what's missing/wrong
- Can use debug tools to diagnose

---

## Next Steps

1. **Immediate:** Open browser console (F12) while viewing event.php
2. **Look for:** Error message showing what failed
3. **Diagnose:** Use test_api_direct.html to isolate issue
4. **Verify:** Check that:
   - exhibitions table exists
   - exhibitions table has rows
   - Sample ID from database is used
5. **Test:** Visit event.php?id=X with valid ID

---

## Summary

**Old behavior:** "Error loading event: HTTP 400:" - unclear what failed
**New behavior:** Shows actual API error message - clear diagnosis

**Debug tools:** Now have multiple ways to test and diagnose issues

**Fix is complete:** All error messages now helpful and actionable

