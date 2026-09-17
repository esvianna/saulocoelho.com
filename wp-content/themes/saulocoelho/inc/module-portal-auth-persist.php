<?php
/**
 * Persistência de sessão no Portal / PWA (issue #21).
 *
 * Sem «Lembrar-me», o WP grava cookie de sessão (Expire=0). Em PWA instalada
 * (iOS/Android standalone) esse cookie some ao fechar a app. Forçamos cookie
 * persistente nos logins do front (Minha Conta, checkout gate, etc.).
 *
 * @package Saulocoelho
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Duração do cookie persistente (dias).
 *
 * @return int
 */
function saulocoelho_portal_auth_cookie_days() {
	$days = (int) apply_filters( 'saulocoelho_portal_auth_cookie_days', 14 );
	return $days > 0 ? $days : 14;
}

/**
 * @param int  $expiration Expiration offset in seconds.
 * @param int  $user_id    User ID.
 * @param bool $remember   Remember flag.
 * @return int
 */
function saulocoelho_portal_auth_cookie_expiration( $expiration, $user_id = 0, $remember = false ) {
	unset( $expiration, $user_id, $remember );
	return saulocoelho_portal_auth_cookie_days() * DAY_IN_SECONDS;
}
add_filter( 'auth_cookie_expiration', 'saulocoelho_portal_auth_cookie_expiration', 20, 3 );

/**
 * WooCommerce Minha Conta: credenciais com remember=true.
 *
 * @param array $creds Credentials.
 * @return array
 */
function saulocoelho_portal_force_woocommerce_remember( $creds ) {
	if ( ! is_array( $creds ) ) {
		return $creds;
	}
	$creds['remember'] = true;
	return $creds;
}
add_filter( 'woocommerce_login_credentials', 'saulocoelho_portal_force_woocommerce_remember', 20 );

/**
 * Após login no front: reemitir cookie persistente (cobre AJAX / fluxos sem remember).
 * Não altera o ecrã /wp-login.php (admin).
 *
 * @param string  $user_login Login.
 * @param WP_User $user      User.
 */
function saulocoelho_portal_reissue_persistent_auth_cookie( $user_login, $user ) {
	unset( $user_login );
	if ( ! $user instanceof WP_User ) {
		return;
	}
	if ( isset( $GLOBALS['pagenow'] ) && $GLOBALS['pagenow'] === 'wp-login.php' ) {
		return;
	}
	wp_set_auth_cookie( (int) $user->ID, true, is_ssl() );
}
add_action( 'wp_login', 'saulocoelho_portal_reissue_persistent_auth_cookie', 99, 2 );
