<?php
/**
 * Template Name: Catalog Page
 */
get_header(); ?>

<?php
$post_id = get_the_ID();
$prog_title_1 = get_post_meta($post_id, 'programs_title_1', true) ?: 'Catálogo de';
$prog_title_2 = get_post_meta($post_id, 'programs_title_2', true) ?: 'Programas e Mentoria';
$prog_desc = get_post_meta($post_id, 'programs_description', true) ?: 'Metodologias exclusivas desenhadas para profissionais que buscam excelência operacional e liderança estratégica.';
?>

<main class="bg-background-dark-alt text-white min-h-screen">
    <div class="max-w-7xl mx-auto px-6 py-24 lg:px-10 mt-10">
        <!-- Page Header -->
        <div class="mb-16 flex flex-col gap-6 max-w-3xl">
            <div class="flex items-center gap-2 text-primary font-bold text-xs tracking-[0.3em] uppercase">
                <span class="h-px w-8 bg-primary"></span>
                Nossas Formações
            </div>
            <h1 class="text-4xl md:text-6xl font-black leading-[1.1] tracking-tight text-white uppercase">
                <?php echo esc_html($prog_title_1); ?> <span class="text-primary"><?php echo esc_html($prog_title_2); ?></span>
            </h1>
            <p class="text-lg text-slate-400 font-light leading-relaxed">
                <?php echo wp_kses_post($prog_desc); ?>
            </p>
        </div>

        <!-- Program Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
            <?php 
            for ($i = 1; $i <= 6; $i++) : 
                $title = get_post_meta($post_id, "prog_card_{$i}_title", true);
                if (!$title && $i > 3) continue;
                
                $img = get_post_meta($post_id, "prog_card_{$i}_img", true);
                $icon = get_post_meta($post_id, "prog_card_{$i}_icon", true) ?: 'school';
                $desc = get_post_meta($post_id, "prog_card_{$i}_desc", true) ?: 'Descrição do programa de mentoria e alta performance.';
                $tag1 = get_post_meta($post_id, "prog_card_{$i}_tag1", true);
                $tag2 = get_post_meta($post_id, "prog_card_{$i}_tag2", true);
                $link = get_post_meta($post_id, "prog_card_{$i}_link", true) ?: '#';
                $title = $title ?: "Programa $i";
            ?>
            <div class="group flex flex-col bg-slate-900/40 rounded-3xl border border-white/5 overflow-hidden hover:bg-slate-900/60 transition-all hover:border-primary/50 shadow-2xl">
                <div class="aspect-video relative overflow-hidden bg-slate-800">
                    <?php if ($img) : ?>
                        <img src="<?php echo esc_url($img); ?>" alt="<?php echo esc_attr($title); ?>" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                    <?php else : ?>
                        <div class="w-full h-full flex items-center justify-center opacity-20">
                            <span class="material-symbols-outlined text-6xl"><?php echo esc_html($icon); ?></span>
                        </div>
                    <?php endif; ?>
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 to-transparent"></div>
                </div>
                
                <div class="p-8 flex flex-col flex-1">
                    <h3 class="text-2xl font-bold text-white mb-4 group-hover:text-primary transition-colors"><?php echo esc_html($title); ?></h3>
                    <p class="text-sm text-slate-400 mb-6 font-light leading-relaxed line-clamp-2"><?php echo esc_html($desc); ?></p>
                    
                    <div class="mt-auto pt-6 border-t border-white/5">
                        <a href="<?php echo esc_url($link); ?>" class="inline-flex items-center gap-2 text-primary text-xs font-black uppercase tracking-widest hover:gap-3 transition-all">
                            Saiba Mais <span class="material-symbols-outlined text-sm">arrow_forward</span>
                        </a>
                    </div>
                </div>
            </div>
            <?php endfor; ?>
        </div>
    </div>
</main>

<?php get_footer(); ?>
