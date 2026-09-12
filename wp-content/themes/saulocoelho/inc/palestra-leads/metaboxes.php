<?php
/**
 * Metaboxes do CPT Palestra.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function sc_palestra_add_metaboxes() {
	add_meta_box(
		'sc_palestra_content',
		__( 'Conteúdo da página', 'saulocoelho' ),
		'sc_palestra_render_content_metabox',
		SC_PALESTRA_CPT,
		'normal',
		'high'
	);
	add_meta_box(
		'sc_palestra_fields',
		__( 'Campos do formulário', 'saulocoelho' ),
		'sc_palestra_render_fields_metabox',
		SC_PALESTRA_CPT,
		'normal',
		'default'
	);
	add_meta_box(
		'sc_palestra_files',
		__( 'Ficheiros (download após cadastro)', 'saulocoelho' ),
		'sc_palestra_render_files_metabox',
		SC_PALESTRA_CPT,
		'normal',
		'default'
	);
	add_meta_box(
		'sc_palestra_side',
		__( 'Link e QR', 'saulocoelho' ),
		'sc_palestra_render_side_metabox',
		SC_PALESTRA_CPT,
		'side',
		'high'
	);
}

function sc_palestra_admin_assets( $hook ) {
	if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
		return;
	}
	$screen = get_current_screen();
	if ( ! $screen || $screen->post_type !== SC_PALESTRA_CPT ) {
		return;
	}
	wp_enqueue_media();
	$rel  = '/assets/js/palestra-admin.js';
	$path = get_template_directory() . $rel;
	wp_enqueue_script(
		'sc-palestra-admin',
		get_template_directory_uri() . $rel,
		array( 'jquery' ),
		file_exists( $path ) ? (string) filemtime( $path ) : wp_get_theme()->get( 'Version' ),
		true
	);
}

function sc_palestra_field( $name, $value, $type = 'text', $attrs = '' ) {
	printf(
		'<input type="%s" class="widefat" name="sc_palestra[%s]" value="%s" %s />',
		esc_attr( $type ),
		esc_attr( $name ),
		esc_attr( (string) $value ),
		$attrs // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static attrs from PHP.
	);
}

function sc_palestra_render_content_metabox( $post ) {
	wp_nonce_field( 'sc_palestra_save', 'sc_palestra_nonce' );
	$c = sc_palestra_event_config( $post->ID );
	?>
	<p><label><strong><?php esc_html_e( 'Linha acima do título (ex.: cidade)', 'saulocoelho' ); ?></strong></label>
		<?php sc_palestra_field( 'eyebrow', $c['eyebrow'] ); ?></p>
	<p><label><strong><?php esc_html_e( 'Data', 'saulocoelho' ); ?></strong></label>
		<?php sc_palestra_field( 'date', $c['date'], 'date' ); ?></p>
	<p><label><strong><?php esc_html_e( 'Local', 'saulocoelho' ); ?></strong></label>
		<?php sc_palestra_field( 'location', $c['location'] ); ?></p>
	<p><label><strong><?php esc_html_e( 'Texto de apoio', 'saulocoelho' ); ?></strong></label>
		<textarea class="widefat" rows="4" name="sc_palestra[support]"><?php echo esc_textarea( $c['support'] ); ?></textarea></p>
	<p><label><strong><?php esc_html_e( 'Texto do botão', 'saulocoelho' ); ?></strong></label>
		<?php sc_palestra_field( 'cta', $c['cta'] ); ?></p>
	<p><label><strong><?php esc_html_e( 'Título após o cadastro', 'saulocoelho' ); ?></strong></label>
		<?php sc_palestra_field( 'thanks_title', $c['thanks_title'] ); ?></p>
	<p><label><strong><?php esc_html_e( 'Nota após o cadastro', 'saulocoelho' ); ?></strong></label>
		<textarea class="widefat" rows="3" name="sc_palestra[thanks_note]"><?php echo esc_textarea( $c['thanks_note'] ); ?></textarea></p>
	<p><label><strong><?php esc_html_e( 'Texto de consentimento (LGPD)', 'saulocoelho' ); ?></strong></label>
		<textarea class="widefat" rows="3" name="sc_palestra[consent]"><?php echo esc_textarea( $c['consent'] ); ?></textarea></p>
	<p><label><strong><?php esc_html_e( 'Introdução do e-mail', 'saulocoelho' ); ?></strong></label>
		<textarea class="widefat" rows="3" name="sc_palestra[email_intro]"><?php echo esc_textarea( $c['email_intro'] ); ?></textarea></p>
	<p>
		<label><strong><?php esc_html_e( 'Imagem de fundo (hero)', 'saulocoelho' ); ?></strong></label><br />
		<input type="hidden" id="sc-palestra-bg-id" name="sc_palestra[bg_id]" value="<?php echo (int) $c['bg_id']; ?>" />
		<button type="button" class="button" id="sc-palestra-bg-pick"><?php esc_html_e( 'Escolher imagem', 'saulocoelho' ); ?></button>
		<button type="button" class="button" id="sc-palestra-bg-clear"><?php esc_html_e( 'Remover', 'saulocoelho' ); ?></button>
		<span id="sc-palestra-bg-preview" style="display:block;margin-top:8px;">
			<?php if ( $c['bg_url'] ) : ?>
				<img src="<?php echo esc_url( $c['bg_url'] ); ?>" alt="" style="max-width:100%;height:auto;" />
			<?php endif; ?>
		</span>
	</p>
	<?php
}

function sc_palestra_render_fields_metabox( $post ) {
	$c      = sc_palestra_event_config( $post->ID );
	$extras = $c['extras'];
	?>
	<p><?php esc_html_e( 'Nome, e-mail e consentimento são sempre obrigatórios. WhatsApp e campos extra são opcionais por palestra.', 'saulocoelho' ); ?></p>
	<p>
		<label>
			<input type="checkbox" name="sc_palestra[ask_whatsapp]" value="1" <?php checked( $c['ask_whatsapp'], '1' ); ?> />
			<?php esc_html_e( 'Pedir WhatsApp', 'saulocoelho' ); ?>
		</label>
	</p>
	<table class="widefat striped" id="sc-palestra-extras">
		<thead>
			<tr>
				<th><?php esc_html_e( 'Rótulo', 'saulocoelho' ); ?></th>
				<th><?php esc_html_e( 'Chave', 'saulocoelho' ); ?></th>
				<th><?php esc_html_e( 'Tipo', 'saulocoelho' ); ?></th>
				<th><?php esc_html_e( 'Obrigatório', 'saulocoelho' ); ?></th>
				<th></th>
			</tr>
		</thead>
		<tbody>
		<?php
		$extras[] = array( 'label' => '', 'key' => '', 'type' => 'text', 'required' => '' );
		foreach ( $extras as $i => $row ) :
			?>
			<tr>
				<td><input type="text" name="sc_palestra_extras[<?php echo (int) $i; ?>][label]" value="<?php echo esc_attr( $row['label'] ?? '' ); ?>" class="widefat" /></td>
				<td><input type="text" name="sc_palestra_extras[<?php echo (int) $i; ?>][key]" value="<?php echo esc_attr( $row['key'] ?? '' ); ?>" class="widefat" placeholder="empresa" /></td>
				<td>
					<select name="sc_palestra_extras[<?php echo (int) $i; ?>][type]">
						<option value="text" <?php selected( ( $row['type'] ?? '' ), 'text' ); ?>><?php esc_html_e( 'Texto', 'saulocoelho' ); ?></option>
						<option value="email" <?php selected( ( $row['type'] ?? '' ), 'email' ); ?>><?php esc_html_e( 'E-mail', 'saulocoelho' ); ?></option>
						<option value="tel" <?php selected( ( $row['type'] ?? '' ), 'tel' ); ?>><?php esc_html_e( 'Telefone', 'saulocoelho' ); ?></option>
					</select>
				</td>
				<td style="text-align:center;"><input type="checkbox" name="sc_palestra_extras[<?php echo (int) $i; ?>][required]" value="1" <?php checked( ! empty( $row['required'] ) ); ?> /></td>
				<td><button type="button" class="button sc-palestra-remove-row">&times;</button></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	<p><button type="button" class="button" id="sc-palestra-add-extra"><?php esc_html_e( 'Adicionar campo', 'saulocoelho' ); ?></button></p>
	<?php
}

function sc_palestra_render_files_metabox( $post ) {
	$c = sc_palestra_event_config( $post->ID );
	?>
	<p><?php esc_html_e( 'Os ficheiros ficam numa pasta protegida (não entram na Biblioteca de Média). PDF, ZIP, PPT/PPTX, PNG e JPG.', 'saulocoelho' ); ?></p>
	<?php if ( ! empty( $c['files'] ) ) : ?>
		<ul>
			<?php foreach ( $c['files'] as $file ) : ?>
				<li>
					<label>
						<input type="checkbox" name="sc_palestra_delete_files[]" value="<?php echo esc_attr( $file['id'] ); ?>" />
						<?php esc_html_e( 'Remover', 'saulocoelho' ); ?>
					</label>
					— <?php echo esc_html( $file['name'] ); ?>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php else : ?>
		<p><em><?php esc_html_e( 'Nenhum ficheiro ainda.', 'saulocoelho' ); ?></em></p>
	<?php endif; ?>
	<p><input type="file" name="sc_palestra_new_files[]" multiple="multiple" /></p>
	<?php
}

function sc_palestra_render_side_metabox( $post ) {
	$c   = sc_palestra_event_config( $post->ID );
	$url = sc_palestra_event_public_url( $post->ID );
	$qr  = 'https://api.qrserver.com/v1/create-qr-code/?size=400x400&ecc=M&format=png&margin=12&data=' . rawurlencode( $url );
	?>
	<p>
		<label>
			<input type="checkbox" name="sc_palestra[is_canonical]" value="1" <?php checked( (int) $post->ID === sc_palestra_canonical_id() || $c['is_canonical'] === '1' ); ?> />
			<?php esc_html_e( 'Página principal (saulocoelho.com/palestra/)', 'saulocoelho' ); ?>
		</label>
	</p>
	<p><strong><?php esc_html_e( 'URL:', 'saulocoelho' ); ?></strong><br />
		<?php if ( $post->post_status === 'publish' ) : ?>
			<a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $url ); ?></a>
		<?php else : ?>
			<em><?php esc_html_e( 'Publique para gerar o link.', 'saulocoelho' ); ?></em>
		<?php endif; ?>
	</p>
	<?php if ( $post->post_status === 'publish' ) : ?>
		<p><img src="<?php echo esc_url( $qr ); ?>" alt="" width="180" height="180" style="background:#fff;padding:6px;border:1px solid #dcdcde;" /></p>
		<p><a class="button" href="<?php echo esc_url( $qr ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'QR em alta', 'saulocoelho' ); ?></a></p>
	<?php endif; ?>
	<p><a href="<?php echo esc_url( admin_url( 'edit.php?post_type=' . SC_PALESTRA_CPT . '&page=sc-palestra-leads&event_id=' . (int) $post->ID ) ); ?>"><?php esc_html_e( 'Ver leads desta palestra', 'saulocoelho' ); ?></a></p>
	<?php
}

function sc_palestra_save_metaboxes( $post_id, $post ) {
	if ( ! $post || $post->post_type !== SC_PALESTRA_CPT ) {
		return;
	}
	if ( ! isset( $_POST['sc_palestra_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['sc_palestra_nonce'] ) ), 'sc_palestra_save' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$raw = isset( $_POST['sc_palestra'] ) && is_array( $_POST['sc_palestra'] ) ? wp_unslash( $_POST['sc_palestra'] ) : array();
	$defaults = sc_palestra_default_meta();

	foreach ( $defaults as $key => $default ) {
		if ( $key === 'ask_whatsapp' || $key === 'is_canonical' ) {
			continue;
		}
		if ( $key === 'bg_id' ) {
			update_post_meta( $post_id, '_sc_palestra_bg_id', isset( $raw['bg_id'] ) ? absint( $raw['bg_id'] ) : 0 );
			continue;
		}
		$value = isset( $raw[ $key ] ) ? sanitize_textarea_field( $raw[ $key ] ) : $default;
		if ( in_array( $key, array( 'eyebrow', 'date', 'location', 'cta', 'thanks_title' ), true ) ) {
			$value = sanitize_text_field( $value );
		}
		update_post_meta( $post_id, '_sc_palestra_' . $key, $value );
	}

	update_post_meta( $post_id, '_sc_palestra_ask_whatsapp', empty( $raw['ask_whatsapp'] ) ? '' : '1' );

	$canonical = ! empty( $raw['is_canonical'] );
	update_post_meta( $post_id, '_sc_palestra_is_canonical', $canonical ? '1' : '' );
	if ( $canonical ) {
		update_option( 'sc_palestra_canonical_id', (int) $post_id );
		$others = get_posts(
			array(
				'post_type'      => SC_PALESTRA_CPT,
				'post_status'    => 'any',
				'posts_per_page' => -1,
				'fields'         => 'ids',
				'post__not_in'   => array( $post_id ),
			)
		);
		foreach ( $others as $other_id ) {
			delete_post_meta( $other_id, '_sc_palestra_is_canonical' );
		}
	} elseif ( sc_palestra_canonical_id() === (int) $post_id ) {
		delete_option( 'sc_palestra_canonical_id' );
	}

	$extras_raw = isset( $_POST['sc_palestra_extras'] ) && is_array( $_POST['sc_palestra_extras'] ) ? wp_unslash( $_POST['sc_palestra_extras'] ) : array();
	update_post_meta( $post_id, '_sc_palestra_extras', sc_palestra_sanitize_extras( $extras_raw ) );

	$files = get_post_meta( $post_id, '_sc_palestra_files', true );
	$files = is_array( $files ) ? $files : array();
	$delete = isset( $_POST['sc_palestra_delete_files'] ) ? array_map( 'sanitize_text_field', wp_unslash( (array) $_POST['sc_palestra_delete_files'] ) ) : array();
	if ( $delete ) {
		$kept = array();
		foreach ( $files as $file ) {
			if ( in_array( $file['id'], $delete, true ) ) {
				$path = sc_palestra_file_path( $post_id, $file['id'] );
				if ( $path && is_file( $path ) ) {
					wp_delete_file( $path );
				}
				continue;
			}
			$kept[] = $file;
		}
		$files = $kept;
	}

	if ( ! empty( $_FILES['sc_palestra_new_files'] ) && is_array( $_FILES['sc_palestra_new_files']['name'] ) ) {
		$bucket = $_FILES['sc_palestra_new_files'];
		$count  = count( $bucket['name'] );
		for ( $i = 0; $i < $count; $i++ ) {
			if ( empty( $bucket['name'][ $i ] ) || (int) $bucket['error'][ $i ] !== UPLOAD_ERR_OK ) {
				continue;
			}
			$one = array(
				'name'     => $bucket['name'][ $i ],
				'type'     => $bucket['type'][ $i ],
				'tmp_name' => $bucket['tmp_name'][ $i ],
				'error'    => $bucket['error'][ $i ],
				'size'     => $bucket['size'][ $i ],
			);
			$ext = strtolower( pathinfo( $one['name'], PATHINFO_EXTENSION ) );
			if ( ! isset( sc_palestra_allowed_mimes()[ $ext ] ) ) {
				continue;
			}
			$dir    = sc_palestra_ensure_private_dir( $post_id );
			$stored = wp_unique_filename( $dir, sanitize_file_name( $one['name'] ) );
			$dest   = trailingslashit( $dir ) . $stored;
			if ( move_uploaded_file( $one['tmp_name'], $dest ) ) {
				$files[] = array(
					'id'   => $stored,
					'name' => sanitize_file_name( $one['name'] ),
					'mime' => sc_palestra_allowed_mimes()[ $ext ],
				);
			}
		}
	}

	update_post_meta( $post_id, '_sc_palestra_files', $files );
}
