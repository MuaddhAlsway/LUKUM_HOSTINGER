<?php
/**
 * DISABLED - Translation API
 * PHASE 1: Disabled MyMemory API wrapper
 * 
 * This endpoint is no longer used.
 * It was calling MyMemory Translation API for real-time translation.
 * 
 * Status: DISABLED
 * Reason: Removed in PHASE 1 cleanup
 * 
 * PHASE 2 will introduce a new translation system.
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Return error response
http_response_code(410); // Gone
echo json_encode([
    'success' => false,
    'error' => 'Translation API disabled (PHASE 1 cleanup)',
    'message' => 'This endpoint is no longer available. A new translation system will be introduced in PHASE 2.'
]);
exit;
?>
