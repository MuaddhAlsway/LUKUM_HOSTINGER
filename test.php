<?php
require_once 'lang/loader.php';
?><!DOCTYPE html>
<html <?php echo getLanguageAttributes(); ?>>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>TEST PAGE - Deployment Verification</title>
<link rel="icon" href="assest/favicon.png" type="image/png">
<link rel="stylesheet" href="global-styles.css">
<link rel="stylesheet" href="lakum-components.css">
<link rel="stylesheet" href="Home.css">
<link rel="stylesheet" href="rtl.css">
<link rel="stylesheet" href="fonts/greta-arabic.css">
<link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet" media="print" onload="this.media='all'">
<noscript><link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet"></noscript>
<script src="assest/navbar-mobile-toggle.js?v=5.0.0" defer></script>
<style>
body {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Helvetica Neue', sans-serif;
  background: #ffffff;
  color: #1a1a1a;
  overflow-x: hidden;
  line-height: 1.6;
}

.test-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 60px 20px;
  text-align: center;
}

.test-header {
  font-size: 3rem;
  font-weight: 300;
  margin-bottom: 30px;
  color: #1a1a1a;
}

.test-content {
  background: #f6f6eb;
  padding: 40px;
  border-radius: 8px;
  margin-bottom: 30px;
}

.test-text {
  font-size: 1.2rem;
  line-height: 1.8;
  color: #525252;
  margin-bottom: 20px;
}

.test-timestamp {
  font-size: 0.9rem;
  color: #999;
  margin-top: 20px;
  padding-top: 20px;
  border-top: 1px solid #e0e0e0;
}

.test-success {
  background: #d4edda;
  color: #155724;
  padding: 20px;
  border-radius: 4px;
  margin-bottom: 20px;
  font-weight: 500;
}

.test-info {
  background: #d1ecf1;
  color: #0c5460;
  padding: 20px;
  border-radius: 4px;
  margin-bottom: 20px;
}
</style>
</head>
<body class="<?php echo getLanguageClass(); ?>">
<div class="lakum-page-loader" id="pageLoader"><div class="lakum-page-loader__content"><div class="lakum-page-loader__spinner"></div></div></div>

<header class="lakum-header">
<div class="lakum-header__container">
<div class="lakum-header__logo">
<a href="index.php" class="lakum-logo">
<img src="assest/logo/right_section.png" alt="LAKUM" class="lakum-logo__left">
<img src="assest/logo/left_section.png" alt="Artspace" class="lakum-logo__right">
</a>
</div>
<nav class="lakum-nav">
<ul class="lakum-nav__list">
<li class="lakum-nav__item"><a href="index.php" class="lakum-nav__link">Home</a></li>
<li class="lakum-nav__item"><a href="about.php" class="lakum-nav__link">About</a></li>
<li class="lakum-nav__item"><a href="spaces.php" class="lakum-nav__link">Spaces</a></li>
<li class="lakum-nav__item"><a href="exhibitions.php" class="lakum-nav__link">Exhibitions</a></li>
<li class="lakum-nav__item"><a href="calendar.php" class="lakum-nav__link">Calendar</a></li>
<li class="lakum-nav__item"><a href="blog.php" class="lakum-nav__link">Blog</a></li>
<li class="lakum-nav__item"><a href="press.php" class="lakum-nav__link">Press</a></li>
<li class="lakum-nav__item"><a href="contact.php" class="lakum-nav__link">Contact</a></li>
<li class="lakum-nav__item"><a href="shop.php" class="lakum-nav__link">Shop</a></li>
<li class="lakum-nav__item"><a href="test.php" class="lakum-nav__link lakum-nav__link--active">TEST</a></li>
</ul>
</nav>
<div class="lakum-language-switcher">
<a href="<?php echo buildLanguageSwitcherUrl(); ?>" class="lakum-lang-link" title="<?php echo isArabic() ? 'Language: English' : 'Language: العربية'; ?>">
<i class="ri-global-line"></i>
<span class="lakum-lang-text"><?php echo isArabic() ? 'EN' : 'AR'; ?></span>
</a>
</div>
<button class="lakum-header__mobile-toggle" aria-label="Toggle navigation menu"><span class="lakum-header__mobile-icon" aria-hidden="true"></span></button>
</div>
</header>

<div class="test-container">
<h1 class="test-header">🧪 TEST PAGE - DEPLOYMENT VERIFICATION</h1>

<div class="test-success">
✅ SUCCESS! This page was successfully deployed to Hostinger!
</div>

<div class="test-info">
ℹ️ This is a temporary test page to verify that new changes are being deployed correctly to the live website.
</div>

<div class="test-content">
<p class="test-text">
<strong>If you can see this page, it means:</strong>
</p>
<p class="test-text">
✅ The deployment workflow is working correctly<br>
✅ Files are being uploaded to Hostinger<br>
✅ New changes are appearing on the live website<br>
✅ The GitHub Actions automation is functioning properly
</p>

<p class="test-text">
<strong>Test Page Information:</strong>
</p>
<p class="test-text">
📄 File: test.php<br>
📍 Location: Root directory<br>
🔗 URL: https://lakumartspace.com/test.php<br>
🏷️ Navigation: Added after SHOP tab
</p>

<p class="test-text">
<strong>What This Proves:</strong>
</p>
<p class="test-text">
1. New files are being deployed to Hostinger<br>
2. Navigation updates are working<br>
3. The deployment process is successful<br>
4. Changes are visible on the live website
</p>

<div class="test-timestamp">
Page created: <?php echo date('Y-m-d H:i:s'); ?> (Server Time)<br>
Language: <?php echo isArabic() ? 'Arabic' : 'English'; ?><br>
Deployment Test: PASSED ✅
</div>
</div>

<p style="margin-top: 40px; color: #999; font-size: 0.9rem;">
This is a temporary test page. It can be deleted after verifying deployment is working correctly.
</p>
</div>

<footer class="lakum-footer">
<div class="lakum-footer__container">
<div class="lakum-footer__content">
<div class="lakum-footer__brand">
<div class="lakum-footer__logo">
<img src="assest/logo/right_section.png" alt="LAKUM" class="lakum-footer__logo-left">
<img src="assest/logo/left_section.png" alt="Artspace" class="lakum-footer__logo-right">
</div>
<p class="lakum-footer__tagline">Where Encounters Shape Culture</p>
</div>
</div>
<div class="lakum-footer__bottom">
<p class="lakum-footer__copyright">© 2025 - 2027 LAKUM Artspace. All rights reserved.</p>
</div>
</div>
</footer>

<script src="assest/fun-interactions.js" defer></script>
</body>
</html>
