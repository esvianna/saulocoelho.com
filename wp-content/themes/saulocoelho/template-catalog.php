<?php
/**
 * Template Name: Catalog Page
 */
get_header(); ?>

<main class="bg-background-dark-alt text-white min-h-screen">
    <div class="max-w-7xl mx-auto px-6 py-12 lg:px-10 mt-20">
        <div class="mb-12 flex flex-col gap-4">
            <div class="flex items-center gap-2 text-primary font-semibold text-sm tracking-wider uppercase">
                <span class="h-px w-8 bg-primary"></span>
                Catálogo de Formação
            </div>
            <h2 class="max-w-2xl text-4xl font-black leading-tight tracking-tight text-white md:text-5xl">
                Programas de <span class="text-primary">Treinamento</span> e Mentoria
            </h2>
            <p class="max-w-xl text-lg text-slate-400">
                Metodologias exclusivas desenhadas para profissionais que buscam excelência operacional e liderança estratégica.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
        <?php
        $courses = array(
            array( "title" => "Mentoria Business Elite", "desc" => "Desenvolva uma visão estratégica de alto nível e domine o mercado corporativo.", "img" => "https://images.unsplash.com/photo-1542744173-8e7e53415bb0?auto=format&fit=crop&w=400&q=80" ),
            array( "title" => "Formação de Líderes", "desc" => "Liderança moderna focada em gestão de pessoas e resultados exponenciais.", "img" => "https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&w=400&q=80" ),
            array( "title" => "Estratégia Avançada", "desc" => "Metodologias práticas e frameworks validados para o crescimento acelerado.", "img" => "https://images.unsplash.com/photo-1553877522-43269d4ea984?auto=format&fit=crop&w=400&q=80" ),
            array( "title" => "Imersão Comercial", "desc" => "Técnicas de vendas consultivas e negociação de elite para fechar grandes contratos.", "img" => "https://images.unsplash.com/photo-1556761175-5973dc0f32e7?auto=format&fit=crop&w=400&q=80" ),
        );

        foreach ($courses as $course) : ?>
            <div class="group relative flex flex-col bg-slate-800/40 rounded-2xl border border-white/5 p-8 hover:bg-slate-800/60 transition-all hover:border-primary/50">
                <div class="size-16 rounded-xl bg-primary/10 flex items-center justify-center mb-8 group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-primary text-3xl">school</span>
                </div>
                <div class="flex flex-1 flex-col">
                    <h3 class="text-xl font-bold text-white mb-4"><?php echo esc_html($course['title']); ?></h3>
                    <p class="text-sm text-slate-400 mb-8 font-light leading-relaxed">
                        <?php echo esc_html($course['desc']); ?>
                    </p>
                    <div class="mt-auto pt-6 border-t border-white/5">
                        <a href="#" class="inline-flex items-center gap-2 text-primary text-xs font-bold uppercase tracking-widest hover:gap-3 transition-all">
                            Saiba Mais <span class="material-symbols-outlined text-sm">arrow_forward</span>
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</main>

<?php get_footer(); ?>
