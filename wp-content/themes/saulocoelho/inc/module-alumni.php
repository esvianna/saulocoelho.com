<?php
/**
 * Módulo Alumni — Galeria de Turmas
 *
 * Fonte de verdade das fotos: CPT `ama_course` (`_alumni_fotos` = IDs de anexos).
 * Produto Woo: textos da secção + quais turmas exibir (`_alumni_turmas`, badge/título/subtítulo).
 *
 * Issue: https://github.com/esvianna/saulocoelho.com/issues/25
 * ADR-019
 *
 * @package SauloCoelho
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Sanitize list of attachment IDs.
 *
 * @param mixed $ids
 * @return int[]
 */
function alumni_sanitize_foto_ids( $ids ) {
	if ( is_string( $ids ) ) {
		$decoded = json_decode( wp_unslash( $ids ), true );
		$ids     = is_array( $decoded ) ? $decoded : [];
	}
	if ( ! is_array( $ids ) ) {
		return [];
	}
	$ids = array_map( 'intval', $ids );
	$ids = array_filter(
		$ids,
		static function ( $id ) {
			return $id > 0;
		}
	);
	return array_values( array_unique( $ids ) );
}

/**
 * Fotos da turma (fonte de verdade no ama_course).
 *
 * @param int $course_id
 * @return int[]
 */
function alumni_get_course_fotos( $course_id ) {
	$course_id = absint( $course_id );
	if ( ! $course_id ) {
		return [];
	}
	return alumni_sanitize_foto_ids( get_post_meta( $course_id, '_alumni_fotos', true ) );
}

/**
 * @param int   $course_id
 * @param int[] $ids
 */
function alumni_set_course_fotos( $course_id, $ids ) {
	$course_id = absint( $course_id );
	if ( ! $course_id ) {
		return;
	}
	update_post_meta( $course_id, '_alumni_fotos', alumni_sanitize_foto_ids( $ids ) );
}

/**
 * Merge attachment IDs into course gallery (union, preserve order).
 *
 * @param int   $course_id
 * @param int[] $incoming
 * @return int[] Resulting IDs
 */
function alumni_merge_course_fotos( $course_id, $incoming ) {
	$existing = alumni_get_course_fotos( $course_id );
	$incoming = alumni_sanitize_foto_ids( $incoming );
	$merged   = array_values( array_unique( array_merge( $existing, $incoming ) ) );
	alumni_set_course_fotos( $course_id, $merged );
	return $merged;
}

/**
 * Build foto payloads for front (large + medium URLs).
 *
 * @param int    $course_id
 * @param string $alt_fallback
 * @return array<int, array{id:int,full:string,thumb:string,alt:string}>
 */
function alumni_build_fotos_payload( $course_id, $alt_fallback = '' ) {
	$fotos = [];
	foreach ( alumni_get_course_fotos( $course_id ) as $img_id ) {
		$full  = wp_get_attachment_image_src( $img_id, 'large' );
		$thumb = wp_get_attachment_image_src( $img_id, 'medium' );
		if ( ! $full ) {
			continue;
		}
		$alt = get_post_meta( $img_id, '_wp_attachment_image_alt', true );
		if ( ! $alt ) {
			$alt = $alt_fallback;
		}
		$fotos[] = [
			'id'    => $img_id,
			'full'  => $full[0],
			'thumb' => $thumb ? $thumb[0] : $full[0],
			'alt'   => $alt,
		];
	}
	return $fotos;
}

// ─── Metaboxes ───────────────────────────────────────────────────────────────

add_action( 'add_meta_boxes', 'alumni_register_metaboxes' );
function alumni_register_metaboxes() {
	add_meta_box(
		'alumni_galerias_turmas',
		'📸 Galerias de Turmas Alumni [#alumni-galeria]',
		'alumni_render_product_metabox',
		'product',
		'normal',
		'default'
	);

	if ( post_type_exists( 'ama_course' ) ) {
		add_meta_box(
			'alumni_course_fotos',
			'📸 Fotos Alumni (turma)',
			'alumni_render_course_metabox',
			'ama_course',
			'normal',
			'default'
		);
	}
}

add_action( 'admin_enqueue_scripts', 'alumni_admin_enqueue' );
function alumni_admin_enqueue( $hook ) {
	global $post;
	if ( ! in_array( $hook, [ 'post.php', 'post-new.php' ], true ) || ! $post ) {
		return;
	}
	if ( ! in_array( $post->post_type, [ 'product', 'ama_course' ], true ) ) {
		return;
	}
	wp_enqueue_media();
}

/**
 * Shared Media Library picker + remove buttons (product link panels or course metabox).
 */
function alumni_print_media_picker_script() {
	static $printed = false;
	if ( $printed ) {
		return;
	}
	$printed = true;
	?>
	<script>
	(function () {
		'use strict';

		window.alumniToggleCourseHint = function (checkbox) {
			var courseId = checkbox.value;
			var hint = document.getElementById('alumni-course-hint-' + courseId);
			if (!hint) return;
			hint.style.display = checkbox.checked ? 'block' : 'none';
		};

		function openAlumniMediaPicker(courseId) {
			if (typeof wp === 'undefined' || !wp.media) {
				alert('Por favor, recarregue a página e tente novamente.');
				return;
			}
			var frame = wp.media({
				title: 'Selecionar Fotos da Turma',
				button: { text: 'Usar estas fotos' },
				library: { type: 'image' },
				multiple: true
			});
			frame.on('select', function () {
				var selection = frame.state().get('selection');
				var preview = document.querySelector('.alumni-fotos-preview[data-course-id="' + courseId + '"]');
				var input = document.querySelector('input.alumni-fotos-ids[data-course-id="' + courseId + '"]');
				if (!preview || !input) return;

				var currentIds = [];
				try { currentIds = JSON.parse(input.value) || []; } catch (e) { currentIds = []; }

				var emptyMsg = preview.querySelector('.alumni-empty-msg');
				if (emptyMsg) emptyMsg.remove();

				selection.each(function (attachment) {
					var att = attachment.toJSON();
					if (currentIds.indexOf(att.id) !== -1) return;
					currentIds.push(att.id);
					var thumbUrl = (att.sizes && att.sizes.thumbnail) ? att.sizes.thumbnail.url : att.url;
					var item = document.createElement('div');
					item.className = 'alumni-foto-item';
					item.setAttribute('data-id', att.id);
					item.style.cssText = 'position:relative;width:80px;height:80px;border-radius:6px;overflow:hidden;border:2px solid #dee2e6;';
					item.innerHTML = '<img src="' + thumbUrl + '" style="width:100%;height:100%;object-fit:cover;">'
						+ '<button type="button" class="alumni-remove-foto" data-course-id="' + courseId + '" data-img-id="' + att.id + '" '
						+ 'style="position:absolute;top:2px;right:2px;background:rgba(220,38,38,0.9);color:#fff;border:none;border-radius:50%;width:20px;height:20px;cursor:pointer;font-size:12px;line-height:1;display:flex;align-items:center;justify-content:center;padding:0;">×</button>';
					preview.appendChild(item);
				});
				input.value = JSON.stringify(currentIds);
				initRemoveButtons();
			});
			frame.open();
		}

		function initUploadButtons() {
			document.querySelectorAll('.alumni-upload-btn').forEach(function (btn) {
				if (btn.getAttribute('data-alumni-bound')) return;
				btn.setAttribute('data-alumni-bound', '1');
				btn.addEventListener('click', function () {
					openAlumniMediaPicker(this.getAttribute('data-course-id'));
				});
			});
		}

		function initRemoveButtons() {
			document.querySelectorAll('.alumni-remove-foto').forEach(function (btn) {
				if (btn.getAttribute('data-alumni-bound')) return;
				btn.setAttribute('data-alumni-bound', '1');
				btn.addEventListener('click', function () {
					var courseId = this.getAttribute('data-course-id');
					var imgId = parseInt(this.getAttribute('data-img-id'), 10);
					var input = document.querySelector('input.alumni-fotos-ids[data-course-id="' + courseId + '"]');
					var item = this.closest('.alumni-foto-item');
					if (input) {
						var ids = [];
						try { ids = JSON.parse(input.value) || []; } catch (e) { ids = []; }
						ids = ids.filter(function (id) { return id !== imgId; });
						input.value = JSON.stringify(ids);
					}
					if (item) {
						item.remove();
						var preview = document.querySelector('.alumni-fotos-preview[data-course-id="' + courseId + '"]');
						if (preview && preview.querySelectorAll('.alumni-foto-item').length === 0) {
							var msg = document.createElement('p');
							msg.className = 'alumni-empty-msg';
							msg.style.cssText = 'color:#aaa;font-size:12px;font-style:italic;';
							msg.textContent = 'Nenhuma foto adicionada. Clique em "Adicionar Fotos".';
							preview.appendChild(msg);
						}
					}
				});
			});
		}

		document.addEventListener('DOMContentLoaded', function () {
			initUploadButtons();
			initRemoveButtons();
		});
	})();
	</script>
	<?php
}

/**
 * Render photo grid UI for a single course (metabox on ama_course).
 *
 * @param int $course_id
 */
function alumni_render_fotos_editor( $course_id ) {
	$course_id   = absint( $course_id );
	$saved_fotos = alumni_get_course_fotos( $course_id );
	?>
	<div style="border:1px solid #C5A059;border-radius:8px;overflow:hidden;">
		<div style="background:#C5A059;padding:10px 16px;display:flex;align-items:center;justify-content:space-between;">
			<strong style="color:#fff;font-size:13px;">📷 Fotos desta turma</strong>
			<button type="button" class="alumni-upload-btn button" data-course-id="<?php echo esc_attr( $course_id ); ?>"
				style="background:#fff;color:#C5A059;border:none;padding:5px 12px;border-radius:5px;font-size:12px;font-weight:bold;cursor:pointer;">
				+ Adicionar Fotos
			</button>
		</div>
		<div style="padding:16px;background:#fff;">
			<input type="hidden" class="alumni-fotos-ids" data-course-id="<?php echo esc_attr( $course_id ); ?>"
				name="alumni_course_fotos" value="<?php echo esc_attr( wp_json_encode( $saved_fotos ) ); ?>">
			<div class="alumni-fotos-preview" data-course-id="<?php echo esc_attr( $course_id ); ?>"
				style="display:flex;flex-wrap:wrap;gap:8px;min-height:60px;">
				<?php if ( empty( $saved_fotos ) ) : ?>
					<p class="alumni-empty-msg" style="color:#aaa;font-size:12px;font-style:italic;">Nenhuma foto adicionada. Clique em "Adicionar Fotos".</p>
				<?php else : ?>
					<?php foreach ( $saved_fotos as $img_id ) :
						$img_src = wp_get_attachment_image_src( $img_id, 'thumbnail' );
						if ( ! $img_src ) {
							continue;
						}
						?>
						<div class="alumni-foto-item" data-id="<?php echo esc_attr( $img_id ); ?>"
							style="position:relative;width:80px;height:80px;border-radius:6px;overflow:hidden;border:2px solid #dee2e6;">
							<img src="<?php echo esc_url( $img_src[0] ); ?>" alt="" style="width:100%;height:100%;object-fit:cover;">
							<button type="button" class="alumni-remove-foto" data-course-id="<?php echo esc_attr( $course_id ); ?>" data-img-id="<?php echo esc_attr( $img_id ); ?>"
								style="position:absolute;top:2px;right:2px;background:rgba(220,38,38,0.9);color:#fff;border:none;border-radius:50%;width:20px;height:20px;cursor:pointer;font-size:12px;line-height:1;display:flex;align-items:center;justify-content:center;padding:0;">×</button>
						</div>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>
			<p style="color:#666;font-size:11px;margin:12px 0 0;">Estas fotos são reutilizadas em todos os produtos Woo que seleccionarem esta turma na galeria Alumni.</p>
		</div>
	</div>
	<?php
	alumni_print_media_picker_script();
}

function alumni_render_course_metabox( $post ) {
	wp_nonce_field( 'alumni_save_course_fotos', 'alumni_course_nonce' );
	$show_hub = get_post_meta( $post->ID, '_alumni_show_on_hub', true ) === '1';
	$can_up   = get_post_meta( $post->ID, '_alumni_students_can_upload', true ) === '1';
	?>
	<div style="background:#f0f7ff;border:1px solid #c2d3f8;border-radius:8px;padding:14px 16px;margin-bottom:16px;">
		<strong style="display:block;margin-bottom:10px;font-size:13px;color:#1a1a2e;">Sala do curso (Portal)</strong>
		<label style="display:flex;align-items:flex-start;gap:8px;font-size:13px;margin-bottom:10px;cursor:pointer;">
			<input type="checkbox" name="alumni_show_on_hub" value="1" <?php checked( $show_hub ); ?> style="margin-top:2px;accent-color:#C5A059;">
			<span>Exibir galeria «Fotos da turma» na página do curso (hub)</span>
		</label>
		<label style="display:flex;align-items:flex-start;gap:8px;font-size:13px;cursor:pointer;">
			<input type="checkbox" name="alumni_students_can_upload" value="1" <?php checked( $can_up ); ?> style="margin-top:2px;accent-color:#C5A059;">
			<span>Permitir que alunos matriculados enviem fotos (partilha com a turma)</span>
		</label>
		<p style="margin:10px 0 0;font-size:11px;color:#666;">Mentores e administradores podem sempre enviar quando a galeria está activa. Alunos só removem as fotos que eles próprios enviaram.</p>
	</div>
	<?php
	alumni_render_fotos_editor( $post->ID );
}

function alumni_render_product_metabox( $post ) {
	wp_nonce_field( 'alumni_save_metabox', 'alumni_nonce' );

	$courses = get_posts(
		[
			'post_type'      => 'ama_course',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'orderby'        => 'title',
			'order'          => 'ASC',
		]
	);

	$selected_turmas = get_post_meta( $post->ID, '_alumni_turmas', true );
	if ( ! is_array( $selected_turmas ) ) {
		$selected_turmas = [];
	}

	$alumni_badge     = get_post_meta( $post->ID, '_alumni_badge', true );
	$alumni_titulo    = get_post_meta( $post->ID, '_alumni_titulo', true );
	$alumni_subtitulo = get_post_meta( $post->ID, '_alumni_subtitulo', true );
	?>
	<div id="alumni-metabox-wrap" style="font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;">
		<?php if ( empty( $courses ) ) : ?>
			<div style="background:#fff3cd;border:1px solid #ffc107;border-radius:8px;padding:16px;color:#856404;font-size:13px;">
				<strong>⚠️ Nenhum curso cadastrado.</strong><br>
				Acesse <strong>Cursos → Adicionar Novo</strong> no AmaEducacional para criar turmas antes de configurar a galeria.
			</div>
		<?php else : ?>
			<div style="background:#e8f0fe;border:1px solid #c2d3f8;border-radius:8px;padding:16px;margin-bottom:20px;">
				<strong style="font-size:13px;color:#1a1a2e;display:block;margin-bottom:14px;">✏️ Textos da Seção (visíveis neste produto)</strong>
				<div style="display:grid;gap:12px;">
					<label style="display:flex;flex-direction:column;gap:4px;">
						<span style="font-size:12px;font-weight:600;color:#444;text-transform:uppercase;letter-spacing:.05em;">Badge</span>
						<input type="text" name="alumni_badge" value="<?php echo esc_attr( $alumni_badge ); ?>" placeholder="Memórias das Turmas" style="border:1px solid #c2d3f8;border-radius:6px;padding:8px 12px;font-size:13px;width:100%;box-sizing:border-box;">
					</label>
					<label style="display:flex;flex-direction:column;gap:4px;">
						<span style="font-size:12px;font-weight:600;color:#444;text-transform:uppercase;letter-spacing:.05em;">Título principal</span>
						<input type="text" name="alumni_titulo" value="<?php echo esc_attr( $alumni_titulo ); ?>" placeholder="Momentos que ficam para sempre" style="border:1px solid #c2d3f8;border-radius:6px;padding:8px 12px;font-size:13px;width:100%;box-sizing:border-box;">
					</label>
					<label style="display:flex;flex-direction:column;gap:4px;">
						<span style="font-size:12px;font-weight:600;color:#444;text-transform:uppercase;letter-spacing:.05em;">Subtítulo</span>
						<input type="text" name="alumni_subtitulo" value="<?php echo esc_attr( $alumni_subtitulo ); ?>" placeholder="Reviva os melhores momentos das nossas turmas" style="border:1px solid #c2d3f8;border-radius:6px;padding:8px 12px;font-size:13px;width:100%;box-sizing:border-box;">
					</label>
				</div>
				<p style="color:#666;font-size:11px;margin:10px 0 0;">💡 Deixe em branco para usar o texto padrão.</p>
			</div>

			<p style="color:#666;font-size:13px;margin:0 0 16px;">
				Seleccione as turmas a exibir. As <strong>fotos</strong> gerem-se no próprio curso (metabox «Fotos Alumni»).
			</p>

			<div style="background:#f8f9fa;border:1px solid #dee2e6;border-radius:8px;padding:16px;margin-bottom:12px;">
				<strong style="font-size:13px;color:#333;display:block;margin-bottom:12px;">Turmas neste produto:</strong>
				<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:8px;">
					<?php foreach ( $courses as $course ) :
						$checked = in_array( $course->ID, $selected_turmas, true );
						$n_fotos = count( alumni_get_course_fotos( $course->ID ) );
						$edit    = get_edit_post_link( $course->ID, 'raw' );
						?>
						<div style="padding:10px 12px;background:#fff;border:1px solid <?php echo $checked ? '#C5A059' : '#dee2e6'; ?>;border-radius:6px;">
							<label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;">
								<input type="checkbox" name="alumni_turmas[]" value="<?php echo esc_attr( $course->ID ); ?>"
									<?php checked( $checked ); ?>
									onchange="alumniToggleCourseHint(this)"
									style="width:16px;height:16px;accent-color:#C5A059;">
								<span style="color:#333;font-weight:600;"><?php echo esc_html( $course->post_title ); ?></span>
							</label>
							<div id="alumni-course-hint-<?php echo esc_attr( $course->ID ); ?>"
								style="display:<?php echo $checked ? 'block' : 'none'; ?>;margin-top:8px;font-size:12px;color:#555;">
								<?php echo esc_html( (string) (int) $n_fotos ); ?> foto<?php echo $n_fotos === 1 ? '' : 's'; ?>
								<?php if ( $edit ) : ?>
									— <a href="<?php echo esc_url( $edit ); ?>" target="_blank" rel="noopener noreferrer">Editar fotos da turma</a>
								<?php endif; ?>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
			<p style="font-size:12px;color:#666;margin:0;">
				Ferramentas → <a href="<?php echo esc_url( admin_url( 'tools.php?page=alumni-migrate' ) ); ?>">Alumni: migrar fotos</a>
				(se ainda houver fotos antigas só no produto).
			</p>
		<?php endif; ?>
	</div>
	<?php
	alumni_print_media_picker_script();
}

add_action( 'save_post_ama_course', 'alumni_save_course_metabox', 10, 2 );
function alumni_save_course_metabox( $post_id, $post ) {
	if ( ! isset( $_POST['alumni_course_nonce'] ) ) {
		return;
	}
	if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['alumni_course_nonce'] ) ), 'alumni_save_course_fotos' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	if ( isset( $_POST['alumni_course_fotos'] ) ) {
		alumni_set_course_fotos( $post_id, wp_unslash( $_POST['alumni_course_fotos'] ) );
	}
	update_post_meta( $post_id, '_alumni_show_on_hub', isset( $_POST['alumni_show_on_hub'] ) ? '1' : '' );
	update_post_meta( $post_id, '_alumni_students_can_upload', isset( $_POST['alumni_students_can_upload'] ) ? '1' : '' );
}

add_action( 'save_post_product', 'alumni_save_product_metabox', 10, 2 );
function alumni_save_product_metabox( $post_id, $post ) {
	if ( ! isset( $_POST['alumni_nonce'] ) ) {
		return;
	}
	if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['alumni_nonce'] ) ), 'alumni_save_metabox' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	update_post_meta( $post_id, '_alumni_badge', isset( $_POST['alumni_badge'] ) ? sanitize_text_field( wp_unslash( $_POST['alumni_badge'] ) ) : '' );
	update_post_meta( $post_id, '_alumni_titulo', isset( $_POST['alumni_titulo'] ) ? sanitize_text_field( wp_unslash( $_POST['alumni_titulo'] ) ) : '' );
	update_post_meta( $post_id, '_alumni_subtitulo', isset( $_POST['alumni_subtitulo'] ) ? sanitize_text_field( wp_unslash( $_POST['alumni_subtitulo'] ) ) : '' );

	$selected_turmas = isset( $_POST['alumni_turmas'] ) ? array_map( 'intval', (array) $_POST['alumni_turmas'] ) : [];
	update_post_meta( $post_id, '_alumni_turmas', $selected_turmas );
}

// ─── Migração one-shot ───────────────────────────────────────────────────────

add_action( 'admin_menu', 'alumni_register_migrate_page' );
function alumni_register_migrate_page() {
	add_management_page(
		'Alumni: migrar fotos',
		'Alumni: migrar fotos',
		'manage_options',
		'alumni-migrate',
		'alumni_render_migrate_page'
	);
}

/**
 * Copy product `_alumni_fotos_{course_id}` → course `_alumni_fotos` (union).
 *
 * @return array{products:int,courses:int,photos:int}
 */
function alumni_run_migration() {
	$products = get_posts(
		[
			'post_type'      => 'product',
			'posts_per_page' => -1,
			'post_status'    => 'any',
			'fields'         => 'ids',
		]
	);

	$touched_courses = [];
	$photo_adds      = 0;

	foreach ( $products as $pid ) {
		$pid = (int) $pid;
		$all = get_post_meta( $pid );
		if ( ! is_array( $all ) ) {
			continue;
		}
		foreach ( $all as $key => $values ) {
			if ( ! preg_match( '/^_alumni_fotos_(\d+)$/', $key, $m ) ) {
				continue;
			}
			$course_id = (int) $m[1];
			$raw       = maybe_unserialize( $values[0] ?? '' );
			$ids       = alumni_sanitize_foto_ids( $raw );
			if ( empty( $ids ) ) {
				continue;
			}
			$before = alumni_get_course_fotos( $course_id );
			$after  = alumni_merge_course_fotos( $course_id, $ids );
			$photo_adds     += max( 0, count( $after ) - count( $before ) );
			$touched_courses[ $course_id ] = true;
		}
	}

	update_option( 'alumni_fotos_migrated_v1', time(), false );

	return [
		'products' => count( $products ),
		'courses'  => count( $touched_courses ),
		'photos'   => $photo_adds,
	];
}

function alumni_render_migrate_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$result = null;
	if ( isset( $_POST['alumni_migrate_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['alumni_migrate_nonce'] ) ), 'alumni_migrate_run' ) ) {
		$result = alumni_run_migration();
	}

	$done_at = get_option( 'alumni_fotos_migrated_v1', '' );
	?>
	<div class="wrap">
		<h1>Alumni: migrar fotos para turmas</h1>
		<p>Copia as fotos antigas guardadas no <strong>produto Woo</strong> (<code>_alumni_fotos_{id}</code>) para o curso <code>ama_course</code> (<code>_alumni_fotos</code>). Se a mesma turma tiver fotos em vários produtos, faz <strong>união</strong> dos IDs.</p>
		<?php if ( $done_at ) : ?>
			<div class="notice notice-info"><p>Última migração registada: <?php echo esc_html( date_i18n( 'd/m/Y H:i', (int) $done_at ) ); ?> (pode correr de novo — é idempotente).</p></div>
		<?php endif; ?>
		<?php if ( is_array( $result ) ) : ?>
			<div class="notice notice-success"><p>
				Concluído. Produtos analisados: <?php echo (int) $result['products']; ?>;
				turmas actualizadas: <?php echo (int) $result['courses']; ?>;
				fotos novas fundidas: <?php echo (int) $result['photos']; ?>.
			</p></div>
		<?php endif; ?>
		<form method="post">
			<?php wp_nonce_field( 'alumni_migrate_run', 'alumni_migrate_nonce' ); ?>
			<?php submit_button( 'Correr migração agora', 'primary', 'submit', false ); ?>
		</form>
	</div>
	<?php
}

add_action( 'admin_notices', 'alumni_maybe_migration_notice' );
function alumni_maybe_migration_notice() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	if ( get_option( 'alumni_fotos_migrated_v1' ) ) {
		return;
	}
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( ! $screen || ! in_array( $screen->id, [ 'product', 'edit-product', 'ama_course', 'edit-ama_course', 'tools_page_alumni-migrate' ], true ) ) {
		return;
	}
	$url = admin_url( 'tools.php?page=alumni-migrate' );
	echo '<div class="notice notice-warning"><p><strong>Alumni:</strong> as fotos passaram a viver no curso. ';
	echo '<a href="' . esc_url( $url ) . '">Migrar fotos dos produtos</a> se ainda não o fez.</p></div>';
}

// ═══════════════════════════════════════════════════════════════════════════
// FASE 2 — ABA "MINHAS TURMAS" NO MINHA CONTA
// ═══════════════════════════════════════════════════════════════════════════

add_action( 'init', 'alumni_register_endpoint' );
function alumni_register_endpoint() {
	add_rewrite_endpoint( 'minhas-turmas', EP_ROOT | EP_PAGES );
}

add_action( 'init', 'alumni_maybe_flush_rewrite_rules', 99 );
function alumni_maybe_flush_rewrite_rules() {
	$stamp = 'alumni_endpoint_v1';
	if ( get_option( 'alumni_rewrite_stamp', '' ) === $stamp ) {
		return;
	}
	flush_rewrite_rules( false );
	update_option( 'alumni_rewrite_stamp', $stamp );
}

add_filter( 'woocommerce_account_menu_items', 'alumni_add_menu_item' );
function alumni_add_menu_item( $items ) {
	$logout = [];
	if ( isset( $items['customer-logout'] ) ) {
		$logout = [ 'customer-logout' => $items['customer-logout'] ];
		unset( $items['customer-logout'] );
	}
	$items['minhas-turmas'] = '🎓 Minhas Turmas';
	return array_merge( $items, $logout );
}

add_action( 'woocommerce_account_minhas-turmas_endpoint', 'alumni_render_my_account_tab' );
function alumni_render_my_account_tab() {
	$user_id = get_current_user_id();
	if ( ! $user_id ) {
		echo '<p>Por favor, faça login para ver suas turmas.</p>';
		return;
	}

	global $wpdb;
	$lms_table           = $wpdb->prefix . 'lms_enrollments';
	$enrolled_course_ids = $wpdb->get_col(
		$wpdb->prepare(
			"SELECT course_id FROM $lms_table WHERE user_id = %d AND status IN ('active','completed') ORDER BY enrolled_at DESC",
			$user_id
		)
	);
	$enrolled_course_ids = array_map( 'intval', (array) $enrolled_course_ids );

	$products_with_galleries = get_posts(
		[
			'post_type'      => 'product',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'meta_query'     => [
				[
					'key'     => '_alumni_turmas',
					'compare' => 'EXISTS',
				],
			],
		]
	);

	$galerias = [];
	foreach ( $products_with_galleries as $product_post ) {
		$pid             = $product_post->ID;
		$selected_turmas = get_post_meta( $pid, '_alumni_turmas', true );
		if ( ! is_array( $selected_turmas ) || empty( $selected_turmas ) ) {
			continue;
		}

		$turmas_do_aluno = array_intersect( array_map( 'intval', $selected_turmas ), $enrolled_course_ids );
		if ( empty( $turmas_do_aluno ) ) {
			continue;
		}

		$turmas_data = [];
		foreach ( $turmas_do_aluno as $course_id ) {
			$course = get_post( $course_id );
			if ( ! $course || $course->post_status !== 'publish' ) {
				continue;
			}
			$fotos = alumni_build_fotos_payload( $course_id, $course->post_title );
			if ( empty( $fotos ) ) {
				continue;
			}
			$turmas_data[] = [
				'id'    => $course_id,
				'title' => $course->post_title,
				'fotos' => $fotos,
			];
		}

		if ( ! empty( $turmas_data ) ) {
			$galerias[] = [
				'product_id'    => $pid,
				'product_title' => get_the_title( $pid ),
				'product_url'   => get_permalink( $pid ),
				'turmas'        => $turmas_data,
			];
		}
	}
	?>

	<style>
	.alumni-mytab-header { margin-bottom: 28px; }
	.alumni-mytab-header h2 { font-size: 22px; font-weight: 800; margin: 0 0 4px; }
	.alumni-mytab-header p  { font-size: 13px; color: #64748b; margin: 0; }
	.alumni-mytab-product   { margin-bottom: 48px; }
	.alumni-mytab-product-name {
		font-size: 15px; font-weight: 700; margin: 0 0 14px;
		display: flex; align-items: center; gap: 8px;
		border-bottom: 1px solid rgba(100,116,139,.15); padding-bottom: 12px;
	}
	.alumni-mytab-product-name a { color: #C5A059; text-decoration: none; transition: color .2s; }
	.alumni-mytab-product-name a:hover { color: #2563eb; }
	.alumni-mytab-tabs { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 16px; }
	.alumni-mytab-tab {
		background: rgba(59,130,246,.07); border: 1px solid rgba(59,130,246,.2);
		color: #C5A059; padding: 7px 18px; border-radius: 999px;
		font-size: 12px; font-weight: 700; cursor: pointer; transition: all .2s;
	}
	.alumni-mytab-tab:hover,
	.alumni-mytab-tab.active { background: #C5A059; color: #fff; box-shadow: 0 0 14px rgba(197,160,89,.3); }
	.alumni-mytab-panel { display: none; }
	.alumni-mytab-panel.active { display: block; }
	.alumni-mytab-grid {
		display: grid; gap: 8px;
		grid-template-columns: repeat(2,1fr);
	}
	@media(min-width:480px){ .alumni-mytab-grid{ grid-template-columns:repeat(3,1fr); } }
	@media(min-width:768px){ .alumni-mytab-grid{ grid-template-columns:repeat(4,1fr); } }
	.alumni-mytab-foto {
		position: relative; aspect-ratio: 1; border-radius: 10px;
		overflow: hidden; cursor: pointer; background: #1e293b;
	}
	.alumni-mytab-foto img { width:100%; height:100%; object-fit:cover; transition:transform .4s; display:block; }
	.alumni-mytab-foto:hover img { transform: scale(1.07); }
	.alumni-mytab-foto-ov {
		position:absolute; inset:0; background:rgba(0,0,0,0);
		display:flex; align-items:center; justify-content:center; transition:background .25s;
	}
	.alumni-mytab-foto:hover .alumni-mytab-foto-ov { background:rgba(0,0,0,.4); }
	.alumni-mytab-zoom {
		width:40px; height:40px; background:rgba(59,130,246,.9); border-radius:50%;
		display:flex; align-items:center; justify-content:center;
		opacity:0; transform:scale(.6); transition:opacity .2s,transform .2s;
	}
	.alumni-mytab-foto:hover .alumni-mytab-zoom { opacity:1; transform:scale(1); }
	.alumni-mytab-zoom svg { width:20px; height:20px; fill:#fff; }
	.alumni-mytab-empty {
		border: 2px dashed rgba(100,116,139,.2); border-radius: 16px;
		padding: 48px; text-align: center; color: #64748b;
	}
	</style>

	<div id="alumni-mytab-root">
		<div class="alumni-mytab-header">
			<h2>🎓 Minhas Turmas</h2>
			<p>Reviva os momentos das turmas em que você participou.</p>
		</div>

		<?php if ( empty( $galerias ) ) : ?>
			<div class="alumni-mytab-empty">
				<span class="material-symbols-outlined" style="font-size:48px;display:block;margin-bottom:12px;color:#94a3b8;">photo_library</span>
				<strong style="display:block;margin-bottom:8px;">Nenhuma galeria disponível</strong>
				<p style="font-size:13px;margin:0;">As fotos das suas turmas aparecerão aqui assim que forem publicadas.</p>
			</div>
		<?php else : ?>
			<?php foreach ( $galerias as $galeria ) :
				$pid_g = $galeria['product_id'];
				?>
				<div class="alumni-mytab-product">
					<p class="alumni-mytab-product-name">
						<span class="material-symbols-outlined" style="font-size:18px;color:#C5A059;">collections</span>
						<a href="<?php echo esc_url( $galeria['product_url'] ); ?>"><?php echo esc_html( $galeria['product_title'] ); ?></a>
					</p>

					<div class="alumni-mytab-tabs">
						<?php foreach ( $galeria['turmas'] as $ti => $turma ) : ?>
							<button type="button"
								class="alumni-mytab-tab <?php echo $ti === 0 ? 'active' : ''; ?>"
								onclick="alumniMytabSwitch(this,'<?php echo esc_attr( $pid_g ); ?>')"
								data-target="alumni-mytab-panel-<?php echo esc_attr( $pid_g ); ?>-<?php echo esc_attr( $turma['id'] ); ?>"
							><?php echo esc_html( $turma['title'] ); ?></button>
						<?php endforeach; ?>
					</div>

					<?php foreach ( $galeria['turmas'] as $ti => $turma ) : ?>
						<div class="alumni-mytab-panel <?php echo $ti === 0 ? 'active' : ''; ?>"
							 id="alumni-mytab-panel-<?php echo esc_attr( $pid_g ); ?>-<?php echo esc_attr( $turma['id'] ); ?>">
							<div class="alumni-mytab-grid">
								<?php foreach ( $turma['fotos'] as $fi => $foto ) : ?>
									<div class="alumni-mytab-foto"
										 onclick="alumniMytabLb(<?php echo esc_js( wp_json_encode( array_column( $turma['fotos'], 'full' ) ) ); ?>,<?php echo esc_js( wp_json_encode( array_column( $turma['fotos'], 'alt' ) ) ); ?>,<?php echo esc_attr( $fi ); ?>,'<?php echo esc_js( $turma['title'] ); ?>')"
										 role="button" tabindex="0">
										<img src="<?php echo esc_url( $foto['thumb'] ); ?>" alt="<?php echo esc_attr( $foto['alt'] ); ?>" loading="lazy">
										<div class="alumni-mytab-foto-ov">
											<div class="alumni-mytab-zoom">
												<svg viewBox="0 0 24 24"><path d="M15.5 14h-.79l-.28-.27A6.5 6.5 0 1 0 14 15.5c0-.29-.02-.58-.07-.86L15.5 14zm-6 0A4.5 4.5 0 1 1 14 9.5 4.5 4.5 0 0 1 9.5 14zm2.5-4.5h-2.5V7H8.5v2.5H6V11h2.5v2.5H10V11h2.5z"/></svg>
											</div>
										</div>
									</div>
								<?php endforeach; ?>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endforeach; ?>
		<?php endif; ?>
	</div>

	<div id="alumni-lb2" onclick="if(event.target===this)alumniLb2Close()" style="display:none;position:fixed;inset:0;z-index:10000;background:rgba(0,0,0,.95);align-items:center;justify-content:center;backdrop-filter:blur(4px);">
		<button onclick="alumniLb2Close()" style="position:fixed;top:20px;right:20px;width:44px;height:44px;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.15);border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;">
			<svg width="20" height="20" viewBox="0 0 24 24" stroke="#fff" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
		</button>
		<button onclick="alumniLb2Nav(-1)" style="position:fixed;left:16px;top:50%;transform:translateY(-50%);width:44px;height:44px;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.15);border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;">
			<svg width="22" height="22" viewBox="0 0 24 24" stroke="#fff" stroke-width="2.5" fill="none"><polyline points="15 18 9 12 15 6"/></svg>
		</button>
		<div style="display:flex;flex-direction:column;align-items:center;gap:14px;max-width:90vw;">
			<img id="alumni-lb2-img" src="" alt="" style="max-width:90vw;max-height:80vh;object-fit:contain;border-radius:12px;box-shadow:0 30px 60px rgba(0,0,0,.8);">
			<div id="alumni-lb2-cap" style="font-size:13px;color:rgba(255,255,255,.45);letter-spacing:.05em;"></div>
		</div>
		<button onclick="alumniLb2Nav(1)" style="position:fixed;right:16px;top:50%;transform:translateY(-50%);width:44px;height:44px;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.15);border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;">
			<svg width="22" height="22" viewBox="0 0 24 24" stroke="#fff" stroke-width="2.5" fill="none"><polyline points="9 18 15 12 9 6"/></svg>
		</button>
		<div id="alumni-lb2-cnt" style="position:fixed;bottom:24px;left:50%;transform:translateX(-50%);font-size:12px;color:rgba(255,255,255,.4);letter-spacing:.1em;font-weight:600;"></div>
	</div>

	<script>
	(function(){
		var _imgs=[],_alts=[],_idx=0,_turma='';

		window.alumniMytabSwitch=function(btn,pid){
			btn.closest('.alumni-mytab-tabs').querySelectorAll('.alumni-mytab-tab').forEach(function(b){b.classList.remove('active');});
			btn.classList.add('active');
			document.querySelectorAll('[id^="alumni-mytab-panel-'+pid+'-"]').forEach(function(p){p.classList.remove('active');});
			var t=document.getElementById(btn.getAttribute('data-target'));
			if(t)t.classList.add('active');
		};

		window.alumniMytabLb=function(imgs,alts,idx,turma){
			_imgs=typeof imgs==='string'?JSON.parse(imgs):imgs;
			_alts=typeof alts==='string'?JSON.parse(alts):alts;
			_idx=parseInt(idx,10)||0; _turma=turma||'';
			_render();
			var lb=document.getElementById('alumni-lb2');
			lb.style.display='flex';
			document.body.style.overflow='hidden';
			document.addEventListener('keydown',_key);
		};

		window.alumniLb2Close=function(){
			document.getElementById('alumni-lb2').style.display='none';
			document.body.style.overflow='';
			document.removeEventListener('keydown',_key);
		};

		window.alumniLb2Nav=function(dir){
			_idx=(_idx+dir+_imgs.length)%_imgs.length; _render();
		};

		function _render(){
			var img=document.getElementById('alumni-lb2-img');
			var cap=document.getElementById('alumni-lb2-cap');
			var cnt=document.getElementById('alumni-lb2-cnt');
			if(!img)return;
			var n=new Image();
			n.onload=function(){img.src=n.src;img.alt=_alts[_idx]||'';};
			n.src=_imgs[_idx];
			if(cap)cap.textContent=_turma+(_alts[_idx]?' — '+_alts[_idx]:'');
			if(cnt)cnt.textContent=(_idx+1)+' / '+_imgs.length;
		}

		function _key(e){
			if(e.key==='Escape')alumniLb2Close();
			if(e.key==='ArrowLeft')alumniLb2Nav(-1);
			if(e.key==='ArrowRight')alumniLb2Nav(1);
		}
	})();
	</script>
	<?php
}
