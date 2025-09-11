<?php
/**
 * GoliNaab Theme Functions
 * 
 * @package GoliNaab
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define theme constants
define('GN_THEME_VERSION', '1.0.0');
define('GN_THEME_PATH', get_template_directory());
define('GN_THEME_URL', get_template_directory_uri());

/**
 * Theme setup
 */
function gn_theme_setup() {
    // Add theme support
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script'
    ));
    add_theme_support('custom-logo');
    add_theme_support('responsive-embeds');
    
    // Register navigation menus
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'golitheme'),
        'footer' => __('Footer Menu', 'golitheme'),
        'social' => __('Social Links', 'golitheme'),
    ));
    
    // Add editor styles
    add_theme_support('editor-styles');
    add_editor_style('assets/styles/editor-style.css');
}
add_action('after_setup_theme', 'gn_theme_setup');

/**
 * Enqueue scripts and styles
 */
function gn_enqueue_assets() {
    // Main stylesheet (Tailwind CSS)
    wp_enqueue_style(
        'gn-style',
        GN_THEME_URL . '/assets/styles/style.css',
        array(),
        GN_THEME_VERSION
    );
    
    // Font preloading for Persian (Doran) and English (Crimson Pro)
    gn_preload_fonts();
    
    // Main theme script
    wp_enqueue_script(
        'gn-script',
        GN_THEME_URL . '/assets/scripts/main.js',
        array(),
        file_exists(GN_THEME_PATH . '/assets/scripts/main.js') ? filemtime(GN_THEME_PATH . '/assets/scripts/main.js') : GN_THEME_VERSION,
        true
    );
    
    // Header functionality script (conditional)
    if (is_front_page() || is_home() || is_page()) {
        wp_enqueue_script(
            'gn-header-script',
            GN_THEME_URL . '/assets/scripts/header.js',
            array(),
            file_exists(GN_THEME_PATH . '/assets/scripts/header.js') ? filemtime(GN_THEME_PATH . '/assets/scripts/header.js') : GN_THEME_VERSION,
            true
        );
        
        // Add nonce data for search
        wp_add_inline_script('gn-header-script', 'window.gn_ajax = ' . wp_json_encode(array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('gn_ajax_nonce'),
        )), 'before');
    }
    
    // Hero functionality script (conditional - only on home page)
    if (is_front_page() || is_home()) {
        wp_enqueue_script(
            'gn-hero-script',
            GN_THEME_URL . '/assets/scripts/hero.js',
            array(),
            file_exists(GN_THEME_PATH . '/assets/scripts/hero.js') ? filemtime(GN_THEME_PATH . '/assets/scripts/hero.js') : GN_THEME_VERSION,
            true
        );
        // Lightweight particles overlay for hero (decorative)
        wp_enqueue_script(
            'gn-hero-particles',
            GN_THEME_URL . '/assets/scripts/hero-particles.js',
            array(),
            file_exists(GN_THEME_PATH . '/assets/scripts/hero-particles.js') ? filemtime(GN_THEME_PATH . '/assets/scripts/hero-particles.js') : GN_THEME_VERSION,
            true
        );
        // Categories modal script (Step 3)
        wp_enqueue_script(
            'gn-categories-script',
            GN_THEME_URL . '/assets/scripts/categories.js',
            array(),
            file_exists(GN_THEME_PATH . '/assets/scripts/categories.js') ? filemtime(GN_THEME_PATH . '/assets/scripts/categories.js') : GN_THEME_VERSION,
            true
        );
        // Sliders (home only)
        wp_enqueue_script(
            'gn-sliders-script',
            GN_THEME_URL . '/assets/scripts/sliders.js',
            array(),
            file_exists(GN_THEME_PATH . '/assets/scripts/sliders.js') ? filemtime(GN_THEME_PATH . '/assets/scripts/sliders.js') : GN_THEME_VERSION,
            true
        );
    }
    
    // Localize script for AJAX
    wp_localize_script('gn-script', 'gn_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('gn_ajax_nonce'),
    ));
}
add_action('wp_enqueue_scripts', 'gn_enqueue_assets');

/**
 * Add defer to non-critical JS (front-end only)
 */
add_filter('script_loader_tag', function($tag, $handle, $src){
    if (is_admin()) return $tag;
    $defer_for = array(
        'gn-script', 'gn-header-script', 'gn-hero-script', 'gn-hero-particles', 'gn-categories-script', 'gn-sliders-script', 'gn-rental'
    );
    if (in_array($handle, $defer_for, true)) {
        // Keep before inline scripts if any are printed 'before'
        $tag = str_replace('<script ', '<script defer ', $tag);
    }
    return $tag;
}, 10, 3);

/**
 * Output base meta tags (OG, theme color, manifest)
 */
function gn_output_meta_tags() {
    $title       = is_singular() ? single_post_title('', false) : get_bloginfo('name');
    $description = is_singular() ? wp_strip_all_tags(get_the_excerpt(null)) : get_bloginfo('description');
    $url         = esc_url(home_url(add_query_arg(array(), $_SERVER['REQUEST_URI'] ?? '')));
    $image       = '';
    if (is_singular() && has_post_thumbnail()) {
        $image = esc_url(get_the_post_thumbnail_url(get_queried_object_id(), 'large'));
    } elseif (function_exists('has_site_icon') && has_site_icon()) {
        $image = esc_url(get_site_icon_url(512));
    }
    $theme_color = '#C8A2C8';
    echo "\n<!-- GoliNaab base meta -->\n";
    echo '<meta name="theme-color" content="' . esc_attr($theme_color) . '">' . "\n";
    echo '<link rel="manifest" href="' . esc_url(GN_THEME_URL . '/assets/manifest.json') . '">' . "\n";
    echo '<meta property="og:site_name" content="' . esc_attr(get_bloginfo('name')) . '">' . "\n";
    echo '<meta property="og:title" content="' . esc_attr($title) . '">' . "\n";
    if (!empty($description)) echo '<meta property="og:description" content="' . esc_attr($description) . '">' . "\n";
    echo '<meta property="og:url" content="' . esc_attr($url) . '">' . "\n";
    if (!empty($image)) echo '<meta property="og:image" content="' . esc_attr($image) . '">' . "\n";
    echo "<!-- /GoliNaab base meta -->\n";
}
add_action('wp_head', 'gn_output_meta_tags', 5);

// Preload hero background image to improve LCP on home
add_action('wp_head', function(){
    if (is_front_page() || is_home()) {
        $bg = get_theme_mod('gn_hero_background', '');
        if (empty($bg)) { $bg = GN_THEME_URL . '/assets/images/flower.png'; }
        echo '<link rel="preload" as="image" href="'.esc_url($bg).'" imagesrcset="'.esc_url($bg).'">' . "\n";
    }
}, 7);

/**
 * Detect EN site by /en path prefix
 */
function gn_is_en_site(): bool {
    $uri = $_SERVER['REQUEST_URI'] ?? '/';
    if ($uri === '') { $uri = '/'; }
    return ($uri === '/en' || $uri === '/en/' || strpos($uri, '/en/') === 0);
}

// Add body class for EN site → CSS already handles LTR + font
add_filter('body_class', function(array $classes){
    if (gn_is_en_site()) { $classes[] = 'en-site'; }
    return $classes;
});

/** Output hreflang alternates for fa/en */
function gn_output_hreflang_links() {
    $uri = $_SERVER['REQUEST_URI'] ?? '/';
    if ($uri === '') { $uri = '/'; }
    if (gn_is_en_site()) {
        $fa_path = preg_replace('#^/en/?#', '/', $uri);
        if ($fa_path === '') { $fa_path = '/'; }
        $en_path = $uri;
    } else {
        $fa_path = $uri;
        $en_path = '/en' . ($uri[0] === '/' ? '' : '/') . $uri;
    }
    $fa_url = home_url($fa_path);
    $en_url = home_url($en_path);
    echo '<link rel="alternate" hreflang="fa" href="'.esc_url($fa_url).'">' . "\n";
    echo '<link rel="alternate" hreflang="en" href="'.esc_url($en_url).'">' . "\n";
}
add_action('wp_head', 'gn_output_hreflang_links', 6);

/** Language toggle (cookie + redirect) */
function gn_lang_toggle_script() { ?>
<script>
(function(){
  var btn=document.querySelector('.gn-lang-toggle'); if(!btn) return;
  btn.addEventListener('click',function(){
    var isEn = location.pathname === '/en' || location.pathname.indexOf('/en/')===0;
    var target = isEn ? location.pathname.replace(/^\/en\/?/,'/') : '/en' + (location.pathname[0]==='/'?'':'/') + location.pathname;
    target = target.replace(/\/\/+/, '/');
    document.cookie='gn_lang='+(isEn?'fa':'en')+';path=/;max-age='+(60*60*24*365);
    location.href = target + location.search + location.hash;
  });
})();
</script>
<?php }
add_action('wp_footer','gn_lang_toggle_script', 20);

/**
 * Preload critical fonts
 */
function gn_preload_fonts() {
    // Preload Persian font (Doran)
    echo '<link rel="preload" href="' . GN_THEME_URL . '/assets/fonts/doran-regular.woff2" as="font" type="font/woff2" crossorigin>';
    
    // Preload English font (Crimson Pro) - local fallback
    echo '<link rel="preload" href="' . GN_THEME_URL . '/assets/fonts/crimson-pro-regular.woff2" as="font" type="font/woff2" crossorigin>';
}

/**
 * Include required files
 */
require_once GN_THEME_PATH . '/inc/setup.php';
require_once GN_THEME_PATH . '/inc/search.php';
require_once GN_THEME_PATH . '/inc/cpt.php';
require_once GN_THEME_PATH . '/inc/ajax.php';

// Enqueue rental form script only on the Rental Request template
add_action('wp_enqueue_scripts', function(){
    if (is_page_template('templates/page-rental.php')) {
        wp_enqueue_script(
            'gn-rental',
            GN_THEME_URL . '/assets/scripts/rental.js',
            array(),
            file_exists(GN_THEME_PATH . '/assets/scripts/rental.js') ? filemtime(GN_THEME_PATH . '/assets/scripts/rental.js') : GN_THEME_VERSION,
            true
        );
        wp_localize_script('gn-rental','gn_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php')
        ));
    }
});
