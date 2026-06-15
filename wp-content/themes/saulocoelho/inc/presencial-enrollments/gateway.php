<?php
/**
 * Gateway WooCommerce — Pagamento direto com o Saulo.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'woocommerce_payment_gateways', 'sc_presencial_register_gateway' );

function sc_presencial_register_gateway( $gateways ) {
	$gateways[] = 'WC_Gateway_SC_Pagamento_Saulo';
	return $gateways;
}

add_action( 'plugins_loaded', 'sc_presencial_init_gateway_class', 11 );

function sc_presencial_init_gateway_class() {
	if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
		return;
	}

	class WC_Gateway_SC_Pagamento_Saulo extends WC_Payment_Gateway {

		public function __construct() {
			$this->id                 = 'sc_pagamento_saulo';
			$this->icon               = '';
			$this->has_fields         = false;
			$this->method_title       = __( 'Pagamento direto com o Saulo', 'saulocoelho' );
			$this->method_description = __( 'Permite finalizar a inscrição e combinar o pagamento diretamente com a equipe. O pedido fica aguardando confirmação.', 'saulocoelho' );

			$this->init_form_fields();
			$this->init_settings();

			$this->title       = $this->get_option( 'title', __( 'Pagamento direto com o Saulo', 'saulocoelho' ) );
			$this->description = $this->get_option( 'description', __( 'Finalize sua inscrição agora. Nossa equipe entrará em contato para combinar o pagamento.', 'saulocoelho' ) );
			$this->enabled     = $this->get_option( 'enabled', 'yes' );

			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		}

		public function init_form_fields() {
			$this->form_fields = array(
				'enabled'     => array(
					'title'   => __( 'Ativar/Desativar', 'saulocoelho' ),
					'type'    => 'checkbox',
					'label'   => __( 'Ativar pagamento direto com o Saulo', 'saulocoelho' ),
					'default' => 'yes',
				),
				'title'       => array(
					'title'       => __( 'Título', 'saulocoelho' ),
					'type'        => 'text',
					'description' => __( 'Texto exibido ao aluno no checkout.', 'saulocoelho' ),
					'default'     => __( 'Pagamento direto com o Saulo', 'saulocoelho' ),
				),
				'description' => array(
					'title'       => __( 'Descrição', 'saulocoelho' ),
					'type'        => 'textarea',
					'description' => __( 'Instruções exibidas no checkout.', 'saulocoelho' ),
					'default'     => __( 'Finalize sua inscrição agora. Nossa equipe entrará em contato para combinar o pagamento.', 'saulocoelho' ),
				),
			);
		}

		public function is_available() {
			if ( 'yes' !== $this->enabled ) {
				return false;
			}
			if ( ! parent::is_available() ) {
				return false;
			}
			return sc_presencial_cart_has_presencial_product();
		}

		public function process_payment( $order_id ) {
			$order = wc_get_order( $order_id );
			if ( ! $order ) {
				wc_add_notice( __( 'Pedido inválido.', 'saulocoelho' ), 'error' );
				return array( 'result' => 'fail' );
			}

			$order->update_status(
				'on-hold',
				__( 'Pagamento a combinar diretamente com a equipe (gateway Saulo Coelho).', 'saulocoelho' )
			);

			wc_reduce_stock_levels( $order_id );

			WC()->cart->empty_cart();

			return array(
				'result'   => 'success',
				'redirect' => $this->get_return_url( $order ),
			);
		}
	}
}
