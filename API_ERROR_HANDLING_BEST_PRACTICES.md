# API Error Handling Best Practices

## Quick Reference: API Template

Use this template for all new API endpoints to ensure proper error handling:

```php
<?php
// STEP 1: Enable output buffering immediately
ob_start();

// STEP 2: Set JSON headers first
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

// STEP 3: Set error handlers BEFORE including config
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    ob_end_clean();
    error_log("Error [$errno]: $errstr in $errfile:$errline");
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $errstr,
        'error_code' => 'PHP_ERROR'
    ]);
    exit;
}, E_ALL);

set_exception_handler(function($exception) {
    ob_end_clean();
    error_log("Exception: " . $exception->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $exception->getMessage(),
        'error_code' => 'PHP_EXCEPTION'
    ]);
    exit;
});

// STEP 4: Include config
require_once __DIR__ . '/config.php';

try {
    // YOUR API LOGIC HERE
    
    // STEP 5: Before sending success response, clean output buffer
    ob_end_clean();
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'data' => $your_data
    ]);
    ob_end_flush();
    
} catch (Exception $e) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'error_code' => 'API_ERROR'
    ]);
    exit;
}
?>
```

## Key Rules

### 1. Output Buffering
- **ALWAYS** start with `ob_start()`
- This captures any stray output before headers
- Always call `ob_end_clean()` before sending responses
- Call `ob_end_flush()` after echoing the response

### 2. Headers
- Set JSON header BEFORE any output
- Set error handlers BEFORE including config
- Call `http_response_code()` before `echo`

### 3. Error Handling
- Use BOTH `set_error_handler()` AND `set_exception_handler()`
- Errors and exceptions need different handlers
- Always clear output buffer in error handlers
- Return JSON in errors, NEVER HTML

### 4. Response Format
- ALL responses should be JSON
- Error responses should include `error_code` for debugging
- Success responses should include `success: true`
- Always include timestamps for audit trails

## Common Issues & Solutions

### Issue: "SyntaxError: Unexpected end of JSON input"
**Cause**: API returning 500 with empty body
**Solution**: Use output buffering and proper error handlers

### Issue: "session_set_cookie_params() warning"
**Cause**: Session already started before setting parameters
**Solution**: Check `session_status()` before configuring session

### Issue: HTML in JSON response
**Cause**: PHP errors producing HTML error pages
**Solution**: Set error handlers early to catch all errors

### Issue: Headers already sent error
**Cause**: Output before headers are set
**Solution**: Use output buffering from the very start

## Testing Your API

### Step 1: Test with curl
```bash
curl -X POST http://localhost/api/your_endpoint.php \
  -H "Content-Type: application/json" \
  -d '{"key":"value"}'
```

### Step 2: Check response
- Should be valid JSON
- Should have `success` field
- Should have `error_code` in errors

### Step 3: Check logs
- Look in `/logs/error.log`
- Check for any PHP warnings or notices
- Verify error messages are informative

## Checklist for New APIs

- [ ] Output buffering starts immediately
- [ ] All headers set before content
- [ ] Error handler catches all errors
- [ ] Exception handler catches exceptions
- [ ] All responses are JSON
- [ ] Error responses include error_code
- [ ] Output buffer cleaned before responses
- [ ] No HTML in any response
- [ ] Proper HTTP status codes used
- [ ] Errors are logged with context
- [ ] Success response includes data
- [ ] Empty responses are prevented

## Example: Checking Your API

Open browser console (F12) and paste:
```javascript
// Test your API
fetch('/api/your_endpoint.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({test: 'data'})
})
.then(r => r.text())
.then(t => {
    try {
        console.log('JSON:', JSON.parse(t));
    } catch(e) {
        console.error('Invalid JSON:', t);
    }
});
```

If you see HTML in the response, there's an error in the API.

## Resources

- [PHP Output Buffering](https://www.php.net/manual/en/book.outcontrol.php)
- [JSON Error Responses](https://jsonapi.org/format/#document-top-level)
- [HTTP Status Codes](https://httpwg.org/specs/rfc7231.html#status.codes)
