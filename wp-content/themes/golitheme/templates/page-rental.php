<?php
/**
 * Template Name: درخواست اجاره (Rental Request)
 * Description: فرم درخواست اجاره برای کالکشن‌های ویژه (MVP)
 *
 * @package GoliNaab
 */

get_header();
?>

<main id="main" class="gn-container py-12" role="main">
  <article class="gn-card max-w-2xl mx-auto">
    <header class="mb-6 text-center">
      <h1 class="text-2xl font-bold">درخواست اجاره</h1>
      <p class="text-gray-600 mt-2">لطفاً اطلاعات را با دقت وارد کنید. ارسال فرم به منزله پذیرش شرایط و ودیعه است.</p>
    </header>

    <form id="gn-rental-form" class="space-y-4" novalidate>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <label class="block">
          <span class="block text-sm text-gray-700 mb-1">تاریخ شروع</span>
          <input type="date" name="start_date" required class="w-full border-2 rounded-xl px-3 py-2">
        </label>
        <label class="block">
          <span class="block text-sm text-gray-700 mb-1">تاریخ پایان</span>
          <input type="date" name="end_date" required class="w-full border-2 rounded-xl px-3 py-2">
        </label>
      </div>

      <label class="block">
        <span class="block text-sm text-gray-700 mb-1">شماره تماس</span>
        <input type="tel" name="phone" required inputmode="tel" class="w-full border-2 rounded-xl px-3 py-2" placeholder="0912xxxxxxx">
      </label>

      <label class="block">
        <span class="block text-sm text-gray-700 mb-1">توضیحات</span>
        <textarea name="notes" rows="4" class="w-full border-2 rounded-xl px-3 py-2" placeholder="توضیحات تکمیلی..."></textarea>
      </label>

      <div class="flex items-start gap-2">
        <input id="gn_deposit_ack" type="checkbox" name="deposit_ack" value="1" required class="mt-1">
        <label for="gn_deposit_ack" class="text-sm text-gray-800">
          می‌پذیرم که مبلغ ودیعه برابر با ۲۰٪ ارزش مورد توافق است و شرایط استفاده را مطالعه کرده‌ام.
          <a class="text-lavender-600 hover:text-lavender-700 underline" href="<?php echo esc_url( home_url('/terms') ); ?>" target="_blank" rel="noopener">مطالعه شرایط</a>
        </label>
      </div>

      <div class="flex items-center justify-between pt-2">
        <div class="text-sm text-gray-600" id="gn-rental-feedback" role="status" aria-live="polite"></div>
        <button type="submit" class="gn-btn gn-btn-primary">ارسال درخواست</button>
      </div>

      <input type="hidden" name="action" value="gn_rental_request">
      <input type="hidden" name="nonce" value="<?php echo esc_attr( wp_create_nonce('gn_rental') ); ?>">
    </form>
  </article>
</main>

<?php
get_footer();
?>
