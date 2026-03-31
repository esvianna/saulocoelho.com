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

    // Selective Refresh for better UX
    if ( isset( $wp_customize->selective_refresh ) ) {
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
