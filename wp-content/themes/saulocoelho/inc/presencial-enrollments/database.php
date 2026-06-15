<?php
/**
 * Tabela de inscrições presenciais.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function sc_presencial_install_table() {
	sc_forms_install_tables();
	sc_forms_maybe_seed_and_migrate();
	sc_presencial_maybe_repair_not_required_status();
	sc_presencial_maybe_repair_duplicate_questionnaires();
	update_option( 'sc_presencial_db_version', SC_PRESENCIAL_DB_VERSION );
}

/**
 * Inscrição completa mais recente do aluno para o produto (questionário já enviado).
 *
 * @return object|null
 */
function sc_presencial_get_user_completed_enrollment_for_product( $user_id, $product_id ) {
	global $wpdb;

	return $wpdb->get_row(
		$wpdb->prepare(
			'SELECT * FROM ' . sc_presencial_table_name() . ' WHERE user_id = %d AND product_id = %d AND form_status = %s ORDER BY updated_at DESC LIMIT 1',
			absint( $user_id ),
			absint( $product_id ),
			'complete'
		)
	);
}

/**
 * Uma linha por produto na UI do aluno (evita duplicata por pedidos repetidos).
 *
 * @param array<int, object> $rows
 * @return array<int, object>
 */
function sc_presencial_dedupe_questionnaire_enrollments_by_product( array $rows ) {
	$by_product = array();

	foreach ( $rows as $row ) {
		$pid = (int) $row->product_id;
		if ( ! isset( $by_product[ $pid ] ) ) {
			$by_product[ $pid ] = $row;
			continue;
		}

		$existing = $by_product[ $pid ];
		if ( $existing->form_status !== 'complete' && $row->form_status === 'complete' ) {
			$by_product[ $pid ] = $row;
			continue;
		}
		if ( $existing->form_status === 'complete' ) {
			continue;
		}
		if ( strtotime( (string) $row->created_at ) > strtotime( (string) $existing->created_at ) ) {
			$by_product[ $pid ] = $row;
		}
	}

	return array_values( $by_product );
}

/**
 * Propaga respostas completas para outras inscrições do mesmo aluno/produto.
 */
function sc_presencial_propagate_questionnaire_to_siblings( $enrollment_id ) {
	global $wpdb;

	$enrollment = sc_presencial_get_enrollment( $enrollment_id );
	if ( ! $enrollment || $enrollment->form_status !== 'complete' ) {
		return;
	}

	$wpdb->update(
		sc_presencial_table_name(),
		array(
			'form_status'        => 'complete',
			'responses_json'     => $enrollment->responses_json,
			'form_snapshot_json' => $enrollment->form_snapshot_json,
			'form_id'            => (int) $enrollment->form_id,
			'form_version'       => (int) $enrollment->form_version,
			'updated_at'         => current_time( 'mysql' ),
		),
		array(
			'user_id'     => (int) $enrollment->user_id,
			'product_id'  => (int) $enrollment->product_id,
			'form_status' => 'pending',
		),
		array( '%s', '%s', '%s', '%d', '%d', '%s' ),
		array( '%d', '%d', '%s' )
	);
}

/**
 * Corrige inscrições presenciais legadas gravadas com form_status incorreto.
 */
function sc_presencial_maybe_repair_not_required_status() {
	if ( get_option( 'sc_presencial_repair_not_required' ) ) {
		return;
	}

	global $wpdb;
	$table = sc_presencial_table_name();
	$wpdb->query(
		$wpdb->prepare(
			"UPDATE {$table} SET form_status = 'pending' WHERE form_status = 'not_required' AND form_schema_version = %s",
			SC_PRESENCIAL_FORM_SCHEMA
		)
	);

	update_option( 'sc_presencial_repair_not_required', 1 );
}

/**
 * Sincroniza questionários pendentes quando o aluno já respondeu o mesmo produto em outro pedido.
 */
function sc_presencial_maybe_repair_duplicate_questionnaires() {
	if ( get_option( 'sc_presencial_repair_duplicate_questionnaires' ) ) {
		return;
	}

	global $wpdb;
	$table = sc_presencial_table_name();

	$pairs = $wpdb->get_results(
		"SELECT DISTINCT user_id, product_id FROM {$table} WHERE form_status = 'complete'"
	);

	foreach ( $pairs as $pair ) {
		$source = sc_presencial_get_user_completed_enrollment_for_product( (int) $pair->user_id, (int) $pair->product_id );
		if ( ! $source ) {
			continue;
		}

		$wpdb->update(
			$table,
			array(
				'form_status'        => 'complete',
				'responses_json'     => $source->responses_json,
				'form_snapshot_json' => $source->form_snapshot_json,
				'form_id'            => (int) $source->form_id,
				'form_version'       => (int) $source->form_version,
				'updated_at'         => current_time( 'mysql' ),
			),
			array(
				'user_id'     => (int) $pair->user_id,
				'product_id'  => (int) $pair->product_id,
				'form_status' => 'pending',
			),
			array( '%s', '%s', '%s', '%d', '%d', '%s' ),
			array( '%d', '%d', '%s' )
		);
	}

	update_option( 'sc_presencial_repair_duplicate_questionnaires', 1 );
}

function sc_presencial_maybe_install_table() {
	if ( get_option( 'sc_presencial_db_version' ) !== SC_PRESENCIAL_DB_VERSION ) {
		sc_presencial_install_table();
	}
	sc_presencial_maybe_repair_not_required_status();
	sc_presencial_maybe_repair_duplicate_questionnaires();
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
 * Status inicial do questionário para o produto.
 */
function sc_presencial_initial_form_status( $product_id ) {
	if ( sc_forms_product_has_form( $product_id ) || sc_presencial_is_presencial_product( $product_id ) ) {
		return 'pending';
	}
	return 'not_required';
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

	if ( ! sc_presencial_product_needs_enrollment( $product_id ) ) {
		return false;
	}

	$existing = sc_presencial_get_enrollment_by_order_product( $order_id, $product_id );
	$now      = current_time( 'mysql' );

	if ( $existing ) {
		return (int) $existing->id;
	}

	$form_id            = sc_forms_product_form_id( $product_id );
	$form               = $form_id ? sc_forms_get_form( $form_id ) : null;
	$form_status        = sc_presencial_initial_form_status( $product_id );
	$responses_json     = null;
	$form_snapshot_json = null;
	$form_version       = $form ? (int) $form->version : 0;
	$form_schema        = $form ? $form->schema_slug : ( sc_presencial_is_presencial_product( $product_id ) ? SC_PRESENCIAL_FORM_SCHEMA : '' );

	$prior_complete = sc_presencial_get_user_completed_enrollment_for_product( $user_id, $product_id );
	if ( $prior_complete ) {
		$form_status        = 'complete';
		$responses_json     = $prior_complete->responses_json;
		$form_snapshot_json = $prior_complete->form_snapshot_json;
		$form_id            = (int) $prior_complete->form_id ?: $form_id;
		$form_version       = (int) $prior_complete->form_version ?: $form_version;
		if ( ! empty( $prior_complete->form_schema_version ) ) {
			$form_schema = $prior_complete->form_schema_version;
		}
	}

	$wpdb->insert(
		sc_presencial_table_name(),
		array(
			'user_id'             => $user_id,
			'order_id'            => $order_id,
			'product_id'          => $product_id,
			'form_id'             => $form_id,
			'form_version'        => $form_version,
			'form_schema_version' => $form_schema,
			'form_snapshot_json'  => $form_snapshot_json,
			'form_status'         => $form_status,
			'attendance_status'   => 'unknown',
			'responses_json'      => $responses_json,
			'created_at'          => $now,
			'updated_at'          => $now,
		),
		array( '%d', '%d', '%d', '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s' )
	);

	$enrollment_id = (int) $wpdb->insert_id;

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

/**
 * Inscrições com questionário ativo para o aluno.
 *
 * @return array<int, object>
 */
function sc_presencial_get_user_questionnaire_enrollments( $user_id ) {
	$rows = sc_presencial_get_user_enrollments( $user_id );
	$rows = array_values(
		array_filter(
			$rows,
			static function ( $row ) {
				return sc_presencial_enrollment_has_questionnaire( $row )
					&& in_array( $row->form_status, array( 'pending', 'complete' ), true );
			}
		)
	);

	return sc_presencial_dedupe_questionnaire_enrollments_by_product( $rows );
}

function sc_presencial_save_responses( $enrollment_id, array $responses, array $schema = null ) {
	global $wpdb;

	$enrollment_id = absint( $enrollment_id );
	if ( ! $enrollment_id ) {
		return false;
	}

	$enrollment = sc_presencial_get_enrollment( $enrollment_id );
	if ( ! $enrollment ) {
		return false;
	}

	$json = wp_json_encode( $responses, JSON_UNESCAPED_UNICODE );
	if ( ! $json ) {
		return false;
	}

	$update = array(
		'responses_json' => $json,
		'form_status'    => 'complete',
		'updated_at'     => current_time( 'mysql' ),
	);

	if ( empty( $enrollment->form_snapshot_json ) ) {
		if ( ! $schema ) {
			$schema = sc_forms_get_schema_for_enrollment( $enrollment );
		}
		$snapshot = sc_forms_create_snapshot( $schema );
		$snap_json = wp_json_encode( $snapshot, JSON_UNESCAPED_UNICODE );
		if ( $snap_json ) {
			$update['form_snapshot_json'] = $snap_json;
		}
		if ( ! empty( $schema['meta']['form_id'] ) ) {
			$update['form_id']      = (int) $schema['meta']['form_id'];
			$update['form_version'] = (int) ( $schema['meta']['form_version'] ?? 0 );
		}
	}

	$formats = array();
	foreach ( $update as $key => $val ) {
		if ( in_array( $key, array( 'form_id', 'form_version' ), true ) ) {
			$formats[] = '%d';
		} else {
			$formats[] = '%s';
		}
	}

	$ok = (bool) $wpdb->update(
		sc_presencial_table_name(),
		$update,
		array( 'id' => $enrollment_id ),
		$formats,
		array( '%d' )
	);

	if ( $ok ) {
		sc_presencial_propagate_questionnaire_to_siblings( $enrollment_id );
	}

	return $ok;
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
