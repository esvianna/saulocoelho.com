<?php
/**
 * My Account navigation (Custom Sidebar React Style)
 *
 * @package WooCommerce\Templates
 * @version 2.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

do_action( 'woocommerce_before_account_navigation' );
?>

<nav class="woocommerce-MyAccount-navigation">
    <div class="flex flex-col gap-2 p-6">
        <p class="text-xs font-bold uppercase tracking-widest text-[#64748b] mb-4">Minha Conta</p>
        
        <?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) : 
            
            // Map WooCommerce Endpoints to Material Icons
            $icon = 'chevron_right';
            if ($endpoint === 'dashboard') $icon = 'dashboard';
            if ($endpoint === 'orders') { $icon = 'school'; $label = 'Meus Cursos'; }
            if ($endpoint === 'downloads') { $icon = 'workspace_premium'; $label = 'Certificados (Downloads)'; }
            if ($endpoint === 'edit-address' || $endpoint === 'edit-account') $icon = 'settings';
            if ($endpoint === 'customer-logout') $icon = 'logout';
            
            // Styling based on endpoint and state
            $is_active = wc_get_account_menu_item_classes( $endpoint );
            $active_class = strpos($is_active, 'is-active') !== false ? 'bg-[#3b82f6]/10 text-[#3b82f6]' : 'text-[#64748b] dark:text-[#94a3b8] hover:bg-[#f1f5f9] dark:hover:bg-white/5';
            
            // Logout specific styling
            if ($endpoint === 'customer-logout') :
                ?>
                </div> <!-- End top logic -->
                <div class="p-6 border-t border-slate-200 dark:border-white/5 bg-slate-50 dark:bg-black/20">
                    <a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>" class="flex items-center gap-3 text-sm font-bold text-red-500 hover:text-red-600 transition-colors">
                        <span class="material-symbols-outlined"><?php echo esc_html($icon); ?></span>
                        Sair da Conta
                    </a>
                </div>
                <?php continue; 
            endif;
        ?>
            <a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>" class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium transition-colors <?php echo esc_attr( $active_class ); ?>">
                <span class="material-symbols-outlined rounded bg-transparent p-0 m-0" style="font-size: 20px;"><?php echo esc_html($icon); ?></span>
                <?php echo esc_html( $label ); ?>
            </a>
            
        <?php endforeach; ?>
    <?php // Div opened at top is closed by the loop logic OR here if logout missing ?>
    <?php if ( ! isset( wp_get_current_user()->ID ) ) echo '</div>'; ?>
</nav>

<?php do_action( 'woocommerce_after_account_navigation' ); ?>
