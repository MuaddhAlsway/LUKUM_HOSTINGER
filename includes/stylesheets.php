<?php
/**
 * LAKUM Artspace - Global Stylesheet Loader
 * This is the single source of truth for all CSS loading
 * All pages must include this file to ensure consistent styling
 */
?>
    <!-- Critical CSS - Inline for instant rendering -->
    <link rel="stylesheet" href="critical-inline.css">
    
    <!-- Unified Header & Navigation Styles (MUST BE FIRST) -->
    <link rel="stylesheet" href="lakum-header-unified.css">
    
    <!-- Component Styles -->
    <link rel="stylesheet" href="lakum-components.css">
    
    <!-- RTL Support -->
    <link rel="stylesheet" href="rtl.css">
    
    <!-- Global Styles (deferred) -->
    <link rel="preload" href="global-styles.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    
    <!-- FAB Button Styles (deferred) -->
    <link rel="preload" href="assest/fab-button.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    
    <!-- Language Switcher Styles (deferred) -->
    <link rel="preload" href="assest/language-switcher.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    
    <!-- Popup Notification Styles (deferred) -->
    <link rel="preload" href="assest/popup-notification.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    
    <!-- Icon Font (deferred) -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript><link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet"></noscript>
    
    <!-- Arabic Font (deferred) -->
    <link rel="preload" href="fonts/greta-arabic.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    
    <!-- Fallback for no-JS -->
    <noscript>
        <link rel="stylesheet" href="global-styles.css">
        <link rel="stylesheet" href="rtl.css">
        <link rel="stylesheet" href="fonts/greta-arabic.css">
        <link rel="stylesheet" href="assest/language-switcher.css">
        <link rel="stylesheet" href="assest/popup-notification.css">
    </noscript>
