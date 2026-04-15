<?php
/**
 * Single product — layout customizado (detalhe do curso).
 *
 * Substitui o fluxo padrão (hooks shop + content-single-product) por template-part próprio.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

get_header(); ?>

<main class="flex-grow bg-background-dark-alt text-white">
    <?php
    // Use the custom course detail layout for single products
    get_template_part( 'template-parts/content', 'course-detail' );
    ?>
</main>

<?php get_footer(); ?>
