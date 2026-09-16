<?php
/**
 * Skin AmaEducacional no tema Saulo: convite, landing de curso e player.
 *
 * @package Saulocoelho
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Landing de venda do LMS no visual dark do site (não o light/roxo da Amaminerais).
 *
 * @return bool
 */
function saulocoelho_lms_force_sales_dark() {
	return true;
}
add_filter( 'ama_educacional_sales_force_dark', 'saulocoelho_lms_force_sales_dark' );

/**
 * CSS da inscrição e da sala/landing do curso.
 */
function saulocoelho_lms_invite_skin() {
	$is_invite = class_exists( '\AmaEducacional\Frontend\InviteRegistration' )
		&& \AmaEducacional\Frontend\InviteRegistration::is_invite_page();
	$is_lms    = is_singular( [ 'ama_course', 'ama_lesson' ] ) || is_post_type_archive( 'ama_course' );
	if ( ! $is_invite && ! $is_lms ) {
		return;
	}

	$css = '
.ama-invite-page {
	background: #050A14;
	color: #F2F4F7;
	font-family: Inter, system-ui, sans-serif;
	padding-top: 3rem;
	padding-bottom: 6rem;
}
.ama-invite-kicker {
	color: #C5A059;
	font-family: Inter, sans-serif;
	font-weight: 800;
	letter-spacing: 0.4em;
	font-size: 10px;
}
.ama-invite-title {
	font-family: "Playfair Display", Georgia, serif;
	color: #fff;
	font-weight: 800;
	letter-spacing: -0.03em;
	text-transform: none;
}
.ama-invite-lead,
.ama-invite-login-hint,
.ama-invite-login-hint a,
.ama-invite-consent,
.ama-invite-consent a {
	color: #9AA3AD;
}
.ama-invite-login-hint a,
.ama-invite-consent a {
	color: #C5A059;
	text-decoration: underline;
	text-underline-offset: 3px;
}
.ama-invite-form label {
	color: #E5E7EB;
	font-family: Inter, sans-serif;
}
.ama-invite-form input[type="text"],
.ama-invite-form input[type="email"],
.ama-invite-form input[type="tel"] {
	background: #0A0E1A;
	border: 1px solid rgba(197, 160, 89, 0.28);
	color: #F2F4F7;
	border-radius: 10px;
}
.ama-invite-form input:focus {
	outline: none;
	border-color: #C5A059;
	box-shadow: 0 0 0 1px rgba(197, 160, 89, 0.35);
}
.ama-invite-submit {
	background: #C5A059;
	color: #050A14;
	width: 100%;
	text-align: center;
	border-radius: 10px;
	font-family: Inter, sans-serif;
	letter-spacing: 0.02em;
}
.ama-invite-submit:hover,
.ama-invite-submit:focus {
	background: #D4AF37;
	color: #050A14;
}
.ama-invite-alert--error {
	background: rgba(127, 29, 29, 0.35);
	border-color: rgba(248, 113, 113, 0.45);
	color: #fecaca;
}
.ama-invite-alert--success {
	background: rgba(6, 78, 59, 0.35);
	border-color: rgba(52, 211, 153, 0.35);
	color: #a7f3d0;
}

/* Curso: o tema pinta todos os Hn de branco/maiúsculas e parte o layout do plugin. */
.ama-lms-wrapper h1,
.ama-lms-wrapper h2,
.ama-lms-wrapper h3,
.ama-lms-wrapper h4,
.ama-lms-wrapper h5,
.ama-lms-wrapper h6 {
	color: inherit !important;
	text-transform: none !important;
	letter-spacing: normal !important;
	font-family: Inter, system-ui, sans-serif !important;
	font-weight: 700 !important;
	line-height: 1.3 !important;
}
.ama-lms-wrapper.ama-mode-sales {
	--ama-sales-hero-gradient: linear-gradient(180deg, rgba(197, 160, 89, 0.12) 0%, rgba(5, 10, 20, 0.92) 50%, transparent 100%);
	--ama-sales-hero-border: rgba(197, 160, 89, 0.18);
	--ama-sales-hero-badge-bg: rgba(197, 160, 89, 0.16);
	--ama-sales-surface-card: #0f172a;
	--ama-sales-surface-muted: #0A0E1A;
	--ama-sales-surface-inset: #0A0E1A;
	--ama-sales-text-heading: #F8FAFC;
	--ama-sales-text: #E5E7EB;
	--ama-sales-text-secondary: #CBD5E1;
	--ama-sales-text-muted: #9AA3AD;
	--ama-sales-text-subtle: #CBD5E1;
	--ama-sales-text-label: #9AA3AD;
	--ama-sales-link: #C5A059;
	--ama-sales-link-hover: #D4AF37;
	--ama-sales-border: rgba(197, 160, 89, 0.22);
	--ama-sales-border-muted: rgba(255, 255, 255, 0.08);
	--ama-sales-border-details: rgba(255, 255, 255, 0.08);
	--ama-sales-divider: rgba(255, 255, 255, 0.08);
	--ama-sales-divider-soft: rgba(255, 255, 255, 0.06);
	--ama-sales-include-icon: #C5A059;
	--ama-sales-btn-cart-border: rgba(197, 160, 89, 0.45);
	--ama-sales-btn-cart-bg: rgba(197, 160, 89, 0.16);
	--ama-sales-btn-cart-hover-bg: rgba(197, 160, 89, 0.28);
	font-family: Inter, system-ui, sans-serif;
}
.ama-lms-wrapper .ama-course-hero-title {
	font-family: "Playfair Display", Georgia, serif !important;
	font-size: clamp(2rem, 4vw, 3.25rem) !important;
	font-weight: 800 !important;
	color: #fff !important;
	text-transform: none !important;
	letter-spacing: -0.03em !important;
	margin: 0.35rem 0 0.75rem !important;
}
.ama-lms-wrapper .ama-sales-hero-badge,
.ama-lms-wrapper .ama-sales-hero-stats,
.ama-lms-wrapper .ama-course-meta {
	font-family: Inter, sans-serif !important;
	text-transform: none !important;
	letter-spacing: 0.02em !important;
	color: #9AA3AD !important;
	font-size: 0.9rem !important;
	font-weight: 500 !important;
}
.ama-lms-wrapper .ama-sales-hero-badge {
	color: #C5A059 !important;
	font-weight: 800 !important;
	letter-spacing: 0.18em !important;
	text-transform: uppercase !important;
	font-size: 0.7rem !important;
	background: rgba(197, 160, 89, 0.16) !important;
}
.ama-lms-wrapper .ama-sidebar-includes-title,
.ama-lms-wrapper .ama-curriculum-title,
.ama-lms-wrapper .ama-sales-main h2 {
	color: #C5A059 !important;
	font-size: 0.8rem !important;
	letter-spacing: 0.16em !important;
	text-transform: uppercase !important;
	font-weight: 800 !important;
}
.ama-lms-wrapper a.button,
.ama-lms-wrapper .button,
.ama-lms-wrapper button[type="submit"],
.ama-lms-wrapper .ama-invite-submit {
	display: inline-flex !important;
	align-items: center !important;
	justify-content: center !important;
	gap: 0.35rem !important;
	background: #C5A059 !important;
	color: #050A14 !important;
	border: 0 !important;
	border-radius: 8px !important;
	font-family: Inter, sans-serif !important;
	text-transform: none !important;
	font-weight: 700 !important;
	line-height: 1.25 !important;
	padding: 0.75rem 1.25rem !important;
	margin: 0.35rem 0.5rem 0.35rem 0 !important;
	min-height: 2.75rem !important;
	box-sizing: border-box !important;
	text-decoration: none !important;
	width: auto !important;
	max-width: 100%;
}
.ama-lms-wrapper a.button.ama-btn-full,
.ama-lms-wrapper .button.ama-btn-full,
.ama-lms-wrapper .ama-invite-submit {
	width: 100% !important;
	margin-right: 0 !important;
}
.ama-lms-wrapper .ama-player-controls-bar,
.ama-lms-wrapper .ama-player-controls {
	gap: 0.75rem !important;
}
.ama-lms-wrapper .ama-player-controls-bar .button,
.ama-lms-wrapper .ama-player-controls .button {
	margin: 0 !important;
}
.ama-lms-wrapper.ama-mode-hub .ama-sidebar-content,
.ama-lms-wrapper.ama-mode-invite .ama-sidebar-content {
	padding: 1.1rem 1.15rem 1.25rem !important;
}
.ama-lms-wrapper.ama-mode-hub .ama-sidebar-content > p:first-child,
.ama-lms-wrapper.ama-mode-invite .ama-sidebar-content > p:first-child {
	margin-top: 0 !important;
}
.ama-lms-wrapper .ama-invite-gate-card .button,
.ama-lms-wrapper .ama-hub-hero-cta .button {
	margin: 0.5rem 0 !important;
}
.ama-lms-wrapper .ama-player-lesson-title {
	font-family: "Playfair Display", Georgia, serif !important;
	color: #fff !important;
	text-transform: none !important;
	font-size: 1.75rem !important;
}
.ama-lms-wrapper.ama-mode-hub .ama-hub-access__icon,
.ama-lms-wrapper.ama-mode-hub .ama-hub-item__done {
	color: #C5A059 !important;
}
.ama-lms-wrapper.ama-mode-hub .ama-hub-progress__fill {
	background: #C5A059 !important;
}
.ama-lms-wrapper.ama-mode-hub .ama-hub-access__card:hover,
.ama-lms-wrapper.ama-mode-hub .ama-hub-access__card:focus {
	border-color: rgba(197, 160, 89, 0.55) !important;
}
/* Player: sem link para catálogo público (só «Voltar ao curso») */
.ama-lms-wrapper .ama-player-back-row .ama-back-sep,
.ama-lms-wrapper .ama-player-back-row .ama-back-link-secondary {
	display: none !important;
}
/* Hub: Aulas / Avaliações / Materiais sempre em 3 colunas (incl. mobile) */
.ama-lms-wrapper.ama-mode-hub .ama-hub-access {
	display: grid !important;
	grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
	gap: 0.5rem !important;
}
.ama-lms-wrapper.ama-mode-hub .ama-hub-access__card {
	min-height: 0 !important;
	padding: 0.75rem 0.5rem !important;
	gap: 0.2rem !important;
}
.ama-lms-wrapper.ama-mode-hub .ama-hub-access__icon {
	font-size: 1.15rem !important;
	width: 1.15rem !important;
	height: 1.15rem !important;
}
.ama-lms-wrapper.ama-mode-hub .ama-hub-access__card strong {
	font-size: 0.8rem !important;
	line-height: 1.25 !important;
}
.ama-lms-wrapper.ama-mode-hub .ama-hub-access__card span:last-child {
	font-size: 0.65rem !important;
	line-height: 1.3 !important;
}
@media (min-width: 640px) {
	.ama-lms-wrapper.ama-mode-hub .ama-hub-access {
		gap: 0.75rem !important;
	}
	.ama-lms-wrapper.ama-mode-hub .ama-hub-access__card {
		padding: 1rem 0.85rem !important;
	}
	.ama-lms-wrapper.ama-mode-hub .ama-hub-access__card strong {
		font-size: 0.95rem !important;
	}
	.ama-lms-wrapper.ama-mode-hub .ama-hub-access__card span:last-child {
		font-size: 0.8rem !important;
	}
}
/* Com 4.º card (Certificado), 2×2 no telemóvel; 4 colunas no desktop */
.ama-lms-wrapper.ama-mode-hub .ama-hub-access:has(> :nth-child(4)) {
	grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
}
@media (min-width: 900px) {
	.ama-lms-wrapper.ama-mode-hub .ama-hub-access:has(> :nth-child(4)) {
		grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
	}
}
';
	wp_add_inline_style( 'ama-lms-style', $css );
}
add_action( 'wp_enqueue_scripts', 'saulocoelho_lms_invite_skin', 30 );
