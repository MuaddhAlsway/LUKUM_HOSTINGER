<?php
require_once 'lang/loader.php';
?><!DOCTYPE html>
<html <?php echo getLanguageAttributes(); ?>>

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - LAKUM Artspace</title>
    <link rel="icon" type="image/png" sizes="32x32" href="assest/logo/right_section.png">
    <link rel="icon" type="image/png" sizes="16x16" href="assest/logo/right_section.png">
    <link rel="apple-touch-icon" href="assest/logo/right_section.png">
    <meta name="msapplication-TileImage" content="assest/logo/right_section.png">

    <!-- Inline Critical CSS for Instant LCP -->
    <style>
        /* Critical CSS - Inline for instant rendering */

        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0
        }

        html {
            font-size: 16px;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Helvetica Neue', sans-serif;
            background: #f6f6eb;
            color: #1a1a1a;
            overflow-x: hidden;
            line-height: 1.6
        }
        
        * {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Helvetica Neue', sans-serif;
        }

        .lakum-hero {
            position: relative;
            width: 100%;
            height: 85vh;
            min-height: 600px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #1a1a1a;
            contain: layout style paint
        }

        .lakum-hero__image-wrapper {
            position: absolute;
            inset: 0;
            z-index: 1;
            overflow: hidden
        }

        .lakum-hero__image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            will-change: transform;
            transform: translateZ(0)
        }

        .lakum-hero__overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to bottom, rgba(0, 0, 0, 0.5) 0%, rgba(0, 0, 0, 0.65) 100%);
            z-index: 2
        }

        .lakum-hero__content {
            position: relative;
            z-index: 3;
            text-align: center;
            color: #fff;
            max-width: 1400px;
            width: 90%;
            padding: 0 20px
        }

        .lakum-hero__title {
            font-size: clamp(2.5rem, 6vw, 4.5rem);
            font-weight: 300;
            line-height: 1.2;
            margin: 0 0 20px 0;
            color: #fff;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.3)
        }

        .lakum-hero__subtitle {
            font-size: clamp(1.1rem, 2vw, 1.4rem);
            font-weight: 300;
            line-height: 1.6;
            color: #fff;
            text-shadow: 0 2px 6px rgba(0, 0, 0, 0.3)
        }

        @media(max-width:768px) {
            .lakum-hero {
                height: 60vh;
                min-height: 450px
            }
        }

        @media(max-width:480px) {
            .lakum-hero {
                height: 50vh;
                min-height: 400px
            }
        }
    </style>

    <!-- Preload Hero Image (Critical for LCP) -->
    <link rel="preload" as="image" href="assest/img-4.png" fetchpriority="high">

    <!-- DNS Prefetch for external resources -->
    <link rel="dns-prefetch" href="https://fonts.googleapis.com">
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">

    <!-- Preconnect to external domains -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdn.jsdelivr.net">

    <!-- Preload critical assets -->
    <link rel="preload" href="global-styles.css" as="style">
    <link rel="preload" href="lakum-components.css" as="style">
    

    <!-- Preload critical fonts -->
    <link rel="prefetch" href="assest/fonts/GretaArabicAR+LT-Regular.otf" as="font" type="font/otf" crossorigin>
    <link rel="prefetch" href="assest/fonts/GretaArabicAR+LT-Light.otf" as="font" type="font/otf" crossorigin>

    <!-- Core Styles - Critical CSS loaded synchronously -->
    <link rel="stylesheet" href="global-styles.css">
    <link rel="stylesheet" href="lakum-components.css">
    <link rel="stylesheet" href="Home.css">

    <!-- RTL Styles -->
    <link rel="stylesheet" href="rtl.css">

    <!-- Fonts -->
    <link rel="stylesheet" href="fonts/greta-arabic.css">

    <!-- Popup Notification Styles -->
    <link rel="stylesheet" href="assest/popup-notification.css">

    <!-- Icons - Defer non-critical icon loading -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript><link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet"></noscript>

    <!-- Image Optimizer - Critical for performance -->
    <script src="assest/navbar-mobile-toggle.js?v=5.0.0" defer></script>

    <!-- Scripts - Defer non-critical JavaScript -->
    <script src="assest/popup-notification.js?v=5.0.0" defer></script>
    <script>
        // Set language for JavaScript - Read from URL parameter or localStorage
        const urlParams = new URLSearchParams(window.location.search);
        const urlLang = urlParams.get('lang');
        const storedLang = localStorage.getItem('lakum_language');
        window.LAKUM_LANG = urlLang || storedLang || 'en';
        window.LAKUM_DIR = window.LAKUM_LANG === 'ar' ? 'rtl' : 'ltr';

        // Performance monitoring
        if ('PerformanceObserver' in window) {
            // Monitor Largest Contentful Paint
            const lcpObserver = new PerformanceObserver((list) => {
                const entries = list.getEntries();
                const lastEntry = entries[entries.length - 1];
                console.log('LCP:', lastEntry.renderTime || lastEntry.loadTime);
            });
            lcpObserver.observe({
                entryTypes: ['largest-contentful-paint']
            });

            // Monitor First Input Delay
            const fidObserver = new PerformanceObserver((list) => {
                const entries = list.getEntries();
                entries.forEach((entry) => {
                    console.log('FID:', entry.processingStart - entry.startTime);
                });
            });
            fidObserver.observe({
                entryTypes: ['first-input']
            });
        }
    </script>
    <link rel="stylesheet" href="contact.css">
<link rel="alternate" hreflang="en" href="https://lakumartspace.infinityfree.me/contact.php?lang=en" />
<link rel="alternate" hreflang="ar" href="https://lakumartspace.infinityfree.me/contact.php?lang=ar" />
<script src="assest/static-json-translator.js?v=1.0.0" defer></script></head>

<body class="<?php echo getLanguageClass(); ?>">

    <!-- Page Loader -->
    <div class="lakum-page-loader" id="pageLoader">
        <div class="lakum-page-loader__content">
            <div class="lakum-page-loader__spinner"></div>
        </div>
    </div>

    <header class="lakum-header">

        <div class="lakum-header__container">
            <div class="lakum-header__logo">
                <a href="index.php" class="lakum-logo">
                    <!-- English: Swapped -->
                    <img src="assest/logo/right_section.png" alt="LAKUM" class="lakum-logo__left">
                    <img src="assest/logo/left_section.png" alt="Artspace" class="lakum-logo__right">
                </a>
            </div>

            <nav class="lakum-nav">
                <ul class="lakum-nav__list">
                    <li class="lakum-nav__item">
                        <a href="index.php" class="lakum-nav__link "><?php echo t('home', 'Home'); ?></a>
                    </li>
                    <li class="lakum-nav__item">
                        <a href="about.php" class="lakum-nav__link "><?php echo t('about', 'About'); ?></a>
                    </li>
                    <li class="lakum-nav__item">
                        <a href="spaces.php" class="lakum-nav__link "><?php echo t('spaces', 'Spaces'); ?></a>
                    </li>
                    <li class="lakum-nav__item">
                        <a href="exhibitions.php" class="lakum-nav__link "><?php echo t('exhibitions', 'Exhibitions'); ?></a>
                    </li>
                    <li class="lakum-nav__item">
                        <a href="calendar.php" class="lakum-nav__link "><?php echo t('calendar', 'Calendar'); ?></a>
                    </li>
                    <li class="lakum-nav__item">
                        <a href="blog.php" class="lakum-nav__link "><?php echo t('blog', 'Blog'); ?></a>
                    </li>
                    <li class="lakum-nav__item">
                        <a href="press.php" class="lakum-nav__link "><?php echo t('press', 'Press'); ?></a>
                    </li>
                    <li class="lakum-nav__item">
                        <a href="contact.php" class="lakum-nav__link lakum-nav__link--active"><?php echo t('contact_us', 'Contact'); ?></a>
                    </li>
                    <li class="lakum-nav__item">
                        <a href="shop.php" class="lakum-nav__link "><?php echo t('shop', 'Shop'); ?></a>
                    </li>

                </ul>
            </nav>

            <!-- Language Switcher -->
            <div class="lakum-language-switcher">
                <a href="<?php echo buildLanguageSwitcherUrl(); ?>" class="lakum-lang-link" title="<?php echo isArabic() ? 'Language: English' : 'Language: العربية'; ?>">
                <i class="ri-global-line"></i>
                <span class="lakum-lang-text"><?php echo isArabic() ? 'EN' : 'AR'; ?></span>
            </a>
            </div>

            <button class="lakum-header__mobile-toggle" aria-label="Toggle menu">
            <span class="lakum-header__mobile-icon"></span>
        </button>
        </div>
    </header>

    <script>
        // Intelligent Page Loader - Proper Implementation
        (function() {
            const loader = document.getElementById('pageLoader');
            if (!loader) return;

            // Hide loader immediately on page load
            hideLoader();

            function showLoader() {
                loader.classList.add('lakum-page-loader--active');
            }

            function hideLoader() {
                loader.classList.remove('lakum-page-loader--active');
            }

            // Detect if link will open in new tab
            function willOpenInNewTab(event, link) {
                // Check if link has target attribute
                if (link.target && link.target !== '_self') {
                    return true;
                }

                // Check for modifier keys (Ctrl, Cmd, Shift)
                if (event.ctrlKey || event.metaKey || event.shiftKey) {
                    return true;
                }

                // Check for middle mouse button
                if (event.button === 1) {
                    return true;
                }

                return false;
            }

            // Check if link is valid for loader
            function shouldShowLoader(event, link) {
                // No href
                if (!link.href) return false;

                // JavaScript link
                if (link.href.startsWith('javascript:')) return false;

                // External link
                try {
                    const linkUrl = new URL(link.href);
                    const currentUrl = new URL(window.location.href);

                    // Different domain
                    if (linkUrl.hostname !== currentUrl.hostname) return false;

                    // Same page (including hash)
                    if (linkUrl.pathname === currentUrl.pathname) return false;

                } catch (e) {
                    return false;
                }

                // Will open in new tab
                if (willOpenInNewTab(event, link)) return false;

                return true;
            }

            // Handle link clicks
            document.addEventListener('click', function(e) {
                const link = e.target.closest('a');
                if (!link) return;

                if (shouldShowLoader(e, link)) {
                    showLoader();
                }
            }, true); // Use capture phase

            // Handle middle mouse button
            document.addEventListener('auxclick', function(e) {
                // Middle click (button 1) should not show loader
                if (e.button === 1) {
                    hideLoader();
                }
            });

            // Hide loader when page becomes visible (handles all load scenarios)
            function handlePageLoad() {
                hideLoader();
            }

            // Multiple events to ensure loader hides
            window.addEventListener('pageshow', handlePageLoad);
            window.addEventListener('load', handlePageLoad);
            document.addEventListener('DOMContentLoaded', handlePageLoad);

            // Handle back/forward navigation
            window.addEventListener('popstate', handlePageLoad);

            // Hide if user switches tabs
            document.addEventListener('visibilitychange', function() {
                if (!document.hidden) {
                    hideLoader();
                }
            });

            // Failsafe: hide after 5 seconds
            let loaderTimeout;
            document.addEventListener('click', function(e) {
                const link = e.target.closest('a');
                if (link && shouldShowLoader(e, link)) {
                    clearTimeout(loaderTimeout);
                    loaderTimeout = setTimeout(hideLoader, 5000);
                }
            }, true);

            // Clear timeout on page load
            window.addEventListener('pageshow', function() {
                clearTimeout(loaderTimeout);
            });

        })();
    </script>

    <!-- Hero Section -->
    <section class="lakum-contact-hero">
        <div class="lakum-contact-hero__overlay"></div>
        <div class="lakum-container">
            <div class="lakum-contact-hero__content">
                <h1 class="lakum-contact-hero__title"><?php echo t('hero_title', 'Get In Touch'); ?></h1>
                <p class="lakum-contact-hero__subtitle"><?php echo t('hero_subtitle', 'Have a question or want to collaborate? We\'d love to hear from you. Reach out and let\'s create something amazing together.'); ?></p>
            </div>
        </div>
    </section>

    <!-- Contact Content -->
    <section class="lakum-contact-content">
        <div class="lakum-container">
            <div class="lakum-contact-grid">
                <!-- Contact Information -->
                <div class="lakum-contact-info">
                    <h2 class="lakum-contact-info__title"><?php echo t('contact_info_title', 'Contact Information'); ?></h2>

                    <div class="lakum-contact-info__items">
                        <div class="lakum-contact-item">
                            <div class="lakum-contact-item__icon">
                                <i class="ri-map-pin-line"></i>
                            </div>
                            <div class="lakum-contact-item__content">
                                <h3 class="lakum-contact-item__title"><?php echo t('contact_visit_title', 'Visit Us'); ?></h3>
                                <p class="lakum-contact-item__text"><?php echo t('contact_visit_address', 'Al Urubah Branch Rd, Umm Al Hamam Al Gharbi'); ?><br><?php echo t('contact_visit_city', 'Riyadh 12328, Saudi Arabia'); ?></p>
                            </div>
                        </div>

                        <div class="lakum-contact-item">
                            <div class="lakum-contact-item__icon">
                                <i class="ri-phone-line"></i>
                            </div>
                            <div class="lakum-contact-item__content">
                                <h3 class="lakum-contact-item__title"><?php echo t('contact_call_title', 'Call Us'); ?></h3>
                                <p class="lakum-contact-item__text"><a href="tel:+966920012083"><?php echo t('contact_call_number', '+966 920 012 083'); ?></a></p>
                            </div>
                        </div>

                        <div class="lakum-contact-item">
                            <div class="lakum-contact-item__icon">
                                <i class="ri-mail-line"></i>
                            </div>
                            <div class="lakum-contact-item__content">
                                <h3 class="lakum-contact-item__title"><?php echo t('contact_email_title', 'Email Us'); ?></h3>
                                <p class="lakum-contact-item__text"><a href="mailto:info@lakumartspace.com"><?php echo t('contact_email_address', 'info@lakumartspace.com'); ?></a></p>
                            </div>
                        </div>

                        <div class="lakum-contact-item">
                            <div class="lakum-contact-item__icon">
                                <i class="ri-time-line"></i>
                            </div>
                            <div class="lakum-contact-item__content">
                                <h3 class="lakum-contact-item__title"><?php echo t('contact_hours_title', 'Opening Hours'); ?></h3>
                                <p class="lakum-contact-item__text"><?php echo t('contact_hours_weekday', 'Sunday - Thursday: 10:00 AM - 10:00 PM'); ?><br><?php echo t('contact_hours_weekend', 'Friday - Saturday: 2:00 PM - 11:00 PM'); ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="lakum-contact-social">
                        <h3 class="lakum-contact-social__title"><?php echo t('contact_connect_title', 'Connect'); ?></h3>
                        <div class="lakum-contact-social__links">
                            <a href="https://www.instagram.com/lakumartspace/" target="_blank" class="lakum-contact-social__link"><i class="ri-instagram-fill"></i></a>
                            <a href="https://x.com/Lakumartspace" target="_blank" class="lakum-contact-social__link"><i class="ri-twitter-x-fill"></i></a>
                        </div>
                    </div>
                </div>

                <!-- Contact Form -->
                <div class="lakum-contact-form-wrapper">
                    <h2 class="lakum-contact-form__title"><?php echo t('form_title', 'Send Us a Message'); ?></h2>

                    <form class="lakum-contact-form" id="contactForm" method="POST">
                        <div class="lakum-form__group">
                            <label for="name" class="lakum-form__label"><?php echo t('form_name_label', 'Full Name'); ?> *</label>
                            <div class="lakum-form__input-wrapper">
                                <i class="ri-user-line lakum-form__icon"></i>
                                <input type="text" id="name" name="name" class="lakum-form__input" placeholder="<?php echo t('form_name_placeholder', 'John Doe'); ?>" required>
                            </div>
                        </div>

                        <div class="lakum-form__group">
                            <label for="email" class="lakum-form__label"><?php echo t('form_email_label', 'Email Address'); ?> *</label>
                            <div class="lakum-form__input-wrapper">
                                <i class="ri-mail-line lakum-form__icon"></i>
                                <input type="email" id="email" name="email" class="lakum-form__input" placeholder="<?php echo t('form_email_placeholder', 'john@example.com'); ?>" required>
                            </div>
                        </div>

                        <div class="lakum-form__group">
                            <label for="phone" class="lakum-form__label"><?php echo t('form_phone_label', 'Phone Number'); ?></label>
                            <div class="lakum-form__input-wrapper">
                                <i class="ri-phone-line lakum-form__icon"></i>
                                <input type="tel" id="phone" name="phone" class="lakum-form__input" placeholder="<?php echo t('form_phone_placeholder', '+966 5XX XXX XXX'); ?>">
                            </div>
                        </div>

                        <div class="lakum-form__group">
                            <label for="subject" class="lakum-form__label"><?php echo t('form_subject_label', 'Subject'); ?> *</label>
                            <div class="lakum-form__input-wrapper">
                                <i class="ri-bookmark-line lakum-form__icon"></i>
                                <select id="subject" name="subject" class="lakum-form__select" required>
                                    <option value=""><?php echo t('form_subject_placeholder', 'Select a subject'); ?></option>
                                    <option value="general"><?php echo t('form_subject_general', 'General Inquiry'); ?></option>
                                    <option value="exhibition"><?php echo t('form_subject_exhibition', 'Exhibition Booking'); ?></option>
                                    <option value="workshop"><?php echo t('form_subject_workshop', 'Workshop Information'); ?></option>
                                    <option value="venue"><?php echo t('form_subject_venue', 'Venue Rental'); ?></option>
                                    <option value="collaboration"><?php echo t('form_subject_collaboration', 'Collaboration'); ?></option>
                                    <option value="other"><?php echo t('form_subject_other', 'Other'); ?></option>
                                </select>
                            </div>
                        </div>

                        <div class="lakum-form__group">
                            <label for="message" class="lakum-form__label"><?php echo t('form_message_label', 'Message'); ?> *</label>
                            <textarea id="message" name="message" class="lakum-form__textarea" rows="5" placeholder="<?php echo t('form_message_placeholder', 'Tell us more about your inquiry...'); ?>" required></textarea>
                        </div>

                        <button type="submit" class="lakum-btn lakum-btn--primary lakum-btn--large">
                            <span><?php echo t('form_submit', 'Send Message'); ?></span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Map Section -->
    <section class="lakum-contact-map">
        <div class="lakum-container">
            <div class="lakum-contact-map__wrapper">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3624.9!2d46.6437815!3d24.7007377!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3e2f1d7057b45f57:0x96059bc262d04422!2sLakum%20Artspace!5e0!3m2!1sen!2ssa!4v1702000000000" class="lakum-contact-map__iframe"
                    allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
        </div>
    </section>

    <footer class="lakum-footer">
        <div class="lakum-footer__container">
            <div class="lakum-footer__content">
                <div class="lakum-footer__brand">
                    <div class="lakum-footer__logo">
                        <!-- English: Swapped -->
                        <img src="assest/logo/right_section.png" alt="LAKUM" class="lakum-footer__logo-left">
                        <img src="assest/logo/left_section.png" alt="Artspace" class="lakum-footer__logo-right">
                    </div>
                    <p class="lakum-footer__tagline">Where Encounters Shape Culture</p>
                </div>

                <nav class="lakum-footer__nav">
                    <h4 class="lakum-footer__nav-title"><?php echo t('footer_navigate', 'Navigate'); ?></h4>
                    <ul class="lakum-footer__nav-list">
                        <li><a href="index.php" class="lakum-footer__link"><?php echo t('home', 'Home'); ?></a></li>
                        <li><a href="about.php" class="lakum-footer__link"><?php echo t('about', 'About'); ?></a></li>
                        <li><a href="spaces.php" class="lakum-footer__link"><?php echo t('spaces', 'Spaces'); ?></a></li>
                        <li><a href="exhibitions.php" class="lakum-footer__link"><?php echo t('exhibitions', 'Exhibitions'); ?></a></li>
                    </ul>
                </nav>

                <nav class="lakum-footer__nav">
                    <h4 class="lakum-footer__nav-title"><?php echo t('footer_explore', 'Explore'); ?></h4>
                    <ul class="lakum-footer__nav-list">
                        <li><a href="calendar.php" class="lakum-footer__link"><?php echo t('calendar', 'Calendar'); ?></a></li>
                        <li><a href="blog.php" class="lakum-footer__link"><?php echo t('blog', 'Blog'); ?></a></li>
                        <li><a href="press.php" class="lakum-footer__link"><?php echo t('press', 'Press'); ?></a></li>
                        <li><a href="contact.php" class="lakum-footer__link"><?php echo t('contact_us', 'Contact'); ?></a></li>
                    </ul>
                </nav>

                <div class="lakum-footer__social">
                    <h4 class="lakum-footer__nav-title"><?php echo t('footer_connect', 'Connect'); ?></h4>
                    <div class="lakum-footer__social-links">
                        <a href="https://www.instagram.com/lakumartspace/" target="_blank" class="lakum-footer__social-link" aria-label="Instagram">
                        <i class="ri-instagram-fill"></i>
                    </a>
                        <a href="https://x.com/Lakumartspace" target="_blank" class="lakum-footer__social-link" aria-label="Twitter">
                        <i class="ri-twitter-x-fill"></i>
                    </a>
                    </div>
                </div>
            </div>

            <div class="lakum-footer__bottom">
                <p class="lakum-footer__copyright">Â© 2025 - 2027 LAKUM Artspace. All rights reserved.</p>
                <div class="lakum-footer__legal">
                    <a href="terms.php" class="lakum-footer__legal-link">Terms & Conditions</a>
                    <span class="lakum-footer__legal-divider">|</span>
                    <a href="privacy.php" class="lakum-footer__legal-link">Privacy Policy</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Expandable Floating Contact Button -->
    <div class="lakum-contact-fab" id="lakumContactFab">
        <button class="lakum-contact-fab__trigger" id="fabTrigger" aria-label="Contact options">
        <i class="ri-mail-line lakum-contact-fab__icon"></i>
        <i class="ri-close-line lakum-contact-fab__close"></i>
    </button>

        <div class="lakum-contact-fab__menu" id="fabMenu">
            <a href="tel:+966920012083" class="lakum-contact-fab__item" data-tooltip="Call us">
            <i class="ri-phone-line"></i>
        </a>
            <a href="https://wa.me/966920012083" target="_blank" class="lakum-contact-fab__item" data-tooltip="WhatsApp">
            <i class="ri-whatsapp-line"></i>
        </a>
            <a href="mailto:info@lakumartspace.com" class="lakum-contact-fab__item" data-tooltip="Email">
            <i class="ri-mail-line"></i>
        </a>
        </div>
    </div>

    <script>
        // Expandable FAB functionality
        (function() {
            const fab = document.getElementById('lakumContactFab');
            const trigger = document.getElementById('fabTrigger');
            const menu = document.getElementById('fabMenu');

            if (trigger && fab) {
                trigger.addEventListener('click', function(e) {
                    e.preventDefault();
                    fab.classList.toggle('lakum-contact-fab--active');
                });

                // Close when clicking outside
                document.addEventListener('click', function(e) {
                    if (!fab.contains(e.target)) {
                        fab.classList.remove('lakum-contact-fab--active');
                    }
                });
            }
        })();

        // Mobile menu toggle
        (function() {
            const toggle = document.querySelector('.lakum-header__mobile-toggle');
            const nav = document.querySelector('.lakum-nav');
            const header = document.querySelector('.lakum-header');

            if (toggle && nav) {
                toggle.addEventListener('click', function() {
                    toggle.classList.toggle('lakum-header__mobile-toggle--active');
                    nav.classList.toggle('lakum-nav--active');
                    header.classList.toggle('lakum-header--menu-open');
                    document.body.style.overflow = nav.classList.contains('lakum-nav--active') ? 'hidden' : '';
                });

                // Close menu when clicking nav link
                const navLinks = document.querySelectorAll('.lakum-nav__link');
                navLinks.forEach(link => {
                    link.addEventListener('click', function() {
                        toggle.classList.remove('lakum-header__mobile-toggle--active');
                        nav.classList.remove('lakum-nav--active');
                        header.classList.remove('lakum-header--menu-open');
                        document.body.style.overflow = '';
                    });
                });
            }
        })();
    </script>

    </body>

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
        footer_copyright: "<?php echo t('footer_copyright', '© 2026 LAKUM Artspace. All rights reserved.'); ?>",
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
            footerCopyright.textContent = translations.footer_copyright || '© 2026 LAKUM Artspace. All rights reserved.';
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

<script src="assest/popup-notification.js?v=5.0.0" defer></script>

</html>










