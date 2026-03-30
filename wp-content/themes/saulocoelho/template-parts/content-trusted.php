<?php
$post_id = get_the_ID();
$trusted_label = get_post_meta($post_id, 'trusted_label', true) ?: 'Empresas que confiam no nosso trabalho';
?>
<!-- Trusted By Section -->
<section class="py-24 border-y border-white/5 bg-background-dark/30">
    <div class="max-w-7xl mx-auto px-6">
        <h4 class="text-slate-500 text-xs font-bold uppercase tracking-[0.4em] text-center mb-16"><?php echo esc_html($trusted_label); ?></h4>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-12 items-center transition-all duration-500">
            <?php
            $has_logos = false;
            for ($i = 1; $i <= 6; $i++) {
                $logo_url = get_post_meta($post_id, 'trusted_image_' . $i, true);
                if ($logo_url) {
                    $has_logos = true;
                    echo '<div class="flex justify-center items-center h-20 md:h-24"><img src="' . esc_url($logo_url) . '" alt="Logo Empresa ' . $i . '" class="max-h-12 md:max-h-16 w-auto object-contain brightness-0 invert opacity-40 hover:opacity-100 transition-opacity duration-300"></div>';
                }
            }
            
            if (!$has_logos): ?>
                <!-- Fallback Text Logos -->
                <div class="flex justify-center h-12 items-center text-white font-black text-xl tracking-[0.2em]">TECHCORP</div>
                <div class="flex justify-center h-12 items-center text-white font-black text-xl tracking-[0.2em]">GLOBALINC</div>
                <div class="flex justify-center h-12 items-center text-white font-black text-xl tracking-[0.2em]">INNOVATE</div>
                <div class="flex justify-center h-12 items-center text-white font-black text-xl tracking-[0.2em]">NEXUS</div>
                <div class="flex justify-center h-12 items-center text-white font-black text-xl tracking-[0.2em]">SYNERGY</div>
                <div class="flex justify-center h-12 items-center text-white font-black text-xl tracking-[0.2em]">ACME INC</div>
            <?php endif; ?>
        </div>
    </div>
</section>
