<?php
/**
 * The template for displaying all pages
 */

get_header();
?>

<main class="bg-background-dark min-h-screen text-white">
    <div class="mx-auto w-full max-w-7xl px-6 py-24 lg:px-10">
        <?php
        while ( have_posts() ) :
            the_post();
            ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <header class="mb-12">
                    <h1 class="text-4xl font-black leading-tight tracking-tight text-white md:text-6xl">
                        <?php the_title(); ?>
                    </h1>
                </header>

                <div class="prose prose-invert max-w-none text-slate-400">
                    <?php the_content(); ?>
                </div>
            </article>
        <?php
        endwhile;
        ?>
    </div>
</main>

<?php
get_footer();
