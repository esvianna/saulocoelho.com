<?php
/**
 * Skin VTIS Quiz — identidade Saulo Coelho (só CSS variables + enqueue).
 *
 * @package Saulocoelho
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enfileira skin quando o plugin VTIS Quiz carrega assets.
 */
function saulocoelho_vtis_quiz_enqueue_skin() {
	$css = '
.vtis-quiz,
.vtis-quiz-page {
	--vtis-quiz-bg: #050A14;
	--vtis-quiz-surface: #0A0E1A;
	--vtis-quiz-text: #F2F4F7;
	--vtis-quiz-muted: #9AA3AD;
	--vtis-quiz-accent: #C5A059;
	--vtis-quiz-accent-text: #050A14;
	--vtis-quiz-border: rgba(197, 160, 89, 0.28);
	--vtis-quiz-font: Inter, system-ui, sans-serif;
	--vtis-quiz-display: "Playfair Display", Georgia, serif;
}
';
	wp_add_inline_style( 'vtis-quiz', $css );
}
add_action( 'vtis_quiz_enqueue_assets', 'saulocoelho_vtis_quiz_enqueue_skin' );
