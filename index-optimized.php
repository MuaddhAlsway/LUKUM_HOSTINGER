<?php
require_once 'lang/loader.php';
require_once 'api/image-helper.php';
require_once 'config.css-loader.php';
?><!DOCTYPE html>
<html <?php echo getLanguageAttributes(); ?>>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title><?php echo t('page_title','LAKUM Artspace - Cultural Hub in Riyadh | Art Exhibitions & Events');?></title>
<link rel="icon" type="image/png" sizes="32x32" href="assest/logo/right_section.png">
<link rel="preload" as="image" href="heroImage/img-4.webp" fetchpriority="high">
<link rel="preload" href="assest/fonts/GretaArabicAR+LT-Regular.otf" as="font" type="font/otf" crossorigin>
<link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
<link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
<style><?php readfile('critical-inline-optimized.css');?></style>
<link rel="preload" href="deferred-styles-optimized.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
<link rel="preload" href="<?php echo getCSSFile('Home');?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
<link rel="preload" href="rtl.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
<link rel="preload" href="fonts/greta-arabic.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
<link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet" media="print" onload="this.media='all'">
<noscript>
<link rel="stylesheet" href="deferred-styles-optimized.css">
<link rel="stylesheet" href="<?php echo getCSSFile('Home');?>">
<link rel="stylesheet" href="rtl.css">
<link rel="stylesheet" href="fonts/greta-arabic.css">
<link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet">
</noscript>
<meta name="description" content="LAKUM Artspace is Riyadh's premier cultural destination for contemporary art exhibitions, creative workshops, and cultural events.">
<meta name="theme-color" content="#1a1a1a">
<meta name="mobile-web-app-capable" content="yes">
<script src="js/deferred-events-loader.js?v=1.0.0" defer></script>
<script src="js/api-cache-manager.js?v=1.0.0" defer></script>
<script src="assest/fun-interactions.js?v=5.0.0" defer></script>
<script src="js/LanguageManager.js?v=1.0.0" defer></script>
</head>
<body class="<?php echo getLanguageClass();?>">
<main>
<header class="lakum-header">
<div class="lakum-header__container">
<div class="lakum-header__logo">
<a href="./" class="lakum-logo">
<img src="assest/logo/right_section.png" alt="LAKUM" class="lakum-logo__left" width="105" height="80" decoding="async">
<img src="assest/logo/left_section.png" alt="Artspace" class="lakum-logo__right" width="105" height="80" decoding="async">
</a>
</div>
<nav class="lakum-nav">
<ul class="lakum-nav__list">
<li class="lakum-nav__item"><a href="index.php" class="lakum-nav__link lakum-nav__link--active"><?php echo t('home','Home');?></a></li>
<li class="lakum-nav__item"><a href="about.php" class="lakum-nav__link"><?php echo t('about','About');?></a></li>
<li class="lakum-nav__item"><a href="spaces.php" class="lakum-nav__link"><?php echo t('spaces','Spaces');?></a></li>
<li class="lakum-nav__item"><a href="exhibitions.php" class="lakum-nav__link"><?php echo t('exhibitions','Exhibitions');?></a></li>
<li class="lakum-nav__item"><a href="calendar.php" class="lakum-nav__link"><?php echo t('calendar','Calendar');?></a></li>
<li class="lakum-nav__item"><a href="blog.php" class="lakum-nav__link"><?php echo t('blog','Blog');?></a></li>
<li class="lakum-nav__item"><a href="press.php" class="lakum-nav__link"><?php echo t('press','Press');?></a></li>
<li class="lakum-nav__item"><a href="contact.php" class="lakum-nav__link"><?php echo t('contact_us','Contact');?></a></li>
<li class="lakum-nav__item"><a href="shop.php" class="lakum-nav__link"><?php echo t('shop','Shop');?></a></li>
</ul>
</nav>
<div class="lakum-language-switcher">
<a href="<?php echo buildLanguageSwitcherUrl();?>" class="lakum-lang-link" title="<?php echo isArabic()?'Language: English':'Language: العربية';?>">
<i class="ri-global-line"></i>
<span class="lakum-lang-text"><?php echo isArabic()?'En':'Ar';?></span>
</a>
</div>
<button class="lakum-header__mobile-toggle" aria-label="Toggle menu">
<span class="lakum-header__mobile-icon" aria-hidden="true"></span>
</button>
</div>
</header>

<section class="lakum-hero" style="aspect-ratio:16/9">
<div class="lakum-hero__image-wrapper">
<img src="heroImage/img-4.webp" alt="LAKUM Artspace - Where Encounters Shape Culture" fetchpriority="high" loading="eager" decoding="async" width="1200" height="800" class="lakum-hero__image" style="aspect-ratio:1200/800;width:100%;height:100%;object-fit:cover;display:block;">
<div class="lakum-hero__overlay"></div>
</div>
<div class="lakum-hero__content">
<h1 class="lakum-hero__title"><?php echo t('hero_title','Where Encounters Shape Culture');?></h1>
<p class="lakum-hero__subtitle"><?php echo t('hero_subtitle','A living space for art, connection, and cultural exchange in the heart of Riyadh');?></p>
</div>
</section>

<section class="lakum-section lakum-section--upcoming">
<div class="lakum-container">
<div class="lakum-section-header">
<h2 class="lakum-section-header__title"><?php echo t('upcoming_exhibitions','Upcoming Exhibitions');?></h2>
<p class="lakum-section-header__subtitle"><?php echo t('explore_exhibitions','Explore our recent artistic journeys and cultural moments');?></p>
</div>
</div>
</section>

<section class="lakum-featured-banner" id="featuredBanner">
<div class="lakum-featured-banner__content">
<div class="lakum-featured-banner__image">
<img src="heroImage/img-4.webp" alt="Featured Event" loading="lazy" decoding="async" width="800" height="450" id="featuredEventImage">
</div>
<div class="lakum-featured-banner__text">
<span class="lakum-featured-banner__date" id="featuredEventDate"><?php echo t('closest_event','Closest Event');?></span>
<h3 class="lakum-featured-banner__title" id="featuredEventTitle"><?php echo t('featured_exhibition','Featured Exhibition');?></h3>
<p class="lakum-featured-banner__description" id="featuredEventDesc"><?php echo t('featured_description','Discover this amazing exhibition showcasing contemporary art and culture.');?></p>
<a href="exhibitions.php" class="lakum-btn lakum-btn--primary" id="featuredEventLink"><?php echo t('view_details','View Details');?></a>
</div>
</div>
</section>

<section class="lakum-section">
<div class="lakum-container">
<div class="lakum-upcoming-grid" id="nextTwoEvents">
<div class="lakum-skeleton-card"></div>
<div class="lakum-skeleton-card"></div>
<div class="lakum-skeleton-card"></div>
</div>
<div class="lakum-section-cta">
<a href="exhibitions.php" class="lakum-btn lakum-btn--outline"><?php echo t('discover_more','Discover More');?></a>
</div>
</div>
</section>

<section class="lakum-cta lakum-cta--primary">
<div class="lakum-container">
<div class="lakum-cta__content">
<h2 class="lakum-cta__title"><?php echo t('cta_title','Driven by Soul, Made by Hands');?></h2>
<p class="lakum-cta__text"><?php echo t('cta_description','Explore our diverse spaces and discover how LAKUM can bring your artistic vision to life');?></p>
<a href="spaces.php" class="lakum-btn lakum-btn--primary"><?php echo t('discover_more','Discover More');?></a>
</div>
</div>
</section>

<section class="lakum-section lakum-section--exhibitions">
<div class="lakum-container">
<div class="lakum-section-divider">
<span class="lakum-section-divider__line"></span>
<h2 class="lakum-section-divider__title"><?php echo t('previous_exhibitions','Previous Exhibitions');?></h2>
<span class="lakum-section-divider__line"></span>
</div>
<div class="lakum-exhibition-grid" id="recentEvents">
<div class="lakum-skeleton-card"></div>
<div class="lakum-skeleton-card"></div>
<div class="lakum-skeleton-card"></div>
</div>
</div>
</section>

<section class="lakum-cta lakum-cta--dark">
<div class="lakum-cta__background" style="background-image:url('heroImage/img-4.webp');will-change:background-image;contain:layout style paint;"></div>
<div class="lakum-container">
<div class="lakum-cta__content">
<h2 class="lakum-cta__title"><?php echo t('create_event','Create Your Own Event');?></h2>
<p class="lakum-cta__text"><?php echo t('create_event_description','Transform your vision into reality with our versatile spaces and comprehensive support services');?></p>
<a href="spaces.php#form" class="lakum-btn lakum-btn--primary"><?php echo t('get_started','Get Started');?></a>
</div>
</div>
</section>

<footer class="lakum-footer">
<div class="lakum-footer__container">
<div class="lakum-footer__brand">
<div class="lakum-footer__logo">
<img src="assest/logo/right_section.png" alt="LAKUM" class="lakum-footer__logo-left" width="105" height="80" decoding="async">
<img src="assest/logo/left_section.png" alt="Artspace" class="lakum-footer__logo-right" width="105" height="80" decoding="async">
</div>
<p class="lakum-footer__tagline"><?php echo t('tagline','Where Encounters Shape Culture');?></p>
</div>
<nav class="lakum-footer__nav">
<h4 class="lakum-footer__nav-title"><?php echo t('navigate','Navigate');?></h4>
<ul class="lakum-footer__nav-list">
<li><a href="index.php" class="lakum-footer__link"><?php echo t('home','Home');?></a></li>
<li><a href="about.php" class="lakum-footer__link"><?php echo t('about','About');?></a></li>
<li><a href="spaces.php" class="lakum-footer__link"><?php echo t('spaces','Spaces');?></a></li>
<li><a href="exhibitions.php" class="lakum-footer__link"><?php echo t('exhibitions','Exhibitions');?></a></li>
</ul>
</nav>
<nav class="lakum-footer__nav">
<h4 class="lakum-footer__nav-title"><?php echo t('explore','Explore');?></h4>
<ul class="lakum-footer__nav-list">
<li><a href="calendar.php" class="lakum-footer__link"><?php echo t('calendar','Calendar');?></a></li>
<li><a href="blog.php" class="lakum-footer__link"><?php echo t('blog','Blog');?></a></li>
<li><a href="press.php" class="lakum-footer__link"><?php echo t('press','Press');?></a></li>
<li><a href="contact.php" class="lakum-footer__link"><?php echo t('contact_us','Contact');?></a></li>
</ul>
</nav>
<div class="lakum-footer__social"><h4 class="lakum-footer__nav-title"><?php echo t('connect','Connect');?></h4><div class="lakum-footer__social-links"><a href="https://www.instagram.com/lakumartspace/" target="_blank" class="lakum-footer__social-link" aria-label="Instagram"><i class="ri-instagram-fill"></i></a><a href="https://x.com/Lakumartspace" target="_blank" class="lakum-footer__social-link" aria-label="Twitter"><i class="ri-twitter-x-fill"></i></a></div></div>
</div>
<div class="lakum-footer__bottom">
<p class="lakum-footer__copyright"><?php echo t('footer_copyright','© 2025 - 2027 LAKUM Artspace. All rights reserved.');?></p>
<div class="lakum-footer__legal">
<a href="terms.php" class="lakum-footer__legal-link"><?php echo t('footer_terms','Terms & Conditions');?></a>
<span class="lakum-footer__legal-divider">|</span>
<a href="privacy.php" class="lakum-footer__legal-link"><?php echo t('footer_privacy','Privacy Policy');?></a>
</div>
</div>
</div>
</footer>

<div class="lakum-contact-fab" id="lakumContactFab"><button class="lakum-contact-fab__trigger" id="fabTrigger" aria-label="Contact options"><i class="ri-mail-line lakum-contact-fab__icon"></i><i class="ri-close-line lakum-contact-fab__close"></i></button><div class="lakum-contact-fab__menu" id="fabMenu"><a href="tel:+966920012083" class="lakum-contact-fab__item" data-tooltip="Call us"><i class="ri-phone-line"></i></a><a href="https://wa.me/966920012083" target="_blank" class="lakum-contact-fab__item" data-tooltip="WhatsApp"><i class="ri-whatsapp-line"></i></a><a href="mailto:info@lakumartspace.com" class="lakum-contact-fab__item" data-tooltip="Email"><i class="ri-mail-line"></i></a></div></div>

</main>

<script>
window.LAKUM_LANG='<?php echo getCurrentLanguage();?>';
async function loadFeaturedEvent(){try{const lang=window.LAKUM_LANG||'en';const response=await fetch(`api/get_events.php?type=all&limit=1000&lang=${lang}&t=${Date.now()}`,{headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}});if(!response.ok)throw new Error(`HTTP ${response.status}`);const data=await response.json();if(data.success&&data.data&&Array.isArray(data.data)){const now=new Date();now.setHours(0,0,0,0);const upcomingEvents=data.data.filter(e=>{const eventDate=new Date(e.event_date);eventDate.setHours(0,0,0,0);return eventDate>=now}).sort((a,b)=>new Date(a.event_date)-new Date(b.event_date));let featuredEvent=upcomingEvents.find(e=>e.is_featured==1);if(!featuredEvent&&upcomingEvents.length>0){featuredEvent=upcomingEvents[0]}if(featuredEvent){const eventDate=new Date(featuredEvent.event_date);const month=eventDate.toLocaleString('en-US',{month:'short'}).toUpperCase();const day=eventDate.getDate();const imgElement=document.getElementById('featuredEventImage');imgElement.src=featuredEvent.cover_image||'heroImage/img-4.webp';imgElement.alt=featuredEvent.title;imgElement.loading='lazy';imgElement.decoding='async';document.getElementById('featuredEventDate').textContent=`${month} ${day}`;document.getElementById('featuredEventTitle').textContent=featuredEvent.title;document.getElementById('featuredEventDesc').textContent=featuredEvent.description||featuredEvent.title;document.getElementById('featuredEventLink').href=`event.php?id=${featuredEvent.id}`}}}catch(error){console.error('Error loading featured event:',error)}}
if(document.readyState==='loading'){document.addEventListener('DOMContentLoaded',loadFeaturedEvent)}else{loadFeaturedEvent()}
</script>
<script src="assest/popup-notification.js?v=5.0.0" defer></script>
</body>
</html>
