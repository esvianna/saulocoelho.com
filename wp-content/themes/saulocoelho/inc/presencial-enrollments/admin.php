<?php
/**
 * Painel admin — inscrições presenciais.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_menu', 'sc_presencial_admin_menu' );

function sc_presencial_admin_menu() {
	add_submenu_page(
		'woocommerce',
		__( 'Inscrições e questionários', 'saulocoelho' ),
		__( 'Inscrições', 'saulocoelho' ),
		'manage_options',
		'sc-presencial-enrollments',
		'sc_presencial_admin_page'
	);
}

add_action( 'admin_post_sc_presencial_export_csv', 'sc_presencial_export_csv' );
add_action( 'admin_post_sc_presencial_update_attendance', 'sc_presencial_admin_update_attendance' );

function sc_presencial_admin_update_attendance() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Sem permissão.', 'saulocoelho' ) );
	}

	check_admin_referer( 'sc_presencial_attendance' );

	$id     = isset( $_POST['enrollment_id'] ) ? absint( $_POST['enrollment_id'] ) : 0;
	$status = isset( $_POST['attendance_status'] ) ? sanitize_text_field( wp_unslash( $_POST['attendance_status'] ) ) : '';

	if ( $id && sc_presencial_update_attendance( $id, $status ) ) {
		add_settings_error( 'sc_presencial', 'updated', __( 'Presença atualizada.', 'saulocoelho' ), 'updated' );
	}

	wp_safe_redirect( wp_get_referer() ? wp_get_referer() : admin_url( 'admin.php?page=sc-presencial-enrollments' ) );
	exit;
}

function sc_presencial_admin_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$product_id = isset( $_GET['product_id'] ) ? absint( $_GET['product_id'] ) : 0;
	$search     = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';
	$view_id    = isset( $_GET['view'] ) ? absint( $_GET['view'] ) : 0;

	if ( $view_id ) {
		sc_presencial_admin_view_responses( $view_id );
		return;
	}

	$rows     = sc_presencial_query_enrollments(
		array(
			'product_id' => $product_id,
			'search'     => $search,
			'limit'      => 300,
		)
	);
	$products = wc_get_products(
		array(
			'limit'  => -1,
			'status' => 'publish',
			'return' => 'ids',
		)
	);

	$product_ids_with_rows = array_unique( array_map( static function ( $r ) {
		return (int) $r->product_id;
	}, $rows ) );

	$stats = array(
		'total'    => count( $rows ),
		'paid'     => 0,
		'pending'  => 0,
		'form_ok'  => 0,
		'present'  => 0,
	);

	foreach ( $rows as $row ) {
		$order = wc_get_order( (int) $row->order_id );
		if ( $order && $order->is_paid() ) {
			++$stats['paid'];
		} else {
			++$stats['pending'];
		}
		if ( $row->form_status === 'complete' ) {
			++$stats['form_ok'];
		}
		if ( $row->attendance_status === 'present' ) {
			++$stats['present'];
		}
	}

	settings_errors( 'sc_presencial' );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Inscrições e questionários', 'saulocoelho' ); ?></h1>

		<p>
			<strong><?php esc_html_e( 'Inscritos:', 'saulocoelho' ); ?></strong> <?php echo (int) $stats['total']; ?> |
			<strong><?php esc_html_e( 'Pagos:', 'saulocoelho' ); ?></strong> <?php echo (int) $stats['paid']; ?> |
			<strong><?php esc_html_e( 'Pagamento pendente:', 'saulocoelho' ); ?></strong> <?php echo (int) $stats['pending']; ?> |
			<strong><?php esc_html_e( 'Questionário completo:', 'saulocoelho' ); ?></strong> <?php echo (int) $stats['form_ok']; ?> |
			<strong><?php esc_html_e( 'Presentes:', 'saulocoelho' ); ?></strong> <?php echo (int) $stats['present']; ?>
		</p>

		<form method="get" style="margin:1rem 0;display:flex;gap:.5rem;flex-wrap:wrap;align-items:center;">
			<input type="hidden" name="page" value="sc-presencial-enrollments" />
			<select name="product_id">
				<option value="0"><?php esc_html_e( 'Todos os produtos', 'saulocoelho' ); ?></option>
				<?php foreach ( $products as $pid ) :
					if ( ! in_array( (int) $pid, $product_ids_with_rows, true ) && ! sc_presencial_product_needs_enrollment( $pid ) ) {
						continue;
					}
					?>
					<option value="<?php echo (int) $pid; ?>" <?php selected( $product_id, $pid ); ?>><?php echo esc_html( get_the_title( $pid ) ); ?></option>
				<?php endforeach; ?>
			</select>
			<input type="search" name="s" value="<?php echo esc_attr( $search ); ?>" placeholder="<?php esc_attr_e( 'Buscar nome ou e-mail…', 'saulocoelho' ); ?>" />
			<button class="button button-primary"><?php esc_html_e( 'Filtrar', 'saulocoelho' ); ?></button>
			<?php
			$export_url = wp_nonce_url(
				add_query_arg(
					array(
						'action'     => 'sc_presencial_export_csv',
						'product_id' => $product_id,
						's'          => $search,
					),
					admin_url( 'admin-post.php' )
				),
				'sc_presencial_export'
			);
			?>
			<a class="button" href="<?php echo esc_url( $export_url ); ?>"><?php esc_html_e( 'Exportar CSV', 'saulocoelho' ); ?></a>
		</form>

		<table class="widefat striped">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Aluno', 'saulocoelho' ); ?></th>
					<th><?php esc_html_e( 'Formação', 'saulocoelho' ); ?></th>
					<th><?php esc_html_e( 'Pedido', 'saulocoelho' ); ?></th>
					<th><?php esc_html_e( 'Pagamento', 'saulocoelho' ); ?></th>
					<th><?php esc_html_e( 'Questionário', 'saulocoelho' ); ?></th>
					<th><?php esc_html_e( 'Presença', 'saulocoelho' ); ?></th>
					<th><?php esc_html_e( 'Ações', 'saulocoelho' ); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php if ( empty( $rows ) ) : ?>
				<tr><td colspan="7"><?php esc_html_e( 'Nenhuma inscrição encontrada.', 'saulocoelho' ); ?></td></tr>
			<?php else : ?>
				<?php foreach ( $rows as $row ) :
					$user  = get_userdata( (int) $row->user_id );
					$order = wc_get_order( (int) $row->order_id );
					?>
					<tr>
						<td>
							<?php echo esc_html( $user ? $user->display_name : '#' . $row->user_id ); ?><br>
							<small><?php echo esc_html( $user ? $user->user_email : '' ); ?></small>
						</td>
						<td><?php echo esc_html( get_the_title( (int) $row->product_id ) ); ?></td>
						<td>
							<?php if ( $order ) : ?>
								<a href="<?php echo esc_url( $order->get_edit_order_url() ); ?>">#<?php echo esc_html( $order->get_order_number() ); ?></a>
							<?php else : ?>
								—
							<?php endif; ?>
						</td>
						<td><?php echo esc_html( $order ? sc_presencial_payment_status_label( $order ) : '—' ); ?></td>
						<td><?php echo esc_html( sc_presencial_form_status_label( $row->form_status ) ); ?></td>
						<td>
							<?php if ( sc_presencial_is_presencial_product( (int) $row->product_id ) ) : ?>
							<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="display:flex;gap:4px;align-items:center;">
								<?php wp_nonce_field( 'sc_presencial_attendance' ); ?>
								<input type="hidden" name="action" value="sc_presencial_update_attendance" />
								<input type="hidden" name="enrollment_id" value="<?php echo (int) $row->id; ?>" />
								<select name="attendance_status">
									<option value="unknown" <?php selected( $row->attendance_status, 'unknown' ); ?>><?php esc_html_e( 'Não registrado', 'saulocoelho' ); ?></option>
									<option value="present" <?php selected( $row->attendance_status, 'present' ); ?>><?php esc_html_e( 'Presente', 'saulocoelho' ); ?></option>
									<option value="absent" <?php selected( $row->attendance_status, 'absent' ); ?>><?php esc_html_e( 'Ausente', 'saulocoelho' ); ?></option>
								</select>
								<button class="button button-small"><?php esc_html_e( 'Salvar', 'saulocoelho' ); ?></button>
							</form>
							<?php else : ?>
								—
							<?php endif; ?>
						</td>
						<td>
							<?php if ( $row->form_status === 'complete' ) : ?>
								<a href="<?php echo esc_url( add_query_arg( array( 'page' => 'sc-presencial-enrollments', 'view' => (int) $row->id ), admin_url( 'admin.php' ) ) ); ?>"><?php esc_html_e( 'Ver respostas', 'saulocoelho' ); ?></a>
							<?php else : ?>
								—
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
			</tbody>
		</table>
	</div>
	<?php
}

function sc_presencial_admin_view_responses( $enrollment_id ) {
	$row = sc_presencial_get_enrollment( $enrollment_id );
	if ( ! $row || $row->form_status !== 'complete' ) {
		echo '<div class="wrap"><p>' . esc_html__( 'Respostas não disponíveis.', 'saulocoelho' ) . '</p></div>';
		return;
	}

	$data = json_decode( (string) $row->responses_json, true );
	if ( ! is_array( $data ) ) {
		$data = array();
	}

	$schema = sc_forms_get_schema_for_enrollment( $row );
	$labels = array();
	foreach ( $schema['fields'] as $field ) {
		$labels[ $field['key'] ] = $field['label'];
	}

	echo '<div class="wrap"><h1>' . esc_html__( 'Respostas do questionário', 'saulocoelho' ) . '</h1>';
	if ( ! empty( $schema['meta']['form_version'] ) ) {
		echo '<p class="description">' . sprintf( esc_html__( 'Versão do formulário no envio: %d', 'saulocoelho' ), (int) $schema['meta']['form_version'] ) . '</p>';
	}
	echo '<p><a href="' . esc_url( admin_url( 'admin.php?page=sc-presencial-enrollments' ) ) . '">&larr; ' . esc_html__( 'Voltar', 'saulocoelho' ) . '</a></p>';
	echo '<table class="widefat striped"><tbody>';

	$ordered_keys = array_keys( $labels );
	foreach ( $data as $key => $val ) {
		if ( ! in_array( $key, $ordered_keys, true ) ) {
			$ordered_keys[] = $key;
		}
	}

	foreach ( $ordered_keys as $key ) {
		if ( ! array_key_exists( $key, $data ) ) {
			continue;
		}
		$val = $data[ $key ];
		$label = $labels[ $key ] ?? $key;
		if ( is_array( $val ) ) {
			$val = implode( ', ', array_map( 'strval', $val ) );
		}
		echo '<tr><th style="width:35%;vertical-align:top;">' . esc_html( $label ) . '</th><td>' . esc_html( (string) $val ) . '</td></tr>';
	}
	echo '</tbody></table></div>';
}

/**
 * Colunas dinâmicas para export CSV a partir dos snapshots das inscrições.
 *
 * @param array<int, object> $rows
 * @return array{keys: string[], labels: array<string, string>}
 */
function sc_presencial_collect_csv_question_columns( array $rows ) {
	$labels = array();
	$keys   = array();

	foreach ( $rows as $row ) {
		if ( $row->form_status !== 'complete' ) {
			continue;
		}
		$schema = sc_forms_get_schema_for_enrollment( $row );
		foreach ( $schema['fields'] as $field ) {
			$k = $field['key'];
			if ( ! isset( $labels[ $k ] ) ) {
				$labels[ $k ] = $field['label'];
				$keys[]       = $k;
			}
		}
	}

	return array(
		'keys'   => $keys,
		'labels' => $labels,
	);
}

function sc_presencial_export_csv() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Sem permissão.', 'saulocoelho' ) );
	}

	check_admin_referer( 'sc_presencial_export' );

	$product_id = isset( $_GET['product_id'] ) ? absint( $_GET['product_id'] ) : 0;
	$search     = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';

	$rows = sc_presencial_query_enrollments(
		array(
			'product_id' => $product_id,
			'search'     => $search,
			'limit'      => 5000,
		)
	);

	$qcols = sc_presencial_collect_csv_question_columns( $rows );

	header( 'Content-Type: text/csv; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename=inscricoes-questionarios.csv' );

	$out = fopen( 'php://output', 'w' );
	$header = array( 'ID', 'Aluno', 'E-mail', 'Produto', 'Pedido', 'Pagamento', 'Questionário', 'Presença', 'Data' );
	foreach ( $qcols['keys'] as $qk ) {
		$header[] = $qcols['labels'][ $qk ] ?? $qk;
	}
	fputcsv( $out, $header );

	foreach ( $rows as $row ) {
		$user  = get_userdata( (int) $row->user_id );
		$order = wc_get_order( (int) $row->order_id );
		$data  = json_decode( (string) $row->responses_json, true );
		if ( ! is_array( $data ) ) {
			$data = array();
		}

		$line = array(
			$row->id,
			$user ? $user->display_name : '',
			$user ? $user->user_email : '',
			get_the_title( (int) $row->product_id ),
			$order ? $order->get_order_number() : '',
			$order ? sc_presencial_payment_status_label( $order ) : '',
			sc_presencial_form_status_label( $row->form_status ),
			sc_presencial_attendance_label( $row->attendance_status ),
			$row->created_at,
		);

		foreach ( $qcols['keys'] as $qk ) {
			$val = $data[ $qk ] ?? '';
			if ( is_array( $val ) ) {
				$val = implode( ', ', array_map( 'strval', $val ) );
			}
			$line[] = (string) $val;
		}

		fputcsv( $out, $line );
	}

	fclose( $out );
	exit;
}
