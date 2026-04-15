<?php
/**
 * Checkout: termos marcados por padrão + erros de validação em modal (menos ruído visual).
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
 * “Li e concordo com os termos” marcado por padrão no checkout.
 */
add_filter( 'woocommerce_terms_is_checked_default', '__return_true' );

/**
 * Reforça default no array de campos (compatibilidade entre versões do WooCommerce).
 *
 * @param array $fields Checkout fields.
 * @return array
 */
function sc_checkout_terms_field_default_checked( $fields ) {
	if ( isset( $fields['order']['terms'] ) && is_array( $fields['order']['terms'] ) ) {
		$fields['order']['terms']['default'] = 1;
	}
	return $fields;
}
add_filter( 'woocommerce_checkout_fields', 'sc_checkout_terms_field_default_checked', 200 );

/**
 * Markup do modal (footer do checkout).
 */
function sc_checkout_error_modal_markup() {
	if ( ! is_checkout() || is_wc_endpoint_url( 'order-pay' ) ) {
		return;
	}
	?>
	<div id="sc-checkout-error-modal" class="sc-checkout-error-modal" hidden aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="sc-checkout-error-modal-title">
		<div class="sc-checkout-error-modal__backdrop" tabindex="-1"></div>
		<div class="sc-checkout-error-modal__panel">
			<h2 id="sc-checkout-error-modal-title" class="sc-checkout-error-modal__title"><?php esc_html_e( 'Não foi possível concluir o pedido', 'saulocoelho' ); ?></h2>
			<p class="sc-checkout-error-modal__intro"><?php esc_html_e( 'Corrija os pontos abaixo e tente novamente.', 'saulocoelho' ); ?></p>
			<ul id="sc-checkout-error-modal-list" class="sc-checkout-error-modal__list"></ul>
			<button type="button" class="sc-checkout-error-modal__close button" id="sc-checkout-error-modal-close">
				<?php esc_html_e( 'Entendi', 'saulocoelho' ); ?>
			</button>
		</div>
	</div>
	<?php
}
add_action( 'wp_footer', 'sc_checkout_error_modal_markup', 5 );

/**
 * Scripts do modal + integração com checkout_error e erros no carregamento da página.
 */
function sc_checkout_error_modal_scripts() {
	if ( ! is_checkout() || is_wc_endpoint_url( 'order-pay' ) ) {
		return;
	}
	wp_register_script( 'sc-checkout-error-modal', false, array( 'jquery' ), wp_get_theme()->get( 'Version' ), true );
	wp_enqueue_script( 'sc-checkout-error-modal' );
	$inline = <<<'JS'
(function($) {
	var modalSel = "#sc-checkout-error-modal";

	function $modal() {
		return $(modalSel);
	}

	function collectErrorMessages() {
		var msgs = [];
		$(".woocommerce-checkout ul.woocommerce-error > li").each(function() {
			var t = $(this).text().replace(/\s+/g, " ").trim();
			if (t && msgs.indexOf(t) === -1) {
				msgs.push(t);
			}
		});
		$(".woocommerce-checkout .woocommerce-error").not("ul").each(function() {
			var $el = $(this);
			if ($el.closest(modalSel).length || $el.closest("ul.woocommerce-error").length) {
				return;
			}
			var t = $el.text().replace(/\s+/g, " ").trim();
			if (t && msgs.indexOf(t) === -1) {
				msgs.push(t);
			}
		});
		return msgs;
	}

	function hideNoticeGroups() {
		$(".woocommerce-NoticeGroup-checkout").addClass("sc-checkout-error-modal-hide-notices");
		$(".woocommerce-notices-wrapper").each(function() {
			if ($(this).find(".woocommerce-error").length) {
				$(this).addClass("sc-checkout-error-modal-hide-notices");
			}
		});
	}

	function showNoticeGroups() {
		$(".sc-checkout-error-modal-hide-notices").removeClass("sc-checkout-error-modal-hide-notices");
	}

	function openModal() {
		var $m = $modal();
		if (!$m.length) {
			return;
		}
		var msgs = collectErrorMessages();
		if (!msgs.length) {
			return;
		}
		var $list = $m.find("#sc-checkout-error-modal-list");
		$list.empty();
		msgs.forEach(function(m) {
			$list.append($("<li/>").text(m));
		});
		hideNoticeGroups();
		$m.removeAttr("hidden").attr("aria-hidden", "false");
		$("body").addClass("sc-checkout-error-modal-open");
		setTimeout(function() {
			$m.find("#sc-checkout-error-modal-close").trigger("focus");
		}, 50);
	}

	function closeModal() {
		var $m = $modal();
		if ($m.length) {
			$m.attr("hidden", "hidden").attr("aria-hidden", "true");
		}
		$("body").removeClass("sc-checkout-error-modal-open");
		showNoticeGroups();
		var $terms = $("#terms, input[name=terms]").filter(":visible").first();
		if ($terms.length) {
			$terms.trigger("focus");
			var top = $terms.offset() ? $terms.offset().top - 120 : 0;
			$("html, body").animate({ scrollTop: Math.max(0, top) }, 300);
			return;
		}
		var $inv = $(".woocommerce-checkout .woocommerce-invalid:visible").first();
		if ($inv.length) {
			var $focus = $inv.find("input, select, textarea").first();
			if ($focus.length) {
				$focus.trigger("focus");
				var t2 = $focus.offset() ? $focus.offset().top - 120 : 0;
				$("html, body").animate({ scrollTop: Math.max(0, t2) }, 300);
			}
		}
	}

	$(document).on("click", "#sc-checkout-error-modal-close", function(e) {
		e.preventDefault();
		closeModal();
	});
	$(document).on("click", ".sc-checkout-error-modal__backdrop", function(e) {
		e.preventDefault();
		closeModal();
	});
	$(document).on("keydown", function(e) {
		if (e.key === "Escape" && $("body").hasClass("sc-checkout-error-modal-open")) {
			closeModal();
		}
	});

	$(document.body).on("checkout_error", function() {
		setTimeout(openModal, 80);
	});

	$(function() {
		if ($(".woocommerce-checkout .woocommerce-error").length) {
			setTimeout(openModal, 100);
		}
	});

	$(document.body).on("updated_checkout", function() {
		if (!$("body").hasClass("sc-checkout-error-modal-open")) {
			return;
		}
		if (!$(".woocommerce-checkout .woocommerce-error").length) {
			var $m = $modal();
			if ($m.length) {
				$m.attr("hidden", "hidden").attr("aria-hidden", "true");
			}
			$("body").removeClass("sc-checkout-error-modal-open");
			showNoticeGroups();
		}
	});
})(jQuery);
JS;
	wp_add_inline_script( 'sc-checkout-error-modal', $inline );
}
add_action( 'wp_enqueue_scripts', 'sc_checkout_error_modal_scripts', 40 );
