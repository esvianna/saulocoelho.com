<?php
/**
 * The template for displaying all pages
 */

get_header();
?>

<main class="mx-auto w-full max-w-7xl flex-1 px-6 py-12 lg:px-10">
    <?php
    while ( have_posts() ) :
        the_post();
        ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <header class="mb-12">
                <h1 class="text-4xl font-black leading-tight tracking-tight text-slate-900 dark:text-white md:text-5xl">
                    <?php the_title(); ?>
                </h1>
            </header>

            <div class="prose dark:prose-invert max-w-none text-slate-600 dark:text-slate-400">
                <?php the_content(); ?>
            </div>
        </article>
        <?php
    endwhile;
    ?>
</main>

<?php
get_footer();
