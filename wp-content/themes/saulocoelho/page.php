<?php
/**
 * The template for displaying all pages
 */

get_header();

// Logic for dynamic subtitle and container width
$post_id = get_the_ID();
$is_woocommerce = false;
if ( function_exists( 'is_woocommerce' ) ) {
    $is_woocommerce = is_cart() || is_checkout() || is_account_page();
}

$subtitle = get_post_meta($post_id, 'page_subtitle', true);
if (!$subtitle) {
    if ($is_woocommerce) {
        $subtitle = 'Portal do Aluno';
    } else {
        $subtitle = 'Institucional';
    }
}

$container_class = $is_woocommerce ? 'max-w-7xl mx-auto' : 'max-w-3xl mx-auto';
?>

<main class="bg-background-dark min-h-screen text-white pb-32">
    <!-- Header Section for Page Title -->
    <div class="relative pt-32 pb-24 overflow-hidden border-b border-white/5 bg-background-dark-alt/50">
        <div class="max-w-7xl mx-auto px-6 lg:px-10 relative z-10 text-center lg:text-left">
            <span class="text-primary font-black tracking-[0.4em] uppercase text-[10px] mb-6 block drop-shadow-lg"><?php echo esc_html($subtitle); ?></span>
            <?php the_title( '<h1 class="text-4xl md:text-7xl font-black leading-[0.95] tracking-tighter text-white uppercase drop-shadow-2xl">', '</h1>' ); ?>
        </div>
        
        <!-- Subtle Glow for Premium Aesthetic -->
        <div class="absolute -top-32 -left-32 w-[600px] h-[600px] bg-primary/5 blur-[150px] rounded-full pointer-events-none"></div>
    </div>

    <!-- Page Content Area -->
    <div class="mx-auto w-full px-6 py-24">
        <?php
        while ( have_posts() ) :
            the_post();
            ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class($container_class); ?>>
                <div class="prose-premium <?php echo $is_woocommerce ? 'max-w-none' : ''; ?>">
                    <?php the_content(); ?>
                </div>
            </article>
        <?php
        endwhile;
        ?>
    </div>
</main>

<?php
get_footer();
