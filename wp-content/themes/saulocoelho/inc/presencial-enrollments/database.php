<?php
/**
 * Tabela de inscrições presenciais.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function sc_presencial_install_table() {
	global $wpdb;

	$table   = sc_presencial_table_name();
	$charset = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE {$table} (
		id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		user_id bigint(20) unsigned NOT NULL,
		order_id bigint(20) unsigned NOT NULL,
		product_id bigint(20) unsigned NOT NULL,
		form_schema_version varchar(64) NOT NULL DEFAULT 'coaching-terapia-2026-07',
		form_status varchar(20) NOT NULL DEFAULT 'pending',
		attendance_status varchar(20) NOT NULL DEFAULT 'unknown',
		responses_json longtext NULL,
		created_at datetime NOT NULL,
		updated_at datetime NOT NULL,
		PRIMARY KEY  (id),
		UNIQUE KEY order_product (order_id, product_id),
		KEY user_id (user_id),
		KEY product_id (product_id),
		KEY form_status (form_status)
	) {$charset};";

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	dbDelta( $sql );

	update_option( 'sc_presencial_db_version', SC_PRESENCIAL_DB_VERSION );
}

function sc_presencial_maybe_install_table() {
	if ( get_option( 'sc_presencial_db_version' ) !== SC_PRESENCIAL_DB_VERSION ) {
		sc_presencial_install_table();
	}
}

/**
 * @return object|null
 */
function sc_presencial_get_enrollment( $id ) {
	global $wpdb;
	$id = absint( $id );
	if ( ! $id ) {
		return null;
	}
	return $wpdb->get_row(
		$wpdb->prepare( 'SELECT * FROM ' . sc_presencial_table_name() . ' WHERE id = %d', $id )
	);
}

/**
 * @return object|null
 */
function sc_presencial_get_enrollment_by_order_product( $order_id, $product_id ) {
	global $wpdb;
	return $wpdb->get_row(
		$wpdb->prepare(
			'SELECT * FROM ' . sc_presencial_table_name() . ' WHERE order_id = %d AND product_id = %d',
			absint( $order_id ),
			absint( $product_id )
		)
	);
}

/**
 * @return int|false ID da inscrição.
 */
function sc_presencial_upsert_enrollment( $user_id, $order_id, $product_id ) {
	global $wpdb;

	$user_id    = absint( $user_id );
	$order_id   = absint( $order_id );
	$product_id = absint( $product_id );

	if ( ! $user_id || ! $order_id || ! $product_id ) {
		return false;
	}

	$existing = sc_presencial_get_enrollment_by_order_product( $order_id, $product_id );
	$now      = current_time( 'mysql' );

	if ( $existing ) {
		return (int) $existing->id;
	}

	$wpdb->insert(
		sc_presencial_table_name(),
		array(
			'user_id'             => $user_id,
			'order_id'            => $order_id,
			'product_id'          => $product_id,
			'form_schema_version' => SC_PRESENCIAL_FORM_SCHEMA,
			'form_status'         => 'pending',
			'attendance_status'   => 'unknown',
			'responses_json'      => null,
			'created_at'          => $now,
			'updated_at'          => $now,
		),
		array( '%d', '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s' )
	);

	$enrollment_id = (int) $wpdb->insert_id;

	/**
	 * Permite integração futura com AmaEducacional (matrícula em ama_course).
	 */
	do_action( 'sc_presencial_enrollment_created', $enrollment_id, $user_id, $order_id, $product_id );

	return $enrollment_id ?: false;
}

/**
 * @return array<int, object>
 */
function sc_presencial_get_user_enrollments( $user_id, $form_status = null ) {
	global $wpdb;
	$user_id = absint( $user_id );
	if ( ! $user_id ) {
		return array();
	}

	$sql  = 'SELECT * FROM ' . sc_presencial_table_name() . ' WHERE user_id = %d';
	$args = array( $user_id );

	if ( $form_status ) {
		$sql   .= ' AND form_status = %s';
		$args[] = $form_status;
	}

	$sql .= ' ORDER BY created_at DESC';

	return $wpdb->get_results( $wpdb->prepare( $sql, $args ) );
}

function sc_presencial_save_responses( $enrollment_id, array $responses ) {
	global $wpdb;

	$enrollment_id = absint( $enrollment_id );
	if ( ! $enrollment_id ) {
		return false;
	}

	$json = wp_json_encode( $responses, JSON_UNESCAPED_UNICODE );
	if ( ! $json ) {
		return false;
	}

	return (bool) $wpdb->update(
		sc_presencial_table_name(),
		array(
			'responses_json' => $json,
			'form_status'    => 'complete',
			'updated_at'     => current_time( 'mysql' ),
		),
		array( 'id' => $enrollment_id ),
		array( '%s', '%s', '%s' ),
		array( '%d' )
	);
}

function sc_presencial_update_attendance( $enrollment_id, $status ) {
	global $wpdb;

	$allowed = array( 'unknown', 'present', 'absent' );
	if ( ! in_array( $status, $allowed, true ) ) {
		return false;
	}

	return (bool) $wpdb->update(
		sc_presencial_table_name(),
		array(
			'attendance_status' => $status,
			'updated_at'        => current_time( 'mysql' ),
		),
		array( 'id' => absint( $enrollment_id ) ),
		array( '%s', '%s' ),
		array( '%d' )
	);
}

/**
 * @return array<int, object>
 */
function sc_presencial_query_enrollments( $args = array() ) {
	global $wpdb;

	$defaults = array(
		'product_id' => 0,
		'search'     => '',
		'limit'      => 200,
		'offset'     => 0,
	);
	$args     = wp_parse_args( $args, $defaults );

	$sql  = 'SELECT e.* FROM ' . sc_presencial_table_name() . ' e WHERE 1=1';
	$bind = array();

	if ( $args['product_id'] ) {
		$sql    .= ' AND e.product_id = %d';
		$bind[]  = absint( $args['product_id'] );
	}

	if ( $args['search'] !== '' ) {
		$like    = '%' . $wpdb->esc_like( $args['search'] ) . '%';
		$sql    .= ' AND e.user_id IN (SELECT ID FROM ' . $wpdb->users . ' WHERE display_name LIKE %s OR user_email LIKE %s)';
		$bind[]  = $like;
		$bind[]  = $like;
	}

	$sql    .= ' ORDER BY e.created_at DESC LIMIT %d OFFSET %d';
	$bind[]  = absint( $args['limit'] );
	$bind[]  = absint( $args['offset'] );

	if ( empty( $bind ) ) {
		return $wpdb->get_results( $sql );
	}

	return $wpdb->get_results( $wpdb->prepare( $sql, $bind ) );
}
