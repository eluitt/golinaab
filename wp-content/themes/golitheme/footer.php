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
                <div class="gn-footer-widgets">
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'footer',
                        'menu_class'     => 'gn-footer-menu',
                        'container'      => false,
                        'fallback_cb'    => false,
                    ));
                    ?>
                </div>
                <div class="gn-footer-social">
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'social',
                        'menu_class'     => 'gn-social-menu',
                        'container'      => false,
                        'fallback_cb'    => '__return_empty_string',
                    ));
                    ?>
                </div>
                <div class="gn-footer-info">
                    <p class="gn-copyright">
                        &copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. 
                        <?php _e('All rights reserved.', 'golitheme'); ?>
                    </p>
                </div>
            </div>
        </div>
    </footer>
</div><!-- #page -->

<?php wp_footer(); ?>
</body>
</html>
