<?php
$post_id = get_the_ID();
$features_title = get_post_meta($post_id, 'features_title', true) ?: 'Autoridade e Experiência';
$features_description = get_post_meta($post_id, 'features_description', true) ?: 'Impactando resultados através de metodologias testadas e aprovadas por líderes de grandes corporações.';
$features_image = get_post_meta($post_id, 'features_image', true);

$features = get_post_meta($post_id, 'home_features', true);

// Fallback manual caso ainda não existam dados no repetidor
if (empty($features)) {
    $features = [[
        'icon' => 'present_to_all',
        'title' => 'Palestras',
        'desc' => 'Conteúdo disruptivo, inspiração e estratégia prática para grandes convenções e públicos corporativos.'
    ]];
}
?>
<!-- Features/Services Section -->
<section class="py-32 relative">
    <div class="max-w-7xl mx-auto px-6">
        <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center mb-20">
            <div class="space-y-4">
                <h2 class="text-4xl md:text-5xl font-black tracking-tight text-white uppercase"><?php echo esc_html($features_title); ?></h2>
                <div class="h-1 w-20 bg-primary"></div>
                <p class="text-slate-400 text-lg max-w-2xl font-light"><?php echo esc_html($features_description); ?></p>
            </div>
            <?php if ($features_image) : ?>
                <div class="hidden lg:block relative group">
                    <div class="absolute -inset-1 bg-gradient-to-r from-primary/20 to-blue-600/20 rounded-3xl blur opacity-25 group-hover:opacity-50 transition duration-1000"></div>
                    <div class="relative rounded-3xl overflow-hidden border border-white/10 shadow-2xl skew-y-1 group-hover:skew-y-0 transition-transform duration-700">
                        <img src="<?php echo esc_url($features_image); ?>" alt="<?php echo esc_attr($features_title); ?>" class="w-full h-[300px] object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <?php if (!empty($features) && is_array($features)) : ?>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <?php foreach ($features as $feature) : 
                    $icon = !empty($feature['icon']) ? $feature['icon'] : 'present_to_all';
                    $title = !empty($feature['title']) ? $feature['title'] : '';
                    $desc = !empty($feature['desc']) ? $feature['desc'] : '';
                    ?>
                    <div class="glass-card p-10 rounded-xl space-y-6 hover:border-primary/50 transition-colors group">
                        <div class="w-16 h-16 rounded-lg bg-primary/10 flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-white transition-all duration-500">
                            <span class="material-symbols-outlined text-3xl"><?php echo esc_html($icon); ?></span>
                        </div>
                        <div class="space-y-3">
                            <h3 class="text-xl font-bold text-white uppercase tracking-wider"><?php echo esc_html($title); ?></h3>
                            <p class="text-slate-400 text-sm leading-relaxed"><?php echo nl2br(esc_html($desc)); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
