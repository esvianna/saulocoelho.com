<?php
/**
 * CRUD admin — formulários pós-inscrição (apenas administrator).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_menu', 'sc_forms_admin_menu' );

function sc_forms_admin_menu() {
	add_submenu_page(
		'woocommerce',
		__( 'Formulários pós-inscrição', 'saulocoelho' ),
		__( 'Formulários pós-inscrição', 'saulocoelho' ),
		'manage_options',
		'sc-post-order-forms',
		'sc_forms_admin_router'
	);
}

add_action( 'admin_post_sc_forms_save', 'sc_forms_admin_handle_save' );
add_action( 'admin_post_sc_forms_duplicate', 'sc_forms_admin_handle_duplicate' );
add_action( 'admin_post_sc_forms_archive', 'sc_forms_admin_handle_archive' );

function sc_forms_admin_can_manage() {
	return current_user_can( 'manage_options' );
}

function sc_forms_admin_router() {
	if ( ! sc_forms_admin_can_manage() ) {
		wp_die( esc_html__( 'Sem permissão.', 'saulocoelho' ) );
	}

	$action = isset( $_GET['action'] ) ? sanitize_key( wp_unslash( $_GET['action'] ) ) : 'list';
	$form_id = isset( $_GET['form_id'] ) ? absint( $_GET['form_id'] ) : 0;

	if ( $action === 'edit' || $action === 'new' ) {
		sc_forms_admin_edit_page( $action === 'new' ? 0 : $form_id );
		return;
	}

	sc_forms_admin_list_page();
}

function sc_forms_admin_list_page() {
	$forms = sc_forms_list_forms();
	settings_errors( 'sc_forms' );
	?>
	<div class="wrap">
		<h1 class="wp-heading-inline"><?php esc_html_e( 'Formulários pós-inscrição', 'saulocoelho' ); ?></h1>
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=sc-post-order-forms&action=new' ) ); ?>" class="page-title-action"><?php esc_html_e( 'Adicionar novo', 'saulocoelho' ); ?></a>
		<hr class="wp-header-end" />
		<p><?php esc_html_e( 'Crie questionários e vincule-os aos produtos WooCommerce (metabox lateral do produto).', 'saulocoelho' ); ?></p>
		<table class="widefat striped">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Título', 'saulocoelho' ); ?></th>
					<th><?php esc_html_e( 'Slug', 'saulocoelho' ); ?></th>
					<th><?php esc_html_e( 'Versão', 'saulocoelho' ); ?></th>
					<th><?php esc_html_e( 'Status', 'saulocoelho' ); ?></th>
					<th><?php esc_html_e( 'Ações', 'saulocoelho' ); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php if ( empty( $forms ) ) : ?>
				<tr><td colspan="5"><?php esc_html_e( 'Nenhum formulário cadastrado.', 'saulocoelho' ); ?></td></tr>
			<?php else : ?>
				<?php foreach ( $forms as $form ) : ?>
					<tr>
						<td><strong><?php echo esc_html( $form->title ); ?></strong></td>
						<td><code><?php echo esc_html( $form->schema_slug ); ?></code></td>
						<td><?php echo (int) $form->version; ?></td>
						<td><?php echo esc_html( $form->status === 'active' ? __( 'Ativo', 'saulocoelho' ) : __( 'Arquivado', 'saulocoelho' ) ); ?></td>
						<td>
							<a href="<?php echo esc_url( admin_url( 'admin.php?page=sc-post-order-forms&action=edit&form_id=' . (int) $form->id ) ); ?>"><?php esc_html_e( 'Editar', 'saulocoelho' ); ?></a>
							|
							<a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=sc_forms_duplicate&form_id=' . (int) $form->id ), 'sc_forms_duplicate_' . (int) $form->id ) ); ?>"><?php esc_html_e( 'Duplicar', 'saulocoelho' ); ?></a>
							<?php if ( $form->status === 'active' ) : ?>
								|
								<a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=sc_forms_archive&form_id=' . (int) $form->id ), 'sc_forms_archive_' . (int) $form->id ) ); ?>" onclick="return confirm('<?php echo esc_js( __( 'Arquivar este formulário?', 'saulocoelho' ) ); ?>');"><?php esc_html_e( 'Arquivar', 'saulocoelho' ); ?></a>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
			</tbody>
		</table>
	</div>
	<?php
}

function sc_forms_admin_edit_page( $form_id ) {
	$form_id = absint( $form_id );
	$form    = $form_id ? sc_forms_get_form( $form_id ) : null;
	$schema  = $form_id ? sc_forms_build_schema_array( $form_id ) : array( 'sections' => array(), 'fields' => array() );

	settings_errors( 'sc_forms' );
	?>
	<div class="wrap">
		<h1><?php echo $form ? esc_html__( 'Editar formulário', 'saulocoelho' ) : esc_html__( 'Novo formulário', 'saulocoelho' ); ?></h1>
		<p><a href="<?php echo esc_url( admin_url( 'admin.php?page=sc-post-order-forms' ) ); ?>">&larr; <?php esc_html_e( 'Voltar', 'saulocoelho' ); ?></a></p>

		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" id="sc-forms-editor">
			<?php wp_nonce_field( 'sc_forms_save' ); ?>
			<input type="hidden" name="action" value="sc_forms_save" />
			<input type="hidden" name="form_id" value="<?php echo (int) $form_id; ?>" />

			<table class="form-table">
				<tr>
					<th><label for="sc_form_title"><?php esc_html_e( 'Título interno', 'saulocoelho' ); ?></label></th>
					<td><input type="text" class="regular-text" id="sc_form_title" name="title" value="<?php echo esc_attr( $form ? $form->title : '' ); ?>" required /></td>
				</tr>
				<tr>
					<th><label for="sc_form_description"><?php esc_html_e( 'Descrição para o aluno', 'saulocoelho' ); ?></label></th>
					<td><textarea class="large-text" id="sc_form_description" name="description" rows="3"><?php echo esc_textarea( $form ? $form->description : '' ); ?></textarea></td>
				</tr>
				<?php if ( $form ) : ?>
				<tr>
					<th><?php esc_html_e( 'Identificador', 'saulocoelho' ); ?></th>
					<td><code><?php echo esc_html( $form->schema_slug ); ?></code> — <?php printf( esc_html__( 'Versão %d', 'saulocoelho' ), (int) $form->version ); ?></td>
				</tr>
				<?php endif; ?>
			</table>

			<h2><?php esc_html_e( 'Seções e perguntas', 'saulocoelho' ); ?></h2>
			<p class="description"><?php esc_html_e( 'Ordem livre. Campos condicionais não estão disponíveis nesta versão.', 'saulocoelho' ); ?></p>

			<div id="sc-sections-wrap">
				<?php
				$sections = $schema['sections'];
				if ( empty( $sections ) ) {
					$sections = array( array( 'id' => 'sec_1', 'title' => __( 'Seção 1', 'saulocoelho' ) ) );
				}
				foreach ( $sections as $si => $section ) :
					sc_forms_admin_render_section_block( $si, $section, $schema['fields'] );
				endforeach;
				?>
			</div>

			<p><button type="button" class="button" id="sc-add-section"><?php esc_html_e( '+ Adicionar seção', 'saulocoelho' ); ?></button></p>

			<?php submit_button( __( 'Salvar formulário', 'saulocoelho' ) ); ?>
		</form>
	</div>

	<script>
	(function(){
		var types = <?php echo wp_json_encode( sc_forms_allowed_field_types() ); ?>;
		var sectionIndex = document.querySelectorAll('.sc-section-block').length;

		function typeOptions(selected) {
			var html = '';
			for (var k in types) {
				html += '<option value="'+k+'"'+(selected===k?' selected':'')+'>'+types[k]+'</option>';
			}
			return html;
		}

		function fieldRow(si, fi, data) {
			data = data || {};
			var key = data.key || ('field_' + si + '_' + fi);
			var label = data.label || '';
			var type = data.type || 'text';
			var required = data.required ? ' checked' : '';
			var maxLen = data.max_length || '';
			var options = '';
			if (data.options) {
				for (var ok in data.options) {
					options += ok + '|' + data.options[ok] + '\n';
				}
			}
			return '<div class="sc-field-row" style="border:1px solid #ddd;padding:12px;margin:8px 0;background:#fafafa;">' +
				'<p><strong>Pergunta</strong> <button type="button" class="button-link sc-remove-field" style="float:right;color:#b32d2e;">Remover</button></p>' +
				'<p><label>Chave <input type="text" name="fields['+si+']['+fi+'][key]" value="'+key+'" class="regular-text" /></label></p>' +
				'<p><label>Texto da pergunta <input type="text" name="fields['+si+']['+fi+'][label]" value="'+label.replace(/"/g,'&quot;')+'" class="large-text" required /></label></p>' +
				'<p><label>Tipo <select name="fields['+si+']['+fi+'][type]">'+typeOptions(type)+'</select></label> ' +
				'<label><input type="checkbox" name="fields['+si+']['+fi+'][required]" value="1"'+required+' /> Obrigatório</label> ' +
				'<label>Máx. chars <input type="number" name="fields['+si+']['+fi+'][max_length]" value="'+maxLen+'" style="width:80px;" min="0" /></label></p>' +
				'<p><label>Opções (chave|rótulo, uma por linha — select/multiselect)<br><textarea name="fields['+si+']['+fi+'][options_text]" rows="3" class="large-text">'+options+'</textarea></label></p>' +
				'<input type="hidden" name="fields['+si+']['+fi+'][sort_order]" value="'+fi+'" />' +
				'</div>';
		}

		document.getElementById('sc-add-section').addEventListener('click', function(){
			var si = sectionIndex++;
			var block = document.createElement('div');
			block.className = 'sc-section-block';
			block.style.cssText = 'border:2px solid #c3c4c7;padding:16px;margin:16px 0;background:#fff;';
			block.innerHTML = '<h3>Seção <button type="button" class="button-link sc-remove-section" style="color:#b32d2e;">Remover seção</button></h3>' +
				'<p><label>Chave da seção <input type="text" name="sections['+si+'][section_key]" value="sec_'+(si+1)+'" /></label> ' +
				'<label>Título <input type="text" name="sections['+si+'][title]" value="Nova seção" class="regular-text" required /></label></p>' +
				'<input type="hidden" name="sections['+si+'][sort_order]" value="'+si+'" />' +
				'<div class="sc-fields-wrap"></div>' +
				'<button type="button" class="button sc-add-field" data-section="'+si+'">+ Pergunta</button>';
			document.getElementById('sc-sections-wrap').appendChild(block);
		});

		document.getElementById('sc-sections-wrap').addEventListener('click', function(e){
			if (e.target.classList.contains('sc-add-field')) {
				var si = e.target.getAttribute('data-section');
				var wrap = e.target.previousElementSibling;
				var fi = wrap.querySelectorAll('.sc-field-row').length;
				wrap.insertAdjacentHTML('beforeend', fieldRow(si, fi, {}));
			}
			if (e.target.classList.contains('sc-remove-field')) {
				e.target.closest('.sc-field-row').remove();
			}
			if (e.target.classList.contains('sc-remove-section')) {
				if (confirm('<?php echo esc_js( __( 'Remover esta seção e todas as perguntas?', 'saulocoelho' ) ); ?>')) {
					e.target.closest('.sc-section-block').remove();
				}
			}
		});
	})();
	</script>
	<?php
}

/**
 * @param int   $si
 * @param array $section
 * @param array $all_fields
 */
function sc_forms_admin_render_section_block( $si, array $section, array $all_fields ) {
	$sid    = $section['id'];
	$fields = array_values(
		array_filter(
			$all_fields,
			static function ( $f ) use ( $sid ) {
				return ( $f['section'] ?? '' ) === $sid;
			}
		)
	);
	if ( empty( $fields ) ) {
		$fields = array( array( 'key' => '', 'label' => '', 'type' => 'text' ) );
	}
	?>
	<div class="sc-section-block" style="border:2px solid #c3c4c7;padding:16px;margin:16px 0;background:#fff;">
		<h3><?php esc_html_e( 'Seção', 'saulocoelho' ); ?>
			<button type="button" class="button-link sc-remove-section" style="color:#b32d2e;"><?php esc_html_e( 'Remover seção', 'saulocoelho' ); ?></button>
		</h3>
		<p>
			<label><?php esc_html_e( 'Chave da seção', 'saulocoelho' ); ?>
				<input type="text" name="sections[<?php echo (int) $si; ?>][section_key]" value="<?php echo esc_attr( $sid ); ?>" />
			</label>
			<label><?php esc_html_e( 'Título', 'saulocoelho' ); ?>
				<input type="text" name="sections[<?php echo (int) $si; ?>][title]" value="<?php echo esc_attr( $section['title'] ); ?>" class="regular-text" required />
			</label>
		</p>
		<input type="hidden" name="sections[<?php echo (int) $si; ?>][sort_order]" value="<?php echo (int) $si; ?>" />
		<div class="sc-fields-wrap">
			<?php foreach ( $fields as $fi => $field ) : sc_forms_admin_render_field_row( $si, $fi, $field ); endforeach; ?>
		</div>
		<button type="button" class="button sc-add-field" data-section="<?php echo (int) $si; ?>"><?php esc_html_e( '+ Pergunta', 'saulocoelho' ); ?></button>
	</div>
	<?php
}

/**
 * @param int   $si
 * @param int   $fi
 * @param array $field
 */
function sc_forms_admin_render_field_row( $si, $fi, array $field ) {
	$types = sc_forms_allowed_field_types();
	$opts_text = '';
	if ( ! empty( $field['options'] ) && is_array( $field['options'] ) ) {
		foreach ( $field['options'] as $ok => $ol ) {
			$opts_text .= $ok . '|' . $ol . "\n";
		}
	}
	?>
	<div class="sc-field-row" style="border:1px solid #ddd;padding:12px;margin:8px 0;background:#fafafa;">
		<p><strong><?php esc_html_e( 'Pergunta', 'saulocoelho' ); ?></strong>
			<button type="button" class="button-link sc-remove-field" style="float:right;color:#b32d2e;"><?php esc_html_e( 'Remover', 'saulocoelho' ); ?></button>
		</p>
		<p><label><?php esc_html_e( 'Chave', 'saulocoelho' ); ?>
			<input type="text" name="fields[<?php echo (int) $si; ?>][<?php echo (int) $fi; ?>][key]" value="<?php echo esc_attr( $field['key'] ?? '' ); ?>" class="regular-text" />
		</label></p>
		<p><label><?php esc_html_e( 'Texto da pergunta', 'saulocoelho' ); ?>
			<input type="text" name="fields[<?php echo (int) $si; ?>][<?php echo (int) $fi; ?>][label]" value="<?php echo esc_attr( $field['label'] ?? '' ); ?>" class="large-text" required />
		</label></p>
		<p>
			<label><?php esc_html_e( 'Tipo', 'saulocoelho' ); ?>
				<select name="fields[<?php echo (int) $si; ?>][<?php echo (int) $fi; ?>][type]">
					<?php foreach ( $types as $tk => $tl ) : ?>
						<option value="<?php echo esc_attr( $tk ); ?>" <?php selected( $field['type'] ?? 'text', $tk ); ?>><?php echo esc_html( $tl ); ?></option>
					<?php endforeach; ?>
				</select>
			</label>
			<label><input type="checkbox" name="fields[<?php echo (int) $si; ?>][<?php echo (int) $fi; ?>][required]" value="1" <?php checked( ! empty( $field['required'] ) ); ?> /> <?php esc_html_e( 'Obrigatório', 'saulocoelho' ); ?></label>
			<label><?php esc_html_e( 'Máx. chars', 'saulocoelho' ); ?>
				<input type="number" name="fields[<?php echo (int) $si; ?>][<?php echo (int) $fi; ?>][max_length]" value="<?php echo esc_attr( $field['max_length'] ?? '' ); ?>" style="width:80px;" min="0" />
			</label>
		</p>
		<p><label><?php esc_html_e( 'Opções (chave|rótulo, uma por linha)', 'saulocoelho' ); ?><br>
			<textarea name="fields[<?php echo (int) $si; ?>][<?php echo (int) $fi; ?>][options_text]" rows="3" class="large-text"><?php echo esc_textarea( trim( $opts_text ) ); ?></textarea>
		</label></p>
		<input type="hidden" name="fields[<?php echo (int) $si; ?>][<?php echo (int) $fi; ?>][sort_order]" value="<?php echo (int) $fi; ?>" />
	</div>
	<?php
}

function sc_forms_admin_handle_save() {
	if ( ! sc_forms_admin_can_manage() ) {
		wp_die( esc_html__( 'Sem permissão.', 'saulocoelho' ) );
	}
	check_admin_referer( 'sc_forms_save' );

	$form_id = isset( $_POST['form_id'] ) ? absint( $_POST['form_id'] ) : 0;
	$title   = isset( $_POST['title'] ) ? sanitize_text_field( wp_unslash( $_POST['title'] ) ) : '';
	$desc    = isset( $_POST['description'] ) ? sanitize_textarea_field( wp_unslash( $_POST['description'] ) ) : '';

	if ( $title === '' ) {
		wp_die( esc_html__( 'Título obrigatório.', 'saulocoelho' ) );
	}

	if ( $form_id ) {
		sc_forms_update_form_meta(
			$form_id,
			array(
				'title'       => $title,
				'description' => $desc,
			)
		);
	} else {
		$form_id = sc_forms_insert_form(
			array(
				'title'       => $title,
				'description' => $desc,
				'schema_slug' => sc_forms_generate_slug( $title ),
			)
		);
	}

	if ( ! $form_id ) {
		wp_die( esc_html__( 'Erro ao salvar.', 'saulocoelho' ) );
	}

	$sections_in = isset( $_POST['sections'] ) && is_array( $_POST['sections'] ) ? wp_unslash( $_POST['sections'] ) : array();
	$fields_in   = isset( $_POST['fields'] ) && is_array( $_POST['fields'] ) ? wp_unslash( $_POST['fields'] ) : array();

	$sections = array();
	foreach ( $sections_in as $sec ) {
		if ( empty( $sec['title'] ) ) {
			continue;
		}
		$sections[] = array(
			'section_key' => sanitize_key( $sec['section_key'] ?? '' ),
			'title'       => sanitize_text_field( $sec['title'] ),
			'sort_order'  => isset( $sec['sort_order'] ) ? (int) $sec['sort_order'] : 0,
		);
	}

	$fields = array();
	foreach ( $fields_in as $si => $section_fields ) {
		if ( ! is_array( $section_fields ) || ! isset( $sections_in[ $si ] ) ) {
			continue;
		}
		$section_key = sanitize_key( $sections_in[ $si ]['section_key'] ?? ( 'sec_' . ( (int) $si + 1 ) ) );
		foreach ( $section_fields as $field ) {
			if ( empty( $field['label'] ) ) {
				continue;
			}
			$type = sanitize_key( $field['type'] ?? 'text' );
			if ( ! isset( sc_forms_allowed_field_types()[ $type ] ) ) {
				$type = 'text';
			}

			$options = array();
			if ( ! empty( $field['options_text'] ) ) {
				$lines = preg_split( '/\r\n|\r|\n/', (string) $field['options_text'] );
				foreach ( $lines as $line ) {
					$line = trim( $line );
					if ( $line === '' ) {
						continue;
					}
					$parts = explode( '|', $line, 2 );
					$ok    = sanitize_key( $parts[0] );
					$ol    = isset( $parts[1] ) ? sanitize_text_field( $parts[1] ) : $ok;
					if ( $ok !== '' ) {
						$options[ $ok ] = $ol;
					}
				}
			}

			$max = isset( $field['max_length'] ) && $field['max_length'] !== '' ? absint( $field['max_length'] ) : sc_forms_default_max_length( $type );

			$fields[] = array(
				'key'        => sanitize_key( $field['key'] ?? '' ) ?: 'field_' . wp_generate_password( 6, false ),
				'section'    => $section_key,
				'label'      => sanitize_text_field( $field['label'] ),
				'type'       => $type,
				'required'   => ! empty( $field['required'] ),
				'max_length' => $max,
				'options'    => $options,
				'sort_order' => isset( $field['sort_order'] ) ? (int) $field['sort_order'] : 0,
			);
		}
	}

	$bump = isset( $_POST['form_id'] ) && absint( $_POST['form_id'] ) > 0;
	sc_forms_save_structure( $form_id, $sections, $fields, $bump );

	add_settings_error( 'sc_forms', 'saved', __( 'Formulário salvo.', 'saulocoelho' ), 'updated' );
	set_transient( 'settings_errors', get_settings_errors(), 30 );

	wp_safe_redirect( admin_url( 'admin.php?page=sc-post-order-forms&action=edit&form_id=' . $form_id ) );
	exit;
}

function sc_forms_admin_handle_duplicate() {
	if ( ! sc_forms_admin_can_manage() ) {
		wp_die( esc_html__( 'Sem permissão.', 'saulocoelho' ) );
	}
	$form_id = isset( $_GET['form_id'] ) ? absint( $_GET['form_id'] ) : 0;
	check_admin_referer( 'sc_forms_duplicate_' . $form_id );

	$new_id = sc_forms_duplicate_form( $form_id );
	if ( $new_id ) {
		wp_safe_redirect( admin_url( 'admin.php?page=sc-post-order-forms&action=edit&form_id=' . $new_id ) );
		exit;
	}
	wp_die( esc_html__( 'Erro ao duplicar.', 'saulocoelho' ) );
}

function sc_forms_admin_handle_archive() {
	if ( ! sc_forms_admin_can_manage() ) {
		wp_die( esc_html__( 'Sem permissão.', 'saulocoelho' ) );
	}
	$form_id = isset( $_GET['form_id'] ) ? absint( $_GET['form_id'] ) : 0;
	check_admin_referer( 'sc_forms_archive_' . $form_id );

	sc_forms_update_form_meta( $form_id, array( 'status' => 'archived' ) );
	wp_safe_redirect( admin_url( 'admin.php?page=sc-post-order-forms' ) );
	exit;
}
