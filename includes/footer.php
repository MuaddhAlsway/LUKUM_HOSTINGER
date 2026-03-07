    <footer class="lakum-footer">
        <div class="lakum-footer__container">
            <div class="lakum-footer__content">
                <div class="lakum-footer__brand">
                    <div class="lakum-footer__logo">
                        <!-- English: Swapped -->
                        <img src="/assest/logo/right_section.png" alt="LAKUM" class="lakum-footer__logo-left" width="472" height="472" style="aspect-ratio: 472 / 472;" loading="lazy">
                        <img src="/assest/logo/left_section.png" alt="Artspace" class="lakum-footer__logo-right" width="472" height="177" style="aspect-ratio: 472 / 177;" loading="lazy">
                    </div>
                    <p class="lakum-footer__tagline"><?php echo t('footer_tagline', 'Where Encounters Shape Culture'); ?></p>
                </div>

                <nav class="lakum-footer__nav">
                    <h4 class="lakum-footer__nav-title"><?php echo t('footer_navigate', 'Navigate'); ?></h4>
                    <ul class="lakum-footer__nav-list">
                        <li><a href="/" class="lakum-footer__link"><?php echo t('home', 'Home'); ?></a></li>
                        <li><a href="/about" class="lakum-footer__link"><?php echo t('about', 'About'); ?></a></li>
                        <li><a href="/spaces" class="lakum-footer__link"><?php echo t('spaces', 'Spaces'); ?></a></li>
                        <li><a href="/exhibitions" class="lakum-footer__link"><?php echo t('exhibitions', 'Exhibitions'); ?></a></li>
                    </ul>
                </nav>

                <nav class="lakum-footer__nav">
                    <h4 class="lakum-footer__nav-title"><?php echo t('footer_explore', 'Explore'); ?></h4>
                    <ul class="lakum-footer__nav-list">
                        <li><a href="/calendar" class="lakum-footer__link"><?php echo t('calendar', 'Calendar'); ?></a></li>
                        <li><a href="/blog" class="lakum-footer__link"><?php echo t('blog', 'Blog'); ?></a></li>
                        <li><a href="/press" class="lakum-footer__link"><?php echo t('press', 'Press'); ?></a></li>
                        <li><a href="/contact" class="lakum-footer__link"><?php echo t('contact_us', 'Contact'); ?></a></li>
                    </ul>
                </nav>

                <div class="lakum-footer__social">
                    <h4 class="lakum-footer__nav-title"><?php echo t('footer_connect', 'Connect'); ?></h4>
                    <div class="lakum-footer__social-links">
                        <a href="https://www.instagram.com/lakumartspace/" target="_blank" class="lakum-footer__social-link" aria-label="Instagram"><i class="ri-instagram-fill"></i></a>
                        <a href="https://x.com/Lakumartspace" target="_blank" class="lakum-footer__social-link" aria-label="Twitter"><i class="ri-twitter-x-fill"></i></a>
                    </div>
                </div>
            </div>

            <div class="lakum-footer__bottom">
                <p class="lakum-footer__copyright"><?php echo t('footer_copyright_prefix', '© 2025 - '); ?><span id="year"></span><?php echo t('footer_copyright_suffix', ' LAKUM Artspace. All rights reserved.'); ?></p>
                <div class="lakum-footer__legal">
                    <a href="/terms.php?lang=<?php echo getCurrentLanguage(); ?>" class="lakum-footer__legal-link"><?php echo t('footer_terms', 'Terms & Conditions'); ?></a>
                    <span class="lakum-footer__legal-divider">|</span>
                    <a href="/privacy.php?lang=<?php echo getCurrentLanguage(); ?>" class="lakum-footer__legal-link"><?php echo t('footer_privacy', 'Privacy Policy'); ?></a>
                </div>
            </div>
        </div>
    </footer>
