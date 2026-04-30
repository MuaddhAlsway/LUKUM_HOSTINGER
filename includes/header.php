<?php
/**
 * LAKUM Artspace - Global Header Navigation
 * This is the single source of truth for all page headers
 * All pages must include this file to ensure consistent navigation
 */
?>
    <!-- LAKUM Header -->
    <header class="lakum-header" role="banner">
        <div class="lakum-header__container">
            <div class="lakum-header__logo">
                <a href="./" class="lakum-logo">
                    <img src="assest/logo/right_section.png" alt="LAKUM" class="lakum-logo__left">
                    <img src="assest/logo/left_section.png" alt="Artspace" class="lakum-logo__right">
                </a>
            </div>
            <nav class="lakum-nav">
                <ul class="lakum-nav__list">
                    <li class="lakum-nav__item"><a href="index.php" class="lakum-nav__link <?php echo (basename($_SERVER['PHP_SELF']) === 'index.php') ? 'lakum-nav__link--active' : ''; ?>"><?php echo t('home', 'Home'); ?></a></li>
                    <li class="lakum-nav__item"><a href="about.php" class="lakum-nav__link <?php echo (basename($_SERVER['PHP_SELF']) === 'about.php') ? 'lakum-nav__link--active' : ''; ?>"><?php echo t('about', 'About'); ?></a></li>
                    <li class="lakum-nav__item"><a href="spaces.php" class="lakum-nav__link <?php echo (basename($_SERVER['PHP_SELF']) === 'spaces.php') ? 'lakum-nav__link--active' : ''; ?>"><?php echo t('spaces', 'Exhibitions'); ?></a></li>
                    <li class="lakum-nav__item"><a href="exhibitions.php" class="lakum-nav__link <?php echo (basename($_SERVER['PHP_SELF']) === 'exhibitions.php') ? 'lakum-nav__link--active' : ''; ?>"><?php echo t('exhibitions', 'Events'); ?></a></li>
                    <li class="lakum-nav__item"><a href="calendar.php" class="lakum-nav__link <?php echo (basename($_SERVER['PHP_SELF']) === 'calendar.php') ? 'lakum-nav__link--active' : ''; ?>"><?php echo t('calendar', 'Venue Hire'); ?></a></li>
                    <li class="lakum-nav__item"><a href="blog.php" class="lakum-nav__link <?php echo (basename($_SERVER['PHP_SELF']) === 'blog.php') ? 'lakum-nav__link--active' : ''; ?>"><?php echo t('blog', 'Blog'); ?></a></li>
                    <li class="lakum-nav__item"><a href="press.php" class="lakum-nav__link <?php echo (basename($_SERVER['PHP_SELF']) === 'press.php') ? 'lakum-nav__link--active' : ''; ?>"><?php echo t('press', 'Press'); ?></a></li>
                    <li class="lakum-nav__item"><a href="contact.php" class="lakum-nav__link <?php echo (basename($_SERVER['PHP_SELF']) === 'contact.php') ? 'lakum-nav__link--active' : ''; ?>"><?php echo t('contact_us', 'Contact'); ?></a></li>
                    <li class="lakum-nav__item"><a href="shop.php" class="lakum-nav__link <?php echo (basename($_SERVER['PHP_SELF']) === 'shop.php') ? 'lakum-nav__link--active' : ''; ?>"><?php echo t('shop', 'Shop'); ?></a></li>
                </ul>
            </nav>
            <div class="lakum-language-switcher">
                <a href="<?php echo buildLanguageSwitcherUrl(); ?>" class="lakum-lang-link" title="<?php echo isArabic() ? 'Language: English' : 'Language: العربية'; ?>">
                    <i class="ri-global-line"></i>
                    <span class="lakum-lang-text"><?php echo isArabic() ? 'EN' : 'AR'; ?></span>
                </a>
            </div>
            <button class="lakum-header__mobile-toggle" aria-label="Toggle menu" aria-expanded="false">
                <span class="lakum-header__mobile-icon" aria-hidden="true"></span>
            </button>
        </div>
    </header>
