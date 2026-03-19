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
    if ($template === 'page-course-detail.php') {
        add_meta_box('course_settings', 'Configurações da Página de Vendas', 'saulocoelho_render_course_metabox', 'page', 'normal', 'high');
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
        'hero_bg_image' => 'Imagem de Fundo (Saulo)',
        'hero_btn_1_text' => 'Botão 1: Texto',
        'hero_btn_1_link' => 'Botão 1: Link',
        'hero_btn_2_text' => 'Botão 2: Texto',
        'hero_btn_2_link' => 'Botão 2: Link',
        'trusted_label' => 'Título da Seção de Logos',
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
        'course_badge' => 'Selo do Topo (ex: Matrículas Abertas)',
        'course_video_url' => 'URL da Imagem/Capa do Vídeo',
        'course_stat_1' => 'Destaque 1 (ex: 10k+ Alunos)',
        'course_stat_2' => 'Destaque 2 (ex: 4.9/5 Avaliação)',
        'course_price_full' => 'Preço cheio (ex: R$ 1.997,00)',
        'course_price_install' => 'Valor da Parcela (ex: R$ 97,00)',
        'course_checkout_link' => 'Link do Checkout',
    ];
    saulocoelho_render_fields($post->ID, $fields);
}

/**
 * HELPER RENDERERS
 */

function saulocoelho_render_fields($post_id, $fields) {
    foreach ($fields as $key => $label) {
        $val = get_post_meta($post_id, $key, true);
        echo '<p><label><strong>' . esc_html($label) . ':</strong></label><br>';
        if (strpos($key, 'desc') !== false || strpos($key, 'title') !== false) {
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
