<?php
/**
 * Leads da palestra — cadastro obrigatório para download.
 *
 * @package SauloCoelho
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SC_PALESTRA_LEADS_DB_VERSION', '1.1.0' );
define( 'SC_PALESTRA_LEADS_SOURCE', 'teresopolis-2026-09' );
define( 'SC_PALESTRA_PAGE_SLUG', 'palestra' );
define( 'SC_PALESTRA_MAX_DOWNLOADS', 8 );
define( 'SC_PALESTRA_MAX_ATTACH_BYTES', 7340032 );

$sc_palestra_dir = __DIR__ . '/palestra-leads';
require_once $sc_palestra_dir . '/database.php';
require_once $sc_palestra_dir . '/event.php';
require_once $sc_palestra_dir . '/cpt.php';
require_once $sc_palestra_dir . '/metaboxes.php';
require_once $sc_palestra_dir . '/mail.php';
require_once $sc_palestra_dir . '/front.php';
require_once $sc_palestra_dir . '/admin.php';

add_action( 'init', 'sc_palestra_register_cpt', 4 );
add_action( 'init', 'sc_palestra_register_rewrites', 5 );
add_filter( 'query_vars', 'sc_palestra_query_vars' );
add_action( 'init', 'sc_palestra_maybe_install_table', 6 );
add_action( 'init', 'sc_palestra_maybe_create_page', 20 );
add_action( 'init', 'sc_palestra_seed_teresopolis', 21 );
add_action( 'init', 'sc_palestra_maybe_flush_rewrites', 22 );
add_action( 'after_switch_theme', 'sc_palestra_install_table' );
add_action( 'after_switch_theme', 'sc_palestra_maybe_create_page' );
add_action( 'after_switch_theme', 'sc_palestra_seed_teresopolis' );

add_action( 'add_meta_boxes', 'sc_palestra_add_metaboxes' );
add_action( 'save_post_' . SC_PALESTRA_CPT, 'sc_palestra_save_metaboxes', 10, 2 );
add_action( 'admin_enqueue_scripts', 'sc_palestra_admin_assets' );
add_filter( 'manage_sc_palestra_posts_columns', 'sc_palestra_cpt_columns' );
add_action( 'manage_sc_palestra_posts_custom_column', 'sc_palestra_cpt_column_values', 10, 2 );
add_filter( 'post_row_actions', 'sc_palestra_row_actions', 10, 2 );

add_action( 'template_redirect', 'sc_palestra_maybe_serve_pdf', 1 );
add_action( 'template_redirect', 'sc_palestra_maybe_404', 5 );
add_action( 'wp_enqueue_scripts', 'sc_palestra_enqueue_assets' );
add_action( 'wp_ajax_sc_palestra_submit', 'sc_palestra_ajax_submit' );
add_action( 'wp_ajax_nopriv_sc_palestra_submit', 'sc_palestra_ajax_submit' );
