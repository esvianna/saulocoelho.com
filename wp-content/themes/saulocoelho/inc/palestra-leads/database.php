<?php
/**
 * Tabela e persistência — leads da palestra.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function sc_palestra_table_name() {
	global $wpdb;
	return $wpdb->prefix . 'sc_palestra_leads';
}

function sc_palestra_install_table() {
	global $wpdb;

	$table   = sc_palestra_table_name();
	$charset = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE {$table} (
		id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		name varchar(190) NOT NULL,
		email varchar(190) NOT NULL,
		whatsapp varchar(32) NOT NULL DEFAULT '',
		consent_at datetime NOT NULL,
		source varchar(64) NOT NULL DEFAULT '',
		event_id bigint(20) unsigned NOT NULL DEFAULT 0,
		extras_json longtext NULL,
		token varchar(64) NOT NULL,
		token_expires_at datetime NOT NULL,
		download_count int(10) unsigned NOT NULL DEFAULT 0,
		emailed_at datetime NULL,
		created_at datetime NOT NULL,
		updated_at datetime NOT NULL,
		PRIMARY KEY  (id),
		UNIQUE KEY token (token),
		KEY email (email),
		KEY source (source),
		KEY event_id (event_id),
		KEY created_at (created_at)
	) {$charset};";

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	dbDelta( $sql );
	update_option( 'sc_palestra_leads_db_version', SC_PALESTRA_LEADS_DB_VERSION );
}

function sc_palestra_maybe_install_table() {
	$installed = get_option( 'sc_palestra_leads_db_version', '' );
	if ( $installed === SC_PALESTRA_LEADS_DB_VERSION ) {
		return;
	}
	sc_palestra_install_table();
}

function sc_palestra_pdf_path() {
	$event = sc_palestra_current_event();
	if ( $event ) {
		$files = get_post_meta( $event->ID, '_sc_palestra_files', true );
		if ( is_array( $files ) && ! empty( $files[0]['id'] ) ) {
			$path = sc_palestra_file_path( $event->ID, $files[0]['id'] );
			if ( $path && is_readable( $path ) ) {
				return $path;
			}
		}
	}
	$path = get_template_directory() . '/private/palestra-o-comportamento-decide-teresopolis.pdf';
	return apply_filters( 'sc_palestra_pdf_path', $path );
}

function sc_palestra_pdf_ready() {
	$path = sc_palestra_pdf_path();
	return is_readable( $path ) && filesize( $path ) > 0;
}

function sc_palestra_public_url() {
	$page = sc_palestra_get_page();
	if ( $page ) {
		return get_permalink( $page );
	}
	return home_url( '/' . SC_PALESTRA_PAGE_SLUG . '/' );
}

function sc_palestra_get_page() {
	$page = get_page_by_path( SC_PALESTRA_PAGE_SLUG );
	return ( $page instanceof WP_Post ) ? $page : null;
}

function sc_palestra_maybe_create_page() {
	$existing = sc_palestra_get_page();
	if ( $existing ) {
		$template = get_page_template_slug( $existing );
		if ( $template !== 'page-palestra.php' ) {
			update_post_meta( $existing->ID, '_wp_page_template', 'page-palestra.php' );
		}
		return;
	}

	$id = wp_insert_post(
		array(
			'post_title'  => __( 'O Comportamento Decide', 'saulocoelho' ),
			'post_name'   => SC_PALESTRA_PAGE_SLUG,
			'post_status' => 'publish',
			'post_type'   => 'page',
			'post_content'=> '',
		),
		true
	);

	if ( is_wp_error( $id ) || ! $id ) {
		return;
	}

	update_post_meta( $id, '_wp_page_template', 'page-palestra.php' );
	flush_rewrite_rules( false );
}

/**
 * @return object|null
 */
function sc_palestra_get_lead_by_email( $email, $source = '' ) {
	global $wpdb;
	$table = sc_palestra_table_name();
	if ( $source === '' ) {
		$row = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM {$table} WHERE email = %s ORDER BY id DESC LIMIT 1",
				$email
			)
		);
		return $row ?: null;
	}
	$row = $wpdb->get_row(
		$wpdb->prepare(
			"SELECT * FROM {$table} WHERE email = %s AND source = %s ORDER BY id DESC LIMIT 1",
			$email,
			$source
		)
	);
	return $row ?: null;
}

/**
 * @return object|null
 */
function sc_palestra_get_lead_by_token( $token ) {
	global $wpdb;
	$table = sc_palestra_table_name();
	$row   = $wpdb->get_row(
		$wpdb->prepare(
			"SELECT * FROM {$table} WHERE token = %s LIMIT 1",
			$token
		)
	);
	return $row ?: null;
}

function sc_palestra_new_token() {
	return bin2hex( random_bytes( 32 ) );
}

function sc_palestra_token_ttl() {
	return (int) apply_filters( 'sc_palestra_token_ttl', 2 * DAY_IN_SECONDS );
}

/**
 * @param array<string, string> $extras
 * @return array{id:int,token:string}|WP_Error
 */
function sc_palestra_upsert_lead( $event_id, $name, $email, $whatsapp, $source, $extras = array() ) {
	global $wpdb;

	$now      = current_time( 'mysql' );
	$token    = sc_palestra_new_token();
	$expires  = date( 'Y-m-d H:i:s', current_time( 'timestamp' ) + sc_palestra_token_ttl() );
	$existing = sc_palestra_get_lead_by_email( $email, $source );
	$json     = ! empty( $extras ) ? wp_json_encode( $extras, JSON_UNESCAPED_UNICODE ) : null;

	if ( $existing ) {
		$updated = $wpdb->update(
			sc_palestra_table_name(),
			array(
				'name'             => $name,
				'whatsapp'         => $whatsapp,
				'consent_at'       => $now,
				'event_id'         => (int) $event_id,
				'extras_json'      => $json,
				'token'            => $token,
				'token_expires_at' => $expires,
				'download_count'   => 0,
				'updated_at'       => $now,
			),
			array( 'id' => (int) $existing->id ),
			array( '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%d', '%s' ),
			array( '%d' )
		);
		if ( false === $updated ) {
			return new WP_Error( 'sc_palestra_db', __( 'Não foi possível atualizar o cadastro.', 'saulocoelho' ) );
		}
		return array(
			'id'    => (int) $existing->id,
			'token' => $token,
		);
	}

	$inserted = $wpdb->insert(
		sc_palestra_table_name(),
		array(
			'name'             => $name,
			'email'            => $email,
			'whatsapp'         => $whatsapp,
			'consent_at'       => $now,
			'source'           => $source,
			'event_id'         => (int) $event_id,
			'extras_json'      => $json,
			'token'            => $token,
			'token_expires_at' => $expires,
			'download_count'   => 0,
			'created_at'       => $now,
			'updated_at'       => $now,
		),
		array( '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%d', '%s', '%s' )
	);

	if ( ! $inserted ) {
		return new WP_Error( 'sc_palestra_db', __( 'Não foi possível gravar o cadastro.', 'saulocoelho' ) );
	}

	return array(
		'id'    => (int) $wpdb->insert_id,
		'token' => $token,
	);
}

function sc_palestra_mark_emailed( $lead_id ) {
	global $wpdb;
	$wpdb->update(
		sc_palestra_table_name(),
		array(
			'emailed_at' => current_time( 'mysql' ),
			'updated_at' => current_time( 'mysql' ),
		),
		array( 'id' => (int) $lead_id ),
		array( '%s', '%s' ),
		array( '%d' )
	);
}

function sc_palestra_increment_download( $lead_id ) {
	global $wpdb;
	$table = sc_palestra_table_name();
	$wpdb->query(
		$wpdb->prepare(
			"UPDATE {$table} SET download_count = download_count + 1, updated_at = %s WHERE id = %d",
			current_time( 'mysql' ),
			(int) $lead_id
		)
	);
}

/**
 * @return array<int, object>
 */
function sc_palestra_query_leads( $search = '', $event_id = 0, $limit = 500 ) {
	global $wpdb;
	$table = sc_palestra_table_name();
	$limit = max( 1, min( 2000, (int) $limit ) );
	$where = '1=1';
	$args  = array();

	if ( $event_id ) {
		$where .= ' AND event_id = %d';
		$args[] = (int) $event_id;
	}
	if ( $search !== '' ) {
		$like   = '%' . $wpdb->esc_like( $search ) . '%';
		$where .= ' AND (name LIKE %s OR email LIKE %s OR whatsapp LIKE %s)';
		$args[] = $like;
		$args[] = $like;
		$args[] = $like;
	}
	$args[] = $limit;
	$sql    = "SELECT * FROM {$table} WHERE {$where} ORDER BY id DESC LIMIT %d";
	return $wpdb->get_results( $wpdb->prepare( $sql, $args ) );
}

function sc_palestra_count_leads( $event_id = 0 ) {
	global $wpdb;
	$table = sc_palestra_table_name();
	if ( $event_id ) {
		return (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(*) FROM {$table} WHERE event_id = %d",
				(int) $event_id
			)
		);
	}
	return (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table}" );
}
