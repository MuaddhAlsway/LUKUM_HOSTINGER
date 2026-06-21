<?php
/**
 * LAKUM Artspace - Global Stylesheet Loader
 * This is the single source of truth for all CSS loading
 * All pages must include this file to ensure consistent styling
 */
?>
    <!-- Critical CSS - Inline for instant rendering -->
    <link rel="stylesheet" href="critical-inline.css?v=2.1.0">
    
    <!-- Unified Header & Navigation Styles (MUST BE FIRST) -->
    <link rel="stylesheet" href="lakum-header-unified.css?v=2.3.0">
    
    <!-- Dropdown Navigation Styles -->
    <link rel="stylesheet" href="lakum-header-dropdowns.css?v=4.0.0">
    
    <!-- Component Styles -->
    <link rel="stylesheet" href="lakum-components.css?v=2.1.0">
    
    <!-- RTL Support -->
    <link rel="stylesheet" href="rtl.css?v=2.1.0">
    
    <!-- Global Styles (deferred) -->
    <link rel="preload" href="global-styles.css?v=2.1.0" as="style" onload="this.onload=null;this.rel='stylesheet'">
    
    <!-- FAB Button Styles (CRITICAL - must load immediately) -->
    <link rel="stylesheet" href="assest/fab-button.css?v=2.1.0">
    
    <!-- Language Switcher Styles (deferred) -->
    <link rel="preload" href="assest/language-switcher.css?v=2.1.0" as="style" onload="this.onload=null;this.rel='stylesheet'">
    
    <!-- Popup Notification Styles (deferred) -->
    <link rel="preload" href="assest/popup-notification.css?v=2.1.0" as="style" onload="this.onload=null;this.rel='stylesheet'">
    
    <!-- Icon Font (deferred) -->
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet"></noscript>
    
    <!-- Arabic Font (deferred) -->
    <link rel="preload" href="fonts/greta-arabic.css?v=2.1.0" as="style" onload="this.onload=null;this.rel='stylesheet'">
    
    <!-- Fallback for no-JS -->
    <noscript>
        <link rel="stylesheet" href="global-styles.css?v=2.1.0">
        <link rel="stylesheet" href="rtl.css?v=2.1.0">
        <link rel="stylesheet" href="fonts/greta-arabic.css?v=2.1.0">
        <link rel="stylesheet" href="assest/language-switcher.css?v=2.1.0">
        <link rel="stylesheet" href="assest/popup-notification.css?v=2.1.0">
    </noscript>
