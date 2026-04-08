<?php
$post_id = get_the_ID();
// Hero
$prog_eyebrow = get_post_meta($post_id, 'programs_eyebrow', true) ?: 'Ecossistema de Formação';
$prog_title_1 = get_post_meta($post_id, 'programs_title_1', true) ?: 'Catálogo de';
$prog_title_2 = get_post_meta($post_id, 'programs_title_2', true) ?: 'Programas';
$prog_desc = get_post_meta($post_id, 'programs_description', true) ?: 'Acreditamos que o conhecimento é o maior acelerador de resultados. Nossos programas são desenhados com base em mais de duas décadas de experiência e aplicação prática no mundo corporativo.';
$prog_btn = get_post_meta($post_id, 'programs_hero_btn_text', true) ?: 'Explorar Programas';
$video_url = get_post_meta($post_id, 'programs_video_url', true);
$video_thumb = get_post_meta($post_id, 'programs_video_thumb', true);

// Vitrine
$carousel_title = get_post_meta($post_id, 'programs_carousel_title', true) ?: 'A Jornada do Conhecimento';
$endcard_title = get_post_meta($post_id, 'programs_endcard_title', true) ?: 'Mais Novidades em Breve';
$endcard_desc = get_post_meta($post_id, 'programs_endcard_desc', true) ?: 'Nossas metodologias estão sempre evoluindo.';

// CTA 1
$cta1_tag = get_post_meta($post_id, 'programs_cta1_tag', true) ?: 'Atendimento Especializado';
$cta1_title = get_post_meta($post_id, 'programs_cta1_title', true) ?: 'Qual é o seu próximo nível?';
$cta1_desc = get_post_meta($post_id, 'programs_cta1_desc', true) ?: 'Não tem certeza de qual programa é o ideal para o seu momento de carreira? Fale direto com um de nossos especialistas.';
$cta1_btn_text = get_post_meta($post_id, 'programs_cta1_btn_text', true) ?: 'Falar com Consultor';
$cta1_btn_link = get_post_meta($post_id, 'programs_cta1_btn_link', true) ?: '#contato';

// CTA 2
$cta2_title = get_post_meta($post_id, 'programs_cta2_title', true) ?: 'Deseja impulsionar o seu time?';
$cta2_desc = get_post_meta($post_id, 'programs_cta2_desc', true) ?: 'Oferecemos treinamentos in-company personalizados para as necessidades exclusivas e os desafios operacionais da sua empresa.';
$cta2_btn_text = get_post_meta($post_id, 'programs_cta2_btn_text', true) ?: 'Solicitar Orçamento In-company';
$cta2_btn_link = get_post_meta($post_id, 'programs_cta2_btn_link', true) ?: '#contato';

// Utility for basic video embeds
function saulocoelho_get_embed_url($url) {
    if (strpos($url, 'youtube.com') !== false || strpos($url, 'youtu.be') !== false) {
        preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/\s]{11})%i', $url, $match);
        $video_id = $match[1] ?? '';
        if ($video_id) return 'https://www.youtube.com/embed/' . $video_id . '?autoplay=1&mute=1&loop=1&playlist=' . $video_id;
    }
    if (strpos($url, 'vimeo.com') !== false) {
        $video_id = (int) substr(parse_url($url, PHP_URL_PATH), 1);
        if ($video_id) return 'https://player.vimeo.com/video/' . $video_id . '?background=1&autoplay=1&loop=1&muted=1';
    }
    return $url; // Fallback for raw mp4 or unknown
}
?>

<!-- ==============================================
     1. HERO SPLIT: TEXTO + VÍDEO
============================================== -->
<div class="mb-24 mt-8 flex flex-col lg:flex-row gap-12 items-center justify-between">
    <!-- Ala Esquerda: Texto -->
    <div class="w-full lg:w-1/2 flex flex-col gap-6 z-10">
        <div class="flex items-center gap-2 text-primary font-bold text-xs tracking-[0.3em] uppercase">
            <span class="h-px w-8 bg-primary shadow-[0_0_8px_currentColor]"></span>
            <?php echo esc_html($prog_eyebrow); ?>
        </div>
        
        <h1 class="text-5xl md:text-7xl font-black leading-[1.05] tracking-tighter text-white">
            <?php echo esc_html($prog_title_1); ?><br>
            <span class="text-primary tracking-tight"><?php echo esc_html($prog_title_2); ?></span>
        </h1>
        
        <p class="text-lg text-slate-300 font-light leading-relaxed mt-4 max-w-lg opacity-90">
            <?php echo wp_kses_post($prog_desc); ?>
        </p>
        
        <div class="mt-4 flex flew-wrap gap-4">
            <a href="#catalogo-slide" class="rounded-xl bg-primary px-8 py-3.5 font-black text-white shadow-[0_0_20px_-5px_rgba(0,186,255,0.4)] transition-all hover:-translate-y-1 hover:shadow-[0_0_25px_0_rgba(0,186,255,0.6)] uppercase tracking-widest text-xs flex items-center gap-2">
                <?php echo esc_html($prog_btn); ?> <span class="material-symbols-outlined text-[16px]">south</span>
            </a>
        </div>
    </div>
    
    <!-- Ala Direita: Vídeo Imersivo -->
    <div class="w-full lg:w-1/2 relative z-0">
        <!-- Efeito de Glow Neon atrás do Vídeo -->
        <div class="absolute inset-0 bg-primary/20 blur-[100px] rounded-full transform -translate-x-12 translate-y-12"></div>
        
        <div class="relative w-full aspect-video rounded-3xl overflow-hidden border border-white/10 shadow-2xl bg-zinc-900 group">
            <?php if ($video_url): ?>
                <?php $embed = saulocoelho_get_embed_url($video_url); ?>
                <?php if (strpos($embed, '.mp4') !== false): ?>
                    <video class="w-full h-full object-cover opacity-80 group-hover:opacity-100 transition-opacity duration-1000" autoplay loop muted playsinline poster="<?php echo esc_url($video_thumb); ?>">
                        <source src="<?php echo esc_url($embed); ?>" type="video/mp4">
                    </video>
                <?php else: ?>
                    <iframe class="w-full h-full absolute inset-0 pointer-events-none opacity-80 group-hover:opacity-100 transition-opacity duration-1000" 
                            src="<?php echo esc_url($embed); ?>" 
                            frameborder="0" allow="autoplay; fullscreen" allowfullscreen>
                    </iframe>
                <?php endif; ?>
            <?php else: ?>
                <!-- Fallback se não tiver vídeo cadastrado -->
                <div class="w-full h-full flex flex-col items-center justify-center opacity-30 bg-pattern-dots">
                    <span class="material-symbols-outlined text-6xl mb-4">play_circle</span>
                    <span class="text-xs uppercase tracking-widest font-bold">Adicione um vídeo no painel WP</span>
                </div>
            <?php endif; ?>
            
            <!-- Overlay Glassmorphism sutil na bordinha interna -->
            <div class="absolute inset-0 ring-1 ring-inset ring-white/10 rounded-3xl pointer-events-none"></div>
        </div>
    </div>
</div>

<!-- ==============================================
     2. CARROSSEL DE PROGRAMAS (WPFetch Automático)
============================================== -->
<?php 
// Buscar produtos "abertos" via WooCommerce
$args = array(
    'post_type'      => 'product',
    'posts_per_page' => -1,
    'tax_query'      => array(
        array(
            'taxonomy' => 'product_cat',
            'field'    => 'slug',
            'terms'    => array('cursos-presenciais', 'on-line', 'palestras'),
        ),
    ),
);
$products = new WP_Query($args);
$open_programs = [];

if ($products->have_posts()) :
    while ($products->have_posts()) : $products->the_post();
        global $product;
        $pid = get_the_ID();
        $status = get_post_meta($pid, 'course_sale_status', true) ?: 'aberto';
        
        // Só pega os abertos
        if ($status === 'aberto') {
            $open_programs[] = [
                'id'    => $pid,
                'title' => get_the_title(),
                'img'   => get_the_post_thumbnail_url($pid, 'large'),
                'desc'  => $product->get_short_description() ?: get_post_meta($pid, 'course_badge', true) ?: 'O caminho para a maestria executiva.',
                'link'  => get_permalink($pid),
            ];
        }
    endwhile;
wp_reset_postdata();
endif;
?>

<?php if (!empty($open_programs)) : ?>
<div id="catalogo-slide" class="mt-32 mb-20 relative scroll-mt-24">
    <div class="flex items-center gap-4 mb-10 pt-4">
        <h2 class="text-2xl lg:text-3xl font-black text-white uppercase tracking-wider"><?php echo esc_html($carousel_title); ?></h2>
        <div class="h-px flex-1 bg-gradient-to-r from-primary/50 to-transparent"></div>
    </div>

    <!-- O Container do Carrossel Native CSS (Snap) -->
    <div class="relative group">
        <!-- Indicador de Scroll Visual (Glow do lado direito para indicar cut-off) -->
        <div class="absolute right-0 top-0 bottom-0 w-24 bg-gradient-to-l from-background-dark-alt to-transparent z-10 pointer-events-none opacity-80"></div>
        
        <div class="flex overflow-x-auto snap-x snap-mandatory gap-6 pb-12 pt-4 px-2 -mx-2 hide-scrollbar" style="scroll-behavior: smooth;">
            
            <?php foreach ($open_programs as $prog) : ?>
                <!-- CARD (Premium Movie Poster Style) -->
                <div class="snap-start shrink-0 w-[280px] sm:w-[320px] group/card relative flex flex-col rounded-[2rem] bg-zinc-900 border border-white/5 overflow-hidden transition-all duration-500 hover:-translate-y-2 hover:shadow-[0_20px_40px_-15px_rgba(0,186,255,0.3)] hover:border-primary/40 cursor-grab active:cursor-grabbing">
                    
                    <div class="aspect-[2/3] relative overflow-hidden bg-slate-950 w-full">
                        <!-- Badge Inscrições -->
                        <div class="absolute top-4 left-4 z-20 transition-transform duration-500 group-hover/card:-translate-y-10 group-hover/card:opacity-0">
                            <span class="bg-primary/90 backdrop-blur text-white text-[9px] font-black uppercase tracking-widest px-3 py-1.5 rounded-full flex items-center gap-1.5 border border-white/10">
                                <span class="w-1.5 h-1.5 bg-white rounded-full animate-[pulse_2s_infinite]"></span>
                                Aberto
                            </span>
                        </div>

                        <?php if ($prog['img']) : ?>
                            <img src="<?php echo esc_url($prog['img']); ?>" alt="<?php echo esc_attr($prog['title']); ?>" 
                                 class="w-full h-full object-cover grayscale-[0.2] transition-all duration-700 group-hover/card:scale-105 group-hover/card:grayscale-0">
                        <?php else : ?>
                            <div class="w-full h-full flex flex-col items-center justify-center opacity-10">
                                <span class="material-symbols-outlined text-7xl font-light">menu_book</span>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Degradê dramático de base -->
                        <div class="absolute inset-0 bg-gradient-to-t from-zinc-950 via-zinc-950/40 to-transparent"></div>
                        
                        <!-- Overlay do Hover (Revela Texto) -->
                        <div class="absolute inset-0 bg-zinc-950/80 backdrop-blur-sm opacity-0 group-hover/card:opacity-100 transition-opacity duration-500 flex flex-col justify-end p-8 border-t border-primary/30">
                            <h3 class="text-2xl font-black text-white mb-2 leading-[1.1] transform translate-y-4 group-hover/card:translate-y-0 transition-transform duration-500"><?php echo esc_html($prog['title']); ?></h3>
                            <p class="text-[12px] text-slate-300 mb-6 font-light leading-relaxed line-clamp-4 transform translate-y-4 group-hover/card:translate-y-0 transition-transform duration-700 delay-75"><?php echo wp_strip_all_tags($prog['desc']); ?></p>
                            
                            <a href="<?php echo esc_url($prog['link']); ?>" class="inline-flex items-center gap-2 text-primary text-[10px] sm:text-xs font-black uppercase tracking-[0.2em] hover:gap-4 transition-all w-max transform translate-y-4 group-hover/card:translate-y-0 duration-1000 delay-100">
                                Acessar Treinamento <span class="material-symbols-outlined text-sm">arrow_forward</span>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <!-- Card Final Convidativo (End of list) -->
            <div class="snap-start shrink-0 w-[280px] sm:w-[320px] flex flex-col items-center justify-center p-8 rounded-[2rem] border border-dashed border-white/20 bg-white/5 opacity-60 hover:opacity-100 transition-all">
                <span class="material-symbols-outlined text-5xl text-primary mb-4">auto_awesome</span>
                <h3 class="font-black text-lg text-white text-center"><?php echo esc_html($endcard_title); ?></h3>
                <p class="text-xs text-slate-400 text-center mt-2"><?php echo esc_html($endcard_desc); ?></p>
            </div>
            
            <!-- Helper Padding para o final do scroll não colar na tela -->
            <div class="shrink-0 w-12"></div>
        </div>
    </div>
    
    <div class="flex items-center gap-2 text-slate-500 text-xs mt-2 uppercase tracking-widest font-bold">
        <span class="material-symbols-outlined text-[16px]">swipe</span> DESLIZE PARA VER MAIS
    </div>
</div>

<style>
/* Utility to hide scrollbar but keep functionality */
.hide-scrollbar::-webkit-scrollbar { display: none; }
.hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
<?php endif; ?>

<!-- ==============================================
     3. CTA AVALIAÇÃO / WHATSAPP (Glassmorphism)
============================================== -->
<div class="my-24 relative overflow-hidden rounded-[2.5rem] border border-white/10 bg-white/[0.02] p-10 md:p-16 flex flex-col md:flex-row items-center justify-between gap-10">
    <!-- Efeito Blur Background -->
    <div class="absolute inset-0 backdrop-blur-xl pointer-events-none"></div>
    <div class="absolute -right-20 -top-20 w-64 h-64 bg-primary/20 blur-[80px] rounded-full pointer-events-none"></div>
    
    <div class="relative z-10 md:w-2/3">
        <div class="inline-flex items-center gap-2 px-3 py-1 bg-white/10 rounded-full border border-white/10 text-[10px] font-black tracking-widest uppercase text-white mb-6">
            <span class="w-1.5 h-1.5 bg-green-400 rounded-full animate-pulse"></span> <?php echo esc_html($cta1_tag); ?>
        </div>
        <h3 class="text-3xl md:text-5xl font-black text-white leading-tight mb-4 tracking-tighter">
            <?php echo esc_html($cta1_title); ?>
        </h3>
        <p class="text-slate-400 font-light text-lg">
            <?php echo esc_html($cta1_desc); ?>
        </p>
    </div>
    
    <div class="relative z-10 md:w-1/3 flex justify-end">
        <a href="<?php echo esc_attr($cta1_btn_link); ?>" class="rounded-2xl px-8 py-5 bg-white text-background-dark font-black uppercase tracking-widest text-xs flex items-center gap-3 hover:bg-primary hover:text-white transition-all shadow-2xl hover:-translate-y-1 whitespace-nowrap">
            <?php echo esc_html($cta1_btn_text); ?> <span class="material-symbols-outlined">forum</span>
        </a>
    </div>
</div>

<!-- ==============================================
     4. IN-COMPANY (FOOTER CTA)
============================================== -->
<div class="mt-24 rounded-3xl bg-zinc-950 p-12 text-center border border-white/5 relative overflow-hidden shadow-2xl">
    <!-- Glow effect sutil -->
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[80%] h-[80%] bg-white/5 blur-[100px] rounded-full pointer-events-none"></div>
    
    <h3 class="text-3xl md:text-4xl font-black text-white mb-4 relative z-10"><?php echo esc_html($cta2_title); ?></h3>
    <p class="text-slate-400 max-w-2xl mx-auto mb-10 font-light relative z-10"><?php echo esc_html($cta2_desc); ?></p>
    
    <div class="flex flex-wrap justify-center gap-6 relative z-10">
        <a href="<?php echo esc_attr($cta2_btn_link); ?>" class="rounded-xl bg-primary px-10 py-4 font-black text-white shadow-xl shadow-primary/20 transition-all hover:-translate-y-1 uppercase tracking-widest text-sm">
            <?php echo esc_html($cta2_btn_text); ?>
        </a>
    </div>
</div>
