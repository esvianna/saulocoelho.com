<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 */

get_header(); ?>

<main class="bg-background-dark-alt py-32">
    <div class="max-w-7xl mx-auto px-6">
        <?php
        if ( have_posts() ) :
            while ( have_posts() ) :
                the_post();
                ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <header class="entry-header mb-8">
                        <?php the_title( '<h1 class="text-4xl font-bold text-white uppercase tracking-tight">', '</h1>' ); ?>
                    </header>

                    <div class="entry-content text-slate-300 prose prose-invert max-w-none">
                        <?php
                        the_content();
                        ?>
                    </div>
                </article>
                <?php
            endwhile;
        else :
            ?>
            <p><?php esc_html_e( 'Sorry, no posts matched your criteria.', 'saulocoelho' ); ?></p>
            <?php
        endif;
        ?>
    </div>
</main>

<?php
get_footer();
