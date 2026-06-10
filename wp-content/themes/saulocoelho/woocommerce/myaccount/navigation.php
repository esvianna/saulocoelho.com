<?php
/**
 * My Account navigation (layout customizado — ícones e rótulos do tema)
 *
 * Base alinhada ao template do WooCommerce para compatibilidade com versões atuais.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_account_navigation' );

$menu_items       = wc_get_account_menu_items();
$logout_endpoint  = 'customer-logout';
$logout_label_src = isset( $menu_items[ $logout_endpoint ] ) ? $menu_items[ $logout_endpoint ] : '';
if ( isset( $menu_items[ $logout_endpoint ] ) ) {
	unset( $menu_items[ $logout_endpoint ] );
}
?>

<nav class="woocommerce-MyAccount-navigation" aria-label="<?php esc_attr_e( 'Account pages', 'woocommerce' ); ?>">
	<div class="flex flex-col gap-2 p-6">
		<p class="text-xs font-bold uppercase tracking-widest text-[#64748b] mb-4"><?php esc_html_e( 'Minha Conta', 'saulocoelho' ); ?></p>

		<?php foreach ( $menu_items as $endpoint => $label ) : ?>
			<?php
			$icon = 'chevron_right';
			if ( 'dashboard' === $endpoint ) {
				$icon = 'school';
				$label = __( 'Meus Cursos', 'saulocoelho' );
			}
			if ( 'orders' === $endpoint ) {
				$icon  = 'receipt_long';
				$label = __( 'Meus Pedidos', 'saulocoelho' );
			}
			if ( 'downloads' === $endpoint ) {
				$icon  = 'workspace_premium';
				$label = __( 'Certificados (Downloads)', 'saulocoelho' );
			}
			if ( 'minhas-turmas' === $endpoint ) {
				$icon = 'photo_library';
			}
			if ( 'edit-address' === $endpoint || 'edit-account' === $endpoint ) {
				$icon = 'settings';
			}

			$is_active = wc_get_account_menu_item_classes( $endpoint );
			$active_class  = strpos( $is_active, 'is-active' ) !== false ? 'bg-primary/10 text-primary' : 'text-[#64748b] dark:text-[#94a3b8] hover:bg-[#f1f5f9] dark:hover:bg-white/5';
			$aria_current = ( function_exists( 'wc_is_current_account_menu_item' ) && wc_is_current_account_menu_item( $endpoint ) ) ? ' aria-current="page"' : '';
			?>
			<a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>" class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium transition-colors <?php echo esc_attr( $active_class ); ?>"<?php echo $aria_current; ?>>
				<span class="material-symbols-outlined rounded bg-transparent p-0 m-0" style="font-size: 20px;"><?php echo esc_html( $icon ); ?></span>
				<?php echo esc_html( $label ); ?>
			</a>
		<?php endforeach; ?>
	</div>

	<?php if ( $logout_label_src ) : ?>
		<div class="p-6 border-t border-slate-200 dark:border-white/5 bg-slate-50 dark:bg-black/20">
			<a href="<?php echo esc_url( wc_get_account_endpoint_url( $logout_endpoint ) ); ?>" class="flex items-center gap-3 text-sm font-bold text-red-500 hover:text-red-600 transition-colors">
				<span class="material-symbols-outlined">logout</span>
				<?php esc_html_e( 'Sair da Conta', 'saulocoelho' ); ?>
			</a>
		</div>
	<?php endif; ?>
</nav>

<?php do_action( 'woocommerce_after_account_navigation' ); ?>
