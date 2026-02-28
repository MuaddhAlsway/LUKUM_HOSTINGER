<?php
/**
 * LAKUM Artspace - Local Configuration
 * 
 * SECURITY: This file contains sensitive credentials
 * NEVER commit this file to Git
 * This file is in .gitignore for security
 */

return [
    // ========================================================================
    // DATABASE CONFIGURATION
    // ========================================================================
    'db' => [
        'host'     => 'localhost',              // Hostinger: use localhost
        'user'     => 'u812122863_neama',       // Hostinger database user
        'password' => 'Neama@2024',             // Hostinger database password
        'database' => 'u812122863_lakum_artspace', // Hostinger database name
        'port'     => 3306,                     // MySQL port
        'charset'  => 'utf8mb4',                // Character set
    ],

    // ========================================================================
    // SITE CONFIGURATION
    // ========================================================================
    'site' => [
        'url'      => 'https://lakumartspace.com',  // Production URL
        'timezone' => 'Asia/Riyadh',                // Timezone
    ],

    // ========================================================================
    // SECURITY CONFIGURATION
    // ========================================================================
    'security' => [
        // JWT Secret - Strong and unique
        'jwt_secret'        => 'lakum_artspace_jwt_secret_2024_production_key_secure',
        
        // Session timeout in seconds (3600 = 1 hour)
        'session_timeout'   => 3600,
        
        // Maximum upload size in bytes (5242880 = 5MB)
        'max_upload_size'   => 5242880,
        
        // Allowed file extensions for uploads
        'allowed_extensions' => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf'],
    ],

    // ========================================================================
    // LOGGING CONFIGURATION
    // ========================================================================
    'logging' => [
        // Path to error log file
        'error_log_path' => __DIR__ . '/logs/error.log',
        
        // Display errors in browser (false for production)
        'display_errors' => false,
        
        // Log errors to file (true for production)
        'log_errors'     => true,
    ],

    // ========================================================================
    // UPLOADS CONFIGURATION
    // ========================================================================
    'uploads' => [
        // Directory where uploaded files are stored
        'directory' => __DIR__ . '/uploads/',
        
        // URL where uploaded files are accessible
        'url'       => 'https://lakumartspace.com/uploads/',
    ],
];
?>
