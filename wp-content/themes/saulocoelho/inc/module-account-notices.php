<?php
/**
 * Avisos WooCommerce / login em pt-BR e visual do portal do aluno.
 *
 * @package Saulocoelho
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Normaliza texto de erro de login (Woo + Limit Login Attempts, etc.).
 *
 * @param string $message Mensagem original.
 * @return string
 */
function saulocoelho_normalize_login_message( $message ) {
	$plain = wp_strip_all_tags( (string) $message );
	$plain = html_entity_decode( $plain, ENT_QUOTES, 'UTF-8' );
	$plain = preg_replace( '/\s+/u', ' ', $plain );
	$plain = trim( $plain );

	$plain = preg_replace( '/\b(ERROR|ERRO|Error)\s*:?\s*/iu', '', $plain );
	$plain = preg_replace( '/^:\s*/u', '', $plain );

	$map = [
		'/Too many failed login attempts\.?/iu' => __( 'Muitas tentativas de login. Aguarde um momento.', 'saulocoelho' ),
		'/Please try again in (\d+) minute\(s\)\./iu' => __( 'Tente novamente em $1 minuto(s).', 'saulocoelho' ),
		'/Please try again later\.?/iu' => __( 'Tente novamente mais tarde.', 'saulocoelho' ),
		'/(\d+)\s*attempt\(s\)\s*(left|remaining)/iu' => '$1 tentativas restantes',
		'/(\d+)\s*attempts?\s*(left|remaining)/iu' => '$1 tentativas restantes',
	];
	foreach ( $map as $pattern => $replacement ) {
		$plain = preg_replace( $pattern, $replacement, $plain );
	}

	$plain = preg_replace( '/tentativas restantes\.\s*(\d+)\s*tentativas restantes/iu', '$1 tentativas restantes', $plain );
	$plain = preg_replace( '/\.\s*tentativas restantes\.?/iu', '.', $plain );
	$plain = preg_replace_callback(
		'/(\d+)\s*tentativas restantes/iu',
		static function ( $m ) {
			$n = (int) $m[1];
			if ( $n === 1 ) {
				return __( '1 tentativa restante', 'saulocoelho' );
			}
			return sprintf(
				/* translators: %d: remaining attempts */
				__( '%d tentativas restantes', 'saulocoelho' ),
				$n
			);
		},
		$plain
	);

	$plain = preg_replace( '/(\d+\s+tentativa(?:s)? restante(?:s)?)\.\s+\1/iu', '$1', $plain );
	$plain = preg_replace( '/(\d+\s+tentativa(?:s)? restante(?:s)?)(?:\.\s*\1)+/iu', '$1', $plain );

	$plain = trim( $plain, " \t\n\r\0\x0B." );
	if ( $plain === '' ) {
		return __( 'Não foi possível entrar. Verifique o e-mail e a senha.', 'saulocoelho' );
	}

	return $plain . '.';
}

/**
 * Erros de credencial / bloqueio de tentativas — irrelevantes depois do login.
 *
 * @param string $text
 * @return bool
 */
function saulocoelho_is_login_gate_notice( $text ) {
	$plain = wp_strip_all_tags( (string) $text );
	return (bool) preg_match(
		'/tentativa(?:s)? de login|login mal-sucedid|failed login|attempt(?:s)? remaining|attempt\(s\)|senha inv[aá]lid|nome ou senha|invalid username|lost your password|muitas tentativas|tente novamente em\s+\d+|tentativas restantes/iu',
		$plain
	);
}

/**
 * Tira avisos de bloqueio da sessão WC.
 * O WPS Limit Login reinsere-os em wp_head em qualquer Minha Conta se o IP estiver locked.
 *
 * @param array $notices
 * @return array
 */
function saulocoelho_strip_login_gate_errors( $notices ) {
	if ( empty( $notices['error'] ) || ! is_array( $notices['error'] ) ) {
		return $notices;
	}
	$kept = [];
	foreach ( $notices['error'] as $item ) {
		$text = is_array( $item ) ? (string) ( $item['notice'] ?? '' ) : (string) $item;
		if ( saulocoelho_is_login_gate_notice( $text ) ) {
			continue;
		}
		$kept[] = $item;
	}
	if ( $kept ) {
		$notices['error'] = $kept;
	} else {
		unset( $notices['error'] );
	}
	return $notices;
}

/**
 * Esta versão do WooCommerce lê wc_notices da sessão e ignora woocommerce_get_notices.
 */
function saulocoelho_purge_login_notices_when_logged_in() {
	if ( ! is_user_logged_in() || ! function_exists( 'WC' ) || ! WC()->session ) {
		return;
	}
	$notices = WC()->session->get( 'wc_notices', [] );
	if ( empty( $notices ) || ! is_array( $notices ) ) {
		return;
	}
	$cleaned = saulocoelho_strip_login_gate_errors( $notices );
	if ( $cleaned !== $notices ) {
		WC()->session->set( 'wc_notices', $cleaned );
	}
}
add_action( 'wp_head', 'saulocoelho_purge_login_notices_when_logged_in', 20 );
add_action( 'woocommerce_account_content', 'saulocoelho_purge_login_notices_when_logged_in', 1 );

/**
 * Remove aviso extra só com "N tentativa(s) restante(s)" se já veio no erro principal.
 *
 * @param array $notices
 * @return array
 */
function saulocoelho_dedupe_login_notices( $notices ) {
	if ( empty( $notices['error'] ) || ! is_array( $notices['error'] ) ) {
		return $notices;
	}

	if ( is_user_logged_in() ) {
		return saulocoelho_strip_login_gate_errors( $notices );
	}

	$cleaned = [];
	foreach ( $notices['error'] as $item ) {
		$text = is_array( $item ) ? (string) ( $item['notice'] ?? '' ) : (string) $item;
		$norm = saulocoelho_normalize_login_message( $text );
		if ( preg_match( '/^0\s+tentativa(?:s)? restante(?:s)?\.?$/iu', $norm ) ) {
			continue;
		}
		if ( is_array( $item ) ) {
			$item['notice'] = $norm;
			$cleaned[]      = $item;
		} else {
			$cleaned[] = $norm;
		}
	}

	$out = [];
	foreach ( $cleaned as $item ) {
		$text = is_array( $item ) ? (string) $item['notice'] : (string) $item;
		$only_remaining = (bool) preg_match( '/^\d+\s+tentativa(?:s)? restante(?:s)?\.?$/iu', $text );
		if ( $only_remaining ) {
			$dup = false;
			foreach ( $out as $kept ) {
				$kt = is_array( $kept ) ? (string) $kept['notice'] : (string) $kept;
				$core = preg_replace( '/\.?$/', '', $text );
				if ( $core !== '' && stripos( $kt, $core ) !== false ) {
					$dup = true;
					break;
				}
			}
			if ( $dup ) {
				continue;
			}
		}
		$out[] = $item;
	}

	$notices['error'] = $out;
	return $notices;
}
add_filter( 'woocommerce_get_notices', 'saulocoelho_dedupe_login_notices' );

/**
 * @param string $message
 * @return string
 */
function saulocoelho_filter_account_error_notice( $message ) {
	if ( is_user_logged_in() && saulocoelho_is_login_gate_notice( $message ) ) {
		return '';
	}
	$raw = wp_strip_all_tags( (string) $message );
	$looks_login = (bool) preg_match( '/attempt|senha inv[aá]lid|invalid username|lost your password|ERROR:|ERRO:/i', $raw );
	if ( ! $looks_login && ! saulocoelho_is_login_context() ) {
		return $message;
	}
	return saulocoelho_normalize_login_message( $message );
}
add_filter( 'woocommerce_add_error', 'saulocoelho_filter_account_error_notice', 5 );

/**
 * @return bool
 */
function saulocoelho_is_login_context() {
	if ( is_user_logged_in() ) {
		return false;
	}
	if ( function_exists( 'is_account_page' ) && is_account_page() ) {
		return true;
	}
	if ( isset( $GLOBALS['pagenow'] ) && $GLOBALS['pagenow'] === 'wp-login.php' ) {
		return true;
	}
	return false;
}

/**
 * @param WP_Error|WP_User|null $user
 * @return WP_Error|WP_User|null
 */
function saulocoelho_translate_authenticate_errors( $user ) {
	if ( ! is_wp_error( $user ) ) {
		return $user;
	}

	$clean = new WP_Error();
	foreach ( $user->get_error_codes() as $code ) {
		$data = $user->get_error_data( $code );
		foreach ( $user->get_error_messages( $code ) as $msg ) {
			$clean->add( $code, saulocoelho_normalize_login_message( $msg ), $data );
		}
	}
	return $clean;
}
add_filter( 'authenticate', 'saulocoelho_translate_authenticate_errors', 9999 );
add_filter( 'login_errors', 'saulocoelho_normalize_login_message' );

/**
 * @param string $translated
 * @param string $text
 * @param string $domain
 * @return string
 */
function saulocoelho_limit_login_gettext( $translated, $text, $domain ) {
	if ( $domain !== 'limit-login-attempts-reloaded' && $domain !== 'limit-login-attempts' ) {
		return $translated;
	}

	switch ( $text ) {
		case '%d attempt remaining.':
		case '%d attempt(s) remaining.':
		case '%d attempt(s) left':
		case '%d attempt left':
			return _n( '%d tentativa restante.', '%d tentativas restantes.', 1, 'saulocoelho' );
		case '%d attempts remaining.':
		case '%d attempts left':
			return __( '%d tentativas restantes.', 'saulocoelho' );
		case 'Please try again in %d minute(s).':
			return __( 'Tente novamente em %d minuto(s).', 'saulocoelho' );
		case 'Too many failed login attempts.':
			return __( 'Muitas tentativas de login. Aguarde um momento.', 'saulocoelho' );
		default:
			return $translated;
	}
}
add_filter( 'gettext', 'saulocoelho_limit_login_gettext', 20, 3 );

/**
 * @param string $translated
 * @param string $single
 * @param string $plural
 * @param int    $number
 * @param string $domain
 * @return string
 */
function saulocoelho_limit_login_ngettext( $translated, $single, $plural, $number, $domain ) {
	if ( $domain !== 'limit-login-attempts-reloaded' && $domain !== 'limit-login-attempts' ) {
		return $translated;
	}
	$hay = $single . ' ' . $plural;
	if ( stripos( $hay, 'attempt' ) !== false ) {
		return sprintf(
			_n( '%d tentativa restante.', '%d tentativas restantes.', (int) $number, 'saulocoelho' ),
			(int) $number
		);
	}
	if ( stripos( $hay, 'minute' ) !== false ) {
		return sprintf(
			_n( 'Tente novamente em %d minuto.', 'Tente novamente em %d minutos.', (int) $number, 'saulocoelho' ),
			(int) $number
		);
	}
	return $translated;
}
add_filter( 'ngettext', 'saulocoelho_limit_login_ngettext', 20, 5 );

/**
 * WooCommerce: aviso de senha temporária após cadastro (convite / wc_create_new_customer).
 *
 * @param string $translated
 * @param string $text
 * @param string $domain
 * @return string
 */
function saulocoelho_woocommerce_account_gettext( $translated, $text, $domain ) {
	if ( $domain !== 'woocommerce' ) {
		return $translated;
	}
	if ( $text === 'Your account is using a temporary password. We emailed you a link to change your password.' ) {
		return __( 'A sua conta está com uma senha temporária. Enviámos um e-mail com o link para definir a senha.', 'saulocoelho' );
	}
	if ( $text === '%1$sResend%2$s' ) {
		return __( '%1$sReenviar%2$s', 'saulocoelho' );
	}
	return $translated;
}
add_filter( 'gettext', 'saulocoelho_woocommerce_account_gettext', 20, 3 );

/**
 * Estilos dos avisos e do formulário de login em Minha Conta.
 */
function saulocoelho_account_notices_styles() {
	if ( ! function_exists( 'is_account_page' ) || ! is_account_page() ) {
		return;
	}

	$css = '
body.woocommerce-account .woocommerce-notices-wrapper,
body.woocommerce-account .woocommerce-NoticeGroup {
	max-width: 42rem;
	margin: 0 auto 1.75rem;
}
body.woocommerce-account ul.woocommerce-error,
body.woocommerce-account .woocommerce-message,
body.woocommerce-account .woocommerce-info,
body.woocommerce-account .woocommerce-error,
body.woocommerce-account .sc-notice {
	list-style: none !important;
	margin: 0 0 1.25rem !important;
	padding: 1rem 1.15rem !important;
	border-radius: 10px !important;
	border: 1px solid rgba(248, 113, 113, 0.45) !important;
	background: rgba(127, 29, 29, 0.35) !important;
	color: #fecaca !important;
	font-family: Inter, system-ui, sans-serif !important;
	font-size: 0.95rem !important;
	line-height: 1.5 !important;
	text-transform: none !important;
	letter-spacing: 0 !important;
	box-shadow: none !important;
}
body.woocommerce-account .woocommerce-message,
body.woocommerce-account .sc-notice--success {
	border-color: rgba(52, 211, 153, 0.35) !important;
	background: rgba(6, 78, 59, 0.35) !important;
	color: #a7f3d0 !important;
}
body.woocommerce-account .woocommerce-info,
body.woocommerce-account .sc-notice--info {
	border-color: rgba(197, 160, 89, 0.35) !important;
	background: rgba(197, 160, 89, 0.12) !important;
	color: #e7d3a1 !important;
	display: flex !important;
	align-items: center !important;
	justify-content: space-between !important;
	gap: 1rem !important;
	flex-wrap: wrap !important;
}
body.woocommerce-account ul.woocommerce-error::before,
body.woocommerce-account ul.woocommerce-error::after,
body.woocommerce-account .woocommerce-error::before,
body.woocommerce-account .woocommerce-error::after,
body.woocommerce-account .woocommerce-message::before,
body.woocommerce-account .woocommerce-message::after,
body.woocommerce-account .woocommerce-info::before,
body.woocommerce-account .woocommerce-info::after,
body.woocommerce-account .wc-block-components-notice-banner::before,
body.woocommerce-account .woocommerce-error > svg,
body.woocommerce-account .woocommerce-message > svg,
body.woocommerce-account .woocommerce-info > svg,
body.woocommerce-account .wc-block-components-notice-banner > svg,
body.woocommerce-account .wc-block-components-notice-banner__icon {
	display: none !important;
	content: none !important;
	width: 0 !important;
	height: 0 !important;
}
body.woocommerce-account .woocommerce-info a.button,
body.woocommerce-account .woocommerce-info .button.wc-forward {
	display: inline-block !important;
	margin: 0 0 0 auto !important;
	padding: 0.45rem 0.9rem !important;
	border-radius: 8px !important;
	background: #C5A059 !important;
	border: 0 !important;
	color: #050A14 !important;
	font-weight: 700 !important;
	text-transform: none !important;
	letter-spacing: 0 !important;
	flex-shrink: 0 !important;
}
body.woocommerce-account .saulocoelho-dashboard-sidebar a,
body.woocommerce-account .woocommerce-MyAccount-navigation a {
	text-decoration: none !important;
	border-bottom: 0 !important;
}
body.woocommerce-account .saulocoelho-dashboard-sidebar .material-symbols-outlined {
	text-decoration: none !important;
	border: 0 !important;
}
body.woocommerce-account ul.woocommerce-error li,
body.woocommerce-account ul.woocommerce-error li strong,
body.woocommerce-account .woocommerce-error a,
body.woocommerce-account .woocommerce-message a {
	color: inherit !important;
	background: transparent !important;
	border: 0 !important;
	padding: 0 !important;
	margin: 0.2rem 0 !important;
	font-weight: 500 !important;
	text-transform: none !important;
}
body.woocommerce-account .woocommerce-form-login,
body.woocommerce-account .u-columns .col-1,
body.woocommerce-account .u-columns .col-2 {
	background: #0A0E1A !important;
	border: 1px solid rgba(255,255,255,0.08) !important;
	border-radius: 12px !important;
	padding: 1.75rem !important;
	color: #F2F4F7 !important;
	font-family: Inter, system-ui, sans-serif !important;
}
body.woocommerce-account .woocommerce-form-login h2,
body.woocommerce-account .u-columns h2 {
	font-family: Inter, sans-serif !important;
	font-size: 0.75rem !important;
	letter-spacing: 0.2em !important;
	text-transform: uppercase !important;
	color: #C5A059 !important;
	font-weight: 800 !important;
	margin: 0 0 1.25rem !important;
}
body.woocommerce-account .woocommerce-form-login label,
body.woocommerce-account .woocommerce-form-register label {
	color: #9AA3AD !important;
	font-size: 0.85rem !important;
}
body.woocommerce-account .woocommerce-form-login input.input-text,
body.woocommerce-account .woocommerce-form-login input[type="text"],
body.woocommerce-account .woocommerce-form-login input[type="password"],
body.woocommerce-account .woocommerce-form-register input.input-text {
	background: #050A14 !important;
	border: 1px solid rgba(197, 160, 89, 0.28) !important;
	color: #F2F4F7 !important;
	border-radius: 8px !important;
	padding: 0.7rem 0.85rem !important;
}
body.woocommerce-account .sc-password-field {
	position: relative !important;
	display: block !important;
}
body.woocommerce-account .sc-password-field .input-text {
	width: 100% !important;
	padding-right: 2.75rem !important;
	box-sizing: border-box !important;
}
body.woocommerce-account .sc-password-toggle {
	position: absolute !important;
	right: 0.35rem !important;
	top: 50% !important;
	transform: translateY(-50%) !important;
	display: inline-flex !important;
	align-items: center !important;
	justify-content: center !important;
	width: 2.25rem !important;
	height: 2.25rem !important;
	margin: 0 !important;
	padding: 0 !important;
	border: 0 !important;
	border-radius: 6px !important;
	background: transparent !important;
	color: #C5A059 !important;
	cursor: pointer !important;
	line-height: 1 !important;
	box-shadow: none !important;
}
body.woocommerce-account .sc-password-toggle:hover,
body.woocommerce-account .sc-password-toggle:focus {
	color: #e7d3a1 !important;
	background: rgba(197, 160, 89, 0.12) !important;
	outline: none !important;
}
body.woocommerce-account .sc-password-toggle .material-symbols-outlined {
	font-size: 1.35rem !important;
	line-height: 1 !important;
}
/* Esconde o olho nativo do WooCommerce (quase invisível no dark). */
body.woocommerce-account .woocommerce-form-login .show-password-input,
body.woocommerce-account .woocommerce-form-register .show-password-input,
body.woocommerce-account .woocommerce-form-login .password-input > .display-password,
body.woocommerce-account .woocommerce-form-login .password-input::after {
	display: none !important;
}
body.woocommerce-account .woocommerce-form-login .button,
body.woocommerce-account .woocommerce-form-login button[type="submit"],
body.woocommerce-account .woocommerce-form-register .button {
	background: #C5A059 !important;
	border: 0 !important;
	color: #050A14 !important;
	font-weight: 700 !important;
	border-radius: 8px !important;
	padding: 0.7rem 1.25rem !important;
	font-family: Inter, sans-serif !important;
}
body.woocommerce-account .woocommerce-form-login a,
body.woocommerce-account .lost_password a {
	color: #C5A059 !important;
}
body.woocommerce-account .woocommerce-form-login-toggle,
body.woocommerce-account .woocommerce-LostPassword {
	color: #9AA3AD !important;
}
body.woocommerce-account .wc-block-components-notice-banner.is-error,
body.woocommerce-account .wc-block-components-notice-banner {
	background: rgba(127, 29, 29, 0.35) !important;
	border: 1px solid rgba(248, 113, 113, 0.45) !important;
	color: #fecaca !important;
	font-family: Inter, system-ui, sans-serif !important;
}
body.woocommerce-account .wc-block-components-notice-banner.is-success {
	background: rgba(6, 78, 59, 0.35) !important;
	border-color: rgba(52, 211, 153, 0.35) !important;
	color: #a7f3d0 !important;
}
';
	wp_add_inline_style( 'saulocoelho-style', $css );
}
add_action( 'wp_enqueue_scripts', 'saulocoelho_account_notices_styles', 40 );
