<?php
$pid = get_the_ID();
$badge = get_post_meta($pid, 'course_badge', true) ?: 'Matrículas Abertas';
$video_img = get_post_meta($pid, 'course_video_url', true);
$stat_1 = get_post_meta($pid, 'course_stat_1', true) ?: '10k+ Alunos';
$stat_2 = get_post_meta($pid, 'course_stat_2', true) ?: '4.9/5 Avaliação';
$price_full = get_post_meta($pid, 'course_price_full', true) ?: 'R$ 1.997,00';
$price_install = get_post_meta($pid, 'course_price_install', true) ?: 'R$ 97,00';
$checkout_link = get_post_meta($pid, 'course_checkout_link', true) ?: '#';
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
            
            <div class="relative group">
                <div class="absolute -inset-2 bg-gradient-to-r from-primary to-blue-600 rounded-2xl blur opacity-20 group-hover:opacity-40 transition duration-1000"></div>
                <div class="relative bg-slate-900 rounded-2xl overflow-hidden shadow-2xl aspect-video border border-white/10">
                    <div class="absolute inset-0 bg-black/40 flex items-center justify-center z-10">
                        <a href="<?php echo esc_url($checkout_link); ?>" class="size-24 bg-primary/90 hover:bg-primary text-white rounded-full flex items-center justify-center shadow-2xl backdrop-blur-sm transition-transform hover:scale-110">
                            <span class="material-symbols-outlined text-5xl" style="font-variation-settings: 'FILL' 1">play_arrow</span>
                        </a>
                    </div>
                    <?php if ($video_img) : ?>
                        <img src="<?php echo esc_url($video_img); ?>" alt="Preview" class="w-full h-full object-cover">
                    <?php else : ?>
                        <div class="w-full h-full bg-slate-800 flex items-center justify-center">
                             <span class="material-symbols-outlined text-8xl text-slate-700">video_library</span>
                        </div>
                    <?php endif; ?>
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

<!-- Curriculum -->
<section class="py-24" id="conteudo">
    <div class="mx-auto max-w-3xl px-6 lg:px-8">
        <div class="text-center mb-20 text-balance">
            <h2 class="text-3xl md:text-5xl font-black tracking-tight mb-6">O que você vai aprender</h2>
            <p class="text-lg text-slate-400 font-light leading-relaxed">Conteúdo estruturado para sua evolução.</p>
        </div>
        
        <div class="flex flex-col gap-6">
            <!-- Dynamizing these as individual fields is overkill for now, keeping as structured placeholders -->
            <div class="bg-white/[0.03] border border-white/10 p-8 rounded-2xl">
                <h4 class="text-xl font-bold text-white mb-2">Módulo 1: Fundamentos da Estratégia</h4>
                <p class="text-slate-400 font-light">A base teórica e os primeiros passos estratégicos.</p>
            </div>
            <div class="bg-white/[0.03] border border-white/10 p-8 rounded-2xl">
                <h4 class="text-xl font-bold text-white mb-2">Módulo 2: Implementação e Gestão</h4>
                <p class="text-slate-400 font-light">Otimização profunda de processos internos.</p>
            </div>
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
