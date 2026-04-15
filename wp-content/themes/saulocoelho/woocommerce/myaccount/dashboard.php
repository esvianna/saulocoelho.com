<?php
/**
 * My Account Dashboard — painel customizado (KPIs, cursos, integração AMA Educacional).
 *
 * Mantém hooks e texto de saudação compatíveis com o fluxo do WooCommerce.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 4.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$current_user = wp_get_current_user();

$allowed_html = array(
	'a' => array(
		'href' => array(),
	),
);
$user_id = $current_user->ID;

// INTEGRAÇÃO DE DADOS AMAEDUCACIONAL
$has_ama = class_exists( '\AmaEducacional\Services\ProgressService' );

$total_active = 0;
$total_completed = 0;
$total_lessons_done = 0;
$enrolled_courses = [];

if ( $has_ama ) {
    global $wpdb;
    
    // Obtém lista de matrículas
    $table = $wpdb->prefix . 'lms_enrollments';
    $rows = $wpdb->get_results( $wpdb->prepare(
        "SELECT course_id, status FROM $table WHERE user_id = %d AND status IN ('active', 'completed') ORDER BY enrolled_at DESC",
        $user_id
    ), ARRAY_A );
    
    if ( $rows ) {
        $enrolled_courses = $rows;
        foreach ( $rows as $row ) {
            if ( $row['status'] === 'active' ) {
                $total_active++;
            } else if ( $row['status'] === 'completed' ) {
                $total_completed++;
            }
        }
    }
    
    // Calcula proxy para Horas de Estudo (Total de Aulas Concluídas globalmente)
    $progress_table = $wpdb->prefix . 'lms_progress';
    $lessons_done = $wpdb->get_var( $wpdb->prepare(
        "SELECT COUNT(id) FROM $progress_table WHERE user_id = %d AND status = 'completed'",
        $user_id
    ) );
    $total_lessons_done = (int) $lessons_done;
}

?>
<div class="saulocoelho-welcome-dash mb-8 flex flex-col gap-2">
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Bem-vindo(a) de volta, <?php echo esc_html( $current_user->display_name ); ?>!</h1>
    <p class="text-sm text-slate-500">Acompanhe seu progresso e retome seus estudos abaixo.</p>
</div>

<!-- KPIs Mocks baseados no Protótipo agora conectados ao Banco de Dados -->
<div class="mb-10 grid grid-cols-1 gap-6 md:grid-cols-3">
    <div class="rounded-xl border border-slate-200 dark:border-white/5 bg-white dark:bg-[#0f172a] p-6 shadow-sm shadow-blue-500/10">
        <div class="flex items-center gap-4">
            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-[#3b82f6]/10 text-[#3b82f6]">
                <span class="material-symbols-outlined">play_lesson</span>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-500">Cursos em Andamento</p>
                <p class="text-2xl font-bold text-slate-900 dark:text-white"><?php echo esc_html( $total_active ); ?></p>
            </div>
        </div>
    </div>
    <div class="rounded-xl border border-slate-200 dark:border-white/5 bg-white dark:bg-[#0f172a] p-6 shadow-sm shadow-green-500/10">
        <div class="flex items-center gap-4">
            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-green-500/10 text-green-500">
                <span class="material-symbols-outlined">workspace_premium</span>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-500">Certificados Concluídos</p>
                <p class="text-2xl font-bold text-slate-900 dark:text-white"><?php echo esc_html( $total_completed ); ?></p>
            </div>
        </div>
    </div>
    <div class="rounded-xl border border-slate-200 dark:border-white/5 bg-white dark:bg-[#0f172a] p-6 shadow-sm shadow-purple-500/10">
        <div class="flex items-center gap-4">
            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-emerald-500/10 text-emerald-500">
                <span class="material-symbols-outlined">inventory</span>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-500">Aulas Concluídas</p>
                <p class="text-2xl font-bold text-slate-900 dark:text-white"><?php echo esc_html( $total_lessons_done ); ?></p>
            </div>
        </div>
    </div>
</div>

<h2 class="mb-6 text-xl font-bold text-slate-900 dark:text-white">Continue Estudando</h2>

<?php if ( $has_ama && ! empty( $enrolled_courses ) ) : ?>
<div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-2">
    <?php 
    $progress_service = new \AmaEducacional\Services\ProgressService();
    $certificate_service = \AmaEducacional\Services\CertificateService::get_instance();
    
    foreach ( $enrolled_courses as $course_data ) : 
        $course_id = (int) $course_data['course_id'];
        $status = $course_data['status'];
        
        $progress = $progress_service->get_progress( $user_id, $course_id );
        
        $title = get_the_title( $course_id );
        $thumb_url = get_the_post_thumbnail_url( $course_id, 'medium' ) ?: 'https://images.unsplash.com/photo-1542744173-8e7e53415bb0?auto=format&fit=crop&w=400&q=80';
        $player_url = esc_url( get_permalink( $course_id ) );
        
        // Verifica se completou
        $is_completed = ($status === 'completed' || $progress >= 100);
    ?>
    
    <div class="group overflow-hidden rounded-xl border border-slate-200 dark:border-white/5 bg-white dark:bg-[#0f172a] shadow-sm transition-all hover:shadow-md flex flex-col">
        <div class="aspect-video overflow-hidden">
            <img alt="<?php echo esc_attr( $title ); ?>" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" src="<?php echo esc_url( $thumb_url ); ?>"/>
        </div>
        <div class="p-6 flex flex-col flex-1">
            <h3 class="mb-4 text-lg font-bold text-slate-900 dark:text-white line-clamp-2"><?php echo esc_html( $title ); ?></h3>
            
            <div class="mt-auto">
                <div class="mb-2 flex items-center justify-between text-sm">
                    <span class="text-slate-500">Progresso</span>
                    <span class="font-bold <?php echo $is_completed ? 'text-green-500' : 'text-[#3b82f6]'; ?>">
                        <?php echo esc_html( $progress ); ?>%
                    </span>
                </div>
                <div class="h-2 w-full overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800">
                    <div class="h-full transition-all duration-1000 <?php echo $is_completed ? 'bg-green-500' : 'bg-[#3b82f6]'; ?>" style="width: <?php echo esc_attr( $progress ); ?>%;"></div>
                </div>
                
                <div class="mt-6 flex gap-3">
                    <a href="<?php echo $player_url; ?>" class="flex-1 rounded-lg bg-[#3b82f6]/10 px-4 py-3 text-sm font-bold text-[#3b82f6] transition-colors hover:bg-[#3b82f6] hover:text-white border-0 cursor-pointer text-center block" style="font-family: inherit;">
                        <?php echo $is_completed ? 'Revisar Aula' : 'Retomar Aula'; ?>
                    </a>
                    
                    <?php if ( $is_completed ) :
                        $download_url = $certificate_service->get_download_url( $course_id );
                        if ( $download_url ) :
                    ?>
                        <a href="<?php echo esc_url( $download_url ); ?>" title="Baixar Certificado" class="flex items-center justify-center rounded-lg bg-green-500/10 px-4 py-3 text-sm font-bold text-green-500 transition-colors hover:bg-green-500 hover:text-white border-0 cursor-pointer block" target="_blank">
                            <span class="material-symbols-outlined text-[20px] pt-1">workspace_premium</span>
                        </a>
                    <?php 
                        endif;
                    endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php else : ?>
<div class="rounded-xl border border-slate-200 dark:border-white/5 bg-slate-50 dark:bg-[#0f172a]/50 p-10 text-center col-span-2 shadow-inner">
    <span class="material-symbols-outlined text-5xl text-slate-400 mb-4 inline-block">school</span>
    <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Nenhum curso iniciado</h3>
    <p class="text-slate-500 mb-6">Explore nossa loja e comece sua jornada ao topo.</p>
    <a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>" class="inline-block rounded-lg bg-[#3b82f6] px-8 py-3 text-sm font-bold text-white transition-colors hover:bg-blue-600 shadow-lg shadow-blue-500/20">
        Ver Treinamentos
    </a>
</div>
<?php endif; ?>

<div class="mt-12 pt-8 border-t border-slate-200 dark:border-white/10 text-slate-500 text-sm">
    <p>
        <?php
        printf(
            /* translators: 1: user display name 2: logout url */
            wp_kses( __( 'Hello %1$s (not %1$s? <a href="%2$s">Log out</a>)', 'woocommerce' ), $allowed_html ),
            '<strong>' . esc_html( $current_user->display_name ) . '</strong>',
            esc_url( wc_logout_url() )
        );
        ?>
    </p>
</div>

<?php
/**
 * My Account dashboard.
 *
 * @since 2.6.0
 */
do_action( 'woocommerce_account_dashboard' );

/**
 * Deprecated woocommerce_before_my_account action.
 *
 * @deprecated 2.6.0
 */
do_action( 'woocommerce_before_my_account' );

/**
 * Deprecated woocommerce_after_my_account action.
 *
 * @deprecated 2.6.0
 */
do_action( 'woocommerce_after_my_account' );
