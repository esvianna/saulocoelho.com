<?php
/**
 * Portal do Aluno — avisos (inbox + sininho). Fase A · issue #16 · ADR-013.
 *
 * Tabelas {prefix}sc_portal_notices / sc_portal_notice_reads.
 * REST saulocoelho/v1. Web Push = Fase B em module-portal-push.php.
 *
 * @package Saulocoelho
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SC_PORTAL_NOTICES_DB', 1 );
define( 'SC_PORTAL_NOTICES_OPT', 'saulocoelho_portal_notices_db' );

/**
 * Upgrade / create tables on init when theme version gate changes.
 */
add_action( 'after_setup_theme', 'sc_portal_notices_maybe_upgrade', 5 );
function sc_portal_notices_maybe_upgrade() {
	$installed = (int) get_option( SC_PORTAL_NOTICES_OPT, 0 );
	if ( $installed >= SC_PORTAL_NOTICES_DB ) {
		return;
	}
	sc_portal_notices_install_tables();
	update_option( SC_PORTAL_NOTICES_OPT, SC_PORTAL_NOTICES_DB, true );
}

/**
 * @return void
 */
function sc_portal_notices_install_tables() {
	global $wpdb;
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	$charset = $wpdb->get_charset_collate();
	$n       = $wpdb->prefix . 'sc_portal_notices';
	$r       = $wpdb->prefix . 'sc_portal_notice_reads';

	$sql_n = "CREATE TABLE {$n} (
		id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		title varchar(200) NOT NULL,
		body text NOT NULL,
		link_url varchar(500) NOT NULL DEFAULT '',
		audience varchar(64) NOT NULL DEFAULT 'all',
		status varchar(20) NOT NULL DEFAULT 'draft',
		author_id bigint(20) unsigned NOT NULL DEFAULT 0,
		created_at datetime NOT NULL,
		published_at datetime DEFAULT NULL,
		PRIMARY KEY  (id),
		KEY status_pub (status, published_at)
	) {$charset};";

	$sql_r = "CREATE TABLE {$r} (
		notice_id bigint(20) unsigned NOT NULL,
		user_id bigint(20) unsigned NOT NULL,
		read_at datetime NOT NULL,
		PRIMARY KEY  (notice_id, user_id),
		KEY user_id (user_id)
	) {$charset};";

	dbDelta( $sql_n );
	dbDelta( $sql_r );
}

/**
 * @return string
 */
function sc_portal_notices_table() {
	global $wpdb;
	return $wpdb->prefix . 'sc_portal_notices';
}

/**
 * @return string
 */
function sc_portal_notices_reads_table() {
	global $wpdb;
	return $wpdb->prefix . 'sc_portal_notice_reads';
}

/**
 * Cria um aviso Portal (API reutilizável — admin e #22 lesson notify).
 *
 * @param array $args {
 *     @type string $title      Título (obrigatório).
 *     @type string $body       Corpo.
 *     @type string $link_url   URL do CTA.
 *     @type string $audience   all | course:{id} | user:{id}.
 *     @type string $status     draft|published (omissão published).
 *     @type int    $author_id  Autor (omissão current user).
 * }
 * @return int|\WP_Error Notice ID.
 */
function sc_portal_notice_create( array $args ) {
	global $wpdb;

	$title = isset( $args['title'] ) ? sanitize_text_field( (string) $args['title'] ) : '';
	if ( '' === $title ) {
		return new \WP_Error( 'title', __( 'Título obrigatório.', 'saulocoelho' ) );
	}

	$body     = isset( $args['body'] ) ? sanitize_textarea_field( (string) $args['body'] ) : '';
	$link_url = isset( $args['link_url'] ) ? esc_url_raw( (string) $args['link_url'] ) : '';
	$audience = isset( $args['audience'] ) ? sanitize_text_field( (string) $args['audience'] ) : 'all';
	if ( ! preg_match( '/^(all|course:\d+|user:\d+)$/', $audience ) ) {
		$audience = 'all';
	}
	$status    = ( isset( $args['status'] ) && 'draft' === $args['status'] ) ? 'draft' : 'published';
	$author_id = isset( $args['author_id'] ) ? (int) $args['author_id'] : get_current_user_id();
	$now       = current_time( 'mysql', true );
	$table     = sc_portal_notices_table();

	$data = array(
		'title'        => $title,
		'body'         => $body,
		'link_url'     => $link_url,
		'audience'     => $audience,
		'status'       => $status,
		'author_id'    => $author_id,
		'created_at'   => $now,
		'published_at' => ( 'published' === $status ) ? $now : null,
	);

	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
	$ok = $wpdb->insert(
		$table,
		$data,
		array( '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s' )
	);
	if ( ! $ok ) {
		return new \WP_Error( 'db', __( 'Não foi possível criar o aviso.', 'saulocoelho' ) );
	}

	return (int) $wpdb->insert_id;
}

/**
 * Utilizador tem acesso ao aviso segundo audience.
 *
 * @param int    $user_id  User ID.
 * @param string $audience all | course:{id} | user:{id}.
 * @return bool
 */
function sc_portal_notice_user_matches( $user_id, $audience ) {
	$user_id  = (int) $user_id;
	$audience = (string) $audience;
	if ( $user_id < 1 ) {
		return false;
	}
	if ( 'all' === $audience || '' === $audience ) {
		return true;
	}
	if ( preg_match( '/^user:(\d+)$/', $audience, $m ) ) {
		return (int) $m[1] === $user_id;
	}
	if ( preg_match( '/^course:(\d+)$/', $audience, $m ) ) {
		return sc_portal_user_enrolled_in_course( $user_id, (int) $m[1] );
	}
	return false;
}

/**
 * Matrícula AmaEducacional (tabela lms_enrollments) ou filtro.
 *
 * @param int $user_id   User ID.
 * @param int $course_id Post ID ama_course.
 * @return bool
 */
function sc_portal_user_enrolled_in_course( $user_id, $course_id ) {
	$user_id   = (int) $user_id;
	$course_id = (int) $course_id;
	$filtered  = apply_filters( 'saulocoelho_portal_user_in_course', null, $user_id, $course_id );
	if ( is_bool( $filtered ) ) {
		return $filtered;
	}
	if ( $user_id < 1 || $course_id < 1 ) {
		return false;
	}
	global $wpdb;
	$table = $wpdb->prefix . 'lms_enrollments';
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) );
	if ( $exists !== $table ) {
		return false;
	}
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$row = $wpdb->get_var(
		$wpdb->prepare(
			"SELECT id FROM {$table} WHERE user_id = %d AND course_id = %d LIMIT 1",
			$user_id,
			$course_id
		)
	);
	return (bool) $row;
}

/**
 * Lista avisos publicados visíveis para o utilizador.
 *
 * @param int $user_id User ID.
 * @param int $limit   Max rows.
 * @return array<int,object>
 */
function sc_portal_notices_for_user( $user_id, $limit = 30 ) {
	global $wpdb;
	$user_id = (int) $user_id;
	$limit   = max( 1, min( 50, (int) $limit ) );
	$n       = sc_portal_notices_table();
	$r       = sc_portal_notices_reads_table();

	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$rows = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT n.id, n.title, n.body, n.link_url, n.audience, n.published_at,
				r.read_at IS NOT NULL AS is_read
			FROM {$n} n
			LEFT JOIN {$r} r ON r.notice_id = n.id AND r.user_id = %d
			WHERE n.status = 'published'
			ORDER BY n.published_at DESC
			LIMIT %d",
			$user_id,
			$limit * 3
		)
	);
	if ( ! is_array( $rows ) ) {
		return array();
	}
	$out = array();
	foreach ( $rows as $row ) {
		if ( ! sc_portal_notice_user_matches( $user_id, $row->audience ) ) {
			continue;
		}
		$out[] = $row;
		if ( count( $out ) >= $limit ) {
			break;
		}
	}
	return $out;
}

/**
 * @param int $user_id User ID.
 * @return int
 */
function sc_portal_notices_unread_count( $user_id ) {
	$items = sc_portal_notices_for_user( $user_id, 50 );
	$n     = 0;
	foreach ( $items as $row ) {
		if ( empty( $row->is_read ) ) {
			++$n;
		}
	}
	return $n;
}

/**
 * Marca aviso como lido (só se o user tiver acesso).
 *
 * @param int $notice_id Notice ID.
 * @param int $user_id   User ID.
 * @return bool|WP_Error
 */
function sc_portal_notice_mark_read( $notice_id, $user_id ) {
	global $wpdb;
	$notice_id = (int) $notice_id;
	$user_id   = (int) $user_id;
	$n         = sc_portal_notices_table();

	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$row = $wpdb->get_row(
		$wpdb->prepare(
			"SELECT id, audience, status FROM {$n} WHERE id = %d",
			$notice_id
		)
	);
	if ( ! $row || 'published' !== $row->status ) {
		return new WP_Error( 'not_found', __( 'Aviso não encontrado.', 'saulocoelho' ), array( 'status' => 404 ) );
	}
	if ( ! sc_portal_notice_user_matches( $user_id, $row->audience ) ) {
		return new WP_Error( 'forbidden', __( 'Sem acesso a este aviso.', 'saulocoelho' ), array( 'status' => 403 ) );
	}

	$r = sc_portal_notices_reads_table();
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$wpdb->replace(
		$r,
		array(
			'notice_id' => $notice_id,
			'user_id'   => $user_id,
			'read_at'   => current_time( 'mysql', true ),
		),
		array( '%d', '%d', '%s' )
	);
	return true;
}

/* -------------------------------------------------------------------------- */
/* REST                                                                       */
/* -------------------------------------------------------------------------- */

add_action( 'rest_api_init', 'sc_portal_notices_register_rest' );
function sc_portal_notices_register_rest() {
	register_rest_route(
		'saulocoelho/v1',
		'/notices',
		array(
			'methods'             => 'GET',
			'callback'            => 'sc_portal_notices_rest_list',
			'permission_callback' => function () {
				return is_user_logged_in();
			},
		)
	);
	register_rest_route(
		'saulocoelho/v1',
		'/notices/unread-count',
		array(
			'methods'             => 'GET',
			'callback'            => 'sc_portal_notices_rest_unread',
			'permission_callback' => function () {
				return is_user_logged_in();
			},
		)
	);
	register_rest_route(
		'saulocoelho/v1',
		'/notices/(?P<id>\d+)/read',
		array(
			'methods'             => 'POST',
			'callback'            => 'sc_portal_notices_rest_read',
			'permission_callback' => function () {
				return is_user_logged_in();
			},
			'args'                => array(
				'id' => array(
					'type'              => 'integer',
					'required'          => true,
					'sanitize_callback' => 'absint',
				),
			),
		)
	);
}

/**
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response
 */
function sc_portal_notices_rest_list( $request ) {
	$user_id = get_current_user_id();
	$rows    = sc_portal_notices_for_user( $user_id, 30 );
	$items   = array();
	foreach ( $rows as $row ) {
		$items[] = array(
			'id'           => (int) $row->id,
			'title'        => $row->title,
			'body'         => $row->body,
			'url'          => $row->link_url,
			'published_at' => $row->published_at,
			'is_read'      => (bool) $row->is_read,
		);
	}
	return rest_ensure_response(
		array(
			'items'        => $items,
			'unread_count' => sc_portal_notices_unread_count( $user_id ),
		)
	);
}

/**
 * @return WP_REST_Response
 */
function sc_portal_notices_rest_unread() {
	return rest_ensure_response(
		array(
			'unread_count' => sc_portal_notices_unread_count( get_current_user_id() ),
		)
	);
}

/**
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response|WP_Error
 */
function sc_portal_notices_rest_read( $request ) {
	$result = sc_portal_notice_mark_read( (int) $request['id'], get_current_user_id() );
	if ( is_wp_error( $result ) ) {
		return $result;
	}
	return rest_ensure_response(
		array(
			'ok'           => true,
			'unread_count' => sc_portal_notices_unread_count( get_current_user_id() ),
		)
	);
}

/* -------------------------------------------------------------------------- */
/* Admin                                                                      */
/* -------------------------------------------------------------------------- */

add_action( 'admin_menu', 'sc_portal_notices_admin_menu' );
function sc_portal_notices_admin_menu() {
	add_menu_page(
		__( 'Avisos do Portal', 'saulocoelho' ),
		__( 'Avisos Portal', 'saulocoelho' ),
		'manage_options',
		'sc-portal-notices',
		'sc_portal_notices_admin_page',
		'dashicons-bell',
		58
	);
}

/**
 * Cursos Ama para o select de audience.
 *
 * @return array<int,string> id => title
 */
function sc_portal_notices_course_choices() {
	$posts = get_posts(
		array(
			'post_type'      => 'ama_course',
			'post_status'    => array( 'publish', 'private', 'draft' ),
			'posts_per_page' => 100,
			'orderby'        => 'title',
			'order'          => 'ASC',
		)
	);
	$out = array();
	foreach ( $posts as $p ) {
		$out[ (int) $p->ID ] = $p->post_title;
	}
	return $out;
}

/**
 * Admin CRUD simples (lista + formulário).
 */
function sc_portal_notices_admin_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	global $wpdb;
	$table = sc_portal_notices_table();
	$msg   = '';

	// Delete.
	if ( isset( $_GET['sc_notice_delete'], $_GET['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'sc_notice_delete_' . (int) $_GET['sc_notice_delete'] ) ) {
		$id = (int) $_GET['sc_notice_delete'];
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->delete( $table, array( 'id' => $id ), array( '%d' ) );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->delete( sc_portal_notices_reads_table(), array( 'notice_id' => $id ), array( '%d' ) );
		$msg = __( 'Aviso excluído.', 'saulocoelho' );
	}

	// Save.
	if ( isset( $_POST['sc_notice_save'] ) && check_admin_referer( 'sc_portal_notice_save' ) ) {
		$id        = isset( $_POST['notice_id'] ) ? (int) $_POST['notice_id'] : 0;
		$title     = isset( $_POST['title'] ) ? sanitize_text_field( wp_unslash( $_POST['title'] ) ) : '';
		$body      = isset( $_POST['body'] ) ? sanitize_textarea_field( wp_unslash( $_POST['body'] ) ) : '';
		$link_url  = isset( $_POST['link_url'] ) ? esc_url_raw( wp_unslash( $_POST['link_url'] ) ) : '';
		$status    = isset( $_POST['status'] ) && 'published' === $_POST['status'] ? 'published' : 'draft';
		$send_push  = ! empty( $_POST['send_push'] );
		$send_email = ! empty( $_POST['send_email'] );
		$aud_type  = isset( $_POST['audience_type'] ) ? sanitize_key( wp_unslash( $_POST['audience_type'] ) ) : 'all';
		$audience  = 'all';
		if ( 'course' === $aud_type ) {
			$cid = isset( $_POST['course_id'] ) ? (int) $_POST['course_id'] : 0;
			$audience = $cid > 0 ? 'course:' . $cid : 'all';
		} elseif ( 'user' === $aud_type ) {
			$uid = isset( $_POST['user_id'] ) ? (int) $_POST['user_id'] : 0;
			$audience = $uid > 0 ? 'user:' . $uid : 'all';
		}

		if ( '' === $title ) {
			$msg = __( 'Título obrigatório.', 'saulocoelho' );
		} else {
			$now = current_time( 'mysql', true );
			$data = array(
				'title'     => $title,
				'body'      => $body,
				'link_url'  => $link_url,
				'audience'  => $audience,
				'status'    => $status,
				'author_id' => get_current_user_id(),
			);
			$fmt = array( '%s', '%s', '%s', '%s', '%s', '%d' );
			$saved_id = $id;

			if ( $id > 0 ) {
				if ( 'published' === $status ) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
					$cur = $wpdb->get_var( $wpdb->prepare( "SELECT published_at FROM {$table} WHERE id = %d", $id ) );
					if ( empty( $cur ) ) {
						$data['published_at'] = $now;
						$fmt[]                = '%s';
					}
				}
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
				$wpdb->update( $table, $data, array( 'id' => $id ), $fmt, array( '%d' ) );
				$msg = __( 'Aviso atualizado.', 'saulocoelho' );
			} else {
				$data['created_at']   = $now;
				$data['published_at'] = ( 'published' === $status ) ? $now : null;
				$fmt[]                = '%s';
				$fmt[]                = '%s';
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
				$wpdb->insert( $table, $data, $fmt );
				$saved_id = (int) $wpdb->insert_id;
				$msg      = __( 'Aviso criado.', 'saulocoelho' );
			}

			if ( ( $send_push || $send_email ) && 'published' === $status && $saved_id > 0 && function_exists( 'sc_portal_push_on_notice_saved' ) ) {
				$push = sc_portal_push_on_notice_saved( $saved_id, $send_push, $send_email );
				if ( is_array( $push ) ) {
					if ( ! empty( $push['error'] ) ) {
						if ( 'muted' === $push['error'] ) {
							$msg .= ' ' . __( 'Envio bloqueado: este curso está silenciado para push/e-mail.', 'saulocoelho' );
						} elseif ( 'unavailable' === $push['error'] ) {
							$msg .= ' ' . __( 'Push não enviado (serviço indisponível ou sem chaves VAPID).', 'saulocoelho' );
						} else {
							$msg .= ' ' . __( 'Não foi possível enfileirar o envio.', 'saulocoelho' );
						}
					} elseif ( ! empty( $push['queued'] ) ) {
						$msg .= ' ' . sprintf(
							/* translators: 1: job status 2: push sent 3: push failed 4: email sent */
							__( 'Fila #%1$d (%2$s): push %3$d ok / %4$d falhas; e-mail %5$d.', 'saulocoelho' ),
							(int) ( $push['job_id'] ?? 0 ),
							isset( $push['status'] ) ? (string) $push['status'] : 'pending',
							(int) ( $push['sent'] ?? 0 ),
							(int) ( $push['failed'] ?? 0 ),
							(int) ( $push['email_sent'] ?? 0 )
						);
						if ( isset( $push['status'] ) && 'done' !== $push['status'] ) {
							$msg .= ' ' . __( 'O restante continua em segundo plano (cron).', 'saulocoelho' );
						}
					}
				}
			} elseif ( ( $send_push || $send_email ) && 'published' !== $status ) {
				$msg .= ' ' . __( '(Envio ignorado: o aviso não está publicado.)', 'saulocoelho' );
			}
		}
	}

	// Silenciar cursos (Fase C).
	if ( isset( $_POST['sc_push_mute_save'] ) && check_admin_referer( 'sc_portal_push_mute' ) && function_exists( 'sc_portal_push_muted_course_ids' ) ) {
		$raw = isset( $_POST['muted_courses'] ) && is_array( $_POST['muted_courses'] ) ? wp_unslash( $_POST['muted_courses'] ) : array();
		$ids = array_values( array_unique( array_filter( array_map( 'intval', $raw ) ) ) );
		update_option( SC_PORTAL_PUSH_MUTED_OPT, $ids, false );
		$msg = __( 'Cursos silenciados atualizados.', 'saulocoelho' );
	}

	$edit_id = isset( $_GET['edit'] ) ? (int) $_GET['edit'] : 0;
	$edit    = null;
	if ( $edit_id > 0 ) {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$edit = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d", $edit_id ) );
	}

	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$list = $wpdb->get_results( "SELECT * FROM {$table} ORDER BY id DESC LIMIT 50" );
	$courses = sc_portal_notices_course_choices();

	$aud_type = 'all';
	$course_id = 0;
	$user_id   = 0;
	if ( $edit ) {
		if ( preg_match( '/^course:(\d+)$/', $edit->audience, $m ) ) {
			$aud_type  = 'course';
			$course_id = (int) $m[1];
		} elseif ( preg_match( '/^user:(\d+)$/', $edit->audience, $m ) ) {
			$aud_type = 'user';
			$user_id  = (int) $m[1];
		}
	}
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Avisos do Portal', 'saulocoelho' ); ?></h1>
		<?php if ( $msg ) : ?>
			<div class="notice notice-success is-dismissible"><p><?php echo esc_html( $msg ); ?></p></div>
		<?php endif; ?>

		<div style="display:grid;grid-template-columns:1fr 1fr;gap:2rem;align-items:start;max-width:1100px;">
			<form method="post" style="background:#fff;padding:1.25rem;border:1px solid #c3c4c7;box-shadow:0 1px 1px rgba(0,0,0,.04);">
				<?php wp_nonce_field( 'sc_portal_notice_save' ); ?>
				<input type="hidden" name="notice_id" value="<?php echo $edit ? (int) $edit->id : 0; ?>" />
				<h2><?php echo $edit ? esc_html__( 'Editar aviso', 'saulocoelho' ) : esc_html__( 'Novo aviso', 'saulocoelho' ); ?></h2>
				<p>
					<label for="sc-n-title"><strong><?php esc_html_e( 'Título', 'saulocoelho' ); ?></strong></label><br />
					<input class="widefat" id="sc-n-title" name="title" type="text" maxlength="200" required value="<?php echo $edit ? esc_attr( $edit->title ) : ''; ?>" />
				</p>
				<p>
					<label for="sc-n-body"><strong><?php esc_html_e( 'Corpo (curto)', 'saulocoelho' ); ?></strong></label><br />
					<textarea class="widefat" id="sc-n-body" name="body" rows="4"><?php echo $edit ? esc_textarea( $edit->body ) : ''; ?></textarea>
				</p>
				<p>
					<label for="sc-n-url"><strong><?php esc_html_e( 'URL (opcional)', 'saulocoelho' ); ?></strong></label><br />
					<input class="widefat" id="sc-n-url" name="link_url" type="url" value="<?php echo $edit ? esc_attr( $edit->link_url ) : ''; ?>" placeholder="https://saulocoelho.com/curso/..." />
				</p>
				<p>
					<label for="sc-n-aud"><strong><?php esc_html_e( 'Audiência', 'saulocoelho' ); ?></strong></label><br />
					<select id="sc-n-aud" name="audience_type">
						<option value="all" <?php selected( $aud_type, 'all' ); ?>><?php esc_html_e( 'Todos os alunos do Portal', 'saulocoelho' ); ?></option>
						<option value="course" <?php selected( $aud_type, 'course' ); ?>><?php esc_html_e( 'Matriculados em um curso', 'saulocoelho' ); ?></option>
						<option value="user" <?php selected( $aud_type, 'user' ); ?>><?php esc_html_e( 'Um usuário (ID)', 'saulocoelho' ); ?></option>
					</select>
				</p>
				<p>
					<label for="sc-n-course"><strong><?php esc_html_e( 'Curso (se aplicável)', 'saulocoelho' ); ?></strong></label><br />
					<select id="sc-n-course" name="course_id" class="widefat">
						<option value="0"><?php esc_html_e( '— escolher —', 'saulocoelho' ); ?></option>
						<?php foreach ( $courses as $cid => $ctitle ) : ?>
							<option value="<?php echo (int) $cid; ?>" <?php selected( $course_id, $cid ); ?>><?php echo esc_html( $ctitle . ' (#' . $cid . ')' ); ?></option>
						<?php endforeach; ?>
					</select>
				</p>
				<p>
					<label for="sc-n-user"><strong><?php esc_html_e( 'User ID (se aplicável)', 'saulocoelho' ); ?></strong></label><br />
					<input id="sc-n-user" name="user_id" type="number" min="1" value="<?php echo $user_id ? (int) $user_id : ''; ?>" />
				</p>
				<p>
					<label for="sc-n-status"><strong><?php esc_html_e( 'Estado', 'saulocoelho' ); ?></strong></label><br />
					<select id="sc-n-status" name="status">
						<option value="draft" <?php selected( $edit ? $edit->status : 'draft', 'draft' ); ?>><?php esc_html_e( 'Rascunho', 'saulocoelho' ); ?></option>
						<option value="published" <?php selected( $edit ? $edit->status : '', 'published' ); ?>><?php esc_html_e( 'Publicado', 'saulocoelho' ); ?></option>
					</select>
				</p>
				<p>
					<label>
						<input type="checkbox" name="send_push" value="1" />
						<?php esc_html_e( 'Enviar push agora (fila; só quem ativou na Conta)', 'saulocoelho' ); ?>
					</label>
					<?php if ( function_exists( 'sc_portal_push_ready' ) && ! sc_portal_push_ready() ) : ?>
						<br /><em style="color:#b32d2e;"><?php esc_html_e( 'Web Push indisponível: falta vendor/autoload ou chaves VAPID.', 'saulocoelho' ); ?></em>
					<?php endif; ?>
				</p>
				<p>
					<label>
						<input type="checkbox" name="send_email" value="1" />
						<?php esc_html_e( 'Enviar também por e-mail (fila; destinatários da audiência)', 'saulocoelho' ); ?>
					</label>
				</p>
				<p>
					<button type="submit" name="sc_notice_save" class="button button-primary"><?php esc_html_e( 'Salvar', 'saulocoelho' ); ?></button>
					<?php if ( $edit ) : ?>
						<a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=sc-portal-notices' ) ); ?>"><?php esc_html_e( 'Cancelar', 'saulocoelho' ); ?></a>
					<?php endif; ?>
				</p>
			</form>

			<div>
				<h2><?php esc_html_e( 'Últimos avisos', 'saulocoelho' ); ?></h2>
				<table class="widefat striped">
					<thead>
						<tr>
							<th>ID</th>
							<th><?php esc_html_e( 'Título', 'saulocoelho' ); ?></th>
							<th><?php esc_html_e( 'Audiência', 'saulocoelho' ); ?></th>
							<th><?php esc_html_e( 'Estado', 'saulocoelho' ); ?></th>
							<th><?php esc_html_e( 'Envio', 'saulocoelho' ); ?></th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						<?php if ( empty( $list ) ) : ?>
							<tr><td colspan="6"><?php esc_html_e( 'Nenhum aviso ainda.', 'saulocoelho' ); ?></td></tr>
						<?php else : ?>
							<?php foreach ( $list as $row ) : ?>
								<?php
								$job_label = '—';
								if ( function_exists( 'sc_portal_push_latest_job_for_notice' ) && function_exists( 'sc_portal_push_job_report_label' ) ) {
									$job_label = sc_portal_push_job_report_label( sc_portal_push_latest_job_for_notice( (int) $row->id ) );
								}
								?>
								<tr>
									<td><?php echo (int) $row->id; ?></td>
									<td><?php echo esc_html( $row->title ); ?></td>
									<td><code><?php echo esc_html( $row->audience ); ?></code></td>
									<td><?php echo esc_html( $row->status ); ?></td>
									<td><small><?php echo esc_html( $job_label ); ?></small></td>
									<td>
										<a href="<?php echo esc_url( admin_url( 'admin.php?page=sc-portal-notices&edit=' . (int) $row->id ) ); ?>"><?php esc_html_e( 'Editar', 'saulocoelho' ); ?></a>
										|
										<a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=sc-portal-notices&sc_notice_delete=' . (int) $row->id ), 'sc_notice_delete_' . (int) $row->id ) ); ?>" onclick="return confirm('Excluir?');"><?php esc_html_e( 'Excluir', 'saulocoelho' ); ?></a>
									</td>
								</tr>
							<?php endforeach; ?>
						<?php endif; ?>
					</tbody>
				</table>

				<?php if ( function_exists( 'sc_portal_push_muted_course_ids' ) ) : ?>
					<?php $muted = sc_portal_push_muted_course_ids(); ?>
					<form method="post" style="margin-top:1.5rem;background:#fff;padding:1.25rem;border:1px solid #c3c4c7;box-shadow:0 1px 1px rgba(0,0,0,.04);">
						<?php wp_nonce_field( 'sc_portal_push_mute' ); ?>
						<h2><?php esc_html_e( 'Silenciar push/e-mail por curso', 'saulocoelho' ); ?></h2>
						<p class="description"><?php esc_html_e( 'Avisos com audiência course:{id} nestes cursos não entram na fila de envio (o aviso no sininho continua a aparecer).', 'saulocoelho' ); ?></p>
						<?php if ( empty( $courses ) ) : ?>
							<p><em><?php esc_html_e( 'Nenhum curso encontrado.', 'saulocoelho' ); ?></em></p>
						<?php else : ?>
							<ul style="max-height:12rem;overflow:auto;margin:0.5rem 0 1rem;padding-left:1.2rem;">
								<?php foreach ( $courses as $cid => $ctitle ) : ?>
									<li>
										<label>
											<input type="checkbox" name="muted_courses[]" value="<?php echo (int) $cid; ?>" <?php checked( in_array( (int) $cid, $muted, true ) ); ?> />
											<?php echo esc_html( $ctitle . ' (#' . $cid . ')' ); ?>
										</label>
									</li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
						<button type="submit" name="sc_push_mute_save" class="button"><?php esc_html_e( 'Guardar silêncios', 'saulocoelho' ); ?></button>
					</form>
				<?php endif; ?>
			</div>
		</div>
	</div>
	<?php
}

/* -------------------------------------------------------------------------- */
/* Front: localize REST for portal JS                                         */
/* -------------------------------------------------------------------------- */

add_filter( 'saulocoelho_portal_js_data', 'sc_portal_notices_js_data' );
/**
 * @param array<string,mixed> $data Localized data.
 * @return array<string,mixed>
 */
function sc_portal_notices_js_data( $data ) {
	if ( ! is_array( $data ) ) {
		$data = array();
	}
	$data['notices'] = array(
		'restUrl'   => esc_url_raw( rest_url( 'saulocoelho/v1/notices' ) ),
		'nonce'     => wp_create_nonce( 'wp_rest' ),
		'emptyText' => __( 'Sem avisos por agora.', 'saulocoelho' ),
		'errorText' => __( 'Não foi possível carregar os avisos.', 'saulocoelho' ),
	);
	return $data;
}
