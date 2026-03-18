<?php
/**
 * WooCommerce basic layout wrapper
 */
get_header(); ?>

<main class="mx-auto w-full max-w-7xl flex-1 px-6 py-24 lg:px-10 mt-20">
    <div class="glass-card p-8 md:p-12 rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/50">
        <?php woocommerce_content(); ?>
    </div>
</main>

<?php get_footer(); ?>
