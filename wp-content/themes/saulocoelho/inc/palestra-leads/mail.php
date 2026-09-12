<?php
/**
 * E-mail com ficheiros ou links tokenizados.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function sc_palestra_mail_from_address() {
	$email = get_theme_mod( 'footer_email', 'contato@saulocoelho.com' );
	$email = sanitize_email( (string) $email );
	if ( ! is_email( $email ) ) {
		$email = 'contato@saulocoelho.com';
	}
	return $email;
}

function sc_palestra_mail_from_name() {
	$name = trim( wp_strip_all_tags( wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ) ) );
	return $name !== '' ? $name : 'Saulo Coelho';
}

function sc_palestra_download_url( $token, $event_id = 0, $file_id = '' ) {
	$base = $event_id ? sc_palestra_event_public_url( $event_id ) : sc_palestra_public_url();
	$args = array( 'sc_palestra_dl' => $token );
	if ( $file_id !== '' ) {
		$args['sc_palestra_file'] = $file_id;
	}
	return add_query_arg( $args, $base );
}

/**
 * @return bool
 */
function sc_palestra_send_lead_email( $event_id, $name, $email, $token ) {
	$config   = sc_palestra_event_config( $event_id );
	$hours    = (int) max( 1, sc_palestra_token_ttl() / HOUR_IN_SECONDS );
	$subject  = sprintf( __( 'Seu material — %s', 'saulocoelho' ), $config['title'] );
	$intro    = $config['email_intro'] !== '' ? $config['email_intro'] : __( 'Segue o material da palestra.', 'saulocoelho' );

	$lines = array(
		sprintf( __( 'Olá, %s.', 'saulocoelho' ), $name ),
		'',
		$intro,
		'',
	);

	$attach = array();
	$total  = 0;
	foreach ( $config['files'] as $file ) {
		$path = sc_palestra_file_path( $event_id, $file['id'] );
		if ( ! $path || ! is_readable( $path ) ) {
			continue;
		}
		$url    = sc_palestra_download_url( $token, $event_id, $file['id'] );
		$lines[] = $file['name'] . ': ' . $url;
		$size    = filesize( $path );
		if ( $total + $size <= SC_PALESTRA_MAX_ATTACH_BYTES ) {
			$attach[] = $path;
			$total   += $size;
		}
	}

	$lines[] = '';
	$lines[] = sprintf(
		__( 'Os links são válidos por %d horas.', 'saulocoelho' ),
		$hours
	);
	$lines[] = '';
	$lines[] = $config['thanks_note'];
	$lines[] = '';
	$lines[] = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );

	$body       = implode( "\n", $lines );
	$from_email = sc_palestra_mail_from_address();
	$from_name  = sc_palestra_mail_from_name();
	$headers    = array(
		'Content-Type: text/plain; charset=UTF-8',
		sprintf( 'From: %s <%s>', $from_name, $from_email ),
		sprintf( 'Reply-To: %s', $from_email ),
	);

	$from_filter = static function () use ( $from_email ) {
		return $from_email;
	};
	$name_filter = static function () use ( $from_name ) {
		return $from_name;
	};

	add_filter( 'wp_mail_from', $from_filter, 999 );
	add_filter( 'wp_mail_from_name', $name_filter, 999 );

	$sent = wp_mail( $email, $subject, $body, $headers, $attach );
	if ( ! $sent && ! empty( $attach ) ) {
		$sent = wp_mail( $email, $subject, $body, $headers );
	}

	remove_filter( 'wp_mail_from', $from_filter, 999 );
	remove_filter( 'wp_mail_from_name', $name_filter, 999 );

	return (bool) $sent;
}
