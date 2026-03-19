<?php
/**
 * Custom Single Product Template
 * Redesigned to follow the "Course Details" high-performance layout.
 */

if ( ! defined( 'ABSOLUTE_PATH' ) ) {
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
