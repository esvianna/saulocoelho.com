<?php
/**
 * Template Name: Catalog Page
 */
get_header(); ?>

<?php
$post_id = get_the_ID();
$prog_title_1 = get_post_meta($post_id, 'programs_title_1', true) ?: 'Catálogo de';
$prog_title_2 = get_post_meta($post_id, 'programs_title_2', true) ?: 'Programas e Mentoria';
$prog_desc = get_post_meta($post_id, 'programs_description', true) ?: 'Metodologias exclusivas desenhadas para profissionais que buscam excelência operacional e liderança estratégica.';
?>

<main class="bg-background-dark-alt text-white min-h-screen">
    <div class="max-w-7xl mx-auto px-6 py-24 lg:px-10 mt-10">
        <!-- Page Header -->
        <div class="mb-16 flex flex-col gap-6 max-w-3xl">
            <div class="flex items-center gap-2 text-primary font-bold text-xs tracking-[0.3em] uppercase">
                <span class="h-px w-8 bg-primary"></span>
                Nossas Formações
            </div>
            <h1 class="text-4xl md:text-6xl font-black leading-[1.1] tracking-tight text-white">
                <?php echo esc_html($prog_title_1); ?> <span class="text-primary"><?php echo esc_html($prog_title_2); ?></span>
            </h1>
            <p class="text-lg text-slate-400 font-light leading-relaxed">
                <?php echo wp_kses_post($prog_desc); ?>
            </p>
        </div>

        <!-- Grouping Logic -->
        <?php 
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
        $open_prods = [];
        $wait_prods = [];

        if ($products->have_posts()) :
            while ($products->have_posts()) : $products->the_post();
                global $product;
                $pid = get_the_ID();
                $status = get_post_meta($pid, 'course_sale_status', true) ?: 'aberto';
                
                $data = [
                    'id'    => $pid,
                    'title' => get_the_title(),
                    'img'   => get_the_post_thumbnail_url($pid, 'large'),
                    'desc'  => $product->get_short_description() ?: get_post_meta($pid, 'course_badge', true) ?: 'Programa exclusivo.',
                    'link'  => get_permalink($pid),
                    'waitlist_btn' => get_post_meta($pid, 'course_waitlist_btn', true) ?: 'Saiba Mais / Lista de Espera',
                ];

                if ($status === 'aberto') {
                    $open_prods[] = $data;
                } else {
                    $wait_prods[] = $data;
                }
            endwhile;
            wp_reset_postdata();
        endif;
        ?>

        <!-- Seção 1: Inscrições Abertas -->
        <?php if (!empty($open_prods)) : ?>
        <div class="mb-16">
            <div class="flex items-center gap-4 mb-8">
                <h2 class="text-2xl font-black text-white uppercase tracking-wider">Inscrições Abertas</h2>
                <div class="h-px flex-1 bg-white/10"></div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($open_prods as $p) : ?>
                <div class="group flex flex-col bg-slate-900/40 rounded-3xl border border-white/5 overflow-hidden hover:bg-slate-900/60 transition-all hover:border-primary/50">
                    <div class="aspect-[3/4] relative overflow-hidden bg-slate-800">
                        <div class="absolute top-4 left-4 z-20">
                            <span class="bg-primary text-white text-[9px] font-black uppercase tracking-widest px-3 py-1.5 rounded-full shadow-lg flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></span>
                                Inscrições Abertas
                            </span>
                        </div>

                        <?php if ($p['img']) : ?>
                            <img src="<?php echo esc_url($p['img']); ?>" alt="<?php echo esc_attr($p['title']); ?>" class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110">
                        <?php else : ?>
                            <div class="w-full h-full flex items-center justify-center opacity-20">
                                <span class="material-symbols-outlined text-6xl">school</span>
                            </div>
                        <?php endif; ?>
                        <div class="absolute inset-0 bg-gradient-to-t from-background-dark via-background-dark/20 to-transparent"></div>
                    </div>
                    
                    <div class="p-8 flex flex-col flex-1">
                        <h3 class="text-2xl font-black text-white mb-3 tracking-tighter group-hover:text-primary transition-colors"><?php echo esc_html($p['title']); ?></h3>
                        <p class="text-[11px] text-slate-400 mb-8 font-light leading-relaxed line-clamp-2 italic tracking-wide uppercase"><?php echo wp_strip_all_tags($p['desc']); ?></p>
                        
                        <div class="mt-auto pt-6 border-t border-white/5">
                            <a href="<?php echo esc_url($p['link']); ?>" class="inline-flex items-center gap-2 text-primary text-[10px] font-black uppercase tracking-[0.2em] hover:gap-4 transition-all">
                                Quero Me Inscrever <span class="material-symbols-outlined text-xs">arrow_forward</span>
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Seção 2: Próximas Turmas / Lista de Espera -->
        <?php if (!empty($wait_prods)) : ?>
        <div class="mt-24">
            <div class="flex items-center gap-4 mb-8">
                <h2 class="text-2xl font-black text-slate-400 uppercase tracking-wider opacity-60">Próximas Turmas / Lista de Espera</h2>
                <div class="h-px flex-1 bg-white/5"></div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <?php foreach ($wait_prods as $p) : ?>
                <div class="group flex flex-col bg-white/[0.03] backdrop-blur-md rounded-3xl border border-white/5 overflow-hidden transition-all opacity-80 hover:opacity-100">
                    <div class="aspect-[3/4] relative overflow-hidden bg-slate-800">
                        <div class="absolute top-4 left-4 z-20">
                            <span class="bg-slate-800/90 text-slate-300 text-[9px] font-black uppercase tracking-widest px-3 py-1.5 rounded-full backdrop-blur-md border border-white/10">
                                Em Breve
                            </span>
                        </div>

                        <?php if ($p['img']) : ?>
                            <img src="<?php echo esc_url($p['img']); ?>" alt="<?php echo esc_attr($p['title']); ?>" class="w-full h-full object-cover grayscale-[0.5] opacity-60 hover:grayscale-0 hover:opacity-100 transition-all duration-700">
                        <?php else : ?>
                            <div class="w-full h-full flex items-center justify-center opacity-20">
                                <span class="material-symbols-outlined text-6xl">school</span>
                            </div>
                        <?php endif; ?>
                        <div class="absolute inset-0 bg-gradient-to-t from-background-dark via-background-dark/20 to-transparent"></div>
                    </div>
                    
                    <div class="p-6 flex flex-col flex-1">
                        <h3 class="text-xl font-black text-white/80 mb-2 tracking-tighter"><?php echo esc_html($p['title']); ?></h3>
                        <p class="text-[10px] text-slate-500 mb-6 font-light leading-relaxed line-clamp-2 italic uppercase"><?php echo wp_strip_all_tags($p['desc']); ?></p>
                        
                        <div class="mt-auto pt-4 border-t border-white/5">
                            <a href="<?php echo esc_url($p['link']); ?>" class="inline-flex items-center gap-2 text-primary text-[10px] font-black uppercase tracking-[0.2em] hover:gap-4 transition-all">
                                <?php echo esc_html($p['waitlist_btn']); ?> <span class="material-symbols-outlined text-xs">arrow_forward</span>
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if (empty($open_prods) && empty($wait_prods)) : ?>
            <p class="text-center text-slate-500 py-12">Nenhum programa encontrado nestas categorias.</p>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>
