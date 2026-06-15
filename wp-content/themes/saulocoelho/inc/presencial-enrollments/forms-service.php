<?php
/**
 * Serviço de formulários — schema runtime, snapshot e migração.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SC_FORMS_TEXT_MAX', 254 );
define( 'SC_FORMS_TEXTAREA_MAX', 4000 );

/**
 * Meta do produto WooCommerce.
 */
function sc_forms_product_form_id( $product_id ) {
	return absint( get_post_meta( absint( $product_id ), 'sc_post_order_form_id', true ) );
}

function sc_forms_product_has_form( $product_id ) {
	$form_id = sc_forms_product_form_id( $product_id );
	if ( ! $form_id ) {
		return false;
	}
	$form = sc_forms_get_form( $form_id );
	return $form && $form->status === 'active';
}

/**
 * Inscrição necessária: produto com formulário ou presencial (check-in).
 */
function sc_presencial_product_needs_enrollment( $product_id ) {
	return sc_forms_product_has_form( $product_id ) || sc_presencial_is_presencial_product( $product_id );
}

/**
 * Schema version reconhecido: legado hardcoded ou formulário CRUD existente no banco.
 */
function sc_presencial_is_valid_form_schema_version( $schema_version ) {
	$schema_version = (string) $schema_version;
	if ( $schema_version === '' ) {
		return false;
	}
	if ( $schema_version === SC_PRESENCIAL_FORM_SCHEMA ) {
		return true;
	}
	return (bool) sc_forms_get_form_by_slug( $schema_version );
}

function sc_presencial_enrollment_has_questionnaire( $enrollment ) {
	if ( ! $enrollment ) {
		return false;
	}
	if ( ! empty( $enrollment->responses_json ) && $enrollment->responses_json !== 'null' ) {
		return true;
	}
	if ( ! empty( $enrollment->form_snapshot_json ) ) {
		return true;
	}
	if ( ! empty( $enrollment->form_id ) && sc_forms_get_form( (int) $enrollment->form_id ) ) {
		return true;
	}
	if ( sc_presencial_is_valid_form_schema_version( $enrollment->form_schema_version ?? '' ) ) {
		return true;
	}
	return sc_forms_product_has_form( (int) $enrollment->product_id )
		|| sc_presencial_is_presencial_product( (int) $enrollment->product_id );
}

/**
 * Aluno deve ver o menu/aba de questionário (pendente ou já respondido).
 */
function sc_presencial_user_has_questionnaire_access( $user_id ) {
	return ! empty( sc_presencial_get_user_questionnaire_enrollments( absint( $user_id ) ) );
}

/**
 * Monta array de schema compatível com render/validação.
 *
 * @return array{sections: array, fields: array, meta?: array}
 */
function sc_forms_build_schema_array( $form_id ) {
	$form_id = absint( $form_id );
	$form    = sc_forms_get_form( $form_id );
	if ( ! $form ) {
		return array( 'sections' => array(), 'fields' => array() );
	}

	$sections_db = sc_forms_get_sections( $form_id );
	$fields_db   = sc_forms_get_fields( $form_id );

	$section_key_by_id = array();
	$sections          = array();
	foreach ( $sections_db as $sec ) {
		$section_key_by_id[ (int) $sec->id ] = $sec->section_key;
		$sections[]                         = array(
			'id'    => $sec->section_key,
			'title' => $sec->title,
		);
	}

	$fields = array();
	foreach ( $fields_db as $f ) {
		$field = array(
			'key'      => $f->field_key,
			'section'  => $section_key_by_id[ (int) $f->section_id ] ?? '',
			'label'    => $f->label,
			'type'     => $f->field_type,
			'required' => (bool) $f->required,
		);

		if ( $f->max_length ) {
			$field['max_length'] = (int) $f->max_length;
		}

		if ( $f->options_json ) {
			$opts = json_decode( $f->options_json, true );
			if ( is_array( $opts ) ) {
				$field['options'] = $opts;
			}
		}

		if ( $f->show_if_json ) {
			$si = json_decode( $f->show_if_json, true );
			if ( is_array( $si ) ) {
				$field['show_if'] = $si;
			}
		}

		if ( $f->other_field_key ) {
			$field['other_field'] = $f->other_field_key;
		}

		$fields[] = $field;
	}

	return array(
		'sections' => $sections,
		'fields'   => $fields,
		'meta'     => array(
			'form_id'      => (int) $form->id,
			'form_version' => (int) $form->version,
			'schema_slug'  => $form->schema_slug,
			'description'  => $form->description,
		),
	);
}

/**
 * Schema para uma inscrição (snapshot > BD > legado).
 *
 * @param object $enrollment
 * @return array
 */
function sc_forms_get_schema_for_enrollment( $enrollment ) {
	if ( ! empty( $enrollment->form_snapshot_json ) ) {
		$snap = json_decode( (string) $enrollment->form_snapshot_json, true );
		if ( is_array( $snap ) && ! empty( $snap['fields'] ) ) {
			return $snap;
		}
	}

	if ( ! empty( $enrollment->form_id ) ) {
		$schema = sc_forms_build_schema_array( (int) $enrollment->form_id );
		if ( ! empty( $schema['fields'] ) ) {
			return $schema;
		}
	}

	return sc_presencial_get_legacy_form_schema();
}

/**
 * Snapshot imutável para gravar na inscrição.
 */
function sc_forms_create_snapshot( array $schema ) {
	$copy = $schema;
	unset( $copy['meta'] );
	$copy['snapshot_at'] = current_time( 'mysql' );
	if ( ! empty( $schema['meta'] ) ) {
		$copy['meta'] = $schema['meta'];
	}
	return $copy;
}

/**
 * Schema legado hardcoded (fallback).
 */
function sc_presencial_get_legacy_form_schema() {
	if ( function_exists( 'sc_presencial_get_hardcoded_form_schema' ) ) {
		return sc_presencial_get_hardcoded_form_schema();
	}
	return array( 'sections' => array(), 'fields' => array() );
}

/**
 * Resolve schema ativo para produto (antes da inscrição existir).
 */
function sc_forms_get_schema_for_product( $product_id ) {
	$form_id = sc_forms_product_form_id( $product_id );
	if ( $form_id ) {
		$schema = sc_forms_build_schema_array( $form_id );
		if ( ! empty( $schema['fields'] ) ) {
			return $schema;
		}
	}
	return array( 'sections' => array(), 'fields' => array() );
}

/**
 * Migração: seed do schema coaching-terapia + vínculo produtos presenciais.
 */
function sc_forms_maybe_seed_and_migrate() {
	if ( get_option( 'sc_forms_seeded_v1' ) ) {
		return;
	}

	sc_forms_install_tables();

	global $wpdb;
	$count = (int) $wpdb->get_var( 'SELECT COUNT(*) FROM ' . sc_forms_table_name() );
	if ( $count > 0 ) {
		update_option( 'sc_forms_seeded_v1', 1 );
		return;
	}

	$legacy = sc_presencial_get_hardcoded_form_schema();
	$slug   = SC_PRESENCIAL_FORM_SCHEMA;
	$now    = current_time( 'mysql' );

	$wpdb->insert(
		sc_forms_table_name(),
		array(
			'title'        => __( 'Coaching | Terapia — Jul/2026', 'saulocoelho' ),
			'description'  => __( 'Responda com sinceridade. Suas respostas nos ajudam a personalizar sua experiência na formação.', 'saulocoelho' ),
			'schema_slug'  => $slug,
			'status'       => 'active',
			'version'      => 1,
			'created_at'   => $now,
			'updated_at'   => $now,
		),
		array( '%s', '%s', '%s', '%s', '%d', '%s', '%s' )
	);

	$form_id = (int) $wpdb->insert_id;
	if ( ! $form_id ) {
		return;
	}

	sc_forms_save_structure( $form_id, $legacy['sections'], $legacy['fields'], false );

	$products = wc_get_products(
		array(
			'limit'  => -1,
			'status' => 'publish',
			'return' => 'ids',
		)
	);

	foreach ( $products as $pid ) {
		if ( sc_presencial_is_presencial_product( $pid ) && ! sc_forms_product_form_id( $pid ) ) {
			update_post_meta( $pid, 'sc_post_order_form_id', $form_id );
		}
	}

	$table = sc_presencial_table_name();
	$wpdb->query(
		$wpdb->prepare(
			"UPDATE {$table} SET form_id = %d, form_version = 1 WHERE form_schema_version = %s AND form_id = 0",
			$form_id,
			SC_PRESENCIAL_FORM_SCHEMA
		)
	);

	update_option( 'sc_forms_seeded_v1', 1 );
}

/**
 * Slug único para novo formulário.
 */
function sc_forms_generate_slug( $title ) {
	$base = sanitize_title( $title );
	if ( $base === '' ) {
		$base = 'form';
	}
	$slug = $base;
	$i    = 1;
	global $wpdb;
	while ( $wpdb->get_var( $wpdb->prepare( 'SELECT id FROM ' . sc_forms_table_name() . ' WHERE schema_slug = %s', $slug ) ) ) {
		$slug = $base . '-' . $i;
		++$i;
	}
	return $slug;
}

/**
 * Duplica formulário.
 *
 * @return int|false
 */
function sc_forms_duplicate_form( $form_id ) {
	$form = sc_forms_get_form( $form_id );
	if ( ! $form ) {
		return false;
	}

	$new_id = sc_forms_insert_form(
		array(
			'title'       => $form->title . ' (' . __( 'cópia', 'saulocoelho' ) . ')',
			'description' => $form->description,
			'schema_slug' => sc_forms_generate_slug( $form->title . '-copy' ),
			'status'      => 'active',
		)
	);

	if ( ! $new_id ) {
		return false;
	}

	$schema = sc_forms_build_schema_array( $form_id );
	sc_forms_save_structure( $new_id, $schema['sections'], $schema['fields'], false );

	return $new_id;
}

/**
 * Tipos de campo permitidos no CRUD v1.
 *
 * @return array<string, string>
 */
function sc_forms_allowed_field_types() {
	return array(
		'text'        => __( 'Texto curto', 'saulocoelho' ),
		'textarea'    => __( 'Parágrafo', 'saulocoelho' ),
		'select'      => __( 'Escolha única', 'saulocoelho' ),
		'multiselect' => __( 'Múltipla escolha', 'saulocoelho' ),
		'date'        => __( 'Data', 'saulocoelho' ),
		'tel'         => __( 'Telefone', 'saulocoelho' ),
	);
}

/**
 * Limite padrão por tipo.
 */
function sc_forms_default_max_length( $type ) {
	if ( $type === 'textarea' ) {
		return SC_FORMS_TEXTAREA_MAX;
	}
	if ( $type === 'text' || $type === 'tel' ) {
		return SC_FORMS_TEXT_MAX;
	}
	return 0;
}
