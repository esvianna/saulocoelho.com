<?php
/**
 * Template Part: Alumni Gallery Section
 *
 * Exibe a seção "Memórias das Nossas Turmas" na página de produto.
 * - Lista as turmas selecionadas pelo admin (ama_course IDs)
 * - Tabs por turma com grid de fotos
 * - Lightbox Vanilla JS puro (sem dependências externas)
 * - Galeria pública (Fase 1)
 *
 * @package SauloCoelho
 */

$pid             = get_the_ID();
$selected_turmas = get_post_meta( $pid, '_alumni_turmas', true );

if ( ! is_array( $selected_turmas ) || empty( $selected_turmas ) ) {
    return; // Nada a exibir
}

// Textos editáveis (com fallback para os padrões)
$alumni_badge     = get_post_meta( $pid, '_alumni_badge', true )     ?: 'Memórias das Turmas';
$alumni_titulo    = get_post_meta( $pid, '_alumni_titulo', true )    ?: 'Momentos que ficam para sempre';
$alumni_subtitulo = get_post_meta( $pid, '_alumni_subtitulo', true ) ?: 'Reviva os melhores momentos das nossas turmas';

// Montar dados das turmas com suas fotos
$turmas_data = [];
foreach ( $selected_turmas as $course_id ) {
    $course = get_post( $course_id );
    if ( ! $course || $course->post_status !== 'publish' ) continue;

    $fotos_ids = get_post_meta( $pid, '_alumni_fotos_' . $course_id, true );
    if ( ! is_array( $fotos_ids ) ) $fotos_ids = [];

    // Filtrar apenas IDs válidos com imagens
    $fotos = [];
    foreach ( $fotos_ids as $img_id ) {
        $full  = wp_get_attachment_image_src( $img_id, 'large' );
        $thumb = wp_get_attachment_image_src( $img_id, 'medium' );
        $alt   = get_post_meta( $img_id, '_wp_attachment_image_alt', true ) ?: esc_html( $course->post_title );
        if ( $full ) {
            $fotos[] = [
                'id'    => $img_id,
                'full'  => $full[0],
                'thumb' => $thumb ? $thumb[0] : $full[0],
                'alt'   => $alt,
            ];
        }
    }

    $turmas_data[] = [
        'id'    => $course_id,
        'title' => $course->post_title,
        'fotos' => $fotos,
    ];
}

// Não exibir se todas as turmas estiverem sem fotos
$has_any_photo = false;
foreach ( $turmas_data as $t ) {
    if ( ! empty( $t['fotos'] ) ) { $has_any_photo = true; break; }
}
if ( ! $has_any_photo ) return;

$first_id = $turmas_data[0]['id'];
?>

<!-- Alumni Gallery Section -->
<section class="alumni-gallery-section" id="alumni-galeria" aria-label="Galeria de Turmas Alumni">

    <style>
    /* ═══════════════════════════════════════════════════
       ALUMNI GALLERY — Premium Dark Theme
       ═══════════════════════════════════════════════════ */
    .alumni-gallery-section {
        background: #0A0E1A;
        padding: 80px 0 96px;
        border-top: 1px solid rgba(255, 255, 255, 0.06);
        position: relative;
        overflow: hidden;
    }

    /* Glow de fundo sutil */
    .alumni-gallery-section::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -10%;
        width: 60%;
        height: 100%;
        background: radial-gradient(ellipse at center, rgba(19, 127, 236, 0.08) 0%, transparent 70%);
        pointer-events: none;
    }

    .alumni-container {
        max-width: 1280px;
        margin: 0 auto;
        padding: 0 24px;
        position: relative;
        z-index: 1;
    }

    /* Cabeçalho */
    .alumni-header {
        text-align: center;
        margin-bottom: 48px;
        opacity: 0;
        transform: translateY(24px);
        transition: opacity 0.6s ease, transform 0.6s ease;
    }

    .alumni-header.visible {
        opacity: 1;
        transform: translateY(0);
    }

    .alumni-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(19, 127, 236, 0.1);
        border: 1px solid rgba(19, 127, 236, 0.25);
        border-radius: 999px;
        padding: 6px 18px;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 0.25em;
        text-transform: uppercase;
        color: #C5A059;
        margin-bottom: 24px;
    }

    .alumni-title {
        font-size: clamp(28px, 5vw, 48px);
        font-weight: 900;
        color: #ffffff;
        letter-spacing: -0.03em;
        line-height: 1.1;
        margin: 0 0 12px;
    }

    .alumni-subtitle {
        font-size: 16px;
        font-weight: 300;
        color: rgba(255, 255, 255, 0.45);
        letter-spacing: 0.02em;
    }

    /* Tabs de turmas */
    .alumni-tabs {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        justify-content: center;
        margin-bottom: 40px;
        opacity: 0;
        transform: translateY(20px);
        transition: opacity 0.6s ease 0.15s, transform 0.6s ease 0.15s;
    }

    .alumni-tabs.visible {
        opacity: 1;
        transform: translateY(0);
    }

    .alumni-tab-btn {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: rgba(255, 255, 255, 0.6);
        padding: 10px 22px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.05em;
        cursor: pointer;
        transition: all 0.25s ease;
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        white-space: nowrap;
    }

    .alumni-tab-btn:hover {
        background: rgba(19, 127, 236, 0.15);
        border-color: rgba(19, 127, 236, 0.4);
        color: #fff;
        transform: translateY(-2px);
    }

    .alumni-tab-btn.active {
        background: #C5A059;
        border-color: #C5A059;
        color: #fff;
        box-shadow: 0 0 24px rgba(19, 127, 236, 0.35);
    }

    /* Painéis de galeria */
    .alumni-panel {
        display: none;
    }

    .alumni-panel.active {
        display: block;
    }

    /* Grid de fotos */
    .alumni-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
    }

    @media (min-width: 640px)  { .alumni-grid { grid-template-columns: repeat(3, 1fr); } }
    @media (min-width: 1024px) { .alumni-grid { grid-template-columns: repeat(4, 1fr); } }

    /* Item de foto */
    .alumni-foto-item {
        position: relative;
        aspect-ratio: 1;
        border-radius: 12px;
        overflow: hidden;
        cursor: pointer;
        background: #050A14;
        opacity: 0;
        transform: scale(0.95);
        transition: opacity 0.4s ease, transform 0.4s ease;
    }

    .alumni-foto-item.visible {
        opacity: 1;
        transform: scale(1);
    }

    .alumni-foto-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.45s ease;
        display: block;
    }

    .alumni-foto-item:hover img {
        transform: scale(1.08);
    }

    /* Overlay ao hover */
    .alumni-foto-overlay {
        position: absolute;
        inset: 0;
        background: rgba(0, 0, 0, 0);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.3s ease;
        border-radius: 12px;
    }

    .alumni-foto-item:hover .alumni-foto-overlay {
        background: rgba(0, 0, 0, 0.45);
    }

    .alumni-foto-zoom-icon {
        width: 48px;
        height: 48px;
        background: rgba(19, 127, 236, 0.9);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transform: scale(0.6);
        transition: opacity 0.25s ease, transform 0.25s ease;
        backdrop-filter: blur(4px);
    }

    .alumni-foto-item:hover .alumni-foto-zoom-icon {
        opacity: 1;
        transform: scale(1);
    }

    .alumni-foto-zoom-icon svg {
        width: 24px;
        height: 24px;
        fill: #ffffff;
    }

    /* Empty state */
    .alumni-empty {
        text-align: center;
        padding: 48px;
        color: rgba(255, 255, 255, 0.3);
        font-size: 14px;
        font-weight: 300;
    }

    /* ═══════════════════════════════════════════════════
       LIGHTBOX
       ═══════════════════════════════════════════════════ */
    #alumni-lightbox {
        position: fixed;
        inset: 0;
        z-index: 10000;
        background: rgba(0, 0, 0, 0.95);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.3s ease;
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
    }

    #alumni-lightbox.open {
        opacity: 1;
        pointer-events: all;
    }

    .alumni-lb-content {
        position: relative;
        max-width: 90vw;
        max-height: 90vh;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 16px;
        transform: scale(0.93);
        transition: transform 0.3s ease;
    }

    #alumni-lightbox.open .alumni-lb-content {
        transform: scale(1);
    }

    .alumni-lb-img-wrap {
        position: relative;
        max-width: 90vw;
        max-height: 78vh;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 40px 80px rgba(0,0,0,0.8);
    }

    #alumni-lb-img {
        max-width: 90vw;
        max-height: 78vh;
        object-fit: contain;
        display: block;
        border-radius: 12px;
        transition: opacity 0.2s ease;
    }

    #alumni-lb-img.loading {
        opacity: 0;
    }

    .alumni-lb-caption {
        font-size: 13px;
        color: rgba(255, 255, 255, 0.5);
        font-weight: 300;
        letter-spacing: 0.05em;
        text-align: center;
    }

    /* Botão fechar */
    .alumni-lb-close {
        position: fixed;
        top: 20px;
        right: 20px;
        width: 44px;
        height: 44px;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 50%;
        color: #fff;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.2s ease, transform 0.2s ease;
        z-index: 10001;
        backdrop-filter: blur(8px);
    }

    .alumni-lb-close:hover {
        background: rgba(19, 127, 236, 0.6);
        transform: scale(1.1);
    }

    .alumni-lb-close svg {
        width: 20px;
        height: 20px;
        stroke: #fff;
        stroke-width: 2;
        fill: none;
    }

    /* Botões de navegação */
    .alumni-lb-nav {
        position: fixed;
        top: 50%;
        transform: translateY(-50%);
        width: 48px;
        height: 48px;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 50%;
        color: #fff;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.2s ease, transform 0.2s ease;
        z-index: 10001;
        backdrop-filter: blur(8px);
    }

    .alumni-lb-nav:hover {
        background: rgba(19, 127, 236, 0.6);
    }

    .alumni-lb-prev { left: 20px; }
    .alumni-lb-next { right: 20px; }

    .alumni-lb-nav:hover { transform: translateY(-50%) scale(1.1); }

    .alumni-lb-nav svg {
        width: 22px;
        height: 22px;
        stroke: #fff;
        stroke-width: 2.5;
        fill: none;
    }

    /* Counter */
    .alumni-lb-counter {
        position: fixed;
        bottom: 24px;
        left: 50%;
        transform: translateX(-50%);
        font-size: 12px;
        color: rgba(255,255,255,0.4);
        letter-spacing: 0.1em;
        font-weight: 600;
        z-index: 10001;
    }

    /* Touch / responsivo */
    @media (max-width: 640px) {
        .alumni-lb-nav { display: none; }
    }
    </style>

    <div class="alumni-container">

        <!-- Cabeçalho -->
        <div class="alumni-header" id="alumni-header-<?php echo esc_attr( $pid ); ?>">
            <span class="alumni-eyebrow">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/></svg>
                <?php echo esc_html( $alumni_badge ); ?>
            </span>
            <h2 class="alumni-title"><?php echo esc_html( $alumni_titulo ); ?></h2>
            <p class="alumni-subtitle"><?php echo esc_html( $alumni_subtitulo ); ?></p>
        </div>

        <!-- Tabs de turmas -->
        <div class="alumni-tabs" id="alumni-tabs-<?php echo esc_attr( $pid ); ?>">
            <?php foreach ( $turmas_data as $index => $turma ) :
                if ( empty( $turma['fotos'] ) ) continue;
            ?>
                <button
                    type="button"
                    class="alumni-tab-btn <?php echo $index === 0 ? 'active' : ''; ?>"
                    data-target="alumni-panel-<?php echo esc_attr( $pid ); ?>-<?php echo esc_attr( $turma['id'] ); ?>"
                    onclick="alumniSwitchTab(this, '<?php echo esc_attr( $pid ); ?>')"
                >
                    <?php echo esc_html( $turma['title'] ); ?>
                </button>
            <?php endforeach; ?>
        </div>

        <!-- Painéis de galeria -->
        <?php foreach ( $turmas_data as $index => $turma ) :
            if ( empty( $turma['fotos'] ) ) continue;
        ?>
            <div
                class="alumni-panel <?php echo $index === 0 ? 'active' : ''; ?>"
                id="alumni-panel-<?php echo esc_attr( $pid ); ?>-<?php echo esc_attr( $turma['id'] ); ?>"
                data-turma="<?php echo esc_attr( $turma['title'] ); ?>"
            >
                <?php if ( empty( $turma['fotos'] ) ) : ?>
                    <div class="alumni-empty">Nenhuma foto disponível para esta turma.</div>
                <?php else : ?>
                    <div class="alumni-grid">
                        <?php foreach ( $turma['fotos'] as $fi => $foto ) : ?>
                            <div
                                class="alumni-foto-item"
                                onclick="alumniOpenLightbox(
                                    <?php echo esc_js( wp_json_encode( array_column( $turma['fotos'], 'full' ) ) ); ?>,
                                    <?php echo esc_js( wp_json_encode( array_column( $turma['fotos'], 'alt' ) ) ); ?>,
                                    <?php echo esc_attr( $fi ); ?>,
                                    '<?php echo esc_js( $turma['title'] ); ?>'
                                )"
                                role="button"
                                tabindex="0"
                                aria-label="Ver foto <?php echo $fi + 1; ?> da turma <?php echo esc_attr( $turma['title'] ); ?>"
                            >
                                <img
                                    src="<?php echo esc_url( $foto['thumb'] ); ?>"
                                    alt="<?php echo esc_attr( $foto['alt'] ); ?>"
                                    loading="lazy"
                                    decoding="async"
                                >
                                <div class="alumni-foto-overlay">
                                    <div class="alumni-foto-zoom-icon">
                                        <svg viewBox="0 0 24 24"><path d="M15.5 14h-.79l-.28-.27A6.5 6.5 0 1 0 14 15.5c0-.29-.02-.58-.07-.86L15.5 14zm-6 0A4.5 4.5 0 1 1 14 9.5 4.5 4.5 0 0 1 9.5 14zm2.5-4.5h-2.5V7H8.5v2.5H6V11h2.5v2.5H10V11h2.5z"/></svg>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

    </div><!-- .alumni-container -->

</section>

<!-- ══════════════════════════════════════════════ -->
<!-- LIGHTBOX (compartilhado, injetado uma vez)     -->
<!-- ══════════════════════════════════════════════ -->
<div id="alumni-lightbox" role="dialog" aria-modal="true" aria-label="Lightbox de fotos" onclick="alumniLightboxBgClick(event)">
    <!-- Botão fechar -->
    <button class="alumni-lb-close" onclick="alumniCloseLightbox()" aria-label="Fechar">
        <svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
    </button>

    <!-- Navegar anterior -->
    <button class="alumni-lb-nav alumni-lb-prev" onclick="alumniLbNav(-1)" aria-label="Foto anterior">
        <svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
    </button>

    <!-- Imagem central -->
    <div class="alumni-lb-content">
        <div class="alumni-lb-img-wrap">
            <img id="alumni-lb-img" src="" alt="">
        </div>
        <div class="alumni-lb-caption" id="alumni-lb-caption"></div>
    </div>

    <!-- Navegar próxima -->
    <button class="alumni-lb-nav alumni-lb-next" onclick="alumniLbNav(1)" aria-label="Próxima foto">
        <svg viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
    </button>

    <!-- Counter -->
    <div class="alumni-lb-counter" id="alumni-lb-counter"></div>
</div>

<script>
(function() {
    'use strict';

    // ── Estado do lightbox ───────────────────────────
    var _lbImages  = [];
    var _lbAlts    = [];
    var _lbIndex   = 0;
    var _lbTurma   = '';

    // ── Abrir lightbox ───────────────────────────────
    window.alumniOpenLightbox = function(imagesJson, altsJson, startIndex, turmaTitle) {
        _lbImages  = typeof imagesJson === 'string' ? JSON.parse(imagesJson) : imagesJson;
        _lbAlts    = typeof altsJson   === 'string' ? JSON.parse(altsJson)   : altsJson;
        _lbIndex   = parseInt(startIndex, 10) || 0;
        _lbTurma   = turmaTitle || '';

        _renderLbImage();

        var lb = document.getElementById('alumni-lightbox');
        lb.classList.add('open');
        document.body.style.overflow = 'hidden';

        // Bind teclado
        document.addEventListener('keydown', _lbKeyHandler);
    };

    // ── Fechar lightbox ──────────────────────────────
    window.alumniCloseLightbox = function() {
        var lb = document.getElementById('alumni-lightbox');
        lb.classList.remove('open');
        document.body.style.overflow = '';
        document.removeEventListener('keydown', _lbKeyHandler);
    };

    // ── Click no fundo fecha ─────────────────────────
    window.alumniLightboxBgClick = function(e) {
        if (e.target === document.getElementById('alumni-lightbox')) {
            alumniCloseLightbox();
        }
    };

    // ── Navegar ──────────────────────────────────────
    window.alumniLbNav = function(dir) {
        _lbIndex = (_lbIndex + dir + _lbImages.length) % _lbImages.length;
        _renderLbImage();
    };

    // ── Renderizar imagem no lightbox ────────────────
    function _renderLbImage() {
        var img     = document.getElementById('alumni-lb-img');
        var caption = document.getElementById('alumni-lb-caption');
        var counter = document.getElementById('alumni-lb-counter');

        img.classList.add('loading');

        var newImg = new Image();
        newImg.onload = function() {
            img.src = newImg.src;
            img.alt = _lbAlts[_lbIndex] || _lbTurma;
            img.classList.remove('loading');
        };
        newImg.src = _lbImages[_lbIndex];

        caption.textContent = _lbTurma + (_lbAlts[_lbIndex] ? ' — ' + _lbAlts[_lbIndex] : '');
        counter.textContent = (_lbIndex + 1) + ' / ' + _lbImages.length;
    }

    // ── Teclado ──────────────────────────────────────
    function _lbKeyHandler(e) {
        switch (e.key) {
            case 'Escape':    alumniCloseLightbox(); break;
            case 'ArrowLeft': alumniLbNav(-1);       break;
            case 'ArrowRight':alumniLbNav(1);        break;
        }
    }

    // ── Switch de tabs ───────────────────────────────
    window.alumniSwitchTab = function(btn, pid) {
        var targetId = btn.getAttribute('data-target');

        // Desativar todas as tabs
        btn.closest('.alumni-tabs').querySelectorAll('.alumni-tab-btn').forEach(function(b) {
            b.classList.remove('active');
        });
        btn.classList.add('active');

        // Esconder todos os painéis
        document.querySelectorAll('[id^="alumni-panel-' + pid + '-"]').forEach(function(panel) {
            panel.classList.remove('active');
        });

        // Mostrar painel alvo
        var target = document.getElementById(targetId);
        if (target) {
            target.classList.add('active');
            // Animar itens novos
            setTimeout(function() {
                target.querySelectorAll('.alumni-foto-item').forEach(function(item, i) {
                    setTimeout(function() {
                        item.classList.add('visible');
                    }, i * 40);
                });
            }, 10);
        }
    };

    // ── IntersectionObserver para animações ─────────
    document.addEventListener('DOMContentLoaded', function() {
        var observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });

        // Observar header e tabs
        ['alumni-header', 'alumni-tabs'].forEach(function(prefix) {
            document.querySelectorAll('[id^="' + prefix + '-"]').forEach(function(el) {
                observer.observe(el);
            });
        });

        // Animar fotos do primeiro painel ativo
        document.querySelectorAll('.alumni-panel.active .alumni-foto-item').forEach(function(item, i) {
            setTimeout(function() {
                item.classList.add('visible');
            }, 100 + i * 50);
        });

        // Suporte a teclado nos foto-items (Enter/Space)
        document.querySelectorAll('.alumni-foto-item').forEach(function(item) {
            item.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    item.click();
                }
            });
        });
    });

})();
</script>
