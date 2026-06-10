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

        /* ================= Mobile Specific (< 1024px) ================= */
        @media (max-width: 1023px) {
            #main-nav.active {
                display: flex !important;
                position: absolute !important;
                top: 5rem !important; /* height of header (h-20 = 5rem) */
                left: 0 !important;
                right: 0 !important;
                height: 100vh !important;
                background: rgba(5, 10, 20, 0.98) !important;
                backdrop-filter: blur(20px) !important;
                flex-direction: column !important;
                justify-content: flex-start !important;
                align-items: center !important;
                z-index: 40 !important;
                padding-top: 3rem !important;
                border-top: 1px solid rgba(255, 255, 255, 0.05) !important;
            }
            #main-nav.active > ul {
                display: flex !important;
                flex-direction: column !important;
                gap: 1.5rem !important;
                text-align: center !important;
                width: 100% !important;
                overflow-y: auto !important;
                max-height: 80vh !important;
                padding: 0 2rem 5rem 2rem !important; /* Safe padding no mobile */
                margin: 0 !important;
            }
            #main-nav.active a {
                font-size: 1.25rem !important;
                font-weight: 700 !important;
                letter-spacing: 0.2em !important;
                justify-content: center !important;
                line-height: 1.5 !important;
                padding: 0.5rem 0 !important;
            }
            /* Mobile Dropdown (expanded inline) */
            #main-nav.active .sub-menu {
                display: flex !important;
                flex-direction: column !important;
                gap: 0.75rem !important;
                margin-top: 1rem !important;
                padding-top: 1rem !important;
                padding-bottom: 0.5rem !important;
                border-top: 1px solid rgba(255, 255, 255, 0.1) !important;
                width: 100% !important;
            }
            #main-nav.active .sub-menu li a {
                font-size: 0.85rem !important;
                color: rgba(255, 255, 255, 0.7) !important;
                letter-spacing: 0.15em !important;
                font-weight: 700 !important;
            }
            #main-nav.active .menu-item-has-children > a::after {
                display: none !important;
            }
            #main-nav.active > ul > li {
                width: 100% !important;
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

<div class="sc-deco-corner sc-deco-corner--tr" aria-hidden="true"><span></span><span></span><span></span></div>
<div class="sc-deco-corner sc-deco-corner--bl" aria-hidden="true"><span></span><span></span><span></span></div>

<header class="sticky top-0 w-full z-[100] border-b border-white/5 bg-black/10 backdrop-blur-xl transition-all duration-500">
    <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="flex items-center gap-3 relative z-[60]">
            <div class="text-primary">
                <span class="material-symbols-outlined text-3xl">terminal</span>
            </div>
            <h2 class="font-display text-xl font-black tracking-tighter uppercase text-white"><?php bloginfo( 'name' ); ?></h2>
        </a>
        
        <nav id="main-nav" class="hidden lg:flex items-center gap-10">
            <?php
            wp_nav_menu( array(
                'theme_location' => 'menu-1',
                'container'      => false,
                'items_wrap'     => '<ul id="%1$s" class="%2$s">%3$s</ul>',
                'fallback_cb'    => false,
            ) );
            ?>
        </nav>

        <div class="flex items-center gap-2 sm:gap-4 relative z-[60]">
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
            <a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="hidden lg:inline-flex bg-primary hover:bg-primary/90 text-white px-8 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-[0.2em] shadow-lg shadow-primary/20 transition-all hover:-translate-y-0.5">
                Área do Cliente
            </a>
            <button id="menu-toggle" class="lg:hidden text-white p-2 flex items-center justify-center transition-transform active:scale-90" aria-label="<?php esc_attr_e( 'Abrir menu', 'saulocoelho' ); ?>">
                <span class="material-symbols-outlined text-3xl">menu</span>
            </button>
        </div>
    </div>
</header>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.getElementById('menu-toggle');
    const mainNav = document.getElementById('main-nav');
    const icon = menuToggle.querySelector('.material-symbols-outlined');
    
    if (menuToggle && mainNav) {
        menuToggle.addEventListener('click', function() {
            mainNav.classList.toggle('active');
            
            // Toggle icon between menu and close
            if (mainNav.classList.contains('active')) {
                icon.textContent = 'close';
                document.body.style.overflow = 'hidden'; // Prevent scrolling
            } else {
                icon.textContent = 'menu';
                document.body.style.overflow = ''; // Restore scrolling
            }
        });

        // Close menu when clicking a link (important for anchor links)
        const menuLinks = mainNav.querySelectorAll('a');
        menuLinks.forEach(link => {
            link.addEventListener('click', () => {
                mainNav.classList.remove('active');
                icon.textContent = 'menu';
                document.body.style.overflow = '';
            });
        });
    }
});
</script>
