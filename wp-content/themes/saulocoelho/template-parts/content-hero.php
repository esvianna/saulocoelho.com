<?php
/**
 * Template part for displaying the hero section
 */
$post_id = get_the_ID();
$hero_eyebrow = get_post_meta($post_id, 'hero_eyebrow', true) ?: 'Alta Performance & Liderança';
$hero_title = get_post_meta($post_id, 'hero_title', true) ?: 'Desperte o seu <br/> <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary to-blue-400">potencial máximo</span>';
$hero_description = get_post_meta($post_id, 'hero_description', true) ?: 'Transforme sua trajetória com as metodologias aplicadas por Saulo Coelho nos maiores ecossistemas corporativos do Brasil.';
$hero_bg_image = get_post_meta($post_id, 'hero_bg_image', true) ?: 'https://sccr.com.br/wp-content/uploads/2025/05/WhatsApp-Image-2025-05-07-at-12.43.04-1.jpeg';
$hero_btn_1_text = get_post_meta($post_id, 'hero_btn_1_text', true) ?: 'Conheça os Programas';
$hero_btn_1_link = get_post_meta($post_id, 'hero_btn_1_link', true) ?: '#programas';
$hero_btn_2_text = get_post_meta($post_id, 'hero_btn_2_text', true) ?: 'Falar com Consultor';
$hero_btn_2_link = get_post_meta($post_id, 'hero_btn_2_link', true) ?: '#contato';
?>
<!-- Hero Section -->
<section class="relative min-h-screen flex items-start pt-48 pb-32 overflow-hidden bg-cover bg-no-repeat bg-right md:bg-[right_-200px_top_80px] lg:bg-[right_top_80px]" style="background-image: url('<?php echo esc_url($hero_bg_image); ?>');">
    <!-- Gradient Overlay for better text legibility on the left -->
    <div class="absolute inset-0 bg-gradient-to-r from-background-dark-alt via-background-dark-alt/60 40% to-transparent"></div>
    <!-- Subtle Blur Overlay -->
    <div class="absolute inset-0 bg-background-dark/60 backdrop-blur-[2px]"></div>
    <!-- Focus Mask (Radial Gradient) -->
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,transparent_0%,rgba(10,17,24,0.8)_80%)]"></div>

    <div class="w-full max-w-7xl mx-auto px-6 relative z-10">
        <div class="max-w-2xl space-y-8 animate-fade-in">
            <div class="space-y-4">
                <span class="text-primary font-bold tracking-[0.3em] uppercase text-sm block"><?php echo esc_html($hero_eyebrow); ?></span>
                <h1 class="text-4xl md:text-6xl font-black leading-[1.1] tracking-tight text-white">
                    <?php echo $hero_title; ?>
                </h1>
                <p class="text-lg md:text-xl text-slate-300 max-w-lg leading-relaxed font-light">
                    <?php echo esc_html($hero_description); ?>
                </p>
            </div>
            <div class="flex flex-col sm:flex-row gap-6 pt-4">
                <a href="<?php echo esc_url($hero_btn_1_link); ?>" class="bg-primary hover:bg-primary/90 text-white px-10 py-5 rounded-2xl text-sm font-black uppercase tracking-widest shadow-2xl shadow-primary/40 transition-all hover:-translate-y-1">
                    <?php echo esc_html($hero_btn_1_text); ?>
                </a>
                <a href="<?php echo esc_url($hero_btn_2_link); ?>" class="bg-white/5 hover:bg-white/10 text-white px-10 py-5 rounded-2xl text-sm font-black uppercase tracking-widest border border-white/10 backdrop-blur-md transition-all hover:-translate-y-1">
                    <?php echo esc_html($hero_btn_2_text); ?>
                </a>
            </div>
        </div>
    </div>
    
    <!-- Decorative Glow at bottom -->
    <div class="absolute bottom-0 left-0 w-full h-32 bg-gradient-to-t from-background-dark-alt to-transparent z-1 pointer-events-none"></div>
</section>
