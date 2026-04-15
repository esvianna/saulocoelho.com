<?php
/**
 * My Account page — layout em duas colunas (sidebar + conteúdo) do tema Saulo Coelho.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/my-account.php.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.0
 */

defined( 'ABSPATH' ) || exit;

// Carrega os ícones do Google Material Symbols caso não existam no tema
echo '<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />';
?>

<style>
/* OVERRIDE DEFINITIVO DO WOOCOMMERCE CORE */
nav.woocommerce-MyAccount-navigation {
    float: none !important;
    width: 100% !important;
}
div.woocommerce-MyAccount-content {
    float: none !important;
    width: 100% !important;
}
nav.woocommerce-MyAccount-navigation ul {
    display: none !important;
}
.saulocoelho-dashboard-sidebar a {
    display: flex !important;
    flex-direction: row !important;
    align-items: center !important;
    white-space: nowrap !important;
    gap: 0.75rem !important;
    text-align: left !important;
}
.saulocoelho-dashboard-sidebar a span.material-symbols-outlined {
    flex-shrink: 0 !important;
    display: inline-block !important;
}
.saulocoelho-dashboard-wrapper {
    display: flex !important;
    gap: 3rem !important;
}
@media (min-width: 1024px) {
    .saulocoelho-dashboard-wrapper {
        flex-direction: row !important;
    }
}
</style>

<div class="max-w-7xl mx-auto saulocoelho-dashboard-wrapper mt-10 lg:mt-20 px-6 min-h-[60vh] w-full items-start">
    <aside class="saulocoelho-dashboard-sidebar w-full lg:w-72 shrink-0 flex flex-col border border-slate-200 dark:border-white/5 bg-white dark:bg-[#0f172a] rounded-xl overflow-hidden mb-6 lg:mb-0 shadow-xl h-fit">
        <?php
        /**
         * My Account navigation.
         */
        do_action( 'woocommerce_account_navigation' );
        ?>
    </aside>

    <main class="saulocoelho-dashboard-main flex-1 w-full min-w-0">
        <div class="woocommerce-MyAccount-content">
            <?php
            /**
             * My Account content.
             */
            do_action( 'woocommerce_account_content' );
            ?>
        </div>
    </main>
</div>
