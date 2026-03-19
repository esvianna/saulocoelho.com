<?php
/**
 * Custom Meta Boxes for Home and About Pages
 * Provides a user-friendly interface for editing site-wide dynamic sections.
 */

/**
 * Register Meta Boxes
 */
function saulocoelho_register_metaboxes() {
    $post_id = isset($_GET['post']) ? (int) $_GET['post'] : (isset($_POST['post_ID']) ? (int) $_POST['post_ID'] : 0);
    if (!$post_id) return;

    $template = get_post_meta($post_id, '_wp_page_template', true);

    // 1. Home Page Settings
    if ($post_id === (int) get_option('page_on_front')) {
        add_meta_box(
            'home_hero_settings',
            'Configurações da Home (Hero & Destaques)',
            'saulocoelho_render_home_metabox',
            'page',
            'normal',
            'high'
        );
    }

    // 2. About Page (Quem é) Settings
    if ($template === 'page-about.php') {
        add_meta_box(
            'about_page_settings',
            'Configurações da Página Sobre (Quem é)',
            'saulocoelho_render_about_metabox',
            'page',
            'normal',
            'high'
        );
    }
}
add_action('add_meta_boxes', 'saulocoelho_register_metaboxes');

/**
 * Render Home Meta Box
 */
function saulocoelho_render_home_metabox($post) {
    $hero_eyebrow = get_post_meta($post->ID, 'hero_eyebrow', true);
    $hero_title = get_post_meta($post->ID, 'hero_title', true);
    $hero_description = get_post_meta($post->ID, 'hero_description', true);
    $hero_bg_image = get_post_meta($post->ID, 'hero_bg_image', true);
    $trusted_label = get_post_meta($post->ID, 'trusted_label', true);
    $features_title = get_post_meta($post->ID, 'features_title', true);
    $features_description = get_post_meta($post->ID, 'features_description', true);
    
    wp_nonce_field('saulocoelho_save_metabox', 'saulocoelho_nonce');
    ?>
    <div class="sc-metabox-wrapper" style="padding: 10px 0;">
        <h3>Seção Hero (Topo)</h3>
        <p>
            <label><strong>Texto Superior (Eyebrow):</strong></label><br>
            <input type="text" name="hero_eyebrow" value="<?php echo esc_attr($hero_eyebrow); ?>" class="large-text" placeholder="Ex: Alta Performance & Liderança">
        </p>
        <p>
            <label><strong>Título Principal (Headline):</strong></label><br>
            <textarea name="hero_title" rows="2" class="large-text"><?php echo esc_textarea($hero_title); ?></textarea>
            <span class="description">Dica: Use <code>&lt;span class="text-transparent bg-clip-text bg-gradient-to-r from-primary to-blue-400"&gt;texto&lt;/span&gt;</code> para o efeito degrade.</span>
        </p>
        <p>
            <label><strong>Descrição:</strong></label><br>
            <textarea name="hero_description" rows="3" class="large-text"><?php echo esc_textarea($hero_description); ?></textarea>
        </p>
        <p>
            <label><strong>Imagem de Fundo (Saulo):</strong></label><br>
            <input type="text" name="hero_bg_image" id="hero_bg_image" value="<?php echo esc_url($hero_bg_image); ?>" class="regular-text">
            <button type="button" class="button button-secondary media-uploader" data-target="#hero_bg_image">Escolher na Biblioteca</button>
        </p>
        <hr>
        <h3>Seção de Empresas e Autoridade</h3>
        <p>
            <label><strong>Título da Seção de Logos:</strong></label><br>
            <input type="text" name="trusted_label" value="<?php echo esc_attr($trusted_label); ?>" class="large-text">
        </p>
        <p>
            <label><strong>Título de Autoridade:</strong></label><br>
            <input type="text" name="features_title" value="<?php echo esc_attr($features_title); ?>" class="large-text">
        </p>
        <p>
            <label><strong>Descrição de Autoridade:</strong></label><br>
            <textarea name="features_description" rows="2" class="large-text"><?php echo esc_textarea($features_description); ?></textarea>
        </p>
    </div>
    <?php saulocoelho_render_metabox_js(); ?>
    <?php
}

/**
 * Render About Meta Box
 */
function saulocoelho_render_about_metabox($post) {
    wp_nonce_field('saulocoelho_save_metabox', 'saulocoelho_nonce');
    
    $fields = [
        'about_eyebrow' => 'Texto Superior (Ex: Autoridade & Excelência)',
        'about_stat_1_number' => 'Estatística 1 (Número)',
        'about_stat_1_label' => 'Estatística 1 (Legenda)',
        'about_stat_2_number' => 'Estatística 2 (Número)',
        'about_stat_2_label' => 'Estatística 2 (Legenda)',
        'about_image' => 'Foto de Perfil',
    ];

    echo '<div class="sc-metabox-wrapper" style="padding: 10px 0;">';
    foreach ($fields as $key => $label) {
        $value = get_post_meta($post->ID, $key, true);
        echo '<p><label><strong>' . esc_html($label) . ':</strong></label><br>';
        if ($key === 'about_image') {
            echo '<input type="text" name="' . $key . '" id="' . $key . '" value="' . esc_url($value) . '" class="regular-text"> ';
            echo '<button type="button" class="button button-secondary media-uploader" data-target="#' . $key . '">Escolher na Biblioteca</button>';
        } else {
            echo '<input type="text" name="' . $key . '" value="' . esc_attr($value) . '" class="large-text">';
        }
        echo '</p>';
    }

    echo '<hr><h3>Linha do Tempo (3 Marcos)</h3>';
    for ($i = 1; $i <= 3; $i++) {
        $y = get_post_meta($post->ID, "about_milestone_{$i}_year", true);
        $t = get_post_meta($post->ID, "about_milestone_{$i}_title", true);
        $d = get_post_meta($post->ID, "about_milestone_{$i}_desc", true);
        echo "<div style='background:#f9f9f9; padding:10px; margin-bottom:10px; border-radius:5px;'>";
        echo "<strong>Marco $i:</strong><br>";
        echo "<input type='text' name='about_milestone_{$i}_year' value='".esc_attr($y)."' placeholder='Ano/Período' style='width:30%'> ";
        echo "<input type='text' name='about_milestone_{$i}_title' value='".esc_attr($t)."' placeholder='Título' style='width:68%'><br>";
        echo "<textarea name='about_milestone_{$i}_desc' rows='2' class='large-text' placeholder='Descrição' style='margin-top:5px'>".esc_textarea($d)."</textarea>";
        echo "</div>";
    }

    echo '<hr><h3>Valores/Diferenciais (3 Itens)</h3>';
    for ($i = 1; $i <= 3; $i++) {
        $icon = get_post_meta($post->ID, "about_value_{$i}_icon", true);
        $title = get_post_meta($post->ID, "about_value_{$i}_title", true);
        $desc = get_post_meta($post->ID, "about_value_{$i}_desc", true);
        echo "<div style='background:#f9f9f9; padding:10px; margin-bottom:10px; border-radius:5px;'>";
        echo "<strong>Valor $i:</strong><br>";
        echo "<input type='text' name='about_value_{$i}_icon' value='".esc_attr($icon)."' placeholder='Ícone Material (ex: visibility, gavel, groups)' style='width:30%'> ";
        echo "<input type='text' name='about_value_{$i}_title' value='".esc_attr($title)."' placeholder='Título' style='width:68%'><br>";
        echo "<textarea name='about_value_{$i}_desc' rows='2' class='large-text' placeholder='Descrição' style='margin-top:5px'>".esc_textarea($desc)."</textarea>";
        echo "</div>";
    }
    echo '</div>';
    saulocoelho_render_metabox_js();
}

/**
 * Common Scripts for Metaboxes
 */
function saulocoelho_render_metabox_js() {
    static $done = false;
    if ($done) return;
    $done = true;
    ?>
    <script>
    jQuery(document).ready(function($){
        $('.media-uploader').click(function(e) {
            e.preventDefault();
            var target = $(this).data('target');
            var image = wp.media({ 
                title: 'Escolher Imagem',
                multiple: false
            }).open()
            .on('select', function(e){
                var uploaded_image = image.state().get('selection').first();
                var image_url = uploaded_image.toJSON().url;
                $(target).val(image_url);
            });
        });
    });
    </script>
    <?php
}

/**
 * Save Meta Box Data
 */
function saulocoelho_save_metaboxes($post_id) {
    if (!isset($_POST['saulocoelho_nonce']) || !wp_verify_nonce($_POST['saulocoelho_nonce'], 'saulocoelho_save_metabox')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_page', $post_id)) return;

    // Define all possible fields
    $fields = [
        'hero_eyebrow', 'hero_title', 'hero_description', 'hero_bg_image', 'trusted_label', 'features_title', 'features_description',
        'about_eyebrow', 'about_stat_1_number', 'about_stat_1_label', 'about_stat_2_number', 'about_stat_2_label', 'about_image',
    ];
    // Add timeline and values dynamically
    for ($i=1; $i<=3; $i++) {
        $fields[] = "about_milestone_{$i}_year";
        $fields[] = "about_milestone_{$i}_title";
        $fields[] = "about_milestone_{$i}_desc";
        $fields[] = "about_value_{$i}_icon";
        $fields[] = "about_value_{$i}_title";
        $fields[] = "about_value_{$i}_desc";
    }

    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            // Use wp_kses_post for titles where <span> might be used, sanitize_text_field for else
            $value = $_POST[$field];
            if ($field === 'hero_title') {
                update_post_meta($post_id, $field, wp_kses_post($value));
            } else {
                update_post_meta($post_id, $field, sanitize_text_field($value));
            }
        }
    }
}
add_action('save_post', 'saulocoelho_save_metaboxes');
