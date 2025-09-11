<?php
/**
 * The footer template
 * 
 * @package GoliNaab
 * @since 1.0.0
 */
?>
    <footer id="gn-colophon" class="gn-footer">
        <div class="gn-container">
            <div class="gn-footer-content">
                <div class="gn-footer-brand">
                    <a class="gn-footer-logo" href="<?php echo esc_url(home_url('/')); ?>">
                        <?php bloginfo('name'); ?>
                    </a>
                    <p class="gn-footer-tagline"><?php bloginfo('description'); ?></p>
                    <div class="gn-footer-social" role="navigation" aria-label="<?php esc_attr_e('شبکه‌های اجتماعی', 'golitheme'); ?>">
                        <?php
                        wp_nav_menu(array(
                            'theme_location' => 'social',
                            'menu_class'     => 'gn-social-menu',
                            'container'      => false,
                            'fallback_cb'    => '__return_empty_string',
                        ));
                        ?>
                        <ul class="gn-social-menu">
                        <li>
                            <a class="gn-social-link gn-social-telegram" href="#" target="_blank" rel="noopener" aria-label="<?php esc_attr_e('تلگرام', 'golitheme'); ?>">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path d="M9.04 15.52 8.89 19a1 1 0 0 0 1.58.86l2.27-1.54 3.78 2.77c.69.51 1.66.12 1.85-.73l3.56-15.49c.2-.87-.63-1.6-1.45-1.26L2.2 9.43c-.94.39-.88 1.75.09 2l5.02 1.32 11.62-7.18-9.89 9.95Z" fill="#2CA5E0"/>
                                </svg>
                            </a>
                        </li>
                        <li>
                            <a class="gn-social-link gn-social-instagram" href="#" target="_blank" rel="noopener" aria-label="<?php esc_attr_e('اینستاگرام', 'golitheme'); ?>">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <rect x="3" y="3" width="18" height="18" rx="5" stroke="#9A7B9A" stroke-width="1.6"/>
                                    <circle cx="12" cy="12" r="4.2" stroke="#9A7B9A" stroke-width="1.6"/>
                                    <circle cx="17.5" cy="6.5" r="1.2" fill="#9A7B9A"/>
                                </svg>
                            </a>
                        </li>
                        </ul>
                    </div>
                </div>
                <div class="gn-footer-widgets">
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'footer',
                        'menu_class'     => 'gn-footer-menu',
                        'container'      => false,
                        'fallback_cb'    => 'wp_page_menu',
                    ));
                    ?>
                </div>
                <div class="gn-footer-legal text-center" role="contentinfo" aria-label="<?php esc_attr_e('اطلاعات حقوقی', 'golitheme'); ?>">
                    <p class="gn-legal-text">
                        <?php esc_html_e('استفاده از سایت مشروط بر قبول توافق نامه کاربری و حفظ حریم شخصی است. حقوق مادی و معنوی این سایت متعلق به مجموعه گلیناب است. نسخه 1', 'golitheme'); ?>
                    </p>
                </div>
            </div>
        </div>
    </footer>
</div><!-- #page -->

<?php wp_footer(); ?>
</body>
</html>
