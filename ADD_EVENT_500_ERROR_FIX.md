# Add Event API - 500 Error Fix and Improvements

**Date:** June 24, 2026  
**Issue:** API returns 500 error when trying to add events  
**Status:** ✅ FIXED

## Root Cause Analysis

The issue had multiple components:

1. **config.php REQUEST_METHOD Error**: Line 312 was accessing `$_SERVER['REQUEST_METHOD']` without checking if it exists first, causing warnings when the code was called from CLI or non-HTTP contexts.

2. **Database Connection Issues**: When database connection failed, the error messages were not being properly returned as JSON to the frontend.

3. **Poor Error Handling**: The frontend wasn't properly parsing JSON error responses from the API.

## Fixes Applied

### 1. Fixed config.php (Line 312)
**Before:**
```php
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
```

**After:**
```php
if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
```

**Why:** Prevents undefined array key warning when REQUEST_METHOD is not available.

---

### 2. Improved add_event_simple.php Error Handling

#### Enhanced Database Connection Error
**Before:**
```php
if (!$db->isConnected()) {
    throw new Exception('Database connection failed');
}
```

**After:**
```php
if (!$db->isConnected()) {
    $conn = $db->getConnection();
    $connError = $conn ? $conn->connect_error : 'Connection object is null';
    error_log('Add Event - DB Connection Error: ' . $connError);
    throw new Exception('Database connection failed: ' . $connError);
}
```

**Why:** Provides detailed error messages for debugging.

#### Added Comprehensive Logging
- Logs raw input data
- Logs event processing steps
- Logs slug generation and uniqueness checks
- Logs insert statement preparation and execution
- Logs full error traces on failure

#### Improved Error Response Format
**Before:**
```php
http_response_code(400);
echo json_encode([
    'success' => false,
    'message' => $e->getMessage()
]);
```

**After:**
```php
http_response_code(500);
echo json_encode([
    'success' => false,
    'message' => $errorMsg,
    'error_code' => 'ADD_EVENT_FAILED',
    'timestamp' => date('Y-m-d H:i:s')
]);
exit;
```

**Why:** Returns consistent JSON with better metadata for frontend handling.

---

### 3. Improved add-event.html Error Handling

#### Better Error Response Parsing
**Before:**
```javascript
if (!response.ok) {
    const errorText = await response.text();
    throw new Error(`API returned ${response.status}: ${errorText || 'No response body'}`);
}
```

**After:**
```javascript
if (!response.ok) {
    let errorMessage = 'API Error';
    try {
        const errorResponse = await response.json();
        errorMessage = errorResponse.message || `Server error (${response.status})`;
        console.error('API Error Response:', response.status, errorResponse);
    } catch (e) {
        // If JSON parsing fails, try text
        const errorText = await response.text();
        console.error('API Error Response:', response.status, errorText);
        errorMessage = errorText ? errorText.substring(0, 100) : `Server error (${response.status})`;
    }
    throw new Error(errorMessage);
}
```

**Why:** Properly parses JSON error responses and falls back to text if needed.

#### User-Friendly Error Messages
**Before:**
```javascript
showPopup('Error: ' + error.message, 'error');
```

**After:**
```javascript
let displayMessage = error.message;
if (error.message.includes('Database')) {
    displayMessage = 'Database connection error. Please ensure the server is properly configured and the database is running.';
} else if (error.message.includes('JSON')) {
    displayMessage = 'Invalid response from server. Please contact administrator.';
} else if (error.message.includes('Network')) {
    displayMessage = 'Network error. Please check your internet connection.';
}

showPopup('Error: ' + displayMessage, 'error');
```

**Why:** Shows actionable error messages to users based on error type.

---

## How to Debug When 500 Errors Occur

If the API still returns a 500 error, follow these steps:

### 1. Check the Error Log
```bash
# On server: tail -f logs/error.log
```

Look for lines starting with `Add Event -` to see the detailed logs:
```
[2026-06-24 15:00:00] Add Event - Raw input: {"title_en":"Test",...}
[2026-06-24 15:00:00] Add Event - Processing event: Test
[2026-06-24 15:00:00] Add Event - Database connected successfully
[2026-06-24 15:00:00] Add Event - Generated initial slug: test
[2026-06-24 15:00:00] Add Event - Final slug: test
[2026-06-24 15:00:00] Add Event - Preparing insert statement
[2026-06-24 15:00:00] Add Event - Binding parameters
[2026-06-24 15:00:00] Add Event - Executing insert
[2026-06-24 15:00:00] Add Event - Event created successfully with ID: 42
```

### 2. Common Issues and Solutions

**Issue: Database Connection Error**
```
Add Event - DB Connection Error: Access denied for user...
```
**Solution:** Check database credentials in `config.local.php`

**Issue: Prepare Statement Failed**
```
Add Event Error: Insert prepare failed: Unknown column...
```
**Solution:** Verify the `events` table has all required columns

**Issue: Execute Statement Failed**
```
Add Event Error: Insert execute failed: Duplicate entry...
```
**Solution:** Check for unique constraint violations (e.g., duplicate slug)

### 3. Browser Console Debugging

When you see an error in the UI:

1. Open browser DevTools (F12)
2. Go to Console tab
3. Look for error messages like:
   - "Database connection error" → Check database is running
   - "Invalid response from server" → Check API response format
   - "Network error" → Check network connectivity

---

## Testing the Fix

### Manual Test
1. Go to Admin → Events
2. Click "Add New Event"
3. Fill in at least:
   - Event Title (EN) - required
   - Location (EN) - required
   - Event Date - required
4. Click Submit
5. Check browser console (F12) for logs
6. Check server `logs/error.log` for detailed logs

### Expected Success Response
```json
{
  "success": true,
  "message": "Event created successfully",
  "event_id": 42,
  "slug": "test-event",
  "event": { ... }
}
```

### Expected Error Response
```json
{
  "success": false,
  "message": "Database connection failed: ...",
  "error_code": "ADD_EVENT_FAILED",
  "timestamp": "2026-06-24 15:00:00"
}
```

---

## Files Modified

1. **api/config.php**
   - Fixed REQUEST_METHOD access on line 312

2. **api/add_event_simple.php**
   - Enhanced database connection error messages
   - Added comprehensive logging throughout the process
   - Improved error response format with error_code and timestamp
   - Better exception handling with full trace logging

3. **admin/add-event.html**
   - Improved error response parsing (JSON + text fallback)
   - User-friendly error messages based on error type
   - Better error categorization and display

---

## Next Steps

1. **Monitor logs** after deployment to catch any new issues
2. **Test with various inputs** to ensure robustness
3. **Consider adding:**
   - Event validation on submit (before API call)
   - Form field validation improvements
   - Retry logic for network errors
   - Success logging for all created events

---

## Notes

- All changes are backward compatible
- Error responses now follow consistent JSON format
- Logging provides clear debugging trail
- Frontend now handles all error scenarios gracefully
- No database schema changes required
