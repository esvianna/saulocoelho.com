<?php
/**
 * Login Minha Conta — layout alinhado ao portal do aluno.
 *
 * @see woocommerce/templates/myaccount/form-login.php
 * @package WooCommerce\Templates
 * @version 9.9.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_customer_login_form' );
?>

<?php if ( 'yes' === get_option( 'woocommerce_enable_myaccount_registration' ) ) : ?>
<div class="u-columns col2-set" id="customer_login">
	<div class="u-column1 col-1">
<?php endif; ?>

		<h2><?php esc_html_e( 'Entrar', 'saulocoelho' ); ?></h2>

		<form class="woocommerce-form woocommerce-form-login login" method="post" novalidate>
			<?php do_action( 'woocommerce_login_form_start' ); ?>

			<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
				<label for="username"><?php esc_html_e( 'Nome de usuário ou e-mail', 'saulocoelho' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span></label>
				<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" required aria-required="true" /><?php // phpcs:ignore WordPress.Security.NonceVerification.Missing ?>
			</p>
			<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
				<label for="password"><?php esc_html_e( 'Senha', 'saulocoelho' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span></label>
				<span class="sc-password-field">
					<input class="woocommerce-Input woocommerce-Input--text input-text" type="password" name="password" id="password" autocomplete="current-password" required aria-required="true" />
					<button type="button" class="sc-password-toggle" data-target="password" aria-label="<?php esc_attr_e( 'Mostrar senha', 'saulocoelho' ); ?>" aria-pressed="false">
						<span class="material-symbols-outlined" aria-hidden="true">visibility</span>
					</button>
				</span>
			</p>

			<?php do_action( 'woocommerce_login_form' ); ?>

			<p class="form-row">
				<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
				<?php
				$sc_login_redirect = ! empty( $_GET['redirect_to'] ) ? esc_url_raw( wp_unslash( $_GET['redirect_to'] ) ) : wc_get_page_permalink( 'myaccount' ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				?>
				<input type="hidden" name="redirect" value="<?php echo esc_url( $sc_login_redirect ); ?>" />
				<button type="submit" class="woocommerce-button button woocommerce-form-login__submit" name="login" value="<?php esc_attr_e( 'Acessar', 'saulocoelho' ); ?>"><?php esc_html_e( 'Acessar', 'saulocoelho' ); ?></button>
				<label class="woocommerce-form__label woocommerce-form__label-for-checkbox woocommerce-form-login__rememberme">
					<input class="woocommerce-form__input woocommerce-form__input-checkbox" name="rememberme" type="checkbox" id="rememberme" value="forever" checked="checked" />
					<span><?php esc_html_e( 'Manter-me ligado neste aparelho', 'saulocoelho' ); ?></span>
				</label>
			</p>
			<p class="woocommerce-LostPassword lost_password">
				<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'Perdeu sua senha?', 'saulocoelho' ); ?></a>
			</p>

			<?php do_action( 'woocommerce_login_form_end' ); ?>
		</form>

<?php if ( 'yes' === get_option( 'woocommerce_enable_myaccount_registration' ) ) : ?>
	</div>
	<div class="u-column2 col-2">
		<h2><?php esc_html_e( 'Cadastrar', 'saulocoelho' ); ?></h2>
		<form method="post" class="woocommerce-form woocommerce-form-register register" <?php do_action( 'woocommerce_register_form_tag' ); ?>>
			<?php do_action( 'woocommerce_register_form_start' ); ?>
			<?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>
				<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
					<label for="reg_username"><?php esc_html_e( 'Nome de usuário', 'saulocoelho' ); ?>&nbsp;<span class="required">*</span></label>
					<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="reg_username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" /><?php // phpcs:ignore WordPress.Security.NonceVerification.Missing ?>
				</p>
			<?php endif; ?>
			<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
				<label for="reg_email"><?php esc_html_e( 'E-mail', 'saulocoelho' ); ?>&nbsp;<span class="required">*</span></label>
				<input type="email" class="woocommerce-Input woocommerce-Input--text input-text" name="email" id="reg_email" autocomplete="email" value="<?php echo ( ! empty( $_POST['email'] ) ) ? esc_attr( wp_unslash( $_POST['email'] ) ) : ''; ?>" /><?php // phpcs:ignore WordPress.Security.NonceVerification.Missing ?>
			</p>
			<?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>
				<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
					<label for="reg_password"><?php esc_html_e( 'Senha', 'saulocoelho' ); ?>&nbsp;<span class="required">*</span></label>
					<span class="sc-password-field">
						<input type="password" class="woocommerce-Input woocommerce-Input--text input-text" name="password" id="reg_password" autocomplete="new-password" />
						<button type="button" class="sc-password-toggle" data-target="reg_password" aria-label="<?php esc_attr_e( 'Mostrar senha', 'saulocoelho' ); ?>" aria-pressed="false">
							<span class="material-symbols-outlined" aria-hidden="true">visibility</span>
						</button>
					</span>
				</p>
			<?php else : ?>
				<p><?php esc_html_e( 'Um link para definir a senha será enviado ao seu e-mail.', 'saulocoelho' ); ?></p>
			<?php endif; ?>
			<?php do_action( 'woocommerce_register_form' ); ?>
			<p class="woocommerce-form-row form-row">
				<?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
				<button type="submit" class="woocommerce-Button woocommerce-button button woocommerce-form-register__submit" name="register" value="<?php esc_attr_e( 'Cadastrar', 'saulocoelho' ); ?>"><?php esc_html_e( 'Cadastrar', 'saulocoelho' ); ?></button>
			</p>
			<?php do_action( 'woocommerce_register_form_end' ); ?>
		</form>
	</div>
</div>
<?php endif; ?>

<?php do_action( 'woocommerce_after_customer_login_form' ); ?>

<script>
(function () {
	document.querySelectorAll('.sc-password-toggle').forEach(function (btn) {
		btn.addEventListener('click', function () {
			var id = btn.getAttribute('data-target');
			var input = id ? document.getElementById(id) : null;
			var icon = btn.querySelector('.material-symbols-outlined');
			if (!input) {
				return;
			}
			var show = input.type === 'password';
			input.type = show ? 'text' : 'password';
			btn.setAttribute('aria-pressed', show ? 'true' : 'false');
			btn.setAttribute('aria-label', show ? <?php echo wp_json_encode( __( 'Ocultar senha', 'saulocoelho' ) ); ?> : <?php echo wp_json_encode( __( 'Mostrar senha', 'saulocoelho' ) ); ?>);
			if (icon) {
				icon.textContent = show ? 'visibility_off' : 'visibility';
			}
		});
	});
})();
</script>
