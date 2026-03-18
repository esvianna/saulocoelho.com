<?php
/**
 * Template Name: Detalhes do Curso (Sales Page)
 */

get_header();
?>

<main class="flex-grow bg-background-dark-alt text-white">
    <?php
    get_template_part( 'template-parts/content', 'course-detail' );
    ?>
</main>

<?php
get_footer();
