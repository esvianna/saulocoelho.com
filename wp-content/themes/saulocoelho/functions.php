<?php
/**
 * Saulo Coelho functions and definitions
 */

if ( ! function_exists( 'saulocoelho_setup' ) ) :
    function saulocoelho_setup() {
        // Add default posts and comments RSS feed links to head.
        add_theme_support( 'automatic-feed-links' );

        // Let WordPress manage the document title.
        add_theme_support( 'title-tag' );

        // Enable support for Post Thumbnails on posts and pages.
        add_theme_support( 'post-thumbnails' );

        // This theme uses wp_nav_menu() in one location.
        register_nav_menus( array(
            'menu-1' => esc_html__( 'Primary', 'saulocoelho' ),
        ) );

        // Switch default core markup for search form, comment form, and comments to output valid HTML5.
        add_theme_support( 'html5', array(
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
        ) );

        // Add theme support for selective refresh for widgets.
        add_theme_support( 'customize-selective-refresh-widgets' );

        // Add WooCommerce support
        add_theme_support( 'woocommerce' );
    }
endif;
add_action( 'after_setup_theme', 'saulocoelho_setup' );

/**
 * Enqueue scripts and styles.
 */
function saulocoelho_scripts() {
    // Compiled Tailwind CSS
    wp_enqueue_style( 'saulocoelho-style', get_template_directory_uri() . '/dist/output.css', array(), '1.0.0' );

    // Inter Font
    wp_enqueue_style( 'saulocoelho-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap', array(), null );

    // Material Symbols
    wp_enqueue_style( 'saulocoelho-material-symbols', 'https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap', array(), null );
}
add_action( 'wp_enqueue_scripts', 'saulocoelho_scripts' );
