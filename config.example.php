<?php
/**
 * LAKUM Artspace - Configuration Template
 * 
 * IMPORTANT: This is a TEMPLATE file. Do NOT use this directly.
 * 
 * To set up your project:
 * 1. Copy this file to config.local.php
 * 2. Update config.local.php with your actual credentials
 * 3. config.local.php is in .gitignore and will NOT be committed
 * 4. This file (config.example.php) shows the structure only
 * 
 * SECURITY: Never commit actual credentials to Git!
 */

return [
    // Database Configuration
    'db' => [
        'host'     => 'localhost',           // Hostinger: use localhost
        'user'     => 'your_db_username',    // Replace with actual username
        'password' => 'your_db_password',    // Replace with actual password
        'database' => 'your_database_name',  // Replace with actual database name
        'port'     => 3306,                  // MySQL port (usually 3306)
        'charset'  => 'utf8mb4',             // Character set
    ],
    
    // Site Configuration
    'site' => [
        'url'      => 'https://lakumartspace.com',  // Your site URL
        'timezone' => 'Asia/Riyadh',                 // Your timezone
    ],
    
    // Security Configuration
    'security' => [
        'jwt_secret'       => 'your_super_secret_jwt_key_change_this',
        'session_timeout'  => 3600,                  // 1 hour in seconds
        'max_upload_size'  => 5242880,               // 5MB in bytes
        'allowed_extensions' => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf'],
    ],
    
    // Error Logging
    'logging' => [
        'error_log_path' => __DIR__ . '/logs/error.log',
        'display_errors' => false,  // Never display errors in production
        'log_errors'     => true,   // Always log errors
    ],
    
    // Upload Configuration
    'uploads' => [
        'directory' => __DIR__ . '/uploads/',
        'url'       => 'https://lakumartspace.com/uploads/',
    ],
];
?>

