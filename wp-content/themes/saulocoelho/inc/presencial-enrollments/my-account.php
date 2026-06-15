<?php
/**
 * Endpoint Minha Conta — questionário pós-inscrição.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', 'sc_presencial_register_account_endpoint' );

function sc_presencial_register_account_endpoint() {
	add_rewrite_endpoint( 'questionario-presencial', EP_ROOT | EP_PAGES );
}

add_action( 'init', 'sc_presencial_maybe_flush_rewrites', 99 );

function sc_presencial_maybe_flush_rewrites() {
	if ( get_option( 'sc_presencial_rewrite_flushed' ) ) {
		return;
	}
	flush_rewrite_rules( false );
	update_option( 'sc_presencial_rewrite_flushed', 1 );
}

add_filter( 'woocommerce_get_query_vars', 'sc_presencial_account_query_vars' );

function sc_presencial_account_query_vars( $vars ) {
	$vars['questionario-presencial'] = 'questionario-presencial';
	return $vars;
}

add_filter( 'woocommerce_account_menu_items', 'sc_presencial_account_menu_items' );

function sc_presencial_account_menu_items( $items ) {
	$user_id = get_current_user_id();
	if ( ! $user_id ) {
		return $items;
	}

	if ( ! sc_presencial_user_has_questionnaire_access( $user_id ) ) {
		return $items;
	}

	$new = array();
	foreach ( $items as $key => $label ) {
		$new[ $key ] = $label;
		if ( $key === 'dashboard' ) {
			$new['questionario-presencial'] = __( 'Questionário de inscrição', 'saulocoelho' );
		}
	}
	if ( ! isset( $new['questionario-presencial'] ) ) {
		$new['questionario-presencial'] = __( 'Questionário de inscrição', 'saulocoelho' );
	}
	return $new;
}

add_action( 'woocommerce_account_questionario-presencial_endpoint', 'sc_presencial_render_account_endpoint' );

function sc_presencial_render_account_endpoint() {
	$user_id = get_current_user_id();
	if ( ! $user_id ) {
		echo '<p>' . esc_html__( 'Faça login para acessar o questionário.', 'saulocoelho' ) . '</p>';
		return;
	}

	$enrollment_id = isset( $_GET['inscricao'] ) ? absint( $_GET['inscricao'] ) : 0;
	$enrollment    = $enrollment_id ? sc_presencial_get_enrollment( $enrollment_id ) : null;

	if ( $enrollment && (int) $enrollment->user_id !== $user_id ) {
		$enrollment = null;
	}

	if ( ! $enrollment ) {
		sc_presencial_render_enrollment_list( $user_id );
		return;
	}

	if ( ! sc_presencial_enrollment_has_questionnaire( $enrollment ) ) {
		echo '<p>' . esc_html__( 'Este produto não possui questionário.', 'saulocoelho' ) . '</p>';
		return;
	}

	$editing = isset( $_GET['edit'] ) && $_GET['edit'] === '1';

	if ( $enrollment->form_status === 'complete' && ! $editing ) {
		echo '<div class="woocommerce-message">' . esc_html__( 'Questionário já enviado. Você pode editar suas respostas se precisar atualizar.', 'saulocoelho' ) . '</div>';
		echo '<p><a class="button" href="' . esc_url( add_query_arg( array( 'inscricao' => (int) $enrollment->id, 'edit' => '1' ), wc_get_account_endpoint_url( 'questionario-presencial' ) ) ) . '">' . esc_html__( 'Editar respostas', 'saulocoelho' ) . '</a></p>';
		echo '<p><a class="button" href="' . esc_url( wc_get_account_endpoint_url( 'questionario-presencial' ) ) . '">' . esc_html__( 'Voltar', 'saulocoelho' ) . '</a></p>';
		return;
	}

	sc_presencial_handle_form_post( $enrollment );
	sc_presencial_render_form( $enrollment );
}

function sc_presencial_render_enrollment_list( $user_id ) {
	if ( isset( $_GET['sent'] ) && $_GET['sent'] === '1' ) {
		echo '<div class="woocommerce-message mb-6">' . esc_html__( 'Questionário enviado com sucesso. Obrigado!', 'saulocoelho' ) . '</div>';
	}

	$rows = sc_presencial_get_user_questionnaire_enrollments( $user_id );

	echo '<h2 class="text-xl font-bold text-slate-900 dark:text-white mb-4">' . esc_html__( 'Questionários de inscrição', 'saulocoelho' ) . '</h2>';

	if ( empty( $rows ) ) {
		echo '<p class="text-slate-500">' . esc_html__( 'Nenhum questionário no momento.', 'saulocoelho' ) . '</p>';
		return;
	}

	echo '<p class="text-sm text-slate-500 mb-4">' . esc_html__( 'Um questionário por formação — mesmo que você tenha mais de um pedido, responda ou edite uma única vez.', 'saulocoelho' ) . '</p>';

	echo '<ul class="space-y-4">';
	foreach ( $rows as $row ) {
		$title = get_the_title( (int) $row->product_id );
		$url   = add_query_arg( 'inscricao', (int) $row->id, wc_get_account_endpoint_url( 'questionario-presencial' ) );
		if ( $row->form_status === 'complete' ) {
			$url = add_query_arg( 'edit', '1', $url );
		}
		$btn_label = $row->form_status === 'pending' ? __( 'Preencher', 'saulocoelho' ) : __( 'Editar', 'saulocoelho' );
		echo '<li class="p-4 border border-slate-200 dark:border-white/10 rounded-xl flex flex-wrap items-center justify-between gap-3">';
		echo '<div><strong class="text-slate-900 dark:text-white">' . esc_html( $title ) . '</strong>';
		echo '<p class="text-sm text-slate-500 m-0">' . esc_html( sc_presencial_form_status_label( $row->form_status ) ) . '</p></div>';
		echo '<a class="inline-block rounded-lg bg-[#C5A059] px-4 py-2 text-sm font-bold text-white" href="' . esc_url( $url ) . '">' . esc_html( $btn_label ) . '</a>';
		echo '</li>';
	}
	echo '</ul>';
}

function sc_presencial_handle_form_post( $enrollment ) {
	if ( ( $_SERVER['REQUEST_METHOD'] ?? '' ) !== 'POST' ) {
		return;
	}

	if ( ! isset( $_POST['sc_presencial_form_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['sc_presencial_form_nonce'] ) ), 'sc_presencial_save_' . $enrollment->id ) ) {
		wc_add_notice( __( 'Sessão expirada. Tente novamente.', 'saulocoelho' ), 'error' );
		return;
	}

	$schema = sc_forms_get_schema_for_enrollment( $enrollment );
	$result = sc_presencial_validate_form_submission( sc_presencial_get_post_input(), $schema );
	if ( ! empty( $result['errors'] ) ) {
		foreach ( $result['errors'] as $msg ) {
			wc_add_notice( $msg, 'error' );
		}
		return;
	}

	if ( sc_presencial_save_responses( (int) $enrollment->id, $result['responses'], $schema ) ) {
		do_action( 'sc_presencial_form_completed', (int) $enrollment->id, $result['responses'] );
		wp_safe_redirect(
			add_query_arg(
				array(
					'sent' => '1',
				),
				wc_get_account_endpoint_url( 'questionario-presencial' )
			)
		);
		exit;
	}

	wc_add_notice( __( 'Não foi possível salvar. Tente novamente.', 'saulocoelho' ), 'error' );
}

function sc_presencial_render_form( $enrollment ) {
	$schema     = sc_forms_get_schema_for_enrollment( $enrollment );
	$order      = wc_get_order( (int) $enrollment->order_id );
	$defaults   = sc_presencial_default_field_values( (int) $enrollment->user_id, $order );
	$product    = get_the_title( (int) $enrollment->product_id );
	$post_input = sc_presencial_get_post_input();

	if ( $enrollment->form_status === 'complete' && ! empty( $enrollment->responses_json ) ) {
		$saved = json_decode( (string) $enrollment->responses_json, true );
		if ( is_array( $saved ) ) {
			$defaults = array_merge( $defaults, $saved );
		}
	}

	$description = ! empty( $schema['meta']['description'] ) ? $schema['meta']['description'] : __( 'Responda com sinceridade. Suas respostas nos ajudam a personalizar sua experiência na formação.', 'saulocoelho' );

	$by_section = array();
	foreach ( $schema['fields'] as $field ) {
		$by_section[ $field['section'] ][] = $field;
	}

	wc_print_notices();
	$is_edit = $enrollment->form_status === 'complete';
	?>
	<div class="sc-presencial-form-wrap max-w-3xl">
		<h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-2"><?php echo esc_html( $product ); ?></h2>
		<p class="text-slate-500 mb-6"><?php echo esc_html( $description ); ?></p>
		<?php if ( $is_edit ) : ?>
			<p class="text-sm text-amber-600 mb-4"><?php esc_html_e( 'Você está editando suas respostas anteriores.', 'saulocoelho' ); ?></p>
		<?php endif; ?>

		<form method="post" class="sc-presencial-form space-y-10">
			<?php wp_nonce_field( 'sc_presencial_save_' . $enrollment->id, 'sc_presencial_form_nonce' ); ?>

			<?php foreach ( $schema['sections'] as $section ) :
				$sid    = $section['id'];
				$fields = $by_section[ $sid ] ?? array();
				if ( empty( $fields ) ) {
					continue;
				}
				?>
				<fieldset class="border border-slate-200 dark:border-white/10 rounded-xl p-6">
					<legend class="text-lg font-bold text-[#C5A059] px-2"><?php echo esc_html( $section['title'] ); ?></legend>
					<div class="space-y-5 mt-4">
						<?php foreach ( $fields as $field ) :
							sc_presencial_render_field( $field, $defaults, $post_input );
						endforeach; ?>
					</div>
				</fieldset>
			<?php endforeach; ?>

			<button type="submit" class="rounded-lg bg-[#C5A059] px-8 py-3 text-sm font-bold text-white border-0 cursor-pointer">
				<?php echo $is_edit ? esc_html__( 'Salvar alterações', 'saulocoelho' ) : esc_html__( 'Enviar questionário', 'saulocoelho' ); ?>
			</button>
		</form>
	</div>
	<script>
	(function(){
		var form = document.querySelector('.sc-presencial-form');
		if (!form) return;
		function toggleConditionals(){
			form.querySelectorAll('[data-show-if-field]').forEach(function(wrap){
				var field = wrap.getAttribute('data-show-if-field');
				var val = wrap.getAttribute('data-show-if-value');
				var type = wrap.getAttribute('data-show-if-type') || 'equals';
				var current = '';
				var inputs = form.querySelectorAll('[name="'+field+'"], [name="'+field+'[]"]');
				if (type === 'includes') {
					var checked = [];
					inputs.forEach(function(i){ if (i.checked) checked.push(i.value); });
					wrap.style.display = checked.indexOf(val) !== -1 ? '' : 'none';
					return;
				}
				inputs.forEach(function(i){
					if (i.type === 'radio' || i.type === 'checkbox') {
						if (i.checked) current = i.value;
					} else {
						current = i.value;
					}
				});
				wrap.style.display = (current === val) ? '' : 'none';
			});
		}
		form.addEventListener('change', toggleConditionals);
		form.addEventListener('input', toggleConditionals);
		toggleConditionals();
	})();
	</script>
	<?php
}

/**
 * @param array<string, mixed> $field
 * @param array<string, mixed> $defaults
 * @param array<string, mixed> $post
 */
function sc_presencial_render_field( array $field, array $defaults, array $post ) {
	$key   = $field['key'];
	$type  = $field['type'];
	$label = $field['label'];
	$req   = ! empty( $field['required'] );

	$value = $post[ $key ] ?? ( $defaults[ $key ] ?? '' );
	if ( $type === 'multiselect' && isset( $post[ $key ] ) && is_array( $post[ $key ] ) ) {
		$value = $post[ $key ];
	}

	$wrap_attrs = '';
	if ( ! empty( $field['show_if'] ) ) {
		$si         = $field['show_if'];
		$wrap_attrs = sprintf(
			' data-show-if-field="%s" data-show-if-value="%s" data-show-if-type="%s" style="display:none;"',
			esc_attr( $si['field'] ),
			esc_attr( $si['value'] ),
			esc_attr( $si['type'] ?? 'equals' )
		);
	}

	$max_attr = '';
	if ( ! empty( $field['max_length'] ) && in_array( $type, array( 'text', 'tel', 'textarea' ), true ) ) {
		$max_attr = ' maxlength="' . (int) $field['max_length'] . '"';
	}

	echo '<div class="sc-field"' . $wrap_attrs . '>';
	echo '<label class="block text-sm font-semibold text-slate-800 dark:text-slate-200 mb-2" for="sc_' . esc_attr( $key ) . '">';
	echo esc_html( $label );
	if ( $req ) {
		echo ' <span class="text-red-500">*</span>';
	}
	echo '</label>';

	if ( $type === 'textarea' ) {
		printf(
			'<textarea class="w-full rounded-lg border border-slate-300 dark:border-white/10 bg-white dark:bg-slate-900 p-3" id="sc_%1$s" name="%1$s" rows="4"%2$s%3$s>%4$s</textarea>',
			esc_attr( $key ),
			$req ? ' required' : '',
			$max_attr,
			esc_textarea( is_string( $value ) ? $value : '' )
		);
	} elseif ( $type === 'select' ) {
		echo '<select class="w-full rounded-lg border border-slate-300 dark:border-white/10 bg-white dark:bg-slate-900 p-3" id="sc_' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '"' . ( $req ? ' required' : '' ) . '>';
		echo '<option value="">' . esc_html__( 'Selecione…', 'saulocoelho' ) . '</option>';
		foreach ( $field['options'] as $opt_key => $opt_label ) {
			printf(
				'<option value="%s" %s>%s</option>',
				esc_attr( $opt_key ),
				selected( (string) $value, (string) $opt_key, false ),
				esc_html( $opt_label )
			);
		}
		echo '</select>';
	} elseif ( $type === 'multiselect' ) {
		$selected = is_array( $value ) ? $value : array();
		echo '<div class="space-y-2">';
		foreach ( $field['options'] as $opt_key => $opt_label ) {
			$id = 'sc_' . $key . '_' . $opt_key;
			printf(
				'<label class="flex items-center gap-2 text-sm" for="%1$s"><input type="checkbox" id="%1$s" name="%2$s[]" value="%3$s" %4$s /> %5$s</label>',
				esc_attr( $id ),
				esc_attr( $key ),
				esc_attr( $opt_key ),
				checked( in_array( (string) $opt_key, array_map( 'strval', $selected ), true ), true, false ),
				esc_html( $opt_label )
			);
		}
		echo '</div>';
	} else {
		$input_type = $type === 'date' ? 'date' : ( $type === 'tel' ? 'tel' : 'text' );
		printf(
			'<input class="w-full rounded-lg border border-slate-300 dark:border-white/10 bg-white dark:bg-slate-900 p-3" type="%1$s" id="sc_%2$s" name="%2$s" value="%3$s"%4$s%5$s />',
			esc_attr( $input_type ),
			esc_attr( $key ),
			esc_attr( is_string( $value ) ? $value : '' ),
			$req ? ' required' : '',
			$max_attr
		);
	}

	echo '</div>';
}

add_action( 'woocommerce_account_dashboard', 'sc_presencial_dashboard_pending_notice', 5 );

function sc_presencial_dashboard_pending_notice() {
	if ( ! is_user_logged_in() ) {
		return;
	}

	$pending = array_filter(
		sc_presencial_get_user_questionnaire_enrollments( get_current_user_id() ),
		static function ( $row ) {
			return $row->form_status === 'pending';
		}
	);

	if ( empty( $pending ) ) {
		return;
	}

	if ( isset( $_GET['sent'] ) && $_GET['sent'] === '1' ) {
		echo '<div class="woocommerce-message mb-6">' . esc_html__( 'Questionário enviado com sucesso. Obrigado!', 'saulocoelho' ) . '</div>';
		return;
	}

	$url = wc_get_account_endpoint_url( 'questionario-presencial' );
	?>
	<div class="mb-8 p-5 rounded-xl border border-amber-500/30 bg-amber-500/10">
		<p class="m-0 font-semibold text-slate-900 dark:text-white"><?php esc_html_e( 'Você tem questionário(s) de inscrição pendente(s).', 'saulocoelho' ); ?></p>
		<p class="mt-2 mb-3 text-sm text-slate-600 dark:text-slate-300"><?php esc_html_e( 'Complete quando puder — sua inscrição já está confirmada.', 'saulocoelho' ); ?></p>
		<a href="<?php echo esc_url( $url ); ?>" class="inline-block rounded-lg bg-[#C5A059] px-4 py-2 text-sm font-bold text-white"><?php esc_html_e( 'Preencher questionário', 'saulocoelho' ); ?></a>
	</div>
	<?php
}

add_action( 'after_switch_theme', 'sc_presencial_flush_rewrites' );

function sc_presencial_flush_rewrites() {
	sc_presencial_register_account_endpoint();
	flush_rewrite_rules();
}
