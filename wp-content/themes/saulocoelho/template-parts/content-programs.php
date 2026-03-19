<!-- Page Header -->
<div class="mb-12 flex flex-col gap-4">
    <div class="flex items-center gap-2 text-primary font-semibold text-sm tracking-wider uppercase">
        <span class="h-px w-8 bg-primary"></span>
        <?php the_title(); ?>
    </div>
    <h2 class="max-w-2xl text-4xl font-black leading-tight tracking-tight text-white md:text-5xl uppercase">
        Catálogo de <span class="text-primary">Programas</span> e Mentoria
    </h2>
    <div class="prose prose-invert max-w-xl text-lg text-slate-400 font-light leading-relaxed">
        <?php the_content(); ?>
    </div>
</div>

<!-- Program Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
    <?php 
    for ($i = 1; $i <= 6; $i++) : 
        $title = get_post_meta(get_the_ID(), "prog_card_{$i}_title", true);
        if (!$title && $i > 4) continue; // Show at least 4 placeholders or saved cards
        
        $icon = get_post_meta(get_the_ID(), "prog_card_{$i}_icon", true) ?: 'school';
        $desc = get_post_meta(get_the_ID(), "prog_card_{$i}_desc", true) ?: 'Descrição do programa de mentoria e alta performance.';
        $tag1 = get_post_meta(get_the_ID(), "prog_card_{$i}_tag1", true);
        $tag2 = get_post_meta(get_the_ID(), "prog_card_{$i}_tag2", true);
        $link = get_post_meta(get_the_ID(), "prog_card_{$i}_link", true) ?: '#';
        $title = $title ?: "Programa $i";
    ?>
    <div class="group relative flex flex-col bg-slate-800/40 rounded-2xl border border-white/5 p-8 hover:bg-slate-800/60 transition-all hover:border-primary/50">
        <div class="size-16 rounded-xl bg-primary/10 flex items-center justify-center mb-8 group-hover:scale-110 transition-transform">
            <span class="material-symbols-outlined text-primary text-3xl"><?php echo esc_html($icon); ?></span>
        </div>
        <h3 class="text-xl font-bold text-white mb-4"><?php echo esc_html($title); ?></h3>
        <p class="text-sm text-slate-400 mb-6 font-light leading-relaxed"><?php echo esc_html($desc); ?></p>
        
        <?php if ($tag1 || $tag2) : ?>
        <ul class="space-y-3 mb-8 text-balance">
            <?php if ($tag1) : ?>
            <li class="flex items-center gap-2 text-[10px] text-slate-400 uppercase tracking-widest font-bold">
                <span class="material-symbols-outlined text-primary text-xs">check_circle</span>
                <?php echo esc_html($tag1); ?>
            </li>
            <?php endif; ?>
            <?php if ($tag2) : ?>
            <li class="flex items-center gap-2 text-[10px] text-slate-400 uppercase tracking-widest font-bold">
                <span class="material-symbols-outlined text-primary text-xs">check_circle</span>
                <?php echo esc_html($tag2); ?>
            </li>
            <?php endif; ?>
        </ul>
        <?php endif; ?>

        <div class="mt-auto pt-6 border-t border-white/5">
            <a href="<?php echo esc_url($link); ?>" class="inline-flex items-center gap-2 text-primary text-xs font-bold uppercase tracking-widest hover:gap-3 transition-all">
                Saiba Mais <span class="material-symbols-outlined text-sm">arrow_forward</span>
            </a>
        </div>
    </div>
    <?php endfor; ?>
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
