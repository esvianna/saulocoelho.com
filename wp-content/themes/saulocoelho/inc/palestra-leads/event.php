<?php
/**
 * Modelo de uma palestra (meta, URLs, ficheiros privados).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function sc_palestra_default_meta() {
	return array(
		'eyebrow'       => '',
		'date'          => '',
		'location'      => '',
		'support'       => 'Preencha para baixar o material. Usamos os seus dados para enviar este ficheiro e comunicações sobre o Método OCD.',
		'cta'           => 'Baixar material',
		'thanks_title'  => 'O seu material está liberado.',
		'thanks_note'   => 'O Método OCD é a jornada de evolução do Saulo Coelho — em breve, mais conteúdo para quem deu o primeiro passo hoje.',
		'consent'       => 'Autorizo o uso dos meus dados para envio deste material e comunicações sobre o Método OCD, nos termos da',
		'email_intro'   => 'Segue o material da palestra.',
		'bg_id'         => 0,
		'ask_whatsapp'  => '1',
		'is_canonical'  => '',
	);
}

/**
 * @return array<string, mixed>
 */
function sc_palestra_event_config( $post_id ) {
	$post_id = absint( $post_id );
	$base    = sc_palestra_default_meta();
	if ( ! $post_id ) {
		$base['files']  = array();
		$base['extras'] = array();
		$base['title']  = '';
		$base['id']     = 0;
		$base['slug']   = '';
		return $base;
	}

	foreach ( $base as $key => $default ) {
		$stored = get_post_meta( $post_id, '_sc_palestra_' . $key, true );
		if ( $stored !== '' && $stored !== null ) {
			$base[ $key ] = $stored;
		}
	}

	$files = get_post_meta( $post_id, '_sc_palestra_files', true );
	$base['files'] = is_array( $files ) ? $files : array();

	$extras = get_post_meta( $post_id, '_sc_palestra_extras', true );
	$base['extras'] = is_array( $extras ) ? $extras : array();

	$post = get_post( $post_id );
	$base['id']    = $post_id;
	$base['title'] = $post ? $post->post_title : '';
	$base['slug']  = $post ? $post->post_name : '';
	$base['bg_id'] = absint( $base['bg_id'] );
	$base['bg_url'] = $base['bg_id'] ? wp_get_attachment_image_url( $base['bg_id'], 'full' ) : '';

	return $base;
}

function sc_palestra_canonical_id() {
	return absint( get_option( 'sc_palestra_canonical_id' ) );
}

/**
 * @return WP_Post|null
 */
function sc_palestra_get_event_by_slug( $slug ) {
	$slug = sanitize_title( $slug );
	if ( $slug === '' ) {
		return null;
	}
	$posts = get_posts(
		array(
			'name'           => $slug,
			'post_type'      => SC_PALESTRA_CPT,
			'post_status'    => 'publish',
			'posts_per_page' => 1,
		)
	);
	return ! empty( $posts ) ? $posts[0] : null;
}

/**
 * Evento da request actual (página /palestra/ ou /palestra/{slug}/).
 *
 * @return WP_Post|null
 */
function sc_palestra_current_event() {
	$slug = get_query_var( 'sc_palestra_slug' );
	if ( is_string( $slug ) && $slug !== '' ) {
		return sc_palestra_get_event_by_slug( $slug );
	}

	$canonical = sc_palestra_canonical_id();
	if ( $canonical ) {
		$post = get_post( $canonical );
		if ( $post && $post->post_type === SC_PALESTRA_CPT && $post->post_status === 'publish' ) {
			return $post;
		}
	}

	$posts = get_posts(
		array(
			'post_type'      => SC_PALESTRA_CPT,
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'orderby'        => 'date',
			'order'          => 'DESC',
		)
	);
	return ! empty( $posts ) ? $posts[0] : null;
}

function sc_palestra_event_public_url( $post_id ) {
	$post_id = absint( $post_id );
	$page    = sc_palestra_get_page();
	$base    = $page ? get_permalink( $page ) : home_url( '/' . SC_PALESTRA_PAGE_SLUG . '/' );

	if ( $post_id && $post_id === sc_palestra_canonical_id() ) {
		return $base;
	}

	$post = get_post( $post_id );
	if ( ! $post ) {
		return $base;
	}

	return trailingslashit( $base ) . $post->post_name . '/';
}

function sc_palestra_private_base_dir() {
	$uploads = wp_upload_dir();
	return trailingslashit( $uploads['basedir'] ) . 'sc-palestra-private';
}

function sc_palestra_private_event_dir( $event_id ) {
	return sc_palestra_private_base_dir() . '/' . absint( $event_id );
}

function sc_palestra_ensure_private_dir( $event_id ) {
	$base = sc_palestra_private_base_dir();
	$dir  = sc_palestra_private_event_dir( $event_id );

	wp_mkdir_p( $base );
	wp_mkdir_p( $dir );

	$ht = $base . '/.htaccess';
	if ( ! file_exists( $ht ) ) {
		file_put_contents(
			$ht,
			"<IfModule mod_authz_core.c>\n\tRequire all denied\n</IfModule>\n<IfModule !mod_authz_core.c>\n\tDeny from all\n</IfModule>\n"
		);
	}
	$index = $base . '/index.php';
	if ( ! file_exists( $index ) ) {
		file_put_contents( $index, "<?php\n// Silence.\n" );
	}

	return $dir;
}

function sc_palestra_allowed_mimes() {
	return array(
		'pdf'  => 'application/pdf',
		'zip'  => 'application/zip',
		'ppt'  => 'application/vnd.ms-powerpoint',
		'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
		'png'  => 'image/png',
		'jpg'  => 'image/jpeg',
		'jpeg' => 'image/jpeg',
	);
}

/**
 * @return array{id:string,name:string,mime:string}|WP_Error
 */
function sc_palestra_import_private_file( $event_id, $source_path, $original_name, $mime = '' ) {
	$event_id = absint( $event_id );
	if ( ! $event_id || ! is_readable( $source_path ) ) {
		return new WP_Error( 'sc_palestra_file', __( 'Ficheiro inválido.', 'saulocoelho' ) );
	}

	$ext = strtolower( pathinfo( $original_name, PATHINFO_EXTENSION ) );
	$allowed = sc_palestra_allowed_mimes();
	if ( ! isset( $allowed[ $ext ] ) ) {
		return new WP_Error( 'sc_palestra_mime', __( 'Tipo de ficheiro não permitido.', 'saulocoelho' ) );
	}

	$dir  = sc_palestra_ensure_private_dir( $event_id );
	$stored = wp_unique_filename( $dir, sanitize_file_name( $original_name ) );
	$dest   = trailingslashit( $dir ) . $stored;

	if ( ! copy( $source_path, $dest ) ) {
		return new WP_Error( 'sc_palestra_copy', __( 'Não foi possível guardar o ficheiro.', 'saulocoelho' ) );
	}

	$item = array(
		'id'   => $stored,
		'name' => sanitize_file_name( $original_name ),
		'mime' => $mime ? $mime : $allowed[ $ext ],
	);

	$files   = get_post_meta( $event_id, '_sc_palestra_files', true );
	$files   = is_array( $files ) ? $files : array();
	$files[] = $item;
	update_post_meta( $event_id, '_sc_palestra_files', $files );

	return $item;
}

function sc_palestra_file_path( $event_id, $stored_id ) {
	$stored_id = basename( (string) $stored_id );
	if ( $stored_id === '' || false !== strpos( $stored_id, '..' ) ) {
		return '';
	}
	return trailingslashit( sc_palestra_private_event_dir( $event_id ) ) . $stored_id;
}

function sc_palestra_find_file( $event_id, $stored_id ) {
	$files = get_post_meta( $event_id, '_sc_palestra_files', true );
	if ( ! is_array( $files ) ) {
		return null;
	}
	foreach ( $files as $file ) {
		if ( isset( $file['id'] ) && $file['id'] === $stored_id ) {
			return $file;
		}
	}
	return null;
}

function sc_palestra_event_has_files( $event_id ) {
	$files = get_post_meta( $event_id, '_sc_palestra_files', true );
	if ( ! is_array( $files ) || empty( $files ) ) {
		return false;
	}
	foreach ( $files as $file ) {
		$path = sc_palestra_file_path( $event_id, $file['id'] ?? '' );
		if ( $path && is_readable( $path ) ) {
			return true;
		}
	}
	return false;
}

function sc_palestra_sanitize_extras( $raw ) {
	$out = array();
	if ( ! is_array( $raw ) ) {
		return $out;
	}
	$reserved = array( 'name', 'email', 'whatsapp', 'consent', 'website', 'action', 'security', 'event_id' );
	foreach ( $raw as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}
		$label = isset( $row['label'] ) ? sanitize_text_field( $row['label'] ) : '';
		$key   = isset( $row['key'] ) ? sanitize_key( $row['key'] ) : '';
		if ( $key === '' && $label !== '' ) {
			$key = sanitize_key( $label );
		}
		if ( $label === '' || $key === '' || in_array( $key, $reserved, true ) ) {
			continue;
		}
		$type = isset( $row['type'] ) ? sanitize_key( $row['type'] ) : 'text';
		if ( ! in_array( $type, array( 'text', 'email', 'tel' ), true ) ) {
			$type = 'text';
		}
		$out[] = array(
			'key'      => $key,
			'label'    => $label,
			'type'     => $type,
			'required' => ! empty( $row['required'] ) ? '1' : '',
		);
		if ( count( $out ) >= 8 ) {
			break;
		}
	}
	return $out;
}
