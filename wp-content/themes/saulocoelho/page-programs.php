<?php
/**
 * Template Name: Programas e Treinamentos
 */

get_header();
?>

<main class="mx-auto w-full max-w-7xl flex-1 px-6 py-12 lg:px-10 bg-background-dark-alt text-white">
    <?php
    get_template_part( 'template-parts/content', 'programs' );
    ?>
</main>

<?php
get_footer();
