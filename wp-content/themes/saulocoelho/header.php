<!DOCTYPE html>
<html <?php language_attributes(); ?> class="dark">
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="fixed top-0 w-full z-50 border-b border-white/5 bg-background-dark-alt/80 backdrop-blur-md">
    <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="flex items-center gap-3 relative z-50">
            <div class="text-primary">
                <span class="material-symbols-outlined text-3xl">terminal</span>
            </div>
            <h2 class="text-xl font-bold tracking-tighter uppercase text-white"><?php bloginfo( 'name' ); ?></h2>
        </a>
        
        <nav class="hidden lg:flex items-center gap-10">
            <?php
            wp_nav_menu( array(
                'theme_location' => 'menu-1',
                'container'      => false,
                'menu_class'     => 'flex items-center gap-10',
                'items_wrap'     => '%3$s',
                'fallback_cb'    => false,
            ) );
            ?>
            <!-- Static links to match mockup -->
            <a href="#" class="text-sm font-medium text-slate-300 hover:text-primary transition-colors uppercase tracking-widest">Quem é?</a>
            <a href="#" class="text-sm font-medium text-slate-300 hover:text-primary transition-colors uppercase tracking-widest">Programas</a>
            <a href="#" class="text-sm font-medium text-slate-300 hover:text-primary transition-colors uppercase tracking-widest">Loja</a>
            <a href="#" class="text-sm font-medium text-slate-300 hover:text-primary transition-colors uppercase tracking-widest">Masterclass</a>
        </nav>

        <div class="hidden lg:flex items-center gap-4">
            <a href="#" class="bg-primary hover:bg-primary/90 text-white text-xs font-bold uppercase tracking-widest px-6 py-3 rounded-lg transition-all text-center">
                Área do Cliente
            </a>
        </div>
        
        <button class="lg:hidden relative z-50 text-white p-2" aria-label="Toggle menu">
            <span class="material-symbols-outlined text-3xl">menu</span>
        </button>
    </div>
</header>
