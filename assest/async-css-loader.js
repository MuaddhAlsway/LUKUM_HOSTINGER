// Async CSS Loader - Non-blocking stylesheet loading
// Loads CSS files asynchronously to prevent render-blocking
(function() {
  'use strict';
  
  // List of CSS files to load asynchronously
  const cssFiles = [
    'Home.css',
    'rtl.css',
    'fonts/greta-arabic.css',
    'assest/language-switcher.css',
    'assest/popup-notification.css'
  ];
  
  // Load CSS file asynchronously
  function loadCSS(href) {
    const link = document.createElement('link');
    link.rel = 'stylesheet';
    link.href = href;
    link.media = 'print';
    link.onload = function() {
      this.media = 'all';
    };
    document.head.appendChild(link);
  }
  
  // Load all CSS files
  cssFiles.forEach(file => {
    loadCSS(file);
  });
})();
