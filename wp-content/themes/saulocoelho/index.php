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

<main class="bg-background-dark text-white">
    <!-- Blog Hero -->
    <section class="py-24 md:py-32 border-b border-white/5 bg-background-dark-alt relative overflow-hidden">
        <div class="absolute inset-0 bg-primary/5 blur-[120px] rounded-full -top-1/2 -left-1/4 pointer-events-none"></div>
        <div class="max-w-7xl mx-auto px-6 relative z-10">
            <?php 
            $blog_title = get_theme_mod('blog_archive_title', 'Insights <span class="text-primary">&</span> Estratégia');
            $blog_desc = get_theme_mod('blog_archive_description', 'Perspectivas exclusivas sobre liderança, alta performance e a construção de organizações antifrágeis por Saulo Coelho.');
            ?>
            <h1 class="text-4xl md:text-7xl font-black tracking-tighter text-white uppercase leading-none"><?php echo wp_kses_post($blog_title); ?></h1>
            <div class="h-1 w-24 bg-primary my-8"></div>
            <p class="text-slate-400 text-lg md:text-xl max-w-2xl font-light leading-relaxed"><?php echo esc_html($blog_desc); ?></p>
        </div>
    </section>

    <!-- Post Feed -->
    <section class="py-24">
        <div class="max-w-7xl mx-auto px-6">
            <?php if ( have_posts() ) : ?>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-12 lg:gap-16">
                    <?php
                    $count = 0;
                    while ( have_posts() ) : the_post();
                        $count++;
                        // Destaque apenas na primeira página para o primeiro post
                        if ( $count === 1 && !is_paged() ) {
                            get_template_part('template-parts/content-post-item', null, array('is_featured' => true));
                        } else {
                            get_template_part('template-parts/content-post-item');
                        }
                    endwhile;
                    ?>
                </div>

                <!-- Pagination -->
                <div class="mt-32 flex justify-center">
                    <?php
                    the_posts_pagination( array(
                        'mid_size'  => 1,
                        'prev_text' => '<span class="material-symbols-outlined text-sm">west</span>',
                        'next_text' => '<span class="material-symbols-outlined text-sm">east</span>',
                    ) );
                    ?>
                </div>

            <?php else : ?>
                <div class="text-center py-32 glass-card rounded-[3rem] border border-white/5">
                    <span class="material-symbols-outlined text-7xl text-white/5 mb-8">article</span>
                    <p class="text-slate-500 font-light italic text-xl"><?php esc_html_e( 'Novos insights estão sendo preparados. Volte em breve.', 'saulocoelho' ); ?></p>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php
get_footer();
