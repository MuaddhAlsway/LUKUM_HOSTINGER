<?php
require_once 'lang/loader.php';
if (file_exists('api/image-helper.php')) { require_once 'api/image-helper.php'; }

// Get title from query parameter (rewritten by .htaccess)
$title = $_GET['title'] ?? null;
$lang = $_GET['lang'] ?? 'en';

// If no title, show error
if (!$title) {
    header('HTTP/1.0 404 Not Found');
    exit('Press release not found');
}
?><!DOCTYPE html>
<html <?php echo getLanguageAttributes(); ?>>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title id="page-title">Press - LAKUM Artspace</title>
    <link rel="icon" type="image/png" sizes="32x32" href="assest/logo/right_section.png">
    <link rel="icon" type="image/png" sizes="16x16" href="assest/logo/right_section.png">
    <link rel="apple-touch-icon" href="assest/logo/right_section.png">
    <meta name="msapplication-TileImage" content="assest/logo/right_section.png">

    <!-- Preload LCP image (hero) -->
    <link rel="preload" as="image" 
          href="heroImage/img-4.webp"
          imagesrcset="heroImage/img-4.webp 1200w"
          imagesizes="(max-width: 768px) 100vw, 650px"
          fetchpriority="high">
    <!-- Preload critical fonts -->
    <link rel="preload" href="assest/fonts/GretaArabicAR+LT-Regular.otf" as="font" type="font/otf" crossorigin>

    <!-- Global Stylesheets -->
    <?php include('includes/stylesheets.php'); ?>

    <!-- Page-specific styles -->
    <link rel="stylesheet" href="press.css">
    <style>
        .press-section {
            padding: 60px 0;
            background: #f6f6eb;
        }
        .press-container {
            max-width: 860px;
            margin: 0 auto;
            padding: 0 24px;
        }
        .press-content {
            background: #fff;
            border-radius: 4px;
            padding: 48px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.08);
            font-size: 16px;
            line-height: 1.8;
            color: #333;
        }
        .press-content p { margin-bottom: 16px; }
        .press-content h2, .press-content h3 { margin: 28px 0 12px; color: #1a1a1a; }
        @media (max-width: 600px) {
            .press-content { padding: 28px 20px; }
        }
    </style>
    <script src="assest/static-json-translator.js?v=1.0.0" defer></script>
</head>

<body class="<?php echo getLanguageClass(); ?>">
    <div class="lakum-page-loader" id="pageLoader">
        <div class="lakum-page-loader__content">
            <div class="lakum-page-loader__spinner"></div>
        </div>
    </div>

    <!-- Global Header Navigation (Unified) -->
    <?php include('lakum-header-unified.php'); ?>

    <!-- Hero Section -->
    <section class="lakum-hero">
        <div class="lakum-hero__image-wrapper">
            <img id="hero-image" src="assest/img-4.webp" alt="Press" class="lakum-hero__image" loading="eager" fetchpriority="high" decoding="async">
            <div class="lakum-hero__overlay"></div>
        </div>
        <div class="lakum-hero__content">
            <h1 class="lakum-hero__title" id="press-title">Loading...</h1>
            <p class="lakum-hero__subtitle" id="press-date">Loading...</p>
        </div>
    </section>

    <!-- Press Content -->
    <section class="press-section">
        <div class="press-container">
            <div class="press-content">
                <div id="press-content">Loading...</div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include('includes/footer.php'); ?>

    <script>
        async function loadPressData() {
            const title = '<?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?>';
            const lang = '<?php echo htmlspecialchars($lang, ENT_QUOTES, 'UTF-8'); ?>';
            
            if (!title || title === 'pressPageDetails') {
                document.getElementById('press-content').innerHTML = '<p style="color: red;">Press release not found</p>';
                return;
            }
            
            try {
                const response = await fetch(`/api/get_press.php?title=${title}&lang=${lang}`);
                const data = await response.json();
                
                if (data.success && data.data) {
                    displayPress(data.data, lang);
                } else {
                    document.getElementById('press-content').innerHTML = '<p style="color: red;">Press release not found</p>';
                }
            } catch (error) {
                console.error('Error loading press:', error);
                document.getElementById('press-content').innerHTML = '<p style="color: red;">Error loading press release</p>';
            }
        }
        
        function displayPress(press, lang) {
            document.getElementById('page-title').textContent = press.title + ' - LAKUM Artspace';
            document.getElementById('press-title').textContent = press.title;
            document.getElementById('press-date').textContent = new Date(press.press_date).toLocaleDateString();
            document.getElementById('press-content').innerHTML = press.content || press.excerpt || 'No content available';
            
            const heroImage = document.getElementById('hero-image');
            heroImage.src = press.cover_image || 'assest/img-4.webp';
            heroImage.alt = press.title;
        }
        
        document.addEventListener('DOMContentLoaded', loadPressData);
    </script>

    <!-- Expandable Floating Contact Button -->
    <div class="fab-button" id="fabButton">
        <button class="fab-button__trigger" id="fabTrigger" aria-label="Contact options" aria-expanded="false">
            <i class="ri-mail-line fab-button__icon"></i>
            <i class="ri-close-line fab-button__close"></i>
        </button>
        <div class="fab-button__menu" id="fabMenu" role="menu">
            <a href="tel:+966920012083" class="fab-button__item" role="menuitem" data-tooltip="Call us">
                <i class="ri-phone-line"></i>
            </a>
            <a href="https://wa.me/966920012083" target="_blank" class="fab-button__item" role="menuitem" data-tooltip="WhatsApp">
                <i class="ri-whatsapp-line"></i>
            </a>
            <a href="mailto:info@lakumartspace.com" class="fab-button__item" role="menuitem" data-tooltip="Email">
                <i class="ri-mail-line"></i>
            </a>
        </div>
    </div>

    <script src="assest/fun-interactions.js" defer></script>
    <script src="assest/navbar-mobile-toggle.js" defer></script>

    <!-- Global Scripts (Centralized) -->
    <?php include('includes/scripts.php'); ?>
</body>
</html>
