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

function saulocoelho_admin_head() {
    echo '<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />';
    echo '<style>
        .icon-modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.8); z-index: 99999; backdrop-filter: blur(4px); align-items: center; justify-content: center; }
        .icon-modal-content { background: #fff; width: 90%; max-width: 800px; max-height: 80vh; border-radius: 12px; display: flex; flex-direction: column; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5); }
        .icon-modal-header { padding: 20px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; }
        .icon-modal-body { padding: 20px; overflow-y: auto; display: grid; grid-template-columns: repeat(auto-fill, minmax(80px, 1fr)); gap: 10px; }
        .icon-item { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 15px 5px; border-radius: 8px; cursor: pointer; border: 1px solid transparent; transition: all 0.2s; text-align: center; }
        .icon-item:hover { background: #f0f6fc; border-color: #007cba; color: #007cba; }
        .icon-item .material-symbols-outlined { font-size: 32px; margin-bottom: 8px; }
        .icon-item span.label { font-size: 10px; opacity: 0.7; pointer-events: none; word-break: break-all; }
        .preview-icon-box { width: 40px; height: 40px; display: inline-flex; align-items: center; justify-content: center; background: #eee; border-radius: 6px; border: 1px solid #ddd; vertical-align: middle; margin-right: 10px; overflow: hidden; }
        .preview-icon-box img { max-width: 100%; max-height: 100%; object-fit: contain; }
    </style>';
}
add_action('admin_head', 'saulocoelho_admin_head');

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

    echo '<hr><h3>Linha do Tempo (Marcos da Carreira)</h3>';
    $ms_title = get_post_meta($post->ID, 'about_milestones_title', true);
    if (empty($ms_title) && !metadata_exists('post', $post->ID, 'about_milestones_title')) {
        $ms_title = 'Trajetória Institucional'; // Default
    }
    echo '<p><label><strong>Título da Seção de Linha do Tempo:</strong></label><br>';
    echo '<input type="text" name="about_milestones_title" value="' . esc_attr($ms_title) . '" class="large-text"></p>';
    
    echo '<p style="color:#666; font-size:12px; margin-top:5px;">Adicione quantos marcos desejar.</p>';
    echo '<input type="hidden" name="about_milestones_present" value="1">';
    echo '<div id="about_milestones_container">';
    
    $milestones = get_post_meta($post->ID, 'about_milestones', true);
    // Backward compatibility loading old 3 items if new repeater is empty and has never been saved
    if (empty($milestones) && !metadata_exists('post', $post->ID, 'about_milestones')) {
        $milestones = [];
        for ($i = 1; $i <= 3; $i++) {
            $year = get_post_meta($post->ID, "about_milestone_{$i}_year", true);
            if ($year) {
                $icons = ['history_edu', 'trending_up', 'verified'];
                $milestones[] = [
                    'year' => $year,
                    'title' => get_post_meta($post->ID, "about_milestone_{$i}_title", true),
                    'desc' => get_post_meta($post->ID, "about_milestone_{$i}_desc", true),
                    'icon' => $icons[$i-1] ?? 'verified'
                ];
            }
        }
    }
    if (!is_array($milestones)) $milestones = [];
    
    foreach ($milestones as $index => $ms) {
        $year = esc_attr($ms['year'] ?? '');
        $title = esc_attr($ms['title'] ?? '');
        $desc = esc_textarea($ms['desc'] ?? '');
        $icon = esc_attr($ms['icon'] ?? 'history_edu');
        
        echo "<div class='milestone-row' style='background:#f9f9f9; padding:15px; margin-bottom:15px; border: 1px solid #eee; border-radius:8px; position:relative;'>";
        // Icon picker (using the general icon modal)
        echo "<label style='font-size:11px; color:#666'>Ícone do Marco (Ícones Mágicos)</label><br>";
        echo "<div style='display:flex; align-items:center; margin-bottom:15px; margin-top:5px;'>";
        echo "<div class='preview-icon-box' id='preview_ms_icon_$index'>";
        if ($icon) {
            echo "<span class='material-symbols-outlined' style='font-variation-settings: \"FILL\" 1;'>$icon</span>";
        }
        echo "</div>";
        echo "<input type='text' name='about_milestones[$index][icon]' id='ms_icon_$index' value='$icon' style='width:50%; margin-right:5px;'> ";
        echo "<button type='button' class='button button-small js-open-icon-modal' data-target='#ms_icon_$index' data-preview='#preview_ms_icon_$index' style='margin-right:5px;'>Ícones Mágicos</button>";
        echo "</div>";
        
        // Year, Title, Desc
        echo "<label style='font-size:11px; color:#666'>Ano/Período</label><br>";
        echo "<input type='text' name='about_milestones[$index][year]' value='$year' style='width:100%; margin-bottom:8px;'>";
        echo "<label style='font-size:11px; color:#666'>Título do Marco</label><br>";
        echo "<input type='text' name='about_milestones[$index][title]' value='$title' style='width:100%; margin-bottom:8px;'>";
        echo "<label style='font-size:11px; color:#666'>Breve Descrição</label><br>";
        echo "<textarea name='about_milestones[$index][desc]' rows='2' style='width:100%; margin-bottom:8px;'>$desc</textarea><br>";
        
        echo "<button type='button' class='button js-remove-milestone' style='color:#a00; border-color:#a00;'>Remover Marco</button>";
        echo "</div>";
    }
    echo '</div>';
    echo '<button type="button" class="button button-primary js-add-milestone">+ Adicionar Novo Marco</button><br><br>';
    
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
    
    echo '<h3>1. Configurações Iniciais (Hero e Preço)</h3>';
    $fields_hero = [
        'course_type' => ['type' => 'select', 'label' => 'Modalidade do Curso', 'options' => ['online' => '100% Online', 'presencial' => 'Imersão Presencial']],
        'course_badge' => 'Selo do Topo (ex: Matrículas Abertas)',
        'course_sec_btn_text' => 'Botão Secundário: Texto (Ex: Ver currículo - vazio p/ ocultar)',
        'course_sec_btn_link' => 'Botão Secundário: Link (Ex: #conteudo)',
        'course_video_url' => 'URL da Imagem/Capa do Vídeo',
        'course_actual_video_url' => 'URL Real do Vídeo Público (Opcional - YouTube/Vimeo)',
        'course_video_mode' => ['type' => 'select', 'label' => 'Modo de Reprodução do Vídeo', 'options' => ['inline' => 'Substituir a Imagem e Tocar na Caixa (Padrão)', 'lightbox' => 'Abrir em Tela Cheia (Janela Pop-up)']],
        'course_price_install' => 'Valor da Parcela (ex: 12x de R$ 97,00)',
    ];
    saulocoelho_render_fields($post->ID, $fields_hero);

    echo '<hr><h3>2. Estatísticas de Destaque</h3>';
    $fields_stats = [
        'course_stat_1' => 'Destaque 1 (ex: 10k+ Alunos)',
        'course_stat_2' => 'Destaque 2 (ex: 4.9/5 Avaliação)',
    ];
    saulocoelho_render_fields($post->ID, $fields_stats);

    echo '<hr><h3>3. O Que Você Vai Aprender (Currículo)</h3>';
    $fields_learning = [
        'course_learning_title' => 'Título da Seção de Aprendizado (Ex: O que você vai aprender)',
        'course_learning_subtitle_desc' => 'Subtítulo do Aprendizado (Ex: Conteúdo estruturado...)',
        'course_learning_mode' => ['type' => 'select', 'label' => 'Modo de Exibição do Aprendizado', 'options' => ['modules' => 'Separado por Módulos (Preencha os cards abaixo)', 'freetext' => 'Texto Livre (Ideal para formato Presencial)']],
        'course_learning_freetext_desc' => 'Texto Livre (Insira toda a ementa/cronograma aqui, se usou a opção Texto Livre acima)',
    ];
    saulocoelho_render_fields($post->ID, $fields_learning);

    echo '<hr><h3>4. CTA Intermediário 1 (Pós-Currículo)</h3>';
    $fields_cta1 = [
        'course_mid_cta_1_text' => 'Texto de Apoio (Ex: Preparado para dominar essas habilidades?)',
        'course_mid_cta_1_btn' => 'Texto do Botão (Ex: Quero começar agora - Deixe vazio para ocultar este CTA)',
        'course_mid_cta_1_link' => 'Link do Botão (Ex: #checkout)',
    ];
    saulocoelho_render_fields($post->ID, $fields_cta1);

    echo '<hr><h3>5. Seção de Benefícios (Vídeo Emocional)</h3>';
    $fields_benefits = [
        'course_benefits_title' => 'Título de Benefícios (Ex: Quais os Benefícios? - Deixe vazio p/ ocultar essa seção)',
        'course_benefits_desc' => 'Texto de Benefícios (Lado Esquerdo)',
        'course_benefits_media_url' => 'Mídia de Benefícios (Lado Direito - Envie link de Imagem ou YouTube/Vimeo)',
    ];
    saulocoelho_render_fields($post->ID, $fields_benefits);

    echo '<hr><h3>6. CTA Intermediário 2 (Pós-Benefícios)</h3>';
    $fields_cta2 = [
        'course_mid_cta_2_text' => 'Texto Forte (Ex: Você merece dar esse próximo passo na sua carreira.)',
        'course_mid_cta_2_btn' => 'Texto do Botão (Ex: Sim, eu quero garantir minha vaga - Deixe vazio p/ ocultar)',
        'course_mid_cta_2_link' => 'Link do Botão (Ex: #checkout)',
    ];
    saulocoelho_render_fields($post->ID, $fields_cta2);

    echo '<hr><h3>Tópicos de Aprendizado (Lista Lateral para acompanhar o Texto Livre da seção)</h3>';
    echo '<p style="color:#666; font-size:12px; margin-top:-10px;">Adicione quantos tópicos desejar. Eles aparecerão na seção "O que você vai aprender".</p>';
    echo '<input type="hidden" name="course_learning_topics_present" value="1">';
    echo '<div id="course_topics_container">';
    $topics = get_post_meta($post->ID, 'course_learning_topics', true);
    if (!is_array($topics)) $topics = [];

    foreach ($topics as $index => $topic) {
        $icon = esc_attr($topic['icon'] ?? '');
        $text = esc_attr($topic['text'] ?? '');
        $desc = esc_textarea($topic['desc'] ?? '');
        echo "<div class='topic-row' style='background:#f9f9f9; padding:15px; margin-bottom:15px; border: 1px solid #eee; border-radius:8px; position:relative;'>";
        echo "<label style='font-size:11px; color:#666'>Ícone (Visual / Mídia)</label><br>";
        echo "<div style='display:flex; align-items:center; margin-bottom:15px; margin-top:5px;'>";
        echo "<div class='preview-icon-box' id='preview_topic_icon_$index'>";
        if ($icon) {
            if (strpos($icon, 'http') === 0 || strpos($icon, '/') === 0) {
                echo "<img src='$icon' />";
            } else {
                echo "<span class='material-symbols-outlined' style='font-variation-settings: \"FILL\" 1;'>$icon</span>";
            }
        }
        echo "</div>";
        echo "<input type='text' name='course_learning_topics[$index][icon]' id='topic_icon_$index' value='$icon' style='width:50%; margin-right:5px;'> ";
        echo "<button type='button' class='button button-small js-open-icon-modal' data-target='#topic_icon_$index' data-preview='#preview_topic_icon_$index' style='margin-right:5px;'>Ícones Mágicos</button>";
        echo "<button type='button' class='button button-small media-uploader' data-target='#topic_icon_$index' data-preview='#preview_topic_icon_$index'>Subir Imagem</button>";
        echo "</div>";
        echo "<label style='font-size:11px; color:#666'>Título do Tópico</label><br>";
        echo "<input type='text' name='course_learning_topics[$index][text]' value='$text' style='width:100%; margin-bottom:8px;'>";
        echo "<label style='font-size:11px; color:#666'>Descrição (Opcional)</label><br>";
        echo "<textarea name='course_learning_topics[$index][desc]' rows='2' style='width:100%; margin-bottom:8px;'>$desc</textarea><br>";
        
        echo "<button type='button' class='button js-remove-topic' style='color:#a00; border-color:#a00;'>Remover Tópico</button>";
        echo "</div>";
    }
    echo '</div>';
    echo '<button type="button" class="button button-primary js-add-topic">+ Adicionar Tópico</button><br><br>';

    echo '<hr><h3>O Que Está Incluso? (Checklist de Benefícios / Materiais)</h3>';
    for ($i=1; $i<=6; $i++) {
        saulocoelho_render_group($post->ID, "course_inc_$i", ['title' => 'Item Incluso (Ex: Apostila Impressa)']);
    }
    
    echo '<hr><h3>O Que NÃO Está Incluso? (Para Presencial)</h3>';
    for ($i=1; $i<=3; $i++) {
        saulocoelho_render_group($post->ID, "course_notinc_$i", ['title' => 'Item Não Incluso (Ex: Hospedagem)']);
    }

    echo '<hr><h3>Logística do Evento (Para Presencial)</h3>';
    $logistics_fields = [
        'course_event_section_title' => 'Título da Seção de Evento (Ex: Logística do Evento)',
        'course_event_location' => '🏢 Local do Evento (P/ Presencial)',
        'course_event_dates' => '📅 Datas e Horários (Ex: 15 e 16 de Maio das 09h às 18h)',
        'course_event_dresscode' => '👗 Dresscode / Avisos (Para Presencial)',
    ];
    saulocoelho_render_fields($post->ID, $logistics_fields);

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
        // Modal HTML creation
        $('body').append(`
        <div class="icon-modal-overlay" id="custom-icon-modal">
            <div class="icon-modal-content">
                <div class="icon-modal-header">
                    <h2 style="margin:0;">Catálogo de Ícones</h2>
                    <button type="button" class="button js-close-icon-modal">Fechar</button>
                </div>
                <div class="icon-modal-body" id="icon-modal-grid"></div>
            </div>
        </div>
        `);

        // Popular icons list
        var iconList = [
            'visibility', 'psychology', 'group', 'target', 'lightbulb', 'trending_up', 'star', 'military_tech',
            'handshake', 'rocket_launch', 'shield_locked', 'work', 'bolt', 'public', 'balance', 'diamond',
            'school', 'menu_book', 'flag', 'verified', 'forum', 'emoji_events', 'check_circle', 'cancel',
            'arrow_forward', 'location_on', 'calendar_month', 'info', 'play_circle', 'assignment_return',
            'verified_user', 'video_library', 'task_alt', 'auto_awesome', 'workspace_premium', 'mindfulness',
            'psychiatry', 'healing', 'self_improvement', 'hub', 'diversity_3', 'thumb_up'
        ];
        
        var iconGridHtml = '';
        iconList.forEach(function(iconName) {
            iconGridHtml += '<div class="icon-item" data-icon="'+iconName+'"><span class="material-symbols-outlined" style="font-variation-settings: \\\'FILL\\\' 1;">'+iconName+'</span><span class="label">'+iconName+'</span></div>';
        });
        $('#icon-modal-grid').html(iconGridHtml);

        var currentIconTarget = null;
        var currentPreviewTarget = null;

        $(document).on('click', '.js-open-icon-modal', function(e){
            e.preventDefault();
            currentIconTarget = $(this).data('target');
            currentPreviewTarget = $(this).data('preview');
            $('#custom-icon-modal').css('display', 'flex').hide().fadeIn(200);
        });

        $(document).on('click', '.js-close-icon-modal, .icon-modal-overlay', function(e){
            if (e.target !== this) return;
            $('#custom-icon-modal').fadeOut(200);
        });

        $(document).on('click', '.icon-item', function(e){
            var selectedIcon = $(this).data('icon');
            if (currentIconTarget) {
                $(currentIconTarget).val(selectedIcon);
            }
            if (currentPreviewTarget) {
                $(currentPreviewTarget).html('<span class="material-symbols-outlined" style="font-variation-settings: \\\'FILL\\\' 1;">'+selectedIcon+'</span>');
            }
            $('#custom-icon-modal').fadeOut(200);
        });

        $(document).on('click', '.media-uploader', function(e) {
            e.preventDefault();
            var target = $(this).data('target');
            var preview = $(this).data('preview');
            var image = wp.media({ title: 'Escolher Imagem', multiple: false }).open()
            .on('select', function(e){
                var uploaded_image = image.state().get('selection').first();
                var uri = uploaded_image.toJSON().url;
                $(target).val(uri);
                if (preview && $(preview).length) {
                    $(preview).html('<img src="'+uri+'" />');
                }
            });
        });

        // Repeater Logic for Milestones
        var msIndex = $('#about_milestones_container .milestone-row').length;
        $('.js-add-milestone').on('click', function(e){
            e.preventDefault();
            var html = '<div class="milestone-row" style="background:#f9f9f9; padding:15px; margin-bottom:15px; border: 1px solid #eee; border-radius:8px; position:relative;">' +
                       '<label style="font-size:11px; color:#666">Ícone do Marco (Ícones Mágicos)</label><br>' +
                       '<div style="display:flex; align-items:center; margin-bottom:15px; margin-top:5px;">' +
                       '<div class="preview-icon-box" id="preview_ms_icon_'+msIndex+'"></div>' +
                       '<input type="text" name="about_milestones['+msIndex+'][icon]" id="ms_icon_'+msIndex+'" value="history_edu" style="width:50%; margin-right:5px;"> ' +
                       '<button type="button" class="button button-small js-open-icon-modal" data-target="#ms_icon_'+msIndex+'" data-preview="#preview_ms_icon_'+msIndex+'" style="margin-right:5px;">Ícones Mágicos</button>' +
                       '</div>' +
                       '<label style="font-size:11px; color:#666">Ano/Período</label><br>' +
                       '<input type="text" name="about_milestones['+msIndex+'][year]" value="" style="width:100%; margin-bottom:8px;">' +
                       '<label style="font-size:11px; color:#666">Título do Marco</label><br>' +
                       '<input type="text" name="about_milestones['+msIndex+'][title]" value="" style="width:100%; margin-bottom:8px;">' +
                       '<label style="font-size:11px; color:#666">Breve Descrição</label><br>' +
                       '<textarea name="about_milestones['+msIndex+'][desc]" rows="2" style="width:100%; margin-bottom:8px;"></textarea><br>' +
                       '<button type="button" class="button js-remove-milestone" style="color:#a00; border-color:#a00;">Remover Marco</button>' +
                       '</div>';
            $('#about_milestones_container').append(html);
            // Default preview update
            $('#preview_ms_icon_'+msIndex).html('<span class="material-symbols-outlined" style="font-variation-settings: \\\'FILL\\\' 1;">history_edu</span>');
            msIndex++;
        });

        $(document).on('click', '.js-remove-milestone', function(e){
            e.preventDefault();
            if(confirm('Remover este marco cronológico?')) {
                $(this).closest('.milestone-row').remove();
            }
        });

        // Repeater Logic for Topics
        var topicIndex = $('#course_topics_container .topic-row').length;
        $('.js-add-topic').on('click', function(e){
            e.preventDefault();
            var html = '<div class="topic-row" style="background:#f9f9f9; padding:15px; margin-bottom:15px; border: 1px solid #eee; border-radius:8px; position:relative;">' +
                       '<label style="font-size:11px; color:#666">Ícone (Visual / Mídia)</label><br>' +
                       '<div style="display:flex; align-items:center; margin-bottom:15px; margin-top:5px;">' +
                       '<div class="preview-icon-box" id="preview_topic_icon_'+topicIndex+'"></div>' +
                       '<input type="text" name="course_learning_topics['+topicIndex+'][icon]" id="topic_icon_'+topicIndex+'" value="" style="width:50%; margin-right:5px;"> ' +
                       '<button type="button" class="button button-small js-open-icon-modal" data-target="#topic_icon_'+topicIndex+'" data-preview="#preview_topic_icon_'+topicIndex+'" style="margin-right:5px;">Ícones Mágicos</button>' +
                       '<button type="button" class="button button-small media-uploader" data-target="#topic_icon_'+topicIndex+'" data-preview="#preview_topic_icon_'+topicIndex+'">Subir Imagem</button>' +
                       '</div>' +
                       '<label style="font-size:11px; color:#666">Título do Tópico</label><br>' +
                       '<input type="text" name="course_learning_topics['+topicIndex+'][text]" value="" style="width:100%; margin-bottom:8px;">' +
                       '<label style="font-size:11px; color:#666">Descrição (Opcional)</label><br>' +
                       '<textarea name="course_learning_topics['+topicIndex+'][desc]" rows="2" style="width:100%; margin-bottom:8px;"></textarea><br>' +
                       '<button type="button" class="button js-remove-topic" style="color:#a00; border-color:#a00;">Remover Tópico</button>' +
                       '</div>';
            $('#course_topics_container').append(html);
            topicIndex++;
        });

        $(document).on('click', '.js-remove-topic', function(e){
            e.preventDefault();
            if(confirm('Remover este tópico?')) {
                $(this).closest('.topic-row').remove();
            }
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

    // Handle array deletions if they were completely removed by the user in the DOM
    if (isset($_POST['course_learning_topics_present']) && !isset($_POST['course_learning_topics'])) {
        delete_post_meta($post_id, 'course_learning_topics');
    }
    if (isset($_POST['about_milestones_present']) && !isset($_POST['about_milestones'])) {
        delete_post_meta($post_id, 'about_milestones');
    }

    // Loop through all POST data and save keys starting with our prefixes
    $prefixes = ['hero_', 'trusted_', 'features_', 'about_', 'prog_', 'store_', 'course_', 'programs_'];
    foreach ($_POST as $key => $value) {
        $should_save = false;
        foreach ($prefixes as $p) {
            if (strpos($key, $p) === 0) { $should_save = true; break; }
        }
        if ($should_save) {
            if (is_array($value)) {
                $sanitized = map_deep($value, 'wp_kses_post');
                update_post_meta($post_id, $key, $sanitized);
            } else {
                update_post_meta($post_id, $key, wp_kses_post($value));
            }
        }
    }
}
add_action('save_post', 'saulocoelho_save_metaboxes');
