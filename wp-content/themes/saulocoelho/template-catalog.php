<?php
/**
 * Template Name: Catalog Page
 */
get_header(); ?>

<main class="bg-background-dark-alt text-white min-h-screen">
    <div class="max-w-7xl mx-auto px-6 py-12 lg:px-10 mt-20">
        <div class="mb-12 flex flex-col gap-4">
            <div class="flex items-center gap-2 text-primary font-semibold text-sm tracking-wider uppercase">
                <span class="h-px w-8 bg-primary"></span>
                Catálogo de Formação
            </div>
            <h2 class="max-w-2xl text-4xl font-black leading-tight tracking-tight text-white md:text-5xl">
                Programas de <span class="text-primary">Treinamento</span> e Mentoria
            </h2>
            <p class="max-w-xl text-lg text-slate-400">
                Metodologias exclusivas desenhadas para profissionais que buscam excelência operacional e liderança estratégica.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
        <?php
        for ($i = 1; $i <= 6; $i++) : 
            $title = get_post_meta(get_the_ID(), "prog_card_{$i}_title", true);
            if (!$title && $i > 4) continue;

            $icon = get_post_meta(get_the_ID(), "prog_card_{$i}_icon", true) ?: 'school';
            $desc = get_post_meta(get_the_ID(), "prog_card_{$i}_desc", true) ?: 'Descrição do programa de treinamento.';
            $link = get_post_meta(get_the_ID(), "prog_card_{$i}_link", true) ?: '#';
            $title = $title ?: "Programa $i";
        ?>
            <div class="group relative flex flex-col bg-slate-800/40 rounded-2xl border border-white/5 p-8 hover:bg-slate-800/60 transition-all hover:border-primary/50">
                <div class="size-16 rounded-xl bg-primary/10 flex items-center justify-center mb-8 group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-primary text-3xl"><?php echo esc_html($icon); ?></span>
                </div>
                <div class="flex flex-1 flex-col">
                    <h3 class="text-xl font-bold text-white mb-4"><?php echo esc_html($title); ?></h3>
                    <p class="text-sm text-slate-400 mb-8 font-light leading-relaxed">
                        <?php echo esc_html($desc); ?>
                    </p>
                    <div class="mt-auto pt-6 border-t border-white/5">
                        <a href="<?php echo esc_url($link); ?>" class="inline-flex items-center gap-2 text-primary text-xs font-bold uppercase tracking-widest hover:gap-3 transition-all">
                            Saiba Mais <span class="material-symbols-outlined text-sm">arrow_forward</span>
                        </a>
                    </div>
                </div>
            </div>
        <?php endfor; ?>
    </div>
</main>

<?php get_footer(); ?>
