<?php
/**
 * Portal do Aluno — Web Push Fase C (fila / cron / relatório / e-mail).
 *
 * Extende module-portal-push.php (Fase B). Issue #16 · ADR-013.
 *
 * @package Saulocoelho
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SC_PORTAL_PUSH_BATCH', 30 );
define( 'SC_PORTAL_PUSH_MUTED_OPT', 'saulocoelho_portal_push_muted_courses' );
define( 'SC_PORTAL_PUSH_CRON', 'sc_portal_push_process_queue' );

/**
 * Upgrade Fase C: tabela de jobs + cron.
 */
add_action( 'after_setup_theme', 'sc_portal_push_fase_c_maybe_upgrade', 7 );
function sc_portal_push_fase_c_maybe_upgrade() {
	$installed = (int) get_option( SC_PORTAL_PUSH_OPT, 0 );
	if ( $installed < 2 ) {
		sc_portal_push_install_jobs_table();
		update_option( SC_PORTAL_PUSH_OPT, 2, true );
	}
	sc_portal_push_schedule_cron();
}

/**
 * @return void
 */
function sc_portal_push_install_jobs_table() {
	global $wpdb;
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	$charset = $wpdb->get_charset_collate();
	$t       = $wpdb->prefix . 'sc_portal_push_jobs';

	$sql = "CREATE TABLE {$t} (
		id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		notice_id bigint(20) unsigned NOT NULL,
		status varchar(20) NOT NULL DEFAULT 'pending',
		send_push tinyint(1) NOT NULL DEFAULT 1,
		send_email tinyint(1) NOT NULL DEFAULT 0,
		cursor_sub_id bigint(20) unsigned NOT NULL DEFAULT 0,
		cursor_email_user_id bigint(20) unsigned NOT NULL DEFAULT 0,
		push_done tinyint(1) NOT NULL DEFAULT 0,
		email_done tinyint(1) NOT NULL DEFAULT 0,
		sent int(10) unsigned NOT NULL DEFAULT 0,
		failed int(10) unsigned NOT NULL DEFAULT 0,
		removed int(10) unsigned NOT NULL DEFAULT 0,
		skipped int(10) unsigned NOT NULL DEFAULT 0,
		email_sent int(10) unsigned NOT NULL DEFAULT 0,
		email_failed int(10) unsigned NOT NULL DEFAULT 0,
		error_msg varchar(255) NOT NULL DEFAULT '',
		created_at datetime NOT NULL,
		updated_at datetime NOT NULL,
		PRIMARY KEY  (id),
		KEY status_idx (status),
		KEY notice_id (notice_id)
	) {$charset};";

	dbDelta( $sql );
}

/**
 * @return string
 */
function sc_portal_push_jobs_table() {
	global $wpdb;
	return $wpdb->prefix . 'sc_portal_push_jobs';
}

/**
 * @return void
 */
function sc_portal_push_schedule_cron() {
	if ( ! wp_next_scheduled( SC_PORTAL_PUSH_CRON ) ) {
		wp_schedule_event( time() + 60, 'sc_portal_push_minute', SC_PORTAL_PUSH_CRON );
	}
}

add_filter( 'cron_schedules', 'sc_portal_push_cron_schedules' );
/**
 * @param array<string,array<string,mixed>> $schedules Schedules.
 * @return array<string,array<string,mixed>>
 */
function sc_portal_push_cron_schedules( $schedules ) {
	if ( ! isset( $schedules['sc_portal_push_minute'] ) ) {
		$schedules['sc_portal_push_minute'] = array(
			'interval' => 60,
			'display'  => 'Portal push (1 min)',
		);
	}
	return $schedules;
}

add_action( SC_PORTAL_PUSH_CRON, 'sc_portal_push_cron_run' );

/**
 * Processa até 3 jobs por tick.
 *
 * @return void
 */
function sc_portal_push_cron_run() {
	for ( $i = 0; $i < 3; $i++ ) {
		$job = sc_portal_push_next_job();
		if ( ! $job ) {
			return;
		}
		sc_portal_push_process_job_tick( $job );
	}
}

/**
 * @return object|null
 */
function sc_portal_push_next_job() {
	global $wpdb;
	$t = sc_portal_push_jobs_table();
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	return $wpdb->get_row(
		"SELECT * FROM {$t} WHERE status IN ('pending','running') ORDER BY id ASC LIMIT 1"
	);
}

/**
 * Cursos com push silenciado (admin).
 *
 * @return int[]
 */
function sc_portal_push_muted_course_ids() {
	$ids = get_option( SC_PORTAL_PUSH_MUTED_OPT, array() );
	if ( ! is_array( $ids ) ) {
		return array();
	}
	return array_values( array_filter( array_map( 'intval', $ids ) ) );
}

/**
 * @param string $audience Audience.
 * @return bool
 */
function sc_portal_push_audience_muted( $audience ) {
	if ( ! preg_match( '/^course:(\d+)$/', (string) $audience, $m ) ) {
		return false;
	}
	return in_array( (int) $m[1], sc_portal_push_muted_course_ids(), true );
}

/**
 * Enfileira envio (substitui envio síncrono da Fase B).
 *
 * @param int  $notice_id  Notice ID.
 * @param bool $send_push  Queue push.
 * @param bool $send_email Queue e-mail.
 * @return array{job_id?:int,queued?:bool,error?:string,sent?:int,failed?:int,removed?:int,skipped?:int}
 */
function sc_portal_push_enqueue_notice( $notice_id, $send_push = true, $send_email = false ) {
	$notice_id  = (int) $notice_id;
	$send_push  = (bool) $send_push;
	$send_email = (bool) $send_email;

	if ( ! $send_push && ! $send_email ) {
		return array( 'error' => 'noop' );
	}

	global $wpdb;
	$n = sc_portal_notices_table();
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$notice = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$n} WHERE id = %d", $notice_id ) );
	if ( ! $notice || 'published' !== $notice->status ) {
		return array( 'error' => 'notice' );
	}

	if ( sc_portal_push_audience_muted( $notice->audience ) ) {
		return array( 'error' => 'muted' );
	}

	if ( $send_push && ! sc_portal_push_ready() ) {
		if ( ! $send_email ) {
			return array( 'error' => 'unavailable' );
		}
		$send_push = false;
	}

	$now = current_time( 'mysql', true );
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
	$wpdb->insert(
		sc_portal_push_jobs_table(),
		array(
			'notice_id'            => $notice_id,
			'status'               => 'pending',
			'send_push'            => $send_push ? 1 : 0,
			'send_email'           => $send_email ? 1 : 0,
			'cursor_sub_id'        => 0,
			'cursor_email_user_id' => 0,
			'push_done'            => $send_push ? 0 : 1,
			'email_done'           => $send_email ? 0 : 1,
			'sent'                 => 0,
			'failed'               => 0,
			'removed'              => 0,
			'skipped'              => 0,
			'email_sent'           => 0,
			'email_failed'         => 0,
			'error_msg'            => '',
			'created_at'           => $now,
			'updated_at'           => $now,
		),
		array( '%d', '%s', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%s', '%s', '%s' )
	);

	$job_id = (int) $wpdb->insert_id;
	sc_portal_push_schedule_cron();

	// 1.º lote já (UX); resto no cron.
	$job = sc_portal_push_get_job( $job_id );
	if ( $job ) {
		sc_portal_push_process_job_tick( $job );
		$job = sc_portal_push_get_job( $job_id );
	}

	return array(
		'queued'  => true,
		'job_id'  => $job_id,
		'sent'    => $job ? (int) $job->sent : 0,
		'failed'  => $job ? (int) $job->failed : 0,
		'removed' => $job ? (int) $job->removed : 0,
		'skipped' => $job ? (int) $job->skipped : 0,
		'status'  => $job ? (string) $job->status : 'pending',
		'email_sent'   => $job ? (int) $job->email_sent : 0,
		'email_failed' => $job ? (int) $job->email_failed : 0,
	);
}

/**
 * @param int $job_id Job ID.
 * @return object|null
 */
function sc_portal_push_get_job( $job_id ) {
	global $wpdb;
	$t = sc_portal_push_jobs_table();
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$t} WHERE id = %d", (int) $job_id ) );
}

/**
 * Último job de um aviso (para relatório na lista).
 *
 * @param int $notice_id Notice ID.
 * @return object|null
 */
function sc_portal_push_latest_job_for_notice( $notice_id ) {
	global $wpdb;
	$t = sc_portal_push_jobs_table();
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	return $wpdb->get_row(
		$wpdb->prepare(
			"SELECT * FROM {$t} WHERE notice_id = %d ORDER BY id DESC LIMIT 1",
			(int) $notice_id
		)
	);
}

/**
 * @param object $job Job row.
 * @return void
 */
function sc_portal_push_process_job_tick( $job ) {
	if ( ! $job || empty( $job->id ) ) {
		return;
	}

	global $wpdb;
	$t   = sc_portal_push_jobs_table();
	$now = current_time( 'mysql', true );

	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$wpdb->update(
		$t,
		array(
			'status'     => 'running',
			'updated_at' => $now,
		),
		array( 'id' => (int) $job->id ),
		array( '%s', '%s' ),
		array( '%d' )
	);

	$job = sc_portal_push_get_job( (int) $job->id );
	if ( ! $job ) {
		return;
	}

	$n = sc_portal_notices_table();
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$notice = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$n} WHERE id = %d", (int) $job->notice_id ) );
	if ( ! $notice ) {
		sc_portal_push_finish_job( (int) $job->id, 'error', __( 'Aviso não encontrado.', 'saulocoelho' ) );
		return;
	}

	if ( empty( $job->push_done ) && ! empty( $job->send_push ) ) {
		sc_portal_push_job_batch_push( $job, $notice );
		$job = sc_portal_push_get_job( (int) $job->id );
	}

	if ( $job && empty( $job->email_done ) && ! empty( $job->send_email ) ) {
		sc_portal_push_job_batch_email( $job, $notice );
		$job = sc_portal_push_get_job( (int) $job->id );
	}

	if ( ! $job ) {
		return;
	}

	if ( ! empty( $job->push_done ) && ! empty( $job->email_done ) ) {
		sc_portal_push_finish_job( (int) $job->id, 'done', '' );
	}
}

/**
 * @param int    $job_id Job ID.
 * @param string $status done|error.
 * @param string $error  Message.
 * @return void
 */
function sc_portal_push_finish_job( $job_id, $status, $error = '' ) {
	global $wpdb;
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$wpdb->update(
		sc_portal_push_jobs_table(),
		array(
			'status'     => $status,
			'error_msg'  => substr( (string) $error, 0, 255 ),
			'updated_at' => current_time( 'mysql', true ),
		),
		array( 'id' => (int) $job_id ),
		array( '%s', '%s', '%s' ),
		array( '%d' )
	);
}

/**
 * @param object $job    Job.
 * @param object $notice Notice.
 * @return void
 */
function sc_portal_push_job_batch_push( $job, $notice ) {
	if ( ! sc_portal_push_ready() ) {
		sc_portal_push_mark_push_done( (int) $job->id );
		return;
	}

	global $wpdb;
	$subs_t  = sc_portal_push_subs_table();
	$cursor  = (int) $job->cursor_sub_id;
	$limit   = (int) SC_PORTAL_PUSH_BATCH;
	$audience = (string) $notice->audience;

	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$rows = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT * FROM {$subs_t} WHERE id > %d ORDER BY id ASC LIMIT %d",
			$cursor,
			$limit
		)
	);

	if ( empty( $rows ) ) {
		sc_portal_push_mark_push_done( (int) $job->id );
		return;
	}

	$payload = wp_json_encode(
		array(
			'title' => (string) $notice->title,
			'body'  => wp_strip_all_tags( (string) $notice->body ),
			'url'   => ! empty( $notice->link_url )
				? (string) $notice->link_url
				: ( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : home_url( '/minha-conta/' ) ),
			'tag'   => 'sc-notice-' . (int) $notice->id,
		)
	);

	$keys = get_option( SC_PORTAL_VAPID_OPT );
	sc_portal_push_autoload();

	$sent = $failed = $removed = $skipped = 0;
	$last_id = $cursor;

	try {
		$webPush = new \Minishlink\WebPush\WebPush(
			array(
				'VAPID' => array(
					'subject'    => 'mailto:contato@saulocoelho.com',
					'publicKey'  => $keys['publicKey'],
					'privateKey' => $keys['privateKey'],
				),
			)
		);
		$webPush->setReuseVAPIDHeaders( true );
	} catch ( Exception $e ) {
		sc_portal_push_finish_job( (int) $job->id, 'error', 'init' );
		return;
	}

	$map = array();
	foreach ( $rows as $row ) {
		$last_id = (int) $row->id;
		if ( ! function_exists( 'sc_portal_notice_user_matches' ) || ! sc_portal_notice_user_matches( (int) $row->user_id, $audience ) ) {
			$skipped++;
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
			$map[ $row->endpoint ] = (int) $row->id;
		} catch ( Exception $e ) {
			$failed++;
		}
	}

	foreach ( $webPush->flush() as $report ) {
		$endpoint = $report->getEndpoint();
		$sub_id   = isset( $map[ $endpoint ] ) ? $map[ $endpoint ] : 0;
		if ( $report->isSuccess() ) {
			$sent++;
			continue;
		}
		$failed++;
		$code = $report->getResponse() ? $report->getResponse()->getStatusCode() : 0;
		if ( in_array( (int) $code, array( 404, 410 ), true ) && $sub_id > 0 ) {
			sc_portal_push_delete_sub( $sub_id );
			$removed++;
		}
	}

	$jobs = sc_portal_push_jobs_table();
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$wpdb->query(
		$wpdb->prepare(
			"UPDATE {$jobs} SET
				cursor_sub_id = %d,
				sent = sent + %d,
				failed = failed + %d,
				removed = removed + %d,
				skipped = skipped + %d,
				updated_at = %s
			WHERE id = %d",
			$last_id,
			$sent,
			$failed,
			$removed,
			$skipped,
			current_time( 'mysql', true ),
			(int) $job->id
		)
	);

	if ( count( $rows ) < $limit ) {
		sc_portal_push_mark_push_done( (int) $job->id );
	}
}

/**
 * @param int $job_id Job ID.
 * @return void
 */
function sc_portal_push_mark_push_done( $job_id ) {
	global $wpdb;
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$wpdb->update(
		sc_portal_push_jobs_table(),
		array(
			'push_done'  => 1,
			'updated_at' => current_time( 'mysql', true ),
		),
		array( 'id' => (int) $job_id ),
		array( '%d', '%s' ),
		array( '%d' )
	);
}

/**
 * @param int $job_id Job ID.
 * @return void
 */
function sc_portal_push_mark_email_done( $job_id ) {
	global $wpdb;
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$wpdb->update(
		sc_portal_push_jobs_table(),
		array(
			'email_done' => 1,
			'updated_at' => current_time( 'mysql', true ),
		),
		array( 'id' => (int) $job_id ),
		array( '%d', '%s' ),
		array( '%d' )
	);
}

/**
 * E-mail em lotes para utilizadores da audiência (não só subs push).
 *
 * @param object $job    Job.
 * @param object $notice Notice.
 * @return void
 */
function sc_portal_push_job_batch_email( $job, $notice ) {
	$user_ids = sc_portal_push_audience_user_ids( (string) $notice->audience, (int) $job->cursor_email_user_id, (int) SC_PORTAL_PUSH_BATCH );
	if ( empty( $user_ids ) ) {
		sc_portal_push_mark_email_done( (int) $job->id );
		return;
	}

	$subject = wp_strip_all_tags( (string) $notice->title );
	$link    = ! empty( $notice->link_url )
		? (string) $notice->link_url
		: ( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : home_url( '/minha-conta/' ) );
	$body_txt = wp_strip_all_tags( (string) $notice->body );
	$message  = $body_txt . "\n\n" . $link . "\n\n— Portal do Aluno · " . wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );

	$sent = $failed = 0;
	$last = (int) $job->cursor_email_user_id;

	foreach ( $user_ids as $uid ) {
		$last  = (int) $uid;
		$user  = get_userdata( $uid );
		$email = $user && is_email( $user->user_email ) ? $user->user_email : '';
		if ( ! $email ) {
			$failed++;
			continue;
		}
		$ok = wp_mail( $email, $subject, $message );
		if ( $ok ) {
			$sent++;
		} else {
			$failed++;
		}
	}

	global $wpdb;
	$jobs = sc_portal_push_jobs_table();
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$wpdb->query(
		$wpdb->prepare(
			"UPDATE {$jobs} SET
				cursor_email_user_id = %d,
				email_sent = email_sent + %d,
				email_failed = email_failed + %d,
				updated_at = %s
			WHERE id = %d",
			$last,
			$sent,
			$failed,
			current_time( 'mysql', true ),
			(int) $job->id
		)
	);

	if ( count( $user_ids ) < (int) SC_PORTAL_PUSH_BATCH ) {
		sc_portal_push_mark_email_done( (int) $job->id );
	}
}

/**
 * IDs de utilizadores da audiência com id > $after, ordenados.
 *
 * @param string $audience Audience.
 * @param int    $after    Cursor user ID.
 * @param int    $limit    Limit.
 * @return int[]
 */
function sc_portal_push_audience_user_ids( $audience, $after = 0, $limit = 30 ) {
	$after = (int) $after;
	$limit = max( 1, (int) $limit );
	$audience = (string) $audience;

	if ( preg_match( '/^user:(\d+)$/', $audience, $m ) ) {
		$uid = (int) $m[1];
		return ( $uid > $after ) ? array( $uid ) : array();
	}

	if ( preg_match( '/^course:(\d+)$/', $audience, $m ) ) {
		return sc_portal_push_course_user_ids( (int) $m[1], $after, $limit );
	}

	// all: páginas de utilizadores do site.
	global $wpdb;
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$ids = $wpdb->get_col(
		$wpdb->prepare(
			"SELECT ID FROM {$wpdb->users} WHERE ID > %d ORDER BY ID ASC LIMIT %d",
			$after,
			$limit
		)
	);
	return array_map( 'intval', (array) $ids );
}

/**
 * @param int $course_id Course ID.
 * @param int $after     Cursor.
 * @param int $limit     Limit.
 * @return int[]
 */
function sc_portal_push_course_user_ids( $course_id, $after, $limit ) {
	global $wpdb;
	$course_id = (int) $course_id;
	if ( $course_id < 1 ) {
		return array();
	}

	$table = $wpdb->prefix . 'lms_enrollments';
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) );
	if ( $exists !== $table ) {
		return array();
	}

	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$ids = $wpdb->get_col(
		$wpdb->prepare(
			"SELECT DISTINCT user_id FROM {$table}
			WHERE course_id = %d AND status IN ('active','completed') AND user_id > %d
			ORDER BY user_id ASC LIMIT %d",
			$course_id,
			(int) $after,
			(int) $limit
		)
	);

	return array_map( 'intval', (array) $ids );
}

/**
 * Texto curto do relatório para a lista admin.
 *
 * @param object|null $job Job.
 * @return string
 */
function sc_portal_push_job_report_label( $job ) {
	if ( ! $job ) {
		return '—';
	}
	$status = (string) $job->status;
	$map    = array(
		'pending' => __( 'Na fila', 'saulocoelho' ),
		'running' => __( 'A enviar…', 'saulocoelho' ),
		'done'    => __( 'Concluído', 'saulocoelho' ),
		'error'   => __( 'Erro', 'saulocoelho' ),
	);
	$label = isset( $map[ $status ] ) ? $map[ $status ] : $status;
	$parts = array( $label );
	$parts[] = sprintf(
		/* translators: 1: push sent 2: push failed */
		__( 'push %1$d/%2$d', 'saulocoelho' ),
		(int) $job->sent,
		(int) $job->failed
	);
	if ( ! empty( $job->send_email ) ) {
		$parts[] = sprintf(
			/* translators: 1: email sent 2: email failed */
			__( 'e-mail %1$d/%2$d', 'saulocoelho' ),
			(int) $job->email_sent,
			(int) $job->email_failed
		);
	}
	return implode( ' · ', $parts );
}

/**
 * Substitui o handler Fase B: enfileira em vez de flush síncrono completo.
 *
 * @param int  $notice_id  Notice ID.
 * @param bool $send_push  Push.
 * @param bool $send_email E-mail.
 * @return array<string,mixed>|null
 */
function sc_portal_push_on_notice_saved_v2( $notice_id, $send_push, $send_email = false ) {
	if ( ! $send_push && ! $send_email ) {
		return null;
	}
	return sc_portal_push_enqueue_notice( (int) $notice_id, (bool) $send_push, (bool) $send_email );
}
