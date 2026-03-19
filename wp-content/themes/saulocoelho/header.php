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
              "primary": "#137fec",
              "background-light": "#f6f7f8",
              "background-dark": "#101922",
              "background-dark-alt": "#0a1118"
            },
            fontFamily: {
              "display": ["Inter", "sans-serif"]
            }
          }
        }
      }
    </script>
    <style>
        /* Force styling for WordPress dynamic menu */
        nav ul {
            display: flex !important;
            align-items: center !important;
            gap: 2.5rem !important; /* gap-10 */
            list-style: none !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        nav ul li {
            list-style: none !important;
            margin: 0 !important;
        }
        nav ul li a {
            font-size: 0.825rem !important; /* text-sm/xs */
            font-weight: 700 !important;
            color: #ffffff !important; /* Changed to pure white for MAX contrast */
            text-transform: uppercase !important;
            letter-spacing: 0.15em !important;
            text-decoration: none !important;
            transition: color 0.2s !important;
        }
        nav ul li a:hover {
            color: #3b82f6 !important; /* text-primary light */
        }
        /* Mobile menu specific styles */
        @media (max-width: 1023px) {
            #main-nav.active {
                display: flex !important;
                position: fixed;
                top: 5rem; /* h-20 */
                left: 0;
                width: 100%;
                height: calc(100vh - 5rem);
                background-color: rgba(16, 25, 34, 0.98); /* background-dark */
                backdrop-filter: blur(12px);
                flex-direction: column;
                justify-content: center;
                z-index: 40;
            }
            #main-nav.active ul {
                flex-direction: column !important;
                gap: 2rem !important;
            }
        }
    </style>
</head>
<body <?php body_class('bg-background-dark-alt'); ?>>
<script>
    // Ensure the body has the dark background even if classes are delayed
    document.body.style.backgroundColor = '#0a1118';
</script>
<?php wp_body_open(); ?>

<header class="sticky top-0 w-full z-50 border-b border-white/5 bg-background-dark/95 backdrop-blur-md">
    <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="flex items-center gap-3 relative z-50">
            <div class="text-primary">
                <span class="material-symbols-outlined text-3xl">terminal</span>
            </div>
            <h2 class="text-xl font-bold tracking-tighter uppercase text-white"><?php bloginfo( 'name' ); ?></h2>
        </a>
        
        <nav id="main-nav" class="hidden lg:flex items-center gap-10">
            <?php
            wp_nav_menu( array(
                'theme_location' => 'menu-1',
                'items_wrap'     => '<ul id="%1$s" class="%2$s">%3$s</ul>',
                'fallback_cb'    => false,
            ) );
            ?>
        </nav>

        <div class="hidden lg:flex items-center gap-4">
            <!-- CTA Button -->
            <a href="https://saulo.vtis.com.br/area-do-cliente/" class="hidden md:block bg-primary hover:bg-primary/90 text-white px-6 py-2 rounded-lg text-xs font-black uppercase tracking-widest transition-all">
                Área do Cliente
            </a>
        </div>
        
        <button id="menu-toggle" class="lg:hidden relative z-50 text-white p-2" aria-label="Toggle menu">
            <span class="material-symbols-outlined text-3xl">menu</span>
        </button>
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
