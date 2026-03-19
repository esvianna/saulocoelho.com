<?php
/**
 * Template part for displaying the hero section
 */
$post_id = get_the_ID();
$hero_eyebrow = get_post_meta($post_id, 'hero_eyebrow', true) ?: 'Alta Performance & Liderança';
$hero_title = get_post_meta($post_id, 'hero_title', true) ?: 'Desperte o seu <br/> <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary to-blue-400">potencial máximo</span>';
$hero_description = get_post_meta($post_id, 'hero_description', true) ?: 'Transforme sua trajetória com as metodologias aplicadas por Saulo Coelho nos maiores ecossistemas corporativos do Brasil.';
$hero_bg_image = get_post_meta($post_id, 'hero_bg_image', true) ?: 'https://sccr.com.br/wp-content/uploads/2025/05/WhatsApp-Image-2025-05-07-at-12.43.04-1.jpeg';
?>
<!-- Hero Section -->
<section class="relative min-h-screen flex items-start pt-48 pb-32 overflow-hidden bg-cover bg-no-repeat bg-right md:bg-[right_-200px_top_80px] lg:bg-[right_top_80px]" style="background-image: url('<?php echo esc_url($hero_bg_image); ?>');">
    <!-- Gradient Overlay for better text legibility on the left -->
    <div class="absolute inset-0 bg-gradient-to-r from-background-dark-alt via-background-dark-alt/60 40% to-transparent"></div>

    <div class="w-full max-w-7xl mx-auto px-6 relative z-10">
        <div class="max-w-2xl space-y-8 animate-fade-in">
            <div class="space-y-4">
                <span class="text-primary font-bold tracking-[0.3em] uppercase text-sm block"><?php echo esc_html($hero_eyebrow); ?></span>
                <h1 class="text-6xl md:text-8xl font-black leading-[1.05] tracking-tight text-white uppercase italic">
                    <?php echo $hero_title; ?>
                </h1>
                <p class="text-lg md:text-xl text-slate-300 max-w-lg leading-relaxed font-light">
                    <?php echo esc_html($hero_description); ?>
                </p>
            </div>
            <div class="flex flex-col sm:flex-row gap-6 pt-4">
                <a href="#programas" class="bg-primary text-white font-black uppercase tracking-widest px-10 py-5 rounded-xl text-sm hover:scale-105 hover:shadow-2xl shadow-primary/20 active:scale-95 transition-all text-center">
                    Conheça os Programas
                </a>
                <a href="#contato" class="border-2 border-white/20 hover:border-white/40 hover:bg-white/5 text-white font-black uppercase tracking-widest px-10 py-5 rounded-xl text-sm transition-all text-center">
                    Falar com Consultor
                </a>
            </div>
        </div>
    </div>
    
    <!-- Decorative Glow at bottom -->
    <div class="absolute bottom-0 left-0 w-full h-32 bg-gradient-to-t from-background-dark-alt to-transparent z-1 pointer-events-none"></div>
</section>
