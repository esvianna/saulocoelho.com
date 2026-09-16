<?php
/**
 * Avisos de erro — sem prefixo "Erro:" duplicado.
 *
 * @see woocommerce/templates/notices/error.php
 * @package WooCommerce\Templates
 * @version 8.6.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! $notices ) {
	return;
}
?>
<ul class="woocommerce-error sc-notice sc-notice--error" role="alert">
	<?php foreach ( $notices as $notice ) : ?>
		<li<?php echo function_exists( 'wc_get_notice_data_attr' ) ? wc_get_notice_data_attr( $notice ) : ''; ?>>
			<?php echo wc_kses_notice( is_array( $notice ) ? $notice['notice'] : $notice ); ?>
		</li>
	<?php endforeach; ?>
</ul>
