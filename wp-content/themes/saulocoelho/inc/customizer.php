<?php
/**
 * Saulo Coelho Theme Customizer
 * Centralizes all editable fields for the footer.
 */

function saulocoelho_customize_register( $wp_customize ) {
    
    // Add Footer Section
    $wp_customize->add_section( 'saulocoelho_footer_section', array(
        'title'    => __( 'Rodapé (Footer)', 'saulocoelho' ),
        'priority' => 120,
    ) );

    // 1. Footer Bio/Description
    $wp_customize->add_setting( 'footer_bio', array(
        'default'           => 'Saulo Coelho - Especialista em Desenvolvimento Humano e Estratégia Corporativa.',
        'sanitize_callback' => 'wp_kses_post',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( 'footer_bio', array(
        'label'    => __( 'Bio do Rodapé', 'saulocoelho' ),
        'section'  => 'saulocoelho_footer_section',
        'type'     => 'textarea',
    ) );

    // 2. Contact: Phone
    $wp_customize->add_setting( 'footer_phone', array(
        'default'           => '+55 (11) 99999-9999',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( 'footer_phone', array(
        'label'    => __( 'Telefone de Contato', 'saulocoelho' ),
        'section'  => 'saulocoelho_footer_section',
        'type'     => 'text',
    ) );

    // 3. Contact: Location
    $wp_customize->add_setting( 'footer_location', array(
        'default'           => 'São Paulo, SP',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( 'footer_location', array(
        'label'    => __( 'Localização/Endereço', 'saulocoelho' ),
        'section'  => 'saulocoelho_footer_section',
        'type'     => 'text',
    ) );

    // 4. Social: Instagram
    $wp_customize->add_setting( 'footer_social_instagram', array(
        'default'           => '#',
        'sanitize_callback' => 'esc_url_raw',
    ) );
    $wp_customize->add_control( 'footer_social_instagram', array(
        'label'    => __( 'Link Instagram', 'saulocoelho' ),
        'section'  => 'saulocoelho_footer_section',
        'type'     => 'url',
    ) );

    // 5. Social: LinkedIn
    $wp_customize->add_setting( 'footer_social_linkedin', array(
        'default'           => '#',
        'sanitize_callback' => 'esc_url_raw',
    ) );
    $wp_customize->add_control( 'footer_social_linkedin', array(
        'label'    => __( 'Link LinkedIn', 'saulocoelho' ),
        'section'  => 'saulocoelho_footer_section',
        'type'     => 'url',
    ) );

    // 6. Social: Email
    $wp_customize->add_setting( 'footer_email', array(
        'default'           => 'contato@saulocoelho.com.br',
        'sanitize_callback' => 'sanitize_email',
    ) );
    $wp_customize->add_control( 'footer_email', array(
        'label'    => __( 'E-mail de Contato', 'saulocoelho' ),
        'section'  => 'saulocoelho_footer_section',
        'type'     => 'email',
    ) );

    // 7. Copyright
    $wp_customize->add_setting( 'footer_copyright', array(
        'default'           => 'Saulo Coelho. Todos os direitos reservados.',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( 'footer_copyright', array(
        'label'    => __( 'Texto de Copyright', 'saulocoelho' ),
        'section'  => 'saulocoelho_footer_section',
        'type'     => 'text',
    ) );

    // 8. Legal: Privacy Policy
    $wp_customize->add_setting( 'footer_privacy_link', array(
        'default'           => '#',
        'sanitize_callback' => 'esc_url_raw',
    ) );
    $wp_customize->add_control( 'footer_privacy_link', array(
        'label'    => __( 'Link Política de Privacidade', 'saulocoelho' ),
        'section'  => 'saulocoelho_footer_section',
        'type'     => 'text',
    ) );

    // 9. Legal: Terms
    $wp_customize->add_setting( 'footer_terms_link', array(
        'default'           => '#',
        'sanitize_callback' => 'esc_url_raw',
    ) );
    $wp_customize->add_control( 'footer_terms_link', array(
        'label'    => __( 'Link Termos de Uso', 'saulocoelho' ),
        'section'  => 'saulocoelho_footer_section',
        'type'     => 'text',
    ) );

    // Add Blog Section
    $wp_customize->add_section( 'saulocoelho_blog_section', array(
        'title'    => __( 'Blog (Home e Posts)', 'saulocoelho' ),
        'priority' => 110,
    ) );

    // --- BLOG ARCHIVE (HOME) ---

    // 10. Blog Archive Title
    $wp_customize->add_setting( 'blog_archive_title', array(
        'default'           => 'Insights <span class="text-primary">&</span> Estratégia',
        'sanitize_callback' => 'wp_kses_post',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( 'blog_archive_title', array(
        'label'    => __( 'Título da Home do Blog', 'saulocoelho' ),
        'section'  => 'saulocoelho_blog_section',
        'type'     => 'textarea',
    ) );

    // 11. Blog Archive Description
    $wp_customize->add_setting( 'blog_archive_description', array(
        'default'           => 'Perspectivas exclusivas sobre liderança, alta performance e a construção de organizações antifrágeis por Saulo Coelho.',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( 'blog_archive_description', array(
        'label'    => __( 'Descrição da Home do Blog', 'saulocoelho' ),
        'section'  => 'saulocoelho_blog_section',
        'type'     => 'textarea',
    ) );

    // 11.1. Blog Exclude Categories
    $wp_customize->add_setting( 'blog_exclude_categories', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'blog_exclude_categories', array(
        'label'    => __( 'IDs de Categorias para Ocultar do Blog', 'saulocoelho' ),
        'description' => __( 'Insira os IDs das categorias que NÃO devem aparecer no feed principal, separados por vírgula (ex: 5,12). Use isso para ocultar a categoria "Imprensa", por exemplo.', 'saulocoelho' ),
        'section'  => 'saulocoelho_blog_section',
        'type'     => 'text',
    ) );

    // --- BLOG SINGLE (CTA) ---

    // 12. Blog CTA Subtitle
    $wp_customize->add_setting( 'blog_cta_subtitle', array(
        'default'           => 'Próximo Passo',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( 'blog_cta_subtitle', array(
        'label'    => __( 'CTA Blog: Sub-título', 'saulocoelho' ),
        'section'  => 'saulocoelho_blog_section',
        'type'     => 'text',
    ) );

    // 13. Blog CTA Title
    $wp_customize->add_setting( 'blog_cta_title', array(
        'default'           => 'Gostou deste Insight?',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( 'blog_cta_title', array(
        'label'    => __( 'CTA Blog: Título', 'saulocoelho' ),
        'section'  => 'saulocoelho_blog_section',
        'type'     => 'text',
    ) );

    // 14. Blog CTA Description
    $wp_customize->add_setting( 'blog_cta_description', array(
        'default'           => 'Leve este conhecimento para a prática com os nossos programas de mentoria e treinamento.',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( 'blog_cta_description', array(
        'label'    => __( 'CTA Blog: Descrição', 'saulocoelho' ),
        'section'  => 'saulocoelho_blog_section',
        'type'     => 'textarea',
    ) );

    // 15. CTA Button 1 Text
    $wp_customize->add_setting( 'blog_cta_btn1_text', array(
        'default'           => 'Ver Programas',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'blog_cta_btn1_text', array(
        'label'    => __( 'CTA Blog: Botão 1 (Texto)', 'saulocoelho' ),
        'section'  => 'saulocoelho_blog_section',
        'type'     => 'text',
    ) );

    // 16. CTA Button 1 URL
    $wp_customize->add_setting( 'blog_cta_btn1_url', array(
        'default'           => '/programas',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'blog_cta_btn1_url', array(
        'label'    => __( 'CTA Blog: Botão 1 (Link)', 'saulocoelho' ),
        'section'  => 'saulocoelho_blog_section',
        'type'     => 'text',
    ) );

    // 17. CTA Button 2 Text
    $wp_customize->add_setting( 'blog_cta_btn2_text', array(
        'default'           => 'Falar com Especialista',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'blog_cta_btn2_text', array(
        'label'    => __( 'CTA Blog: Botão 2 (Texto)', 'saulocoelho' ),
        'section'  => 'saulocoelho_blog_section',
        'type'     => 'text',
    ) );

    // 18. CTA Button 2 URL
    $wp_customize->add_setting( 'blog_cta_btn2_url', array(
        'default'           => '/contato',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'blog_cta_btn2_url', array(
        'label'    => __( 'CTA Blog: Botão 2 (Link)', 'saulocoelho' ),
        'section'  => 'saulocoelho_blog_section',
        'type'     => 'text',
    ) );

    // Add Contact Section
    $wp_customize->add_section( 'saulocoelho_contact_section', array(
        'title'    => __( 'Página de Contato', 'saulocoelho' ),
        'priority' => 115,
    ) );

    // Add WhatsApp Section
    $wp_customize->add_section( 'saulocoelho_whatsapp_section', array(
        'title'    => __( 'Botão WhatsApp Flutuante', 'saulocoelho' ),
        'priority' => 118,
    ) );

    // 23. WhatsApp: Enable
    $wp_customize->add_setting( 'whatsapp_enable', array(
        'default'           => false,
        'sanitize_callback' => 'saulocoelho_sanitize_checkbox',
    ) );
    $wp_customize->add_control( 'whatsapp_enable', array(
        'label'    => __( 'Ativar Botão Flutuante?', 'saulocoelho' ),
        'section'  => 'saulocoelho_whatsapp_section',
        'type'     => 'checkbox',
    ) );

    // 24. WhatsApp: Phone
    $wp_customize->add_setting( 'whatsapp_phone', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'whatsapp_phone', array(
        'label'    => __( 'Número do WhatsApp (com DDD)', 'saulocoelho' ),
        'description' => __( 'Ex: 11988887777 (apenas números)', 'saulocoelho' ),
        'section'  => 'saulocoelho_whatsapp_section',
        'type'     => 'text',
    ) );

    // 25. WhatsApp: Message
    $wp_customize->add_setting( 'whatsapp_message', array(
        'default'           => 'Olá! Gostaria de saber mais sobre os seus serviços.',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'whatsapp_message', array(
        'label'    => __( 'Mensagem Inicial', 'saulocoelho' ),
        'section'  => 'saulocoelho_whatsapp_section',
        'type'     => 'text',
    ) );

    // Checkbox sanitizer
    if ( ! function_exists( 'saulocoelho_sanitize_checkbox' ) ) {
        function saulocoelho_sanitize_checkbox( $checked ) {
            return ( ( isset( $checked ) && true === $checked ) ? true : false );
        }
    }

    // 19. Contact Hero Title
    $wp_customize->add_setting( 'contact_hero_title', array(
        'default'           => 'Prepare-se para o Próximo Nível',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( 'contact_hero_title', array(
        'label'    => __( 'Título do Hero', 'saulocoelho' ),
        'section'  => 'saulocoelho_contact_section',
        'type'     => 'text',
    ) );

    // 20. Contact Form Title
    $wp_customize->add_setting( 'contact_form_title', array(
        'default'           => 'Envie uma Mensagem',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( 'contact_form_title', array(
        'label'    => __( 'Título do Formulário', 'saulocoelho' ),
        'section'  => 'saulocoelho_contact_section',
        'type'     => 'text',
    ) );

    // 21. Contact Form Description
    $wp_customize->add_setting( 'contact_form_desc', array(
        'default'           => 'Preencha os campos abaixo para iniciarmos uma conversa estratégica sobre seus objetivos.',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( 'contact_form_desc', array(
        'label'    => __( 'Descrição do Formulário', 'saulocoelho' ),
        'section'  => 'saulocoelho_contact_section',
        'type'     => 'textarea',
    ) );

    // 22. Contact Form Shortcode
    $wp_customize->add_setting( 'contact_form_shortcode', array(
        'default'           => '',
        'sanitize_callback' => 'wp_kses_post',
    ) );
    $wp_customize->add_control( 'contact_form_shortcode', array(
        'label'    => __( 'Shortcode do Formulário (CF7, WPForms, etc)', 'saulocoelho' ),
        'section'  => 'saulocoelho_contact_section',
        'type'     => 'text',
        'description' => 'Cole aqui o shortcode (ex: [contact-form-7 id="..."])'
    ) );

    // Selective Refresh for better UX
    if ( isset( $wp_customize->selective_refresh ) ) {
        $wp_customize->selective_refresh->add_partial( 'contact_hero_title', array(
            'selector'        => '.contact-hero h1',
            'render_callback' => function() { return get_theme_mod( 'contact_hero_title' ); },
        ) );
        $wp_customize->selective_refresh->add_partial( 'blog_archive_title', array(
            'selector'        => 'section.relative h1',
            'render_callback' => function() { return get_theme_mod( 'blog_archive_title' ); },
        ) );
        $wp_customize->selective_refresh->add_partial( 'blog_cta_title', array(
            'selector'        => 'footer h2.text-3xl',
            'render_callback' => function() { return get_theme_mod( 'blog_cta_title' ); },
        ) );
        $wp_customize->selective_refresh->add_partial( 'footer_bio', array(
            'selector'        => 'footer p.max-w-xs',
            'render_callback' => function() { return get_theme_mod( 'footer_bio' ); },
        ) );
        $wp_customize->selective_refresh->add_partial( 'footer_copyright', array(
            'selector'        => 'footer .max-w-7xl.mt-20 p',
            'render_callback' => function() { 
                $text = get_theme_mod( 'footer_copyright', 'Saulo Coelho. Todos os direitos reservados.' );
                return '© ' . date('Y') . ' ' . $text; 
            },
        ) );
    }
}
add_action( 'customize_register', 'saulocoelho_customize_register' );
