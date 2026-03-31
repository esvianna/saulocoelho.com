<?php
/**
 * The template for displaying archive pages
 */

get_header(); ?>

<main class="bg-background-dark text-white">
    <!-- Archive Hero -->
    <section class="py-24 md:py-32 border-b border-white/5 bg-background-dark-alt relative overflow-hidden">
        <div class="absolute inset-0 bg-primary/5 blur-[120px] rounded-full -top-1/2 -left-1/4 pointer-events-none"></div>
        <div class="max-w-7xl mx-auto px-6 relative z-10">
            <div class="flex items-center gap-4 text-[10px] font-black uppercase tracking-[0.4em] text-primary mb-6">
                <span class="material-symbols-outlined text-sm">folder_open</span>
                <?php esc_html_e( 'Categoria / Arquivo', 'saulocoelho' ); ?>
            </div>
            
            <?php the_archive_title( '<h1 class="text-4xl md:text-7xl font-black tracking-tighter text-white uppercase leading-none">', '</h1>' ); ?>
            
            <div class="h-1 w-24 bg-primary my-8"></div>
            
            <?php the_archive_description( '<div class="text-slate-400 text-lg md:text-xl max-w-2xl font-light leading-relaxed">', '</div>' ); ?>
        </div>
    </section>

    <!-- Post Feed -->
    <section class="py-24">
        <div class="max-w-7xl mx-auto px-6">
            <?php if ( have_posts() ) : ?>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-12 lg:gap-16">
                    <?php
                    while ( have_posts() ) : the_post();
                        get_template_part('template-parts/content-post-item');
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
                    <span class="material-symbols-outlined text-7xl text-white/5 mb-8">search_off</span>
                    <p class="text-slate-500 font-light italic text-xl"><?php esc_html_e( 'Nenhum insight encontrado nesta categoria.', 'saulocoelho' ); ?></p>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php get_footer(); ?>
