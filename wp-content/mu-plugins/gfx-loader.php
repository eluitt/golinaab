<?php
// File: wp-content/mu-plugins/gfx/gfx-loader.php
// Loader اصلی برای همه فایل‌های PHP داخل gfx/
// (اگر در آینده خواستید JS یا پوشهٔ دیگری اضافه کنید، اینجاییم.)

if ( ! defined('ABSPATH') ) {
    exit;
}

$dir = __DIR__ . '/gfx';
if ( is_dir($dir) ) {
    $files = glob($dir . '/*.php');
    sort($files, SORT_NATURAL | SORT_FLAG_CASE);
    foreach ( $files as $file ) {
        require_once $file;
    }
}
