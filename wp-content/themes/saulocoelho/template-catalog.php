<?php
/**
 * Template Name: Catalog Page
 */
get_header(); ?>

<main class="mx-auto w-full max-w-7xl flex-1 px-6 py-12 lg:px-10 mt-20">
    <div class="mb-12 flex flex-col gap-4">
        <div class="flex items-center gap-2 text-primary font-semibold text-sm tracking-wider uppercase">
            <span class="h-px w-8 bg-primary"></span>
            Catálogo de Formação
        </div>
        <h2 class="max-w-2xl text-4xl font-black leading-tight tracking-tight text-slate-900 dark:text-white md:text-5xl">
            Programas de <span class="text-primary">Treinamento</span> e Mentoria
        </h2>
        <p class="max-w-xl text-lg text-slate-600 dark:text-slate-400">
            Metodologias exclusivas desenhadas para profissionais que buscam excelência operacional e liderança estratégica.
        </p>
    </div>

    <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        <?php
        $courses = array(
            array( "title" => "Mentoria Business Elite", "desc" => "Desenvolva uma visão estratégica de alto nível e domine o mercado corporativo.", "img" => "https://images.unsplash.com/photo-1542744173-8e7e53415bb0?auto=format&fit=crop&w=400&q=80" ),
            array( "title" => "Formação de Líderes", "desc" => "Liderança moderna focada em gestão de pessoas e resultados exponenciais.", "img" => "https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&w=400&q=80" ),
            array( "title" => "Estratégia Avançada", "desc" => "Metodologias práticas e frameworks validados para o crescimento acelerado.", "img" => "https://images.unsplash.com/photo-1553877522-43269d4ea984?auto=format&fit=crop&w=400&q=80" ),
            array( "title" => "Imersão Comercial", "desc" => "Técnicas de vendas consultivas e negociação de elite para fechar grandes contratos.", "img" => "https://images.unsplash.com/photo-1556761175-5973dc0f32e7?auto=format&fit=crop&w=400&q=80" ),
        );

        foreach ($courses as $course) : ?>
            <div class="group flex flex-col overflow-hidden rounded-xl border border-slate-200 bg-white transition-all hover:shadow-xl dark:border-slate-800 dark:bg-slate-900/50">
                <div class="aspect-[3/4] overflow-hidden bg-slate-100 dark:bg-slate-800">
                    <img alt="<?php echo esc_attr($course['title']); ?>" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110" src="<?php echo esc_url($course['img']); ?>"/>
                </div>
                <div class="flex flex-1 flex-col p-6">
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white"><?php echo esc_html($course['title']); ?></h3>
                    <p class="mt-2 flex-1 text-sm leading-relaxed text-slate-600 dark:text-slate-400">
                        <?php echo esc_html($course['desc']); ?>
                    </p>
                    <a href="#" class="mt-6 flex items-center justify-between text-sm font-bold uppercase tracking-wider text-primary group-hover:gap-2 transition-all">
                        Saiba Mais
                        <span class="material-symbols-outlined text-lg">arrow_forward</span>
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</main>

<?php get_footer(); ?>
