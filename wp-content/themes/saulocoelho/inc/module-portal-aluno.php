<?php
/**
 * Portal do Aluno — app-shell Minha Conta + PWA (só logados).
 *
 * Issue #15. Separado do PWA OCD (app.saulocoelho.com).
 *
 * @package Saulocoelho
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Contexto do portal: Minha Conta **ou** sala/player do LMS, com utilizador autenticado.
 * Convite público (`/inscricao/`) fica de fora.
 */
function saulocoelho_is_portal_aluno() {
	if ( ! is_user_logged_in() ) {
		return false;
	}
	if ( class_exists( '\AmaEducacional\Frontend\InviteRegistration' )
		&& \AmaEducacional\Frontend\InviteRegistration::is_invite_page() ) {
		return false;
	}
	if ( function_exists( 'is_account_page' ) && is_account_page() ) {
		return true;
	}
	return saulocoelho_is_portal_lms_content();
}

/**
 * Páginas de curso/aula do AmaEducacional (hub + player).
 */
function saulocoelho_is_portal_lms_content() {
	return is_singular( array( 'ama_course', 'ama_lesson' ) );
}

/**
 * Ícone do PWA / topbar = ícone do site (Customizer), com fallback ao asset do tema.
 *
 * @param int $size Tamanho pedido (192 ou 512).
 * @return string URL absoluta.
 */
function saulocoelho_portal_icon_url( $size = 192 ) {
	$size = (int) $size;
	if ( $size < 1 ) {
		$size = 192;
	}
	$url = '';
	if ( function_exists( 'get_site_icon_url' ) ) {
		$url = get_site_icon_url( $size );
	}
	if ( ! $url ) {
		$file = $size >= 512 ? 'icon-512.png' : 'icon-192.png';
		$url  = get_template_directory_uri() . '/assets/portal-aluno/' . $file;
	}
	// Cache-bust: ícones de ecrã inicial / PWA ficam presos sem query string.
	$ver = wp_get_theme()->get( 'Version' );
	return add_query_arg( 'v', $ver ? $ver : '1', $url );
}

/**
 * Endpoints que pertencem à tab Conta (não são tabs próprias).
 *
 * @return string[]
 */
function saulocoelho_portal_conta_endpoints() {
	return array(
		'edit-account',
		'edit-address',
		'orders',
		'payment-methods',
		'questionario-presencial',
		'minhas-turmas',
		'view-order',
	);
}

/**
 * Tab activa: cursos | certificados | conta.
 */
function saulocoelho_portal_active_tab() {
	if ( saulocoelho_is_portal_lms_content() ) {
		return 'cursos';
	}
	if ( function_exists( 'is_wc_endpoint_url' ) ) {
		if ( is_wc_endpoint_url( 'downloads' ) ) {
			return 'certificados';
		}
		foreach ( saulocoelho_portal_conta_endpoints() as $ep ) {
			if ( is_wc_endpoint_url( $ep ) ) {
				return 'conta';
			}
		}
	}
	return 'cursos';
}

/**
 * URL da tab Conta (hub = detalhes da conta).
 */
function saulocoelho_portal_conta_url() {
	return function_exists( 'wc_get_account_endpoint_url' )
		? wc_get_account_endpoint_url( 'edit-account' )
		: home_url( '/minha-conta/' );
}

/**
 * URLs das três tabs.
 *
 * @return array<string,string>
 */
function saulocoelho_portal_tab_urls() {
	$account = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : home_url( '/minha-conta/' );
	return array(
		'cursos'       => $account,
		'certificados' => function_exists( 'wc_get_account_endpoint_url' ) ? wc_get_account_endpoint_url( 'downloads' ) : trailingslashit( $account ) . 'downloads/',
		'conta'        => saulocoelho_portal_conta_url(),
	);
}

/**
 * Itens secundários da Conta a partir do menu Woo (sem Cursos/Certificados/logout).
 *
 * @return array<string,string> endpoint => label
 */
function saulocoelho_portal_conta_menu_items() {
	if ( ! function_exists( 'wc_get_account_menu_items' ) ) {
		return array();
	}
	$items = wc_get_account_menu_items();
	$skip  = array( 'dashboard', 'downloads', 'customer-logout' );
	$order = array(
		'edit-account',
		'edit-address',
		'orders',
		'payment-methods',
		'questionario-presencial',
		'minhas-turmas',
	);
	$out = array();
	foreach ( $order as $key ) {
		if ( isset( $items[ $key ] ) ) {
			$out[ $key ] = $items[ $key ];
		}
	}
	foreach ( $items as $endpoint => $label ) {
		if ( isset( $out[ $endpoint ] ) || in_array( $endpoint, $skip, true ) ) {
			continue;
		}
		$out[ $endpoint ] = $label;
	}
	return $out;
}

add_filter( 'body_class', 'saulocoelho_portal_body_class' );
function saulocoelho_portal_body_class( $classes ) {
	if ( saulocoelho_is_portal_aluno() ) {
		$classes[] = 'sc-portal-aluno';
		$classes[] = 'sc-portal-tab-' . saulocoelho_portal_active_tab();
	}
	return $classes;
}

add_action( 'wp_enqueue_scripts', 'saulocoelho_portal_assets', 30 );
function saulocoelho_portal_assets() {
	if ( ! saulocoelho_is_portal_aluno() ) {
		return;
	}
	$ver  = wp_get_theme()->get( 'Version' );
	$base = get_template_directory_uri() . '/assets/portal-aluno';
	wp_enqueue_style( 'saulocoelho-portal-aluno', $base . '/portal.css', array( 'saulocoelho-style' ), $ver );
	wp_enqueue_script( 'saulocoelho-portal-aluno', $base . '/portal.js', array(), $ver, true );
	wp_localize_script(
		'saulocoelho-portal-aluno',
		'scPortalAluno',
		apply_filters(
			'saulocoelho_portal_js_data',
			array(
				'manifestUrl' => home_url( '/portal-aluno/manifest.webmanifest' ),
				'swUrl'       => home_url( '/portal-aluno/sw.js' ),
				'installLabel'=> __( 'Instalar Portal do Aluno', 'saulocoelho' ),
				'iosHint'     => __( 'No iPhone: toque em Compartilhar e depois em “Adicionar à Tela de Início”.', 'saulocoelho' ),
				'installed'   => __( 'App já instalado neste dispositivo.', 'saulocoelho' ),
				'dismissLabel'=> __( 'Agora não', 'saulocoelho' ),
				'scope'       => home_url( '/' ),
				'accountUrl'  => function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : home_url( '/minha-conta/' ),
				'resumeParam' => 'sc_portal_resume',
			)
		)
	);
}

/**
 * Banner PWA: oculto no PHP; o JS mostra se não for standalone e não estiver dispensado.
 *
 * @param string $variant 'dash' | 'conta'.
 */
function saulocoelho_portal_render_install_banner( $variant = 'dash' ) {
	$variant = 'conta' === $variant ? 'conta' : 'dash';
	$id      = 'dash' === $variant ? 'sc-portal-install-dash' : 'sc-portal-install';
	$title   = __( 'Instale o Portal do Aluno', 'saulocoelho' );
	$text    = 'dash' === $variant
		? __( 'Acesso rápido aos cursos na tela inicial do celular — como um app.', 'saulocoelho' )
		: __( 'Acesso rápido aos cursos na tela inicial. Disponível só nesta área.', 'saulocoelho' );
	?>
	<div class="sc-portal-install sc-portal-install--<?php echo esc_attr( $variant ); ?>" id="<?php echo esc_attr( $id ); ?>" hidden data-sc-portal-install>
		<div class="sc-portal-install__body">
			<p class="sc-portal-install__title"><?php echo esc_html( $title ); ?></p>
			<p class="sc-portal-install__text"><?php echo esc_html( $text ); ?></p>
			<div class="sc-portal-install__actions">
				<button type="button" class="sc-portal-install__btn" data-sc-portal-install-btn><?php esc_html_e( 'Instalar app', 'saulocoelho' ); ?></button>
				<button type="button" class="sc-portal-install__dismiss" data-sc-portal-install-dismiss><?php esc_html_e( 'Agora não', 'saulocoelho' ); ?></button>
			</div>
			<p class="sc-portal-install__ios" data-sc-portal-install-ios hidden></p>
		</div>
	</div>
	<?php
}

add_action( 'wp_head', 'saulocoelho_portal_manifest_link', 5 );
function saulocoelho_portal_manifest_link() {
	if ( ! saulocoelho_is_portal_aluno() ) {
		return;
	}
	echo '<link rel="manifest" href="' . esc_url( home_url( '/portal-aluno/manifest.webmanifest' ) ) . '">' . "\n";
	echo '<meta name="theme-color" content="#050A14">' . "\n";
	echo '<meta name="apple-mobile-web-app-capable" content="yes">' . "\n";
	echo '<meta name="apple-mobile-web-app-title" content="' . esc_attr__( 'Portal do Aluno', 'saulocoelho' ) . '">' . "\n";
	echo '<link rel="apple-touch-icon" href="' . esc_url( saulocoelho_portal_icon_url( 192 ) ) . '">' . "\n";
}

add_action( 'init', 'saulocoelho_portal_rewrite' );
function saulocoelho_portal_rewrite() {
	add_rewrite_rule( '^portal-aluno/manifest\.webmanifest$', 'index.php?sc_portal_manifest=1', 'top' );
	add_rewrite_rule( '^portal-aluno/sw\.js$', 'index.php?sc_portal_sw=1', 'top' );
}

add_filter( 'query_vars', 'saulocoelho_portal_query_vars' );
function saulocoelho_portal_query_vars( $vars ) {
	$vars[] = 'sc_portal_manifest';
	$vars[] = 'sc_portal_sw';
	return $vars;
}

add_action( 'template_redirect', 'saulocoelho_portal_serve_pwa_files', 0 );
function saulocoelho_portal_serve_pwa_files() {
	if ( get_query_var( 'sc_portal_manifest' ) ) {
		saulocoelho_portal_output_manifest();
		exit;
	}
	if ( get_query_var( 'sc_portal_sw' ) ) {
		saulocoelho_portal_output_sw();
		exit;
	}
}

function saulocoelho_portal_output_manifest() {
	// Manifest público (necessário antes do login no install); start_url exige auth no WP.
	// ?sc_portal_resume=1 → JS restaura a última página do Portal (aula/curso) ao abrir a PWA.
	$start    = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : home_url( '/minha-conta/' );
	$start    = add_query_arg( 'sc_portal_resume', '1', $start );
	$icon_192 = saulocoelho_portal_icon_url( 192 );
	$icon_512 = saulocoelho_portal_icon_url( 512 );
	$manifest = array(
		'name'             => 'Portal do Aluno — Saulo Coelho',
		'short_name'       => 'Portal do Aluno',
		'description'      => __( 'Cursos, certificados e conta do aluno.', 'saulocoelho' ),
		'start_url'        => $start,
		'id'               => '/portal-aluno/',
		'scope'            => home_url( '/' ),
		'display'          => 'standalone',
		'background_color' => '#050A14',
		'theme_color'      => '#050A14',
		'lang'             => 'pt-BR',
		'icons'            => array(
			array(
				'src'     => $icon_192,
				'sizes'   => '192x192',
				'type'    => 'image/png',
				'purpose' => 'any',
			),
			array(
				'src'     => $icon_512,
				'sizes'   => '512x512',
				'type'    => 'image/png',
				'purpose' => 'any',
			),
			array(
				'src'     => $icon_192,
				'sizes'   => '192x192',
				'type'    => 'image/png',
				'purpose' => 'maskable',
			),
			array(
				'src'     => $icon_512,
				'sizes'   => '512x512',
				'type'    => 'image/png',
				'purpose' => 'maskable',
			),
		),
	);
	nocache_headers();
	header( 'Content-Type: application/manifest+json; charset=utf-8' );
	echo wp_json_encode( $manifest );
}

function saulocoelho_portal_output_sw() {
	$path = get_template_directory() . '/assets/portal-aluno/sw.js';
	if ( ! is_readable( $path ) ) {
		status_header( 404 );
		exit;
	}
	nocache_headers();
	header( 'Content-Type: application/javascript; charset=utf-8' );
	header( 'Service-Worker-Allowed: /' );
	// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	echo file_get_contents( $path );
}

/**
 * Flush rewrite once após activar rotas do portal.
 */
add_action( 'init', 'saulocoelho_portal_maybe_flush', 99 );
function saulocoelho_portal_maybe_flush() {
	$flag = 'saulocoelho_portal_rewrite_v1';
	if ( get_option( $flag ) === '1' ) {
		return;
	}
	flush_rewrite_rules( false );
	update_option( $flag, '1', false );
}

add_action( 'woocommerce_before_account_navigation', 'saulocoelho_portal_render_shell_start', 5 );
function saulocoelho_portal_render_shell_start() {
	if ( ! saulocoelho_is_portal_aluno() ) {
		return;
	}
	// Marcador para CSS; tabs/header injectados via wp_footer + template.
}

add_action( 'wp_body_open', 'saulocoelho_portal_skip_to_content', 1 );
function saulocoelho_portal_skip_to_content() {
	// reserved
}

/**
 * Banner instalar + lista Conta no topo do conteúdo (tab Conta).
 */
add_action( 'woocommerce_account_content', 'saulocoelho_portal_conta_hub_prefix', 1 );
function saulocoelho_portal_conta_hub_prefix() {
	if ( ! saulocoelho_is_portal_aluno() ) {
		return;
	}
	if ( saulocoelho_portal_active_tab() !== 'conta' ) {
		return;
	}
	$items = saulocoelho_portal_conta_menu_items();
	$icons = array(
		'edit-account'             => 'manage_accounts',
		'edit-address'             => 'home',
		'orders'                   => 'receipt_long',
		'payment-methods'          => 'credit_card',
		'questionario-presencial'  => 'assignment',
		'minhas-turmas'            => 'photo_library',
	);
	?>
	<div class="sc-portal-conta-hub">
		<?php saulocoelho_portal_render_install_banner( 'conta' ); ?>
		<?php if ( $items ) : ?>
			<nav class="sc-portal-conta-links" aria-label="<?php esc_attr_e( 'Opções da conta', 'saulocoelho' ); ?>">
				<?php foreach ( $items as $endpoint => $label ) : ?>
					<?php
					$url    = wc_get_account_endpoint_url( $endpoint );
					$active = function_exists( 'is_wc_endpoint_url' ) && is_wc_endpoint_url( $endpoint );
					$icon   = isset( $icons[ $endpoint ] ) ? $icons[ $endpoint ] : 'chevron_right';
					if ( 'edit-account' === $endpoint ) {
						$label = __( 'Detalhes da conta', 'saulocoelho' );
					}
					if ( 'minhas-turmas' === $endpoint ) {
						$label = wp_strip_all_tags( $label );
					}
					?>
					<a class="sc-portal-conta-link<?php echo $active ? ' is-active' : ''; ?>" href="<?php echo esc_url( $url ); ?>">
						<span class="material-symbols-outlined" aria-hidden="true"><?php echo esc_html( $icon ); ?></span>
						<span class="sc-portal-conta-link__label"><?php echo esc_html( $label ); ?></span>
						<span class="material-symbols-outlined sc-portal-conta-link__chev" aria-hidden="true">chevron_right</span>
					</a>
				<?php endforeach; ?>
				<a class="sc-portal-conta-link sc-portal-conta-link--logout" href="<?php echo esc_url( wc_get_account_endpoint_url( 'customer-logout' ) ); ?>">
					<span class="material-symbols-outlined" aria-hidden="true">logout</span>
					<span class="sc-portal-conta-link__label"><?php esc_html_e( 'Sair', 'saulocoelho' ); ?></span>
				</a>
			</nav>
		<?php endif; ?>
	</div>
	<?php
}

add_action( 'wp_footer', 'saulocoelho_portal_render_tabs', 5 );
function saulocoelho_portal_render_tabs() {
	if ( ! saulocoelho_is_portal_aluno() ) {
		return;
	}
	$tabs   = saulocoelho_portal_tab_urls();
	$active = saulocoelho_portal_active_tab();
	$defs   = array(
		'cursos'       => array( 'label' => __( 'Cursos', 'saulocoelho' ), 'icon' => 'school' ),
		'certificados' => array( 'label' => __( 'Certificados', 'saulocoelho' ), 'icon' => 'workspace_premium' ),
		'conta'        => array( 'label' => __( 'Conta', 'saulocoelho' ), 'icon' => 'person' ),
	);
	?>
	<nav class="sc-portal-tabs" aria-label="<?php esc_attr_e( 'Portal do Aluno', 'saulocoelho' ); ?>">
		<?php foreach ( $defs as $key => $def ) : ?>
			<a
				href="<?php echo esc_url( $tabs[ $key ] ); ?>"
				class="sc-portal-tabs__item<?php echo $active === $key ? ' is-active' : ''; ?>"
				<?php echo $active === $key ? ' aria-current="page"' : ''; ?>
			>
				<span class="material-symbols-outlined" aria-hidden="true"><?php echo esc_html( $def['icon'] ); ?></span>
				<span class="sc-portal-tabs__label"><?php echo esc_html( $def['label'] ); ?></span>
			</a>
		<?php endforeach; ?>
	</nav>
	<?php
}
