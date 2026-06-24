# Troubleshooting Guide - Add Event API Issues

## Quick Diagnosis Flow

```
Does the form submit?
├─ NO → Check browser console (F12) for JavaScript errors
├─ YES → Check the error message displayed
    ├─ "Database connection error"
    │  └─ Go to Section 1: Database Connection Issues
    ├─ "Invalid response from server"
    │  └─ Go to Section 2: API Response Issues
    ├─ "Network error"
    │  └─ Go to Section 3: Network Issues
    └─ Success message → Event created successfully ✓
```

---

## Section 1: Database Connection Issues

### Symptom
Error message: "Database connection error. Please ensure the server is properly configured and the database is running."

### Cause
Database server is not running or credentials are wrong.

### Solution

#### Step 1: Verify MySQL is Running
```bash
# Windows - Check if MySQL is running
netstat -an | findstr 3306

# Linux/Mac
sudo service mysql status
# or
sudo service mysqld status
```

**If not running, start it:**
```bash
# Windows - XAMPP
# Click "Start" button next to MySQL in XAMPP Control Panel

# Linux
sudo service mysql start

# Mac with Homebrew
brew services start mysql
```

#### Step 2: Verify Database Credentials
Edit `config.local.php` and check:

```php
'db' => [
    'host'     => 'localhost',  // ← Usually localhost
    'user'     => 'YOUR_USER',  // ← Check this
    'password' => 'YOUR_PASS',  // ← Check this
    'database' => 'YOUR_DB',    // ← Check this
    'port'     => 3306,         // ← Usually 3306
],
```

#### Step 3: Test Connection Directly
```bash
# Using mysql command
mysql -h localhost -u YOUR_USER -p YOUR_PASS -D YOUR_DB -e "SELECT 1;"

# If successful, should show: 1
```

#### Step 4: Check Error Log
```bash
tail -20 logs/error.log | grep "Add Event"
```

Look for lines like:
```
[Date] Add Event - DB Connection Error: Access denied for user 'user'@'localhost'
[Date] Add Event - DB Connection Error: No such file or directory
[Date] Add Event - DB Connection Error: Connection refused
```

**Common Error Messages:**

| Error | Cause | Fix |
|-------|-------|-----|
| "Access denied" | Wrong password or user | Check config.local.php |
| "Connection refused" | MySQL not running | Start MySQL service |
| "No such file or directory" | Wrong host | Use "localhost" or "127.0.0.1" |
| "Unknown database" | Wrong database name | Check database name in config |

---

## Section 2: API Response Issues

### Symptom
Error message: "Invalid response from server. Please contact administrator."

### Cause
API is returning something other than JSON, usually a PHP error.

### Solution

#### Step 1: Check Browser Console
```
Press F12 → Console tab → Look for red errors
```

#### Step 2: Check Network Response
```
Press F12 → Network tab → Find add_event_simple.php → Click it
Look at "Response" tab - what does it show?
```

**Expected (Good):**
```json
{
  "success": false,
  "message": "Database connection failed: ...",
  "error_code": "ADD_EVENT_FAILED"
}
```

**Unexpected (Bad):**
```html
<br />
<b>Fatal error</b>:  Uncaught Error: ...
```

#### Step 3: Check Error Log
```bash
tail -50 logs/error.log
```

Look for PHP errors like:
```
PHP Fatal error: Uncaught Exception: ...
PHP Warning: Undefined variable: ...
PHP Parse error: syntax error, ...
```

#### Step 4: Verify Events Table Exists
```bash
mysql -h localhost -u YOUR_USER -p YOUR_PASS YOUR_DB -e "SHOW TABLES LIKE 'events';"
```

**Should show:**
```
| Tables_in_database |
| events             |
```

**If empty:** The events table doesn't exist. Run database setup.

#### Step 5: Check Table Structure
```bash
mysql -h localhost -u YOUR_USER -p YOUR_PASS YOUR_DB -e "DESCRIBE events;"
```

**Must have columns:**
- id (primary key)
- title_en, description_en, location_en
- title_ar, description_ar, location_ar
- event_date, event_time, event_end_time, end_date
- cover_image, video_url, category, is_featured, slug

**If missing columns:** Update database schema.

---

## Section 3: Network Issues

### Symptom
Error message: "Network error. Please check your internet connection."

### Cause
Network connectivity problem between browser and server.

### Solution

#### Step 1: Verify Server is Accessible
```bash
# Ping the server
ping localhost

# Or try to access the main page
curl http://localhost/LUKUM_HOSTINGER-deployment-hostinger/
```

#### Step 2: Check CORS Settings
If getting CORS error in console:
```
Access to XMLHttpRequest blocked by CORS policy
```

The `api/config.php` should have:
```php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
```

#### Step 3: Check API URL
In `admin/add-event.html`, look for:
```javascript
fetch(getApiUrl('add_event_simple.php'), {
```

Check what `getApiUrl()` returns - it should be something like:
```
http://localhost/LUKUM_HOSTINGER-deployment-hostinger/api/add_event_simple.php
```

#### Step 4: Monitor Network Traffic
```
Press F12 → Network tab → Submit form
Look for request to add_event_simple.php
Check: Status (should be 500 or 200), Response (check format)
```

---

## Section 4: Form Validation Issues

### Symptom
Can't submit form, or validation errors appear.

### Solution

#### Step 1: Check Required Fields
The form requires:
- **Event Title (EN)** - Cannot be empty
- **Location (EN)** - Cannot be empty  
- **Event Date** - Must be a valid date

**Fix:** Fill in all required fields before submitting.

#### Step 2: Check Browser Console
```
Press F12 → Console tab → Look for validation errors
```

#### Step 3: Check Form State
```javascript
// In browser console, type:
document.getElementById('title_en').value
document.getElementById('location_en').value
document.getElementById('event_date').value

// Should show the values you entered
```

---

## Section 5: Logging Issues

### Can't Find Log File

#### Windows XAMPP
```
Typical location: C:\xampp\htdocs\LUKUM_HOSTINGER-deployment-hostinger\logs\error.log
```

#### Linux
```
Typical location: /var/www/html/LUKUM_HOSTINGER-deployment-hostinger/logs/error.log
```

#### View Log File
```bash
# Windows PowerShell
Get-Content logs/error.log -Tail 50

# Linux/Mac
tail -50 logs/error.log

# Follow log in real-time
tail -f logs/error.log
```

#### Log Format
```
[Date Time Timezone] Message
[2026-06-24 15:30:00 Europe/Berlin] Add Event - Processing event: Test
[2026-06-24 15:30:01 Europe/Berlin] Add Event - Event created successfully with ID: 42
```

---

## Section 6: Advanced Debugging

### Enable Detailed Logging
Edit `config.local.php`:
```php
'logging' => [
    'error_log_path' => __DIR__ . '/logs/error.log',
    'display_errors' => true,  // Set to true for detailed errors in browser
    'log_errors'     => true,
],
```

**WARNING:** Only for development! Disable in production.

### Test API Directly
Create test file `api/test_add_event.php`:
```php
<?php
require_once 'config.php';

$testData = [
    'title_en' => 'Test Event ' . time(),
    'description_en' => 'Test Description',
    'location_en' => 'Test Location',
    'event_date' => date('Y-m-d'),
    'event_time' => '10:00:00',
    'event_end_time' => '18:00:00',
];

$response = file_get_contents(__DIR__ . '/add_event_simple.php', 
    false, stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => 'Content-Type: application/json',
            'content' => json_encode($testData),
        ]
    ])
);

echo "Response: " . $response;
?>
```

Access: `http://localhost/LUKUM_HOSTINGER-deployment-hostinger/api/test_add_event.php`

### Check Database Logs
```bash
# MySQL error log (location varies by installation)
# Windows XAMPP
tail -50 "C:\xampp\mysql\data\mysql_error.log"

# Linux
tail -50 /var/log/mysql/error.log
```

---

## Section 7: Common Issues Checklist

```
□ MySQL is running
□ Database credentials are correct
□ Database and events table exist
□ All required columns exist in events table
□ Form fields are filled
□ Browser console shows no JavaScript errors
□ API returns valid JSON response
□ logs/error.log shows successful operation
□ Event appears in events list
```

---

## Section 8: Getting Help

If you're still stuck:

### 1. Gather Information
```bash
# Collect these for diagnosis:
cat logs/error.log | tail -100 > debug.txt
mysql -u user -p database -e "SHOW TABLES;" >> debug.txt
curl http://localhost/.../api/config.php >> debug.txt
```

### 2. Check Documentation
- Read: `ADD_EVENT_500_ERROR_FIX.md`
- Read: `VERIFY_FIX.md`
- Read: `FIX_SUMMARY.txt`

### 3. Check Error Messages
- Look in: `logs/error.log`
- Browser console: F12 → Console
- Network tab: F12 → Network

### 4. Test Incrementally
- Test 1: Can you connect to database?
- Test 2: Can you create event via API directly?
- Test 3: Does form submit?
- Test 4: Does event appear in list?

---

## Performance Considerations

If form is slow:
1. Check if MySQL is responsive: `mysql -u user -p -e "SELECT 1;"` (should be instant)
2. Check network tab for slow requests (should be <1s)
3. Check server load: `top` or Task Manager
4. Check disk space: `df -h` or Disk Management

---

## Security Notes

When debugging:
1. Don't share error logs publicly (they contain system info)
2. Don't commit `config.local.php` with real credentials
3. Disable `display_errors` in production
4. Use `logs/error.log` instead of displaying errors to users

---

## Still Stuck?

Last resort - Check these files directly:
1. `api/config.php` - Database configuration
2. `api/add_event_simple.php` - API implementation
3. `admin/add-event.html` - Frontend form
4. `logs/error.log` - What actually happened
5. Browser DevTools (F12) - What the browser sees

One of these will show you the real issue!
