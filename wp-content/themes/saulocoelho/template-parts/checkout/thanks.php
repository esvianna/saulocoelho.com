<?php
/**
 * Template de Sucesso (Obrigado)
 */
if ( ! defined( 'ABSPATH' ) ) exit;

$order_id = isset( $_GET['order_id'] ) ? intval( $_GET['order_id'] ) : 0;
$order = $order_id ? wc_get_order( $order_id ) : null;
$first_name = $order ? $order->get_billing_first_name() : 'Aluno';
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
        .survey-item { background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 12px; transition: all 0.2s; cursor: pointer; text-align: center; }
        .survey-item:hover { background: rgba(255, 255, 255, 0.1); border-color: #0d6efd; }
        .survey-item.active { background: #0d6efd; border-color: #0d6efd; color: white; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-6 relative">
    <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-primary/20 blur-[120px] rounded-full"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-blue-600/10 blur-[120px] rounded-full"></div>

    <div class="w-full max-w-2xl z-10 text-center">
        <!-- Success Icon Animation -->
        <div class="mb-8 flex justify-center">
            <div class="w-24 h-24 bg-green-500/20 text-green-400 rounded-full flex items-center justify-center border-2 border-green-500/30 scale-110 animate-bounce">
                <span class="material-symbols-outlined text-5xl">check_circle</span>
            </div>
        </div>

        <h1 class="text-4xl font-black mb-4">Parabéns, <?php echo esc_html($first_name); ?>!</h1>
        <p class="text-white/60 text-lg mb-10 max-w-md mx-auto">Sua vaga está garantida. Estamos muito felizes em ter você conosco nesta jornada!</p>

        <div class="glass-card p-10 text-left">
            <h2 class="text-xl font-bold mb-6 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">poll</span>
                Uma pergunta rápida...
            </h2>
            
            <form id="survey-form" class="space-y-8">
                <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                
                <div class="space-y-4">
                    <p class="text-sm font-medium text-white/50 uppercase tracking-widest">Como você conheceu o Saulo Coelho?</p>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3" id="source-options">
                        <div class="survey-item p-4 text-xs font-bold js-survey-btn" data-value="Instagram">Instagram</div>
                        <div class="survey-item p-4 text-xs font-bold js-survey-btn" data-value="Google">Google / Pesquisa</div>
                        <div class="survey-item p-4 text-xs font-bold js-survey-btn" data-value="Indicação">Indicação de Amigo</div>
                        <div class="survey-item p-4 text-xs font-bold js-survey-btn" data-value="Anúncios">Anúncios Online</div>
                        <div class="survey-item p-4 text-xs font-bold js-survey-btn" data-value="YouTube">YouTube</div>
                        <div class="survey-item p-4 text-xs font-bold js-survey-btn" data-value="Outros">Outros</div>
                    </div>
                    <input type="hidden" name="source" id="survey-source" required>
                </div>

                <div class="space-y-4">
                    <p class="text-sm font-medium text-white/50 uppercase tracking-widest">O que você mais espera desse treinamento?</p>
                    <textarea name="expectations" rows="3" class="w-full bg-white/5 border border-white/10 rounded-xl p-4 text-white placeholder-white/20 focus:border-primary outline-none transition-all" placeholder="Quero dominar..." required></textarea>
                </div>

                <button type="submit" class="btn-primary w-full p-5 flex items-center justify-center gap-3">
                    Enviar e Acessar meu Curso
                    <span class="material-symbols-outlined">rocket_launch</span>
                </button>
            </form>

            <div id="survey-success" class="hidden text-center py-6">
                <div class="text-green-400 font-bold mb-4">Respostas enviadas com sucesso! Redirecionando...</div>
                <div class="w-full h-1 bg-white/10 rounded-full overflow-hidden">
                    <div class="h-full bg-primary animate-[loading_3s_linear_forwards]"></div>
                </div>
            </div>
        </div>

        <p class="mt-8 text-white/30 text-xs">A confirmação detalhada do pedido foi enviada para o seu e-mail.</p>
    </div>

    <?php wp_footer(); ?>
    <script>
        jQuery(document).ready(function($) {
            $('.js-survey-btn').click(function() {
                $('.js-survey-btn').removeClass('active');
                $(this).addClass('active');
                $('#survey-source').val($(this).data('value'));
            });

            $('#survey-form').submit(function(e) {
                e.preventDefault();
                var $form = $(this);
                var btn = $form.find('button');
                btn.prop('disabled', true).text('Enviando...');

                $.post('<?php echo admin_url('admin-ajax.php'); ?>', $form.serialize() + '&action=sc_save_survey', function(res) {
                    $form.fadeOut(function() {
                        $('#survey-success').fadeIn();
                        setTimeout(function() {
                            window.location.href = '<?php echo esc_url( home_url('/minha-conta/minhas-turmas/') ); ?>';
                        }, 2500);
                    });
                });
            });
        });

        // Simple animation for loading bar
        var style = document.createElement('style');
        style.innerHTML = '@keyframes loading { from { width: 0; } to { width: 100%; } }';
        document.head.appendChild(style);
    </script>
</body>
</html>
