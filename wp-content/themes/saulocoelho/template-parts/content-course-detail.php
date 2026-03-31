<?php
$pid = get_the_ID();
$badge = get_post_meta($pid, 'course_badge', true) ?: 'Matrículas Abertas';
$sec_btn_text = get_post_meta($pid, 'course_sec_btn_text', true);
if (empty($sec_btn_text) && !metadata_exists('post', $pid, 'course_sec_btn_text')) {
    $sec_btn_text = 'Ver currículo';
}
$sec_btn_link = get_post_meta($pid, 'course_sec_btn_link', true);
if (empty($sec_btn_link) && !metadata_exists('post', $pid, 'course_sec_btn_link')) {
    $sec_btn_link = '#conteudo';
}
$primary_btn_text = get_post_meta($pid, 'course_btn_1_text', true) ?: 'Quero me inscrever agora';
$instructor_name = get_post_meta($pid, 'course_instructor_name', true);
$ter_btn_text = get_post_meta($pid, 'course_ter_btn_text', true);
$ter_btn_link = get_post_meta($pid, 'course_ter_btn_link', true);
$qua_btn_text = get_post_meta($pid, 'course_qua_btn_text', true);
$qua_btn_link = get_post_meta($pid, 'course_qua_btn_link', true);
$video_img = get_post_meta($pid, 'course_video_url', true);
$stat_1 = get_post_meta($pid, 'course_stat_1', true) ?: '6k+ Alunos';
$stat_1_label = get_post_meta($pid, 'course_stat_1_label', true) ?: 'Instituições';
$stat_2 = get_post_meta($pid, 'course_stat_2', true) ?: '4.95/5 Avaliação';
$stat_2_label = get_post_meta($pid, 'course_stat_2_label', true) ?: 'Feedback';
$stat_3 = get_post_meta($pid, 'course_stat_3', true) ?: '40h+';
$stat_3_label = get_post_meta($pid, 'course_stat_3_label', true) ?: 'Conteúdo';
$stat_4 = get_post_meta($pid, 'course_stat_4', true) ?: 'Vitalício';
$stat_4_label = get_post_meta($pid, 'course_stat_4_label', true) ?: 'Acesso';
$price_install = get_post_meta($pid, 'course_price_install', true) ?: 'R$ 97,00';
$checkout_link = '?add-to-cart=' . $pid;

// WooCommerce Price Logic
$product = function_exists('wc_get_product') ? wc_get_product($pid) : null;
$regular_price_val = $product ? $product->get_regular_price() : '';
$sale_price_val = $product ? $product->get_sale_price() : '';
if (!$regular_price_val && $product) {
    $regular_price_val = $product->get_price(); // Fallback
}
$regular_price_formatted = $regular_price_val ? wc_price($regular_price_val) : '';
$sale_price_formatted = $sale_price_val ? wc_price($sale_price_val) : '';
$course_type = get_post_meta($pid, 'course_type', true) ?: 'online';
$event_section_title = get_post_meta($pid, 'course_event_section_title', true) ?: 'Logística do Evento';
$event_loc = get_post_meta($pid, 'course_event_location', true);
$event_dates = get_post_meta($pid, 'course_event_dates', true);
$event_dress = get_post_meta($pid, 'course_event_dresscode', true);

$learning_title = get_post_meta($pid, 'course_learning_title', true) ?: 'O que você vai aprender';
$learning_subtitle = get_post_meta($pid, 'course_learning_subtitle_desc', true) ?: 'Conteúdo estruturado para sua evolução.';
$learning_mode = get_post_meta($pid, 'course_learning_mode', true) ?: 'modules';
$learning_freetext = get_post_meta($pid, 'course_learning_freetext_desc', true);

$learning_topics = get_post_meta($pid, 'course_learning_topics', true);
if (!is_array($learning_topics)) $learning_topics = [];
$has_topics = count($learning_topics) > 0;

$benefits_title = get_post_meta($pid, 'course_benefits_title', true);
$benefits_desc = get_post_meta($pid, 'course_benefits_desc', true);
$benefits_media = get_post_meta($pid, 'course_benefits_media_url', true);

$mid_cta_1_text = get_post_meta($pid, 'course_mid_cta_1_text', true);
$mid_cta_1_btn = get_post_meta($pid, 'course_mid_cta_1_btn', true);
$mid_cta_1_link = get_post_meta($pid, 'course_mid_cta_1_link', true) ?: '#checkout';

$mid_cta_2_text = get_post_meta($pid, 'course_mid_cta_2_text', true);
$mid_cta_2_btn = get_post_meta($pid, 'course_mid_cta_2_btn', true);
$mid_cta_2_link = get_post_meta($pid, 'course_mid_cta_2_link', true) ?: '#checkout';

// Video Logic
$actual_video_url = get_post_meta($pid, 'course_actual_video_url', true);
$video_mode = get_post_meta($pid, 'course_video_mode', true) ?: 'inline';

$embed_url = '';
if ($actual_video_url) {
    if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i', $actual_video_url, $match)) {
        $embed_url = 'https://www.youtube.com/embed/' . $match[1] . '?autoplay=1&rel=0';
    } elseif (preg_match('/vimeo\.com\/(?:.*#|.*\/videos\/)?([0-9]+)/i', $actual_video_url, $match)) {
        $embed_url = 'https://player.vimeo.com/video/' . $match[1] . '?autoplay=1&autopause=0&title=0&byline=0&portrait=0';
    } else {
        $embed_url = esc_url($actual_video_url); // Fallback to raw URL
    }
}
?>

<!-- Course Hero Section -->
<section class="relative py-12 lg:py-24 overflow-hidden bg-background-dark">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
            <div class="flex flex-col gap-8">
                <span class="inline-flex items-center rounded-full bg-primary/10 px-5 py-2 text-[10px] font-black text-primary ring-1 ring-inset ring-primary/20 w-fit uppercase tracking-[0.3em]">
                    <?php echo esc_html($badge); ?>
                </span>
                <h1 class="text-4xl md:text-6xl font-black leading-[1.05] tracking-tighter text-white">
                    <?php the_title(); ?>
                </h1>
                <div class="prose prose-invert text-lg lg:text-xl text-slate-400 max-w-xl font-light leading-relaxed">
                    <?php the_content(); ?>
                </div>
                <?php if ($instructor_name) : ?>
                    <div class="text-white/60 text-sm font-bold uppercase tracking-wider -mt-4">
                        — <?php echo esc_html($instructor_name); ?>
                    </div>
                <?php endif; ?>
                <div class="flex flex-wrap gap-4 mt-4">
                    <a href="<?php echo esc_url($checkout_link); ?>" class="bg-primary hover:bg-primary/90 text-white px-8 py-5 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] shadow-2xl shadow-primary/40 transition-all hover:-translate-y-1 flex items-center justify-center gap-3">
                        <?php echo esc_html($primary_btn_text); ?>
                        <span class="material-symbols-outlined text-sm">arrow_forward</span>
                    </a>
                    
                    <?php if (!empty($sec_btn_text)) : ?>
                    <a href="<?php echo esc_url($sec_btn_link); ?>" class="bg-white/5 hover:bg-white/10 text-white px-8 py-5 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] border border-white/10 backdrop-blur-md transition-all hover:-translate-y-1 text-center flex items-center justify-center">
                        <?php echo esc_html($sec_btn_text); ?>
                    </a>
                    <?php endif; ?>

                    <?php if (!empty($ter_btn_text)) : ?>
                    <a href="<?php echo esc_url($ter_btn_link); ?>" class="bg-white/5 hover:bg-white/10 text-white px-8 py-5 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] border border-white/10 backdrop-blur-md transition-all hover:-translate-y-1 text-center flex items-center justify-center">
                        <?php echo esc_html($ter_btn_text); ?>
                    </a>
                    <?php endif; ?>

                    <?php if (!empty($qua_btn_text)) : ?>
                    <a href="<?php echo esc_url($qua_btn_link); ?>" class="bg-white/5 hover:bg-white/10 text-white px-8 py-5 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] border border-white/10 backdrop-blur-md transition-all hover:-translate-y-1 text-center flex items-center justify-center">
                        <?php echo esc_html($qua_btn_text); ?>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="relative group w-full">
                <div class="absolute -inset-2 bg-gradient-to-r from-primary to-blue-600 rounded-2xl blur opacity-20 group-hover:opacity-40 transition duration-1000"></div>
                <!-- ID adcionado para referenciar via JS e classes ajustadas -->
                <div class="relative bg-slate-900 rounded-2xl overflow-hidden shadow-2xl aspect-video border border-white/10" id="video-container">
                    
                    <?php if ($embed_url) : ?>
                        <!-- Capa do Vídeo (Será substituída ou abrirá o lightbox no clique) -->
                        <div id="video-cover" class="absolute inset-0 z-20 cursor-pointer flex items-center justify-center bg-black/40 group-hover:bg-black/20 transition-all duration-300"
                             onclick="playCourseVideo('<?php echo esc_js($video_mode); ?>', '<?php echo esc_js($embed_url); ?>')">
                            
                            <div class="size-24 bg-primary/90 text-white rounded-full flex items-center justify-center shadow-2xl backdrop-blur-sm transition-transform hover:scale-110">
                                <span class="material-symbols-outlined text-5xl" style="font-variation-settings: 'FILL' 1">play_arrow</span>
                            </div>
                        </div>
                    <?php else : ?>
                        <!-- Fallback: vai para o checkout -->
                        <div class="absolute inset-0 bg-black/40 flex items-center justify-center z-10">
                            <a href="<?php echo esc_url($checkout_link); ?>" class="size-24 bg-primary/90 hover:bg-primary text-white rounded-full flex items-center justify-center shadow-2xl backdrop-blur-sm transition-transform hover:scale-110">
                                <span class="material-symbols-outlined text-5xl" style="font-variation-settings: 'FILL' 1">play_arrow</span>
                            </a>
                        </div>
                    <?php endif; ?>

                    <?php if ($video_img) : ?>
                        <img src="<?php echo esc_url($video_img); ?>" alt="Preview" class="w-full h-full object-cover">
                    <?php else : ?>
                        <div class="w-full h-full bg-slate-800 flex items-center justify-center">
                             <span class="material-symbols-outlined text-8xl text-slate-700">video_library</span>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Container vazio para o iFrame inline -->
                    <div id="video-player-inline" class="absolute inset-0 z-10 hidden"></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Grid -->
<section class="py-16 border-y border-white/5 bg-background-dark-alt">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-12">
            <div class="flex flex-col items-center text-center gap-2">
                <span class="text-4xl font-black text-primary"><?php echo esc_html($stat_1); ?></span>
                <span class="text-xs font-bold text-slate-500 uppercase tracking-widest"><?php echo esc_html($stat_1_label); ?></span>
            </div>
            <div class="flex flex-col items-center text-center gap-2">
                <span class="text-4xl font-black text-primary"><?php echo esc_html($stat_2); ?></span>
                <span class="text-xs font-bold text-slate-500 uppercase tracking-widest"><?php echo esc_html($stat_2_label); ?></span>
            </div>
            <div class="flex flex-col items-center text-center gap-2">
                <span class="text-4xl font-black text-primary"><?php echo esc_html($stat_3); ?></span>
                <span class="text-xs font-bold text-slate-500 uppercase tracking-widest"><?php echo esc_html($stat_3_label); ?></span>
            </div>
            <div class="flex flex-col items-center text-center gap-2">
                <span class="text-4xl font-black text-primary"><?php echo esc_html($stat_4); ?></span>
                <span class="text-xs font-bold text-slate-500 uppercase tracking-widest"><?php echo esc_html($stat_4_label); ?></span>
            </div>
        </div>
    </div>
</section>

<!-- Curriculum -->
<section class="py-24" id="conteudo">
    <div class="mx-auto <?php echo $has_topics ? 'max-w-6xl' : 'max-w-3xl'; ?> px-6 lg:px-8">
        <div class="text-center mb-12 text-balance">
            <h2 class="text-3xl md:text-5xl font-black tracking-tight mb-6"><?php echo esc_html($learning_title); ?></h2>
            <?php if ($learning_subtitle) : ?>
                <p class="text-lg text-slate-400 font-light leading-relaxed"><?php echo esc_html($learning_subtitle); ?></p>
            <?php endif; ?>
        </div>
        
        <div class="<?php echo $has_topics ? 'grid lg:grid-cols-5 gap-12 items-start' : 'flex flex-col gap-6'; ?>">
            <!-- Coluna Esquerda: Texto ou Módulos -->
            <div class="<?php echo $has_topics ? 'lg:col-span-3 flex flex-col gap-6' : 'w-full flex flex-col gap-6'; ?>">
                <?php if ($learning_mode === 'freetext' && !empty($learning_freetext)) : ?>
                    <div class="bg-white/[0.03] border border-white/10 p-8 md:p-12 rounded-3xl text-slate-300 font-light leading-relaxed prose prose-invert prose-p:mb-6 prose-ul:mb-6 prose-li:my-2 custom-list h-full">
                        <?php echo wpautop(wp_kses_post($learning_freetext)); ?>
                    </div>
                <?php else : ?>
                    <?php
                    $has_modules = false;
                    for ($i = 1; $i <= 8; $i++) {
                        $mod_title = get_post_meta($pid, "course_mod_{$i}_title", true);
                        $mod_desc = get_post_meta($pid, "course_mod_{$i}_desc", true);
                        
                        if ($mod_title) {
                            $has_modules = true;
                            ?>
                            <div class="bg-white/[0.03] border border-white/10 p-8 rounded-2xl transition-all hover:bg-white/[0.05]">
                                <h4 class="text-xl font-bold text-white mb-2"><?php echo esc_html($mod_title); ?></h4>
                                <?php if ($mod_desc): ?>
                                    <p class="text-slate-400 font-light leading-relaxed"><?php echo esc_html($mod_desc); ?></p>
                                <?php endif; ?>
                            </div>
                            <?php
                        }
                    }
                    
                    if (!$has_modules) {
                        echo '<p class="text-slate-400 font-light text-center">Nenhum módulo cadastrado ainda.</p>';
                    }
                    ?>
                <?php endif; ?>
            </div>
            
            <!-- Coluna Direita: Tópicos Dinâmicos -->
            <?php if ($has_topics) : ?>
                <div class="lg:col-span-2 flex flex-col gap-4">
                    <?php foreach ($learning_topics as $topic) : 
                        if (empty($topic['text'])) continue;
                    ?>
                        <div class="flex items-start gap-4 p-5 rounded-2xl bg-white/[0.02] border border-white/5 transition-all hover:bg-white/[0.04] hover:-translate-y-1">
                            <?php if (!empty($topic['icon'])) : 
                                if (strpos($topic['icon'], 'http') === 0 || strpos($topic['icon'], '/') === 0) : ?>
                                    <img src="<?php echo esc_url($topic['icon']); ?>" alt="" class="w-12 h-12 rounded-xl object-contain bg-slate-800 p-2 shrink-0 shadow-lg border border-white/10">
                                <?php else : ?>
                                    <div class="w-12 h-12 rounded-xl bg-primary/20 text-primary flex items-center justify-center shrink-0 border border-primary/20">
                                        <span class="material-symbols-outlined text-2xl" style="font-variation-settings: 'FILL' 1;"><?php echo esc_html($topic['icon']); ?></span>
                                    </div>
                                <?php endif;
                            else: ?>
                                <div class="w-12 h-12 rounded-xl bg-primary/20 text-primary flex items-center justify-center shrink-0 border border-primary/20">
                                    <span class="material-symbols-outlined text-2xl" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                                </div>
                            <?php endif; ?>
                            <div class="flex flex-col self-center">
                                <p class="text-slate-200 font-medium leading-relaxed">
                                    <?php echo esc_html($topic['text']); ?>
                                </p>
                                <?php if (!empty($topic['desc'])) : ?>
                                    <p class="text-slate-400 text-sm font-light leading-relaxed mt-1">
                                        <?php echo nl2br(esc_html($topic['desc'])); ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php if ($mid_cta_1_btn) : ?>
<!-- CTA 1 (Pós-Currículo) -->
<section class="py-12 bg-background-dark border-t border-white/5 relative z-20 shadow-[0_-20px_40px_-15px_rgba(0,0,0,0.5)]">
    <div class="max-w-4xl mx-auto px-6 text-center">
        <?php if ($mid_cta_1_text) : ?>
            <p class="text-white text-xl md:text-2xl mb-8 font-black leading-tight tracking-tight"><?php echo esc_html($mid_cta_1_text); ?></p>
        <?php endif; ?>
        <a href="<?php echo esc_url($mid_cta_1_link); ?>" class="inline-block bg-primary hover:bg-primary/90 text-white px-10 py-5 rounded-2xl text-sm font-black uppercase tracking-[0.2em] shadow-xl shadow-primary/30 transition-all hover:-translate-y-1">
            <?php echo esc_html($mid_cta_1_btn); ?>
        </a>
    </div>
</section>
<?php endif; ?>

<?php if ($benefits_title) : ?>
<!-- Benefits Section (Split Screen) -->
<section class="py-24 bg-slate-900/50 border-t border-white/5 relative overflow-hidden" id="beneficios">
    <!-- Glow effect behind benefits -->
    <div class="absolute top-0 right-0 w-1/2 h-full bg-primary/10 blur-[150px] pointer-events-none rounded-full transform translate-x-1/2"></div>
    
    <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10">
        <div class="grid lg:grid-cols-2 gap-16 lg:gap-24 items-center">
            
            <!-- Left: Text -->
            <div class="space-y-8">
                <h2 class="text-3xl md:text-5xl font-black text-white leading-tight tracking-tight scale-fade-in"><?php echo esc_html($benefits_title); ?></h2>
                <?php if ($benefits_desc) : ?>
                    <div class="text-lg text-slate-300 font-light leading-relaxed space-y-4 prose prose-invert prose-p:text-slate-300 prose-ul:text-slate-300 prose-li:marker:text-primary custom-list">
                        <?php echo wpautop(wp_kses_post($benefits_desc)); ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Right: Media -->
            <div class="relative w-full aspect-video rounded-3xl overflow-hidden border border-white/10 shadow-2xl bg-background-dark flex items-center justify-center group">
                <div class="absolute inset-0 bg-gradient-to-tr from-white/5 to-transparent pointer-events-none z-10"></div>
                <?php if (strpos($benefits_media, 'youtube.com') !== false || strpos($benefits_media, 'youtu.be') !== false || strpos($benefits_media, 'vimeo.com') !== false) : 
                    // Video Iframe Logic
                    $embed_url = '';
                    if (strpos($benefits_media, 'youtube.com') !== false || strpos($benefits_media, 'youtu.be') !== false) {
                        preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i', $benefits_media, $video_match);
                        if (!empty($video_match[1])) {
                            $embed_url = 'https://www.youtube.com/embed/' . $video_match[1] . '?modestbranding=1&rel=0';
                        }
                    } elseif (strpos($benefits_media, 'vimeo.com') !== false) {
                        preg_match('/vimeo\.com\/([0-9]+)/i', $benefits_media, $video_match);
                        if (!empty($video_match[1])) {
                            $embed_url = 'https://player.vimeo.com/video/' . $video_match[1];
                        }
                    }
                    if ($embed_url) : ?>
                        <iframe src="<?php echo esc_url($embed_url); ?>" class="w-full h-full object-cover" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>
                    <?php else : ?>
                        <p class="text-slate-500 text-sm">Formato de vídeo inválido.</p>
                    <?php endif;
                elseif (preg_match('/\.mp4$|\.webm$|\.ogg$/i', $benefits_media)) : ?>
                    <!-- Native Hosted Video (Anti-Download Protection) -->
                    <video src="<?php echo esc_url($benefits_media); ?>" class="w-full h-full object-cover rounded-3xl" controls playsinline controlsList="nodownload" oncontextmenu="return false;"></video>
                <?php elseif ($benefits_media) : ?>
                    <!-- Image -->
                    <img src="<?php echo esc_url($benefits_media); ?>" alt="Benefícios" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                <?php else : ?>
                    <!-- Placeholder if left empty but title exists -->
                    <div class="flex flex-col items-center justify-center text-slate-600 gap-4">
                        <span class="material-symbols-outlined text-5xl">image</span>
                        <p class="text-sm font-light">Nenhuma mídia informada.</p>
                    </div>
                <?php endif; ?>
            </div>
            
        </div>
    </div>
</section>
<?php endif; ?>

<?php if ($mid_cta_2_btn) : ?>
<!-- CTA 2 (Pós-Benefícios) -->
<section class="py-16 bg-slate-900/80 border-t border-white/5 relative z-20">
    <div class="max-w-4xl mx-auto px-6 text-center">
        <?php if ($mid_cta_2_text) : ?>
            <p class="text-white text-2xl font-black mb-8 leading-tight"><?php echo wp_kses_post($mid_cta_2_text); ?></p>
        <?php endif; ?>
        <a href="<?php echo esc_url($mid_cta_2_link); ?>" class="inline-block bg-primary hover:bg-primary/90 text-white px-10 py-5 rounded-2xl text-sm font-black uppercase tracking-[0.2em] shadow-xl shadow-primary/30 transition-all hover:-translate-y-1">
            <?php echo esc_html($mid_cta_2_btn); ?>
        </a>
    </div>
</section>
<?php endif; ?>


<!-- What's Included / Not Included -->
<section class="py-24 border-t border-white/5 bg-background-dark">
    <div class="mx-auto max-w-5xl px-6 lg:px-8">
        <div class="grid md:grid-cols-2 gap-16">
            <!-- Included -->
            <div class="<?php echo ($course_type === 'online') ? 'md:col-span-2 md:max-w-2xl md:mx-auto w-full' : ''; ?>">
                <h3 class="text-2xl font-black text-white mb-8 flex items-center gap-3 <?php echo ($course_type === 'online') ? 'justify-center' : ''; ?>">
                    <span class="material-symbols-outlined text-green-500 text-3xl">check_circle</span>
                    O Que Está Incluso
                </h3>
                <ul class="flex flex-col gap-4">
                    <?php
                    $has_inc = false;
                    for ($i = 1; $i <= 6; $i++) {
                        $inc_title = get_post_meta($pid, "course_inc_{$i}_title", true);
                        if ($inc_title) {
                            $has_inc = true;
                            echo '<li class="flex items-start gap-3 text-slate-300"><span class="material-symbols-outlined text-green-500 shrink-0 text-xl">check</span> ' . esc_html($inc_title) . '</li>';
                        }
                    }
                    if (!$has_inc) {
                        echo '<li class="text-slate-500 text-sm italic">Nenhum item adicionado à lista.</li>';
                    }
                    ?>
                </ul>
            </div>
            
            <?php if ($course_type === 'presencial') : ?>
            <!-- Not Included -->
            <div>
                <h3 class="text-2xl font-black text-white mb-8 flex items-center gap-3">
                    <span class="material-symbols-outlined text-red-500/80 text-3xl">cancel</span>
                    O Que Não Está Incluso
                </h3>
                <ul class="flex flex-col gap-4">
                    <?php
                    $has_notinc = false;
                    for ($i = 1; $i <= 3; $i++) {
                        $notinc_title = get_post_meta($pid, "course_notinc_{$i}_title", true);
                        if ($notinc_title) {
                            $has_notinc = true;
                            echo '<li class="flex items-start gap-3 text-slate-400"><span class="material-symbols-outlined text-slate-500 shrink-0 text-xl">close</span> ' . esc_html($notinc_title) . '</li>';
                        }
                    }
                    if (!$has_notinc) {
                        echo '<li class="text-slate-500 text-sm italic">Nenhum detalhe informado.</li>';
                    }
                    ?>
                </ul>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php if ($course_type === 'presencial') : ?>
<!-- Event Info (Presencial) -->
<section class="py-16 border-b border-white/5 bg-slate-900/50 relative overflow-hidden">
    <div class="mx-auto max-w-5xl px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-black text-white"><?php echo esc_html($event_section_title); ?></h2>
        </div>
        
        <div class="grid md:grid-cols-3 gap-8">
            <div class="bg-background-dark p-8 rounded-2xl border border-white/10 flex flex-col items-center text-center gap-4 hover:-translate-y-1 transition-transform">
                <span class="material-symbols-outlined text-4xl text-primary">location_on</span>
                <h4 class="text-white font-bold">Local</h4>
                <p class="text-slate-400 text-sm"><?php echo nl2br(esc_html($event_loc ?: 'Local a definir')); ?></p>
            </div>
            <div class="bg-background-dark p-8 rounded-2xl border border-white/10 flex flex-col items-center text-center gap-4 hover:-translate-y-1 transition-transform">
                <span class="material-symbols-outlined text-4xl text-primary">calendar_month</span>
                <h4 class="text-white font-bold">Datas e Horários</h4>
                <p class="text-slate-400 text-sm"><?php echo nl2br(esc_html($event_dates ?: 'Datas em breve')); ?></p>
            </div>
            <div class="bg-background-dark p-8 rounded-2xl border border-white/10 flex flex-col items-center text-center gap-4 hover:-translate-y-1 transition-transform">
                <span class="material-symbols-outlined text-4xl text-primary">info</span>
                <h4 class="text-white font-bold">Avisos Importantes</h4>
                <p class="text-slate-400 text-sm"><?php echo nl2br(esc_html($event_dress ?: 'Sem avisos no momento')); ?></p>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Pricing -->
<section class="py-24 relative overflow-hidden" id="checkout">
    <div class="mx-auto max-w-5xl px-6 lg:px-8 relative z-10">
        <div class="bg-slate-900 dark:bg-slate-900/80 rounded-3xl p-10 md:p-20 text-center border border-white/10 backdrop-blur-xl shadow-2xl relative overflow-hidden">
             <!-- Glow effect -->
            <div class="absolute -top-1/2 -left-1/2 w-full h-full bg-primary/20 blur-[120px] rounded-full"></div>
            
            <h3 class="text-3xl md:text-5xl font-black text-white mb-6 relative z-10">Pronto para o próximo passo?</h3>
            
            <div class="flex flex-col items-center gap-8 relative z-10">
                <div class="flex flex-col items-center gap-2">
                    <?php if ($sale_price_val) : ?>
                        <span class="text-slate-400 text-lg">
                            Valor à vista de <span class="line-through text-slate-500 mr-1"><?php echo wp_kses_post($regular_price_formatted); ?></span> por <strong class="text-white"><?php echo wp_kses_post($sale_price_formatted); ?></strong>
                        </span>
                    <?php else : ?>
                        <span class="text-slate-400 text-lg">
                            Valor à vista <strong class="text-white"><?php echo wp_kses_post($regular_price_formatted); ?></strong>
                        </span>
                    <?php endif; ?>
                    
                    <div class="flex items-center gap-4 mt-2">
                        <span class="text-white text-3xl font-bold">ou 12x de</span>
                        <span class="text-primary text-7xl font-black"><?php echo esc_html($price_install); ?></span>
                    </div>
                </div>
                
                <a href="<?php echo esc_url($checkout_link); ?>" class="w-full max-w-md bg-primary hover:bg-primary/90 text-white px-10 py-6 rounded-2xl text-2xl font-black shadow-2xl shadow-primary/40 transition-all hover:-translate-y-1 uppercase tracking-widest text-center">
                    Quero Garantir Minha Vaga
                </a>
                
                <div class="flex flex-wrap justify-center gap-8 mt-4 pt-8 border-t border-white/5 w-full">
                    <div class="flex items-center gap-2 text-slate-400 text-[10px] font-bold uppercase tracking-widest">
                        <span class="material-symbols-outlined text-green-500">verified_user</span>
                        Compra 100% Segura
                    </div>
                    <div class="flex items-center gap-2 text-slate-400 text-[10px] font-bold uppercase tracking-widest">
                        <span class="material-symbols-outlined text-green-500">assignment_return</span>
                        7 Dias de Garantia
                    </div>
                    <div class="flex items-center gap-2 text-slate-400 text-[10px] font-bold uppercase tracking-widest">
                        <span class="material-symbols-outlined text-green-500">play_circle</span>
                        Acesso Imediato
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Lightbox Modal -->
<div id="course-video-lightbox" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/90 backdrop-blur-sm opacity-0 transition-opacity duration-300">
    <div class="relative w-full max-w-5xl aspect-video bg-black rounded-2xl shadow-2xl overflow-hidden scale-95 transition-transform duration-300 mx-4" id="video-lightbox-content">
        <button onclick="closeCourseLightbox()" class="absolute top-4 right-4 z-50 text-white bg-black/50 hover:bg-primary rounded-full p-2 backdrop-blur-md transition-colors shadow-lg">
            <span class="material-symbols-outlined">close</span>
        </button>
        <div id="lightbox-player-container" class="w-full h-full"></div>
    </div>
</div>

<script>
function playCourseVideo(mode, url) {
    if (mode === 'lightbox') {
        const lightbox = document.getElementById('course-video-lightbox');
        const content = document.getElementById('video-lightbox-content');
        const player = document.getElementById('lightbox-player-container');
        
        player.innerHTML = `<iframe src="${url}" class="w-full h-full border-0 absolute inset-0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>`;
        
        // Exibir modal
        lightbox.classList.remove('hidden');
        lightbox.classList.add('flex');
        
        // Aplicar transições
        setTimeout(() => {
            lightbox.classList.remove('opacity-0');
            content.classList.remove('scale-95');
        }, 10);
        
    } else {
        // Modo Inline (Na própria caixa do Hero)
        const cover = document.getElementById('video-cover');
        const player = document.getElementById('video-player-inline');
        
        player.innerHTML = `<iframe src="${url}" class="w-full h-full border-0 absolute inset-0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>`;
        player.classList.remove('hidden');
        
        // Sumir com a cover suavemente
        cover.style.opacity = '0';
        setTimeout(() => cover.classList.add('hidden'), 300);
    }
}

function closeCourseLightbox() {
    const lightbox = document.getElementById('course-video-lightbox');
    const content = document.getElementById('video-lightbox-content');
    const player = document.getElementById('lightbox-player-container');
    
    // Ocultar com transição
    lightbox.classList.add('opacity-0');
    content.classList.add('scale-95');
    
    setTimeout(() => {
        lightbox.classList.remove('flex');
        lightbox.classList.add('hidden');
        player.innerHTML = ''; // Parar o vídeo
    }, 300);
}
</script>

<?php
// ── Módulo Alumni: Galeria de Turmas ──────────────────────────────────────
// Renderiza a seção de fotos das turmas passadas (se configuradas no admin).
get_template_part( 'template-parts/alumni/gallery-section' );
?>
