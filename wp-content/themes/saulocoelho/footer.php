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
                        <span class="material-symbols-outlined text-sm">share</span>
                    </a>
                <?php endif; ?>

                <?php if ( $footer_linkedin && $footer_linkedin !== '#' ) : ?>
                    <a href="<?php echo esc_url( $footer_linkedin ); ?>" target="_blank" class="w-12 h-12 rounded-full border border-white/10 flex items-center justify-center text-slate-400 hover:text-white hover:bg-white/5 hover:border-white/20 backdrop-blur-sm transition-all" title="LinkedIn">
                        <span class="material-symbols-outlined text-sm">group</span>
                    </a>
                <?php endif; ?>

                <?php if ( $footer_email ) : ?>
                    <a href="mailto:<?php echo esc_attr( $footer_email ); ?>" class="w-12 h-12 rounded-full border border-white/10 flex items-center justify-center text-slate-400 hover:text-white hover:bg-white/5 hover:border-white/20 backdrop-blur-sm transition-all" title="E-mail">
                        <span class="material-symbols-outlined text-sm">mail</span>
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

<?php wp_footer(); ?>
</body>
</html>
