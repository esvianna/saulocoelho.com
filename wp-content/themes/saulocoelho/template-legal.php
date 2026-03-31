<?php
/**
 * Template Name: Legal Page (Privacy/Terms)
 * Description: A focused, high-readability layout for legal documents.
 */

get_header();
?>

<main class="bg-background-dark min-h-screen text-white pb-32">
    <!-- Clean Legal Header -->
    <div class="relative pt-32 pb-20 overflow-hidden border-b border-white/5 bg-black/20">
        <div class="max-w-4xl mx-auto px-6 relative z-10 text-center">
            <span class="text-primary/60 font-black tracking-[0.5em] uppercase text-[9px] mb-4 block">Documento Oficial</span>
            <?php the_title( '<h1 class="text-3xl md:text-5xl font-black leading-tight tracking-tight text-white uppercase">', '</h1>' ); ?>
            <div class="flex items-center justify-center gap-2 mt-6 text-slate-500 text-[10px] uppercase tracking-widest">
                <span class="material-symbols-outlined text-sm">event_note</span>
                Última atualização: <?php the_modified_date(); ?>
            </div>
        </div>
        
        <!-- Minimal Glow -->
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[300px] bg-primary/2 blur-[120px] rounded-full pointer-events-none"></div>
    </div>

    <!-- Content Area -->
    <div class="mx-auto w-full px-6 py-20">
        <?php
        while ( have_posts() ) :
            the_post();
            ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class('max-w-3xl mx-auto'); ?>>
                <div class="prose-premium opacity-90">
                    <?php the_content(); ?>
                </div>
                
                <!-- Print Button / Utility -->
                <div class="mt-20 pt-10 border-t border-white/5 flex justify-center">
                    <button onclick="window.print()" class="flex items-center gap-2 text-slate-500 hover:text-white transition-colors text-[10px] font-black uppercase tracking-widest">
                        <span class="material-symbols-outlined text-base">print</span>
                        Imprimir Documento
                    </button>
                </div>
            </article>
        <?php
        endwhile;
        ?>
    </div>
</main>

<?php
get_footer();
