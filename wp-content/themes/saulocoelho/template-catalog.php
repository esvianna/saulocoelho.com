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

        <!-- Program Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
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

            if ($products->have_posts()) :
                while ($products->have_posts()) : $products->the_post();
                    global $product;
                    $id = get_the_ID();
                    $img = get_the_post_thumbnail_url($id, 'large');
                    $desc = $product->get_short_description() ?: get_post_meta($id, 'course_badge', true) ?: 'Programa exclusivo.';
                    $link = get_permalink();
                    $title = get_the_title();
            ?>
            <div class="group flex flex-col bg-slate-900/40 rounded-2xl border border-white/5 overflow-hidden hover:bg-slate-900/60 transition-all hover:border-primary/50">
                <div class="aspect-[3/4] relative overflow-hidden bg-slate-800">
                    <?php if ($img) : ?>
                        <img src="<?php echo esc_url($img); ?>" alt="<?php echo esc_attr($title); ?>" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                    <?php else : ?>
                        <div class="w-full h-full flex items-center justify-center opacity-20">
                            <span class="material-symbols-outlined text-6xl">school</span>
                        </div>
                    <?php endif; ?>
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/20 to-transparent"></div>
                </div>
                
                <div class="p-6 flex flex-col flex-1">
                    <h3 class="text-xl font-bold text-white mb-2 group-hover:text-primary transition-colors"><?php echo esc_html($title); ?></h3>
                    <p class="text-xs text-slate-400 mb-6 font-light leading-relaxed line-clamp-2 italic"><?php echo wp_strip_all_tags($desc); ?></p>
                    
                    <div class="mt-auto pt-4 border-t border-white/5">
                        <a href="<?php echo esc_url($link); ?>" class="inline-flex items-center gap-2 text-primary text-[10px] font-black uppercase tracking-widest hover:gap-3 transition-all">
                            Saiba Mais <span class="material-symbols-outlined text-xs">arrow_forward</span>
                        </a>
                    </div>
                </div>
            </div>
            <?php 
                endwhile;
                wp_reset_postdata();
            else :
                echo '<p class="col-span-full text-center text-slate-500 py-12">Nenhum programa encontrado nestas categorias.</p>';
            endif;
            ?>
        </div>
    </div>
</main>

<?php get_footer(); ?>
