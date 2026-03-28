<?php
/**
 * My Account page (Custom UI Premium Overhaul)
 *
 * @package WooCommerce\Templates
 * @version 3.5.0
 */

defined( 'ABSPATH' ) || exit;

// Carrega os ícones do Google Material Symbols caso não existam no tema
echo '<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />';
?>

<div class="saulocoelho-dashboard-wrapper flex flex-col lg:flex-row mt-10 lg:mt-20 px-4 lg:px-0 gap-8 min-h-[60vh]">
    <aside class="saulocoelho-dashboard-sidebar w-full lg:w-64 flex flex-col border border-slate-200 dark:border-white/5 bg-white dark:bg-[#0f172a] rounded-xl overflow-hidden mb-6 lg:mb-0 shadow-sm h-fit">
        <?php
        /**
         * My Account navigation.
         */
        do_action( 'woocommerce_account_navigation' );
        ?>
    </aside>

    <main class="saulocoelho-dashboard-main flex-1 w-full lg:min-w-[700px]">
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
