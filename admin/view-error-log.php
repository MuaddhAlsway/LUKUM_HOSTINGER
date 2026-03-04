<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    die('Not authenticated');
}

// Read error log
$logFile = '../logs/php_errors.log';
if (!file_exists($logFile)) {
    $logFile = ini_get('error_log');
}

if (!file_exists($logFile)) {
    die('Error log not found at: ' . $logFile);
}

// Get last 100 lines
$lines = file($logFile);
$lastLines = array_slice($lines, -100);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Error Log Viewer</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #f5f5f5; }
        pre { background: white; padding: 15px; border-radius: 5px; overflow-x: auto; }
        h1 { color: #333; }
        .info { color: #666; margin-bottom: 20px; }
    </style>
</head>
<body>
    <h1>PHP Error Log</h1>
    <div class="info">
        <p>Log file: <?php echo $logFile; ?></p>
        <p>Total lines: <?php echo count($lines); ?></p>
        <p>Showing last 100 lines</p>
    </div>
    <pre><?php echo htmlspecialchars(implode('', $lastLines)); ?></pre>
</body>
</html>

