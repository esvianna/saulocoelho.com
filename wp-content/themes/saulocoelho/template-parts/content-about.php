<?php
$post_id = get_the_ID();
$about_eyebrow = get_post_meta($post_id, 'about_eyebrow', true) ?: 'Autoridade & Excelência';
$about_title_1 = get_post_meta($post_id, 'about_title_1', true) ?: 'Quem é';
$about_title_2 = get_post_meta($post_id, 'about_title_2', true) ?: 'Saulo Coelho?';
$about_desc = get_post_meta($post_id, 'about_description', true) ?: 'Com décadas de experiência, Saulo Coelho transformou o cenário profissional através de liderança estratégica e uma visão institucional sólida e inovadora.';

$stat_1_n = get_post_meta($post_id, 'about_stat_1_number', true) ?: '25+';
$stat_1_l = get_post_meta($post_id, 'about_stat_1_label', true) ?: 'Anos de Carreira';
$stat_2_n = get_post_meta($post_id, 'about_stat_2_number', true) ?: '500+';
$stat_2_l = get_post_meta($post_id, 'about_stat_2_label', true) ?: 'Projetos Liderados';
$about_image = get_post_meta($post_id, 'about_image', true);
?>

<!-- Hero Section -->
<section class="relative py-20 lg:py-32 overflow-hidden">
    <div class="max-w-7xl mx-auto px-6 lg:px-12 grid lg:grid-cols-2 gap-16 items-center">
        <div class="z-10">
            <span class="inline-block px-4 py-1.5 mb-6 text-xs font-bold tracking-widest uppercase text-primary bg-primary/10 rounded-full"><?php echo esc_html($about_eyebrow); ?></span>
            <h1 class="text-4xl md:text-6xl font-black leading-[1.1] text-white mb-8">
                <?php echo esc_html($about_title_1); ?><br/>
                <span class="text-primary"><?php echo esc_html($about_title_2); ?></span>
            </h1>
            <div class="prose prose-invert text-lg lg:text-xl text-slate-400 font-light leading-relaxed mb-10 max-w-xl">
                <?php echo wp_kses_post($about_desc); ?>
            </div>
            <div class="flex items-center gap-10">
                <div class="flex flex-col gap-1">
                    <span class="text-4xl lg:text-5xl font-black text-white"><?php echo esc_html($stat_1_n); ?></span>
                    <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400"><?php echo esc_html($stat_1_l); ?></span>
                </div>
                <div class="w-px h-12 bg-white/10"></div>
                <div class="flex flex-col gap-1">
                    <span class="text-4xl lg:text-5xl font-black text-white"><?php echo esc_html($stat_2_n); ?></span>
                    <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400"><?php echo esc_html($stat_2_l); ?></span>
                </div>
            </div>
        </div>
        <div class="relative">
            <div class="aspect-[4/5] rounded-3xl overflow-hidden shadow-2xl bg-slate-900/50 flex items-center justify-center border border-white/5 relative group">
                <?php if ($about_image) : ?>
                    <img src="<?php echo esc_url($about_image); ?>" alt="<?php the_title(); ?>" class="w-full h-full object-cover">
                <?php else : ?>
                    <span class="material-symbols-outlined text-9xl text-slate-800 group-hover:scale-110 transition-transform duration-500">person</span>
                <?php endif; ?>
                <div class="absolute inset-0 bg-gradient-to-t from-background-dark-alt/60 to-transparent"></div>
            </div>
        </div>
    </div>
</section>

<!-- Institutional History -->
<section class="py-24 bg-background-dark/30">
    <div class="max-w-4xl mx-auto px-6 lg:px-12">
        <div class="text-center mb-20">
            <h2 class="text-3xl lg:text-4xl font-bold mb-4">Trajetória Institucional</h2>
            <div class="w-20 h-1 bg-primary mx-auto"></div>
        </div>
        <div class="space-y-12 relative before:absolute before:inset-0 before:ml-5 before:-translate-x-px md:before:mx-auto md:before:translate-x-0 before:h-full before:w-0.5 before:bg-gradient-to-b before:from-transparent before:via-slate-800 before:to-transparent">
            <?php 
            for ($i = 1; $i <= 3; $i++) : 
                $year = get_post_meta($post_id, "about_milestone_{$i}_year", true);
                if (!$year) continue;
                $title = get_post_meta($post_id, "about_milestone_{$i}_title", true);
                $desc = get_post_meta($post_id, "about_milestone_{$i}_desc", true);
                $icons = ['history_edu', 'trending_up', 'verified'];
                $icon = $icons[$i-1];
            ?>
            <div class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group">
                <div class="flex items-center justify-center w-10 h-10 rounded-full border border-slate-800 bg-background-dark-alt text-primary font-bold shadow-sm shrink-0 md:order-1 md:group-odd:-translate-x-1/2 md:group-even:translate-x-1/2 z-10 transition-transform group-hover:scale-110">
                    <span class="material-symbols-outlined text-sm"><?php echo esc_html($icon); ?></span>
                </div>
                <div class="w-[calc(100%-4rem)] md:w-[calc(50%-2.5rem)] p-6 rounded-xl bg-background-dark/40 border border-white/5 shadow-xl hover:bg-background-dark/60 transition-colors">
                    <div class="flex items-center justify-between mb-2">
                        <time class="font-bold text-primary"><?php echo esc_html($year); ?></time>
                    </div>
                    <h4 class="text-lg font-bold mb-2 text-white"><?php echo esc_html($title); ?></h4>
                    <p class="text-slate-400 text-sm leading-relaxed"><?php echo esc_html($desc); ?></p>
                </div>
            </div>
            <?php endfor; ?>
        </div>
    </div>
</section>

<!-- Values/Authority Grid -->
<section class="py-24">
    <div class="max-w-7xl mx-auto px-6 lg:px-12">
        <div class="grid md:grid-cols-3 gap-12">
            <?php 
            for ($i = 1; $i <= 3; $i++) : 
                $icon = get_post_meta($post_id, "about_value_{$i}_icon", true);
                if (!$icon) continue;
                $title = get_post_meta($post_id, "about_value_{$i}_title", true);
                $desc = get_post_meta($post_id, "about_value_{$i}_desc", true);
            ?>
            <div class="space-y-4 p-8 rounded-2xl bg-background-dark/20 border border-white/[0.03] hover:border-primary/20 transition-all group">
                <span class="material-symbols-outlined text-primary text-4xl group-hover:scale-110 transition-transform duration-300"><?php echo esc_html($icon); ?></span>
                <h3 class="text-xl font-bold text-white"><?php echo esc_html($title); ?></h3>
                <p class="text-slate-400 leading-relaxed"><?php echo esc_html($desc); ?></p>
            </div>
            <?php endfor; ?>
        </div>
    </div>
</section>
