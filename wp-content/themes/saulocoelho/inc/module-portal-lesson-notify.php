<?php
/**
 * Issue #22 — Notificar alunos ao publicar aula LMS (ama_lesson).
 *
 * Metabox opt-in → cria aviso Portal (audience course:{id}) + fila push/e-mail (#16).
 *
 * @package SauloCoelho
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SC_LESSON_NOTIFY_META', '_sc_portal_lesson_notice_id' );
define( 'SC_LESSON_NOTIFY_AT_META', '_sc_portal_lesson_notified_at' );
define( 'SC_MATERIAL_NOTIFY_META', '_sc_portal_material_notice_id' );
define( 'SC_MATERIAL_NOTIFY_AT_META', '_sc_portal_material_notified_at' );

/**
 * Metabox na edição de aula.
 */
add_action( 'add_meta_boxes', 'sc_portal_lesson_notify_metabox' );
function sc_portal_lesson_notify_metabox() {
	if ( ! post_type_exists( 'ama_lesson' ) ) {
		return;
	}
	add_meta_box(
		'sc_portal_lesson_notify',
		__( 'Notificar alunos (Portal)', 'saulocoelho' ),
		'sc_portal_lesson_notify_render',
		'ama_lesson',
		'side',
		'default'
	);
}

/**
 * Estado de notificação da aula (meta).
 *
 * @param int $lesson_id Lesson ID.
 * @return array{notice_id:int,notified_at:string}
 */
function sc_portal_lesson_notify_status( $lesson_id ) {
	$lesson_id = (int) $lesson_id;
	return array(
		'notice_id'   => (int) get_post_meta( $lesson_id, SC_LESSON_NOTIFY_META, true ),
		'notified_at' => (string) get_post_meta( $lesson_id, SC_LESSON_NOTIFY_AT_META, true ),
	);
}

/**
 * Cria aviso Portal + opcionalmente enfileira push/e-mail para uma aula.
 *
 * Usado pelo metabox admin e pelo painel do mentor (REST Ama).
 *
 * @param int   $lesson_id Lesson ID.
 * @param array $args {
 *     @type int  $course_id  Curso (opcional; senão usa `_ama_parent_course`).
 *     @type bool $force      Reenviar mesmo se já notificado.
 *     @type bool $send_push  Enfileirar push.
 *     @type bool $send_email Enfileirar e-mail.
 * }
 * @return array|\WP_Error { notice_id, notified_at, message, push? }
 */
function sc_portal_lesson_notify_send( $lesson_id, array $args = array() ) {
	$lesson_id = (int) $lesson_id;
	$post      = get_post( $lesson_id );
	if ( ! $post || 'ama_lesson' !== $post->post_type ) {
		return new \WP_Error( 'not_found', __( 'Aula não encontrada.', 'saulocoelho' ), array( 'status' => 404 ) );
	}
	if ( 'publish' !== $post->post_status ) {
		return new \WP_Error( 'not_published', __( 'Publique a aula antes de notificar.', 'saulocoelho' ), array( 'status' => 400 ) );
	}
	if ( ! function_exists( 'sc_portal_notice_create' ) ) {
		return new \WP_Error( 'unavailable', __( 'Avisos Portal indisponíveis.', 'saulocoelho' ), array( 'status' => 503 ) );
	}

	$course_id = isset( $args['course_id'] ) ? (int) $args['course_id'] : (int) get_post_meta( $lesson_id, '_ama_parent_course', true );
	if ( $course_id < 1 ) {
		return new \WP_Error( 'no_course', __( 'Associe a aula a um curso antes de notificar.', 'saulocoelho' ), array( 'status' => 400 ) );
	}
	$parent = (int) get_post_meta( $lesson_id, '_ama_parent_course', true );
	if ( $parent > 0 && $parent !== $course_id ) {
		return new \WP_Error( 'wrong_course', __( 'Esta aula não pertence a este curso.', 'saulocoelho' ), array( 'status' => 400 ) );
	}

	$force       = ! empty( $args['force'] );
	$prev_notice = (int) get_post_meta( $lesson_id, SC_LESSON_NOTIFY_META, true );
	if ( $prev_notice > 0 && ! $force ) {
		return new \WP_Error(
			'already',
			__( 'Alunos já foram notificados desta aula. Use forçar para enviar outra vez.', 'saulocoelho' ),
			array(
				'status'    => 409,
				'notice_id' => $prev_notice,
			)
		);
	}

	$send_push  = array_key_exists( 'send_push', $args ) ? (bool) $args['send_push'] : true;
	$send_email = ! empty( $args['send_email'] );

	$lesson_title = get_the_title( $lesson_id );
	$course_title = get_the_title( $course_id );
	$link         = add_query_arg( 'lesson_id', $lesson_id, get_permalink( $course_id ) );

	$title = sprintf(
		/* translators: 1: lesson title */
		__( 'Nova aula: %s', 'saulocoelho' ),
		$lesson_title ? $lesson_title : ( '#' . $lesson_id )
	);
	$body = sprintf(
		/* translators: 1: course title */
		__( 'Há conteúdo novo em %s. Toque para abrir a aula.', 'saulocoelho' ),
		$course_title ? $course_title : __( 'o seu curso', 'saulocoelho' )
	);

	$notice_id = sc_portal_notice_create(
		array(
			'title'     => $title,
			'body'      => $body,
			'link_url'  => $link,
			'audience'  => 'course:' . $course_id,
			'status'    => 'published',
			'author_id' => get_current_user_id(),
		)
	);
	if ( is_wp_error( $notice_id ) ) {
		return $notice_id;
	}

	$notified_at = current_time( 'mysql' );
	update_post_meta( $lesson_id, SC_LESSON_NOTIFY_META, (int) $notice_id );
	update_post_meta( $lesson_id, SC_LESSON_NOTIFY_AT_META, $notified_at );
	if ( $parent < 1 ) {
		update_post_meta( $lesson_id, '_ama_parent_course', $course_id );
	}

	$extra = '';
	$push  = null;
	if ( ( $send_push || $send_email ) && function_exists( 'sc_portal_push_on_notice_saved' ) ) {
		$push = sc_portal_push_on_notice_saved( (int) $notice_id, $send_push, $send_email );
		if ( is_array( $push ) ) {
			if ( ! empty( $push['error'] ) ) {
				if ( 'muted' === $push['error'] ) {
					$extra = ' ' . __( 'Push/e-mail bloqueados: curso silenciado. O aviso ficou no inbox.', 'saulocoelho' );
				} elseif ( 'unavailable' === $push['error'] ) {
					$extra = ' ' . __( 'Push indisponível; aviso criado no inbox.', 'saulocoelho' );
				} else {
					$extra = ' ' . __( 'Aviso criado; fila de envio com erro.', 'saulocoelho' );
				}
			} elseif ( ! empty( $push['queued'] ) ) {
				$extra = ' ' . sprintf(
					/* translators: %d: job id */
					__( 'Fila de envio #%d iniciada.', 'saulocoelho' ),
					(int) ( $push['job_id'] ?? 0 )
				);
			}
		}
	}

	return array(
		'notice_id'   => (int) $notice_id,
		'notified_at' => $notified_at,
		'message'     => sprintf(
			/* translators: %d: notice id */
			__( 'Alunos notificados (aviso #%d).', 'saulocoelho' ),
			(int) $notice_id
		) . $extra,
		'push'        => $push,
	);
}

/**
 * Estado de notificação do material (meta).
 *
 * @param int $material_id Material ID.
 * @return array{notice_id:int,notified_at:string}
 */
function sc_portal_material_notify_status( $material_id ) {
	$material_id = (int) $material_id;
	return array(
		'notice_id'   => (int) get_post_meta( $material_id, SC_MATERIAL_NOTIFY_META, true ),
		'notified_at' => (string) get_post_meta( $material_id, SC_MATERIAL_NOTIFY_AT_META, true ),
	);
}

/**
 * Cria aviso Portal + opcionalmente enfileira push/e-mail para material de apoio.
 *
 * Usado pelo painel do mentor (REST Ama) ao enviar PDF.
 *
 * @param int   $material_id Material (ama_material) ID.
 * @param array $args {
 *     @type int  $course_id  Curso (opcional; senão usa `_ama_parent_course`).
 *     @type bool $force      Reenviar mesmo se já notificado.
 *     @type bool $send_push  Enfileirar push.
 *     @type bool $send_email Enfileirar e-mail.
 * }
 * @return array|\WP_Error { notice_id, notified_at, message, push? }
 */
function sc_portal_material_notify_send( $material_id, array $args = array() ) {
	$material_id = (int) $material_id;
	$post        = get_post( $material_id );
	if ( ! $post || 'ama_material' !== $post->post_type ) {
		return new \WP_Error( 'not_found', __( 'Material não encontrado.', 'saulocoelho' ), array( 'status' => 404 ) );
	}
	if ( 'publish' !== $post->post_status ) {
		return new \WP_Error( 'not_published', __( 'Publique o material antes de notificar.', 'saulocoelho' ), array( 'status' => 400 ) );
	}
	if ( ! function_exists( 'sc_portal_notice_create' ) ) {
		return new \WP_Error( 'unavailable', __( 'Avisos Portal indisponíveis.', 'saulocoelho' ), array( 'status' => 503 ) );
	}

	$course_id = isset( $args['course_id'] ) ? (int) $args['course_id'] : (int) get_post_meta( $material_id, '_ama_parent_course', true );
	if ( $course_id < 1 ) {
		return new \WP_Error( 'no_course', __( 'Associe o material a um curso antes de notificar.', 'saulocoelho' ), array( 'status' => 400 ) );
	}
	$parent = (int) get_post_meta( $material_id, '_ama_parent_course', true );
	if ( $parent > 0 && $parent !== $course_id ) {
		return new \WP_Error( 'wrong_course', __( 'Este material não pertence a este curso.', 'saulocoelho' ), array( 'status' => 400 ) );
	}

	$force       = ! empty( $args['force'] );
	$prev_notice = (int) get_post_meta( $material_id, SC_MATERIAL_NOTIFY_META, true );
	if ( $prev_notice > 0 && ! $force ) {
		return new \WP_Error(
			'already',
			__( 'Alunos já foram notificados deste material. Use forçar para enviar outra vez.', 'saulocoelho' ),
			array(
				'status'    => 409,
				'notice_id' => $prev_notice,
			)
		);
	}

	$send_push  = array_key_exists( 'send_push', $args ) ? (bool) $args['send_push'] : true;
	$send_email = ! empty( $args['send_email'] );

	$material_title = get_the_title( $material_id );
	$course_title   = get_the_title( $course_id );
	$link           = add_query_arg( 'lesson_id', $material_id, get_permalink( $course_id ) );

	$title = sprintf(
		/* translators: 1: material title */
		__( 'Novo material: %s', 'saulocoelho' ),
		$material_title ? $material_title : ( '#' . $material_id )
	);
	$body = sprintf(
		/* translators: 1: course title */
		__( 'Há material de apoio novo em %s. Toque para abrir.', 'saulocoelho' ),
		$course_title ? $course_title : __( 'o seu curso', 'saulocoelho' )
	);

	$notice_id = sc_portal_notice_create(
		array(
			'title'     => $title,
			'body'      => $body,
			'link_url'  => $link,
			'audience'  => 'course:' . $course_id,
			'status'    => 'published',
			'author_id' => get_current_user_id(),
		)
	);
	if ( is_wp_error( $notice_id ) ) {
		return $notice_id;
	}

	$notified_at = current_time( 'mysql' );
	update_post_meta( $material_id, SC_MATERIAL_NOTIFY_META, (int) $notice_id );
	update_post_meta( $material_id, SC_MATERIAL_NOTIFY_AT_META, $notified_at );
	if ( $parent < 1 ) {
		update_post_meta( $material_id, '_ama_parent_course', $course_id );
	}

	$extra = '';
	$push  = null;
	if ( ( $send_push || $send_email ) && function_exists( 'sc_portal_push_on_notice_saved' ) ) {
		$push = sc_portal_push_on_notice_saved( (int) $notice_id, $send_push, $send_email );
		if ( is_array( $push ) ) {
			if ( ! empty( $push['error'] ) ) {
				if ( 'muted' === $push['error'] ) {
					$extra = ' ' . __( 'Push/e-mail bloqueados: curso silenciado. O aviso ficou no inbox.', 'saulocoelho' );
				} elseif ( 'unavailable' === $push['error'] ) {
					$extra = ' ' . __( 'Push indisponível; aviso criado no inbox.', 'saulocoelho' );
				} else {
					$extra = ' ' . __( 'Aviso criado; fila de envio com erro.', 'saulocoelho' );
				}
			} elseif ( ! empty( $push['queued'] ) ) {
				$extra = ' ' . sprintf(
					/* translators: %d: job id */
					__( 'Fila de envio #%d iniciada.', 'saulocoelho' ),
					(int) ( $push['job_id'] ?? 0 )
				);
			}
		}
	}

	return array(
		'notice_id'   => (int) $notice_id,
		'notified_at' => $notified_at,
		'message'     => sprintf(
			/* translators: %d: notice id */
			__( 'Alunos notificados (aviso #%d).', 'saulocoelho' ),
			(int) $notice_id
		) . $extra,
		'push'        => $push,
	);
}

/**
 * @param \WP_Post $post Post.
 */
function sc_portal_lesson_notify_render( $post ) {
	wp_nonce_field( 'sc_portal_lesson_notify', 'sc_portal_lesson_notify_nonce' );

	$course_id   = (int) get_post_meta( $post->ID, '_ama_parent_course', true );
	$status      = sc_portal_lesson_notify_status( (int) $post->ID );
	$notice_id   = (int) $status['notice_id'];
	$notified_at = (string) $status['notified_at'];
	$push_ready  = function_exists( 'sc_portal_push_ready' ) && sc_portal_push_ready();

	echo '<p class="description" style="margin-top:0;">';
	esc_html_e( 'Cria um aviso no sininho dos alunos matriculados neste curso. Desligado por omissão — não dispara em cada gravação.', 'saulocoelho' );
	echo '</p>';

	if ( $course_id < 1 ) {
		echo '<p><strong>' . esc_html__( 'Associe a aula a um curso (caixa «Associar a um Curso») antes de notificar.', 'saulocoelho' ) . '</strong></p>';
	} else {
		$ct = get_the_title( $course_id );
		echo '<p class="description">' . esc_html(
			sprintf(
				/* translators: %s: course title */
				__( 'Curso: %s', 'saulocoelho' ),
				$ct ? $ct : ( '#' . $course_id )
			)
		) . '</p>';
	}

	if ( $notice_id > 0 ) {
		echo '<p style="margin:0.5rem 0;padding:0.5rem;background:#f0f6fc;border-left:3px solid #C5A059;">';
		echo esc_html(
			sprintf(
				/* translators: 1: notice id 2: datetime */
				__( 'Já notificado (aviso #%1$d%2$s).', 'saulocoelho' ),
				$notice_id,
				$notified_at ? ' · ' . $notified_at : ''
			)
		);
		echo '</p>';
	}

	echo '<p><label><input type="checkbox" name="sc_notify_students" value="1" /> ';
	esc_html_e( 'Notificar alunos deste curso', 'saulocoelho' );
	echo '</label></p>';

	if ( $notice_id > 0 ) {
		echo '<p><label><input type="checkbox" name="sc_notify_force" value="1" /> ';
		esc_html_e( 'Forçar novo aviso (já notificado)', 'saulocoelho' );
		echo '</label></p>';
	}

	echo '<p><label><input type="checkbox" name="sc_notify_push" value="1" checked="checked" /> ';
	esc_html_e( 'Enviar push agora', 'saulocoelho' );
	echo '</label></p>';
	if ( ! $push_ready ) {
		echo '<p class="description">' . esc_html__( 'Push indisponível (VAPID/vendor). O aviso no inbox continua a ser criado.', 'saulocoelho' ) . '</p>';
	}

	echo '<p><label><input type="checkbox" name="sc_notify_email" value="1" /> ';
	esc_html_e( 'Enviar também por e-mail', 'saulocoelho' );
	echo '</label></p>';

	echo '<p class="description">' . esc_html__( 'Só actua se a aula estiver publicada. Silenciar curso (#16) bloqueia push/e-mail.', 'saulocoelho' ) . '</p>';
}

/**
 * Após gravar a aula (curso já associado pelo LessonBinder).
 *
 * @param int      $post_id Post ID.
 * @param \WP_Post $post    Post.
 * @param bool     $update  Update.
 */
add_action( 'save_post_ama_lesson', 'sc_portal_lesson_notify_on_save', 30, 3 );
function sc_portal_lesson_notify_on_save( $post_id, $post, $update ) {
	unset( $update );

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( wp_is_post_revision( $post_id ) ) {
		return;
	}
	if ( ! isset( $_POST['sc_portal_lesson_notify_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['sc_portal_lesson_notify_nonce'] ) ), 'sc_portal_lesson_notify' ) ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	if ( empty( $_POST['sc_notify_students'] ) ) {
		return;
	}

	$result = sc_portal_lesson_notify_send(
		(int) $post_id,
		array(
			'force'      => ! empty( $_POST['sc_notify_force'] ),
			'send_push'  => ! empty( $_POST['sc_notify_push'] ),
			'send_email' => ! empty( $_POST['sc_notify_email'] ),
		)
	);

	if ( is_wp_error( $result ) ) {
		$type = 'error';
		$code = $result->get_error_code();
		if ( 'already' === $code ) {
			$type = 'warning';
		}
		set_transient(
			'sc_lesson_notify_flash_' . get_current_user_id(),
			array(
				'type' => $type,
				'msg'  => $result->get_error_message(),
			),
			45
		);
		return;
	}

	set_transient(
		'sc_lesson_notify_flash_' . get_current_user_id(),
		array(
			'type' => 'success',
			'msg'  => isset( $result['message'] ) ? (string) $result['message'] : __( 'Alunos notificados.', 'saulocoelho' ),
		),
		45
	);
}

/**
 * Flash admin após redirect do save.
 */
add_action( 'admin_notices', 'sc_portal_lesson_notify_admin_notice' );
function sc_portal_lesson_notify_admin_notice() {
	$uid   = get_current_user_id();
	$flash = get_transient( 'sc_lesson_notify_flash_' . $uid );
	if ( ! is_array( $flash ) || empty( $flash['msg'] ) ) {
		return;
	}
	delete_transient( 'sc_lesson_notify_flash_' . $uid );
	$class = 'notice-info';
	if ( isset( $flash['type'] ) ) {
		if ( 'success' === $flash['type'] ) {
			$class = 'notice-success';
		} elseif ( 'error' === $flash['type'] ) {
			$class = 'notice-error';
		} elseif ( 'warning' === $flash['type'] ) {
			$class = 'notice-warning';
		}
	}
	printf(
		'<div class="notice %1$s is-dismissible"><p>%2$s</p></div>',
		esc_attr( $class ),
		esc_html( (string) $flash['msg'] )
	);
}
