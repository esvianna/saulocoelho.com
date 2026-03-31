<?php
/**
 * The template for displaying all single posts
 */

get_header();
?>

<main id="primary" class="site-main bg-background-dark min-h-screen">

    <?php
    while ( have_posts() ) :
        the_post();
        ?>

        <!-- Post Hero -->
        <header class="relative pt-32 pb-16 md:pt-48 md:pb-24 overflow-hidden bg-background-dark-alt border-b border-white/5">
            <div class="absolute inset-0 z-0">
                <?php if ( has_post_thumbnail() ) : ?>
                    <?php the_post_thumbnail('full', array('class' => 'w-full h-full object-cover opacity-20 blur-sm scale-105')); ?>
                <?php endif; ?>
                <div class="absolute inset-0 bg-gradient-to-t from-background-dark via-background-dark/80 to-transparent"></div>
            </div>

            <div class="max-w-4xl mx-auto px-6 relative z-10 text-center space-y-8">
                <div class="flex items-center justify-center gap-4 text-[10px] font-black uppercase tracking-[0.4em] text-primary">
                    <?php
                    $categories = get_the_category();
                    if ( ! empty( $categories ) ) : ?>
                        <span class="bg-primary/10 px-4 py-1.5 rounded-full border border-primary/20"><?php echo esc_html( $categories[0]->name ); ?></span>
                    <?php endif; ?>
                    <span class="w-1.5 h-1.5 rounded-full bg-white/20"></span>
                    <span class="text-slate-500"><?php echo get_the_date(); ?></span>
                </div>

                <h1 class="text-4xl md:text-7xl font-black text-white uppercase tracking-tighter leading-[0.95]">
                    <?php the_title(); ?>
                </h1>

                <div class="flex items-center justify-center gap-3 pt-4">
                    <div class="w-10 h-10 rounded-full bg-primary/20 flex items-center justify-center text-primary border border-primary/20">
                        <span class="material-symbols-outlined text-xl">person</span>
                    </div>
                    <span class="text-slate-400 text-xs font-bold uppercase tracking-widest"><?php the_author(); ?></span>
                </div>
            </div>
        </header>

        <!-- Post Content -->
        <article id="post-<?php the_ID(); ?>" <?php post_class('pt-12 pb-24'); ?>>
            <div class="max-w-3xl mx-auto px-6 prose-premium">
                <?php
                the_content(
                    sprintf(
                        wp_kses(
                            /* translators: %s: Name of current post. Only visible to screen readers */
                            __( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'saulocoelho' ),
                            array(
                                'span' => array(
                                    'class' => array(),
                                ),
                            )
                        ),
                        get_the_title()
                    )
                );

                wp_link_pages(
                    array(
                        'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'saulocoelho' ),
                        'after'  => '</div>',
                    )
                );
                ?>
            </div>
        </article>

        <!-- Post Footer / CTA -->
        <?php
        $cta_subtitle = get_theme_mod('blog_cta_subtitle', 'Próximo Passo');
        $cta_title = get_theme_mod('blog_cta_title', 'Gostou deste Insight?');
        $cta_description = get_theme_mod('blog_cta_description', 'Leve este conhecimento para a prática com os nossos programas de mentoria e treinamento.');
        $cta_btn1_text = get_theme_mod('blog_cta_btn1_text', 'Ver Programas');
        $cta_btn1_url = get_theme_mod('blog_cta_btn1_url', '/programas');
        $cta_btn2_text = get_theme_mod('blog_cta_btn2_text', 'Falar com Especialista');
        $cta_btn2_url = get_theme_mod('blog_cta_btn2_url', '/contato');
        ?>
        <footer class="py-24 border-t border-white/5 bg-background-dark-alt">
            <div class="max-w-4xl mx-auto px-6 text-center space-y-12">
                <div class="space-y-4">
                    <h4 class="text-primary text-[10px] font-black uppercase tracking-[0.4em]"><?php echo esc_html($cta_subtitle); ?></h4>
                    <h2 class="text-3xl md:text-5xl font-black text-white uppercase tracking-tighter"><?php echo esc_html($cta_title); ?></h2>
                    <p class="text-slate-400 text-lg font-light max-w-xl mx-auto"><?php echo esc_html($cta_description); ?></p>
                </div>

                <div class="flex flex-col md:flex-row gap-6 justify-center">
                    <a href="<?php echo esc_url($cta_btn1_url); ?>" class="px-10 py-5 bg-primary text-white rounded-xl font-bold uppercase text-[10px] tracking-[0.2em] hover:bg-primary/90 hover:-translate-y-1 transition-all shadow-xl shadow-primary/20">
                        <?php echo esc_html($cta_btn1_text); ?>
                    </a>
                    <a href="<?php echo esc_url($cta_btn2_url); ?>" class="px-10 py-5 bg-white/5 text-white rounded-xl font-bold uppercase text-[10px] tracking-[0.2em] hover:bg-white/10 hover:-translate-y-1 transition-all border border-white/10">
                        <?php echo esc_html($cta_btn2_text); ?>
                    </a>
                </div>
            </div>
        </footer>

        <?php
    endwhile; // End of the loop.
    ?>

</main><!-- #main -->

<?php
get_footer();
