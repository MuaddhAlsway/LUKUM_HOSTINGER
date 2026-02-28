<?php
require_once 'lang/loader.php';
?><!DOCTYPE html>
<html <?php echo getLanguageAttributes(); ?>>
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo t('page_title', 'About LAKUM Artspace'); ?></title>
<link rel="icon" href="assest/favicon.png" type="image/png">
<link rel="preload" as="image" href="assest/img-4.png" fetchpriority="high">
<link rel="dns-prefetch" href="https://fonts.googleapis.com">
<link rel="dns-prefetch" href="https://fonts.gstatic.com">
<link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="preconnect" href="https://cdn.jsdelivr.net">
<link rel="preload" href="global-styles.css" as="style">
<link rel="preload" href="lakum-components.css" as="style">
<link rel="prefetch" href="assest/fonts/GretaArabicAR+LT-Regular.otf" as="font" type="font/otf" crossorigin>
<link rel="prefetch" href="assest/fonts/GretaArabicAR+LT-Light.otf" as="font" type="font/otf" crossorigin>
<link rel="stylesheet" href="global-styles.css">
<link rel="stylesheet" href="lakum-components.css">
<link rel="stylesheet" href="Home.css">
<link rel="stylesheet" href="rtl.css">
<link rel="stylesheet" href="fonts/greta-arabic.css">
<link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet" media="print" onload="this.media='all'">
<noscript><link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet"></noscript>
<script src="assest/navbar-mobile-toggle.js?v=5.0.0" defer></script>
<meta name="title" content="About LAKUM Artspace - Our Story & Mission">
<meta name="description" content="Learn about LAKUM Artspace, a premier cultural destination in Riyadh dedicated to fostering artistic expression, cultural exchange, and creative innovation.">
<meta name="keywords" content="about LAKUM, art gallery Riyadh, cultural hub, art exhibitions, creative workshops">
<meta name="author" content="LAKUM Artspace">
<meta name="language" content="<?php echo isArabic() ? 'Arabic' : 'English'; ?>">
<meta name="robots" content="index, follow">
<meta name="googlebot" content="index, follow">
<link rel="canonical" href="https://lakumartspace.infinityfree.me/about.php">
<link rel="alternate" hreflang="en" href="https://lakumartspace.infinityfree.me/about.php?lang=en" />
<link rel="alternate" hreflang="ar" href="https://lakumartspace.infinityfree.me/about.php?lang=ar" />
<meta name="theme-color" content="#1a1a1a">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="format-detection" content="telephone=no">
<style>
* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

html {
  font-size: 16px;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}

body {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Helvetica Neue', sans-serif;
  background: #2048daff;
  color: #1a1a1a;
  overflow-x: hidden;
  line-height: 1.6;
}

.page-hero {
  background: linear-gradient(135deg, rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.7)), url('assest/img-4.png') !important;
  background-size: cover !important;
  background-position: center !important;
  min-height: 400px !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  color: white !important;
  text-align: center !important;
  padding: 60px 20px !important;
}

.page-content {
  width: 100% !important;
  display: block !important;
}

.lakum-container {
 
    max-width: 1600px;
    margin: 0 auto;
    padding: 0 clamp(20px, 5vw, 60px);
}

.lakum-about-section {
  padding: clamp(40px, 5vw, 64px) 0 !important;
  background: #f6f6eb !important;
  border-bottom: 1px solid #e0e0e0 !important;
}

.lakum-about-section__content {
  display: grid;
    grid-template-columns: 1fr 1fr;
    gap: clamp(48px, 8vw, 96px);
    align-items: center;;
}

.lakum-about-section__text {
 display: flex;
    flex-direction: column;
}

.lakum-about-section__title {
  font-size: 2.5rem !important;
  font-weight: 300 !important;
  margin-bottom: 30px !important;
  color: #1a1a1a !important;
  text-align: center !important;
}

.lakum-about-section__description {
  display: flex !important;
  flex-direction: column !important;
  gap: clamp(20px, 3vw, 32px) !important;
}

.lakum-about-text__paragraphs {
      display: flex;
    flex-direction: column;
        gap: clamp(20px, 3vw, 32px);
}
.lakum-about-text__paragraphs p {
  font-weight: 300 !important;
  line-height: 1.8 !important;
  color: #525252 !important;
  margin: 0 !important;
  text-align: left !important;
  letter-spacing: 0.3px !important;
}

/* Arabic RTL Support */
html[lang="ar"] .lakum-about-section__content {
  grid-template-columns: 1fr 1fr;
  direction: rtl;
}

html[lang="ar"] .lakum-about-section__text {
  text-align: right;
}

html[lang="ar"] .lakum-about-text__paragraphs p {
  text-align: right !important;
}

html[lang="ar"] .lakum-workshops-section__content {
  grid-template-columns: 1fr 1fr;
  direction: rtl;
}

html[lang="ar"] .lakum-workshops-section__text {
  text-align: right;
}

html[lang="ar"] .lakum-workshops-section__description {
  text-align: right !important;
}

.lakum-about-section__image {
    position: relative;
    width: 100%;
    height: 100%;
    min-height: 550px;
}

@media (max-width: 1024px) {
    .lakum-about-section__image {
        min-height: auto;
    }
}

.lakum-about-section__image img {
  width: 100% !important;
  height: 100% !important;
  display: block !important;
  object-fit: cover !important;
  border-radius: var(--radius-sm) !important;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1) !important;
}

.lakum-workshops-section {
      padding: clamp(40px, 5vw, 64px) 0;
    background: #ffffff;
}

.lakum-workshops-section__content {
      display: grid;
    grid-template-columns: 1fr 1fr;
    gap: clamp(48px, 8vw, 96px);
    align-items: center;
}

.lakum-workshops-section__text {
  display: flex;
    flex-direction: column;
}

.lakum-workshops-section__title {
font-size: clamp(2rem, 4vw, 3rem);
    font-weight: 300;
    letter-spacing: -0.02em;
    color: #1a1a1a;
    margin: 0 0 clamp(32px, 4vw, 48px) 0;
    line-height: 1.2;
    text-align: center !important;
}

.lakum-workshops-section__description {
 font-size: clamp(1rem, 1.5vw, 1.1rem);
    font-weight: 300;
    line-height: 1.8;
    color: #525252;
    margin: 0;
    text-align: left;
    letter-spacing: 0.3px;
}

html[lang="ar"] .lakum-workshops-section__description {
  text-align: right !important;
}

.lakum-workshops-section__image {
       position: relative;
    width: 100%;
    height: 550px;
}

.lakum-workshops-section__image img {
 width: 100%;
    height: 100%;
    object-fit: cover;
}

.lakum-stats-section {
  padding: 80px 0 !important;
  background: #f6f6eb !important;
  margin: 60px 0 !important;
}

.lakum-stats-grid {
  display: grid !important;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)) !important;
  gap: 40px !important;
  text-align: center !important;
}

.lakum-stat-card {
  padding: 40px 20px !important;
}

.lakum-stat-card__number {
  font-size: 3rem !important;
  font-weight: 300 !important;
  color: #1a1a1a !important;
  margin-bottom: 10px !important;
}

.lakum-stat-card__label {
  font-size: 1rem !important;
  color: #666 !important;
  font-weight: 400 !important;
}

.lakum-tagline-section {
  padding: 80px 0 !important;
  text-align: center !important;
  border-bottom: 1px solid #e0e0e0 !important;
}

.lakum-tagline-section__spaces {
  font-size: clamp(1.75rem, 4vw, 3rem) !important;
  font-weight: 300 !important;
  font-style: italic !important;
  letter-spacing: -0.01em !important;
  color: #8a8a8a !important;
  margin: 0 0 clamp(16px, 2vw, 24px) 0 !important;
  line-height: 1.3 !important;
  max-width: 1000px !important;
  margin-left: auto !important;
  margin-right: auto !important;
  text-align: center !important;
}

.lakum-tagline-section__title {
  font-size: clamp(1.25rem, 2.5vw, 1.75rem) !important;
  font-weight: 300 !important;
  color: #525252 !important;
  margin: 0 !important;
  line-height: 1.4 !important;
  text-align: center !important;
}

.lakum-section {
  padding: 80px 0 !important;
}

.lakum-section-header {
  margin-bottom: 60px !important;
  text-align: center !important;
}

.lakum-section-header__title {
  font-size: 2.5rem !important;
  font-weight: 300 !important;
  color: #1a1a1a !important;
  margin-bottom: 15px !important;
}

.lakum-upcoming-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
  gap: clamp(32px, 4vw, 48px);
  width: 100%;
  padding: 0 clamp(20px, 5vw, 60px);
}

/* Arabic RTL Support */
html[lang="ar"] .lakum-upcoming-grid {
  direction: rtl;
}

.lakum-event-card {
  background: #f6f6eb !important;
  border-radius: 8px !important;
  overflow: hidden !important;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1) !important;
  transition: transform 0.3s ease !important;
  width: 100% !important;
  height: 485.56px !important;
  display: flex !important;
  flex-direction: column !important;
}

.lakum-event-card:hover {
  transform: translateY(-8px) !important;
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15) !important;
}

.lakum-upcoming-card {
  background: #f6f6eb !important;
}

.lakum-event-card__image {
  position: relative !important;
  width: 100% !important;
  height: 320px !important;
  overflow: hidden !important;
  background: #edecdf !important;
  flex-shrink: 0 !important;
}

.lakum-event-card__image img {
  width: 100% !important;
  height: 100% !important;
  object-fit: cover !important;
}

.lakum-event-card__date {
  position: absolute !important;
  top: 15px !important;
  right: 15px !important;
  background: white !important;
  padding: 8px 12px !important;
  border-radius: 4px !important;
  text-align: center !important;
}

/* Arabic RTL Support */
html[lang="ar"] .lakum-event-card__date {
  right: auto !important;
  left: 15px !important;
}

.lakum-event-card__date-month {
  display: block !important;
  font-size: 0.75rem !important;
  color: #999 !important;
  text-transform: uppercase !important;
}

.lakum-event-card__date-day {
  display: block !important;
  font-size: 1.5rem !important;
  font-weight: 600 !important;
  color: #1a1a1a !important;
}

.lakum-event-card__content {
  padding: 20px !important;
  flex: 1 !important;
  display: flex !important;
  flex-direction: column !important;
}

.lakum-event-card__title {
  font-size: 1.3rem !important;
  font-weight: 500 !important;
  margin-bottom: 8px !important;
  color: #1a1a1a !important;
}

.lakum-event-card__time {
  font-size: 0.9rem !important;
  color: #ff6b35 !important;
  margin-bottom: 12px !important;
}

.lakum-event-card__link {
  font-size: 0.9rem !important;
  color: #1a1a1a !important;
  text-decoration: none !important;
  font-weight: 500 !important;
}

.lakum-section-cta {
  display: flex !important;
  justify-content: center !important;
  margin-top: 40px !important;
}

@media (max-width: 768px) {
  .lakum-about-section__content,
  .lakum-workshops-section__content {
    grid-template-columns: 1fr !important;
    gap: 30px !important;
  }

  .lakum-about-section__text {
    padding: 0 !important;
    width: 100% !important;
    height: auto !important;
  }

  .lakum-about-section__image {
    width: 100% !important;
    height: auto !important;
  }

  .lakum-about-section__text,
  .lakum-workshops-section__text {
    padding: 0 !important;
  }

  .lakum-about-section__title,
  .lakum-workshops-section__title,
  .lakum-tagline-section__title {
    font-size: 2rem !important;
  }

  .lakum-stats-grid {
    grid-template-columns: repeat(2, 1fr) !important;
  }
}

@media (max-width: 480px) {
  .lakum-about-section__content,
  .lakum-workshops-section__content {
    grid-template-columns: 1fr !important;
    gap: 20px !important;
  }

  .lakum-about-section__text {
    width: 100% !important;
    height: auto !important;
  }

  .lakum-about-section__image {
    width: 100% !important;
    height: auto !important;
  }

  .lakum-workshops-section__image {
    width: 100% !important;
    height: auto !important;
  }

  .lakum-stats-grid {
    grid-template-columns: 1fr !important;
  }

  .lakum-about-section__title,
  .lakum-workshops-section__title {
    font-size: 1.5rem !important;
  }
}

.lakum-btn {
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  padding: clamp(14px, 2vw, 18px) clamp(36px, 5vw, 48px) !important;
  font-family: 'Atyp Kido TRIAL', sans-serif !important;
  font-size: clamp(0.95rem, 1.5vw, 1.05rem) !important;
  font-weight: 500 !important;
  letter-spacing: 0.5px !important;
  text-decoration: none !important;
  text-transform: capitalize !important;
  border-radius: var(--radius-sm) !important;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
  cursor: pointer !important;
  border: none !important;
}

.lakum-btn--outline {
  background: #1a1a1a !important;
  color: #ffffff !important;
  border: 2px solid #1a1a1a !important;
}

.lakum-btn--primary {
  background: #1a1a1a !important;
  color: #ffffff !important;
  border: 2px solid #1a1a1a !important;
}
</style>
<script src="assest/static-json-translator.js?v=1.0.0" defer></script></head>
<body class="<?php echo getLanguageClass(); ?>">
<div class="lakum-page-loader" id="pageLoader"><div class="lakum-page-loader__content"><div class="lakum-page-loader__spinner"></div></div></div>
<header class="lakum-header">
<div class="lakum-header__container"><div class="lakum-header__logo"><a href="index.php" class="lakum-logo"><img src="assest/logo/right_section.png" alt="LAKUM" class="lakum-logo__left"><img src="assest/logo/left_section.png" alt="Artspace" class="lakum-logo__right"></a></div><nav class="lakum-nav"><ul class="lakum-nav__list"><li class="lakum-nav__item"><a href="index.php" class="lakum-nav__link"><?php echo t('home', 'Home'); ?></a></li><li class="lakum-nav__item"><a href="about.php" class="lakum-nav__link lakum-nav__link--active"><?php echo t('about', 'About'); ?></a></li><li class="lakum-nav__item"><a href="spaces.php" class="lakum-nav__link"><?php echo t('spaces', 'Spaces'); ?></a></li><li class="lakum-nav__item"><a href="exhibitions.php" class="lakum-nav__link"><?php echo t('exhibitions', 'Exhibitions'); ?></a></li><li class="lakum-nav__item"><a href="calendar.php" class="lakum-nav__link"><?php echo t('calendar', 'Calendar'); ?></a></li><li class="lakum-nav__item"><a href="blog.php" class="lakum-nav__link"><?php echo t('blog', 'Blog'); ?></a></li><li class="lakum-nav__item"><a href="press.php" class="lakum-nav__link"><?php echo t('press', 'Press'); ?></a></li><li class="lakum-nav__item"><a href="contact.php" class="lakum-nav__link"><?php echo t('contact_us', 'Contact'); ?></a></li><li class="lakum-nav__item"><a href="shop.php" class="lakum-nav__link"><?php echo t('shop', 'Shop'); ?></a></li><li class="lakum-nav__item"><a href="test.php" class="lakum-nav__link">TEST</a></li></ul></nav><div class="lakum-language-switcher">
<a href="<?php echo buildLanguageSwitcherUrl(); ?>" class="lakum-lang-link" title="<?php echo isArabic() ? 'Language: English' : 'Language: ???????'; ?>">
<i class="ri-global-line"></i>
<span class="lakum-lang-text"><?php echo isArabic() ? 'EN' : 'AR'; ?></span>
</a>
</div><button class="lakum-header__mobile-toggle" aria-label="Toggle navigation menu"><span class="lakum-header__mobile-icon" aria-hidden="true"></span></button></div></header>
<div class="page-hero"><div></div></div>
<div class="page-content">
<section class="lakum-about-section"><div class="lakum-container"><div class="lakum-about-section__content"><div class="lakum-about-section__text"><h2 class="lakum-about-section__title"><?php echo t('about_heading', 'About Us'); ?></h2><div class="lakum-about-section__description"><div class="lakum-about-text__paragraphs"><p><strong><?php echo t('about_intro', 'Lakum Artspace is more than a gallery. It is a living space for connection. Rooted in Riyadh and reaching beyond, Lakum Artspace has evolved into a multidisciplinary platform where artists, thinkers, makers, and audiences come together.'); ?></strong></p><p><?php echo t('about_belief', 'We believe art is not only seen but shared, not only displayed but lived. At Lakum Artspace, exhibitions transform into gatherings, ideas become collaborations, and disciplines intersect to form new ways of engaging with culture.'); ?></p><p><?php echo t('about_exchange', 'As a dynamic space for cultural exchange, Lakum Artspace embraces the unexpected. Our programming spans art, design, sound, film, food, and performance, guided by curiosity, critical thought, and care. Each encounter is an invitation to witness, participate, and contribute to the evolving cultural dialogue.'); ?></p><p><strong><?php echo t('about_closing', 'For those who seek meaning through connection, this is the new face of Lakum Artspace.'); ?></strong></p></div></div></div><div class="lakum-about-section__image"><img src="assest/img-4.png" alt="LAKUM Artspace" loading="lazy"></div></div></div></section>
<section class="lakum-workshops-section"><div class="lakum-container"><div class="lakum-workshops-section__content"><div class="lakum-workshops-section__image"><img src="assest/img-4.png" alt="Workshops & Seminars" loading="lazy"></div><div class="lakum-workshops-section__text"><h2 class="lakum-workshops-section__title"><?php echo t('workshops_heading', 'Workshops & Seminars'); ?></h2><p class="lakum-workshops-section__description"><?php echo t('workshops_description_1', 'We offer a diverse range of workshops, seminars, and educational programs designed to inspire creativity and foster artistic growth. From beginner-friendly classes to advanced masterclasses, our expert instructors provide comprehensive guidance and mentorship.'); ?></p><p class="lakum-workshops-section__description"><?php echo t('workshops_description_2', 'Whether you\'re looking to develop new skills, explore different artistic mediums, or connect with fellow creatives, our programs provide the perfect platform for artistic development and cultural exchange.'); ?></p></div></div></div></section>
<section class="lakum-stats-section"><div class="lakum-container"><div class="lakum-stats-grid"><div class="lakum-stat-card"><div class="lakum-stat-card__number">+39</div><div class="lakum-stat-card__label"><?php echo t('stat_exhibitions', 'Exhibitions & Workshops'); ?></div></div><div class="lakum-stat-card"><div class="lakum-stat-card__number">+200K</div><div class="lakum-stat-card__label"><?php echo t('stat_pieces', 'Art Pieces'); ?></div></div><div class="lakum-stat-card"><div class="lakum-stat-card__number">+300K</div><div class="lakum-stat-card__label"><?php echo t('stat_artists', 'Artist & Instructor'); ?></div></div><div class="lakum-stat-card"><div class="lakum-stat-card__number">+55K</div><div class="lakum-stat-card__label"><?php echo t('stat_participants', 'Participants & Visitors'); ?></div></div></div></div></section>
<section class="lakum-tagline-section"><div class="lakum-container"><p class="lakum-tagline-section__spaces"><?php echo t('tagline_spaces', 'Artspace, Gallery, Hub, Library, Shop, Caf�'); ?></p><h2 class="lakum-tagline-section__title"><?php echo t('tagline_main', 'Where Encounters Shape Culture'); ?></h2></div></section>
<section class="lakum-section"><div class="lakum-container"><div class="lakum-section-header"><h2 class="lakum-section-header__title"><?php echo t('upcoming_exhibitions', 'Upcoming Exhibitions'); ?></h2></div><div class="lakum-upcoming-grid" id="upcomingEvents"><div class="lakum-skeleton-card"></div><div class="lakum-skeleton-card"></div><div class="lakum-skeleton-card"></div></div><div class="lakum-section-cta"><a href="exhibitions.php" class="lakum-btn lakum-btn--outline"><?php echo t('view_more', 'View More'); ?></a></div></div></section>
</div>
<footer class="lakum-footer"><div class="lakum-footer__container"><div class="lakum-footer__content"><div class="lakum-footer__brand"><div class="lakum-footer__logo"><img src="assest/logo/right_section.png" alt="LAKUM" class="lakum-footer__logo-left"><img src="assest/logo/left_section.png" alt="Artspace" class="lakum-footer__logo-right"></div><p class="lakum-footer__tagline"><?php echo t('footer_tagline', 'Where Encounters Shape Culture'); ?></p></div><nav class="lakum-footer__nav"><h4 class="lakum-footer__nav-title"><?php echo t('footer_navigate', 'Navigate'); ?></h4><ul class="lakum-footer__nav-list"><li><a href="index.php" class="lakum-footer__link"><?php echo t('home', 'Home'); ?></a></li><li><a href="about.php" class="lakum-footer__link"><?php echo t('about', 'About'); ?></a></li><li><a href="spaces.php" class="lakum-footer__link"><?php echo t('spaces', 'Spaces'); ?></a></li><li><a href="exhibitions.php" class="lakum-footer__link"><?php echo t('exhibitions', 'Exhibitions'); ?></a></li></ul></nav><nav class="lakum-footer__nav"><h4 class="lakum-footer__nav-title"><?php echo t('footer_explore', 'Explore'); ?></h4><ul class="lakum-footer__nav-list"><li><a href="calendar.php" class="lakum-footer__link"><?php echo t('calendar', 'Calendar'); ?></a></li><li><a href="blog.php" class="lakum-footer__link"><?php echo t('blog', 'Blog'); ?></a></li><li><a href="press.php" class="lakum-footer__link"><?php echo t('press', 'Press'); ?></a></li><li><a href="contact.php" class="lakum-footer__link"><?php echo t('contact_us', 'Contact'); ?></a></li></ul></nav><div class="lakum-footer__social"><h4 class="lakum-footer__nav-title"><?php echo t('footer_connect', 'Connect'); ?></h4><div class="lakum-footer__social-links"><a href="https://www.instagram.com/lakumartspace/" target="_blank" class="lakum-footer__social-link" aria-label="Instagram"><i class="ri-instagram-fill"></i></a><a href="https://x.com/Lakumartspace" target="_blank" class="lakum-footer__social-link" aria-label="Twitter"><i class="ri-twitter-x-fill"></i></a></div></div></div><div class="lakum-footer__bottom"><p class="lakum-footer__copyright"><?php echo t('footer_copyright', '� 2025 - 2027 LAKUM Artspace. All rights reserved.'); ?></p><div class="lakum-footer__legal"><a href="terms.php" class="lakum-footer__legal-link"><?php echo t('footer_terms', 'Terms & Conditions'); ?></a><span class="lakum-footer__legal-divider">|</span><a href="privacy.php" class="lakum-footer__legal-link"><?php echo t('footer_privacy', 'Privacy Policy'); ?></a></div></div></div></footer>
<div class="lakum-contact-fab" id="lakumContactFab"><button class="lakum-contact-fab__trigger" id="fabTrigger" aria-label="Contact options"><i class="ri-mail-line lakum-contact-fab__icon"></i><i class="ri-close-line lakum-contact-fab__close"></i></button><div class="lakum-contact-fab__menu" id="fabMenu"><a href="tel:+966920012083" class="lakum-contact-fab__item" data-tooltip="Call us"><i class="ri-phone-line"></i></a><a href="https://wa.me/966920012083" target="_blank" class="lakum-contact-fab__item" data-tooltip="WhatsApp"><i class="ri-whatsapp-line"></i></a><a href="mailto:info@lakumartspace.com" class="lakum-contact-fab__item" data-tooltip="Email"><i class="ri-mail-line"></i></a></div></div>
<script src="assest/fun-interactions.js" defer></script>
<script src="mobile-performance-optimizer.js" defer></script>
<script>
    // Set current language from PHP
    window.LAKUM_LANG = '<?php echo getCurrentLanguage(); ?>';
    
    // Load all events from API (real database data only)
    async function loadAllEvents() {
        try {
            // Add timestamp to bypass cache
            const timestamp = new Date().getTime();
            const lang = window.LAKUM_LANG || localStorage.getItem('lakum_language') || 'en';
            const response = await fetch(`api/get_events.php?type=all&limit=1000&lang=${lang}&t=${timestamp}`, {
                cache: 'no-store'
            });
            const data = await response.json();
            if (data.success && data.data && Array.isArray(data.data)) {
                console.log('Loaded events from database:', data.data.length);
                return data.data;
            }
        } catch (error) {
            console.error('Error loading events:', error);
        }
        // Return empty array if API fails - no mock data
        return [];
    }

    // Load upcoming events
    async function loadUpcomingEvents() {
        const events = await loadAllEvents();
        const now = new Date();
        now.setHours(0, 0, 0, 0);
        
        const upcomingEvents = events.filter(e => {
            const eventDate = new Date(e.event_date);
            eventDate.setHours(0, 0, 0, 0);
            return eventDate >= now;
        }).sort((a, b) => new Date(a.event_date) - new Date(b.event_date));
        
        const container = document.getElementById('upcomingEvents');
        container.innerHTML = '';

        const filteredEvents = upcomingEvents.slice(0, 3);

        if (filteredEvents.length === 0) {
            container.innerHTML = '<p style="text-align: center; padding: 40px; color: #999;">No upcoming events</p>';
            return;
        }

        filteredEvents.forEach(event => {
            const eventDate = new Date(event.event_date);
            const month = eventDate.toLocaleDateString('en-US', { month: 'short' }).toUpperCase();
            const day = eventDate.getDate();
            const dateStr = `${month} ${day}`;

            const card = document.createElement('div');
            card.className = 'lakum-event-card';
            card.innerHTML = `
                <div class="lakum-event-card__image">
                    <img src="${event.cover_image || 'assest/img-4.png'}" alt="${event.title}" loading="lazy">
                </div>
                <div class="lakum-event-card__content">
                    <h3 class="lakum-event-card__title">${event.title}</h3>
                    <p class="lakum-event-card__time">${dateStr}</p>
                </div>
            `;
            container.appendChild(card);
        });
    }

    // Initialize
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', loadUpcomingEvents);
    } else {
        loadUpcomingEvents();
    }

    // Reload when page becomes visible
    document.addEventListener('visibilitychange', () => {
        if (!document.hidden) {
            loadUpcomingEvents();
        }
    });
</script>

<script>
    // Translation strings for JavaScript
    const translations = {
        nav_home: "<?php echo t('home', 'Home'); ?>",
        nav_about: "<?php echo t('about', 'About'); ?>",
        nav_spaces: "<?php echo t('spaces', 'Spaces'); ?>",
        nav_exhibitions: "<?php echo t('exhibitions', 'Exhibitions'); ?>",
        nav_calendar: "<?php echo t('calendar', 'Calendar'); ?>",
        nav_blog: "<?php echo t('blog', 'Blog'); ?>",
        nav_press: "<?php echo t('press', 'Press'); ?>",
        nav_contact: "<?php echo t('contact_us', 'Contact'); ?>",
        nav_shop: "<?php echo t('shop', 'Shop'); ?>",
        footer_tagline: "<?php echo t('footer_tagline', 'Where Encounters Shape Culture'); ?>",
        footer_navigate: "<?php echo t('footer_navigate', 'Navigate'); ?>",
        footer_explore: "<?php echo t('footer_explore', 'Explore'); ?>",
        footer_connect: "<?php echo t('footer_connect', 'Connect'); ?>",
        footer_copyright: "<?php echo t('footer_copyright', '� 2026 LAKUM Artspace. All rights reserved.'); ?>",
        footer_terms: "<?php echo t('footer_terms', 'Terms & Conditions'); ?>",
        footer_privacy: "<?php echo t('footer_privacy', 'Privacy Policy'); ?>"
    };

    // Update navbar and footer text when language changes
    function updateNavbarFooterLanguage() {
        // Navbar links
        const navLinks = {
            'home': translations.nav_home || 'Home',
            'about': translations.nav_about || 'About',
            'spaces': translations.nav_spaces || 'Spaces',
            'exhibitions': translations.nav_exhibitions || 'Exhibitions',
            'calendar': translations.nav_calendar || 'Calendar',
            'blog': translations.nav_blog || 'Blog',
            'press': translations.nav_press || 'Press',
            'contact_us': translations.nav_contact || 'Contact',
            'shop': translations.nav_shop || 'Shop'
        };

        // Update navbar
        const navItems = document.querySelectorAll('.lakum-nav__link');
        navItems.forEach(link => {
            const href = link.getAttribute('href');
            if (href === 'index.php') link.textContent = navLinks.home;
            else if (href === 'about.php') link.textContent = navLinks.about;
            else if (href === 'spaces.php') link.textContent = navLinks.spaces;
            else if (href === 'exhibitions.php') link.textContent = navLinks.exhibitions;
            else if (href === 'calendar.php') link.textContent = navLinks.calendar;
            else if (href === 'blog.php') link.textContent = navLinks.blog;
            else if (href === 'press.php') link.textContent = navLinks.press;
            else if (href === 'contact.php') link.textContent = navLinks.contact_us;
            else if (href === 'shop.php') link.textContent = navLinks.shop;
        });

        // Update footer tagline
        const footerTagline = document.querySelector('.lakum-footer__tagline');
        if (footerTagline) {
            footerTagline.textContent = translations.footer_tagline || 'Where Encounters Shape Culture';
        }

        // Update footer navigation titles
        const footerNavTitles = document.querySelectorAll('.lakum-footer__nav-title');
        if (footerNavTitles.length >= 3) {
            footerNavTitles[0].textContent = translations.footer_navigate || 'Navigate';
            footerNavTitles[1].textContent = translations.footer_explore || 'Explore';
            footerNavTitles[2].textContent = translations.footer_connect || 'Connect';
        }

        // Update footer links
        const footerLinks = document.querySelectorAll('.lakum-footer__link');
        footerLinks.forEach(link => {
            const href = link.getAttribute('href');
            if (href === 'index.php') link.textContent = navLinks.home;
            else if (href === 'about.php') link.textContent = navLinks.about;
            else if (href === 'spaces.php') link.textContent = navLinks.spaces;
            else if (href === 'exhibitions.php') link.textContent = navLinks.exhibitions;
            else if (href === 'calendar.php') link.textContent = navLinks.calendar;
            else if (href === 'blog.php') link.textContent = navLinks.blog;
            else if (href === 'press.php') link.textContent = navLinks.press;
            else if (href === 'contact.php') link.textContent = navLinks.contact_us;
        });

        // Update footer bottom
        const footerCopyright = document.querySelector('.lakum-footer__copyright');
        if (footerCopyright) {
            footerCopyright.textContent = translations.footer_copyright || '� 2026 LAKUM Artspace. All rights reserved.';
        }

        const footerTermsLink = document.querySelector('.lakum-footer__legal-link:first-child');
        if (footerTermsLink) {
            footerTermsLink.textContent = translations.footer_terms || 'Terms & Conditions';
        }

        const footerPrivacyLink = document.querySelector('.lakum-footer__legal-link:last-child');
        if (footerPrivacyLink) {
            footerPrivacyLink.textContent = translations.footer_privacy || 'Privacy Policy';
        }
    }

    // Listen for language changes
    document.addEventListener('lakum-language-changed', (e) => {
        const lang = e.detail?.lang || document.documentElement.lang;
        // Reload translations for the new language
        fetch(`api/get-translations.php?lang=${lang}`)
            .then(r => r.json())
            .then(data => {
                if (data.success && data.translations) {
                    // Update translations object
                    Object.assign(translations, data.translations);
                    // Update navbar and footer
                    updateNavbarFooterLanguage();
                }
            })
            .catch(err => console.log('Language update skipped'));
    });

    // Also listen for storage changes (multi-tab sync)
    window.addEventListener('storage', (e) => {
        if (e.key === 'lakum_language' && e.newValue) {
            const lang = e.newValue;
            fetch(`api/get-translations.php?lang=${lang}`)
                .then(r => r.json())
                .then(data => {
                    if (data.success && data.translations) {
                        Object.assign(translations, data.translations);
                        updateNavbarFooterLanguage();
                    }
                })
                .catch(err => console.log('Language update skipped'));
        }
    });

    // Call on page load
    updateNavbarFooterLanguage();
</script>
</body>
</html>














