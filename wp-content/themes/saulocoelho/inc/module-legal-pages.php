<?php
/**
 * Páginas legais — cria /privacidade/ (modelo editável) e liga à política WP.
 *
 * @package Saulocoelho
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SAULOCOELHO_PRIVACY_SLUG', 'privacidade' );

/**
 * @return WP_Post|null
 */
function saulocoelho_get_privacy_page() {
	$page = get_page_by_path( SAULOCOELHO_PRIVACY_SLUG );
	return ( $page instanceof WP_Post ) ? $page : null;
}

/**
 * URL pública do aviso (página WP, customizer ou fallback do slug).
 *
 * @return string
 */
function saulocoelho_privacy_url() {
	if ( function_exists( 'get_privacy_policy_url' ) ) {
		$url = get_privacy_policy_url();
		if ( $url ) {
			return $url;
		}
	}
	$mod = get_theme_mod( 'footer_privacy_link', '' );
	if ( $mod && $mod !== '#' ) {
		return $mod;
	}
	$page = saulocoelho_get_privacy_page();
	if ( $page ) {
		return get_permalink( $page );
	}
	return home_url( '/' . SAULOCOELHO_PRIVACY_SLUG . '/' );
}

/**
 * Texto-modelo (LGPD). A equipe edita em Páginas → Privacidade; este seed não sobrescreve.
 *
 * @return string
 */
function saulocoelho_privacy_page_seed_content() {
	$email = get_theme_mod( 'footer_email', 'contato@saulocoelho.com' );
	if ( ! is_email( $email ) ) {
		$email = 'contato@saulocoelho.com';
	}

	return '
<blockquote>Este texto é um <strong>modelo operacional</strong> para revisão da equipe Saulo Coelho. Não substitui parecer jurídico. Completar razão social, CNPJ e encarregado (DPO), se houver, antes de considerar a versão final.</blockquote>

<p>Este aviso descreve como o site <strong>saulocoelho.com</strong> trata dados pessoais, em especial no cadastro da área do aluno, na inscrição em turmas (incluindo Leadership Academy por link ou QR) e na captura de leads de palestras.</p>

<h2>1. Quem trata os dados</h2>
<p>O controlador dos dados é a operação do site Saulo Coelho, disponível em saulocoelho.com. Pedidos de titular: <a href="mailto:' . esc_attr( $email ) . '">' . esc_html( $email ) . '</a>.</p>
<p>Ainda não constam neste modelo a razão social completa, o CNPJ nem o nome de um encarregado (DPO). A equipe deve preencher estes campos na revisão.</p>

<h2>2. Quais dados recolhemos</h2>
<ul>
<li><strong>Identificação e contacto:</strong> nome, e-mail e, quando pedido, WhatsApp.</li>
<li><strong>Conta:</strong> credenciais da área do aluno (Minha Conta / WooCommerce) e histórico de pedidos, se houver compra.</li>
<li><strong>Formação:</strong> matrícula, progresso em cursos e, quando existirem, resultados de avaliações ou quizzes no mesmo site.</li>
<li><strong>Leads de eventos:</strong> dados do formulário de palestra para envio de material e comunicações sobre programas.</li>
<li><strong>Técnicos:</strong> endereço IP, data/hora, páginas visitadas e cookies necessários ao funcionamento (login, carrinho, segurança).</li>
</ul>
<p>Não pedimos dados de saúde nem diagnóstico clínico neste site.</p>

<h2>3. Para que usamos</h2>
<ul>
<li>Criar e autenticar a conta, matricular o aluno na turma e dar acesso à área do aluno.</li>
<li>Enviar confirmação de inscrição, dados de acesso e avisos operacionais do curso ou do pedido.</li>
<li>Comunicar sobre programas, eventos e conteúdos relacionados, quando houver consentimento ou relação contratual.</li>
<li>Cumprir obrigações legais, prevenir abuso (limites de login, segurança) e melhorar o serviço.</li>
</ul>

<h2>4. Bases legais (LGPD)</h2>
<ul>
<li><strong>Consentimento</strong> — checkbox do aviso de privacidade na inscrição por convite e nos formulários de lead.</li>
<li><strong>Execução de contrato</strong> — prestação do curso, conta e compras na loja.</li>
<li><strong>Legítimo interesse</strong> — segurança da plataforma e comunicações estritamente relacionadas ao serviço, com respeito aos direitos do titular.</li>
<li><strong>Obrigação legal</strong> — quando a lei exigir conservação ou partilha.</li>
</ul>

<h2>5. Com quem partilhamos</h2>
<p>Prestadores que operam o site, na medida do necessário: hospedagem, WordPress, WooCommerce, e-mail transaccional (SMTP) e, se configurado, meios de pagamento. Não vendemos listas de contactos. O PWA do Método OCD (<code>app.saulocoelho.com</code>) é um produto distinto e não recebe automaticamente os dados da inscrição da Leadership Academy.</p>

<h2>6. Conservação</h2>
<p>Mantemos os dados enquanto a conta ou a matrícula forem necessárias à formação, e pelo prazo legal aplicável a obrigações fiscais ou de defesa de direitos. Leads de palestra seguem a finalidade do evento e da comunicação consentida. O titular pode pedir exclusão, salvo hipótese legal de guarda.</p>

<h2>7. Direitos do titular</h2>
<p>Nos termos da Lei n.º 13.709/2018 (LGPD), pode solicitar confirmação de tratamento, acesso, correção, anonimização, portabilidade, informação sobre partilhas, revogação do consentimento e eliminação dos dados, quando cabível. Pedidos: <a href="mailto:' . esc_attr( $email ) . '">' . esc_html( $email ) . '</a>. Também pode contactar a Autoridade Nacional de Proteção de Dados (ANPD).</p>

<h2>8. Cookies</h2>
<p>Utilizamos cookies essenciais de sessão, autenticação e carrinho. Cookies de medição ou marketing, se vierem a ser adoptados, serão descritos nesta página e, quando exigido, sujeitos a consentimento.</p>

<h2>9. Segurança e menores</h2>
<p>Aplicamos medidas compatíveis com a natureza do site (HTTPS, limites de tentativas de login, controlos de acesso). O conteúdo destina-se a adultos; não recolhemos intencionalmente dados de crianças.</p>

<h2>10. Actualizações</h2>
<p>Este aviso pode ser alterado. A data de actualização aparece no topo da página. Alterações relevantes serão reflectidas neste endereço: <strong>' . esc_html( home_url( '/' . SAULOCOELHO_PRIVACY_SLUG . '/' ) ) . '</strong>.</p>
';
}

/**
 * Cria a página uma vez; não reescreve conteúdo já editado pela equipe.
 */
function saulocoelho_maybe_create_privacy_page() {
	$existing = saulocoelho_get_privacy_page();
	if ( $existing ) {
		$tpl = get_page_template_slug( $existing );
		if ( $tpl !== 'template-legal.php' ) {
			update_post_meta( $existing->ID, '_wp_page_template', 'template-legal.php' );
		}
		saulocoelho_maybe_assign_wp_privacy_page( $existing->ID );
		return;
	}

	$id = wp_insert_post(
		[
			'post_title'   => __( 'Aviso de privacidade', 'saulocoelho' ),
			'post_name'    => SAULOCOELHO_PRIVACY_SLUG,
			'post_status'  => 'publish',
			'post_type'    => 'page',
			'post_content' => saulocoelho_privacy_page_seed_content(),
			'post_excerpt' => __( 'Modelo LGPD para revisão da equipe. Cadastro, turmas, loja e leads.', 'saulocoelho' ),
		],
		true
	);

	if ( is_wp_error( $id ) || ! $id ) {
		return;
	}

	update_post_meta( $id, '_wp_page_template', 'template-legal.php' );
	update_post_meta( $id, '_sc_legal_seed', 'privacy_v1' );
	saulocoelho_maybe_assign_wp_privacy_page( (int) $id );
}

/**
 * Liga a página às Definições → Privacidade do WordPress, sem substituir uma política já válida.
 *
 * @param int $page_id
 */
function saulocoelho_maybe_assign_wp_privacy_page( $page_id ) {
	$page_id = (int) $page_id;
	if ( $page_id <= 0 ) {
		return;
	}
	$current = (int) get_option( 'wp_page_for_privacy_policy' );
	if ( $current > 0 && get_post_status( $current ) === 'publish' ) {
		return;
	}
	update_option( 'wp_page_for_privacy_policy', $page_id );
}

add_action( 'init', 'saulocoelho_maybe_create_privacy_page', 22 );
add_action( 'after_switch_theme', 'saulocoelho_maybe_create_privacy_page' );
