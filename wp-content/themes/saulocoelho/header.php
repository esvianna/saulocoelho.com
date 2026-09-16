<!DOCTYPE html>
<html <?php language_attributes(); ?> class="dark">
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
    <!-- Temporary Tailwind CDN for layout fix -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = {
        darkMode: 'class',
        theme: {
          extend: {
            colors: {
              "primary": "#C5A059",
              "primary-light": "#D4AF37",
              "primary-dark": "#A6894A",
              "background-light": "#f6f7f8",
              "background-dark": "#050A14",
              "background-dark-alt": "#0A0E1A"
            },
            fontFamily: {
              "display": ["Playfair Display", "Georgia", "serif"],
              "sans": ["Inter", "sans-serif"]
            }
          }
        }
      }
    </script>
    <style>
        /* Tipografia — Playfair Display nos títulos, Inter no corpo */
        h1, h2, h3, h4, h5, h6, .font-display {
            font-family: 'Playfair Display', Georgia, serif;
        }
        body {
            font-family: 'Inter', sans-serif;
        }

        /* Linhas decorativas douradas (referência Carta Pública) */
        .sc-deco-corner {
            pointer-events: none;
            position: fixed;
            z-index: 2;
            width: 140px;
            height: 140px;
            opacity: 0.45;
        }
        .sc-deco-corner span {
            position: absolute;
            display: block;
            height: 1px;
            width: 90px;
            background: linear-gradient(90deg, transparent, #C5A059, #D4AF37);
        }
        .sc-deco-corner--tr { top: 5.5rem; right: 0; }
        .sc-deco-corner--tr span:nth-child(1) { top: 20px; right: 24px; transform: rotate(-45deg); }
        .sc-deco-corner--tr span:nth-child(2) { top: 32px; right: 24px; transform: rotate(-45deg); width: 70px; opacity: 0.7; }
        .sc-deco-corner--tr span:nth-child(3) { top: 44px; right: 24px; transform: rotate(-45deg); width: 50px; opacity: 0.5; }
        .sc-deco-corner--bl { bottom: 24px; left: 0; }
        .sc-deco-corner--bl span:nth-child(1) { bottom: 20px; left: 24px; transform: rotate(-45deg); }
        .sc-deco-corner--bl span:nth-child(2) { bottom: 32px; left: 24px; transform: rotate(-45deg); width: 70px; opacity: 0.7; }
        .sc-deco-corner--bl span:nth-child(3) { bottom: 44px; left: 24px; transform: rotate(-45deg); width: 50px; opacity: 0.5; }
        @media (max-width: 767px) {
            .sc-deco-corner { opacity: 0.25; width: 80px; height: 80px; }
            .sc-deco-corner span { width: 50px; }
        }

        /* Typography and general links (Mobile & Desktop) */
        #main-nav li {
            list-style: none !important;
            margin: 0 !important;
            position: relative;
        }
        #main-nav a {
            font-weight: 700 !important;
            color: #ffffff !important;
            text-transform: uppercase !important;
            letter-spacing: 0.15em !important;
            text-decoration: none !important;
            transition: all 0.3s ease !important;
            display: flex !important;
            align-items: center !important;
            gap: 0.25rem !important;
        }
        #main-nav > ul > li > a:hover {
            color: #C5A059 !important;
        }

        /* Dropdown indicator (Desktop & Mobile) */
        #main-nav .menu-item-has-children > a::after {
            content: "\e313";
            font-family: 'Material Symbols Outlined' !important;
            font-weight: normal !important;
            font-size: 1.25rem !important;
            transition: transform 0.3s ease !important;
        }

        /* ================= Desktop Specific (lg) ================= */
        @media (min-width: 1024px) {
            /* Force horizontal layout only in desktop */
            #main-nav > ul {
                display: flex !important;
                align-items: center !important;
                gap: 2.5rem !important;
                list-style: none !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            #main-nav a {
                font-size: 0.825rem !important;
            }
            #main-nav .menu-item-has-children:hover > a::after {
                transform: rotate(-180deg) !important;
            }
            /* Desktop Dropdown Styles */
            #main-nav .sub-menu {
                position: absolute !important;
                top: 100% !important;
                left: 50% !important;
                transform: translateX(-50%) translateY(15px) !important;
                min-width: 14rem !important;
                background-color: rgba(5, 10, 20, 0.95) !important;
                backdrop-filter: blur(16px) !important;
                border: 1px solid rgba(255, 255, 255, 0.05) !important;
                border-radius: 0.75rem !important;
                padding: 0.5rem !important;
                opacity: 0 !important;
                visibility: hidden !important;
                transition: opacity 0.3s ease, transform 0.3s ease, visibility 0.3s ease !important;
                box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.5) !important;
                display: flex !important;
                flex-direction: column !important;
                gap: 0.25rem !important;
                z-index: 50 !important;
            }
            #main-nav .menu-item-has-children:hover > .sub-menu {
                opacity: 1 !important;
                visibility: visible !important;
                transform: translateX(-50%) translateY(0) !important;
            }
            #main-nav .sub-menu li {
                width: 100% !important;
            }
            #main-nav .sub-menu li a {
                padding: 0.75rem 1rem !important;
                border-radius: 0.5rem !important;
                font-size: 0.75rem !important;
                color: rgba(255,255,255,0.7) !important;
                justify-content: flex-start !important;
                width: 100% !important;
            }
            #main-nav .sub-menu li a:hover {
                background: rgba(255, 255, 255, 0.05) !important;
                color: #ffffff !important;
            }
            #main-nav .sub-menu .menu-item-has-children > a::after {
                transform: rotate(-90deg) !important;
                margin-left: auto !important;
            }
            #main-nav .sub-menu .menu-item-has-children > .sub-menu {
                left: 100% !important;
                top: 0 !important;
                transform: translateX(15px) translateY(0) !important;
            }
            #main-nav .sub-menu .menu-item-has-children:hover > .sub-menu {
                transform: translateX(0) translateY(0) !important;
            }
        }

        /*
         * Drawer mobile FORA do <header>: sticky + backdrop-blur no header
         * criam containing block e o position:fixed do nav deixa de cobrir o ecrã.
         */
        #sc-mobile-drawer {
            display: none;
        }
        @media (max-width: 1023px) {
            #sc-mobile-drawer.active {
                display: flex !important;
                position: fixed !important;
                top: 5rem !important;
                left: 0 !important;
                right: 0 !important;
                bottom: 0 !important;
                width: 100% !important;
                z-index: 90 !important;
                flex-direction: column !important;
                align-items: stretch !important;
                justify-content: flex-start !important;
                background: #050A14 !important;
                padding: 1.5rem 0 2rem !important;
                border-top: 1px solid rgba(255, 255, 255, 0.05) !important;
                overflow-y: auto !important;
                -webkit-overflow-scrolling: touch !important;
            }
            body.admin-bar #sc-mobile-drawer.active {
                top: calc(5rem + 46px) !important;
            }
            @media (min-width: 783px) {
                body.admin-bar #sc-mobile-drawer.active {
                    top: calc(5rem + 32px) !important;
                }
            }
            #sc-mobile-drawer.active > ul {
                display: flex !important;
                flex-direction: column !important;
                gap: 1.25rem !important;
                text-align: center !important;
                width: 100% !important;
                list-style: none !important;
                margin: 0 !important;
                padding: 0 2rem 1.5rem 2rem !important;
            }
            #sc-mobile-drawer li {
                list-style: none !important;
                margin: 0 !important;
                width: 100% !important;
            }
            #sc-mobile-drawer a {
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                gap: 0.25rem !important;
                font-size: 1.15rem !important;
                font-weight: 700 !important;
                letter-spacing: 0.2em !important;
                line-height: 1.5 !important;
                padding: 0.5rem 0 !important;
                color: #ffffff !important;
                text-transform: uppercase !important;
                text-decoration: none !important;
            }
            #sc-mobile-drawer .sub-menu {
                display: flex !important;
                flex-direction: column !important;
                gap: 0.75rem !important;
                margin-top: 1rem !important;
                padding-top: 1rem !important;
                padding-bottom: 0.5rem !important;
                border-top: 1px solid rgba(255, 255, 255, 0.1) !important;
                width: 100% !important;
                list-style: none !important;
            }
            #sc-mobile-drawer .sub-menu li a {
                font-size: 0.85rem !important;
                color: rgba(255, 255, 255, 0.7) !important;
                letter-spacing: 0.15em !important;
            }
            #sc-mobile-drawer .menu-item-has-children > a::after {
                display: none !important;
            }
            #sc-mobile-drawer .sc-mobile-account {
                display: flex !important;
                flex-direction: column !important;
                align-items: center !important;
                gap: 0.75rem !important;
                width: 100% !important;
                margin-top: auto !important;
                padding: 1.5rem 2rem 0.5rem !important;
                border-top: 1px solid rgba(255, 255, 255, 0.08) !important;
            }
            #sc-mobile-drawer .sc-mobile-account a {
                width: 100% !important;
                max-width: 20rem !important;
                justify-content: center !important;
                gap: 0.5rem !important;
                font-size: 0.95rem !important;
                letter-spacing: 0.15em !important;
            }
            #sc-mobile-drawer .sc-mobile-account__primary {
                background: #C5A059 !important;
                color: #050A14 !important;
                border-radius: 0.75rem !important;
                padding: 0.85rem 1.25rem !important;
            }
            #sc-mobile-drawer .sc-mobile-account__logout {
                color: #f87171 !important;
                font-size: 0.85rem !important;
            }
        }
    </style>
</head>
<body <?php body_class('bg-background-dark-alt'); ?>>
<script>
    // Ensure the body has the dark background even if classes are delayed
    document.body.style.backgroundColor = '#050A14';
</script>
<?php wp_body_open(); ?>

<?php
$sc_is_portal = function_exists( 'saulocoelho_is_portal_aluno' ) && saulocoelho_is_portal_aluno();
if ( $sc_is_portal ) {
	get_template_part( 'template-parts/portal-aluno/topbar' );
} else {
?>

<div class="sc-deco-corner sc-deco-corner--tr" aria-hidden="true"><span></span><span></span><span></span></div>
<div class="sc-deco-corner sc-deco-corner--bl" aria-hidden="true"><span></span><span></span><span></span></div>

<header class="sc-site-header-marketing sticky top-0 w-full z-[100] border-b border-white/5 bg-black/10 backdrop-blur-xl transition-all duration-500">
    <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="flex items-center gap-3 relative z-[60]">
            <div class="text-primary">
                <span class="material-symbols-outlined text-3xl">terminal</span>
            </div>
            <h2 class="font-display text-xl font-black tracking-tighter uppercase text-white"><?php bloginfo( 'name' ); ?></h2>
        </a>

        <?php
        $sc_account_url   = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : wp_login_url();
        $sc_logged_in     = is_user_logged_in();
        $sc_account_label = $sc_logged_in
            ? __( 'Área do Aluno', 'saulocoelho' )
            : __( 'Entrar', 'saulocoelho' );
        $sc_account_icon  = $sc_logged_in ? 'account_circle' : 'login';
        ?>

        <nav id="main-nav" class="hidden lg:flex items-center gap-10" aria-label="<?php esc_attr_e( 'Menu principal', 'saulocoelho' ); ?>">
            <?php
            wp_nav_menu( array(
                'theme_location' => 'menu-1',
                'container'      => false,
                'items_wrap'     => '<ul id="%1$s" class="%2$s">%3$s</ul>',
                'fallback_cb'    => false,
            ) );
            ?>
        </nav>

        <div class="flex items-center gap-1 sm:gap-3 relative z-[60]">
            <?php
            if ( function_exists( 'WC' ) && WC()->cart ) {
                $sc_cart_count = WC()->cart->get_cart_contents_count();
                $sc_checkout   = wc_get_checkout_url();
                $sc_cart_label = $sc_cart_count > 0
                    /* translators: %d: number of items in cart */
                    ? sprintf( __( 'Carrinho — continuar compra (%d itens)', 'saulocoelho' ), $sc_cart_count )
                    : __( 'Carrinho — finalizar compra', 'saulocoelho' );
                ?>
            <a
                href="<?php echo esc_url( $sc_checkout ); ?>"
                class="relative flex items-center justify-center text-white/90 hover:text-white p-2 rounded-xl transition-colors hover:bg-white/5"
                aria-label="<?php echo esc_attr( $sc_cart_label ); ?>"
            >
                <span class="material-symbols-outlined text-2xl sm:text-[28px]" aria-hidden="true">shopping_cart</span>
                <?php if ( $sc_cart_count > 0 ) : ?>
                    <span class="absolute top-0 right-0 min-w-[1.125rem] h-[1.125rem] px-1 flex items-center justify-center rounded-full bg-primary text-[10px] font-black text-white leading-none">
                        <?php echo esc_html( $sc_cart_count > 99 ? '99+' : $sc_cart_count ); ?>
                    </span>
                <?php endif; ?>
            </a>
                <?php
            }
            ?>
            <a
                href="<?php echo esc_url( $sc_account_url ); ?>"
                class="lg:hidden flex items-center justify-center text-white/90 hover:text-white p-2 rounded-xl transition-colors hover:bg-white/5"
                aria-label="<?php echo esc_attr( $sc_account_label ); ?>"
            >
                <span class="material-symbols-outlined text-2xl" aria-hidden="true"><?php echo esc_html( $sc_account_icon ); ?></span>
            </a>
            <a href="<?php echo esc_url( $sc_account_url ); ?>" class="hidden lg:inline-flex bg-primary hover:bg-primary/90 text-white px-8 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-[0.2em] shadow-lg shadow-primary/20 transition-all hover:-translate-y-0.5">
                <?php echo esc_html( $sc_account_label ); ?>
            </a>
            <button id="menu-toggle" class="lg:hidden text-white p-2 flex items-center justify-center transition-transform active:scale-90" aria-label="<?php esc_attr_e( 'Abrir menu', 'saulocoelho' ); ?>" aria-expanded="false" aria-controls="sc-mobile-drawer">
                <span class="material-symbols-outlined text-3xl">menu</span>
            </button>
        </div>
    </div>
</header>

<nav id="sc-mobile-drawer" class="lg:hidden" aria-label="<?php esc_attr_e( 'Menu mobile', 'saulocoelho' ); ?>" hidden>
    <?php
    wp_nav_menu( array(
        'theme_location' => 'menu-1',
        'container'      => false,
        'items_wrap'     => '<ul id="%1$s" class="%2$s">%3$s</ul>',
        'fallback_cb'    => false,
    ) );
    ?>
    <div class="sc-mobile-account" aria-label="<?php esc_attr_e( 'Conta', 'saulocoelho' ); ?>">
        <a class="sc-mobile-account__primary" href="<?php echo esc_url( $sc_account_url ); ?>">
            <span class="material-symbols-outlined" aria-hidden="true"><?php echo esc_html( $sc_account_icon ); ?></span>
            <?php echo esc_html( $sc_account_label ); ?>
        </a>
        <?php if ( $sc_logged_in ) : ?>
            <a class="sc-mobile-account__logout" href="<?php echo esc_url( wp_logout_url( home_url( '/' ) ) ); ?>">
                <span class="material-symbols-outlined" aria-hidden="true">logout</span>
                <?php esc_html_e( 'Sair', 'saulocoelho' ); ?>
            </a>
        <?php endif; ?>
    </div>
</nav>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.getElementById('menu-toggle');
    const mobileDrawer = document.getElementById('sc-mobile-drawer');
    const icon = menuToggle ? menuToggle.querySelector('.material-symbols-outlined') : null;

    function setDrawerOpen(open) {
        if (!mobileDrawer || !menuToggle || !icon) return;
        mobileDrawer.classList.toggle('active', open);
        if (open) {
            mobileDrawer.removeAttribute('hidden');
        } else {
            mobileDrawer.setAttribute('hidden', '');
        }
        icon.textContent = open ? 'close' : 'menu';
        menuToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        menuToggle.setAttribute('aria-label', open
            ? <?php echo wp_json_encode( __( 'Fechar menu', 'saulocoelho' ) ); ?>
            : <?php echo wp_json_encode( __( 'Abrir menu', 'saulocoelho' ) ); ?>
        );
        document.body.style.overflow = open ? 'hidden' : '';
    }

    if (menuToggle && mobileDrawer && icon) {
        menuToggle.addEventListener('click', function() {
            setDrawerOpen(!mobileDrawer.classList.contains('active'));
        });

        mobileDrawer.querySelectorAll('a').forEach(function(link) {
            link.addEventListener('click', function() {
                setDrawerOpen(false);
            });
        });
    }
});
</script>
<?php } // fim header marketing (não portal) ?>
