<?php
/**
 * Inscrições presenciais — gateway, formulário pós-pedido e painel admin.
 *
 * @package SauloCoelho
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SC_PRESENCIAL_DB_VERSION', '1.0.0' );
define( 'SC_PRESENCIAL_FORM_SCHEMA', 'coaching-terapia-2026-07' );

$sc_presencial_dir = __DIR__ . '/presencial-enrollments';
require_once $sc_presencial_dir . '/helpers.php';
require_once $sc_presencial_dir . '/database.php';
require_once $sc_presencial_dir . '/form-schema.php';
require_once $sc_presencial_dir . '/gateway.php';
require_once $sc_presencial_dir . '/orders.php';
require_once $sc_presencial_dir . '/my-account.php';
require_once $sc_presencial_dir . '/admin.php';

add_action( 'after_switch_theme', 'sc_presencial_install_table' );
add_action( 'admin_init', 'sc_presencial_maybe_install_table' );
