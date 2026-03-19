<!-- Store Header -->
<section class="py-16 lg:py-24 bg-background-dark-alt">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
        <div class="flex flex-col gap-6 max-w-2xl">
            <div class="flex items-center gap-3 text-primary font-black uppercase text-xs tracking-widest">
                <span class="h-px w-10 bg-primary"></span>
                Loja Oficial
            </div>
            <h1 class="text-4xl lg:text-6xl font-black text-white leading-tight">
                Produtos e <span class="text-primary">Estratégias</span> de Elite
            </h1>
            <p class="text-lg text-slate-400 font-light leading-relaxed">
                Acesse as ferramentas e conhecimentos que Saulo Coelho utiliza para escalar negócios e desenvolver lideranças de alta performance.
            </p>
        </div>
    </div>
</section>

<!-- Product Catalog -->
<section class="py-20 bg-background-dark">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
        <!-- Filters (Placeholder) -->
        <div class="flex items-center gap-8 mb-16 border-b border-white/5 pb-8 overflow-x-auto no-scrollbar">
            <button class="text-primary font-bold uppercase text-xs tracking-[0.2em] whitespace-nowrap">Todos os Produtos</button>
            <button class="text-slate-500 hover:text-white transition-colors font-bold uppercase text-xs tracking-[0.2em] whitespace-nowrap">Treinamentos</button>
            <button class="text-slate-500 hover:text-white transition-colors font-bold uppercase text-xs tracking-[0.2em] whitespace-nowrap">Mentorias</button>
            <button class="text-slate-500 hover:text-white transition-colors font-bold uppercase text-xs tracking-[0.2em] whitespace-nowrap">E-books</button>
        </div>

        <!-- Product Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
            <?php 
            for ($i = 1; $i <= 6; $i++) : 
                $title = get_post_meta(get_the_ID(), "store_prod_{$i}_title", true);
                if (!$title && $i > 3) continue;

                $img = get_post_meta(get_the_ID(), "store_prod_{$i}_img", true);
                $badge = get_post_meta(get_the_ID(), "store_prod_{$i}_badge", true);
                $desc = get_post_meta(get_the_ID(), "store_prod_{$i}_desc", true) ?: 'Descrição do produto ou treinamento de elite.';
                $price = get_post_meta(get_the_ID(), "store_prod_{$i}_price", true) ?: 'R$ 0,00';
                $install = get_post_meta(get_the_ID(), "store_prod_{$i}_installments", true) ?: '12x de';
                $link = get_post_meta(get_the_ID(), "store_prod_{$i}_link", true) ?: '#';
                $title = $title ?: "Produto $i";
            ?>
            <div class="group relative flex flex-col bg-background-dark-alt rounded-2xl overflow-hidden border border-white/5 hover:border-primary/30 transition-all hover:shadow-2xl shadow-primary/10">
                <div class="aspect-[4/3] bg-slate-800 overflow-hidden relative">
                    <?php if ($img) : ?>
                        <img src="<?php echo esc_url($img); ?>" alt="<?php echo esc_attr($title); ?>" class="w-full h-full object-cover opacity-80 group-hover:scale-110 transition-transform duration-700">
                    <?php else : ?>
                        <div class="w-full h-full bg-slate-900 flex items-center justify-center">
                            <span class="material-symbols-outlined text-6xl text-slate-800">shopping_bag</span>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($badge) : ?>
                        <div class="absolute top-4 left-4 bg-primary text-white text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-tighter shadow-lg"><?php echo esc_html($badge); ?></div>
                    <?php endif; ?>
                </div>
                <div class="p-8 flex flex-col flex-1 gap-4">
                    <h3 class="text-xl font-bold text-white group-hover:text-primary transition-colors"><?php echo esc_html($title); ?></h3>
                    <p class="text-sm text-slate-400 font-light leading-relaxed flex-1"><?php echo esc_html($desc); ?></p>
                    <div class="pt-6 border-t border-white/5 flex items-center justify-between">
                        <div class="flex flex-col">
                            <span class="text-[10px] text-slate-500 uppercase font-black tracking-widest"><?php echo esc_html($install); ?></span>
                            <span class="text-xl font-black text-white"><?php echo esc_html($price); ?></span>
                        </div>
                        <a href="<?php echo esc_url($link); ?>" class="bg-primary hover:bg-primary/90 text-white text-xs font-black px-6 py-3 rounded-lg uppercase tracking-widest transition-all">Ver Detalhes</a>
                    </div>
                </div>
            </div>
            <?php endfor; ?>
        </div>
    </div>
</section>
