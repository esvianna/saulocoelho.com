<?php
/**
 * CPT Palestras — CRUD no admin e URLs /palestra/{slug}/.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SC_PALESTRA_CPT', 'sc_palestra' );
define( 'SC_PALESTRA_REWRITE_VERSION', '1.1.0' );

function sc_palestra_register_cpt() {
	register_post_type(
		SC_PALESTRA_CPT,
		array(
			'labels'              => array(
				'name'               => __( 'Palestras', 'saulocoelho' ),
				'singular_name'      => __( 'Palestra', 'saulocoelho' ),
				'add_new'            => __( 'Adicionar nova', 'saulocoelho' ),
				'add_new_item'       => __( 'Nova palestra / material', 'saulocoelho' ),
				'edit_item'          => __( 'Editar palestra', 'saulocoelho' ),
				'new_item'           => __( 'Nova palestra', 'saulocoelho' ),
				'view_item'          => __( 'Ver página pública', 'saulocoelho' ),
				'search_items'       => __( 'Buscar palestras', 'saulocoelho' ),
				'not_found'          => __( 'Nenhuma palestra.', 'saulocoelho' ),
				'not_found_in_trash' => __( 'Nenhuma palestra na lixeira.', 'saulocoelho' ),
				'menu_name'          => __( 'Palestras', 'saulocoelho' ),
			),
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_icon'           => 'dashicons-megaphone',
			'menu_position'       => 58,
			'supports'            => array( 'title' ),
			'capability_type'     => 'page',
			'map_meta_cap'        => true,
			'has_archive'         => false,
			'rewrite'             => false,
			'query_var'           => false,
		)
	);
}

function sc_palestra_register_rewrites() {
	add_rewrite_tag( '%sc_palestra_slug%', '([^/]+)' );
	add_rewrite_rule(
		'^palestra/([^/]+)/?$',
		'index.php?pagename=' . SC_PALESTRA_PAGE_SLUG . '&sc_palestra_slug=$matches[1]',
		'top'
	);
}

function sc_palestra_query_vars( $vars ) {
	$vars[] = 'sc_palestra_slug';
	return $vars;
}

function sc_palestra_maybe_flush_rewrites() {
	if ( get_option( 'sc_palestra_rewrite_version' ) === SC_PALESTRA_REWRITE_VERSION ) {
		return;
	}
	sc_palestra_register_rewrites();
	flush_rewrite_rules( false );
	update_option( 'sc_palestra_rewrite_version', SC_PALESTRA_REWRITE_VERSION );
}

function sc_palestra_seed_teresopolis() {
	if ( get_option( 'sc_palestra_seeded' ) ) {
		return;
	}

	$existing = get_posts(
		array(
			'post_type'      => SC_PALESTRA_CPT,
			'post_status'    => 'any',
			'posts_per_page' => 1,
			'fields'         => 'ids',
		)
	);
	if ( ! empty( $existing ) ) {
		update_option( 'sc_palestra_seeded', '1' );
		if ( ! get_option( 'sc_palestra_canonical_id' ) ) {
			update_option( 'sc_palestra_canonical_id', (int) $existing[0] );
		}
		return;
	}

	$id = wp_insert_post(
		array(
			'post_type'   => SC_PALESTRA_CPT,
			'post_status' => 'publish',
			'post_title'  => 'O Comportamento Decide',
			'post_name'   => 'teresopolis',
		),
		true
	);

	if ( is_wp_error( $id ) || ! $id ) {
		return;
	}

	$defaults = sc_palestra_default_meta();
	$defaults['eyebrow']     = 'Teresópolis';
	$defaults['date']        = '2026-09-14';
	$defaults['location']    = 'Teresópolis';
	$defaults['support']     = 'Preencha para baixar os slides da palestra. Usamos nome, e-mail e WhatsApp só para enviar este material e novidades do Método OCD.';
	$defaults['is_canonical'] = '1';

	foreach ( $defaults as $key => $value ) {
		if ( $key === 'files' || $key === 'extras' ) {
			continue;
		}
		update_post_meta( $id, '_sc_palestra_' . $key, $value );
	}
	update_post_meta( $id, '_sc_palestra_extras', array() );
	update_option( 'sc_palestra_canonical_id', (int) $id );

	$legacy = get_template_directory() . '/private/palestra-o-comportamento-decide-teresopolis.pdf';
	if ( is_readable( $legacy ) ) {
		sc_palestra_import_private_file(
			$id,
			$legacy,
			'O-Comportamento-Decide-Teresopolis.pdf',
			'application/pdf'
		);
	}

	sc_palestra_migrate_legacy_leads( (int) $id );
	update_option( 'sc_palestra_seeded', '1' );
}

function sc_palestra_migrate_legacy_leads( $event_id ) {
	global $wpdb;
	$table = sc_palestra_table_name();
	$wpdb->query(
		$wpdb->prepare(
			"UPDATE {$table} SET event_id = %d, source = %s WHERE event_id = 0 AND source = %s",
			$event_id,
			'teresopolis',
			'teresopolis-2026-09'
		)
	);
}

function sc_palestra_cpt_columns( $columns ) {
	$date = isset( $columns['date'] ) ? $columns['date'] : __( 'Date' );
	unset( $columns['date'] );
	$columns['sc_when']     = __( 'Data', 'saulocoelho' );
	$columns['sc_location'] = __( 'Local', 'saulocoelho' );
	$columns['sc_url']      = __( 'Link público', 'saulocoelho' );
	$columns['date']        = $date;
	return $columns;
}

function sc_palestra_cpt_column_values( $column, $post_id ) {
	if ( $column === 'sc_when' ) {
		$date = get_post_meta( $post_id, '_sc_palestra_date', true );
		echo $date ? esc_html( mysql2date( 'd/m/Y', $date . ' 00:00:00' ) ) : '—';
		return;
	}
	if ( $column === 'sc_location' ) {
		$loc = get_post_meta( $post_id, '_sc_palestra_location', true );
		echo $loc ? esc_html( $loc ) : '—';
		return;
	}
	if ( $column === 'sc_url' ) {
		$url = sc_palestra_event_public_url( $post_id );
		echo '<a href="' . esc_url( $url ) . '" target="_blank" rel="noopener noreferrer">' . esc_html( $url ) . '</a>';
	}
}

function sc_palestra_row_actions( $actions, $post ) {
	if ( $post->post_type !== SC_PALESTRA_CPT ) {
		return $actions;
	}
	$actions['sc_leads'] = '<a href="' . esc_url( admin_url( 'edit.php?post_type=' . SC_PALESTRA_CPT . '&page=sc-palestra-leads&event_id=' . (int) $post->ID ) ) . '">' . esc_html__( 'Leads', 'saulocoelho' ) . '</a>';
	$actions['sc_view']  = '<a href="' . esc_url( sc_palestra_event_public_url( $post->ID ) ) . '" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Ver página', 'saulocoelho' ) . '</a>';
	return $actions;
}
