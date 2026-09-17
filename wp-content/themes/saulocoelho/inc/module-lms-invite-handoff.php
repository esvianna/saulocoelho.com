<?php
/**
 * Handoff após inscrição por convite LMS (AmaEducacional).
 *
 * Com senha no formulário de inscrição (Ama ≥ 1.0.41), o aluno já tem
 * acesso sem depender do e-mail. Este módulo mantém:
 * 1) autenticação na hora (cookie) após user_register em /inscricao/{slug}/
 * 2) redirect à sala do curso
 * 3) aviso de senha só para contas antigas criadas sem senha no formulário
 *
 * @package SauloCoelho
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SC_INVITE_HANDOFF_META', '_sc_invite_needs_password_nudge' );
define( 'SC_INVITE_HANDOFF_SLUG_META', '_sc_invite_course_slug' );

/**
 * Pedido atual é a página de inscrição por token.
 */
function sc_invite_handoff_is_request() {
	$path = wp_parse_url( isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '', PHP_URL_PATH );
	$path = is_string( $path ) ? untrailingslashit( $path ) : '';
	return (bool) preg_match( '#/inscricao/([^/]+)$#', $path );
}

/**
 * Slug do curso na URL /inscricao/{slug}/.
 *
 * @return string
 */
function sc_invite_handoff_course_slug() {
	$path = wp_parse_url( isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '', PHP_URL_PATH );
	$path = is_string( $path ) ? $path : '';
	if ( preg_match( '#/inscricao/([^/]+)/?#', $path, $m ) ) {
		return sanitize_title( rawurldecode( $m[1] ) );
	}
	return '';
}

/**
 * @param string $slug
 * @return int
 */
function sc_invite_handoff_course_id_from_slug( $slug ) {
	$slug = sanitize_title( $slug );
	if ( $slug === '' ) {
		return 0;
	}

	$posts = get_posts(
		array(
			'name'           => $slug,
			'post_type'      => 'ama_course',
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'fields'         => 'ids',
		)
	);

	return ! empty( $posts ) ? (int) $posts[0] : 0;
}

/**
 * @param int $user_id
 * @param int $course_id
 */
function sc_invite_handoff_user_is_enrolled( $user_id, $course_id ) {
	global $wpdb;

	$user_id    = absint( $user_id );
	$course_id  = absint( $course_id );
	if ( ! $user_id || ! $course_id ) {
		return false;
	}

	$table = $wpdb->prefix . 'lms_enrollments';
	$id    = $wpdb->get_var(
		$wpdb->prepare(
			"SELECT id FROM {$table} WHERE user_id = %d AND course_id = %d AND status IN ('active','completed') LIMIT 1",
			$user_id,
			$course_id
		)
	);

	return ! empty( $id );
}

/**
 * @param int $course_id
 * @return string
 */
function sc_invite_handoff_course_url( $course_id ) {
	$course_id = absint( $course_id );
	if ( ! $course_id ) {
		return home_url( '/minha-conta/' );
	}
	$link = get_permalink( $course_id );
	return $link ? $link : home_url( '/minha-conta/' );
}

/**
 * Login silencioso — o aluno não escolheu senha neste fluxo.
 *
 * @param int $user_id
 */
function sc_invite_handoff_login_user( $user_id ) {
	$user_id = absint( $user_id );
	if ( ! $user_id || is_user_logged_in() ) {
		return;
	}

	wp_set_current_user( $user_id );
	wp_set_auth_cookie( $user_id, true, is_ssl() );
}

/**
 * Novo utilizador criado no POST de /inscricao/.
 *
 * @param int $user_id
 */
function sc_invite_handoff_on_user_register( $user_id ) {
	if ( is_admin() && ! wp_doing_ajax() ) {
		return;
	}
	if ( ! sc_invite_handoff_is_request() ) {
		return;
	}

	$user_id = absint( $user_id );
	if ( ! $user_id ) {
		return;
	}

	$slug = sc_invite_handoff_course_slug();
	if ( $slug !== '' ) {
		update_user_meta( $user_id, SC_INVITE_HANDOFF_SLUG_META, $slug );
	}

	// Senha já escolhida no formulário → não pedir «definir senha» depois.
	$chose_password = isset( $_POST['ama_invite_password'] ) && (string) wp_unslash( $_POST['ama_invite_password'] ) !== '';
	if ( ! $chose_password ) {
		update_user_meta( $user_id, SC_INVITE_HANDOFF_META, '1' );
	}

	sc_invite_handoff_login_user( $user_id );
}
add_action( 'user_register', 'sc_invite_handoff_on_user_register', 99 );

/**
 * WC também dispara isto quando usa wc_create_new_customer().
 *
 * @param int $customer_id
 */
function sc_invite_handoff_on_wc_customer( $customer_id ) {
	sc_invite_handoff_on_user_register( $customer_id );
}
add_action( 'woocommerce_created_customer', 'sc_invite_handoff_on_wc_customer', 99 );

/**
 * Depois da matrícula do plugin, ir para a sala (não ficar na página “veja o e-mail”).
 */
function sc_invite_handoff_maybe_redirect() {
	if ( ! sc_invite_handoff_is_request() || ! is_user_logged_in() ) {
		return;
	}

	$method = isset( $_SERVER['REQUEST_METHOD'] ) ? strtoupper( (string) wp_unslash( $_SERVER['REQUEST_METHOD'] ) ) : 'GET';
	if ( $method !== 'POST' ) {
		return;
	}

	$slug      = sc_invite_handoff_course_slug();
	$course_id = sc_invite_handoff_course_id_from_slug( $slug );
	$user_id   = get_current_user_id();

	if ( ! $course_id || ! sc_invite_handoff_user_is_enrolled( $user_id, $course_id ) ) {
		return;
	}

	$url = sc_invite_handoff_course_url( $course_id );
	wp_safe_redirect( $url );
	exit;
}
add_action( 'template_redirect', 'sc_invite_handoff_maybe_redirect', 999 );

/**
 * CTA na página de convite caso o redirect PHP ainda não possa correr
 * (matrícula gravada só durante o template).
 */
function sc_invite_handoff_footer_cta() {
	if ( ! sc_invite_handoff_is_request() ) {
		return;
	}

	$slug      = sc_invite_handoff_course_slug();
	$course_id = sc_invite_handoff_course_id_from_slug( $slug );
	$logged_in  = is_user_logged_in();
	$enrolled   = $logged_in && $course_id && sc_invite_handoff_user_is_enrolled( get_current_user_id(), $course_id );
	$course_url = $course_id ? sc_invite_handoff_course_url( $course_id ) : sc_invite_handoff_account_url();
	$lost       = wp_lostpassword_url();
	$set_pwd    = function_exists( 'wc_get_account_endpoint_url' ) ? wc_get_account_endpoint_url( 'edit-account' ) : sc_invite_handoff_account_url();

	$strings = array(
		'courseUrl'  => $course_url,
		'loggedIn'   => $logged_in,
		'enrolled'   => $enrolled,
		'setPwdUrl'  => $set_pwd,
		'lostUrl'    => $lost,
		'title'      => __( 'Inscrição confirmada', 'saulocoelho' ),
		'lead'       => __( 'Pode entrar na sala agora. Guarde o e-mail e a senha que usou no cadastro para as próximas vezes.', 'saulocoelho' ),
		'ctaCourse'  => __( 'Entrar na sala do curso', 'saulocoelho' ),
		'ctaPwd'     => __( 'Alterar senha na conta', 'saulocoelho' ),
		'ctaLost'    => __( 'Esqueci a senha', 'saulocoelho' ),
	);
	?>
	<style>
		.sc-invite-handoff {
			margin: 1.5rem 0 0;
			padding: 1.25rem 1.35rem;
			border-radius: 12px;
			border: 1px solid rgba(197, 160, 89, 0.4);
			background: rgba(15, 23, 42, 0.92);
			color: #E5E7EB;
		}
		.sc-invite-handoff h2 {
			margin: 0 0 0.5rem;
			font-size: 1.15rem;
			color: #C5A059 !important;
		}
		.sc-invite-handoff p {
			margin: 0 0 1rem;
			font-size: 0.95rem;
			line-height: 1.5;
			color: #E5E7EB !important;
		}
		.sc-invite-handoff__actions { display: flex; flex-direction: column; gap: 0.6rem; }
		.sc-invite-handoff a.button,
		.sc-invite-handoff .button {
			display: inline-flex; align-items: center; justify-content: center;
			padding: 0.85rem 1.1rem; border-radius: 10px; font-weight: 700;
			text-decoration: none; text-align: center;
		}
		.sc-invite-handoff .button-primary { background: #C5A059; color: #050A14; border: 0; }
		.sc-invite-handoff .button-secondary { background: transparent; color: #C5A059; border: 1px solid rgba(197,160,89,.45); }
	</style>
	<script>
	(function () {
		var cfg = <?php echo wp_json_encode( $strings ); ?>;
		var page = document.querySelector('.ama-invite-page, .ama-invite-wrap');
		if (!page) return;
		var success = document.querySelector('.ama-invite-alert--success');
		var shouldShow = !!(success || cfg.enrolled || (cfg.loggedIn && success));
		if (!shouldShow) return;

		var box = document.createElement('div');
		box.className = 'sc-invite-handoff';
		box.innerHTML =
			'<h2>' + cfg.title + '</h2>' +
			'<p>' + cfg.lead + '</p>' +
			'<div class="sc-invite-handoff__actions">' +
				'<a class="button button-primary" href="' + cfg.courseUrl + '">' + cfg.ctaCourse + '</a>' +
				'<a class="button button-secondary" href="' + cfg.setPwdUrl + '">' + cfg.ctaPwd + '</a>' +
				'<a class="button button-secondary" href="' + cfg.lostUrl + '">' + cfg.ctaLost + '</a>' +
			'</div>';

		var wrap = document.querySelector('.ama-invite-wrap') || page;
		wrap.appendChild(box);

		if (cfg.enrolled && cfg.courseUrl && success) {
			window.setTimeout(function () {
				window.location.href = cfg.courseUrl;
			}, 2800);
		}
	})();
	</script>
	<?php
}
add_action( 'wp_footer', 'sc_invite_handoff_footer_cta', 50 );

/**
 * Faixa na sala do curso (destino após o convite) — só se ainda precisa definir senha.
 */
function sc_invite_handoff_course_banner() {
	if ( ! is_singular( 'ama_course' ) || ! is_user_logged_in() ) {
		return;
	}
	if ( ! get_user_meta( get_current_user_id(), SC_INVITE_HANDOFF_META, true ) ) {
		return;
	}
	// Conta criada com senha no formulário (fluxo novo).
	if ( get_user_meta( get_current_user_id(), '_ama_invite_password_chosen', true ) ) {
		delete_user_meta( get_current_user_id(), SC_INVITE_HANDOFF_META );
		return;
	}

	$set_pwd = function_exists( 'wc_get_account_endpoint_url' ) ? wc_get_account_endpoint_url( 'edit-account' ) : sc_invite_handoff_account_url();
	$lost    = wp_lostpassword_url();
	?>
	<div class="sc-invite-handoff sc-invite-handoff--hub" style="max-width:720px;margin:1rem auto 1.5rem;">
		<h2><?php esc_html_e( 'Bem-vindo à turma', 'saulocoelho' ); ?></h2>
		<p><?php esc_html_e( 'Sua inscrição está confirmada. Defina uma senha em Minha Conta para as próximas vezes — o e-mail de acesso pode ir para o spam.', 'saulocoelho' ); ?></p>
		<div class="sc-invite-handoff__actions">
			<a class="button button-primary" href="<?php echo esc_url( $set_pwd ); ?>"><?php esc_html_e( 'Definir minha senha', 'saulocoelho' ); ?></a>
			<a class="button button-secondary" href="<?php echo esc_url( $lost ); ?>"><?php esc_html_e( 'Não recebi o e-mail', 'saulocoelho' ); ?></a>
		</div>
	</div>
	<script>
	(function () {
		var banner = document.querySelector('.sc-invite-handoff--hub');
		var hub = document.querySelector('.ama-lms-wrapper');
		if (banner && hub && hub.parentNode) {
			hub.parentNode.insertBefore(banner, hub);
		}
	})();
	</script>
	<style>
		.sc-invite-handoff--hub {
			padding: 1.25rem 1.35rem;
			border-radius: 12px;
			border: 1px solid rgba(197, 160, 89, 0.4);
			background: rgba(15, 23, 42, 0.95) !important;
			color: #E5E7EB !important;
		}
		.sc-invite-handoff--hub h2 {
			margin: 0 0 0.5rem;
			font-size: 1.15rem;
			color: #C5A059 !important;
			text-transform: none !important;
			letter-spacing: normal !important;
		}
		.sc-invite-handoff--hub p {
			margin: 0 0 1rem;
			color: #E5E7EB !important;
			opacity: 1 !important;
		}
		.sc-invite-handoff--hub .sc-invite-handoff__actions { display: flex; flex-wrap: wrap; gap: 0.6rem; }
		.sc-invite-handoff--hub .button-primary { background: #C5A059; color: #050A14; border: 0; padding: .75rem 1rem; border-radius: 10px; font-weight: 700; text-decoration: none; }
		.sc-invite-handoff--hub .button-secondary { color: #C5A059; border: 1px solid rgba(197,160,89,.45); padding: .75rem 1rem; border-radius: 10px; text-decoration: none; }
	</style>
	<?php
}
add_action( 'wp_footer', 'sc_invite_handoff_course_banner', 40 );

/**
 * Permalink da conta com fallback.
 *
 * @return string
 */
function sc_invite_handoff_account_url() {
	if ( function_exists( 'wc_get_page_permalink' ) ) {
		$url = wc_get_page_permalink( 'myaccount' );
		if ( $url ) {
			return $url;
		}
	}
	return home_url( '/minha-conta/' );
}

/**
 * Aviso persistente até o aluno gravar uma senha em Detalhes da conta.
 */
function sc_invite_handoff_account_nudge() {
	if ( ! is_user_logged_in() ) {
		return;
	}
	if ( ! get_user_meta( get_current_user_id(), SC_INVITE_HANDOFF_META, true ) ) {
		return;
	}

	$set_pwd = function_exists( 'wc_get_account_endpoint_url' ) ? wc_get_account_endpoint_url( 'edit-account' ) : home_url( '/minha-conta/' );
	$lost    = wp_lostpassword_url();
	$slug    = (string) get_user_meta( get_current_user_id(), SC_INVITE_HANDOFF_SLUG_META, true );
	$course  = $slug ? sc_invite_handoff_course_url( sc_invite_handoff_course_id_from_slug( $slug ) ) : '';

	echo '<div class="woocommerce-info sc-invite-password-nudge" style="border-top-color:#C5A059;">';
	echo '<p style="margin:0 0 .5rem;"><strong>' . esc_html__( 'Sua vaga está confirmada.', 'saulocoelho' ) . '</strong> ';
	echo esc_html__( 'Se não recebeu o e-mail de acesso, defina uma senha aqui — não é preciso esperar a caixa de entrada.', 'saulocoelho' );
	echo '</p><p style="margin:0;">';
	echo '<a class="button" href="' . esc_url( $set_pwd ) . '">' . esc_html__( 'Definir senha agora', 'saulocoelho' ) . '</a> ';
	echo '<a href="' . esc_url( $lost ) . '">' . esc_html__( 'Enviar link de redefinição', 'saulocoelho' ) . '</a>';
	if ( $course ) {
		echo ' · <a href="' . esc_url( $course ) . '">' . esc_html__( 'Ir para a sala do curso', 'saulocoelho' ) . '</a>';
	}
	echo '</p></div>';
}
add_action( 'woocommerce_account_dashboard', 'sc_invite_handoff_account_nudge', 4 );
add_action( 'woocommerce_before_edit_account_form', 'sc_invite_handoff_account_nudge', 4 );

/**
 * Quem grava os detalhes da conta já definiu (ou confirmou) a senha.
 */
function sc_invite_handoff_clear_nudge_on_save( $user_id ) {
	delete_user_meta( absint( $user_id ), SC_INVITE_HANDOFF_META );
}
add_action( 'woocommerce_save_account_details', 'sc_invite_handoff_clear_nudge_on_save' );

/**
 * Dica no login quando o plugin manda o aluno existente para Minha Conta.
 */
function sc_invite_handoff_login_hint() {
	$redirect = isset( $_GET['redirect_to'] ) ? esc_url_raw( wp_unslash( $_GET['redirect_to'] ) ) : '';
	if ( $redirect === '' || ( strpos( $redirect, '/curso/' ) === false && strpos( $redirect, '/inscricao/' ) === false ) ) {
		return;
	}
	echo '<p class="sc-invite-login-hint" style="margin:1rem 0 0;font-size:.9rem;">';
	echo esc_html__( 'Se acabou de se inscrever e não recebeu o e-mail, use o mesmo endereço da inscrição em', 'saulocoelho' );
	echo ' <a href="' . esc_url( wp_lostpassword_url() ) . '">' . esc_html__( 'Esqueci a senha', 'saulocoelho' ) . '</a>.';
	echo '</p>';
}
add_action( 'woocommerce_login_form_end', 'sc_invite_handoff_login_hint' );
