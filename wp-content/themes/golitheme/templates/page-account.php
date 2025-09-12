<?php
/**
 * Template Name: Account (Login/Register + Dashboard)
 * Description: Shows login/register when logged out, and a simple dashboard when logged in.
 */
defined('ABSPATH') || exit;

$gold          = '#D4AF37';
$lavender      = '#C8A2C8';
$lavenderSoft  = '#EBDDF9';

// If guest, we will not include theme header/footer to keep page minimal
$gn_is_guest_page = ! is_user_logged_in();

function gn_account_handle_login() {
    if (empty($_POST['gn_login_submit'])) return;
    if (!wp_verify_nonce($_POST['gn_login_nonce'] ?? '', 'gn_login')) {
        wp_die(__('Security check failed.', 'golitheme'));
    }
    $username = sanitize_text_field(wp_unslash($_POST['username'] ?? ''));
    $password = $_POST['password'] ?? '';
    $remember = !empty($_POST['remember_me']);
    $user = wp_signon([
        'user_login'    => $username,
        'user_password' => $password,
        'remember'      => $remember,
    ], false);
    if (is_wp_error($user)) {
        set_transient('gn_login_error', $user->get_error_message(), 60);
        return;
    }
    $return_to = isset($_POST['return_to']) ? esc_url_raw($_POST['return_to']) : '';
    if (empty($return_to) || strpos($return_to, home_url()) !== 0) {
        $return_to = home_url('/account');
    }
    wp_safe_redirect($return_to);
    exit;
}

function gn_account_handle_register() {
    if (empty($_POST['gn_register_submit'])) return;
    if (!wp_verify_nonce($_POST['gn_register_nonce'] ?? '', 'gn_register')) {
        wp_die(__('Security check failed.', 'golitheme'));
    }
    if (!empty($_POST['website'])) { // honeypot
        wp_die(__('Bot detected.', 'golitheme'));
    }
    $full_name = sanitize_text_field(wp_unslash($_POST['full_name'] ?? ''));
    $username  = sanitize_user($_POST['reg_username'] ?? '');
    $email     = sanitize_email($_POST['reg_email'] ?? '');
    $password  = $_POST['reg_password'] ?? '';

    $errs = new WP_Error();
    if (!$full_name || !$username || !$email || !$password) $errs->add('required', __('همۀ فیلدها الزامی است.', 'golitheme'));
    if (username_exists($username)) $errs->add('u', __('نام کاربری تکراری است.', 'golitheme'));
    if (email_exists($email))      $errs->add('e', __('ایمیل تکراری است.', 'golitheme'));

    if ($errs->has_errors()) {
        set_transient('gn_register_error', implode('<br>', $errs->get_error_messages()), 60);
        return;
    }

    if (function_exists('wc_create_new_customer')) {
        $user_id = wc_create_new_customer($email, $username, $password);
        if (is_wp_error($user_id)) {
            set_transient('gn_register_error', $user_id->get_error_message(), 60);
            return;
        }
    } else {
        $user_id = wp_create_user($username, $password, $email);
        if (is_wp_error($user_id)) {
            set_transient('gn_register_error', $user_id->get_error_message(), 60);
            return;
        }
    }

    $parts = preg_split('/\s+/', trim($full_name), 2);
    update_user_meta($user_id, 'first_name', $parts[0] ?? '');
    if (!empty($parts[1])) update_user_meta($user_id, 'last_name', $parts[1]);

    wp_set_current_user($user_id);
    wp_set_auth_cookie($user_id, true);
    wp_safe_redirect(home_url('/account'));
    exit;
}

function gn_account_handle_profile_update() {
    if (empty($_POST['gn_profile_update']) || !is_user_logged_in()) return;
    if (!wp_verify_nonce($_POST['gn_profile_nonce'] ?? '', 'gn_profile')) {
        wp_die(__('Security check failed.', 'golitheme'));
    }
    $user_id = get_current_user_id();
    $first   = sanitize_text_field($_POST['first_name'] ?? '');
    $last    = sanitize_text_field($_POST['last_name'] ?? '');
    $display = sanitize_text_field($_POST['display_name'] ?? '');
    $email   = sanitize_email($_POST['user_email'] ?? '');
    $pass    = $_POST['new_password'] ?? '';

    wp_update_user([
        'ID'           => $user_id,
        'display_name' => $display ?: ($first . ' ' . $last),
        'user_email'   => $email,
    ]);
    update_user_meta($user_id, 'first_name', $first);
    update_user_meta($user_id, 'last_name', $last);

    if ($pass !== '') {
        wp_set_password($pass, $user_id);
        wp_set_auth_cookie($user_id, true);
    }
    set_transient('gn_profile_notice', __('تغییرات با موفقیت ذخیره شد.', 'golitheme'), 60);
}

gn_account_handle_login();
gn_account_handle_register();
gn_account_handle_profile_update();

$login_error    = get_transient('gn_login_error') ?: '';
$register_error = get_transient('gn_register_error') ?: '';
$profile_notice = get_transient('gn_profile_notice') ?: '';
delete_transient('gn_login_error');
delete_transient('gn_register_error');
delete_transient('gn_profile_notice');
?>
<?php if ( $gn_is_guest_page ) : ?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php wp_head(); ?>
</head>
<body <?php body_class('login-template'); ?>>
  <?php wp_body_open(); ?>
<?php else : get_header(); endif; ?>

<main id="main" class="gn-main">
  <section class="gn-container<?php echo is_user_logged_in() ? '' : ' gn-login-center'; ?>" style="max-width:980px;padding:2rem 1rem;">
    <?php if (!is_user_logged_in()) : ?>
      <div class="gn-card gn-login-card" style="background:#fff;border-radius:1.5rem;box-shadow:0 10px 30px rgba(200,162,200,.12);padding:1.5rem;">
        <a href="<?php echo esc_url(home_url('/')); ?>" class="gn-back-btn" aria-label="بازگشت">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#4b2a5a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <polyline points="15 18 9 12 15 6"></polyline>
            <line x1="9" y1="12" x2="21" y2="12"></line>
          </svg>
        </a>

        <div class="gn-login-logo" style="text-align:center; margin: .25rem 0 1rem;">
          <?php if (function_exists('the_custom_logo') && has_custom_logo()) : ?>
            <?php the_custom_logo(); ?>
          <?php else : ?>
            <a href="<?php echo esc_url(home_url('/')); ?>" style="text-decoration:none;color:#4b2a5a;font-weight:700;"><?php bloginfo('name'); ?></a>
          <?php endif; ?>
        </div>

        <div class="gn-tab-nav">
          <button class="gn-tab-btn active" data-target="#gn-login">ورود</button>
          <button class="gn-tab-btn" data-target="#gn-register">ثبت‌نام</button>
        </div>

        <?php if ($login_error): ?>
          <div style="background:#ffecec;color:#c00;padding:.75rem;border-radius:.75rem;margin-bottom:1rem;"><?php echo wp_kses_post($login_error); ?></div>
        <?php endif; ?>
        <?php if ($register_error): ?>
          <div style="background:#ffecec;color:#c00;padding:.75rem;border-radius:.75rem;margin-bottom:1rem;"><?php echo wp_kses_post($register_error); ?></div>
        <?php endif; ?>

        <div id="gn-login" class="gn-tab-panel" style="display:block;">
          <form method="post" class="gn-account" style="display:grid;gap:.75rem;">
            <?php wp_nonce_field('gn_login', 'gn_login_nonce'); ?>
            <input type="hidden" name="return_to" value="<?php echo esc_attr($_GET['return_to'] ?? ''); ?>">
            <input type="text" name="username" placeholder="نام کاربری یا ایمیل" required>
            <input type="password" name="password" placeholder="رمز عبور" required>
            <label style="display:flex;align-items:center;gap:.5rem;color:#4b2a5a;">
              <input type="checkbox" name="remember_me"> مرا به خاطر بسپار
            </label>
            <button type="submit" name="gn_login_submit" class="gn-account-btn">ورود</button>
            <a href="<?php echo esc_url(wp_lostpassword_url()); ?>" style="color:<?php echo esc_attr($lavender);?>;text-decoration:none;">فراموشی رمز عبور؟</a>
          </form>
        </div>

        <div id="gn-register" class="gn-tab-panel" style="display:none;">
          <form method="post" class="gn-account" style="display:grid;gap:.75rem;">
            <?php wp_nonce_field('gn_register', 'gn_register_nonce'); ?>
            <input type="text" name="full_name" placeholder="نام و نام خانوادگی" required>
            <input type="text" name="reg_username" placeholder="نام کاربری" required>
            <input type="email" name="reg_email" placeholder="ایمیل" required>
            <input type="password" name="reg_password" placeholder="رمز عبور" required>
            <input type="text" name="website" value="" style="display:none !important;" tabindex="-1" autocomplete="off">
            <button type="submit" name="gn_register_submit" class="gn-account-btn">ثبت‌نام</button>
          </form>
        </div>
      </div>

      <!-- animations handled by assets/scripts/auth.js -->

    <?php else: ?>
      <?php $user_id = get_current_user_id(); $user = get_userdata($user_id); ?>
      <?php if ($profile_notice = get_transient('gn_profile_notice')): delete_transient('gn_profile_notice'); ?>
        <div style="background:#ecfff2;color:#0a8a2a;padding:.75rem;border-radius:.75rem;margin-bottom:1rem;"><?php echo esc_html($profile_notice); ?></div>
      <?php endif; ?>

      <div class="gn-grid" style="display:grid;gap:1rem;grid-template-columns:1fr;align-items:start;">
        <div class="gn-card" style="background:#fff;border-radius:1.5rem;box-shadow:0 10px 30px rgba(200,162,200,.12);padding:1.25rem;">
          <h2 style="margin:.25rem 0 1rem;color:#4b2a5a;">اطلاعات حساب</h2>
          <form method="post" style="display:grid;gap:.75rem;">
            <?php wp_nonce_field('gn_profile','gn_profile_nonce'); ?>
            <label>نام
              <input type="text" name="first_name" value="<?php echo esc_attr(get_user_meta($user_id,'first_name',true)); ?>" style="border:1px solid #d7c6e7;border-radius:1rem;padding:.6rem;background:#f6f0fa;color:#4b2a5a;">
            </label>
            <label>نام خانوادگی
              <input type="text" name="last_name" value="<?php echo esc_attr(get_user_meta($user_id,'last_name',true)); ?>" style="border:1px solid #d7c6e7;border-radius:1rem;padding:.6rem;background:#f6f0fa;color:#4b2a5a;">
            </label>
            <label>نمایش عمومی
              <input type="text" name="display_name" value="<?php echo esc_attr($user->display_name); ?>" style="border:1px solid #d7c6e7;border-radius:1rem;padding:.6rem;background:#f6f0fa;color:#4b2a5a;">
            </label>
            <label>ایمیل
              <input type="email" name="user_email" value="<?php echo esc_attr($user->user_email); ?>" style="border:1px solid #d7c6e7;border-radius:1rem;padding:.6rem;background:#f6f0fa;color:#4b2a5a;">
            </label>
            <label>رمز جدید (اختیاری)
              <input type="password" name="new_password" value="" placeholder="••••••••" style="border:1px solid #d7c6e7;border-radius:1rem;padding:.6rem;background:#f6f0fa;color:#4b2a5a;">
            </label>
            <button type="submit" name="gn_profile_update" style="justify-self:start;background:<?php echo esc_attr($gold);?>;color:#2C2C2C;font-weight:600;border:none;border-radius:1rem;padding:.6rem 1rem;cursor:pointer;">ذخیره تغییرات</button>
          </form>
        </div>

        <div class="gn-card" style="background:#fff;border-radius:1.5rem;box-shadow:0 10px 30px rgba(200,162,200,.12);padding:1.25rem;">
          <h2 style="margin:.25rem 0 1rem;color:#4b2a5a;">سفارش‌های من</h2>
          <?php if (function_exists('wc_get_orders')) : 
            $order_ids = wc_get_orders([
              'customer_id' => $user_id,
              'status'      => ['processing','completed'],
              'limit'       => 10,
              'return'      => 'ids',
            ]);
          ?>
            <?php if (!empty($order_ids)) : ?>
              <ul style="list-style:none;margin:0;padding:0;display:grid;gap:.5rem;">
                <?php foreach ($order_ids as $oid): $order = wc_get_order($oid); if (!$order) continue; foreach ($order->get_items() as $item): $product = $item->get_product(); if (!$product) continue; $thumb = get_the_post_thumbnail_url($product->get_id(),'thumbnail'); ?>
                  <li style="display:flex;align-items:center;gap:.5rem;">
                    <?php if ($thumb): ?><img src="<?php echo esc_url($thumb); ?>" alt="" width="40" height="40" style="border-radius:.5rem;object-fit:cover;"><?php endif; ?>
                    <a href="<?php echo esc_url(get_permalink($product->get_id())); ?>" style="color:#4b2a5a;text-decoration:none;">&lrm;<?php echo esc_html($product->get_name()); ?>&lrm;</a>
                    <span style="margin-inline-start:auto;color:#82638a;"><?php echo esc_html(wc_price($item->get_total())); ?></span>
                  </li>
                <?php endforeach; endforeach; ?>
              </ul>
            <?php else: ?>
              <p style="color:#82638a;">سفارشی یافت نشد.</p>
            <?php endif; ?>
          <?php else: ?>
            <p style="color:#82638a;">ووکامرس فعال نیست.</p>
          <?php endif; ?>
        </div>

        <div style="text-align:left;">
          <a href="<?php echo esc_url(wp_logout_url(home_url('/'))); ?>" class="gn-btn" style="display:inline-block;background:linear-gradient(90deg, <?php echo esc_attr($lavender);?>, <?php echo esc_attr($lavenderSoft);?>);color:#2C2C2C;font-weight:600;border:none;border-radius:9999px;padding:.5rem 1rem;text-decoration:none;">خروج</a>
        </div>
      </div>
    <?php endif; ?>
  </section>
  <style>
    /* Minor helpers to fit brand */
    .gn-container { direction: rtl; }
  </style>
</main>
<?php if ( $gn_is_guest_page ) : ?>
  <?php wp_footer(); ?>
</body>
</html>
<?php else : get_footer(); endif;


