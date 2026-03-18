<?php
/**
 * Custom Meta Boxes for Home Page
 * Provides a user-friendly interface for editing Hero and Features sections.
 */

function saulocoelho_home_metaboxes() {
    $post_id = isset($_GET['post']) ? (int) $_GET['post'] : (isset($_POST['post_ID']) ? (int) $_POST['post_ID'] : 0);
    
    // Only show on the Front Page
    if ($post_id && $post_id === (int) get_option('page_on_front')) {
        add_meta_box(
            'home_hero_settings',
            'Configurações da Seção Hero (Topo)',
            'saulocoelho_render_hero_metabox',
            'page',
            'normal',
            'high'
        );
    }
}
add_action('add_meta_boxes', 'saulocoelho_home_metaboxes');

function saulocoelho_render_hero_metabox($post) {
    $hero_eyebrow = get_post_meta($post->ID, 'hero_eyebrow', true);
    $hero_title = get_post_meta($post->ID, 'hero_title', true);
    $hero_description = get_post_meta($post->ID, 'hero_description', true);
    $hero_bg_image = get_post_meta($post->ID, 'hero_bg_image', true);
    
    wp_nonce_field('saulocoelho_save_hero', 'hero_nonce');
    ?>
    <div class="sc-metabox-wrapper" style="padding: 10px 0;">
        <p>
            <label for="hero_eyebrow"><strong>Texto Superior (Eyebrow):</strong></label><br>
            <input type="text" name="hero_eyebrow" id="hero_eyebrow" value="<?php echo esc_attr($hero_eyebrow); ?>" class="large-text" placeholder="Ex: Alta Performance & Liderança">
        </p>
        <p>
            <label for="hero_title"><strong>Título Principal (Headline):</strong></label><br>
            <textarea name="hero_title" id="hero_title" rows="3" class="large-text"><?php echo esc_textarea($hero_title); ?></textarea>
            <span class="description">Dica: Use <code>&lt;span class="text-transparent bg-clip-text bg-gradient-to-r from-primary to-blue-400"&gt;texto&lt;/span&gt;</code> para o efeito degrade.</span>
        </p>
        <p>
            <label for="hero_description"><strong>Descrição:</strong></label><br>
            <textarea name="hero_description" id="hero_description" rows="3" class="large-text"><?php echo esc_textarea($hero_description); ?></textarea>
        </p>
        <p>
            <label for="hero_bg_image"><strong>Imagem de Fundo (Saulo):</strong></label><br>
            <input type="text" name="hero_bg_image" id="hero_bg_image" value="<?php echo esc_url($hero_bg_image); ?>" class="regular-text">
            <button type="button" class="button button-secondary" id="hero_bg_image_button">Escolher na Biblioteca</button>
            <br><span class="description">Escolha uma imagem da biblioteca ou cole a URL.</span>
        </p>
        <hr>
        <h3>Seção de Empresas e Autoridade</h3>
        <?php
        $trusted_label = get_post_meta($post->ID, 'trusted_label', true);
        $features_title = get_post_meta($post->ID, 'features_title', true);
        $features_description = get_post_meta($post->ID, 'features_description', true);
        ?>
        <p>
            <label for="trusted_label"><strong>Título da Seção de Logos:</strong></label><br>
            <input type="text" name="trusted_label" id="trusted_label" value="<?php echo esc_attr($trusted_label); ?>" class="large-text" placeholder="Ex: Empresas que confiam no nosso trabalho">
        </p>
        <p>
            <label for="features_title"><strong>Título de Autoridade:</strong></label><br>
            <input type="text" name="features_title" id="features_title" value="<?php echo esc_attr($features_title); ?>" class="large-text" placeholder="Ex: Autoridade e Experiência">
        </p>
        <p>
            <label for="features_description"><strong>Descrição de Autoridade:</strong></label><br>
            <textarea name="features_description" id="features_description" rows="2" class="large-text"><?php echo esc_textarea($features_description); ?></textarea>
        </p>
    </div>
    <script>
    jQuery(document).ready(function($){
        $('#hero_bg_image_button').click(function(e) {
            e.preventDefault();
            var image = wp.media({ 
                title: 'Escolher Imagem do Hero',
                multiple: false
            }).open()
            .on('select', function(e){
                var uploaded_image = image.state().get('selection').first();
                var image_url = uploaded_image.toJSON().url;
                $('#hero_bg_image').val(image_url);
            });
        });
    });
    </script>
    <?php
}

function saulocoelho_save_hero_metabox($post_id) {
    if (!isset($_POST['hero_nonce']) || !wp_verify_nonce($_POST['hero_nonce'], 'saulocoelho_save_hero')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_page', $post_id)) return;

    $fields = ['hero_eyebrow', 'hero_title', 'hero_description', 'hero_bg_image', 'trusted_label', 'features_title', 'features_description'];
    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
        }
    }
}
add_action('save_post', 'saulocoelho_save_hero_metabox');
