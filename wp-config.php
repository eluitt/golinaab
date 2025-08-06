<?php
/**
 * Optimised wp-config.php for “golinaab” (local development)
 * – حافظت از کلیدها و اطلاعات مخصوص
 * – تنظیم خودکار حالت توسعه/پروداکشن
 */

 /*--------------------------------------------------------------
 | ۱) داده‌های پایگاه‌داده
 *--------------------------------------------------------------*/
define( 'DB_NAME',     'golinaab' );
define( 'DB_USER',     'root'     );
define( 'DB_PASSWORD', ''         );
define( 'DB_HOST',     '127.0.0.1' );
define( 'DB_CHARSET',  'utf8mb4'  );
define( 'DB_COLLATE',  ''         );

 /*--------------------------------------------------------------
 | ۲) کلیدها و سالت‌های یکتا
 *--------------------------------------------------------------*/
define('AUTH_KEY',         'vK5CW6 Zvq~Et@s6b0O9dGmO_?<aP{,+hVYuAVQz wawqDE$<4LtP8&sf[601*#m');
define('SECURE_AUTH_KEY',  '`2Xesy}-uU,hM{-;V!/}yWvY{5]-Lhb:F1c6+=91+_LtAO?R<`}&g/IpQMW.DzV ');
define('LOGGED_IN_KEY',    '5+~oPzBh$J5{(5;%/>TNQ,ExtdEUKm~u.W3JxEBtCHGO[$[%m$}qh)1t?blR!Q2|');
define('NONCE_KEY',        '9ff`n3Ia{S$bp@kw?yQ0;=-FAVe`9NdP#R#;6(~0GX/cAI|Zne|ZiKmA|Lvv<H3{');
define('AUTH_SALT',        'cD$WB8]mat2Mm[q<[xTejzU!Fg-vRR7+46cch}I~Tjp;bUByM*q-0|66Oc=NB+%>');
define('SECURE_AUTH_SALT', 'f<KE|F=^y&:u]AJz9D(u^=!Gbjp[muBp+]3MyK |fd|ov>UXg3^@g7SSQ+u 1YU>');
define('LOGGED_IN_SALT',   '(hp~zE+).5j|k-O-^%LH|pl|b_~&$9dH2!qf`00g2*eBV?h_k}]T&EGPX-m?lkgP');
define('NONCE_SALT',       'dF$.N`-t+mhb(z;sWTes4>~(P$K-y-GJC8|Xfs$OFYBlv3{,%C^+y2g&OXGm|zO8');

 /*--------------------------------------------------------------
 | ۳) پیشوند جداول
 *--------------------------------------------------------------*/
$table_prefix = 'gfx_';

 /*--------------------------------------------------------------
 | ۴) تشخیص محیط و دیباگ
 *--------------------------------------------------------------*/
define( 'WP_ENVIRONMENT_TYPE', getenv('WP_ENV') ?: 'development' );

if ( WP_ENVIRONMENT_TYPE === 'production' ) {
	define( 'WP_DEBUG',       false );
	define( 'WP_DEBUG_LOG',   false );
	define( 'SCRIPT_DEBUG',   false );
} else {
	define( 'WP_DEBUG',       true  );
	define( 'WP_DEBUG_LOG',   true  );
	define( 'SCRIPT_DEBUG',   true  );
}

 /*--------------------------------------------------------------
 | ۵) بهینه‌سازی منابع و امنیت
 *--------------------------------------------------------------*/
define( 'WP_MEMORY_LIMIT',     '256M' );
define( 'WP_MAX_MEMORY_LIMIT', '512M' );
define( 'AUTOSAVE_INTERVAL',   120 );
define( 'WP_POST_REVISIONS',   5 );
define( 'DISALLOW_FILE_EDIT',  true );   // غیرفعال‌سازی ادیتور در پیشخوان
define( 'DISALLOW_FILE_MODS',  false );  // اجازهٔ آپدیت هسته/افزونه
define( 'WP_CACHE',            true );   // فعال در استیج و لایو
define( 'FS_METHOD',           'direct' );
define( 'WP_REDIS_DISABLED', false );
 /*--------------------------------------------------------------
 | ۶) تعیین داینامیک آدرس سایت (خارج از WP-CLI)
 *--------------------------------------------------------------*/
if ( ! defined( 'WP_CLI' ) ) {
	$scheme = ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] === 'on' ) ? 'https' : 'http';
	$host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
	define( 'WP_HOME',    "{$scheme}://{$host}" );
	define( 'WP_SITEURL', WP_HOME );
}

 /*--------------------------------------------------------------
 | ۷) بارگذاری هسته
 *--------------------------------------------------------------*/
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}
require_once ABSPATH . 'wp-settings.php';
