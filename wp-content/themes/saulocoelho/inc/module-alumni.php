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

    // Textos editáveis da seção
    $alumni_badge     = get_post_meta( $post->ID, '_alumni_badge', true );
    $alumni_titulo    = get_post_meta( $post->ID, '_alumni_titulo', true );
    $alumni_subtitulo = get_post_meta( $post->ID, '_alumni_subtitulo', true );

    ?>
    <div id="alumni-metabox-wrap" style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;">

        <?php if ( empty( $courses ) ) : ?>
            <div style="background:#fff3cd;border:1px solid #ffc107;border-radius:8px;padding:16px;color:#856404;font-size:13px;">
                <strong>⚠️ Nenhum curso cadastrado.</strong><br>
                Acesse <strong>Cursos → Adicionar Novo</strong> no AmaEducacional para criar turmas antes de configurar a galeria.
            </div>
        <?php else : ?>

            <!-- Textos da Seção -->
            <div style="background:#e8f0fe;border:1px solid #c2d3f8;border-radius:8px;padding:16px;margin-bottom:20px;">
                <strong style="font-size:13px;color:#1a1a2e;display:block;margin-bottom:14px;">✏️ Textos da Seção (visíveis no site)</strong>
                <div style="display:grid;gap:12px;">
                    <label style="display:flex;flex-direction:column;gap:4px;">
                        <span style="font-size:12px;font-weight:600;color:#444;text-transform:uppercase;letter-spacing:.05em;">Badge (etiqueta acima do título)</span>
                        <input type="text" name="alumni_badge" value="<?php echo esc_attr( $alumni_badge ); ?>" placeholder="Memórias das Turmas" style="border:1px solid #c2d3f8;border-radius:6px;padding:8px 12px;font-size:13px;width:100%;box-sizing:border-box;">
                    </label>
                    <label style="display:flex;flex-direction:column;gap:4px;">
                        <span style="font-size:12px;font-weight:600;color:#444;text-transform:uppercase;letter-spacing:.05em;">Título principal</span>
                        <input type="text" name="alumni_titulo" value="<?php echo esc_attr( $alumni_titulo ); ?>" placeholder="Momentos que ficam para sempre" style="border:1px solid #c2d3f8;border-radius:6px;padding:8px 12px;font-size:13px;width:100%;box-sizing:border-box;">
                    </label>
                    <label style="display:flex;flex-direction:column;gap:4px;">
                        <span style="font-size:12px;font-weight:600;color:#444;text-transform:uppercase;letter-spacing:.05em;">Subtítulo</span>
                        <input type="text" name="alumni_subtitulo" value="<?php echo esc_attr( $alumni_subtitulo ); ?>" placeholder="Reviva os melhores momentos das nossas turmas" style="border:1px solid #c2d3f8;border-radius:6px;padding:8px 12px;font-size:13px;width:100%;box-sizing:border-box;">
                    </label>
                </div>
                <p style="color:#666;font-size:11px;margin:10px 0 0;">💡 Deixe em branco para usar o texto padrão (exibido como placeholder acima).</p>
            </div>

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

    // Textos da seção
    $alumni_badge     = isset( $_POST['alumni_badge'] )     ? sanitize_text_field( $_POST['alumni_badge'] )     : '';
    $alumni_titulo    = isset( $_POST['alumni_titulo'] )    ? sanitize_text_field( $_POST['alumni_titulo'] )    : '';
    $alumni_subtitulo = isset( $_POST['alumni_subtitulo'] ) ? sanitize_text_field( $_POST['alumni_subtitulo'] ) : '';
    update_post_meta( $post_id, '_alumni_badge',     $alumni_badge );
    update_post_meta( $post_id, '_alumni_titulo',    $alumni_titulo );
    update_post_meta( $post_id, '_alumni_subtitulo', $alumni_subtitulo );

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

// ═══════════════════════════════════════════════════════════════════════════
// FASE 2 — ABA "MINHAS TURMAS" NO MINHA CONTA
// ═══════════════════════════════════════════════════════════════════════════

// ─── 4. ENDPOINT WooCommerce ─────────────────────────────────────────────────

add_action( 'init', 'alumni_register_endpoint' );
function alumni_register_endpoint() {
    add_rewrite_endpoint( 'minhas-turmas', EP_ROOT | EP_PAGES );
}

// ─── 5. MENU DA CONTA ────────────────────────────────────────────────────────

add_filter( 'woocommerce_account_menu_items', 'alumni_add_menu_item' );
function alumni_add_menu_item( $items ) {
    $logout = [];
    if ( isset( $items['customer-logout'] ) ) {
        $logout = [ 'customer-logout' => $items['customer-logout'] ];
        unset( $items['customer-logout'] );
    }
    $items['minhas-turmas'] = '🎓 Minhas Turmas';
    return array_merge( $items, $logout );
}

// ─── 6. CONTEÚDO DA ABA ──────────────────────────────────────────────────────

add_action( 'woocommerce_account_minhas-turmas_endpoint', 'alumni_render_my_account_tab' );
function alumni_render_my_account_tab() {
    $user_id = get_current_user_id();
    if ( ! $user_id ) {
        echo '<p>Por favor, faça login para ver suas turmas.</p>';
        return;
    }

    // Cursos em que o aluno está matriculado
    global $wpdb;
    $lms_table = $wpdb->prefix . 'lms_enrollments';
    $enrolled_course_ids = $wpdb->get_col( $wpdb->prepare(
        "SELECT course_id FROM $lms_table WHERE user_id = %d AND status IN ('active','completed') ORDER BY enrolled_at DESC",
        $user_id
    ) );
    $enrolled_course_ids = array_map( 'intval', (array) $enrolled_course_ids );

    // Produtos com galerias alumni
    $products_with_galleries = get_posts( [
        'post_type'      => 'product',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'meta_query'     => [ [ 'key' => '_alumni_turmas', 'compare' => 'EXISTS' ] ],
    ] );

    // Montar galerias filtradas pelas matrículas do aluno
    $galerias = [];
    foreach ( $products_with_galleries as $product_post ) {
        $pid             = $product_post->ID;
        $selected_turmas = get_post_meta( $pid, '_alumni_turmas', true );
        if ( ! is_array( $selected_turmas ) || empty( $selected_turmas ) ) continue;

        $turmas_do_aluno = array_intersect( array_map( 'intval', $selected_turmas ), $enrolled_course_ids );
        if ( empty( $turmas_do_aluno ) ) continue;

        $turmas_data = [];
        foreach ( $turmas_do_aluno as $course_id ) {
            $course = get_post( $course_id );
            if ( ! $course || $course->post_status !== 'publish' ) continue;

            $fotos_ids = get_post_meta( $pid, '_alumni_fotos_' . $course_id, true );
            if ( ! is_array( $fotos_ids ) ) $fotos_ids = [];

            $fotos = [];
            foreach ( $fotos_ids as $img_id ) {
                $full  = wp_get_attachment_image_src( $img_id, 'large' );
                $thumb = wp_get_attachment_image_src( $img_id, 'medium' );
                if ( $full ) {
                    $fotos[] = [
                        'full'  => $full[0],
                        'thumb' => $thumb ? $thumb[0] : $full[0],
                        'alt'   => get_post_meta( $img_id, '_wp_attachment_image_alt', true ) ?: esc_html( $course->post_title ),
                    ];
                }
            }

            if ( ! empty( $fotos ) ) {
                $turmas_data[] = [
                    'id'    => $course_id,
                    'title' => $course->post_title,
                    'fotos' => $fotos,
                ];
            }
        }

        if ( ! empty( $turmas_data ) ) {
            $galerias[] = [
                'product_id'    => $pid,
                'product_title' => get_the_title( $pid ),
                'product_url'   => get_permalink( $pid ),
                'turmas'        => $turmas_data,
            ];
        }
    }
    ?>

    <style>
    .alumni-mytab-header { margin-bottom: 28px; }
    .alumni-mytab-header h2 { font-size: 22px; font-weight: 800; margin: 0 0 4px; }
    .alumni-mytab-header p  { font-size: 13px; color: #64748b; margin: 0; }
    .alumni-mytab-product   { margin-bottom: 48px; }
    .alumni-mytab-product-name {
        font-size: 15px; font-weight: 700; margin: 0 0 14px;
        display: flex; align-items: center; gap: 8px;
        border-bottom: 1px solid rgba(100,116,139,.15); padding-bottom: 12px;
    }
    .alumni-mytab-product-name a { color: #3b82f6; text-decoration: none; transition: color .2s; }
    .alumni-mytab-product-name a:hover { color: #2563eb; }
    .alumni-mytab-tabs { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 16px; }
    .alumni-mytab-tab {
        background: rgba(59,130,246,.07); border: 1px solid rgba(59,130,246,.2);
        color: #3b82f6; padding: 7px 18px; border-radius: 999px;
        font-size: 12px; font-weight: 700; cursor: pointer; transition: all .2s;
    }
    .alumni-mytab-tab:hover,
    .alumni-mytab-tab.active { background: #3b82f6; color: #fff; box-shadow: 0 0 14px rgba(59,130,246,.3); }
    .alumni-mytab-panel { display: none; }
    .alumni-mytab-panel.active { display: block; }
    .alumni-mytab-grid {
        display: grid; gap: 8px;
        grid-template-columns: repeat(2,1fr);
    }
    @media(min-width:480px){ .alumni-mytab-grid{ grid-template-columns:repeat(3,1fr); } }
    @media(min-width:768px){ .alumni-mytab-grid{ grid-template-columns:repeat(4,1fr); } }
    .alumni-mytab-foto {
        position: relative; aspect-ratio: 1; border-radius: 10px;
        overflow: hidden; cursor: pointer; background: #1e293b;
    }
    .alumni-mytab-foto img { width:100%; height:100%; object-fit:cover; transition:transform .4s; display:block; }
    .alumni-mytab-foto:hover img { transform: scale(1.07); }
    .alumni-mytab-foto-ov {
        position:absolute; inset:0; background:rgba(0,0,0,0);
        display:flex; align-items:center; justify-content:center; transition:background .25s;
    }
    .alumni-mytab-foto:hover .alumni-mytab-foto-ov { background:rgba(0,0,0,.4); }
    .alumni-mytab-zoom {
        width:40px; height:40px; background:rgba(59,130,246,.9); border-radius:50%;
        display:flex; align-items:center; justify-content:center;
        opacity:0; transform:scale(.6); transition:opacity .2s,transform .2s;
    }
    .alumni-mytab-foto:hover .alumni-mytab-zoom { opacity:1; transform:scale(1); }
    .alumni-mytab-zoom svg { width:20px; height:20px; fill:#fff; }
    .alumni-mytab-empty {
        border: 2px dashed rgba(100,116,139,.2); border-radius: 16px;
        padding: 48px; text-align: center; color: #64748b;
    }
    </style>

    <div id="alumni-mytab-root">
        <div class="alumni-mytab-header">
            <h2>🎓 Minhas Turmas</h2>
            <p>Reviva os momentos das turmas em que você participou.</p>
        </div>

        <?php if ( empty( $galerias ) ) : ?>
            <div class="alumni-mytab-empty">
                <span class="material-symbols-outlined" style="font-size:48px;display:block;margin-bottom:12px;color:#94a3b8;">photo_library</span>
                <strong style="display:block;margin-bottom:8px;">Nenhuma galeria disponível</strong>
                <p style="font-size:13px;margin:0;">As fotos das suas turmas aparecerão aqui assim que forem publicadas.</p>
            </div>
        <?php else : ?>
            <?php foreach ( $galerias as $galeria ) :
                $pid_g = $galeria['product_id'];
            ?>
                <div class="alumni-mytab-product">
                    <p class="alumni-mytab-product-name">
                        <span class="material-symbols-outlined" style="font-size:18px;color:#3b82f6;">collections</span>
                        <a href="<?php echo esc_url( $galeria['product_url'] ); ?>"><?php echo esc_html( $galeria['product_title'] ); ?></a>
                    </p>

                    <div class="alumni-mytab-tabs">
                        <?php foreach ( $galeria['turmas'] as $ti => $turma ) : ?>
                            <button type="button"
                                class="alumni-mytab-tab <?php echo $ti === 0 ? 'active' : ''; ?>"
                                onclick="alumniMytabSwitch(this,'<?php echo esc_attr( $pid_g ); ?>')"
                                data-target="alumni-mytab-panel-<?php echo esc_attr( $pid_g ); ?>-<?php echo esc_attr( $turma['id'] ); ?>"
                            ><?php echo esc_html( $turma['title'] ); ?></button>
                        <?php endforeach; ?>
                    </div>

                    <?php foreach ( $galeria['turmas'] as $ti => $turma ) : ?>
                        <div class="alumni-mytab-panel <?php echo $ti === 0 ? 'active' : ''; ?>"
                             id="alumni-mytab-panel-<?php echo esc_attr( $pid_g ); ?>-<?php echo esc_attr( $turma['id'] ); ?>">
                            <div class="alumni-mytab-grid">
                                <?php foreach ( $turma['fotos'] as $fi => $foto ) : ?>
                                    <div class="alumni-mytab-foto"
                                         onclick="alumniMytabLb(<?php echo esc_js( wp_json_encode( array_column( $turma['fotos'], 'full' ) ) ); ?>,<?php echo esc_js( wp_json_encode( array_column( $turma['fotos'], 'alt' ) ) ); ?>,<?php echo esc_attr( $fi ); ?>,'<?php echo esc_js( $turma['title'] ); ?>')"
                                         role="button" tabindex="0">
                                        <img src="<?php echo esc_url( $foto['thumb'] ); ?>" alt="<?php echo esc_attr( $foto['alt'] ); ?>" loading="lazy">
                                        <div class="alumni-mytab-foto-ov">
                                            <div class="alumni-mytab-zoom">
                                                <svg viewBox="0 0 24 24"><path d="M15.5 14h-.79l-.28-.27A6.5 6.5 0 1 0 14 15.5c0-.29-.02-.58-.07-.86L15.5 14zm-6 0A4.5 4.5 0 1 1 14 9.5 4.5 4.5 0 0 1 9.5 14zm2.5-4.5h-2.5V7H8.5v2.5H6V11h2.5v2.5H10V11h2.5z"/></svg>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Lightbox inline (reutilizável) -->
    <div id="alumni-lb2" onclick="if(event.target===this)alumniLb2Close()" style="display:none;position:fixed;inset:0;z-index:10000;background:rgba(0,0,0,.95);align-items:center;justify-content:center;backdrop-filter:blur(4px);">
        <button onclick="alumniLb2Close()" style="position:fixed;top:20px;right:20px;width:44px;height:44px;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.15);border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;">
            <svg width="20" height="20" viewBox="0 0 24 24" stroke="#fff" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
        <button onclick="alumniLb2Nav(-1)" style="position:fixed;left:16px;top:50%;transform:translateY(-50%);width:44px;height:44px;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.15);border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;">
            <svg width="22" height="22" viewBox="0 0 24 24" stroke="#fff" stroke-width="2.5" fill="none"><polyline points="15 18 9 12 15 6"/></svg>
        </button>
        <div style="display:flex;flex-direction:column;align-items:center;gap:14px;max-width:90vw;">
            <img id="alumni-lb2-img" src="" alt="" style="max-width:90vw;max-height:80vh;object-fit:contain;border-radius:12px;box-shadow:0 30px 60px rgba(0,0,0,.8);">
            <div id="alumni-lb2-cap" style="font-size:13px;color:rgba(255,255,255,.45);letter-spacing:.05em;"></div>
        </div>
        <button onclick="alumniLb2Nav(1)" style="position:fixed;right:16px;top:50%;transform:translateY(-50%);width:44px;height:44px;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.15);border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;">
            <svg width="22" height="22" viewBox="0 0 24 24" stroke="#fff" stroke-width="2.5" fill="none"><polyline points="9 18 15 12 9 6"/></svg>
        </button>
        <div id="alumni-lb2-cnt" style="position:fixed;bottom:24px;left:50%;transform:translateX(-50%);font-size:12px;color:rgba(255,255,255,.4);letter-spacing:.1em;font-weight:600;"></div>
    </div>

    <script>
    (function(){
        var _imgs=[],_alts=[],_idx=0,_turma='';

        window.alumniMytabSwitch=function(btn,pid){
            btn.closest('.alumni-mytab-tabs').querySelectorAll('.alumni-mytab-tab').forEach(function(b){b.classList.remove('active');});
            btn.classList.add('active');
            document.querySelectorAll('[id^="alumni-mytab-panel-'+pid+'-"]').forEach(function(p){p.classList.remove('active');});
            var t=document.getElementById(btn.getAttribute('data-target'));
            if(t)t.classList.add('active');
        };

        window.alumniMytabLb=function(imgs,alts,idx,turma){
            _imgs=typeof imgs==='string'?JSON.parse(imgs):imgs;
            _alts=typeof alts==='string'?JSON.parse(alts):alts;
            _idx=parseInt(idx,10)||0; _turma=turma||'';
            _render();
            var lb=document.getElementById('alumni-lb2');
            lb.style.display='flex';
            document.body.style.overflow='hidden';
            document.addEventListener('keydown',_key);
        };

        window.alumniLb2Close=function(){
            document.getElementById('alumni-lb2').style.display='none';
            document.body.style.overflow='';
            document.removeEventListener('keydown',_key);
        };

        window.alumniLb2Nav=function(dir){
            _idx=(_idx+dir+_imgs.length)%_imgs.length; _render();
        };

        function _render(){
            var img=document.getElementById('alumni-lb2-img');
            var cap=document.getElementById('alumni-lb2-cap');
            var cnt=document.getElementById('alumni-lb2-cnt');
            if(!img)return;
            var n=new Image();
            n.onload=function(){img.src=n.src;img.alt=_alts[_idx]||'';};
            n.src=_imgs[_idx];
            if(cap)cap.textContent=_turma+(_alts[_idx]?' — '+_alts[_idx]:'');
            if(cnt)cnt.textContent=(_idx+1)+' / '+_imgs.length;
        }

        function _key(e){
            if(e.key==='Escape')alumniLb2Close();
            if(e.key==='ArrowLeft')alumniLb2Nav(-1);
            if(e.key==='ArrowRight')alumniLb2Nav(1);
        }
    })();
    </script>
    <?php
}
