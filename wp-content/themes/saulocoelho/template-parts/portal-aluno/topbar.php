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
	<a class="sc-portal-topbar__site" href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( $site_name ); ?>">
		<?php esc_html_e( 'Site', 'saulocoelho' ); ?>
	</a>
</header>
