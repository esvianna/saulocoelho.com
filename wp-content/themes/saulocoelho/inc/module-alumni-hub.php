<?php
/**
 * Alumni na sala do curso — galeria + upload pelos alunos.
 *
 * Meta no ama_course:
 *   _alumni_show_on_hub           = '1' → secção na hub
 *   _alumni_students_can_upload   = '1' → alunos matriculados podem enviar
 *
 * Issue #25 follow-up / ADR-019
 *
 * @package SauloCoelho
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @param int $user_id
 * @param int $course_id
 * @return bool
 */
function alumni_user_is_enrolled_in_course( $user_id, $course_id ) {
	$user_id   = absint( $user_id );
	$course_id = absint( $course_id );
	if ( ! $user_id || ! $course_id ) {
		return false;
	}
	if ( class_exists( '\AmaEducacional\Services\EnrollmentService' ) ) {
		try {
			return ( new \AmaEducacional\Services\EnrollmentService() )->is_enrolled( $user_id, $course_id );
		} catch ( \Throwable $e ) {
			// fall through
		}
	}
	global $wpdb;
	$table = $wpdb->prefix . 'lms_enrollments';
	$found = $wpdb->get_var(
		$wpdb->prepare(
			"SELECT id FROM {$table} WHERE user_id = %d AND course_id = %d AND status IN ('active','completed') LIMIT 1",
			$user_id,
			$course_id
		)
	);
	return ! empty( $found );
}

/**
 * Pode ver a galeria na hub (matriculado, mentor ou admin).
 *
 * @param int $user_id
 * @param int $course_id
 */
function alumni_user_can_view_hub_gallery( $user_id, $course_id ) {
	$user_id   = absint( $user_id );
	$course_id = absint( $course_id );
	if ( ! $user_id || ! $course_id ) {
		return false;
	}
	if ( user_can( $user_id, 'manage_options' ) ) {
		return true;
	}
	if ( alumni_user_is_course_mentor( $user_id, $course_id ) ) {
		return true;
	}
	return alumni_user_is_enrolled_in_course( $user_id, $course_id );
}

/**
 * @param int $user_id
 * @param int $course_id
 * @return bool
 */
function alumni_user_is_course_mentor( $user_id, $course_id ) {
	$user_id   = absint( $user_id );
	$course_id = absint( $course_id );
	if ( ! $user_id || ! $course_id ) {
		return false;
	}
	if ( class_exists( '\AmaEducacional\Services\MentorService' ) ) {
		try {
			return ( new \AmaEducacional\Services\MentorService() )->user_can_mentor_course( $user_id, $course_id );
		} catch ( \Throwable $e ) {
			return false;
		}
	}
	return false;
}

/**
 * Pode enviar fotos (opção ligada + matriculado/mentor/admin).
 *
 * @param int $user_id
 * @param int $course_id
 */
function alumni_user_can_upload_hub_photo( $user_id, $course_id ) {
	$user_id   = absint( $user_id );
	$course_id = absint( $course_id );
	if ( ! alumni_user_can_view_hub_gallery( $user_id, $course_id ) ) {
		return false;
	}
	if ( user_can( $user_id, 'manage_options' ) ) {
		return true;
	}
	if ( alumni_user_is_course_mentor( $user_id, $course_id ) ) {
		return true;
	}
	if ( get_post_meta( $course_id, '_alumni_students_can_upload', true ) !== '1' ) {
		return false;
	}
	return alumni_user_is_enrolled_in_course( $user_id, $course_id );
}

/**
 * Pode apagar uma foto (autor, mentor ou admin).
 *
 * @param int $user_id
 * @param int $course_id
 * @param int $attachment_id
 */
function alumni_user_can_delete_hub_photo( $user_id, $course_id, $attachment_id ) {
	$user_id       = absint( $user_id );
	$course_id     = absint( $course_id );
	$attachment_id = absint( $attachment_id );
	if ( ! alumni_user_can_view_hub_gallery( $user_id, $course_id ) ) {
		return false;
	}
	if ( user_can( $user_id, 'manage_options' ) ) {
		return true;
	}
	if ( alumni_user_is_course_mentor( $user_id, $course_id ) ) {
		return true;
	}
	$author = (int) get_post_field( 'post_author', $attachment_id );
	return $author === $user_id && get_post_meta( $attachment_id, '_alumni_from_student', true ) === '1';
}

/**
 * Galeria visível na hub: flag ligada OU já existem fotos na turma.
 *
 * @param int $course_id
 * @return bool
 */
function alumni_course_should_show_hub_gallery( $course_id ) {
	$course_id = absint( $course_id );
	if ( ! $course_id ) {
		return false;
	}
	if ( get_post_meta( $course_id, '_alumni_show_on_hub', true ) === '1' ) {
		return true;
	}
	return count( alumni_get_course_fotos( $course_id ) ) > 0;
}

/**
 * Render da secção na sala do curso. Chamado pelo AmaEducacional se a função existir.
 *
 * @param int $course_id
 */
function alumni_render_course_hub_gallery( $course_id ) {
	$course_id = absint( $course_id );
	if ( ! $course_id || ! alumni_course_should_show_hub_gallery( $course_id ) ) {
		return;
	}

	$user_id = get_current_user_id();
	if ( ! alumni_user_can_view_hub_gallery( $user_id, $course_id ) ) {
		return;
	}

	$can_upload = alumni_user_can_upload_hub_photo( $user_id, $course_id );
	$fotos      = alumni_build_fotos_payload( $course_id, get_the_title( $course_id ) );
	$nonce      = wp_create_nonce( 'alumni_hub_' . $course_id );
	$ajax_url   = admin_url( 'admin-ajax.php' );
	?>
	<div class="ama-card ama-hub-alumni" id="ama-hub-fotos" data-course-id="<?php echo esc_attr( $course_id ); ?>">
		<div class="ama-hub-alumni__head">
			<h3><?php esc_html_e( 'Fotos da turma', 'saulocoelho' ); ?></h3>
			<?php if ( $can_upload ) : ?>
				<label class="ama-hub-alumni__upload-btn button">
					<?php esc_html_e( 'Enviar foto', 'saulocoelho' ); ?>
					<input type="file" accept="image/jpeg,image/png,image/webp" class="ama-hub-alumni__file" hidden>
				</label>
			<?php endif; ?>
		</div>
		<p class="ama-hub-alumni__hint">
			<?php
			if ( $can_upload ) {
				esc_html_e( 'Partilhe momentos com os colegas. JPEG, PNG ou WebP até 5 MB.', 'saulocoelho' );
			} else {
				esc_html_e( 'Memórias partilhadas desta turma.', 'saulocoelho' );
			}
			?>
		</p>
		<div class="ama-hub-alumni__status" hidden></div>
		<div class="ama-hub-alumni__grid" id="ama-hub-fotos-grid">
			<?php if ( empty( $fotos ) ) : ?>
				<p class="ama-hub-empty ama-hub-alumni__empty"><?php esc_html_e( 'Ainda não há fotos. Seja o primeiro a partilhar.', 'saulocoelho' ); ?></p>
			<?php else : ?>
				<?php foreach ( $fotos as $i => $foto ) :
					$can_del = alumni_user_can_delete_hub_photo( $user_id, $course_id, (int) $foto['id'] );
					?>
					<figure class="ama-hub-alumni__item" data-id="<?php echo esc_attr( $foto['id'] ); ?>">
						<button type="button" class="ama-hub-alumni__thumb" data-full="<?php echo esc_url( $foto['full'] ); ?>" data-alt="<?php echo esc_attr( $foto['alt'] ); ?>" data-index="<?php echo esc_attr( $i ); ?>">
							<img src="<?php echo esc_url( $foto['thumb'] ); ?>" alt="<?php echo esc_attr( $foto['alt'] ); ?>" loading="lazy">
						</button>
						<?php if ( $can_del ) : ?>
							<button type="button" class="ama-hub-alumni__del" data-id="<?php echo esc_attr( $foto['id'] ); ?>" aria-label="<?php esc_attr_e( 'Remover foto', 'saulocoelho' ); ?>">×</button>
						<?php endif; ?>
					</figure>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
	</div>

	<div id="ama-hub-alumni-lb" class="ama-hub-alumni-lb" hidden>
		<button type="button" class="ama-hub-alumni-lb__close" aria-label="<?php esc_attr_e( 'Fechar', 'saulocoelho' ); ?>">×</button>
		<img src="" alt="" class="ama-hub-alumni-lb__img">
	</div>

	<style>
	.ama-hub-alumni__head{display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:12px;margin-bottom:8px}
	.ama-hub-alumni__head h3{margin:0}
	.ama-hub-alumni__upload-btn{cursor:pointer;margin:0}
	.ama-hub-alumni__hint{font-size:13px;opacity:.75;margin:0 0 14px}
	.ama-hub-alumni__status{font-size:13px;margin:0 0 12px;padding:8px 12px;border-radius:8px;background:rgba(197,160,89,.12);color:inherit}
	.ama-hub-alumni__status.is-error{background:rgba(220,38,38,.15)}
	.ama-hub-alumni__grid{display:grid;grid-template-columns:repeat(2,1fr);gap:10px}
	@media(min-width:640px){.ama-hub-alumni__grid{grid-template-columns:repeat(3,1fr)}}
	@media(min-width:960px){.ama-hub-alumni__grid{grid-template-columns:repeat(4,1fr)}}
	.ama-hub-alumni__item{position:relative;margin:0;aspect-ratio:1;border-radius:12px;overflow:hidden;background:rgba(15,23,42,.5)}
	.ama-hub-alumni__thumb{display:block;width:100%;height:100%;padding:0;border:0;cursor:pointer;background:transparent}
	.ama-hub-alumni__thumb img{width:100%;height:100%;object-fit:cover;display:block;transition:transform .35s}
	.ama-hub-alumni__item:hover img{transform:scale(1.05)}
	.ama-hub-alumni__del{position:absolute;top:6px;right:6px;width:28px;height:28px;border:0;border-radius:50%;background:rgba(220,38,38,.9);color:#fff;font-size:16px;line-height:1;cursor:pointer;z-index:2}
	.ama-hub-alumni-lb{position:fixed;inset:0;z-index:100000;background:rgba(0,0,0,.92);display:flex;align-items:center;justify-content:center;padding:24px}
	.ama-hub-alumni-lb[hidden]{display:none!important}
	.ama-hub-alumni-lb__img{max-width:92vw;max-height:88vh;object-fit:contain;border-radius:12px}
	.ama-hub-alumni-lb__close{position:fixed;top:16px;right:16px;width:44px;height:44px;border:0;border-radius:50%;background:rgba(255,255,255,.12);color:#fff;font-size:24px;cursor:pointer}
	</style>

	<script>
	(function(){
		var root = document.getElementById('ama-hub-fotos');
		if (!root) return;
		var courseId = root.getAttribute('data-course-id');
		var nonce = <?php echo wp_json_encode( $nonce ); ?>;
		var ajaxUrl = <?php echo wp_json_encode( $ajax_url ); ?>;
		var grid = document.getElementById('ama-hub-fotos-grid');
		var statusEl = root.querySelector('.ama-hub-alumni__status');
		var fileInput = root.querySelector('.ama-hub-alumni__file');
		var lb = document.getElementById('ama-hub-alumni-lb');
		var lbImg = lb ? lb.querySelector('.ama-hub-alumni-lb__img') : null;

		function setStatus(msg, isError) {
			if (!statusEl) return;
			if (!msg) { statusEl.hidden = true; statusEl.textContent = ''; return; }
			statusEl.hidden = false;
			statusEl.textContent = msg;
			statusEl.classList.toggle('is-error', !!isError);
		}

		function openLb(url, alt) {
			if (!lb || !lbImg) return;
			lbImg.src = url;
			lbImg.alt = alt || '';
			lb.hidden = false;
			document.body.style.overflow = 'hidden';
		}
		function closeLb() {
			if (!lb) return;
			lb.hidden = true;
			document.body.style.overflow = '';
		}
		if (lb) {
			lb.addEventListener('click', function(e){ if (e.target === lb) closeLb(); });
			var closeBtn = lb.querySelector('.ama-hub-alumni-lb__close');
			if (closeBtn) closeBtn.addEventListener('click', closeLb);
			document.addEventListener('keydown', function(e){ if (e.key === 'Escape') closeLb(); });
		}

		root.addEventListener('click', function(e){
			var thumb = e.target.closest('.ama-hub-alumni__thumb');
			if (thumb) {
				openLb(thumb.getAttribute('data-full'), thumb.getAttribute('data-alt'));
				return;
			}
			var del = e.target.closest('.ama-hub-alumni__del');
			if (del) {
				if (!confirm('Remover esta foto?')) return;
				var fd = new FormData();
				fd.append('action', 'alumni_hub_delete_photo');
				fd.append('nonce', nonce);
				fd.append('course_id', courseId);
				fd.append('attachment_id', del.getAttribute('data-id'));
				setStatus('A remover…');
				fetch(ajaxUrl, { method: 'POST', body: fd, credentials: 'same-origin' })
					.then(function(r){ return r.json(); })
					.then(function(data){
						if (!data || !data.success) {
							setStatus((data && data.data && data.data.message) || 'Não foi possível remover.', true);
							return;
						}
						var item = del.closest('.ama-hub-alumni__item');
						if (item) item.remove();
						if (grid && !grid.querySelector('.ama-hub-alumni__item')) {
							grid.innerHTML = '<p class="ama-hub-empty ama-hub-alumni__empty">Ainda não há fotos. Seja o primeiro a partilhar.</p>';
						}
						setStatus('Foto removida.');
					})
					.catch(function(){ setStatus('Erro de rede.', true); });
			}
		});

		if (fileInput) {
			fileInput.addEventListener('change', function(){
				if (!fileInput.files || !fileInput.files[0]) return;
				var fd = new FormData();
				fd.append('action', 'alumni_hub_upload_photo');
				fd.append('nonce', nonce);
				fd.append('course_id', courseId);
				fd.append('alumni_photo', fileInput.files[0]);
				setStatus('A enviar…');
				fetch(ajaxUrl, { method: 'POST', body: fd, credentials: 'same-origin' })
					.then(function(r){ return r.json(); })
					.then(function(data){
						fileInput.value = '';
						if (!data || !data.success || !data.data) {
							setStatus((data && data.data && data.data.message) || 'Upload falhou.', true);
							return;
						}
						var empty = grid.querySelector('.ama-hub-alumni__empty');
						if (empty) empty.remove();
						var f = data.data;
						var fig = document.createElement('figure');
						fig.className = 'ama-hub-alumni__item';
						fig.setAttribute('data-id', f.id);
						fig.innerHTML = '<button type="button" class="ama-hub-alumni__thumb" data-full="'+f.full+'" data-alt="'+(f.alt||'')+'">'
							+ '<img src="'+f.thumb+'" alt="'+(f.alt||'')+'" loading="lazy"></button>'
							+ '<button type="button" class="ama-hub-alumni__del" data-id="'+f.id+'" aria-label="Remover foto">×</button>';
						grid.appendChild(fig);
						setStatus('Foto enviada. Obrigado!');
					})
					.catch(function(){ fileInput.value = ''; setStatus('Erro de rede.', true); });
			});
		}
	})();
	</script>
	<?php
}

add_action( 'wp_ajax_alumni_hub_upload_photo', 'alumni_ajax_hub_upload_photo' );
function alumni_ajax_hub_upload_photo() {
	$course_id = isset( $_POST['course_id'] ) ? absint( $_POST['course_id'] ) : 0;
	$user_id   = get_current_user_id();
	if ( ! $course_id || ! $user_id ) {
		wp_send_json_error( [ 'message' => 'Sessão inválida.' ], 403 );
	}
	if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'alumni_hub_' . $course_id ) ) {
		wp_send_json_error( [ 'message' => 'Nonce inválido.' ], 403 );
	}
	if ( ! alumni_user_can_upload_hub_photo( $user_id, $course_id ) ) {
		wp_send_json_error( [ 'message' => 'Sem permissão para enviar fotos nesta turma.' ], 403 );
	}
	if ( empty( $_FILES['alumni_photo'] ) || ! is_array( $_FILES['alumni_photo'] ) ) {
		wp_send_json_error( [ 'message' => 'Nenhum ficheiro recebido.' ], 400 );
	}
	$file = $_FILES['alumni_photo'];
	if ( ! empty( $file['error'] ) ) {
		wp_send_json_error( [ 'message' => 'Erro no upload (código ' . (int) $file['error'] . ').' ], 400 );
	}
	if ( ! empty( $file['size'] ) && (int) $file['size'] > 5 * 1024 * 1024 ) {
		wp_send_json_error( [ 'message' => 'Ficheiro demasiado grande (máx. 5 MB).' ], 400 );
	}

	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$overrides = [
		'test_form' => false,
		'mimes'     => [
			'jpg|jpeg|jpe' => 'image/jpeg',
			'png'          => 'image/png',
			'webp'         => 'image/webp',
		],
	];
	$moved = wp_handle_upload( $file, $overrides );
	if ( isset( $moved['error'] ) ) {
		wp_send_json_error( [ 'message' => $moved['error'] ], 400 );
	}

	$attachment = [
		'post_mime_type' => $moved['type'],
		'post_title'     => sanitize_file_name( wp_basename( $moved['file'] ) ),
		'post_content'   => '',
		'post_status'    => 'inherit',
		'post_author'    => $user_id,
	];
	$attach_id = wp_insert_attachment( $attachment, $moved['file'] );
	if ( is_wp_error( $attach_id ) || ! $attach_id ) {
		wp_send_json_error( [ 'message' => 'Não foi possível guardar o anexo.' ], 500 );
	}
	$meta = wp_generate_attachment_metadata( $attach_id, $moved['file'] );
	if ( $meta ) {
		wp_update_attachment_metadata( $attach_id, $meta );
	}
	update_post_meta( $attach_id, '_alumni_course_id', $course_id );
	update_post_meta( $attach_id, '_alumni_from_student', '1' );
	alumni_merge_course_fotos( $course_id, [ $attach_id ] );

	$full  = wp_get_attachment_image_src( $attach_id, 'large' );
	$thumb = wp_get_attachment_image_src( $attach_id, 'medium' );
	$user  = get_userdata( $user_id );
	$alt   = $user ? $user->display_name : '';

	wp_send_json_success(
		[
			'id'    => $attach_id,
			'full'  => $full ? $full[0] : $moved['url'],
			'thumb' => $thumb ? $thumb[0] : $moved['url'],
			'alt'   => $alt,
		]
	);
}

add_action( 'wp_ajax_alumni_hub_delete_photo', 'alumni_ajax_hub_delete_photo' );
function alumni_ajax_hub_delete_photo() {
	$course_id     = isset( $_POST['course_id'] ) ? absint( $_POST['course_id'] ) : 0;
	$attachment_id = isset( $_POST['attachment_id'] ) ? absint( $_POST['attachment_id'] ) : 0;
	$user_id       = get_current_user_id();
	if ( ! $course_id || ! $attachment_id || ! $user_id ) {
		wp_send_json_error( [ 'message' => 'Pedido inválido.' ], 400 );
	}
	if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'alumni_hub_' . $course_id ) ) {
		wp_send_json_error( [ 'message' => 'Nonce inválido.' ], 403 );
	}
	if ( ! alumni_user_can_delete_hub_photo( $user_id, $course_id, $attachment_id ) ) {
		wp_send_json_error( [ 'message' => 'Sem permissão para remover esta foto.' ], 403 );
	}

	$ids = alumni_get_course_fotos( $course_id );
	$ids = array_values( array_filter( $ids, static function ( $id ) use ( $attachment_id ) {
		return (int) $id !== $attachment_id;
	} ) );
	alumni_set_course_fotos( $course_id, $ids );

	// Só apaga o ficheiro se for upload de aluno desta turma (não remove fotos oficiais do admin).
	if ( get_post_meta( $attachment_id, '_alumni_from_student', true ) === '1'
		&& (int) get_post_meta( $attachment_id, '_alumni_course_id', true ) === $course_id ) {
		wp_delete_attachment( $attachment_id, true );
	}

	wp_send_json_success( [ 'removed' => $attachment_id ] );
}
