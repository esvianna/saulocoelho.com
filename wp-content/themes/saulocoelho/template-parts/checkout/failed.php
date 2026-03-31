<?php
/**
 * Template de Pagamento Falhou
 */
if ( ! defined( 'ABSPATH' ) ) exit;

$checkout_url = wc_get_checkout_url();
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="dark">
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: '#0d6efd',
                        dark: '#0f172a',
                    }
                }
            }
        }
    </script>
    <style>
        body { background-color: #0f172a; color: white; -webkit-font-smoothing: antialiased; }
        .glass-card { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 24px; }
        .btn-primary { background: #0d6efd; color: white; border-radius: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em; transition: all 0.3s; }
        .btn-primary:hover { background: #0b5ed7; transform: translateY(-2px); box-shadow: 0 10px 20px -5px rgba(13, 110, 253, 0.4); }
        .btn-secondary { background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); color: white; border-radius: 12px; font-weight: 600; transition: all 0.3s; }
        .btn-secondary:hover { background: rgba(255, 255, 255, 0.1); }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-6 relative">
    <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-red-500/10 blur-[120px] rounded-full"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-primary/10 blur-[120px] rounded-full"></div>

    <div class="w-full max-w-xl z-10 text-center">
        <!-- Error Icon -->
        <div class="mb-8 flex justify-center">
            <div class="w-24 h-24 bg-red-500/20 text-red-400 rounded-full flex items-center justify-center border-2 border-red-500/30">
                <span class="material-symbols-outlined text-5xl">warning</span>
            </div>
        </div>

        <h1 class="text-4xl font-black mb-4">Algo deu errado com o pagamento...</h1>
        <p class="text-white/60 text-lg mb-10 max-w-md mx-auto">Não se preocupe! Pode ter sido apenas uma instabilidade com a operadora. Vamos tentar de novo?</p>

        <div class="glass-card p-10 flex flex-col sm:flex-row gap-4 justify-center">
            <a href="<?php echo esc_url($checkout_url); ?>" class="btn-primary w-full p-5 flex items-center justify-center gap-3">
                <span class="material-symbols-outlined">refresh</span>
                Tentar Novamente
            </a>
            <a href="<?php echo esc_url(home_url()); ?>" class="btn-secondary w-full p-5 flex items-center justify-center gap-3">
                Ir para o Início
            </a>
        </div>

        <p class="mt-8 text-white/30 text-xs">Se o erro persistir, nossa equipe estará pronta para te ajudar via WhatsApp.</p>
    </div>

    <?php wp_footer(); ?>
</body>
</html>
