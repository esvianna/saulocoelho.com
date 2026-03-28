<?php
/**
 * WooCommerce basic layout wrapper
 */

if ( is_singular( 'product' ) ) {
    // If it's a single product, load our custom template explicitly.
    // WooCommerce's hierarchy behaves weirdly if woocommerce.php exists.
    include get_theme_file_path( 'woocommerce/single-product.php' );
    return;
}

get_header(); ?>

<main class="mx-auto w-full max-w-7xl flex-1 px-6 py-24 lg:px-10 mt-20 bg-background-dark-alt text-white">
    <div class="glass-card p-8 md:p-12 rounded-2xl border border-white/5 bg-background-dark/30 backdrop-blur-md">
        <?php woocommerce_content(); ?>
    </div>
</main>

<?php get_footer(); ?>
