<?php
/**
 * Saulo Coelho functions and definitions
 */

if ( ! function_exists( 'saulocoelho_setup' ) ) :
    function saulocoelho_setup() {
        // Add default posts and comments RSS feed links to head.
        add_theme_support( 'automatic-feed-links' );

        // Let WordPress manage the document title.
        add_theme_support( 'title-tag' );

        // Enable support for Post Thumbnails on posts and pages.
        add_theme_support( 'post-thumbnails' );

        // This theme uses wp_nav_menu() in one location.
        register_nav_menus( array(
            'menu-1' => esc_html__( 'Primary', 'saulocoelho' ),
            'footer-menu' => esc_html__( 'Footer Navigation', 'saulocoelho' ),
        ) );

        // Switch default core markup for search form, comment form, and comments to output valid HTML5.
        add_theme_support( 'html5', array(
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
        ) );

        // Add theme support for selective refresh for widgets.
        add_theme_support( 'customize-selective-refresh-widgets' );

        // Add WooCommerce support
        add_theme_support( 'woocommerce' );
    }
endif;
add_action( 'after_setup_theme', 'saulocoelho_setup' );

/**
 * Enqueue scripts and styles.
 */
function saulocoelho_scripts() {
    // Theme Stylesheet
    $theme_version = wp_get_theme()->get( 'Version' );
    wp_enqueue_style( 'saulocoelho-style', get_stylesheet_uri(), array(), $theme_version );

    // Inter (corpo/UI) + Playfair Display (títulos)
    wp_enqueue_style(
        'saulocoelho-fonts',
        'https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;1,400&display=swap',
        array(),
        null
    );

    // Material Symbols
    wp_enqueue_style( 'saulocoelho-material-symbols', 'https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap', array(), null );
}
add_action( 'wp_enqueue_scripts', 'saulocoelho_scripts' );

/**
 * Add Tailwind classes to menu links
 */
function saulocoelho_nav_menu_link_attributes( $atts, $item, $args ) {
    if ( isset( $args->theme_location ) && $args->theme_location === 'menu-1' ) {
        $atts['class'] = 'text-white !text-white hover:text-primary transition-colors font-bold uppercase text-xs tracking-widest';
    }
    return $atts;
}
add_filter( 'nav_menu_link_attributes', 'saulocoelho_nav_menu_link_attributes', 10, 3 );

/**
 * Custom Meta Boxes and Admin UX
 */
if ( file_exists( __DIR__ . '/inc/metaboxes.php' ) ) {
    require_once __DIR__ . '/inc/metaboxes.php';
}

/**
 * Módulo Alumni — Galerias de Turmas
 */
if ( file_exists( __DIR__ . '/inc/module-alumni.php' ) ) {
    require_once __DIR__ . '/inc/module-alumni.php';
}

/**
 * Gate de Checkout (Login/Cadastro antes do Checkout)
 */
if ( file_exists( __DIR__ . '/inc/module-checkout-gate.php' ) ) {
    require_once __DIR__ . '/inc/module-checkout-gate.php';
}

/**
 * Checkout: resumo de faturamento + checkout sem envio (serviço).
 */
if ( file_exists( __DIR__ . '/inc/module-checkout-billing-summary.php' ) ) {
    require_once __DIR__ . '/inc/module-checkout-billing-summary.php';
}

/**
 * Checkout: modal de erros + termos marcados por padrão.
 */
if ( file_exists( __DIR__ . '/inc/module-checkout-error-modal.php' ) ) {
    require_once __DIR__ . '/inc/module-checkout-error-modal.php';
}

function saulocoelho_admin_scripts($hook) {
    // Only load on post edit pages
    if ($hook !== 'post.php' && $hook !== 'post-new.php') return;
    
    wp_enqueue_media();
}
add_action('admin_enqueue_scripts', 'saulocoelho_admin_scripts');

/**
 * WooCommerce - Otimização de Fluxo de Vendas (Skip Cart)
 * Redireciona diretamente para o Checkout ao invés de enviar para o Carrinho.
 */
function saulocoelho_redirect_to_checkout_add_cart() {
    return wc_get_checkout_url();
}
add_filter( 'woocommerce_add_to_cart_redirect', 'saulocoelho_redirect_to_checkout_add_cart' );

// Remove a mensagem genérica de "Item adicionado ao carrinho" que aparece no topo da tela
add_filter( 'wc_add_to_cart_message_html', '__return_false' );

/**
 * WooCommerce - Permitir Editar Quantidade na Tabela de Checkout
 * Transforma o texto fixo "× 4" em um input type="number"
 */
function saulocoelho_checkout_qty_input( $quantity_html, $cart_item, $cart_item_key ) {
    $_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );

    // Se o produto for vendido individualmente 
    if ( $_product->is_sold_individually() ) {
        return sprintf( ' <strong class="product-quantity">&times;&nbsp;%s</strong>', $cart_item['quantity'] );
    }

    // Cria um input de quantidade do WooCommerce
    $input_html = woocommerce_quantity_input( array(
        'input_name'   => "cart[{$cart_item_key}][qty]",
        'input_value'  => $cart_item['quantity'],
        'max_value'    => $_product->get_max_purchase_quantity(),
        'min_value'    => '0', // Se zerar, ele vai remover o item
        'product_name' => $_product->get_name(),
    ), $_product, false );

    return '<div class="checkout-qty-wrapper mt-2" data-cart_item_key="' . esc_attr( $cart_item_key ) . '">' . $input_html . '</div>';
}
add_filter( 'woocommerce_checkout_cart_item_quantity', 'saulocoelho_checkout_qty_input', 10, 3 );

/**
 * EndPoint AJAX - Recebe a nova quantidade e recalcula o carrinho
 */
function saulocoelho_ajax_update_checkout_qty() {
    if ( isset( $_POST['cart_item_key'] ) && isset( $_POST['qty'] ) ) {
        $cart_item_key = sanitize_text_field( wp_unslash( $_POST['cart_item_key'] ) );
        $quantity      = absint( $_POST['qty'] );
        
        WC()->cart->set_quantity( $cart_item_key, $quantity, true );
        WC()->cart->calculate_totals();

        if ( WC()->cart->is_empty() ) {
            wp_send_json_success( array( 'empty_cart' => true, 'redirect_url' => wc_get_page_permalink( 'shop' ) ) );
        } else {
            wp_send_json_success();
        }
    }
    wp_die();
}
add_action( 'wp_ajax_saulocoelho_update_checkout_qty', 'saulocoelho_ajax_update_checkout_qty' );
add_action( 'wp_ajax_nopriv_saulocoelho_update_checkout_qty', 'saulocoelho_ajax_update_checkout_qty' );

/**
 * Injetar Javascript no Checkout para Capturar Mundanças e Disparar AJAX 
 */
function saulocoelho_checkout_qty_script() {
    if ( ! is_checkout() || is_wc_endpoint_url( 'order-pay' ) ) return;
    ?>
    <script>
    jQuery(document).ready(function($) {
        $(document).on('change', '.checkout-qty-wrapper input.qty', function(e) {
            e.preventDefault();
            
            var $wrapper = $(this).closest('.checkout-qty-wrapper');
            var qty = $(this).val();
            var cart_item_key = $wrapper.data('cart_item_key');

            // "Pausa" a tabela colocando o spinner nativo do Woo
            $('.woocommerce-checkout-payment, .woocommerce-checkout-review-order-table').block({
                message: null,
                overlayCSS: { background: '#fff', opacity: 0.6 }
            });

            // Envia para nosso EndPoint AJAX
            $.ajax({
                type: 'POST',
                url: wc_checkout_params.ajax_url,
                data: {
                    action: 'saulocoelho_update_checkout_qty',
                    cart_item_key: cart_item_key,
                    qty: qty
                },
                success: function(response) {
                    if (response.success) {
                        if (response.data && response.data.empty_cart) {
                            window.location.href = response.data.redirect_url;
                        } else {
                            // Essa linha chama as engrenagens do Woo
                            // ele mesmo busca a tabela "recalculada" na DOM via JS
                            $('body').trigger('update_checkout');
                        }
                    }
                }
            });
        });
    });
    </script>
    <style>
    /* Deixar o campo bonitinho para temas Dark Premium */
    .checkout-qty-wrapper input.qty {
        background: rgba(255,255,255,0.05) !important;
        border: 1px solid rgba(255,255,255,0.1) !important;
        color: white !important;
        padding: 4px 8px !important;
        border-radius: 8px !important;
        width: 70px !important;
        text-align: center;
        font-weight: bold;
        transition: all 0.3s ease;
    }
    .checkout-qty-wrapper input.qty:focus {
        border-color: #C5A059 !important;
        box-shadow: 0 0 10px rgba(197,160,89,0.3) !important;
    }
    .checkout-qty-wrapper { display: inline-block; margin-left: 10px; }
    
    /* Ocultar a linha de "País" inteira (Travado no Brasil) */
    #billing_country_field, #shipping_country_field {
        display: none !important;
    }
    </style>
    <?php
}
add_action( 'wp_footer', 'saulocoelho_checkout_qty_script' );

/**
 * Otimização de Formulário Checkout (Remoção de Fricção)
 * Remove 'Nome da Empresa' que é inútil para compras de info-produto padrão
 */
add_filter( 'woocommerce_checkout_fields', 'saulocoelho_optimize_checkout_fields', 99 );
function saulocoelho_optimize_checkout_fields( $fields ) {
    // Esconde itens irrelevantes para cursos
    unset( $fields['billing']['billing_company'] );
    unset( $fields['order']['order_comments'] ); // Remove Informação adicional
    
    // Se o cliente quiser CPF/CNPJ via plugin Claudio Sanches, deixamos livre.
    return $fields;
}

/**
 * Motor Turbo BR: Auto-Preenchimento Assíncrono do ViaCEP no Checkout
 * Independente de plugins terceiros para garantir funcionamento em 100% dos casos.
 */
function saulocoelho_viacep_checkout_script() {
    if ( ! is_checkout() || is_wc_endpoint_url( 'order-pay' ) ) return;
    ?>
    <script>
    jQuery(document).ready(function($) {
        // Escuta quando o usuário sai do campo de CEP ou digita o 8º número
        $('#billing_postcode').on('keyup blur', function(e) {
            var cep = $(this).val().replace(/\D/g, '');
            
            // Só dispara se tiver 8 números exatos
            if (cep.length === 8 && $(this).data('last_cep') !== cep) {
                $(this).data('last_cep', cep); // Evita duplicidade de requisição

                // Chamada na API do Governo (não preenche placeholders no formulário — evita falha de validação)
                $.getJSON('https://viacep.com.br/ws/' + cep + '/json/?callback=?', function(dados) {
                    if (!("erro" in dados)) {
                        // Injeta os dados nos campos nativos do WooCommerce e Plugin BR
                        $('#billing_address_1').val(dados.logradouro);
                        $('#billing_neighborhood').val(dados.bairro);
                        $('#billing_city').val(dados.localidade);
                        
                        // Atualiza Estado (Select2 do Woo)
                        $('#billing_state').val(dados.uf).trigger('change'); 
                        
                        // Joga o cursor piscando pro campo de Número, induzindo o fechamento!
                        setTimeout(function(){
                            $('#billing_number').focus();
                        }, 100);
                        
                    } else {
                        // Limpa se CEP for inválido
                        $('#billing_address_1, #billing_neighborhood, #billing_city').val('');
                    }
                });
            }
        });
    });
    </script>
    <?php
}
add_action( 'wp_footer', 'saulocoelho_viacep_checkout_script', 30 );

/**
 * Checkout: rejeita valores “placeholder” (--, Buscando rua..., etc.) que passam validação vazia.
 */
add_action( 'woocommerce_after_checkout_validation', 'saulocoelho_checkout_reject_placeholder_fields', 10, 2 );
function saulocoelho_checkout_reject_placeholder_fields( $data, $errors ) {
    // Apenas valores que “parecem” preenchidos mas são placeholders (evita duplicar erro de campo vazio do WC).
    $is_placeholder = function ( $val ) {
        $val = trim( wc_clean( (string) $val ) );
        if ( $val === '--' || $val === '...' || $val === '…' ) {
            return true;
        }
        if ( function_exists( 'mb_stripos' ) ) {
            if ( mb_stripos( $val, 'buscando', 0, 'UTF-8' ) !== false ) {
                return true;
            }
        } elseif ( stripos( $val, 'buscando' ) !== false ) {
            return true;
        }
        return false;
    };

    if ( $is_placeholder( $data['billing_last_name'] ?? '' ) ) {
        $errors->add( 'billing_last_name', __( 'Substitua o sobrenome por um valor válido (sem traços ou texto de carregamento).', 'saulocoelho' ) );
    }
    if ( $is_placeholder( $data['billing_address_1'] ?? '' ) ) {
        $errors->add( 'billing_address_1', __( 'Informe o logradouro válido (rua). Se usou CEP, aguarde a busca ou digite manualmente.', 'saulocoelho' ) );
    }
    if ( $is_placeholder( $data['billing_city'] ?? '' ) ) {
        $errors->add( 'billing_city', __( 'Informe a cidade corretamente (confira o CEP).', 'saulocoelho' ) );
    }
}

/**
 * Theme Customizer
 */
if ( file_exists( __DIR__ . '/inc/customizer.php' ) ) {
    require_once __DIR__ . '/inc/customizer.php';
}

/**
 * Módulo de Testemunhos e Prova Social
 */
if ( file_exists( __DIR__ . '/inc/module-testimonials.php' ) ) {
    require_once __DIR__ . '/inc/module-testimonials.php';
}

/**
 * Inscrições presenciais — gateway, questionário e painel admin (issue #3).
 */
if ( file_exists( __DIR__ . '/inc/module-presencial-enrollments.php' ) ) {
    require_once __DIR__ . '/inc/module-presencial-enrollments.php';
}

/**
 * Otimização de Blog - Filtragem de Categorias
 * Oculta as categorias selecionadas no Customizer do feed principal do blog.
 */
function saulocoelho_exclude_categories_blog( $query ) {
    if ( ! is_admin() && $query->is_main_query() && $query->is_home() ) {
        $exclude_ids = get_theme_mod( 'blog_exclude_categories' );
        if ( ! empty( $exclude_ids ) ) {
            $exclude_array = array_map( 'trim', explode( ',', $exclude_ids ) );
            $minus_ids = array_map( function($id) { return '-' . $id; }, $exclude_array );
            $query->set( 'cat', implode( ',', $minus_ids ) );
        }
    }
}
add_action( 'pre_get_posts', 'saulocoelho_exclude_categories_blog' );
