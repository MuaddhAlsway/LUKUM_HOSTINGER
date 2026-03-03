/* Async CSS Loader - Non-blocking */
(function() {
  const cssFiles = [
    'global-styles.css',
    'lakum-components.css',
    'Home.css',
    'rtl.css',
    'fonts/greta-arabic.css',
    'assest/language-switcher.css',
    'assest/popup-notification.css'
  ];

  function loadCSS(href) {
    const link = document.createElement('link');
    link.rel = 'stylesheet';
    link.href = href;
    document.head.appendChild(link);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
      cssFiles.forEach(loadCSS);
    });
  } else {
    cssFiles.forEach(loadCSS);
  }
})();
