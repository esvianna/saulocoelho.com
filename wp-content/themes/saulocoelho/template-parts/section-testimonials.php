<?php
/**
 * Template Part: Section Testimonials
 */

$pid = get_the_ID();
$is_home = is_front_page();

$manual_ids_str = $is_home ? get_post_meta($pid, 'home_testimonials_ids', true) : get_post_meta($pid, 'course_testimonials_ids', true);
$section_title = $is_home ? get_post_meta($pid, 'home_testimonials_title', true) : get_post_meta($pid, 'course_testimonials_title', true);
$section_title = $section_title ?: 'O que dizem nossos alunos';

$args = [
    'post_type' => 'testimonial',
    'post_status' => 'publish',
    'posts_per_page' => 6,
];

if (!empty($manual_ids_str)) {
    $args['post__in'] = array_map('trim', explode(',', $manual_ids_str));
    $args['orderby'] = 'post__in';
} elseif (!$is_home) {
    // If on product page and no manual IDs, try to find testimonials for this product
    $args['meta_query'] = [
        'relation' => 'OR',
        [
            'key' => 'testimonial_product_id',
            'value' => $pid,
            'compare' => '='
        ],
        [
            'key' => 'testimonial_product_id',
            'value' => '',
            'compare' => '='
        ]
    ];
}

$query = new WP_Query($args);

if ($query->have_posts()) :
?>
<section class="py-24 bg-background-dark overflow-hidden relative" id="depoimentos">
    <!-- Glow -->
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-full bg-primary/5 blur-[150px] rounded-full pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10">
        <div class="text-center mb-16">
            <span class="text-xs font-bold tracking-[0.3em] uppercase text-primary mb-4 block">Prova Social</span>
            <h2 class="text-3xl md:text-5xl font-black text-white leading-tight"><?php echo esc_html($section_title); ?></h2>
            <div class="h-1 w-20 bg-primary mx-auto mt-8"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php while ($query->have_posts()) : $query->the_post(); 
                $tid = get_the_ID();
                $role = get_post_meta($tid, 'testimonial_role', true) ?: 'Aluno';
                $rating = get_post_meta($tid, 'testimonial_rating', true) ?: 5;
                $video_url = get_post_meta($tid, 'testimonial_video_url', true);
                $has_video = !empty($video_url);
            ?>
            <div class="group bg-white/[0.03] border border-white/5 rounded-3xl p-8 flex flex-col transition-all duration-500 hover:border-primary/30 hover:bg-white/[0.05] hover:-translate-y-2 hover:shadow-[0_20px_50px_rgba(0,0,0,0.4)]">
                <!-- Stars -->
                <div class="flex gap-1 mb-6 text-primary">
                    <?php for ($i=1; $i<=5; $i++) : ?>
                        <span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' <?php echo ($i <= $rating ? '1' : '0'); ?>;"><?php echo ($i <= $rating ? 'star' : 'star_outline'); ?></span>
                    <?php endfor; ?>
                </div>

                <!-- Text -->
                <div class="prose prose-invert italic text-slate-300 font-light leading-relaxed mb-8 grow">
                    <?php the_content(); ?>
                </div>

                <!-- Author -->
                <div class="flex items-center justify-between border-t border-white/5 pt-6 mt-auto">
                    <div class="flex items-center gap-4">
                        <?php if (has_post_thumbnail()) : ?>
                            <?php the_post_thumbnail('thumbnail', ['class' => 'w-12 h-12 rounded-full object-cover border border-white/10 shadow-lg']); ?>
                        <?php else : ?>
                            <div class="w-12 h-12 rounded-full bg-slate-800 flex items-center justify-center border border-white/10 shadow-lg">
                                <span class="material-symbols-outlined text-slate-500">person</span>
                            </div>
                        <?php endif; ?>
                        
                        <div>
                            <h4 class="text-white font-bold text-sm"><?php the_title(); ?></h4>
                            <p class="text-slate-500 text-[10px] font-bold uppercase tracking-widest"><?php echo esc_html($role); ?></p>
                        </div>
                    </div>

                    <?php if ($has_video) : ?>
                        <button onclick="playTestimonialVideo('<?php echo esc_js($video_url); ?>')" class="w-10 h-10 rounded-full bg-primary/10 border border-primary/20 text-primary flex items-center justify-center hover:bg-primary hover:text-white transition-all shadow-lg group/video" title="Ver Depoimento em Vídeo">
                            <span class="material-symbols-outlined text-lg translate-x-0.5" style="font-variation-settings: 'FILL' 1;">play_arrow</span>
                        </button>
                    <?php endif; ?>
                </div>
            </div>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>
    </div>
</section>

<!-- Lightbox para Depoimentos (Reutilizando estrutura se existir ou criando uma dedicada) -->
<div id="testimonial-video-lightbox" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/90 backdrop-blur-md opacity-0 transition-opacity duration-300">
    <div class="relative w-full max-w-4xl aspect-video bg-black rounded-2xl shadow-2xl overflow-hidden scale-95 transition-transform duration-300 mx-4" id="testimonial-lightbox-content">
        <button onclick="closeTestimonialLightbox()" class="absolute top-4 right-4 z-50 text-white bg-black/50 hover:bg-primary rounded-full p-2 backdrop-blur-md transition-colors">
            <span class="material-symbols-outlined">close</span>
        </button>
        <div id="testimonial-player-container" class="w-full h-full"></div>
    </div>
</div>

<script>
function playTestimonialVideo(url) {
    const lightbox = document.getElementById('testimonial-video-lightbox');
    const content = document.getElementById('testimonial-lightbox-content');
    const container = document.getElementById('testimonial-player-container');
    
    let embedUrl = '';
    if (url.includes('youtube.com') || url.includes('youtu.be')) {
        const id = url.split('v=')[1] || url.split('/').pop();
        embedUrl = `https://www.youtube.com/embed/${id}?autoplay=1&rel=0`;
    } else if (url.includes('vimeo.com')) {
        const id = url.split('/').pop();
        embedUrl = `https://player.vimeo.com/video/${id}?autoplay=1`;
    } else {
        embedUrl = url;
    }

    container.innerHTML = `<iframe src="${embedUrl}" class="w-full h-full" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>`;
    
    lightbox.classList.remove('hidden');
    lightbox.classList.add('flex');
    setTimeout(() => {
        lightbox.classList.remove('opacity-0');
        content.classList.remove('scale-95');
    }, 10);
}

function closeTestimonialLightbox() {
    const lightbox = document.getElementById('testimonial-video-lightbox');
    const content = document.getElementById('testimonial-lightbox-content');
    const container = document.getElementById('testimonial-player-container');
    
    lightbox.classList.add('opacity-0');
    content.classList.add('scale-95');
    setTimeout(() => {
        lightbox.classList.remove('flex');
        lightbox.classList.add('hidden');
        container.innerHTML = '';
    }, 300);
}
</script>
<?php endif; ?>
