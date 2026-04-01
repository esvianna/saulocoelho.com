<?php
/**
 * Template part for displaying a press/media post item
 */

$post_id = get_the_ID();
$external_url = get_post_meta($post_id, 'press_external_url', true);
$permalink = !empty($external_url) ? $external_url : get_permalink();
$target = !empty($external_url) ? '_blank' : '_self';

$categories = get_the_category();
$source = !empty($categories) ? $categories[0]->name : 'Imprensa';
$date = get_the_date('d M, Y');
?>

<div class="group relative flex flex-col bg-background-dark-alt/40 border border-white/5 rounded-3xl overflow-hidden hover:border-primary/30 transition-all duration-500 hover:-translate-y-2 hover:shadow-[0_20px_50px_rgba(0,0,0,0.5)]">
    <!-- Image Wrapper -->
    <div class="aspect-[16/10] overflow-hidden relative">
        <?php if (has_post_thumbnail()) : ?>
            <?php the_post_thumbnail('medium_large', ['class' => 'w-full h-full object-cover transition-transform duration-700 group-hover:scale-110']); ?>
        <?php else : ?>
            <div class="w-full h-full bg-slate-900 flex items-center justify-center">
                <span class="material-symbols-outlined text-5xl text-white/5">newspaper</span>
            </div>
        <?php endif; ?>
        
        <!-- Overlay -->
        <div class="absolute inset-0 bg-gradient-to-t from-background-dark-alt via-transparent to-transparent opacity-60"></div>
        
        <!-- Category Badge -->
        <div class="absolute top-4 left-4">
            <span class="px-3 py-1 text-[10px] font-bold uppercase tracking-widest bg-primary/20 text-primary backdrop-blur-md rounded-full border border-primary/20">
                <?php echo esc_html($source); ?>
            </span>
        </div>
    </div>

    <!-- Content -->
    <div class="p-8 flex flex-col flex-grow">
        <time class="text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em] mb-3"><?php echo esc_html($date); ?></time>
        <h3 class="text-xl font-bold text-white mb-6 line-clamp-2 group-hover:text-primary transition-colors leading-tight">
            <?php the_title(); ?>
        </h3>
        
        <div class="mt-auto flex items-center justify-between">
            <a href="<?php echo esc_url($permalink); ?>" target="<?php echo esc_attr($target); ?>" class="inline-flex items-center gap-2 text-xs font-bold text-white uppercase tracking-widest group/link">
                <span>Ler Matéria</span>
                <span class="material-symbols-outlined text-sm transition-transform group-hover/link:translate-x-1">east</span>
            </a>
            
            <?php if (!empty($external_url)) : ?>
                <span class="material-symbols-outlined text-slate-700 text-sm" title="Link Externo">open_in_new</span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Hover Glow -->
    <div class="absolute inset-0 bg-primary/5 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none"></div>
</div>
