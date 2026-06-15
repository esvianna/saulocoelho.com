<?php
/**
 * Template de Sucesso (Obrigado) — /finalizado
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$order_id   = isset( $_GET['order_id'] ) ? absint( $_GET['order_id'] ) : 0;
$order      = $order_id ? wc_get_order( $order_id ) : null;
$first_name = $order ? $order->get_billing_first_name() : __( 'Aluno', 'saulocoelho' );

$pending_questionnaires = array();
$questionnaire_url      = '';

if ( $order && function_exists( 'sc_presencial_sync_order_enrollments' ) ) {
	sc_presencial_sync_order_enrollments( $order_id, array(), $order );

	foreach ( $order->get_items() as $item ) {
		$product_id = (int) $item->get_product_id();
		if ( ! sc_forms_product_has_form( $product_id ) && ! sc_presencial_is_presencial_product( $product_id ) ) {
			continue;
		}
		$row = sc_presencial_get_enrollment_by_order_product( $order_id, $product_id );
		if ( $row && $row->form_status === 'pending' && sc_presencial_enrollment_has_questionnaire( $row ) ) {
			$pending_questionnaires[] = $row;
		}
	}
}

if ( count( $pending_questionnaires ) === 1 ) {
	$questionnaire_url = add_query_arg(
		'inscricao',
		(int) $pending_questionnaires[0]->id,
		wc_get_account_endpoint_url( 'questionario-presencial' )
	);
} elseif ( count( $pending_questionnaires ) > 1 ) {
	$questionnaire_url = wc_get_account_endpoint_url( 'questionario-presencial' );
}

$has_full_questionnaire = ! empty( $pending_questionnaires );
$course_url             = esc_url( home_url( '/minha-conta/minhas-turmas/' ) );
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="dark">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
	<script src="https://cdn.tailwindcss.com"></script>
	<script>
		tailwind.config = {
			darkMode: 'class',
			theme: {
				extend: {
					colors: {
						primary: '#C5A059',
						'primary-dark': '#A6894A',
						dark: '#050A14',
					}
				}
			}
		}
	</script>
	<style>
		body { background-color: #050A14; color: white; -webkit-font-smoothing: antialiased; }
		.glass-card { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 24px; }
		.btn-primary { background: #C5A059; color: white; border-radius: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em; transition: all 0.3s; display: inline-flex; align-items: center; justify-content: center; gap: 0.75rem; text-decoration: none; }
		.btn-primary:hover { background: #A6894A; transform: translateY(-2px); box-shadow: 0 10px 20px -5px rgba(197, 160, 89, 0.4); color: white; }
		.btn-secondary { background: transparent; color: rgba(255,255,255,0.7); border: 1px solid rgba(255,255,255,0.15); border-radius: 12px; font-weight: 600; text-decoration: none; display: inline-block; }
		.btn-secondary:hover { border-color: #C5A059; color: #C5A059; }
		.survey-item { background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 12px; transition: all 0.2s; cursor: pointer; text-align: center; }
		.survey-item:hover { background: rgba(255, 255, 255, 0.1); border-color: #C5A059; }
		.survey-item.active { background: #C5A059; border-color: #C5A059; color: white; }
	</style>
</head>
<body class="min-h-screen flex items-center justify-center p-6 relative">
	<div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-primary/20 blur-[120px] rounded-full"></div>
	<div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-primary/10 blur-[120px] rounded-full"></div>

	<div class="w-full max-w-2xl z-10 text-center">
		<div class="mb-8 flex justify-center">
			<div class="w-24 h-24 bg-green-500/20 text-green-400 rounded-full flex items-center justify-center border-2 border-green-500/30 scale-110 animate-bounce">
				<span class="material-symbols-outlined text-5xl">check_circle</span>
			</div>
		</div>

		<h1 class="text-4xl font-black mb-4"><?php printf( esc_html__( 'Parabéns, %s!', 'saulocoelho' ), esc_html( $first_name ) ); ?></h1>
		<p class="text-white/60 text-lg mb-10 max-w-md mx-auto"><?php esc_html_e( 'Sua vaga está garantida. Estamos muito felizes em ter você conosco nesta jornada!', 'saulocoelho' ); ?></p>

		<div class="glass-card p-10 text-left">
			<?php if ( $has_full_questionnaire ) : ?>
				<h2 class="text-xl font-bold mb-4 flex items-center gap-2">
					<span class="material-symbols-outlined text-primary">assignment</span>
					<?php esc_html_e( 'Próximo passo: questionário de inscrição', 'saulocoelho' ); ?>
				</h2>
				<p class="text-white/70 mb-4 leading-relaxed">
					<?php esc_html_e( 'Para personalizarmos sua experiência, preencha o questionário completo de inscrição — os mesmos tópicos mapeados do formulário da formação (dados pessoais, momento atual, sobre você e objetivos).', 'saulocoelho' ); ?>
				</p>
				<p class="text-white/50 text-sm mb-8">
					<?php esc_html_e( 'São várias perguntas organizadas em seções. Você pode salvar e voltar depois em Minha Conta.', 'saulocoelho' ); ?>
				</p>
				<div class="flex flex-col sm:flex-row gap-4">
					<a href="<?php echo esc_url( $questionnaire_url ); ?>" class="btn-primary w-full sm:flex-1 p-5">
						<?php esc_html_e( 'Preencher questionário agora', 'saulocoelho' ); ?>
						<span class="material-symbols-outlined">edit_note</span>
					</a>
					<a href="<?php echo esc_url( $course_url ); ?>" class="btn-secondary w-full sm:w-auto p-5 text-center text-sm">
						<?php esc_html_e( 'Preencher depois', 'saulocoelho' ); ?>
					</a>
				</div>
			<?php else : ?>
				<h2 class="text-xl font-bold mb-6 flex items-center gap-2">
					<span class="material-symbols-outlined text-primary">poll</span>
					<?php esc_html_e( 'Uma pergunta rápida...', 'saulocoelho' ); ?>
				</h2>

				<form id="survey-form" class="space-y-8">
					<input type="hidden" name="order_id" value="<?php echo (int) $order_id; ?>">

					<div class="space-y-4">
						<p class="text-sm font-medium text-white/50 uppercase tracking-widest"><?php esc_html_e( 'Como você conheceu o Saulo Coelho?', 'saulocoelho' ); ?></p>
						<div class="grid grid-cols-2 sm:grid-cols-3 gap-3" id="source-options">
							<div class="survey-item p-4 text-xs font-bold js-survey-btn" data-value="Instagram">Instagram</div>
							<div class="survey-item p-4 text-xs font-bold js-survey-btn" data-value="Google">Google / Pesquisa</div>
							<div class="survey-item p-4 text-xs font-bold js-survey-btn" data-value="Indicação"><?php esc_html_e( 'Indicação de Amigo', 'saulocoelho' ); ?></div>
							<div class="survey-item p-4 text-xs font-bold js-survey-btn" data-value="Anúncios"><?php esc_html_e( 'Anúncios Online', 'saulocoelho' ); ?></div>
							<div class="survey-item p-4 text-xs font-bold js-survey-btn" data-value="YouTube">YouTube</div>
							<div class="survey-item p-4 text-xs font-bold js-survey-btn" data-value="Outros"><?php esc_html_e( 'Outros', 'saulocoelho' ); ?></div>
						</div>
						<input type="hidden" name="source" id="survey-source" required>
					</div>

					<div class="space-y-4">
						<p class="text-sm font-medium text-white/50 uppercase tracking-widest"><?php esc_html_e( 'O que você mais espera desse treinamento?', 'saulocoelho' ); ?></p>
						<textarea name="expectations" rows="3" class="w-full bg-white/5 border border-white/10 rounded-xl p-4 text-white placeholder-white/20 focus:border-primary outline-none transition-all" placeholder="<?php esc_attr_e( 'Quero dominar...', 'saulocoelho' ); ?>" required></textarea>
					</div>

					<button type="submit" class="btn-primary w-full p-5 border-0 cursor-pointer">
						<?php esc_html_e( 'Enviar e Acessar meu Curso', 'saulocoelho' ); ?>
						<span class="material-symbols-outlined">rocket_launch</span>
					</button>
				</form>

				<div id="survey-success" class="hidden text-center py-6">
					<div class="text-green-400 font-bold mb-4"><?php esc_html_e( 'Respostas enviadas com sucesso! Redirecionando...', 'saulocoelho' ); ?></div>
					<div class="w-full h-1 bg-white/10 rounded-full overflow-hidden">
						<div class="h-full bg-primary animate-[loading_3s_linear_forwards]"></div>
					</div>
				</div>
			<?php endif; ?>
		</div>

		<p class="mt-8 text-white/30 text-xs"><?php esc_html_e( 'A confirmação detalhada do pedido foi enviada para o seu e-mail.', 'saulocoelho' ); ?></p>
	</div>

	<?php wp_footer(); ?>
	<?php if ( ! $has_full_questionnaire ) : ?>
	<script>
		jQuery(document).ready(function($) {
			$('.js-survey-btn').click(function() {
				$('.js-survey-btn').removeClass('active');
				$(this).addClass('active');
				$('#survey-source').val($(this).data('value'));
			});

			$('#survey-form').submit(function(e) {
				e.preventDefault();
				var $form = $(this);
				var btn = $form.find('button');
				btn.prop('disabled', true).text('<?php echo esc_js( __( 'Enviando...', 'saulocoelho' ) ); ?>');

				$.post('<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>', $form.serialize() + '&action=sc_save_survey', function(res) {
					$form.fadeOut(function() {
						$('#survey-success').fadeIn();
						setTimeout(function() {
							window.location.href = '<?php echo esc_js( $course_url ); ?>';
						}, 2500);
					});
				});
			});
		});

		var style = document.createElement('style');
		style.innerHTML = '@keyframes loading { from { width: 0; } to { width: 100%; } }';
		document.head.appendChild(style);
	</script>
	<?php endif; ?>
</body>
</html>
