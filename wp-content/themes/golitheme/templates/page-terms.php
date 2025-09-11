<?php
/**
 * Template Name: شرایط و قوانین (Terms)
 * Description: صفحه شرایط استفاده و ودیعه
 *
 * @package GoliNaab
 */

get_header();
?>

<main id="main" class="gn-container py-12" role="main">
  <article class="gn-card max-w-3xl mx-auto leading-8">
    <header class="mb-8 text-center">
      <h1 class="text-2xl font-bold">شرایط استفاده و ودیعه</h1>
      <p class="text-gray-600 mt-2">مطالعه این صفحه به‌منزله آگاهی و پذیرش مفاد آن است.</p>
    </header>

    <section class="mb-6">
      <h2 class="text-xl font-bold mb-2">۱) کلیات</h2>
      <p class="text-gray-800">
        استفاده از خدمات وب‌سایت «گلی‌ناب» به معنای پذیرش تمامی قوانین و سیاست‌های این صفحه است. ممکن است این شرایط در بازه‌های زمانی به‌روزرسانی شود.
      </p>
    </section>

    <section class="mb-6">
      <h2 class="text-xl font-bold mb-2">۲) حریم خصوصی</h2>
      <p class="text-gray-800">
        اطلاعات شخصی کاربران صرفاً برای ارائه خدمات بهتر استفاده می‌شود و نزد ما محرمانه باقی می‌ماند.
      </p>
    </section>

    <section class="mb-6">
      <h2 class="text-xl font-bold mb-2">۳) ودیعه و اجاره</h2>
      <ul class="list-disc pr-5 text-gray-800 space-y-2">
        <li>میزان ودیعه در حال حاضر معادل ۲۰٪ ارزش مورد توافق است.</li>
        <li>بازگشت ودیعه پس از تحویل سالم آیتم انجام می‌شود.</li>
        <li>هرگونه خسارت یا تأخیر می‌تواند از ودیعه کسر گردد.</li>
      </ul>
    </section>

    <section class="mb-6">
      <h2 class="text-xl font-bold mb-2">۴) مسئولیت‌ها</h2>
      <p class="text-gray-800">
        کاربر متعهد است از آیتم اجاره‌ای به‌صورت مسئولانه استفاده کند و آن را در تاریخ مقرر و وضعیت مناسب بازگرداند.
      </p>
    </section>

    <footer class="mt-8 text-sm text-gray-600">
      <p>آخرین به‌روزرسانی: <?php echo esc_html( date_i18n( get_option('date_format') ) ); ?></p>
    </footer>
  </article>
</main>

<?php
get_footer();
?>
