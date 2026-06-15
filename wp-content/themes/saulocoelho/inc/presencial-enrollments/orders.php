<?php
/**
 * Sincroniza inscrições presenciais a partir de pedidos WooCommerce.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'woocommerce_checkout_order_processed', 'sc_presencial_sync_order_enrollments', 20, 3 );
add_action( 'woocommerce_order_status_changed', 'sc_presencial_sync_order_enrollments_on_status', 20, 4 );

/**
 * @param int      $order_id
 * @param array    $posted_data
 * @param WC_Order $order
 */
function sc_presencial_sync_order_enrollments( $order_id, $posted_data = array(), $order = null ) {
	$order = $order instanceof WC_Order ? $order : wc_get_order( $order_id );
	if ( ! $order ) {
		return;
	}

	$user_id = (int) $order->get_user_id();
	if ( ! $user_id ) {
		return;
	}

	foreach ( $order->get_items() as $item ) {
		$product_id = (int) $item->get_product_id();
		if ( ! sc_presencial_is_presencial_product( $product_id ) ) {
			continue;
		}
		sc_presencial_upsert_enrollment( $user_id, $order->get_id(), $product_id );
	}
}

function sc_presencial_sync_order_enrollments_on_status( $order_id, $old_status, $new_status, $order ) {
	if ( in_array( $new_status, array( 'cancelled', 'refunded', 'failed', 'trash' ), true ) ) {
		return;
	}
	sc_presencial_sync_order_enrollments( $order_id, array(), $order );
}

/**
 * CTA na página de obrigado.
 */
add_action( 'woocommerce_thankyou', 'sc_presencial_thankyou_form_cta', 15 );

function sc_presencial_thankyou_form_cta( $order_id ) {
	if ( ! $order_id ) {
		return;
	}

	$order = wc_get_order( $order_id );
	if ( ! $order || (int) $order->get_user_id() !== get_current_user_id() ) {
		return;
	}

	$pending = array();
	foreach ( $order->get_items() as $item ) {
		$product_id = (int) $item->get_product_id();
		if ( ! sc_presencial_is_presencial_product( $product_id ) ) {
			continue;
		}
		$row = sc_presencial_get_enrollment_by_order_product( $order_id, $product_id );
		if ( $row && $row->form_status === 'pending' ) {
			$pending[] = $row;
		}
	}

	if ( empty( $pending ) ) {
		return;
	}

	$account_url = wc_get_account_endpoint_url( 'questionario-presencial' );
	?>
	<div class="sc-presencial-thankyou-cta" style="margin:2rem 0;padding:1.5rem;border:1px solid rgba(197,160,89,.35);border-radius:12px;background:rgba(197,160,89,.08);">
		<h3 style="margin:0 0 .5rem;font-size:1.15rem;color:#C5A059;"><?php esc_html_e( 'Próximo passo: questionário de inscrição', 'saulocoelho' ); ?></h3>
		<p style="margin:0 0 1rem;opacity:.9;"><?php esc_html_e( 'Sua vaga está reservada. Preencha o questionário para personalizarmos sua experiência na formação.', 'saulocoelho' ); ?></p>
		<a href="<?php echo esc_url( $account_url ); ?>" class="button" style="background:#C5A059;color:#050A14;border:none;padding:.75rem 1.25rem;border-radius:8px;font-weight:700;text-decoration:none;display:inline-block;">
			<?php esc_html_e( 'Preencher questionário agora', 'saulocoelho' ); ?>
		</a>
		<p style="margin:1rem 0 0;font-size:.875rem;opacity:.75;"><?php esc_html_e( 'Você também pode completar depois em Minha Conta.', 'saulocoelho' ); ?></p>
	</div>
	<?php
}

/**
 * E-mail à equipe quando pedido on-hold via gateway Saulo.
 */
add_action( 'woocommerce_order_status_on-hold', 'sc_presencial_notify_team_on_hold', 10, 2 );

function sc_presencial_notify_team_on_hold( $order_id, $order = null ) {
	$order = $order instanceof WC_Order ? $order : wc_get_order( $order_id );
	if ( ! $order || $order->get_payment_method() !== 'sc_pagamento_saulo' ) {
		return;
	}

	$admin_email = get_option( 'admin_email' );
	$subject     = sprintf(
		/* translators: %s: order number */
		__( '[Saulo Coelho] Nova inscrição aguardando pagamento — Pedido #%s', 'saulocoelho' ),
		$order->get_order_number()
	);

	$body  = sprintf( __( "Novo pedido com pagamento a combinar:\n\nPedido: #%s\nCliente: %s\nE-mail: %s\nTotal: %s\n\nAcesse o painel para confirmar o pagamento quando recebido.", 'saulocoelho' ),
		$order->get_order_number(),
		$order->get_formatted_billing_full_name(),
		$order->get_billing_email(),
		$order->get_formatted_order_total()
	);

	wp_mail( $admin_email, $subject, $body );
}

add_action( 'sc_presencial_form_completed', 'sc_presencial_notify_team_form_completed', 10, 2 );

function sc_presencial_notify_team_form_completed( $enrollment_id, $responses ) {
	$row = sc_presencial_get_enrollment( $enrollment_id );
	if ( ! $row ) {
		return;
	}
	$user    = get_userdata( (int) $row->user_id );
	$subject = sprintf(
		__( '[Saulo Coelho] Questionário preenchido — %s', 'saulocoelho' ),
		get_the_title( (int) $row->product_id )
	);
	$body    = sprintf(
		__( "O aluno %s concluiu o questionário de inscrição.\n\nProduto: %s\nPedido: #%d\n\nPainel: %s", 'saulocoelho' ),
		$user ? $user->display_name : '#' . $row->user_id,
		get_the_title( (int) $row->product_id ),
		(int) $row->order_id,
		admin_url( 'admin.php?page=sc-presencial-enrollments&view=' . $enrollment_id )
	);
	wp_mail( get_option( 'admin_email' ), $subject, $body );
}
