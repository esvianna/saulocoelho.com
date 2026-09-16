<?php
/**
 * Top bar do Portal do Aluno (substitui header de marketing).
 *
 * @package Saulocoelho
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$account   = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : home_url( '/minha-conta/' );
$icon_url  = function_exists( 'saulocoelho_portal_icon_url' ) ? saulocoelho_portal_icon_url( 192 ) : '';
$site_name = get_bloginfo( 'name' );
?>
<header class="sc-portal-topbar" role="banner">
	<a class="sc-portal-topbar__brand" href="<?php echo esc_url( $account ); ?>">
		<?php if ( $icon_url ) : ?>
			<img
				class="sc-portal-topbar__mark"
				src="<?php echo esc_url( $icon_url ); ?>"
				alt=""
				width="32"
				height="32"
				decoding="async"
			/>
		<?php else : ?>
			<span class="sc-portal-topbar__mark sc-portal-topbar__mark--fallback" aria-hidden="true">SC</span>
		<?php endif; ?>
		<span class="sc-portal-topbar__title"><?php esc_html_e( 'Portal do Aluno', 'saulocoelho' ); ?></span>
	</a>
	<div class="sc-portal-topbar__actions">
		<div class="sc-portal-bell" data-sc-portal-bell>
			<button
				type="button"
				class="sc-portal-bell__btn"
				data-sc-portal-bell-btn
				aria-expanded="false"
				aria-controls="sc-portal-bell-panel"
				aria-label="<?php esc_attr_e( 'Avisos', 'saulocoelho' ); ?>"
			>
				<span class="material-symbols-outlined" aria-hidden="true">notifications</span>
				<span class="sc-portal-bell__badge" data-sc-portal-bell-badge hidden>0</span>
			</button>
			<div class="sc-portal-bell__panel" id="sc-portal-bell-panel" data-sc-portal-bell-panel hidden role="region" aria-label="<?php esc_attr_e( 'Lista de avisos', 'saulocoelho' ); ?>">
				<p class="sc-portal-bell__heading"><?php esc_html_e( 'Avisos', 'saulocoelho' ); ?></p>
				<ul class="sc-portal-bell__list" data-sc-portal-bell-list></ul>
				<p class="sc-portal-bell__empty" data-sc-portal-bell-empty hidden><?php esc_html_e( 'Sem avisos por agora.', 'saulocoelho' ); ?></p>
			</div>
		</div>
		<a class="sc-portal-topbar__site" href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( $site_name ); ?>">
			<?php esc_html_e( 'Site', 'saulocoelho' ); ?>
		</a>
	</div>
</header>
