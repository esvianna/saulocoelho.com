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
<?php 
$wa_enable = get_theme_mod( 'whatsapp_enable', false );
$wa_phone = get_theme_mod( 'whatsapp_phone', '' );
$wa_msg = get_theme_mod( 'whatsapp_message', 'Olá! Gostaria de saber mais sobre os seus serviços.' );

if ( $wa_enable && ! empty( $wa_phone ) ) : 
    $wa_url = 'https://api.whatsapp.com/send?phone=55' . preg_replace('/\D/', '', $wa_phone) . '&text=' . urlencode( $wa_msg );
?>
    <a href="<?php echo esc_url( $wa_url ); ?>" target="_blank" id="whatsapp-float" class="fixed bottom-[6.5rem] right-8 z-[90] w-14 h-14 rounded-full bg-[#25D366] text-white flex items-center justify-center shadow-[0_10px_20px_rgba(37,211,102,0.3)] transition-all duration-300 hover:scale-110 hover:-translate-y-1 hover:shadow-[0_15px_30px_rgba(37,211,102,0.4)] group" title="Falar com a Equipe">
        <svg viewBox="0 0 24 24" width="28" height="28" fill="currentColor"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766 0-3.18-2.586-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.522-2.961-2.638-.086-.115-.718-.954-.718-1.815 0-.861.452-1.281.612-1.441.16-.16.347-.2.463-.2h.334c.108 0 .252-.04.394.303l.473 1.157c.044.106.074.23.002.377-.071.148-.107.247-.213.372-.106.124-.22.277-.314.372-.105.105-.213.22-.093.426.12.206.534.882 1.146 1.428.788.701 1.447.918 1.653 1.02.206.103.328.087.45-.054.123-.141.52-.605.66-.813.14-.209.28-.175.474-.101l1.492.703c.194.095.324.143.372.226.048.082.048.476-.096.881zM12.004 2.013c4.502-.009 8.198 3.674 8.207 8.175.008 4.401-3.472 7.971-7.82 8.158l.001.001c-1.248.021-2.43-.238-3.474-.712l-4.144 1.09 1.114-4.068c-.628-1.127-.991-2.438-.988-3.834.01-4.504 3.702-8.211 8.104-8.2zm0 1.65c-3.51.008-6.383 2.881-6.391 6.39-.002 1.353.424 2.377 1.01 3.298l-.151.551 1.4-.368-.137.5c-.172.63-.441 1.611-.349 1.27l-.46 1.685 1.735-.456.327.204c.947.591 1.986.938 3.109.921 3.427-.08 6.257-2.848 6.251-6.275-.006-3.421-2.775-6.195-6.194-6.23z"/></svg>
        
        <span class="absolute right-full mr-4 bg-white text-slate-800 text-[10px] font-black uppercase tracking-widest px-3 py-1.5 rounded-lg shadow-xl opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap">
            Falar com a Equipe
        </span>
    </a>
<?php endif; ?>

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
