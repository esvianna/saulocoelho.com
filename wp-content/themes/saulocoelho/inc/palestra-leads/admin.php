<?php
/**
 * Admin — leads por palestra, CSV.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_menu', 'sc_palestra_admin_menu' );
add_action( 'admin_post_sc_palestra_export_csv', 'sc_palestra_export_csv' );

function sc_palestra_admin_menu() {
	add_submenu_page(
		'edit.php?post_type=' . SC_PALESTRA_CPT,
		__( 'Leads', 'saulocoelho' ),
		__( 'Leads', 'saulocoelho' ),
		'manage_options',
		'sc-palestra-leads',
		'sc_palestra_admin_page'
	);
}

function sc_palestra_admin_events() {
	return get_posts(
		array(
			'post_type'      => SC_PALESTRA_CPT,
			'post_status'    => array( 'publish', 'draft', 'pending' ),
			'posts_per_page' => 200,
			'orderby'        => 'date',
			'order'          => 'DESC',
		)
	);
}

function sc_palestra_admin_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$search   = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';
	$event_id = isset( $_GET['event_id'] ) ? absint( $_GET['event_id'] ) : 0;
	$rows     = sc_palestra_query_leads( $search, $event_id );
	$total    = sc_palestra_count_leads( $event_id );
	$events   = sc_palestra_admin_events();

	$export_url = wp_nonce_url(
		add_query_arg(
			array(
				'action'   => 'sc_palestra_export_csv',
				's'        => $search,
				'event_id' => $event_id,
			),
			admin_url( 'admin-post.php' )
		),
		'sc_palestra_export'
	);

	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Leads das palestras', 'saulocoelho' ); ?></h1>
		<p>
			<a class="button button-primary" href="<?php echo esc_url( admin_url( 'post-new.php?post_type=' . SC_PALESTRA_CPT ) ); ?>"><?php esc_html_e( 'Nova palestra', 'saulocoelho' ); ?></a>
			<a class="button" href="<?php echo esc_url( $export_url ); ?>"><?php esc_html_e( 'Exportar CSV', 'saulocoelho' ); ?></a>
		</p>

		<p>
			<strong><?php esc_html_e( 'Cadastros:', 'saulocoelho' ); ?></strong>
			<?php echo (int) $total; ?>
		</p>

		<form method="get" style="margin:1rem 0;display:flex;gap:.5rem;flex-wrap:wrap;align-items:center;">
			<input type="hidden" name="post_type" value="<?php echo esc_attr( SC_PALESTRA_CPT ); ?>" />
			<input type="hidden" name="page" value="sc-palestra-leads" />
			<select name="event_id">
				<option value="0"><?php esc_html_e( 'Todas as palestras', 'saulocoelho' ); ?></option>
				<?php foreach ( $events as $event ) : ?>
					<option value="<?php echo (int) $event->ID; ?>" <?php selected( $event_id, $event->ID ); ?>><?php echo esc_html( $event->post_title ); ?></option>
				<?php endforeach; ?>
			</select>
			<input type="search" name="s" value="<?php echo esc_attr( $search ); ?>" placeholder="<?php esc_attr_e( 'Buscar nome, e-mail ou WhatsApp…', 'saulocoelho' ); ?>" />
			<button class="button button-primary"><?php esc_html_e( 'Filtrar', 'saulocoelho' ); ?></button>
		</form>

		<table class="widefat striped">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Nome', 'saulocoelho' ); ?></th>
					<th><?php esc_html_e( 'E-mail', 'saulocoelho' ); ?></th>
					<th><?php esc_html_e( 'WhatsApp', 'saulocoelho' ); ?></th>
					<th><?php esc_html_e( 'Palestra', 'saulocoelho' ); ?></th>
					<th><?php esc_html_e( 'Extras', 'saulocoelho' ); ?></th>
					<th><?php esc_html_e( 'E-mail enviado', 'saulocoelho' ); ?></th>
					<th><?php esc_html_e( 'Downloads', 'saulocoelho' ); ?></th>
					<th><?php esc_html_e( 'Cadastro', 'saulocoelho' ); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php if ( empty( $rows ) ) : ?>
				<tr><td colspan="8"><?php esc_html_e( 'Nenhum lead ainda.', 'saulocoelho' ); ?></td></tr>
			<?php else : ?>
				<?php foreach ( $rows as $row ) : ?>
					<?php
					$event_title = '';
					if ( ! empty( $row->event_id ) ) {
						$event_title = get_the_title( (int) $row->event_id );
					}
					if ( $event_title === '' ) {
						$event_title = $row->source;
					}
					$extras = array();
					if ( ! empty( $row->extras_json ) ) {
						$decoded = json_decode( $row->extras_json, true );
						if ( is_array( $decoded ) ) {
							$extras = $decoded;
						}
					}
					$extra_bits = array();
					foreach ( $extras as $k => $v ) {
						if ( $v === '' ) {
							continue;
						}
						$extra_bits[] = $k . ': ' . $v;
					}
					?>
					<tr>
						<td><?php echo esc_html( $row->name ); ?></td>
						<td><?php echo esc_html( $row->email ); ?></td>
						<td><?php echo esc_html( $row->whatsapp ); ?></td>
						<td><?php echo esc_html( $event_title ); ?></td>
						<td><?php echo $extra_bits ? esc_html( implode( ' | ', $extra_bits ) ) : '—'; ?></td>
						<td><?php echo $row->emailed_at ? esc_html( $row->emailed_at ) : '—'; ?></td>
						<td><?php echo (int) $row->download_count; ?></td>
						<td><?php echo esc_html( $row->created_at ); ?></td>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
			</tbody>
		</table>
	</div>
	<?php
}

function sc_palestra_export_csv() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Sem permissão.', 'saulocoelho' ) );
	}
	check_admin_referer( 'sc_palestra_export' );

	$search   = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';
	$event_id = isset( $_GET['event_id'] ) ? absint( $_GET['event_id'] ) : 0;
	$rows     = sc_palestra_query_leads( $search, $event_id, 5000 );

	$extra_keys = array();
	foreach ( $rows as $row ) {
		if ( empty( $row->extras_json ) ) {
			continue;
		}
		$decoded = json_decode( $row->extras_json, true );
		if ( ! is_array( $decoded ) ) {
			continue;
		}
		foreach ( array_keys( $decoded ) as $key ) {
			$extra_keys[ $key ] = true;
		}
	}
	$extra_keys = array_keys( $extra_keys );

	$filename = 'leads-palestra-' . gmdate( 'Y-m-d-His' ) . '.csv';

	nocache_headers();
	header( 'Content-Type: text/csv; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename="' . $filename . '"' );

	$out = fopen( 'php://output', 'w' );
	fprintf( $out, chr( 0xEF ) . chr( 0xBB ) . chr( 0xBF ) );
	$header = array( 'id', 'palestra', 'nome', 'email', 'whatsapp', 'origem', 'consentimento', 'email_enviado', 'downloads', 'cadastro' );
	foreach ( $extra_keys as $key ) {
		$header[] = $key;
	}
	fputcsv( $out, $header, ';' );

	foreach ( $rows as $row ) {
		$event_title = ! empty( $row->event_id ) ? get_the_title( (int) $row->event_id ) : $row->source;
		$extras      = array();
		if ( ! empty( $row->extras_json ) ) {
			$decoded = json_decode( $row->extras_json, true );
			if ( is_array( $decoded ) ) {
				$extras = $decoded;
			}
		}
		$line = array(
			$row->id,
			$event_title,
			$row->name,
			$row->email,
			$row->whatsapp,
			$row->source,
			$row->consent_at,
			$row->emailed_at ? $row->emailed_at : '',
			$row->download_count,
			$row->created_at,
		);
		foreach ( $extra_keys as $key ) {
			$line[] = isset( $extras[ $key ] ) ? $extras[ $key ] : '';
		}
		fputcsv( $out, $line, ';' );
	}

	fclose( $out );
	exit;
}
