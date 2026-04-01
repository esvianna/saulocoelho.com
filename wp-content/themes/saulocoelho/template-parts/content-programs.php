<?php
$post_id = get_the_ID();
$prog_title_1 = get_post_meta($post_id, 'programs_title_1', true) ?: 'Catálogo de';
$prog_title_2 = get_post_meta($post_id, 'programs_title_2', true) ?: 'Programas e Mentoria';
$prog_desc = get_post_meta($post_id, 'programs_description', true) ?: 'Metodologias exclusivas desenhadas para profissionais que buscam excelência operacional e liderança estratégica.';
?>

<!-- Page Header -->
<div class="mb-16 flex flex-col gap-6 max-w-3xl">
    <div class="flex items-center gap-2 text-primary font-bold text-xs tracking-[0.3em] uppercase">
        <span class="h-px w-8 bg-primary"></span>
        Nossas Soluções
    </div>
    <h1 class="text-4xl md:text-6xl font-black leading-[1.1] tracking-tight text-white">
        <?php echo esc_html($prog_title_1); ?> <span class="text-primary"><?php echo esc_html($prog_title_2); ?></span>
    </h1>
    <p class="text-lg text-slate-400 font-light leading-relaxed">
        <?php echo wp_kses_post($prog_desc); ?>
    </p>
</div>

<!-- Program Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
    <?php 
    for ($i = 1; $i <= 6; $i++) {
        $title = get_post_meta($post_id, "prog_card_{$i}_title", true);
        if (!$title && $i > 4) continue;
        
        $img    = get_post_meta($post_id, "prog_card_{$i}_img", true);
        $icon   = get_post_meta($post_id, "prog_card_{$i}_icon", true) ?: 'school';
        $desc   = get_post_meta($post_id, "prog_card_{$i}_desc", true) ?: 'Breve descrição do programa.';
        $link   = get_post_meta($post_id, "prog_card_{$i}_link", true) ?: '#';
        $status = get_post_meta($post_id, "prog_card_{$i}_status", true) ?: 'aberto';
        $title  = $title ?: "Programa $i";

        $is_open = ($status === 'aberto');
    ?>
    <div class="group flex flex-col bg-slate-900/40 rounded-3xl border border-white/5 overflow-hidden hover:bg-slate-900/60 transition-all hover:border-primary/50 <?php echo !$is_open ? 'opacity-80' : ''; ?>">
        <div class="aspect-[3/4] relative overflow-hidden bg-slate-800">
            <!-- Badge de Status -->
            <div class="absolute top-4 left-4 z-20">
                <?php if ($is_open) : ?>
                    <span class="bg-primary text-white text-[9px] font-black uppercase tracking-widest px-3 py-1.5 rounded-full shadow-lg flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></span>
                        Inscrições Abertas
                    </span>
                <?php else : ?>
                    <span class="bg-slate-800/90 text-slate-300 text-[9px] font-black uppercase tracking-widest px-3 py-1.5 rounded-full backdrop-blur-md border border-white/10">
                        Aguarde Próxima Turma
                    </span>
                <?php endif; ?>
            </div>

            <?php if ($img) : ?>
                <img src="<?php echo esc_url($img); ?>" alt="<?php echo esc_attr($title); ?>" class="w-full h-full object-cover transition-transform duration-700 <?php echo $is_open ? 'group-hover:scale-110' : 'grayscale-[0.5]'; ?>">
            <?php else : ?>
                <div class="w-full h-full flex items-center justify-center opacity-20">
                    <span class="material-symbols-outlined text-6xl"><?php echo esc_html($icon); ?></span>
                </div>
            <?php endif; ?>
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/20 to-transparent"></div>
        </div>
        
        <div class="p-8 flex flex-col flex-1">
            <h3 class="text-xl font-bold text-white mb-3 <?php echo $is_open ? 'group-hover:text-primary' : ''; ?> transition-colors leading-tight"><?php echo esc_html($title); ?></h3>
            <p class="text-[13px] text-slate-400 mb-8 font-light leading-relaxed line-clamp-3 italic opacity-70"><?php echo esc_html($desc); ?></p>
            
            <div class="mt-auto pt-6 border-t border-white/5">
                <a href="<?php echo esc_url($link); ?>" class="inline-flex items-center gap-2 text-primary text-[10px] font-black uppercase tracking-widest hover:gap-3 transition-all">
                    <?php echo $is_open ? 'Garanta Sua Vaga' : 'Saiba Mais / Lista de Espera'; ?> 
                    <span class="material-symbols-outlined text-xs">arrow_forward</span>
                </a>
            </div>
        </div>
    </div>
    <?php } ?>
</div>

<!-- Footer CTA -->
<div class="mt-24 rounded-3xl bg-slate-900 p-12 text-center border border-white/5 relative overflow-hidden shadow-2xl">
    <!-- Glow effect -->
    <div class="absolute -top-1/2 -left-1/2 w-full h-full bg-primary/5 blur-[120px] rounded-full"></div>
    
    <h3 class="text-3xl md:text-4xl font-black text-white mb-4 relative z-10">Não encontrou o que procurava?</h3>
    <p class="text-slate-400 max-w-2xl mx-auto mb-10 font-light relative z-10">Oferecemos treinamentos in-company personalizados para as necessidades exclusivas da sua equipe.</p>
    
    <div class="flex flex-wrap justify-center gap-6 relative z-10">
        <a href="#contato" class="rounded-xl bg-primary px-10 py-4 font-black text-white shadow-xl shadow-primary/20 transition-all hover:-translate-y-1 uppercase tracking-widest text-sm">
            Solicitar Orçamento
        </a>
        <a href="#contato" class="rounded-xl border border-white/20 bg-white/5 px-10 py-4 font-black text-white backdrop-blur-sm transition-all hover:bg-white/10 uppercase tracking-widest text-sm">
            Falar com Consultor
        </a>
    </div>
</div>
