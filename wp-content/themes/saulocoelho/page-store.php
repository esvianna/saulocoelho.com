<?php
/**
 * Template Name: Loja (Product Catalog)
 */

get_header();
?>

<main class="flex-grow bg-background-dark-alt text-white">
    <?php
    get_template_part( 'template-parts/content', 'store' );
    ?>
</main>

<?php
get_footer();
