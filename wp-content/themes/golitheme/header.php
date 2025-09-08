<?php
/**
 * The header template
 * 
 * @package GoliNaab
 * @since 1.0.0
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div id="page" class="gn-site">
    <a class="gn-skip-link" href="#main"><?php _e('Skip to content', 'golitheme'); ?></a>
    
    <header id="masthead" class="gn-header gn-header-overlay" role="banner" aria-label="<?php esc_attr_e('Main header','golitheme'); ?>">
        <div class="gn-container">
            <div class="gn-header-bar" role="navigation">
                <div class="gn-header-brand">
                    <?php if (has_custom_logo()) { the_custom_logo(); } else { ?>
                        <a class="gn-header-logo" href="<?php echo esc_url(home_url('/')); ?>"><?php bloginfo('name'); ?></a>
                        <span class="gn-header-tagline"><?php bloginfo('description'); ?></span>
                    <?php } ?>
                </div>

                <div class="gn-header-search">
                    <form class="gn-search-form" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
                        <div class="gn-search-input-wrapper">
                            <input type="search" class="gn-search-input"
                                placeholder="<?php _e('جستجو در محصولات و دوره‌ها...', 'golitheme'); ?>"
                                value="<?php echo get_search_query(); ?>" name="s" autocomplete="off"
                                aria-label="<?php _e('Search', 'golitheme'); ?>" />
                            <button type="submit" class="gn-search-submit" aria-label="<?php _e('Search', 'golitheme'); ?>">
                                <svg class="gn-search-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="11" cy="11" r="8"></circle>
                                    <path d="m21 21-4.35-4.35"></path>
                                </svg>
                            </button>
                        </div>
                        <div class="gn-search-results" id="gn-search-results" role="listbox" aria-live="polite" aria-label="<?php _e('Search results', 'golitheme'); ?>"></div>
                    </form>
                </div>

                <div class="gn-header-icons">
                    <div class="gn-left-wrap">
                        <button class="gn-header-icon gn-left-toggle" aria-label="<?php esc_attr_e('Toggle menu','golitheme'); ?>">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#9A7B9A" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                              <path d="M4 6h16M4 12h12M4 18h16"/>
                            </svg>
                        </button>
                        <aside id="gn-left-mini" class="gn-left-mini" aria-hidden="true" aria-label="<?php esc_attr_e('Quick panel','golitheme'); ?>">
                            <div class="gn-left-mini__inner">
                                <div class="gn-left-mini__section">
                                    <h3 class="gn-left-mini__title"><?php _e('دسترسی سریع','golitheme'); ?></h3>
                                    <ul class="gn-left-mini__list">
                                        <li><a href="<?php echo esc_url(home_url('/')); ?>"><?php _e('خانه','golitheme'); ?></a></li>
                                        <li><a href="<?php echo esc_url(home_url('/shop')); ?>"><?php _e('فروشگاه','golitheme'); ?></a></li>
                                        <li><a href="<?php echo esc_url(home_url('/courses')); ?>"><?php _e('دوره‌ها','golitheme'); ?></a></li>
                                    </ul>
                                </div>
                            </div>
                        </aside>
                    </div>
                    <a class="gn-header-icon gn-account" href="<?php echo esc_url( function_exists('wc_get_page_id') ? get_permalink(wc_get_page_id('myaccount')) : wp_login_url() ); ?>" aria-label="<?php esc_attr_e('Account','golitheme'); ?>">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#D4AF37" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                          <path d="M20 21c0-4-4-6-8-6s-8 2-8 6"/>
                          <circle cx="12" cy="7" r="4"/>
                        </svg>
                    </a>
                    <a class="gn-header-icon gn-cart" href="<?php echo esc_url( function_exists('wc_get_cart_url') ? wc_get_cart_url() : home_url('/cart') ); ?>" aria-label="<?php esc_attr_e('Cart','golitheme'); ?>">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#9A7B9A" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                          <circle cx="9" cy="20" r="1.6"/>
                          <circle cx="17" cy="20" r="1.6"/>
                          <path d="M3 4h2l2.4 11.2a2 2 0 0 0 2 1.6h6.6a2 2 0 0 0 2-1.6L20 8H7"/>
                        </svg>
                    </a>
                    <button class="gn-menu-toggle" aria-controls="gn-primary-menu" aria-expanded="false">
                        <span class="gn-menu-toggle-text"><?php _e('Menu', 'golitheme'); ?></span>
                        <span class="gn-menu-toggle-icon"><span></span><span></span><span></span></span>
                    </button>
                </div>
            </div>
        </div>
        <nav id="gn-site-navigation" class="gn-main-navigation" aria-label="<?php esc_attr_e('Primary','golitheme'); ?>">
            <?php wp_nav_menu(array(
                'theme_location' => 'primary',
                'menu_id'        => 'gn-primary-menu',
                'menu_class'     => 'gn-primary-menu',
                'container'      => false,
                'fallback_cb'    => false,
            )); ?>
        </nav>
    </header>
