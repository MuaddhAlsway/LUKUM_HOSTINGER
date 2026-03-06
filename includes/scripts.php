<?php
/**
 * LAKUM Artspace - Global Script Loader
 * This is the single source of truth for all JavaScript loading
 * All pages must include this file to ensure consistent script execution
 * 
 * IMPORTANT: This file must be included BEFORE the closing </body> tag
 * 
 * Script Loading Order (CRITICAL - DO NOT CHANGE):
 * 1. lakum-header.js - Main header/navigation controller (SINGLE SOURCE OF TRUTH)
 * 2. fun-interactions.js - Interactive elements
 * 3. popup-notification.js - Notification system
 * 4. fab-button.js - Floating action button
 * 5. Page-specific scripts (loaded by individual pages)
 */
?>
    <!-- Unified Header Navigation Controller (SINGLE SOURCE OF TRUTH) -->
    <script src="lakum-header-unified.js" defer></script>
    
    <!-- Interactive Elements -->
    <script src="assest/fun-interactions.js" defer></script>
    
    <!-- Notification System -->
    <script src="assest/popup-notification.js?v=5.0.0" defer></script>
    
    <!-- Floating Action Button -->
    <script src="assest/fab-button.js" defer></script>
