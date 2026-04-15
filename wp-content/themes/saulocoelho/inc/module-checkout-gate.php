<?php
/**
 * Módulo Checkout Gate — Saulo Coelho
 * Intercepta usuários deslogados no Checkout e fornece uma experiência premium de Login/Cadastro.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Normaliza trechos de endereço vindos do front (evita "--", placeholders de ViaCEP etc.).
 *
 * @param string $val Valor bruto.
 * @return string Valor limpo ou string vazia se inválido.
 */
function sc_gate_normalize_address_part( $val ) {
    $val = trim( (string) wp_unslash( $val ) );
    if ( $val === '' || $val === '--' || $val === '...' || $val === '…' ) {
        return '';
    }
    if ( function_exists( 'mb_stripos' ) ) {
        if ( mb_stripos( $val, 'buscando', 0, 'UTF-8' ) !== false ) {
            return '';
        }
    } elseif ( stripos( $val, 'buscando' ) !== false ) {
        return '';
    }
    return $val;
}

/**
 * Conta apenas dígitos (CPF/CNPJ).
 *
 * @param string $val Valor.
 * @return int Número de dígitos.
 */
function sc_gate_digits_only_length( $val ) {
    return strlen( preg_replace( '/\D/', '', (string) wp_unslash( $val ) ) );
}

// 1. INTERCEPTAR CHECKOUT E REDIRECIONAR 
add_action( 'template_redirect', 'sc_checkout_gate_redirect' );
function sc_checkout_gate_redirect() {
    // Se estiver no checkout e NÃO estiver logado e NÃO estiver na finalização de pedido
    if ( is_checkout() && ! is_user_logged_in() && ! is_wc_endpoint_url( 'order-pay' ) && ! is_wc_endpoint_url( 'order-received' ) ) {
        // Verifica se o carrinho não está vazio
        if ( ! WC()->cart->is_empty() ) {
            wp_redirect( home_url( '/boas-vindas/' ) );
            exit;
        }
    }
}

/**
 * 2. REDIRECIONAR PÁGINAS DE SUCESSO E FALHA
 */
add_action( 'template_redirect', 'sc_checkout_custom_pages_redirect' );
function sc_checkout_custom_pages_redirect() {
    global $wp;

    // Se estiver na finalização de pedido (Página de Obrigado nativa)
    if ( is_wc_endpoint_url( 'order-received' ) && ! isset( $_GET['sc_thanks'] ) ) {
        $order_id = absint( $wp->query_vars['order-received'] );
        if ( $order_id ) {
            wp_redirect( add_query_arg( array( 'sc_thanks' => 1, 'order_id' => $order_id ), home_url( '/finalizado' ) ) );
            exit;
        }
    }

    // Se o pagamento falhou (Geralmente WooCommerce volta pro checkout com parâmetro de erro)
    if ( is_checkout() && isset( $_GET['pay_for_order'] ) && isset( $_GET['payment_status'] ) && $_POST['payment_status'] === 'failed' ) {
         wp_redirect( home_url( '/pagamento-falhou' ) );
         exit;
    }
}

// 3. REGISTRAR ENDPOINTS CUSTOMIZADOS 
add_action( 'init', 'sc_checkout_gate_rewrite_rule' );
function sc_checkout_gate_rewrite_rule() {
    add_rewrite_rule( '^boas-vindas/?$', 'index.php?sc_checkout_gate=1', 'top' );
    add_rewrite_rule( '^finalizado/?$', 'index.php?sc_checkout_success=1', 'top' );
    add_rewrite_rule( '^pagamento-falhou/?$', 'index.php?sc_checkout_failed=1', 'top' );
}

add_filter( 'query_vars', 'sc_checkout_gate_query_vars' );
function sc_checkout_gate_query_vars( $vars ) {
    $vars[] = 'sc_checkout_gate';
    $vars[] = 'sc_checkout_success';
    $vars[] = 'sc_checkout_failed';
    return $vars;
}

add_action( 'template_include', 'sc_checkout_gate_template_include' );
function sc_checkout_gate_template_include( $template ) {
    if ( get_query_var( 'sc_checkout_gate' ) ) {
        return __DIR__ . '/checkout-gate-template.php';
    }
    if ( get_query_var( 'sc_checkout_success' ) ) {
        return get_template_directory() . '/template-parts/checkout/thanks.php';
    }
    if ( get_query_var( 'sc_checkout_failed' ) ) {
        return get_template_directory() . '/template-parts/checkout/failed.php';
    }
    return $template;
}

// 3. CAPTCHA MATEMÁTICO (Simples e Leve)
function sc_generate_math_captcha() {
    $num1 = rand( 1, 10 );
    $num2 = rand( 1, 10 );
    $result = $num1 + $num2;
    WC()->session->set( 'sc_captcha_result', $result );
    return "$num1 + $num2";
}

// 4. AJAX: VALIDAÇÃO DE CPF/CNPJ ÚNICO
add_action( 'wp_ajax_sc_check_document', 'sc_ajax_check_document' );
add_action( 'wp_ajax_nopriv_sc_check_document', 'sc_ajax_check_document' );
function sc_ajax_check_document() {
    $doc = sanitize_text_field( $_POST['document'] );
    $type = sanitize_text_field( $_POST['type'] ); // 'cpf' ou 'cnpj'
    
    if ( empty( $doc ) ) wp_send_json_error( 'Documento vazio' );

    $meta_key = ( $type === 'cpf' ) ? 'billing_cpf' : 'billing_cnpj';
    
    $user_query = new WP_User_Query( array(
        'meta_key'   => $meta_key,
        'meta_value' => $doc,
        'number'     => 1
    ) );

    if ( ! empty( $user_query->get_results() ) ) {
        wp_send_json_error( 'Este ' . strtoupper($type) . ' já possui cadastro. Por favor, faça login.' );
    } else {
        wp_send_json_success( 'Documento disponível' );
    }
}

// 5. AJAX: CADASTRO RÁPIDO (Opção B)
add_action( 'wp_ajax_sc_quick_register', 'sc_ajax_quick_register' );
add_action( 'wp_ajax_nopriv_sc_quick_register', 'sc_ajax_quick_register' );
function sc_ajax_quick_register() {
    check_ajax_referer( 'sc_gate_nonce', 'security' );

    $email    = sanitize_email( wp_unslash( $_POST['email'] ?? '' ) );
    $password = isset( $_POST['password'] ) ? (string) wp_unslash( $_POST['password'] ) : '';
    $captcha  = isset( $_POST['captcha_answer'] ) ? intval( $_POST['captcha_answer'] ) : 0;
    $saved_captcha = WC()->session->get( 'sc_captcha_result' );

    // Validações Básicas
    if ( $captcha !== $saved_captcha ) {
        wp_send_json_error( 'Resultado da conta matemático incorreto.' );
    }
    if ( ! is_email( $email ) ) {
        wp_send_json_error( 'Informe um e-mail válido.' );
    }
    if ( email_exists( $email ) ) {
        wp_send_json_error( 'Este e-mail já está cadastrado. Tente fazer login.' );
    }
    if ( strlen( $password ) < 6 ) {
        wp_send_json_error( 'A senha deve ter pelo menos 6 caracteres.' );
    }

    $persontype = isset( $_POST['persontype'] ) ? sanitize_text_field( wp_unslash( $_POST['persontype'] ) ) : '1';
    if ( $persontype !== '1' && $persontype !== '2' ) {
        wp_send_json_error( 'Tipo de pessoa inválido.' );
    }

    $phone   = sc_gate_normalize_address_part( $_POST['phone'] ?? '' );
    $postcode = sc_gate_normalize_address_part( $_POST['postcode'] ?? '' );
    $address_number = sc_gate_normalize_address_part( $_POST['address_number'] ?? '' );
    $address_1 = sc_gate_normalize_address_part( $_POST['address_1'] ?? '' );
    $city    = sc_gate_normalize_address_part( $_POST['city'] ?? '' );
    $state   = sc_gate_normalize_address_part( $_POST['state'] ?? '' );

    if ( $phone === '' ) {
        wp_send_json_error( 'O WhatsApp é obrigatório.' );
    }
    if ( $postcode === '' || sc_gate_digits_only_length( $postcode ) !== 8 ) {
        wp_send_json_error( 'Informe um CEP válido com 8 dígitos.' );
    }
    if ( $address_number === '' ) {
        wp_send_json_error( 'O número do endereço é obrigatório.' );
    }
    if ( $address_1 === '' ) {
        wp_send_json_error( 'Informe o logradouro (preencha o CEP e aguarde ou digite a rua manualmente).' );
    }
    if ( $city === '' || $state === '' || strlen( $state ) !== 2 ) {
        wp_send_json_error( 'Cidade e estado são obrigatórios. Confira se o CEP foi encontrado.' );
    }

    if ( $persontype === '1' ) {
        $first = sc_gate_normalize_address_part( $_POST['first_name'] ?? '' );
        $last  = sc_gate_normalize_address_part( $_POST['last_name'] ?? '' );
        $cpf   = isset( $_POST['cpf'] ) ? (string) wp_unslash( $_POST['cpf'] ) : '';
        if ( $first === '' ) {
            wp_send_json_error( 'O nome é obrigatório.' );
        }
        if ( $last === '' ) {
            wp_send_json_error( 'O sobrenome é obrigatório.' );
        }
        if ( sc_gate_digits_only_length( $cpf ) !== 11 ) {
            wp_send_json_error( 'Informe um CPF válido (11 dígitos).' );
        }
    } else {
        $company = sc_gate_normalize_address_part( $_POST['company'] ?? '' );
        $cnpj    = isset( $_POST['cnpj'] ) ? (string) wp_unslash( $_POST['cnpj'] ) : '';
        if ( $company === '' ) {
            wp_send_json_error( 'A razão social é obrigatória.' );
        }
        if ( sc_gate_digits_only_length( $cnpj ) !== 14 ) {
            wp_send_json_error( 'Informe um CNPJ válido (14 dígitos).' );
        }
    }

    // Criar Usuário
    $customer_id = wc_create_new_customer( $email, '', $password );

    if ( is_wp_error( $customer_id ) ) {
        wp_send_json_error( $customer_id->get_error_message() );
    }

    // Mapear campos do formulário para Billing
    update_user_meta( $customer_id, 'billing_persontype', $persontype );
    
    if ( $persontype == '1' ) {
        update_user_meta( $customer_id, 'billing_first_name', sanitize_text_field( wp_unslash( $_POST['first_name'] ) ) );
        update_user_meta( $customer_id, 'billing_last_name', sanitize_text_field( wp_unslash( $_POST['last_name'] ) ) );
        update_user_meta( $customer_id, 'billing_cpf', sanitize_text_field( wp_unslash( $_POST['cpf'] ) ) );
    } else {
        update_user_meta( $customer_id, 'billing_company', sanitize_text_field( wp_unslash( $_POST['company'] ) ) );
        update_user_meta( $customer_id, 'billing_cnpj', sanitize_text_field( wp_unslash( $_POST['cnpj'] ) ) );
    }

    update_user_meta( $customer_id, 'billing_phone', sanitize_text_field( wp_unslash( $_POST['phone'] ) ) );
    update_user_meta( $customer_id, 'billing_postcode', sanitize_text_field( wp_unslash( $_POST['postcode'] ) ) );
    update_user_meta( $customer_id, 'billing_number', sanitize_text_field( wp_unslash( $_POST['address_number'] ) ) );
    update_user_meta( $customer_id, 'billing_address_1', sanitize_text_field( wp_unslash( $_POST['address_1'] ) ) );
    update_user_meta( $customer_id, 'billing_address_2', sanitize_text_field( wp_unslash( $_POST['address_complement'] ?? '' ) ) );
    // Bairro, Cidade e Estado (ViaCEP já preencheu no front, mas salvamos se enviados)
    update_user_meta( $customer_id, 'billing_neighborhood', sanitize_text_field( wp_unslash( $_POST['neighborhood'] ?? '' ) ) );
    update_user_meta( $customer_id, 'billing_city', sanitize_text_field( wp_unslash( $_POST['city'] ) ) );
    update_user_meta( $customer_id, 'billing_state', strtoupper( sanitize_text_field( wp_unslash( $_POST['state'] ) ) ) );
    update_user_meta( $customer_id, 'billing_country', 'BR' );

    // Login Silencioso
    wp_set_current_user( $customer_id );
    wp_set_auth_cookie( $customer_id );

    wp_send_json_success( array( 'redirect' => wc_get_checkout_url() ) );
}

// 6. AJAX: LOGIN RÁPIDO (Opção A)
add_action( 'wp_ajax_sc_quick_login', 'sc_ajax_quick_login' );
add_action( 'wp_ajax_nopriv_sc_quick_login', 'sc_ajax_quick_login' );
function sc_ajax_quick_login() {
    check_ajax_referer( 'sc_gate_nonce', 'security' );

    $info = array();
    $info['user_login'] = sanitize_user( $_POST['username'] );
    $info['user_password'] = $_POST['password'];
    $info['remember'] = true;

    $user_signon = wp_signon( $info, false );

    if ( is_wp_error( $user_signon ) ) {
        wp_send_json_error( 'Usuário ou senha incorretos.' );
    } else {
        wp_send_json_success( array( 'redirect' => wc_get_checkout_url() ) );
    }
}

// 7. AJAX: SALVAR PESQUISA PÓS-VENDA
add_action( 'wp_ajax_sc_save_survey', 'sc_ajax_save_survey' );
add_action( 'wp_ajax_nopriv_sc_save_survey', 'sc_ajax_save_survey' );
function sc_ajax_save_survey() {
    $order_id     = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
    $source       = sanitize_text_field($_POST['source']);
    $expectations = sanitize_textarea_field($_POST['expectations']);

    if ( $order_id ) {
        update_post_meta( $order_id, '_survey_source', $source );
        update_post_meta( $order_id, '_survey_expectations', $expectations );
        
        // Também salvar no usuário para histórico futuro de marketing
        $user_id = get_post_field( 'post_author', $order_id );
        if ( $user_id ) {
            update_user_meta( $user_id, 'marketing_source', $source );
        }
        
        wp_send_json_success( 'Pesquisa salva com sucesso.' );
    }
    wp_send_json_error( 'ID do pedido inválido.' );
}
