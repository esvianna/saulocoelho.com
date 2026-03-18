<?php
/**
 * The template for displaying all pages
 */

get_header();
?>

<main class="bg-background-dark-alt min-h-screen text-white pb-24">
    <!-- Header Section for Page Title -->
    <div class="relative pt-32 pb-16 overflow-hidden border-b border-white/5 bg-background-dark/30">
        <div class="max-w-7xl mx-auto px-6 lg:px-10 relative z-10">
            <span class="text-primary font-bold tracking-[0.3em] uppercase text-[10px] mb-4 block">Institucional</span>
            <?php the_title( '<h1 class="text-4xl md:text-6xl font-black leading-tight tracking-tight text-white uppercase">', '</h1>' ); ?>
        </div>
        <!-- Decorative Glow -->
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-primary/5 blur-[120px] rounded-full"></div>
    </div>

    <div class="mx-auto w-full max-w-7xl px-6 py-20 lg:px-10">
        <?php
        while ( have_posts() ) :
            the_post();
            ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <div class="prose prose-invert prose-slate max-w-none 
                    prose-headings:text-white prose-headings:font-black prose-headings:uppercase prose-headings:tracking-tight
                    prose-p:text-slate-400 prose-p:leading-relaxed prose-p:text-lg
                    prose-a:text-primary hover:prose-a:text-primary/80
                    prose-strong:text-white prose-strong:font-bold">
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
