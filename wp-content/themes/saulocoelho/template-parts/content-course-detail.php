<?php
$pid = get_the_ID();
$badge = get_post_meta($pid, 'course_badge', true) ?: 'Matrículas Abertas';
$video_img = get_post_meta($pid, 'course_video_url', true);
$stat_1 = get_post_meta($pid, 'course_stat_1', true) ?: '10k+ Alunos';
$stat_2 = get_post_meta($pid, 'course_stat_2', true) ?: '4.9/5 Avaliação';
$price_full = get_post_meta($pid, 'course_price_full', true) ?: 'R$ 1.997,00';
$price_install = get_post_meta($pid, 'course_price_install', true) ?: 'R$ 97,00';
$checkout_link = '?add-to-cart=' . $pid;
$course_type = get_post_meta($pid, 'course_type', true) ?: 'online';
$event_loc = get_post_meta($pid, 'course_event_location', true);
$event_dates = get_post_meta($pid, 'course_event_dates', true);
$event_dress = get_post_meta($pid, 'course_event_dresscode', true);

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
                <div class="flex flex-col sm:flex-row gap-6 mt-4">
                    <a href="<?php echo esc_url($checkout_link); ?>" class="bg-primary hover:bg-primary/90 text-white px-10 py-5 rounded-2xl text-xs font-black uppercase tracking-[0.2em] shadow-2xl shadow-primary/40 transition-all hover:-translate-y-1 flex items-center justify-center gap-3">
                        Quero me inscrever agora
                        <span class="material-symbols-outlined text-sm">arrow_forward</span>
                    </a>
                    <a href="#conteudo" class="bg-white/5 hover:bg-white/10 text-white px-10 py-5 rounded-2xl text-xs font-black uppercase tracking-[0.2em] border border-white/10 backdrop-blur-md transition-all hover:-translate-y-1 text-center">
                        Ver currículo
                    </a>
                </div>
            </div>
            
            <div class="relative group h-full">
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
                <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Instituições</span>
            </div>
            <div class="flex flex-col items-center text-center gap-2">
                <span class="text-4xl font-black text-primary"><?php echo esc_html($stat_2); ?></span>
                <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Feedback</span>
            </div>
            <div class="flex flex-col items-center text-center gap-2">
                <span class="text-4xl font-black text-primary">40h+</span>
                <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Conteúdo</span>
            </div>
            <div class="flex flex-col items-center text-center gap-2">
                <span class="text-4xl font-black text-primary">Vitalício</span>
                <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Acesso</span>
            </div>
        </div>
    </div>
</section>

<?php if ($course_type === 'presencial') : ?>
<!-- Event Info (Presencial) -->
<section class="py-16 border-b border-white/5 bg-slate-900/50 relative overflow-hidden">
    <div class="mx-auto max-w-5xl px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-black text-white">Logística do Evento</h2>
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

<!-- Curriculum -->
<section class="py-24" id="conteudo">
    <div class="mx-auto max-w-3xl px-6 lg:px-8">
        <div class="text-center mb-20 text-balance">
            <h2 class="text-3xl md:text-5xl font-black tracking-tight mb-6">O que você vai aprender</h2>
            <p class="text-lg text-slate-400 font-light leading-relaxed">Conteúdo estruturado para sua evolução.</p>
        </div>
        
        <div class="flex flex-col gap-6">
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
                            <p class="text-slate-400 font-light"><?php echo esc_html($mod_desc); ?></p>
                        <?php endif; ?>
                    </div>
                    <?php
                }
            }
            
            if (!$has_modules) {
                echo '<p class="text-slate-400 font-light text-center">Nenhum módulo cadastrado ainda.</p>';
            }
            ?>
        </div>
    </div>
</section>

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

<!-- Pricing -->
<section class="py-24 relative overflow-hidden" id="checkout">
    <div class="mx-auto max-w-5xl px-6 lg:px-8 relative z-10">
        <div class="bg-slate-900 dark:bg-slate-900/80 rounded-3xl p-10 md:p-20 text-center border border-white/10 backdrop-blur-xl shadow-2xl relative overflow-hidden">
             <!-- Glow effect -->
            <div class="absolute -top-1/2 -left-1/2 w-full h-full bg-primary/20 blur-[120px] rounded-full"></div>
            
            <h3 class="text-3xl md:text-5xl font-black text-white mb-6 relative z-10">Pronto para o próximo passo?</h3>
            
            <div class="flex flex-col items-center gap-8 relative z-10">
                <div class="flex flex-col items-center gap-2">
                    <span class="text-slate-500 line-through text-lg">De <?php echo esc_html($price_full); ?></span>
                    <div class="flex items-center gap-4">
                        <span class="text-white text-3xl font-bold">12x de</span>
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
