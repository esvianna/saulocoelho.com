<?php
/**
 * Helpers — inscrições presenciais.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Produto WooCommerce com modalidade presencial (metabox course_type).
 */
function sc_presencial_is_presencial_product( $product_id ) {
	$product_id = absint( $product_id );
	if ( ! $product_id ) {
		return false;
	}
	return get_post_meta( $product_id, 'course_type', true ) === 'presencial';
}

/**
 * Carrinho contém ao menos um produto presencial.
 */
function sc_presencial_cart_has_presencial_product() {
	if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
		return false;
	}
	foreach ( WC()->cart->get_cart() as $item ) {
		$pid = isset( $item['product_id'] ) ? (int) $item['product_id'] : 0;
		if ( sc_presencial_is_presencial_product( $pid ) ) {
			return true;
		}
	}
	return false;
}

/**
 * Nome da tabela de inscrições.
 */
function sc_presencial_table_name() {
	global $wpdb;
	return $wpdb->prefix . 'sc_presencial_enrollments';
}

/**
 * Rótulos de status do formulário.
 */
function sc_presencial_form_status_label( $status ) {
	$labels = array(
		'pending'  => __( 'Questionário pendente', 'saulocoelho' ),
		'complete' => __( 'Questionário completo', 'saulocoelho' ),
	);
	return $labels[ $status ] ?? $status;
}

/**
 * Rótulos de presença.
 */
function sc_presencial_attendance_label( $status ) {
	$labels = array(
		'unknown' => __( 'Não registrado', 'saulocoelho' ),
		'present' => __( 'Presente', 'saulocoelho' ),
		'absent'  => __( 'Ausente', 'saulocoelho' ),
	);
	return $labels[ $status ] ?? $status;
}

/**
 * Status de pagamento legível a partir do pedido WC.
 */
function sc_presencial_payment_status_label( $order ) {
	if ( ! $order instanceof WC_Order ) {
		return '—';
	}
	if ( $order->is_paid() ) {
		return __( 'Pago', 'saulocoelho' );
	}
	$status = $order->get_status();
	$map    = array(
		'on-hold'    => __( 'Aguardando pagamento', 'saulocoelho' ),
		'pending'    => __( 'Pagamento pendente', 'saulocoelho' ),
		'processing' => __( 'Em processamento', 'saulocoelho' ),
		'cancelled'  => __( 'Cancelado', 'saulocoelho' ),
		'failed'     => __( 'Falhou', 'saulocoelho' ),
	);
	return $map[ $status ] ?? wc_get_order_status_name( $status );
}

/**
 * Pré-preenche campos do formulário a partir do usuário/pedido.
 */
function sc_presencial_default_field_values( $user_id, $order = null ) {
	$user_id = absint( $user_id );
	$values  = array();

	if ( $user_id ) {
		$first = get_user_meta( $user_id, 'billing_first_name', true );
		$last  = get_user_meta( $user_id, 'billing_last_name', true );
		$full  = trim( $first . ' ' . $last );
		if ( $full !== '' ) {
			$values['full_name'] = $full;
		}
		$phone = get_user_meta( $user_id, 'billing_phone', true );
		if ( is_string( $phone ) && $phone !== '' ) {
			$values['phone_whatsapp'] = $phone;
		}
	}

	return $values;
}
