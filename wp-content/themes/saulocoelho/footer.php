<?php
$sc_is_portal = function_exists( 'saulocoelho_is_portal_aluno' ) && saulocoelho_is_portal_aluno();
if ( ! $sc_is_portal ) :

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

<footer class="sc-site-footer-marketing py-20 bg-background-dark-alt border-t border-white/5">
    <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-4 gap-12">
        <div class="col-span-1 md:col-span-2 space-y-6">
            <div class="flex items-center gap-3">
                <div class="text-primary">
                    <span class="material-symbols-outlined text-2xl">terminal</span>
                </div>
                <h2 class="font-display text-lg font-black tracking-tighter uppercase text-white"><?php bloginfo( 'name' ); ?></h2>
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
            <?php
            if ( ! $footer_privacy || $footer_privacy === '#' ) {
                $footer_privacy = function_exists( 'saulocoelho_privacy_url' ) ? saulocoelho_privacy_url() : home_url( '/privacidade/' );
            }
            ?>
            <a href="<?php echo esc_url( $footer_privacy ); ?>" class="hover:text-slate-400 transition-colors">Privacidade</a>

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
    <a href="<?php echo esc_url( $wa_url ); ?>" target="_blank" rel="noopener noreferrer" id="whatsapp-float" class="fixed bottom-[6.5rem] right-8 z-[90] w-[60px] h-[60px] rounded-full bg-[#25D366] text-white flex items-center justify-center transition-transform duration-300 group" aria-label="Fale comigo! Contato do WhatsApp">
        <svg viewBox="0 0 24 24" width="34" height="34" fill="currentColor" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/></svg>

        <span class="wa-tooltip" aria-hidden="true">Fale comigo!</span>
    </a>
    <script>
        setTimeout(function () {
            var wa = document.getElementById('whatsapp-float');
            if (!wa) return;
            wa.classList.add('wa-tooltip-show');
            setTimeout(function () { wa.classList.remove('wa-tooltip-show'); }, 5000);
        }, 3000);
    </script>
<?php endif; ?>

<button id="back-to-top" class="fixed bottom-8 right-8 z-[90] w-12 h-12 rounded-2xl bg-background-dark-alt/40 backdrop-blur-xl border border-white/10 text-slate-400 flex items-center justify-center transition-all duration-500 opacity-0 transform translate-y-10 pointer-events-none hover:border-primary hover:text-primary hover:-translate-y-1 hover:shadow-[0_10px_30px_rgba(197,160,89,0.3)] group" title="Voltar ao Topo">
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

<?php endif; // ! portal ?>

<?php wp_footer(); ?>
</body>
</html>
