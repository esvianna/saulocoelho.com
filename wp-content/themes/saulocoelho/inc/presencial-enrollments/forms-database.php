<?php
/**
 * Tabelas e persistência — formulários pós-inscrição (CRUD).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function sc_forms_table_name() {
	global $wpdb;
	return $wpdb->prefix . 'sc_forms';
}

function sc_form_sections_table_name() {
	global $wpdb;
	return $wpdb->prefix . 'sc_form_sections';
}

function sc_form_fields_table_name() {
	global $wpdb;
	return $wpdb->prefix . 'sc_form_fields';
}

function sc_forms_install_tables() {
	global $wpdb;

	$charset = $wpdb->get_charset_collate();

	$sql_forms = 'CREATE TABLE ' . sc_forms_table_name() . " (
		id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		title varchar(255) NOT NULL,
		description text NULL,
		schema_slug varchar(64) NOT NULL,
		status varchar(20) NOT NULL DEFAULT 'active',
		version int(10) unsigned NOT NULL DEFAULT 1,
		created_at datetime NOT NULL,
		updated_at datetime NOT NULL,
		PRIMARY KEY  (id),
		UNIQUE KEY schema_slug (schema_slug),
		KEY status (status)
	) {$charset};";

	$sql_sections = 'CREATE TABLE ' . sc_form_sections_table_name() . " (
		id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		form_id bigint(20) unsigned NOT NULL,
		section_key varchar(64) NOT NULL,
		title varchar(255) NOT NULL,
		sort_order int(11) NOT NULL DEFAULT 0,
		PRIMARY KEY  (id),
		KEY form_id (form_id)
	) {$charset};";

	$sql_fields = 'CREATE TABLE ' . sc_form_fields_table_name() . " (
		id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		form_id bigint(20) unsigned NOT NULL,
		section_id bigint(20) unsigned NOT NULL,
		field_key varchar(64) NOT NULL,
		label varchar(500) NOT NULL,
		field_type varchar(32) NOT NULL DEFAULT 'text',
		required tinyint(1) NOT NULL DEFAULT 0,
		max_length int(10) unsigned NULL,
		options_json longtext NULL,
		show_if_json longtext NULL,
		other_field_key varchar(64) NULL,
		sort_order int(11) NOT NULL DEFAULT 0,
		PRIMARY KEY  (id),
		KEY form_id (form_id),
		KEY section_id (section_id)
	) {$charset};";

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	dbDelta( $sql_forms );
	dbDelta( $sql_sections );
	dbDelta( $sql_fields );

	sc_presencial_upgrade_enrollments_table();
}

/**
 * Colunas extras na tabela de inscrições (v2).
 */
function sc_presencial_upgrade_enrollments_table() {
	global $wpdb;

	$table   = sc_presencial_table_name();
	$charset = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE {$table} (
		id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		user_id bigint(20) unsigned NOT NULL,
		order_id bigint(20) unsigned NOT NULL,
		product_id bigint(20) unsigned NOT NULL,
		form_id bigint(20) unsigned NOT NULL DEFAULT 0,
		form_version int(10) unsigned NOT NULL DEFAULT 0,
		form_schema_version varchar(64) NOT NULL DEFAULT '',
		form_snapshot_json longtext NULL,
		form_status varchar(20) NOT NULL DEFAULT 'pending',
		attendance_status varchar(20) NOT NULL DEFAULT 'unknown',
		responses_json longtext NULL,
		created_at datetime NOT NULL,
		updated_at datetime NOT NULL,
		PRIMARY KEY  (id),
		UNIQUE KEY order_product (order_id, product_id),
		KEY user_id (user_id),
		KEY product_id (product_id),
		KEY form_id (form_id),
		KEY form_status (form_status)
	) {$charset};";

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	dbDelta( $sql );
}

/**
 * @return object|null
 */
function sc_forms_get_form( $form_id ) {
	global $wpdb;
	$form_id = absint( $form_id );
	if ( ! $form_id ) {
		return null;
	}
	return $wpdb->get_row(
		$wpdb->prepare( 'SELECT * FROM ' . sc_forms_table_name() . ' WHERE id = %d', $form_id )
	);
}

/**
 * @return array<int, object>
 */
function sc_forms_list_forms( $status = null ) {
	global $wpdb;
	$sql = 'SELECT * FROM ' . sc_forms_table_name() . ' WHERE 1=1';
	$args = array();
	if ( $status ) {
		$sql   .= ' AND status = %s';
		$args[] = $status;
	}
	$sql .= ' ORDER BY title ASC';
	if ( empty( $args ) ) {
		return $wpdb->get_results( $sql );
	}
	return $wpdb->get_results( $wpdb->prepare( $sql, $args ) );
}

/**
 * @return array<int, object>
 */
function sc_forms_get_sections( $form_id ) {
	global $wpdb;
	return $wpdb->get_results(
		$wpdb->prepare(
			'SELECT * FROM ' . sc_form_sections_table_name() . ' WHERE form_id = %d ORDER BY sort_order ASC, id ASC',
			absint( $form_id )
		)
	);
}

/**
 * @return array<int, object>
 */
function sc_forms_get_fields( $form_id ) {
	global $wpdb;
	return $wpdb->get_results(
		$wpdb->prepare(
			'SELECT * FROM ' . sc_form_fields_table_name() . ' WHERE form_id = %d ORDER BY sort_order ASC, id ASC',
			absint( $form_id )
		)
	);
}

/**
 * @return int|false
 */
function sc_forms_insert_form( array $data ) {
	global $wpdb;
	$now = current_time( 'mysql' );
	$ok  = $wpdb->insert(
		sc_forms_table_name(),
		array(
			'title'        => $data['title'],
			'description'  => $data['description'] ?? '',
			'schema_slug'  => $data['schema_slug'],
			'status'       => $data['status'] ?? 'active',
			'version'      => 1,
			'created_at'   => $now,
			'updated_at'   => $now,
		),
		array( '%s', '%s', '%s', '%s', '%d', '%s', '%s' )
	);
	return $ok ? (int) $wpdb->insert_id : false;
}

function sc_forms_update_form_meta( $form_id, array $data ) {
	global $wpdb;
	$row = array( 'updated_at' => current_time( 'mysql' ) );
	$fmt = array( '%s' );

	if ( isset( $data['title'] ) ) {
		$row['title'] = $data['title'];
		$fmt[]        = '%s';
	}
	if ( array_key_exists( 'description', $data ) ) {
		$row['description'] = $data['description'];
		$fmt[]              = '%s';
	}
	if ( isset( $data['status'] ) ) {
		$row['status'] = $data['status'];
		$fmt[]         = '%s';
	}
	if ( isset( $data['version'] ) ) {
		$row['version'] = absint( $data['version'] );
		$fmt[]          = '%d';
	}

	return (bool) $wpdb->update(
		sc_forms_table_name(),
		$row,
		array( 'id' => absint( $form_id ) ),
		$fmt,
		array( '%d' )
	);
}

function sc_forms_delete_form_structure( $form_id ) {
	global $wpdb;
	$form_id = absint( $form_id );
	$wpdb->delete( sc_form_fields_table_name(), array( 'form_id' => $form_id ), array( '%d' ) );
	$wpdb->delete( sc_form_sections_table_name(), array( 'form_id' => $form_id ), array( '%d' ) );
}

function sc_forms_save_structure( $form_id, array $sections, array $fields, $bump_version = true ) {
	global $wpdb;

	$form_id = absint( $form_id );
	if ( ! $form_id ) {
		return false;
	}

	sc_forms_delete_form_structure( $form_id );

	$section_map = array();
	foreach ( $sections as $i => $section ) {
		$key = sanitize_key( $section['section_key'] ?? $section['id'] ?? ( 'sec_' . ( $i + 1 ) ) );
		if ( $key === '' ) {
			$key = 'sec_' . ( $i + 1 );
		}
		$wpdb->insert(
			sc_form_sections_table_name(),
			array(
				'form_id'     => $form_id,
				'section_key' => $key,
				'title'       => sanitize_text_field( $section['title'] ?? '' ),
				'sort_order'  => isset( $section['sort_order'] ) ? (int) $section['sort_order'] : $i,
			),
			array( '%d', '%s', '%s', '%d' )
		);
		$section_map[ $key ] = (int) $wpdb->insert_id;
	}

	foreach ( $fields as $i => $field ) {
		$section_key = sanitize_key( $field['section'] ?? '' );
		$section_id  = $section_map[ $section_key ] ?? 0;
		if ( ! $section_id ) {
			continue;
		}

		$options = null;
		if ( ! empty( $field['options'] ) && is_array( $field['options'] ) ) {
			$options = wp_json_encode( $field['options'], JSON_UNESCAPED_UNICODE );
		}

		$show_if = null;
		if ( ! empty( $field['show_if'] ) && is_array( $field['show_if'] ) ) {
			$show_if = wp_json_encode( $field['show_if'], JSON_UNESCAPED_UNICODE );
		}

		$wpdb->insert(
			sc_form_fields_table_name(),
			array(
				'form_id'          => $form_id,
				'section_id'       => $section_id,
				'field_key'        => sanitize_key( $field['key'] ?? ( 'field_' . ( $i + 1 ) ) ),
				'label'            => sanitize_text_field( $field['label'] ?? '' ),
				'field_type'       => sanitize_key( $field['type'] ?? 'text' ),
				'required'         => ! empty( $field['required'] ) ? 1 : 0,
				'max_length'       => isset( $field['max_length'] ) ? absint( $field['max_length'] ) : null,
				'options_json'     => $options,
				'show_if_json'     => $show_if,
				'other_field_key'  => ! empty( $field['other_field'] ) ? sanitize_key( $field['other_field'] ) : null,
				'sort_order'       => isset( $field['sort_order'] ) ? (int) $field['sort_order'] : $i,
			),
			array( '%d', '%d', '%s', '%s', '%s', '%d', '%d', '%s', '%s', '%s', '%d' )
		);
	}

	if ( $bump_version ) {
		$form = sc_forms_get_form( $form_id );
		if ( $form ) {
			sc_forms_update_form_meta(
				$form_id,
				array( 'version' => (int) $form->version + 1 )
			);
		}
	}

	return true;
}
