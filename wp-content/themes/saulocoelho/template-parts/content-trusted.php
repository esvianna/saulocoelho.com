<?php
$post_id = get_the_ID();
$trusted_label = get_post_meta($post_id, 'trusted_label', true) ?: 'Empresas que confiam no nosso trabalho';
?>
<!-- Trusted By Section -->
<section class="py-24 border-y border-white/5 bg-background-dark/30">
    <div class="max-w-7xl mx-auto px-6">
        <h4 class="text-slate-500 text-xs font-bold uppercase tracking-[0.4em] text-center mb-16"><?php echo esc_html($trusted_label); ?></h4>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-12 items-center opacity-30 grayscale hover:grayscale-0 transition-all duration-500">
            <!-- Logos placeholders from mockup - Replace with real SVG logos -->
            <div class="flex justify-center h-12 text-white font-black text-xl tracking-[0.2em]">TECHCORP</div>
            <div class="flex justify-center h-12 text-white font-black text-xl tracking-[0.2em]">GLOBALINC</div>
            <div class="flex justify-center h-12 text-white font-black text-xl tracking-[0.2em]">INNOVATE</div>
            <div class="flex justify-center h-12 text-white font-black text-xl tracking-[0.2em]">NEXUS</div>
            <div class="flex justify-center h-12 text-white font-black text-xl tracking-[0.2em]">SYNERGY</div>
        </div>
    </div>
</section>
