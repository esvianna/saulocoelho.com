<?php
/**
 * Template Name: Loja (Product Catalog)
 */

get_header();
?>

<main class="flex-grow">
    <?php
    get_template_part( 'template-parts/content', 'store' );
    ?>
</main>

<?php
get_footer();
