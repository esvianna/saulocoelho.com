<?php
/**
 * Template Name: Programas e Treinamentos
 */

get_header();
?>

<main class="bg-background-dark-alt text-white min-h-screen">
    <div class="mx-auto w-full max-w-7xl px-6 py-24 lg:px-10">
        <?php
        get_template_part( 'template-parts/content', 'programs' );
        ?>
    </div>
</main>

<?php
get_footer();
