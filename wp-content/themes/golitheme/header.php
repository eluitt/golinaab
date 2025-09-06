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
    
    <header id="masthead" class="gn-header">
        <div class="gn-container">
            <div class="gn-header-content">
                <div class="gn-site-branding">
                    <?php
                    if (has_custom_logo()) {
                        the_custom_logo();
                    } else {
                        ?>
                        <h1 class="gn-site-title">
                            <a href="<?php echo esc_url(home_url('/')); ?>" rel="home">
                                <?php bloginfo('name'); ?>
                            </a>
                        </h1>
                        <?php
                        $description = get_bloginfo('description', 'display');
                        if ($description || is_customize_preview()) {
                            ?>
                            <p class="gn-site-description"><?php echo $description; ?></p>
                            <?php
                        }
                    }
                    ?>
                </div>
                
                <nav id="gn-site-navigation" class="gn-main-navigation">
                    <button class="gn-menu-toggle" aria-controls="gn-primary-menu" aria-expanded="false">
                        <span class="gn-menu-toggle-text"><?php _e('Menu', 'golitheme'); ?></span>
                        <span class="gn-menu-toggle-icon">
                            <span></span>
                            <span></span>
                            <span></span>
                        </span>
                    </button>
                    
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'primary',
                        'menu_id'        => 'gn-primary-menu',
                        'menu_class'     => 'gn-primary-menu',
                        'container'      => false,
                        'fallback_cb'    => false,
                    ));
                    ?>
                </nav>
            </div>
        </div>
    </header>
