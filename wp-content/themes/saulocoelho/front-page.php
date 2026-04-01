<?php
/**
 * The front page template file
 */

get_header(); ?>

<main class="bg-background-dark-alt text-white">
    <?php
    get_template_part( 'template-parts/content', 'hero' );
    get_template_part( 'template-parts/content', 'trusted' );
    get_template_part( 'template-parts/content', 'features' );
    get_template_part( 'template-parts/section', 'testimonials' );
    ?>
</main>

<?php
get_footer();
