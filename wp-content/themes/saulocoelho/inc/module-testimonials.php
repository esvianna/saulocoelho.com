<?php
/**
 * Module: Testimonials & Social Proof
 */

// Register Testimonial CPT
function saulocoelho_register_testimonial_cpt() {
    $labels = [
        'name'               => 'Testemunhos',
        'singular_name'      => 'Testemunho',
        'menu_name'          => 'Testemunhos',
        'add_new'            => 'Adicionar Novo',
        'add_new_item'       => 'Adicionar Novo Testemunho',
        'edit_item'          => 'Editar Testemunho',
        'new_item'           => 'Novo Testemunho',
        'all_items'          => 'Todos os Testemunhos',
        'search_items'       => 'Procurar Testemunhos',
        'not_found'          => 'Nenhum testemunho encontrado',
    ];

    $args = [
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => false,
        'menu_icon'          => 'dashicons-testimonial',
        'supports'           => ['title', 'editor', 'thumbnail'],
        'show_in_rest'       => true,
    ];

    register_post_type('testimonial', $args);
}
add_action('init', 'saulocoelho_register_testimonial_cpt');

/**
 * Register Testimonial Meta Boxes
 */
function saulocoelho_testimonial_add_meta_boxes() {
    add_meta_box(
        'testimonial_details',
        'Dados do Testemunho',
        'saulocoelho_render_testimonial_metabox',
        'testimonial',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'saulocoelho_testimonial_add_meta_boxes');

function saulocoelho_render_testimonial_metabox($post) {
    wp_nonce_field('saulocoelho_save_testimonial', 'testimonial_nonce');

    $role = get_post_meta($post->ID, 'testimonial_role', true);
    $rating = get_post_meta($post->ID, 'testimonial_rating', true) ?: 5;
    $video_url = get_post_meta($post->ID, 'testimonial_video_url', true);
    $product_id = get_post_meta($post->ID, 'testimonial_product_id', true);

    echo '<p><label><strong>Cargo / Empresa (ex: Aluno de Mastermind):</strong></label><br>';
    echo '<input type="text" name="testimonial_role" value="' . esc_attr($role) . '" class="large-text"></p>';

    echo '<p><label><strong>Avaliação (Estrelas 1-5):</strong></label><br>';
    echo '<select name="testimonial_rating">';
    for ($i = 5; $i >= 1; $i--) {
        echo '<option value="'.$i.'" '.selected($rating, $i, false).'>'.$i.' Estrelas</option>';
    }
    echo '</select></p>';

    echo '<p><label><strong>URL do Vídeo (YouTube, Vimeo ou Arquivo MP4):</strong></label><br>';
    echo '<input type="url" name="testimonial_video_url" id="testimonial_video_url" value="' . esc_url($video_url) . '" class="large-text" style="width: 80%;" placeholder="https://www.youtube.com/watch?v=...">';
    echo '<button type="button" class="button testimonial-upload-button" data-target="testimonial_video_url">Selecionar da Biblioteca</button>';
    echo '<br><span class="description">Você pode colar um link do YouTube/Vimeo ou subir um arquivo .mp4 diretamente para a biblioteca do WordPress.</span></p>';

    // Selected Product (Optional)
    $products = get_posts(['post_type' => 'product', 'posts_per_page' => -1]);
    echo '<p><label><strong>Vincular ao Produto (Opcional):</strong></label><br>';
    echo '<select name="testimonial_product_id"><option value="">- Global (Aparece em todos ou na Home) -</option>';
    foreach ($products as $prod) {
        echo '<option value="'.$prod->ID.'" '.selected($product_id, $prod->ID, false).'>'.$prod->post_title.'</option>';
    }
    echo '</select></p>';

    // JS for Media Upload
    ?>
    <script>
    jQuery(document).ready(function($){
        var mediaUploader;
        $('.testimonial-upload-button').click(function(e) {
            e.preventDefault();
            var targetId = $(this).data('target');
            if (mediaUploader) {
                mediaUploader.open();
                return;
            }
            mediaUploader = wp.media({
                title: 'Selecionar Vídeo',
                button: { text: 'Usar este vídeo' },
                multiple: false,
                library: { type: 'video' }
            });
            mediaUploader.on('select', function() {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                $('#' + targetId).val(attachment.url);
            });
            mediaUploader.open();
        });
    });
    </script>
    <?php
}

function saulocoelho_testimonial_admin_scripts($hook) {
    global $post_type;
    if ('testimonial' === $post_type) {
        wp_enqueue_media();
    }
}
add_action('admin_enqueue_scripts', 'saulocoelho_testimonial_admin_scripts');

function saulocoelho_save_testimonial_meta($post_id) {
    if (!isset($_POST['testimonial_nonce']) || !wp_verify_nonce($_POST['testimonial_nonce'], 'saulocoelho_save_testimonial')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

    $fields = ['testimonial_role', 'testimonial_rating', 'testimonial_video_url', 'testimonial_product_id'];
    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
        }
    }
}
add_action('save_post', 'saulocoelho_save_testimonial_meta');

/**
 * Handle Frontend Review Submission
 */
function saulocoelho_handle_review_submission() {
    if (!isset($_POST['review_nonce']) || !wp_verify_nonce($_POST['review_nonce'], 'submit_review')) {
        wp_die('Erro de segurança.');
    }

    if (!is_user_logged_in()) {
        wp_die('Você precisa estar logado para enviar uma avaliação.');
    }

    $current_user = wp_get_current_user();
    $name = sanitize_text_field($_POST['review_name']) ?: $current_user->display_name;
    $rating = intval($_POST['review_rating']);
    $content = sanitize_textarea_field($_POST['review_text']);
    $video_url = esc_url_raw($_POST['review_video']);
    $product_id = intval($_POST['review_product_id']);

    $new_post = [
        'post_title'   => $name,
        'post_content' => $content,
        'post_status'  => 'pending',
        'post_type'    => 'testimonial',
    ];

    $post_id = wp_insert_post($new_post);

    if ($post_id) {
        update_post_meta($post_id, 'testimonial_rating', $rating);
        update_post_meta($post_id, 'testimonial_video_url', $video_url);
        update_post_meta($post_id, 'testimonial_product_id', $product_id);
        update_post_meta($post_id, 'testimonial_role', 'Aluno');
        
        // Redirect back with success message
        wp_redirect(add_query_arg('review_status', 'submitted', get_permalink($product_id)));
        exit;
    }

    wp_die('Erro ao enviar avaliação.');
}
add_action('admin_post_submit_student_review', 'saulocoelho_handle_review_submission');
add_action('admin_post_nopriv_submit_student_review', 'saulocoelho_handle_review_submission');
