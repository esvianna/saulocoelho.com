<?php
/**
 * Template part for displaying posts in a grid
 */

$is_featured = isset($args['is_featured']) && $args['is_featured'];
$classes = 'glass-card group overflow-hidden rounded-3xl transition-all duration-700 flex flex-col h-full';

if ($is_featured) {
    $classes .= ' md:grid md:grid-cols-2 md:col-span-3 items-center bg-primary/5 border-primary/20';
} else {
    $classes .= ' border border-white/5 hover:border-primary/30';
}
?>

<article id="post-<?php the_ID(); ?>" <?php post_class($classes); ?>>
    <div class="<?php echo $is_featured ? 'h-full' : 'aspect-[4/3]'; ?> overflow-hidden relative">
        <?php if ( has_post_thumbnail() ) : ?>
            <a href="<?php the_permalink(); ?>" class="absolute inset-0">
                <?php the_post_thumbnail('large', array('class' => 'w-full h-full object-cover group-hover:scale-110 transition-transform duration-1000 opacity-80 group-hover:opacity-100')); ?>
            </a>
        <?php else : ?>
            <a href="<?php the_permalink(); ?>" class="absolute inset-0 bg-white/5 flex items-center justify-center group overflow-hidden">
                <span class="material-symbols-outlined text-6xl text-white/10 group-hover:scale-120 transition-transform duration-500">article</span>
            </a>
        <?php endif; ?>
        <div class="absolute inset-0 bg-gradient-to-t from-background-dark/80 to-transparent"></div>
    </div>

    <div class="<?php echo $is_featured ? 'p-12 lg:p-20' : 'p-8 pb-12'; ?> flex-1 flex flex-col space-y-6 relative">
        <div class="flex items-center gap-4 text-[10px] font-black uppercase tracking-[0.3em] text-primary">
            <?php
            $categories = get_the_category();
            if ( ! empty( $categories ) ) : ?>
                <span class="bg-primary/10 px-3 py-1 rounded-full"><?php echo esc_html( $categories[0]->name ); ?></span>
            <?php endif; ?>
            <span class="w-1.5 h-1.5 rounded-full bg-white/20"></span>
            <span class="text-slate-500"><?php echo get_the_date(); ?></span>
        </div>

        <h3 class="<?php echo $is_featured ? 'text-3xl md:text-5xl' : 'text-2xl'; ?> font-black text-white group-hover:text-primary transition-colors leading-tight uppercase tracking-tighter">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h3>

        <div class="text-slate-400 <?php echo $is_featured ? 'text-lg max-w-xl' : 'text-sm'; ?> font-light leading-relaxed <?php echo $is_featured ? '' : 'line-clamp-3'; ?>">
            <?php echo wp_trim_words( get_the_excerpt(), $is_featured ? 40 : 20 ); ?>
        </div>

        <div class="pt-6 mt-auto">
            <a href="<?php the_permalink(); ?>" class="inline-flex items-center gap-3 text-[10px] font-black uppercase tracking-[0.3em] text-white/60 group-hover:text-primary group-hover:gap-5 transition-all duration-300">
                Ler Insight
                <span class="material-symbols-outlined text-sm">arrow_forward</span>
            </a>
        </div>
    </div>
</article>
