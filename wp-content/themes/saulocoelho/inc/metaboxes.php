<?php
/**
 * Custom Meta Boxes for Saulo Coelho Theme
 * Provides a user-friendly interface for editing site-wide dynamic sections.
 */

/**
 * Register Meta Boxes
 */
function saulocoelho_register_metaboxes() {
    $post_id = isset($_GET['post']) ? (int) $_GET['post'] : (isset($_POST['post_ID']) ? (int) $_POST['post_ID'] : 0);
    if (!$post_id) return;

    $template = get_post_meta($post_id, '_wp_page_template', true);
    $is_front_page = ($post_id === (int) get_option('page_on_front'));

    // 1. Home Page Settings
    if ($is_front_page) {
        add_meta_box('home_settings', 'Configurações da Home (Hero & Destaques)', 'saulocoelho_render_home_metabox', 'page', 'normal', 'high');
    }

    // 2. About Page (Quem é)
    if ($template === 'page-about.php') {
        add_meta_box('about_settings', 'Configurações da Página Sobre (Quem é)', 'saulocoelho_render_about_metabox', 'page', 'normal', 'high');
    }

    // 3. Programs & Catalog
    if ($template === 'page-programs.php' || $template === 'template-catalog.php') {
        add_meta_box('programs_settings', 'Configurações da Vitrine de Programas', 'saulocoelho_render_programs_metabox', 'page', 'normal', 'high');
    }

    // 4. Store
    if ($template === 'page-store.php') {
        add_meta_box('store_settings', 'Configurações da Loja', 'saulocoelho_render_store_metabox', 'page', 'normal', 'high');
    }

    // 5. Course Detail (Sales Page)
    if ($template === 'page-course-detail.php' || get_post_type($post_id) === 'product') {
        add_meta_box('course_settings', 'Configurações da Página de Vendas', 'saulocoelho_render_course_metabox', ['page', 'product'], 'normal', 'high');
    }
}
add_action('add_meta_boxes', 'saulocoelho_register_metaboxes');

/**
 * RENDER FUNCTIONS
 */

// Home
function saulocoelho_render_home_metabox($post) {
    wp_nonce_field('saulocoelho_save_metabox', 'saulocoelho_nonce');
    $fields = [
        'hero_eyebrow' => 'Texto Superior (Eyebrow)',
        'hero_title' => 'Título Principal (Headline)',
        'hero_description' => 'Descrição',
        'hero_bg_image' => 'Imagem de Fundo Desktop',
        'hero_bg_image_mobile' => 'Imagem de Fundo Mobile (Opcional - Formato Retrato)',
        'hero_mobile_overlay' => ['type' => 'select', 'label' => 'Escurecimento da Imagem (Apenas Mobile)', 'options' => ['0' => 'Nenhum', '30' => 'Leve (30%)', '60' => 'Médio (60%)', '80' => 'Forte (80%)']],
        'hero_btn_1_text' => 'Botão 1: Texto',
        'hero_btn_1_link' => 'Botão 1: Link',
        'hero_btn_2_text' => 'Botão 2: Texto',
        'hero_btn_2_link' => 'Botão 2: Link',
        'trusted_label' => 'Título da Seção de Logos',
        'trusted_image_1' => 'Logo da Empresa 1',
        'trusted_image_2' => 'Logo da Empresa 2',
        'trusted_image_3' => 'Logo da Empresa 3',
        'trusted_image_4' => 'Logo da Empresa 4',
        'trusted_image_5' => 'Logo da Empresa 5',
        'trusted_image_6' => 'Logo da Empresa 6',
        'features_title' => 'Título de Autoridade',
        'features_description' => 'Descrição de Autoridade',
    ];
    saulocoelho_render_fields($post->ID, $fields);
}

// About
function saulocoelho_render_about_metabox($post) {
    wp_nonce_field('saulocoelho_save_metabox', 'saulocoelho_nonce');
    $fields = [
        'about_eyebrow' => 'Texto Superior (Selo)',
        'about_title_1' => 'Título Linha 1 (Branco)',
        'about_title_2' => 'Título Linha 2 (Azul)',
        'about_description' => 'Descrição Introdutória',
        'about_stat_1_number' => 'Estatística 1 (Número)',
        'about_stat_1_label' => 'Estatística 1 (Legenda)',
        'about_stat_2_number' => 'Estatística 2 (Número)',
        'about_stat_2_label' => 'Estatística 2 (Legenda)',
        'about_image' => 'Foto de Perfil',
    ];
    saulocoelho_render_fields($post->ID, $fields);

    echo '<hr><h3>Linha do Tempo (3 Marcos)</h3>';
    for ($i=1; $i<=3; $i++) {
        saulocoelho_render_group($post->ID, "about_milestone_$i", ['year' => 'Ano/Período', 'title' => 'Título', 'desc' => 'Descrição']);
    }
    
    echo '<hr><h3>Valores (3 Itens)</h3>';
    for ($i=1; $i<=3; $i++) {
        saulocoelho_render_group($post->ID, "about_value_$i", ['icon' => 'Ícone Material', 'title' => 'Título', 'desc' => 'Descrição']);
    }
}

// Programs
function saulocoelho_render_programs_metabox($post) {
    wp_nonce_field('saulocoelho_save_metabox', 'saulocoelho_nonce');
    
    $fields = [
        'programs_title_1' => 'Título Linha 1 (Branco)',
        'programs_title_2' => 'Título Linha 2 (Azul)',
        'programs_description' => 'Descrição da Seção',
    ];
    saulocoelho_render_fields($post->ID, $fields);

    echo '<hr><h3>Cards de Programas (Até 6)</h3>';
    for ($i=1; $i<=6; $i++) {
        saulocoelho_render_group($post->ID, "prog_card_$i", ['img' => 'Imagem de Capa', 'icon' => 'Ícone Material (Opcional)', 'title' => 'Título', 'desc' => 'Descrição', 'tag1' => 'Tag 1', 'tag2' => 'Tag 2', 'link' => 'Link do Botão']);
    }
}

// Store
function saulocoelho_render_store_metabox($post) {
    wp_nonce_field('saulocoelho_save_metabox', 'saulocoelho_nonce');
    echo '<h3>Produtos em Destaque (Até 6)</h3>';
    for ($i=1; $i<=6; $i++) {
        saulocoelho_render_group($post->ID, "store_prod_$i", ['img' => 'Imagem do Produto', 'badge' => 'Selo (ex: Bestseller)', 'title' => 'Nome do Produto', 'desc' => 'Breve Descrição', 'price' => 'Preço (Ex: R$ 97,00)', 'installments' => 'Parcelamento (Ex: 12x de)', 'link' => 'Link de Compra']);
    }
}

// Course Detail
function saulocoelho_render_course_metabox($post) {
    wp_nonce_field('saulocoelho_save_metabox', 'saulocoelho_nonce');
    $fields = [
        'course_type' => ['type' => 'select', 'label' => 'Modalidade do Curso', 'options' => ['online' => '100% Online', 'presencial' => 'Imersão Presencial']],
        'course_badge' => 'Selo do Topo (ex: Matrículas Abertas)',
        'course_video_url' => 'URL da Imagem/Capa do Vídeo',
        'course_actual_video_url' => 'URL Real do Vídeo Público (Opcional - YouTube/Vimeo)',
        'course_video_mode' => ['type' => 'select', 'label' => 'Modo de Reprodução do Vídeo', 'options' => ['inline' => 'Substituir a Imagem e Tocar na Caixa (Padrão)', 'lightbox' => 'Abrir em Tela Cheia (Janela Pop-up)']],
        'course_stat_1' => 'Destaque 1 (ex: 10k+ Alunos)',
        'course_stat_2' => 'Destaque 2 (ex: 4.9/5 Avaliação)',
        'course_price_install' => 'Valor da Parcela (ex: 12x de R$ 97,00)',
        'course_event_location' => '🏢 Local do Evento (P/ Presencial)',
        'course_event_dates' => '📅 Datas e Horários (Ex: 15 e 16 de Maio das 09h às 18h)',
        'course_event_dresscode' => '👗 Dresscode / Avisos (Para Presencial)',
        'course_learning_title' => 'Título da Seção de Aprendizado (Ex: O que você vai aprender)',
        'course_learning_subtitle_desc' => 'Subtítulo do Aprendizado (Ex: Conteúdo estruturado...)',
        'course_learning_mode' => ['type' => 'select', 'label' => 'Modo de Exibição do Aprendizado', 'options' => ['modules' => 'Separado por Módulos (Preencha os cards abaixo)', 'freetext' => 'Texto Livre (Ideal para formato Presencial)']],
        'course_learning_freetext_desc' => 'Texto Livre (Insira toda a ementa/cronograma aqui, se usou a opção Texto Livre acima)',
        'course_benefits_title' => 'Seção Bônus 1: Título de Benefícios (Ex: Quais os Benefícios? - Deixe vazio p/ ocultar)',
        'course_benefits_desc' => 'Texto de Benefícios (Lado Esquerdo)',
        'course_benefits_media_url' => 'Mídia de Benefícios (Lado Direito - Envie link de Imagem ou YouTube/Vimeo)',
    ];
    saulocoelho_render_fields($post->ID, $fields);

    echo '<hr><h3>O Que Está Incluso? (Checklist de Benefícios / Materiais)</h3>';
    for ($i=1; $i<=6; $i++) {
        saulocoelho_render_group($post->ID, "course_inc_$i", ['title' => 'Item Incluso (Ex: Apostila Impressa)']);
    }
    
    echo '<hr><h3>O Que NÃO Está Incluso? (Para Presencial)</h3>';
    for ($i=1; $i<=3; $i++) {
        saulocoelho_render_group($post->ID, "course_notinc_$i", ['title' => 'Item Não Incluso (Ex: Hospedagem)']);
    }

    echo '<hr><h3>Módulos do Curso (Apenas para cursos que possuem divisão de conteúdo)</h3>';
    for ($i=1; $i<=8; $i++) {
        saulocoelho_render_group($post->ID, "course_mod_$i", ['title' => 'Título do Módulo', 'desc' => 'Descrição do Módulo']);
    }
}

/**
 * HELPER RENDERERS
 */

function saulocoelho_render_fields($post_id, $fields) {
    foreach ($fields as $key => $field) {
        $val = get_post_meta($post_id, $key, true);
        
        $is_array = is_array($field);
        $label = $is_array ? $field['label'] : $field;
        $type = $is_array ? ($field['type'] ?? 'text') : 'text';

        echo '<p><label><strong>' . esc_html($label) . ':</strong></label><br>';
        
        if ($type === 'select') {
             echo '<select name="' . $key . '" style="width: 100%; max-width: 400px; padding: 5px;">';
             foreach($field['options'] as $opt_val => $opt_label) {
                 $sel = ($val == $opt_val) ? 'selected' : '';
                 echo '<option value="'.esc_attr($opt_val).'" '.$sel.'>'.esc_html($opt_label).'</option>';
             }
             echo '</select>';
        }
        else if (strpos($key, 'desc') !== false || strpos($key, 'title') !== false || strpos($key, 'dresscode') !== false || strpos($key, 'dates') !== false) {
            echo '<textarea name="' . $key . '" rows="2" class="large-text">' . esc_textarea($val) . '</textarea>';
        } elseif (strpos($key, 'image') !== false || strpos($key, 'img') !== false || strpos($key, 'url') !== false) {
            echo '<input type="text" name="' . $key . '" id="' . $key . '" value="' . esc_url($val) . '" class="regular-text"> ';
            echo '<button type="button" class="button button-secondary media-uploader" data-target="#' . $key . '">Biblioteca</button>';
        } else {
            echo '<input type="text" name="' . $key . '" value="' . esc_attr($val) . '" class="large-text">';
        }
        echo '</p>';
    }
}

function saulocoelho_render_group($post_id, $prefix, $fields) {
    echo "<div style='background:#f9f9f9; padding:15px; margin-bottom:15px; border: 1px solid #eee; border-radius:8px;'>";
    echo "<strong>" . strtoupper(str_replace('_', ' ', $prefix)) . "</strong><br><br>";
    foreach ($fields as $key => $label) {
        $field_name = $prefix . '_' . $key;
        $val = get_post_meta($post_id, $field_name, true);
        echo "<label style='font-size:11px; color:#666'>".esc_html($label)."</label><br>";
        if ($key === 'desc') {
            echo "<textarea name='$field_name' rows='2' style='width:100%'>".esc_textarea($val)."</textarea>";
        } elseif ($key === 'img' || $key === 'image') {
            echo "<input type='text' name='$field_name' id='$field_name' value='".esc_url($val)."' style='width:70%'> ";
            echo "<button type='button' class='button button-small media-uploader' data-target='#$field_name'>Midia</button>";
        } elseif ($key === 'icon') {
            $icons = [
                '' => '- Selecione um Ícone -',
                'visibility' => 'Visão Sistêmica (Olho)',
                'psychology' => 'Liderança / Mente (Cérebro)',
                'group' => 'Equipe / Integração (Pessoas)',
                'target' => 'Foco / Objetivo (Alvo)',
                'lightbulb' => 'Ideia / Inovação (Lâmpada)',
                'trending_up' => 'Crescimento (Gráfico)',
                'star' => 'Excelência (Estrela)',
                'military_tech' => 'Autoridade (Medalha)',
                'handshake' => 'Parceria / Acordo (Aperto de Mão)',
                'rocket_launch' => 'Lançamento / Aceleração (Foguete)',
                'shield_locked' => 'Segurança / Integridade (Escudo)',
                'work' => 'Profissionalismo (Maleta)',
                'bolt' => 'Energia / Agilidade (Raio)',
                'public' => 'Alcance Global / Mundo (Globo)',
                'balance' => 'Equilíbrio / Justiça (Balança)',
                'diamond' => 'Valor / Premium (Diamante)',
                'school' => 'Educação / Formação (Capelo)',
                'menu_book' => 'Conhecimento (Livro)',
                'flag' => 'Meta / Marco (Bandeira)',
                'verified' => 'Verificado / Check',
                'forum' => 'Comunicação / Chat (Balões)',
                'emoji_events' => 'Vitória / Troféu'
            ];
            echo "<select name='$field_name' style='width:100%; margin-bottom:8px;'>";
            // Check if current value exists in our list. If not and not empty, add it temporarily so it's not lost.
            if ($val && !array_key_exists($val, $icons)) {
                echo "<option value='".esc_attr($val)."' selected>Personalizado: ".esc_html($val)."</option>";
            }
            foreach ($icons as $icon_val => $icon_label) {
                $sel = ($val == $icon_val) ? 'selected' : '';
                echo "<option value='".esc_attr($icon_val)."' $sel>".esc_html($icon_label)."</option>";
            }
            echo "</select>";
        } else {
            echo "<input type='text' name='$field_name' value='".esc_attr($val)."' style='width:100%; margin-bottom:8px;'>";
        }
    }
    echo "</div>";
}

/**
 * Common Scripts
 */
function saulocoelho_render_metabox_js() {
    static $done = false; if ($done) return; $done = true;
    ?>
    <script>
    jQuery(document).ready(function($){
        $(document).on('click', '.media-uploader', function(e) {
            e.preventDefault();
            var target = $(this).data('target');
            var image = wp.media({ title: 'Escolher Imagem', multiple: false }).open()
            .on('select', function(e){
                var uploaded_image = image.state().get('selection').first();
                $(target).val(uploaded_image.toJSON().url);
            });
        });
    });
    </script>
    <?php
}
add_action('admin_footer', 'saulocoelho_render_metabox_js');

/**
 * Save Data
 */
function saulocoelho_save_metaboxes($post_id) {
    if (!isset($_POST['saulocoelho_nonce']) || !wp_verify_nonce($_POST['saulocoelho_nonce'], 'saulocoelho_save_metabox')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_page', $post_id)) return;

    // Loop through all POST data and save keys starting with our prefixes
    $prefixes = ['hero_', 'trusted_', 'features_', 'about_', 'prog_', 'store_', 'course_', 'programs_'];
    foreach ($_POST as $key => $value) {
        $should_save = false;
        foreach ($prefixes as $p) {
            if (strpos($key, $p) === 0) { $should_save = true; break; }
        }
        if ($should_save) {
            update_post_meta($post_id, $key, wp_kses_post($value));
        }
    }
}
add_action('save_post', 'saulocoelho_save_metaboxes');
