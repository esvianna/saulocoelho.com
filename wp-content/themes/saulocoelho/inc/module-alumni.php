<?php
/**
 * Módulo Alumni — Galeria de Turmas
 *
 * Registra a metabox "📸 Galerias de Turmas" na tela de edição do produto (WooCommerce).
 * As "turmas" são os CPTs `ama_course` do plugin AmaEducacional.
 * O admin seleciona quais turmas exibir e faz upload das fotos via WP Media Library.
 *
 * Dados salvos no post meta do produto:
 *   _alumni_turmas          → array de IDs de `ama_course` selecionados
 *   _alumni_fotos_{id}      → array de IDs de anexos (imagens) para cada turma
 *
 * @package SauloCoelho
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// ─── 1. REGISTRAR METABOX ───────────────────────────────────────────────────

add_action( 'add_meta_boxes', 'alumni_register_metabox' );
function alumni_register_metabox() {
    add_meta_box(
        'alumni_galerias_turmas',
        '📸 Galerias de Turmas Alumni',
        'alumni_render_metabox',
        'product',
        'normal',
        'default'
    );
}

// ─── 2. RENDERIZAR METABOX ──────────────────────────────────────────────────

function alumni_render_metabox( $post ) {
    wp_nonce_field( 'alumni_save_metabox', 'alumni_nonce' );

    // Buscar todos os cursos `ama_course` publicados
    $courses = get_posts( [
        'post_type'      => 'ama_course',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'orderby'        => 'title',
        'order'          => 'ASC',
    ] );

    // Dados salvos
    $selected_turmas = get_post_meta( $post->ID, '_alumni_turmas', true );
    if ( ! is_array( $selected_turmas ) ) {
        $selected_turmas = [];
    }

    ?>
    <div id="alumni-metabox-wrap" style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;">

        <?php if ( empty( $courses ) ) : ?>
            <div style="background:#fff3cd;border:1px solid #ffc107;border-radius:8px;padding:16px;color:#856404;font-size:13px;">
                <strong>⚠️ Nenhum curso cadastrado.</strong><br>
                Acesse <strong>Cursos → Adicionar Novo</strong> no AmaEducacional para criar turmas antes de configurar a galeria.
            </div>
        <?php else : ?>

            <p style="color:#666;font-size:13px;margin:0 0 16px;">
                Selecione as turmas que deseja exibir na galeria desta página de produto e faça o upload das fotos de cada uma.
            </p>

            <!-- Lista de turmas com checkbox -->
            <div style="background:#f8f9fa;border:1px solid #dee2e6;border-radius:8px;padding:16px;margin-bottom:20px;">
                <strong style="font-size:13px;color:#333;display:block;margin-bottom:12px;">Turmas para exibir neste produto:</strong>
                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:8px;">
                    <?php foreach ( $courses as $course ) :
                        $checked = in_array( $course->ID, $selected_turmas, true ) ? 'checked' : '';
                    ?>
                        <label style="display:flex;align-items:center;gap:8px;padding:8px 12px;background:#fff;border:1px solid <?php echo $checked ? '#0d6efd' : '#dee2e6'; ?>;border-radius:6px;cursor:pointer;transition:all .2s;font-size:13px;">
                            <input
                                type="checkbox"
                                name="alumni_turmas[]"
                                value="<?php echo esc_attr( $course->ID ); ?>"
                                <?php echo $checked; ?>
                                onchange="alumniToggleFotosPanel(this)"
                                style="width:16px;height:16px;accent-color:#0d6efd;"
                            >
                            <span style="color:#333;"><?php echo esc_html( $course->post_title ); ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Painéis de fotos por turma (aparecem quando checkbox marcado) -->
            <?php foreach ( $courses as $course ) :
                $is_selected = in_array( $course->ID, $selected_turmas, true );
                $fotos_key   = '_alumni_fotos_' . $course->ID;
                $saved_fotos = get_post_meta( $post->ID, $fotos_key, true );
                if ( ! is_array( $saved_fotos ) ) {
                    $saved_fotos = [];
                }
            ?>
                <div
                    id="alumni-fotos-panel-<?php echo esc_attr( $course->ID ); ?>"
                    class="alumni-fotos-panel"
                    data-course-id="<?php echo esc_attr( $course->ID ); ?>"
                    style="display:<?php echo $is_selected ? 'block' : 'none'; ?>;margin-bottom:16px;border:1px solid #0d6efd;border-radius:8px;overflow:hidden;"
                >
                    <div style="background:#0d6efd;padding:10px 16px;display:flex;align-items:center;justify-content:space-between;">
                        <strong style="color:#fff;font-size:13px;">📷 Fotos: <?php echo esc_html( $course->post_title ); ?></strong>
                        <button
                            type="button"
                            class="alumni-upload-btn button button-primary"
                            data-course-id="<?php echo esc_attr( $course->ID ); ?>"
                            style="background:#fff;color:#0d6efd;border:none;padding:5px 12px;border-radius:5px;font-size:12px;font-weight:bold;cursor:pointer;"
                        >
                            + Adicionar Fotos
                        </button>
                    </div>

                    <div style="padding:16px;background:#fff;">
                        <!-- Campo hidden com IDs das imagens (JSON array) -->
                        <input
                            type="hidden"
                            class="alumni-fotos-ids"
                            name="alumni_fotos[<?php echo esc_attr( $course->ID ); ?>]"
                            value="<?php echo esc_attr( wp_json_encode( array_map( 'intval', $saved_fotos ) ) ); ?>"
                        >

                        <!-- Preview das imagens -->
                        <div
                            class="alumni-fotos-preview"
                            data-course-id="<?php echo esc_attr( $course->ID ); ?>"
                            style="display:flex;flex-wrap:wrap;gap:8px;min-height:60px;"
                        >
                            <?php if ( empty( $saved_fotos ) ) : ?>
                                <p class="alumni-empty-msg" style="color:#aaa;font-size:12px;font-style:italic;">Nenhuma foto adicionada. Clique em "Adicionar Fotos".</p>
                            <?php else : ?>
                                <?php foreach ( $saved_fotos as $img_id ) :
                                    $img_src = wp_get_attachment_image_src( $img_id, 'thumbnail' );
                                    if ( ! $img_src ) continue;
                                ?>
                                    <div class="alumni-foto-item" data-id="<?php echo esc_attr( $img_id ); ?>" style="position:relative;width:80px;height:80px;border-radius:6px;overflow:hidden;border:2px solid #dee2e6;">
                                        <img src="<?php echo esc_url( $img_src[0] ); ?>" style="width:100%;height:100%;object-fit:cover;">
                                        <button
                                            type="button"
                                            class="alumni-remove-foto"
                                            data-course-id="<?php echo esc_attr( $course->ID ); ?>"
                                            data-img-id="<?php echo esc_attr( $img_id ); ?>"
                                            style="position:absolute;top:2px;right:2px;background:rgba(220,38,38,0.9);color:#fff;border:none;border-radius:50%;width:20px;height:20px;cursor:pointer;font-size:12px;line-height:1;display:flex;align-items:center;justify-content:center;padding:0;"
                                        >×</button>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

        <?php endif; ?>
    </div>

    <script>
    (function() {
        'use strict';

        // Mostrar/ocultar painel de fotos quando checkbox é alterado
        window.alumniToggleFotosPanel = function(checkbox) {
            var courseId = checkbox.value;
            var panel    = document.getElementById('alumni-fotos-panel-' + courseId);
            if (!panel) return;

            if (checkbox.checked) {
                panel.style.display = 'block';
            } else {
                panel.style.display = 'none';
            }
        };

        // Inicializar botões de upload
        function initAlumniUpload() {
            var btns = document.querySelectorAll('.alumni-upload-btn');
            btns.forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var courseId = this.getAttribute('data-course-id');
                    openAlumniMediaPicker(courseId);
                });
            });
        }

        // Abrir o Media Picker nativo do WordPress
        function openAlumniMediaPicker(courseId) {
            if (typeof wp === 'undefined' || !wp.media) {
                alert('Por favor, recarregue a página e tente novamente.');
                return;
            }

            var frame = wp.media({
                title   : 'Selecionar Fotos da Turma',
                button  : { text: 'Usar estas fotos' },
                library : { type: 'image' },
                multiple: true
            });

            frame.on('select', function() {
                var selection = frame.state().get('selection');
                var preview   = document.querySelector('.alumni-fotos-preview[data-course-id="' + courseId + '"]');
                var input     = document.querySelector('input[name="alumni_fotos[' + courseId + ']"]');

                if (!preview || !input) return;

                // Ler IDs já salvos
                var currentIds = [];
                try { currentIds = JSON.parse(input.value) || []; } catch(e) { currentIds = []; }

                // Remover mensagem vazia
                var emptyMsg = preview.querySelector('.alumni-empty-msg');
                if (emptyMsg) emptyMsg.remove();

                selection.each(function(attachment) {
                    var att = attachment.toJSON();

                    // Evitar duplicatas
                    if (currentIds.indexOf(att.id) !== -1) return;

                    currentIds.push(att.id);

                    var thumbUrl = (att.sizes && att.sizes.thumbnail) ? att.sizes.thumbnail.url : att.url;

                    var item = document.createElement('div');
                    item.className = 'alumni-foto-item';
                    item.setAttribute('data-id', att.id);
                    item.style.cssText = 'position:relative;width:80px;height:80px;border-radius:6px;overflow:hidden;border:2px solid #dee2e6;';
                    item.innerHTML = '<img src="' + thumbUrl + '" style="width:100%;height:100%;object-fit:cover;">'
                        + '<button type="button" class="alumni-remove-foto" data-course-id="' + courseId + '" data-img-id="' + att.id + '" '
                        + 'style="position:absolute;top:2px;right:2px;background:rgba(220,38,38,0.9);color:#fff;border:none;border-radius:50%;width:20px;height:20px;cursor:pointer;font-size:12px;line-height:1;display:flex;align-items:center;justify-content:center;padding:0;">×</button>';

                    preview.appendChild(item);
                });

                // Atualizar input hidden
                input.value = JSON.stringify(currentIds);

                // Re-inicializar botões de remoção
                initRemoveButtons();
            });

            frame.open();
        }

        // Botões de remoção
        function initRemoveButtons() {
            document.querySelectorAll('.alumni-remove-foto').forEach(function(btn) {
                // Evitar duplo bind
                if (btn.getAttribute('data-alumni-bound')) return;
                btn.setAttribute('data-alumni-bound', '1');

                btn.addEventListener('click', function() {
                    var courseId = this.getAttribute('data-course-id');
                    var imgId    = parseInt(this.getAttribute('data-img-id'), 10);
                    var input    = document.querySelector('input[name="alumni_fotos[' + courseId + ']"]');
                    var item     = this.closest('.alumni-foto-item');

                    if (input) {
                        var ids = [];
                        try { ids = JSON.parse(input.value) || []; } catch(e) { ids = []; }
                        ids = ids.filter(function(id) { return id !== imgId; });
                        input.value = JSON.stringify(ids);
                    }

                    if (item) {
                        item.remove();
                        // Mostrar mensagem vazia se não houver mais fotos
                        var preview = document.querySelector('.alumni-fotos-preview[data-course-id="' + courseId + '"]');
                        if (preview && preview.querySelectorAll('.alumni-foto-item').length === 0) {
                            var msg = document.createElement('p');
                            msg.className = 'alumni-empty-msg';
                            msg.style.cssText = 'color:#aaa;font-size:12px;font-style:italic;';
                            msg.textContent = 'Nenhuma foto adicionada. Clique em "Adicionar Fotos".';
                            preview.appendChild(msg);
                        }
                    }
                });
            });
        }

        // Init
        document.addEventListener('DOMContentLoaded', function() {
            initAlumniUpload();
            initRemoveButtons();
        });

    })();
    </script>
    <?php
}

// ─── 3. SALVAR DADOS ────────────────────────────────────────────────────────

add_action( 'save_post_product', 'alumni_save_metabox', 10, 2 );
function alumni_save_metabox( $post_id, $post ) {
    // Verificações de segurança
    if ( ! isset( $_POST['alumni_nonce'] ) ) return;
    if ( ! wp_verify_nonce( $_POST['alumni_nonce'], 'alumni_save_metabox' ) ) return;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    // Turmas selecionadas
    $selected_turmas = isset( $_POST['alumni_turmas'] ) ? array_map( 'intval', (array) $_POST['alumni_turmas'] ) : [];
    update_post_meta( $post_id, '_alumni_turmas', $selected_turmas );

    // Fotos por turma
    $fotos_data = isset( $_POST['alumni_fotos'] ) ? (array) $_POST['alumni_fotos'] : [];
    foreach ( $fotos_data as $course_id => $ids_json ) {
        $course_id = intval( $course_id );
        if ( $course_id <= 0 ) continue;

        $ids = [];
        $decoded = json_decode( wp_unslash( $ids_json ), true );
        if ( is_array( $decoded ) ) {
            $ids = array_map( 'intval', $decoded );
            $ids = array_filter( $ids, fn( $id ) => $id > 0 );
            $ids = array_values( $ids );
        }

        update_post_meta( $post_id, '_alumni_fotos_' . $course_id, $ids );
    }

    // Limpar fotos de turmas que foram desmarcadas
    $all_courses = get_posts( [
        'post_type'      => 'ama_course',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'fields'         => 'ids',
    ] );
    foreach ( $all_courses as $course_id ) {
        if ( ! in_array( $course_id, $selected_turmas, true ) && ! array_key_exists( $course_id, $fotos_data ) ) {
            // Mantemos os dados de fotos mesmo quando a turma é desmarcada,
            // para não perder o trabalho do admin — apenas não exibe no front-end.
        }
    }
}
