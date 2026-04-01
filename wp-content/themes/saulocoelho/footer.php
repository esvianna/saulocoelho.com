<?php
$footer_bio = get_theme_mod( 'footer_bio', 'Saulo Coelho - Especialista em Desenvolvimento Humano e Estratégia Corporativa.' );
$footer_phone = get_theme_mod( 'footer_phone', '+55 (11) 99999-9999' );
$footer_location = get_theme_mod( 'footer_location', 'São Paulo, SP' );
$footer_instagram = get_theme_mod( 'footer_social_instagram', '#' );
$footer_linkedin = get_theme_mod( 'footer_social_linkedin', '#' );
$footer_email = get_theme_mod( 'footer_email', 'contato@saulocoelho.com.br' );
$footer_copyright = get_theme_mod( 'footer_copyright', 'Saulo Coelho. Todos os direitos reservados.' );
$footer_privacy = get_theme_mod( 'footer_privacy_link', '#' );
$footer_terms = get_theme_mod( 'footer_terms_link', '#' );
?>

<footer class="py-20 bg-background-dark-alt border-t border-white/5">
    <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-4 gap-12">
        <div class="col-span-1 md:col-span-2 space-y-6">
            <div class="flex items-center gap-3">
                <div class="text-primary">
                    <span class="material-symbols-outlined text-2xl">terminal</span>
                </div>
                <h2 class="text-lg font-black tracking-tighter uppercase text-white"><?php bloginfo( 'name' ); ?></h2>
            </div>
            
            <?php if ( $footer_bio ) : ?>
                <p class="text-slate-500 max-w-xs text-sm font-light leading-relaxed"><?php echo wp_kses_post( $footer_bio ); ?></p>
            <?php endif; ?>

            <div class="flex gap-4">
                <?php if ( $footer_instagram && $footer_instagram !== '#' ) : ?>
                    <a href="<?php echo esc_url( $footer_instagram ); ?>" target="_blank" class="w-12 h-12 rounded-full border border-white/10 flex items-center justify-center text-slate-400 hover:text-white hover:bg-white/5 hover:border-white/20 backdrop-blur-sm transition-all" title="Instagram">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-instagram"><rect width="20" height="20" x="2" y="2" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" x2="17.51" y1="6.5" y2="6.5"/></svg>
                    </a>
                <?php endif; ?>

                <?php if ( $footer_linkedin && $footer_linkedin !== '#' ) : ?>
                    <a href="<?php echo esc_url( $footer_linkedin ); ?>" target="_blank" class="w-12 h-12 rounded-full border border-white/10 flex items-center justify-center text-slate-400 hover:text-white hover:bg-white/5 hover:border-white/20 backdrop-blur-sm transition-all" title="LinkedIn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-linkedin"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect width="4" height="12" x="2" y="9"/><circle cx="4" cy="4" r="2"/></svg>
                    </a>
                <?php endif; ?>

                <?php if ( $footer_email ) : ?>
                    <a href="mailto:<?php echo esc_attr( $footer_email ); ?>" class="w-12 h-12 rounded-full border border-white/10 flex items-center justify-center text-slate-400 hover:text-white hover:bg-white/5 hover:border-white/20 backdrop-blur-sm transition-all" title="E-mail">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-mail"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="space-y-6">
            <h4 class="text-white text-[10px] font-black uppercase tracking-[0.3em]">Navegação</h4>
            <?php
            if ( has_nav_menu( 'footer-menu' ) ) {
                wp_nav_menu( array(
                    'theme_location' => 'footer-menu',
                    'container'      => false,
                    'menu_class'     => 'space-y-4 text-sm text-slate-400',
                    'fallback_cb'    => false,
                    'items_wrap'     => '<ul id="%1$s" class="%2$s">%3$s</ul>',
                ) );
            } else {
                echo '<p class="text-slate-600 text-xs italic">Defina o menu no painel.</p>';
            }
            ?>
        </div>

        <div class="space-y-6">
            <h4 class="text-white text-[10px] font-black uppercase tracking-[0.3em]">Contato</h4>
            <ul class="space-y-4 text-sm text-slate-400">
                <?php if ( $footer_phone ) : ?>
                    <li class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-primary text-lg">call</span>
                        <?php echo esc_html( $footer_phone ); ?>
                    </li>
                <?php endif; ?>
                
                <?php if ( $footer_location ) : ?>
                    <li class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-primary text-lg">location_on</span>
                        <?php echo esc_html( $footer_location ); ?>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-6 pt-20 mt-20 border-t border-white/5 flex flex-col md:flex-row justify-between items-center gap-4 text-xs text-slate-600 uppercase tracking-widest">
        <p>© <?php echo date('Y'); ?> <?php echo esc_html( $footer_copyright ); ?></p>
        <div class="flex gap-8 text-[10px]">
            <?php if ( $footer_privacy && $footer_privacy !== '#' ) : ?>
                <a href="<?php echo esc_url( $footer_privacy ); ?>" class="hover:text-slate-400 transition-colors">Privacidade</a>
            <?php endif; ?>

            <?php if ( $footer_terms && $footer_terms !== '#' ) : ?>
                <a href="<?php echo esc_url( $footer_terms ); ?>" class="hover:text-slate-400 transition-colors">Termos</a>
            <?php endif; ?>
        </div>
    </div>
</footer>

<!-- Back to Top Button -->
<button id="back-to-top" class="fixed bottom-8 right-8 z-[90] w-12 h-12 rounded-2xl bg-background-dark-alt/40 backdrop-blur-xl border border-white/10 text-slate-400 flex items-center justify-center transition-all duration-500 opacity-0 transform translate-y-10 pointer-events-none hover:border-primary hover:text-primary hover:-translate-y-1 hover:shadow-[0_10px_30px_rgba(19,127,236,0.3)] group" title="Voltar ao Topo">
    <div class="absolute inset-0 bg-primary/5 opacity-0 group-hover:opacity-100 transition-opacity rounded-2xl"></div>
    <span class="material-symbols-outlined text-2xl relative z-10 font-bold">north</span>
</button>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const backToTop = document.getElementById('back-to-top');
        
        window.addEventListener('scroll', () => {
            if (window.scrollY > 400) {
                backToTop.classList.remove('opacity-0', 'translate-y-10', 'pointer-events-none');
                backToTop.classList.add('opacity-100', 'translate-y-0', 'pointer-events-auto');
            } else {
                backToTop.classList.add('opacity-0', 'translate-y-10', 'pointer-events-none');
                backToTop.classList.remove('opacity-100', 'translate-y-0', 'pointer-events-auto');
            }
        });

        backToTop.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    });
</script>

<?php wp_footer(); ?>
</body>
</html>
