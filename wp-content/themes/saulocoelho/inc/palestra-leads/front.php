<?php
/**
 * Front: formulário AJAX, rate limit e entrega dos ficheiros.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function sc_palestra_privacy_url() {
	$url = function_exists( 'get_privacy_policy_url' ) ? get_privacy_policy_url() : '';
	if ( $url ) {
		return $url;
	}
	$mod = get_theme_mod( 'footer_privacy_link', '' );
	return ( $mod && $mod !== '#' ) ? $mod : home_url( '/' );
}

function sc_palestra_normalize_whatsapp( $raw ) {
	$digits = preg_replace( '/\D+/', '', (string) $raw );
	$len    = strlen( $digits );
	if ( $len >= 10 && $len <= 13 ) {
		return $digits;
	}
	return '';
}

function sc_palestra_client_ip() {
	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	return $ip !== '' ? $ip : '0.0.0.0';
}

function sc_palestra_rate_limited() {
	$key   = 'sc_palestra_rl_' . md5( sc_palestra_client_ip() );
	$count = (int) get_transient( $key );
	if ( $count >= 8 ) {
		return true;
	}
	set_transient( $key, $count + 1, 15 * MINUTE_IN_SECONDS );
	return false;
}

function sc_palestra_enqueue_assets() {
	if ( ! is_page_template( 'page-palestra.php' ) ) {
		return;
	}

	$rel  = '/assets/js/palestra-leads.js';
	$path = get_template_directory() . $rel;
	$ver  = file_exists( $path ) ? (string) filemtime( $path ) : wp_get_theme()->get( 'Version' );

	wp_enqueue_script(
		'sc-palestra-leads',
		get_template_directory_uri() . $rel,
		array(),
		$ver,
		true
	);

	wp_localize_script(
		'sc-palestra-leads',
		'scPalestra',
		array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'sc_palestra_submit' ),
			'i18n'    => array(
				'generic' => __( 'Não foi possível enviar. Tente novamente.', 'saulocoelho' ),
			),
		)
	);
}

function sc_palestra_maybe_404() {
	if ( ! is_page_template( 'page-palestra.php' ) ) {
		return;
	}
	if ( ! empty( $_GET['sc_palestra_dl'] ) ) {
		return;
	}
	if ( ! sc_palestra_current_event() ) {
		global $wp_query;
		$wp_query->set_404();
		status_header( 404 );
		nocache_headers();
		include get_query_template( '404' );
		exit;
	}
}

function sc_palestra_published_event( $event_id ) {
	$post = get_post( absint( $event_id ) );
	if ( ! $post || $post->post_type !== SC_PALESTRA_CPT || $post->post_status !== 'publish' ) {
		return null;
	}
	return $post;
}

function sc_palestra_ajax_submit() {
	check_ajax_referer( 'sc_palestra_submit', 'security' );

	$honeypot = isset( $_POST['website'] ) ? trim( (string) wp_unslash( $_POST['website'] ) ) : '';
	if ( $honeypot !== '' ) {
		wp_send_json_error( array( 'message' => __( 'Não foi possível enviar. Tente novamente.', 'saulocoelho' ) ) );
	}

	if ( sc_palestra_rate_limited() ) {
		wp_send_json_error( array( 'message' => __( 'Muitas tentativas. Aguarde alguns minutos.', 'saulocoelho' ) ) );
	}

	$event_id = isset( $_POST['event_id'] ) ? absint( $_POST['event_id'] ) : 0;
	$event    = sc_palestra_published_event( $event_id );
	if ( ! $event ) {
		wp_send_json_error( array( 'message' => __( 'Palestra inválida.', 'saulocoelho' ) ) );
	}

	$config = sc_palestra_event_config( $event->ID );
	if ( ! sc_palestra_event_has_files( $event->ID ) ) {
		wp_send_json_error( array( 'message' => __( 'O material ainda não está disponível. Tente mais tarde.', 'saulocoelho' ) ) );
	}

	$name     = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
	$email    = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$whatsapp = '';
	$consent  = ! empty( $_POST['consent'] );

	if ( strlen( $name ) < 2 ) {
		wp_send_json_error( array( 'message' => __( 'Informe o seu nome.', 'saulocoelho' ) ) );
	}
	if ( ! is_email( $email ) ) {
		wp_send_json_error( array( 'message' => __( 'Informe um e-mail válido.', 'saulocoelho' ) ) );
	}
	if ( $config['ask_whatsapp'] === '1' ) {
		$whatsapp = isset( $_POST['whatsapp'] ) ? sc_palestra_normalize_whatsapp( wp_unslash( $_POST['whatsapp'] ) ) : '';
		if ( $whatsapp === '' ) {
			wp_send_json_error( array( 'message' => __( 'Informe um WhatsApp válido com DDD.', 'saulocoelho' ) ) );
		}
	}
	if ( ! $consent ) {
		wp_send_json_error( array( 'message' => __( 'É necessário autorizar o uso dos dados para baixar o material.', 'saulocoelho' ) ) );
	}

	$extra_in = isset( $_POST['extra'] ) && is_array( $_POST['extra'] ) ? wp_unslash( $_POST['extra'] ) : array();
	$extras   = array();
	foreach ( $config['extras'] as $field ) {
		$key = $field['key'];
		$val = isset( $extra_in[ $key ] ) ? sanitize_text_field( $extra_in[ $key ] ) : '';
		if ( $field['type'] === 'email' && $val !== '' && ! is_email( $val ) ) {
			wp_send_json_error( array( 'message' => sprintf( __( 'E-mail inválido: %s', 'saulocoelho' ), $field['label'] ) ) );
		}
		if ( ! empty( $field['required'] ) && $val === '' ) {
			wp_send_json_error( array( 'message' => sprintf( __( 'Preencha: %s', 'saulocoelho' ), $field['label'] ) ) );
		}
		$extras[ $key ] = $val;
	}

	$saved = sc_palestra_upsert_lead( $event->ID, $name, $email, $whatsapp, $event->post_name, $extras );
	if ( is_wp_error( $saved ) ) {
		wp_send_json_error( array( 'message' => $saved->get_error_message() ) );
	}

	$sent = sc_palestra_send_lead_email( $event->ID, $name, $email, $saved['token'] );
	if ( $sent ) {
		sc_palestra_mark_emailed( $saved['id'] );
	}

	$downloads = array();
	foreach ( $config['files'] as $file ) {
		$path = sc_palestra_file_path( $event->ID, $file['id'] );
		if ( ! $path || ! is_readable( $path ) ) {
			continue;
		}
		$downloads[] = array(
			'label' => $file['name'],
			'url'   => sc_palestra_download_url( $saved['token'], $event->ID, $file['id'] ),
		);
	}

	wp_send_json_success(
		array(
			'downloads' => $downloads,
			'emailed'   => (bool) $sent,
			'email'     => $email,
		)
	);
}

function sc_palestra_maybe_serve_pdf() {
	if ( empty( $_GET['sc_palestra_dl'] ) ) {
		return;
	}

	$token = sanitize_text_field( wp_unslash( $_GET['sc_palestra_dl'] ) );
	$lead  = $token !== '' ? sc_palestra_get_lead_by_token( $token ) : null;

	if ( ! $lead ) {
		wp_die( esc_html__( 'Link inválido. Preencha o formulário novamente.', 'saulocoelho' ), '', array( 'response' => 403 ) );
	}

	$expires = strtotime( $lead->token_expires_at );
	if ( ! $expires || $expires < current_time( 'timestamp' ) ) {
		wp_die( esc_html__( 'Este link expirou. Preencha o formulário novamente para receber um novo.', 'saulocoelho' ), '', array( 'response' => 403 ) );
	}

	if ( (int) $lead->download_count >= SC_PALESTRA_MAX_DOWNLOADS ) {
		wp_die( esc_html__( 'Limite de downloads deste link atingido. Preencha o formulário novamente.', 'saulocoelho' ), '', array( 'response' => 403 ) );
	}

	$event_id = isset( $lead->event_id ) ? (int) $lead->event_id : 0;
	$files    = $event_id ? get_post_meta( $event_id, '_sc_palestra_files', true ) : array();
	$files    = is_array( $files ) ? $files : array();

	$file_id = isset( $_GET['sc_palestra_file'] ) ? sanitize_text_field( wp_unslash( $_GET['sc_palestra_file'] ) ) : '';
	$file    = $file_id !== '' ? sc_palestra_find_file( $event_id, $file_id ) : null;
	if ( ! $file && count( $files ) === 1 ) {
		$file = $files[0];
	}
	if ( ! $file && ! empty( $files[0] ) ) {
		$file = $files[0];
	}

	$path = $file ? sc_palestra_file_path( $event_id, $file['id'] ) : '';
	if ( ! $path || ! is_readable( $path ) ) {
		$legacy = get_template_directory() . '/private/palestra-o-comportamento-decide-teresopolis.pdf';
		if ( is_readable( $legacy ) ) {
			$path = $legacy;
			$file = array(
				'name' => 'O-Comportamento-Decide-Teresopolis.pdf',
				'mime' => 'application/pdf',
			);
		}
	}

	if ( ! $path || ! is_readable( $path ) ) {
		wp_die( esc_html__( 'O material não está disponível no momento.', 'saulocoelho' ), '', array( 'response' => 404 ) );
	}

	sc_palestra_increment_download( (int) $lead->id );

	$filename = isset( $file['name'] ) ? $file['name'] : basename( $path );
	$mime     = isset( $file['mime'] ) ? $file['mime'] : 'application/octet-stream';

	nocache_headers();
	header( 'Content-Type: ' . $mime );
	header( 'Content-Disposition: attachment; filename="' . sanitize_file_name( $filename ) . '"' );
	header( 'Content-Length: ' . (string) filesize( $path ) );
	header( 'X-Content-Type-Options: nosniff' );

	readfile( $path );
	exit;
}
