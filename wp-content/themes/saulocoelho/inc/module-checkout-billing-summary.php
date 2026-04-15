<?php
/**
 * Checkout: serviço sem envio + resumo de faturamento com formulário recolhível.
 *
 * @package SauloCoelho
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WooCommerce' ) ) {
	return;
}

/**
 * Valor utilizável para endereço / nome (rejeita placeholders comuns).
 *
 * @param mixed $val Valor.
 * @return bool
 */
function sc_billing_summary_value_ok( $val ) {
	$val = trim( (string) $val );
	if ( $val === '' || $val === '--' || $val === '...' || $val === '…' ) {
		return false;
	}
	if ( function_exists( 'mb_stripos' ) ) {
		if ( mb_stripos( $val, 'buscando', 0, 'UTF-8' ) !== false ) {
			return false;
		}
	} elseif ( stripos( $val, 'buscando' ) !== false ) {
		return false;
	}
	return true;
}

/**
 * Lê campo de cobrança: valor atual do checkout e, se vazio, meta do usuário.
 *
 * @param WC_Checkout $checkout Checkout.
 * @param int         $user_id  ID do usuário.
 * @param string      $key      Sufixo WooCommerce (ex.: first_name, postcode) ou chave completa billing_*.
 * @return string
 */
function sc_billing_summary_checkout_value( $checkout, $user_id, $key ) {
	$input_key = ( strpos( $key, 'billing_' ) === 0 ) ? $key : 'billing_' . $key;
	$meta_key  = $input_key;

	$v = $checkout->get_value( $input_key );
	if ( is_string( $v ) && sc_billing_summary_value_ok( $v ) ) {
		return $v;
	}

	$short = preg_replace( '/^billing_/', '', $input_key );
	if ( $short !== $input_key ) {
		$v = $checkout->get_value( $short );
		if ( is_string( $v ) && sc_billing_summary_value_ok( $v ) ) {
			return $v;
		}
	}

	$meta = get_user_meta( $user_id, $meta_key, true );
	return is_string( $meta ) ? $meta : '';
}

/**
 * Perfil tem dados mínimos para exibir o resumo (usuário logado).
 *
 * @param WC_Checkout $checkout Checkout.
 * @param int         $user_id  User ID.
 * @return bool
 */
function sc_billing_summary_is_complete( $checkout, $user_id ) {
	if ( ! $user_id ) {
		return false;
	}

	$ptype_raw = sc_billing_summary_checkout_value( $checkout, $user_id, 'persontype' );
	$is_pj     = ( $ptype_raw === '2' || (int) $ptype_raw === 2 );

	$addr_ok = function () use ( $checkout, $user_id ) {
		$keys = array( 'postcode', 'address_1', 'city', 'state' );
		foreach ( $keys as $k ) {
			if ( ! sc_billing_summary_value_ok( sc_billing_summary_checkout_value( $checkout, $user_id, $k ) ) ) {
				return false;
			}
		}
		$st = strtoupper( trim( sc_billing_summary_checkout_value( $checkout, $user_id, 'state' ) ) );
		return strlen( $st ) === 2;
	};

	if ( ! $addr_ok() ) {
		return false;
	}

	if ( $is_pj ) {
		$company = sc_billing_summary_checkout_value( $checkout, $user_id, 'company' );
		return sc_billing_summary_value_ok( $company );
	}

	$fn = sc_billing_summary_checkout_value( $checkout, $user_id, 'first_name' );
	$ln = sc_billing_summary_checkout_value( $checkout, $user_id, 'last_name' );
	return sc_billing_summary_value_ok( $fn ) && sc_billing_summary_value_ok( $ln );
}

/**
 * Monta linhas de texto para o cartão.
 *
 * @param WC_Checkout $checkout Checkout.
 * @param int         $user_id  User ID.
 * @return array{primary:string,secondary:string}
 */
function sc_billing_summary_build_lines( $checkout, $user_id ) {
	$ptype_raw = sc_billing_summary_checkout_value( $checkout, $user_id, 'persontype' );
	$is_pj     = ( $ptype_raw === '2' || (int) $ptype_raw === 2 );

	$pc = sc_billing_summary_checkout_value( $checkout, $user_id, 'postcode' );
	$pc = preg_replace( '/\D/', '', $pc );
	if ( strlen( $pc ) === 8 ) {
		$pc = substr( $pc, 0, 5 ) . '-' . substr( $pc, 5, 3 );
	}

	$street = sc_billing_summary_checkout_value( $checkout, $user_id, 'address_1' );
	$num    = sc_billing_summary_checkout_value( $checkout, $user_id, 'number' );
	$city   = sc_billing_summary_checkout_value( $checkout, $user_id, 'city' );
	$uf     = strtoupper( sc_billing_summary_checkout_value( $checkout, $user_id, 'state' ) );

	if ( $is_pj ) {
		$who = sc_billing_summary_checkout_value( $checkout, $user_id, 'company' );
		$doc = sc_billing_summary_checkout_value( $checkout, $user_id, 'cnpj' );
		if ( sc_billing_summary_value_ok( $doc ) ) {
			$who .= ' · CNPJ ' . $doc;
		}
	} else {
		$fn = sc_billing_summary_checkout_value( $checkout, $user_id, 'first_name' );
		$ln  = sc_billing_summary_checkout_value( $checkout, $user_id, 'last_name' );
		$who = trim( $fn . ' ' . $ln );
		$cpf = sc_billing_summary_checkout_value( $checkout, $user_id, 'cpf' );
		if ( sc_billing_summary_value_ok( $cpf ) ) {
			$who .= ' · CPF ' . $cpf;
		}
	}

	$line1_parts = array_filter(
		array(
			$who,
			$pc ? 'CEP ' . $pc : '',
			trim( $street . ( $num ? ', ' . $num : '' ) ),
			trim( $city . ( $uf ? ' - ' . $uf : '' ) ),
		)
	);
	$primary = implode( ', ', $line1_parts );

	$phone = sc_billing_summary_checkout_value( $checkout, $user_id, 'phone' );
	$email = sc_billing_summary_checkout_value( $checkout, $user_id, 'email' );
	if ( ! sc_billing_summary_value_ok( $email ) && $user_id ) {
		$u       = get_userdata( $user_id );
		$email   = $u ? $u->user_email : '';
	}
	$sec_parts = array_filter( array( $phone, $email ) );
	$secondary = implode( ' · ', $sec_parts );

	return array(
		'primary'   => $primary,
		'secondary' => $secondary,
	);
}

/**
 * Body class quando o carrinho não precisa de envio físico.
 *
 * @param string[] $classes Classes.
 * @return string[]
 */
function sc_service_checkout_body_class( $classes ) {
	if ( ! function_exists( 'is_checkout' ) || ! is_checkout() || is_wc_endpoint_url( 'order-pay' ) ) {
		return $classes;
	}
	if ( ! WC()->cart || WC()->cart->is_empty() ) {
		return $classes;
	}
	if ( ! WC()->cart->needs_shipping() ) {
		$classes[] = 'sc-checkout-no-shipping';
	}
	return $classes;
}
add_filter( 'body_class', 'sc_service_checkout_body_class' );

/**
 * Remove campos de envio quando não há necessidade de shipping (ex.: só virtual/serviço).
 *
 * @param array $fields Checkout fields.
 * @return array
 */
function sc_service_checkout_remove_shipping_fields( $fields ) {
	if ( ! is_checkout() || is_wc_endpoint_url( 'order-pay' ) ) {
		return $fields;
	}
	if ( ! WC()->cart || WC()->cart->is_empty() ) {
		return $fields;
	}
	if ( WC()->cart->needs_shipping() ) {
		return $fields;
	}
	if ( isset( $fields['shipping'] ) && is_array( $fields['shipping'] ) ) {
		unset( $fields['shipping'] );
	}
	return $fields;
}
add_filter( 'woocommerce_checkout_fields', 'sc_service_checkout_remove_shipping_fields', 100 );

/**
 * Ajusta rótulos de cobrança / envio no checkout (serviço).
 *
 * @param string $translation Tradução atual.
 * @param string $text        Texto original.
 * @param string $domain      Domínio.
 * @return string
 */
function sc_service_checkout_gettext( $translation, $text, $domain ) {
	if ( 'woocommerce' !== $domain || ! function_exists( 'is_checkout' ) || ! is_checkout() ) {
		return $translation;
	}
	$map = array(
		'Billing details'  => __( 'Dados de faturamento', 'saulocoelho' ),
		'Billing address'  => __( 'Dados de faturamento', 'saulocoelho' ),
	);
	if ( isset( $map[ $text ] ) ) {
		return $map[ $text ];
	}
	return $translation;
}
add_filter( 'gettext', 'sc_service_checkout_gettext', 20, 3 );

/**
 * Renderiza o cartão de resumo antes dos campos de cobrança.
 */
function sc_checkout_render_billing_summary_card() {
	if ( ! is_user_logged_in() ) {
		return;
	}
	if ( ! function_exists( 'WC' ) || ! WC()->checkout() ) {
		return;
	}
	$checkout = WC()->checkout();
	$user_id  = get_current_user_id();
	if ( ! sc_billing_summary_is_complete( $checkout, $user_id ) ) {
		return;
	}
	$lines = sc_billing_summary_build_lines( $checkout, $user_id );
	?>
	<div class="sc-billing-summary-card" data-sc-billing-summary="1">
		<p class="sc-billing-summary-card__label"><?php esc_html_e( 'Dados de faturamento', 'saulocoelho' ); ?></p>
		<p class="sc-billing-summary-card__primary"><?php echo esc_html( $lines['primary'] ); ?></p>
		<?php if ( sc_billing_summary_value_ok( $lines['secondary'] ) ) : ?>
			<p class="sc-billing-summary-card__secondary"><?php echo esc_html( $lines['secondary'] ); ?></p>
		<?php endif; ?>
		<button type="button" class="sc-billing-summary-toggle button">
			<?php esc_html_e( 'Alterar dados de faturamento', 'saulocoelho' ); ?>
		</button>
	</div>
	<p class="sc-billing-summary-hint sc-billing-summary-hint--expanded" style="display:none;">
		<button type="button" class="sc-billing-summary-collapse button-link">
			<?php esc_html_e( 'Mostrar apenas resumo', 'saulocoelho' ); ?>
		</button>
	</p>
	<?php
}
add_action( 'woocommerce_before_checkout_billing_form', 'sc_checkout_render_billing_summary_card', 5 );

/**
 * Scripts do resumo recolhível.
 */
function sc_checkout_billing_summary_scripts() {
	if ( ! is_checkout() || is_wc_endpoint_url( 'order-pay' ) ) {
		return;
	}
	wp_register_script( 'sc-checkout-billing-summary', false, array( 'jquery' ), wp_get_theme()->get( 'Version' ), true );
	wp_enqueue_script( 'sc-checkout-billing-summary' );
	$inline = <<<'JS'
(function($) {
	var STORAGE_KEY = "sc_checkout_billing_edit";
	function hasSummaryCard() {
		return $(".sc-billing-summary-card").length > 0;
	}
	function getWrap() {
		return $(".woocommerce-billing-fields").first();
	}
	function showExpandedHint(show) {
		var $h = $(".sc-billing-summary-hint--expanded");
		if (show) { $h.show(); } else { $h.hide(); }
	}
	function applyBillingLayout() {
		var $wrap = getWrap();
		if (!$wrap.length || !hasSummaryCard()) {
			return;
		}
		var editing = window.sessionStorage.getItem(STORAGE_KEY) === "1";
		var hasErr = $(".woocommerce-checkout .woocommerce-error").length > 0;
		if (hasErr || editing) {
			$wrap.removeClass("sc-billing-collapsed").addClass("sc-billing-expanded");
			showExpandedHint(true);
		} else {
			$wrap.addClass("sc-billing-collapsed").removeClass("sc-billing-expanded");
			showExpandedHint(false);
		}
	}
	$(document.body).on("checkout_error", function() {
		window.sessionStorage.setItem(STORAGE_KEY, "1");
		var $w = getWrap();
		$w.removeClass("sc-billing-collapsed").addClass("sc-billing-expanded");
		showExpandedHint(true);
	});
	$(document.body).on("updated_checkout", function() {
		applyBillingLayout();
	});
	$(function() {
		applyBillingLayout();
	});
	$(document).on("click", ".sc-billing-summary-toggle", function(e) {
		e.preventDefault();
		window.sessionStorage.setItem(STORAGE_KEY, "1");
		var $w = getWrap();
		$w.removeClass("sc-billing-collapsed").addClass("sc-billing-expanded");
		showExpandedHint(true);
		var top = $w.offset() ? $w.offset().top - 80 : 0;
		$("html, body").animate({ scrollTop: Math.max(0, top) }, 280);
		setTimeout(function() {
			var $inp = $w.find(".woocommerce-billing-fields__field-wrapper :input:visible:enabled").first();
			if ($inp.length) { $inp.trigger("focus"); }
		}, 320);
	});
	$(document).on("click", ".sc-billing-summary-collapse", function(e) {
		e.preventDefault();
		if ($(".woocommerce-checkout .woocommerce-error").length) {
			return;
		}
		window.sessionStorage.removeItem(STORAGE_KEY);
		var $w = getWrap();
		$w.addClass("sc-billing-collapsed").removeClass("sc-billing-expanded");
		showExpandedHint(false);
		var $card = $(".sc-billing-summary-card");
		if ($card.length && $card.offset()) {
			$("html, body").animate({ scrollTop: Math.max(0, $card.offset().top - 100) }, 280);
		}
	});
})(jQuery);
JS;
	wp_add_inline_script( 'sc-checkout-billing-summary', $inline );
}
add_action( 'wp_enqueue_scripts', 'sc_checkout_billing_summary_scripts', 35 );
