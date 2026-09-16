<?php
/**
 * Portal do Aluno — Web Push (Fase B · issue #16 · ADR-013).
 *
 * Subscriptions + VAPID + envio ao publicar aviso.
 * Opt-in na tab Conta; SW push/notificationclick.
 *
 * @package Saulocoelho
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SC_PORTAL_PUSH_DB', 1 );
define( 'SC_PORTAL_PUSH_OPT', 'saulocoelho_portal_push_db' );
define( 'SC_PORTAL_VAPID_OPT', 'saulocoelho_portal_vapid' );

/**
 * Autoload Composer (minishlink/web-push) se existir.
 */
function sc_portal_push_autoload() {
	static $done = false;
	if ( $done ) {
		return;
	}
	$done = true;
	$autoload = get_template_directory() . '/vendor/autoload.php';
	if ( file_exists( $autoload ) ) {
		require_once $autoload;
	}
}

add_action( 'after_setup_theme', 'sc_portal_push_maybe_upgrade', 6 );
function sc_portal_push_maybe_upgrade() {
	$installed = (int) get_option( SC_PORTAL_PUSH_OPT, 0 );
	if ( $installed < SC_PORTAL_PUSH_DB ) {
		sc_portal_push_install_tables();
		update_option( SC_PORTAL_PUSH_OPT, SC_PORTAL_PUSH_DB, true );
	}
	sc_portal_push_ensure_vapid();
}

/**
 * @return void
 */
function sc_portal_push_install_tables() {
	global $wpdb;
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	$charset = $wpdb->get_charset_collate();
	$t       = $wpdb->prefix . 'sc_portal_push_subs';

	$sql = "CREATE TABLE {$t} (
		id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		user_id bigint(20) unsigned NOT NULL,
		endpoint text NOT NULL,
		endpoint_hash char(64) NOT NULL,
		p256dh varchar(255) NOT NULL,
		auth_key varchar(255) NOT NULL,
		user_agent varchar(255) NOT NULL DEFAULT '',
		created_at datetime NOT NULL,
		updated_at datetime NOT NULL,
		PRIMARY KEY  (id),
		UNIQUE KEY endpoint_hash (endpoint_hash),
		KEY user_id (user_id)
	) {$charset};";

	dbDelta( $sql );
}

/**
 * @return string
 */
function sc_portal_push_subs_table() {
	global $wpdb;
	return $wpdb->prefix . 'sc_portal_push_subs';
}

/**
 * Garante par VAPID (public/private) em options.
 *
 * @return array{publicKey:string,privateKey:string}|null
 */
function sc_portal_push_ensure_vapid() {
	$keys = get_option( SC_PORTAL_VAPID_OPT );
	if ( is_array( $keys ) && ! empty( $keys['publicKey'] ) && ! empty( $keys['privateKey'] ) ) {
		return $keys;
	}

	sc_portal_push_autoload();
	if ( ! class_exists( '\Minishlink\WebPush\VAPID' ) ) {
		return null;
	}

	try {
		$keys = \Minishlink\WebPush\VAPID::createVapidKeys();
	} catch ( Exception $e ) {
		return null;
	}

	if ( empty( $keys['publicKey'] ) || empty( $keys['privateKey'] ) ) {
		return null;
	}

	update_option(
		SC_PORTAL_VAPID_OPT,
		array(
			'publicKey'  => $keys['publicKey'],
			'privateKey' => $keys['privateKey'],
		),
		false
	);

	return $keys;
}

/**
 * @return string
 */
function sc_portal_push_public_key() {
	$keys = sc_portal_push_ensure_vapid();
	return ( is_array( $keys ) && ! empty( $keys['publicKey'] ) ) ? (string) $keys['publicKey'] : '';
}

/**
 * @return bool
 */
function sc_portal_push_ready() {
	sc_portal_push_autoload();
	return class_exists( '\Minishlink\WebPush\WebPush' ) && '' !== sc_portal_push_public_key();
}

/* -------------------------------------------------------------------------- */
/* REST                                                                       */
/* -------------------------------------------------------------------------- */

add_action( 'rest_api_init', 'sc_portal_push_register_rest' );
function sc_portal_push_register_rest() {
	register_rest_route(
		'saulocoelho/v1',
		'/push/status',
		array(
			'methods'             => 'GET',
			'callback'            => 'sc_portal_push_rest_status',
			'permission_callback' => function () {
				return is_user_logged_in();
			},
		)
	);

	register_rest_route(
		'saulocoelho/v1',
		'/push/subscribe',
		array(
			'methods'             => 'POST',
			'callback'            => 'sc_portal_push_rest_subscribe',
			'permission_callback' => function () {
				return is_user_logged_in();
			},
		)
	);

	register_rest_route(
		'saulocoelho/v1',
		'/push/unsubscribe',
		array(
			'methods'             => 'POST',
			'callback'            => 'sc_portal_push_rest_unsubscribe',
			'permission_callback' => function () {
				return is_user_logged_in();
			},
		)
	);
}

/**
 * @return WP_REST_Response
 */
function sc_portal_push_rest_status() {
	$user_id = get_current_user_id();
	$count   = sc_portal_push_user_sub_count( $user_id );
	return rest_ensure_response(
		array(
			'ready'           => sc_portal_push_ready(),
			'vapidPublicKey'  => sc_portal_push_public_key(),
			'subscribed'      => $count > 0,
			'subscriptionCount' => $count,
		)
	);
}

/**
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response|WP_Error
 */
function sc_portal_push_rest_subscribe( $request ) {
	if ( ! sc_portal_push_ready() ) {
		return new WP_Error( 'sc_push_unavailable', __( 'Push indisponível neste servidor.', 'saulocoelho' ), array( 'status' => 503 ) );
	}

	$params   = $request->get_json_params();
	$endpoint = isset( $params['endpoint'] ) ? esc_url_raw( (string) $params['endpoint'] ) : '';
	$p256dh   = isset( $params['keys']['p256dh'] ) ? sanitize_text_field( (string) $params['keys']['p256dh'] ) : '';
	$auth     = isset( $params['keys']['auth'] ) ? sanitize_text_field( (string) $params['keys']['auth'] ) : '';

	if ( '' === $endpoint || '' === $p256dh || '' === $auth ) {
		return new WP_Error( 'sc_push_invalid', __( 'Subscription inválida.', 'saulocoelho' ), array( 'status' => 400 ) );
	}

	if ( ! sc_portal_push_endpoint_allowed( $endpoint ) ) {
		return new WP_Error( 'sc_push_endpoint', __( 'Endpoint de push não permitido.', 'saulocoelho' ), array( 'status' => 400 ) );
	}

	$user_id = get_current_user_id();
	$hash    = hash( 'sha256', $endpoint );
	$now     = current_time( 'mysql', true );
	$ua      = isset( $_SERVER['HTTP_USER_AGENT'] ) ? substr( sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ), 0, 255 ) : '';

	global $wpdb;
	$table = sc_portal_push_subs_table();

	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$existing = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$table} WHERE endpoint_hash = %s", $hash ) );

	$data = array(
		'user_id'       => $user_id,
		'endpoint'      => $endpoint,
		'endpoint_hash' => $hash,
		'p256dh'        => $p256dh,
		'auth_key'      => $auth,
		'user_agent'    => $ua,
		'updated_at'    => $now,
	);

	if ( $existing ) {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->update( $table, $data, array( 'id' => (int) $existing ), array( '%d', '%s', '%s', '%s', '%s', '%s', '%s' ), array( '%d' ) );
	} else {
		$data['created_at'] = $now;
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->insert( $table, $data, array( '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s' ) );
	}

	return rest_ensure_response(
		array(
			'success'           => true,
			'subscribed'        => true,
			'subscriptionCount' => sc_portal_push_user_sub_count( $user_id ),
		)
	);
}

/**
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response|WP_Error
 */
function sc_portal_push_rest_unsubscribe( $request ) {
	$params   = $request->get_json_params();
	$endpoint = isset( $params['endpoint'] ) ? esc_url_raw( (string) $params['endpoint'] ) : '';
	$user_id  = get_current_user_id();
	global $wpdb;
	$table = sc_portal_push_subs_table();

	if ( '' !== $endpoint ) {
		$hash = hash( 'sha256', $endpoint );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->delete(
			$table,
			array(
				'user_id'       => $user_id,
				'endpoint_hash' => $hash,
			),
			array( '%d', '%s' )
		);
	} else {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->delete( $table, array( 'user_id' => $user_id ), array( '%d' ) );
	}

	return rest_ensure_response(
		array(
			'success'           => true,
			'subscribed'        => sc_portal_push_user_sub_count( $user_id ) > 0,
			'subscriptionCount' => sc_portal_push_user_sub_count( $user_id ),
		)
	);
}

/**
 * @param int $user_id User ID.
 * @return int
 */
function sc_portal_push_user_sub_count( $user_id ) {
	$user_id = (int) $user_id;
	if ( $user_id < 1 ) {
		return 0;
	}
	global $wpdb;
	$table = sc_portal_push_subs_table();
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	return (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$table} WHERE user_id = %d", $user_id ) );
}

/**
 * Só FCM / Mozilla / Apple push hosts.
 *
 * @param string $endpoint Endpoint URL.
 * @return bool
 */
function sc_portal_push_endpoint_allowed( $endpoint ) {
	$host = wp_parse_url( $endpoint, PHP_URL_HOST );
	if ( ! is_string( $host ) || '' === $host ) {
		return false;
	}
	$host = strtolower( $host );
	if ( 'fcm.googleapis.com' === $host || 'android.googleapis.com' === $host ) {
		return true;
	}
	if ( 'updates.push.services.mozilla.com' === $host || preg_match( '/\.push\.services\.mozilla\.com$/', $host ) ) {
		return true;
	}
	if ( 'web.push.apple.com' === $host || preg_match( '/\.push\.apple\.com$/', $host ) ) {
		return true;
	}
	if ( preg_match( '/\.notify\.windows\.com$/', $host ) ) {
		return true;
	}
	return false;
}

/**
 * Remove subscription por hash ou id.
 *
 * @param int $id Row ID.
 * @return void
 */
function sc_portal_push_delete_sub( $id ) {
	global $wpdb;
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$wpdb->delete( sc_portal_push_subs_table(), array( 'id' => (int) $id ), array( '%d' ) );
}

/**
 * Envia push para subscribers que casam com a audiência do aviso.
 *
 * @param object|int $notice Notice row or ID.
 * @return array{sent:int,failed:int,removed:int,skipped:int,error?:string}
 */
function sc_portal_push_send_notice( $notice ) {
	$result = array(
		'sent'    => 0,
		'failed'  => 0,
		'removed' => 0,
		'skipped' => 0,
	);

	if ( ! sc_portal_push_ready() ) {
		$result['error'] = 'unavailable';
		return $result;
	}

	global $wpdb;
	if ( is_numeric( $notice ) ) {
		$n = sc_portal_notices_table();
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$notice = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$n} WHERE id = %d", (int) $notice ) );
	}
	if ( ! $notice || empty( $notice->id ) ) {
		$result['error'] = 'notice';
		return $result;
	}

	$audience = isset( $notice->audience ) ? (string) $notice->audience : 'all';
	$table    = sc_portal_push_subs_table();
	// Cap Fase B (Fase C = filas). phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$rows = $wpdb->get_results( "SELECT * FROM {$table} ORDER BY id ASC LIMIT 200" );
	if ( empty( $rows ) ) {
		return $result;
	}

	$payload = wp_json_encode(
		array(
			'title' => (string) $notice->title,
			'body'  => wp_strip_all_tags( (string) $notice->body ),
			'url'   => ! empty( $notice->link_url ) ? (string) $notice->link_url : ( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : home_url( '/minha-conta/' ) ),
			'tag'   => 'sc-notice-' . (int) $notice->id,
		)
	);

	$keys = get_option( SC_PORTAL_VAPID_OPT );
	sc_portal_push_autoload();

	try {
		$auth = array(
			'VAPID' => array(
				'subject'    => 'mailto:contato@saulocoelho.com',
				'publicKey'  => $keys['publicKey'],
				'privateKey' => $keys['privateKey'],
			),
		);
		$webPush = new \Minishlink\WebPush\WebPush( $auth );
		$webPush->setReuseVAPIDHeaders( true );
	} catch ( Exception $e ) {
		$result['error'] = 'init';
		return $result;
	}

	$notifications = array();
	foreach ( $rows as $row ) {
		if ( ! function_exists( 'sc_portal_notice_user_matches' ) || ! sc_portal_notice_user_matches( (int) $row->user_id, $audience ) ) {
			$result['skipped']++;
			continue;
		}
		try {
			$sub = \Minishlink\WebPush\Subscription::create(
				array(
					'endpoint' => $row->endpoint,
					'keys'     => array(
						'p256dh' => $row->p256dh,
						'auth'   => $row->auth_key,
					),
				)
			);
			$webPush->queueNotification( $sub, $payload, array( 'TTL' => 86400 ) );
			$notifications[ $row->endpoint ] = (int) $row->id;
		} catch ( Exception $e ) {
			$result['failed']++;
		}
	}

	foreach ( $webPush->flush() as $report ) {
		$endpoint = $report->getEndpoint();
		$sub_id   = isset( $notifications[ $endpoint ] ) ? $notifications[ $endpoint ] : 0;
		if ( $report->isSuccess() ) {
			$result['sent']++;
			continue;
		}
		$result['failed']++;
		$code = $report->getResponse() ? $report->getResponse()->getStatusCode() : 0;
		if ( in_array( (int) $code, array( 404, 410 ), true ) && $sub_id > 0 ) {
			sc_portal_push_delete_sub( $sub_id );
			$result['removed']++;
		}
	}

	return $result;
}

/* -------------------------------------------------------------------------- */
/* Admin: checkbox ao guardar                                                 */
/* -------------------------------------------------------------------------- */

add_action( 'admin_init', 'sc_portal_push_admin_hooks' );
function sc_portal_push_admin_hooks() {
	// Injected via filter on save in notices module — see sc_portal_push_on_notice_saved.
}

/**
 * Chamado após guardar aviso publicado (pelo módulo notices).
 * Fase C: enfileira (lote + cron). Fallback síncrono se a fila não existir.
 *
 * @param int  $notice_id  Notice ID.
 * @param bool $send_push  Whether to send push.
 * @param bool $send_email Whether to send e-mail.
 * @return array<string,mixed>|null
 */
function sc_portal_push_on_notice_saved( $notice_id, $send_push, $send_email = false ) {
	if ( ! $send_push && ! $send_email ) {
		return null;
	}
	if ( function_exists( 'sc_portal_push_on_notice_saved_v2' ) ) {
		return sc_portal_push_on_notice_saved_v2( (int) $notice_id, (bool) $send_push, (bool) $send_email );
	}
	if ( ! $send_push ) {
		return null;
	}
	return sc_portal_push_send_notice( (int) $notice_id );
}

/* -------------------------------------------------------------------------- */
/* Front: Conta opt-in + localize                                             */
/* -------------------------------------------------------------------------- */

add_filter( 'saulocoelho_portal_js_data', 'sc_portal_push_js_data', 20 );
/**
 * @param array<string,mixed> $data Data.
 * @return array<string,mixed>
 */
function sc_portal_push_js_data( $data ) {
	if ( ! is_array( $data ) ) {
		$data = array();
	}
	$data['push'] = array(
		'ready'          => sc_portal_push_ready(),
		'vapidPublicKey' => sc_portal_push_public_key(),
		'restUrl'        => esc_url_raw( rest_url( 'saulocoelho/v1/push' ) ),
		'nonce'          => wp_create_nonce( 'wp_rest' ),
		'subscribed'     => sc_portal_push_user_sub_count( get_current_user_id() ) > 0,
		'labels'         => array(
			'enable'      => __( 'Ativar notificações neste dispositivo', 'saulocoelho' ),
			'disable'     => __( 'Desativar neste dispositivo', 'saulocoelho' ),
			'enabled'     => __( 'Notificações ativas neste dispositivo.', 'saulocoelho' ),
			'denied'      => __( 'Permissão negada no navegador. Você ainda pode ver avisos no sininho.', 'saulocoelho' ),
			'unsupported' => __( 'Este dispositivo/navegador não suporta Web Push.', 'saulocoelho' ),
			'iosHint'     => __( 'No iPhone: instale o Portal na Tela de Início e ative aqui depois.', 'saulocoelho' ),
			'error'       => __( 'Não foi possível ativar as notificações.', 'saulocoelho' ),
			'loading'     => __( 'Processando…', 'saulocoelho' ),
		),
	);
	return $data;
}

add_action( 'woocommerce_account_content', 'sc_portal_push_render_conta_block', 2 );
/**
 * Bloco opt-in na tab Conta (após hub de links).
 */
function sc_portal_push_render_conta_block() {
	if ( ! function_exists( 'saulocoelho_is_portal_aluno' ) || ! saulocoelho_is_portal_aluno() ) {
		return;
	}
	if ( ! function_exists( 'saulocoelho_portal_active_tab' ) || 'conta' !== saulocoelho_portal_active_tab() ) {
		return;
	}
	?>
	<section class="sc-portal-push" data-sc-portal-push hidden>
		<div class="sc-portal-push__card">
			<div class="sc-portal-push__icon" aria-hidden="true">
				<span class="material-symbols-outlined">notifications_active</span>
			</div>
			<div class="sc-portal-push__body">
				<h3 class="sc-portal-push__title"><?php esc_html_e( 'Notificações', 'saulocoelho' ); ?></h3>
				<p class="sc-portal-push__desc">
					<?php esc_html_e( 'Receba avisos da equipe mesmo com o Portal fechado. O sininho continua funcionando sem isso.', 'saulocoelho' ); ?>
				</p>
				<p class="sc-portal-push__status" data-sc-portal-push-status></p>
				<p class="sc-portal-push__hint" data-sc-portal-push-hint hidden></p>
				<div class="sc-portal-push__actions">
					<button type="button" class="sc-portal-push__btn sc-portal-push__btn--primary" data-sc-portal-push-enable>
						<?php esc_html_e( 'Ativar notificações neste dispositivo', 'saulocoelho' ); ?>
					</button>
					<button type="button" class="sc-portal-push__btn sc-portal-push__btn--ghost" data-sc-portal-push-disable hidden>
						<?php esc_html_e( 'Desativar neste dispositivo', 'saulocoelho' ); ?>
					</button>
				</div>
			</div>
		</div>
	</section>
	<?php
}
