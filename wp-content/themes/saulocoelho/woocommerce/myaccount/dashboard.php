<?php
/**
 * My Account Dashboard (Custom React Style Overhaul)
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     4.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$current_user = wp_get_current_user();
?>
<div class="saulocoelho-welcome-dash mb-8 flex flex-col gap-2">
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Bem-vindo(a) de volta, <?php echo esc_html( $current_user->display_name ); ?>!</h1>
    <p class="text-sm text-slate-500">Acompanhe seu progresso e retome seus estudos abaixo.</p>
</div>

<!-- KPIs Mocks baseados no Protótipo -->
<div class="mb-10 grid grid-cols-1 gap-6 md:grid-cols-3">
    <div class="rounded-xl border border-slate-200 dark:border-white/5 bg-white dark:bg-[#0f172a] p-6 shadow-sm shadow-blue-500/10">
        <div class="flex items-center gap-4">
            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-[#3b82f6]/10 text-[#3b82f6]">
                <span class="material-symbols-outlined">play_lesson</span>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-500">Cursos em Andamento</p>
                <p class="text-2xl font-bold text-slate-900 dark:text-white">2</p>
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
                <p class="text-2xl font-bold text-slate-900 dark:text-white">5</p>
            </div>
        </div>
    </div>
    <div class="rounded-xl border border-slate-200 dark:border-white/5 bg-white dark:bg-[#0f172a] p-6 shadow-sm shadow-purple-500/10">
        <div class="flex items-center gap-4">
            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-purple-500/10 text-purple-500">
                <span class="material-symbols-outlined">timer</span>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-500">Horas de Estudo</p>
                <p class="text-2xl font-bold text-slate-900 dark:text-white">42h</p>
            </div>
        </div>
    </div>
</div>

<h2 class="mb-6 text-xl font-bold text-slate-900 dark:text-white">Continue Estudando (Protótipo)</h2>
<div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-2">
    <!-- Card Prototype 1 -->
    <div class="group overflow-hidden rounded-xl border border-slate-200 dark:border-white/5 bg-white dark:bg-[#0f172a] shadow-sm transition-all hover:shadow-md">
        <div class="aspect-video overflow-hidden">
            <img alt="Curso" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" src="https://images.unsplash.com/photo-1542744173-8e7e53415bb0?auto=format&fit=crop&w=400&q=80"/>
        </div>
        <div class="p-6">
            <h3 class="mb-4 text-lg font-bold text-slate-900 dark:text-white">Mentoria Business Elite</h3>
            <div class="mb-2 flex items-center justify-between text-sm">
                <span class="text-slate-500">Progresso</span>
                <span class="font-bold text-[#3b82f6]">65%</span>
            </div>
            <div class="h-2 w-full overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800">
                <div class="h-full bg-[#3b82f6] transition-all duration-1000" style="width: 65%;"></div>
            </div>
            <button class="mt-6 w-full rounded-lg bg-[#3b82f6]/10 px-4 py-3 text-sm font-bold text-[#3b82f6] transition-colors hover:bg-[#3b82f6] hover:text-white border-0 cursor-pointer text-center block" style="font-family: inherit;">
                Retomar Aula
            </button>
        </div>
    </div>
    
    <!-- Card Prototype 2 -->
    <div class="group overflow-hidden rounded-xl border border-slate-200 dark:border-white/5 bg-white dark:bg-[#0f172a] shadow-sm transition-all hover:shadow-md">
        <div class="aspect-video overflow-hidden">
            <img alt="Curso" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" src="https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?auto=format&fit=crop&w=400&q=80"/>
        </div>
        <div class="p-6">
            <h3 class="mb-4 text-lg font-bold text-slate-900 dark:text-white">High Performance Leadership</h3>
            <div class="mb-2 flex items-center justify-between text-sm">
                <span class="text-slate-500">Progresso</span>
                <span class="font-bold text-[#3b82f6]">30%</span>
            </div>
            <div class="h-2 w-full overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800">
                <div class="h-full bg-[#3b82f6] transition-all duration-1000" style="width: 30%;"></div>
            </div>
            <button class="mt-6 w-full rounded-lg bg-[#3b82f6]/10 px-4 py-3 text-sm font-bold text-[#3b82f6] transition-colors hover:bg-[#3b82f6] hover:text-white border-0 cursor-pointer text-center block" style="font-family: inherit;">
                Retomar Aula
            </button>
        </div>
    </div>
</div>

<div class="mt-12 pt-8 border-t border-slate-200 dark:border-white/10 text-slate-500 text-sm">
    <p>
        <?php
        printf(
            /* translators: 1: user display name 2: logout url */
            wp_kses( __( 'Hello %1$s (not %1$s? <a href="%2$s">Log out</a>)', 'woocommerce' ), array( 'a' => array( 'href' => array() ) ) ),
            '<strong>' . esc_html( $current_user->display_name ) . '</strong>',
            esc_url( wc_logout_url() )
        );
        ?>
    </p>

    <p>
        <?php
        /* translators: 1: Orders URL 2: Address URL 3: Account URL. */
        $dashboard_desc = __( 'From your account dashboard you can view your <a href="%1$s">recent orders</a>, manage your <a href="%2$s">shipping and billing addresses</a>, and <a href="%3$s">edit your password and account details</a>.', 'woocommerce' );
        if ( wc_shipping_enabled() ) {
            /* translators: 1: Orders URL 2: Addresses URL 3: Account URL. */
            $dashboard_desc = __( 'From your account dashboard you can view your <a href="%1$s">recent orders</a>, manage your <a href="%2$s">shipping and billing addresses</a>, and <a href="%3$s">edit your password and account details</a>.', 'woocommerce' );
        }
        printf(
            wp_kses( $dashboard_desc, array( 'a' => array( 'href' => array() ) ) ),
            esc_url( wc_get_endpoint_url( 'orders' ) ),
            esc_url( wc_get_endpoint_url( 'edit-address' ) ),
            esc_url( wc_get_endpoint_url( 'edit-account' ) )
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
?>
