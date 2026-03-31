<?php
/**
 * Template de Boas-Vindas (Checkout Gate)
 */
if ( ! defined( 'ABSPATH' ) ) exit;

// Generate math captcha for this session
$math_captcha_question = sc_generate_math_captcha();
$nonce = wp_create_nonce( 'sc_gate_nonce' );
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
        .input-premium { background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); color: white; border-radius: 12px; transition: all 0.3s; }
        .input-premium:focus { border-color: #0d6efd; box-shadow: 0 0 15px rgba(13, 110, 253, 0.2); outline: none; }
        .btn-primary { background: #0d6efd; color: white; border-radius: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em; transition: all 0.3s; }
        .btn-primary:hover { background: #0b5ed7; transform: translateY(-2px); box-shadow: 0 10px 20px -5px rgba(13, 110, 253, 0.4); }
        .btn-secondary { background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); color: white; border-radius: 12px; font-weight: 600; transition: all 0.3s; }
        .btn-secondary:hover { background: rgba(255, 255, 255, 0.1); }
        .hidden { display: none; }
        .loader { width: 20px; height: 20px; border: 3px solid rgba(255,255,255,.3); border-radius: 50%; border-top-color: #fff; animation: spin 0.8s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }
        /* Style for person type toggle */
        .type-toggle .active { background: #0d6efd; color: white; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-6 relative overflow-x-hidden">
    <!-- Background Decor -->
    <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-primary/20 blur-[120px] rounded-full pointer-events-none"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-blue-600/10 blur-[120px] rounded-full pointer-events-none"></div>

    <div class="w-full max-w-2xl z-10">
        <!-- Logo / Intro -->
        <div class="text-center mb-10">
            <h1 class="text-4xl font-black tracking-tight mb-3">Seja bem-vindo(a)!</h1>
            <p class="text-white/60 text-lg">Para finalizar sua inscrição, precisamos te identificar.</p>
        </div>

        <div class="glass-card p-8 lg:p-12">
            <!-- Tabs -->
            <div class="flex gap-4 mb-8">
                <button id="tab-login" class="flex-1 py-4 text-sm font-bold uppercase tracking-wider border-b-2 border-primary text-white">Já tenho cadastro</button>
                <button id="tab-register" class="flex-1 py-4 text-sm font-bold uppercase tracking-wider border-b-2 border-transparent text-white/40 hover:text-white/60">Criar nova conta</button>
            </div>

            <!-- Login Form -->
            <form id="form-login" class="space-y-6">
                <div class="space-y-2">
                    <label class="text-xs font-bold uppercase text-white/40 tracking-widest pl-1">E-mail ou Usuário</label>
                    <input type="text" name="username" required class="input-premium w-full p-4" placeholder="seu@email.com">
                </div>
                <div class="space-y-2 relative">
                    <div class="flex justify-between">
                        <label class="text-xs font-bold uppercase text-white/40 tracking-widest pl-1">Sua Senha</label>
                        <a href="<?php echo esc_url( wp_lostpassword_url() ); ?>" class="text-[10px] uppercase text-primary font-bold hover:underline">Esqueci a senha</a>
                    </div>
                    <div class="relative">
                        <input type="password" name="password" required class="input-premium w-full p-4 pr-12" id="password-login">
                        <button type="button" class="absolute right-4 top-1/2 -translate-y-1/2 text-white/40 hover:text-white js-toggle-pwd" data-target="#password-login">
                            <span class="material-symbols-outlined">visibility</span>
                        </button>
                    </div>
                </div>
                <button type="submit" class="btn-primary w-full p-5 flex items-center justify-center gap-3">
                    Continuar para o Pagamento
                    <span class="material-symbols-outlined">chevron_right</span>
                </button>
                <div id="login-error" class="text-red-400 text-sm text-center font-medium hidden"></div>
            </form>

            <!-- Register Form -->
            <form id="form-register" class="space-y-6 hidden">
                <!-- Person Type Toggle -->
                <div class="flex bg-white/5 p-1 rounded-xl type-toggle">
                    <button type="button" class="flex-1 py-2 rounded-lg text-xs font-bold uppercase tracking-widest active js-type-toggle" data-type="1">Pessoa Física</button>
                    <button type="button" class="flex-1 py-2 rounded-lg text-xs font-bold uppercase tracking-widest js-type-toggle" data-type="2">Pessoa Jurídica</button>
                    <input type="hidden" name="persontype" value="1" id="input-persontype">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2 col-span-1 md:col-span-2 js-field-pf">
                        <label class="text-xs font-bold uppercase text-white/40 tracking-widest pl-1">Nome Completo</label>
                        <input type="text" name="first_name" class="input-premium w-full p-4" placeholder="Ex: João Silva">
                    </div>
                    <div class="space-y-2 col-span-1 md:col-span-2 js-field-pj hidden">
                        <label class="text-xs font-bold uppercase text-white/40 tracking-widest pl-1">Razão Social</label>
                        <input type="text" name="company" class="input-premium w-full p-4" placeholder="Ex: Empresa de Consultoria LTDA">
                    </div>

                    <div class="space-y-2 js-field-pf">
                        <label class="text-xs font-bold uppercase text-white/40 tracking-widest pl-1 pl-1">CPF</label>
                        <input type="text" name="cpf" class="input-premium w-full p-4 js-check-doc" data-type="cpf" placeholder="000.000.000-00">
                    </div>
                    <div class="space-y-2 js-field-pj hidden">
                        <label class="text-xs font-bold uppercase text-white/40 tracking-widest pl-1">CNPJ</label>
                        <input type="text" name="cnpj" class="input-premium w-full p-4 js-check-doc" data-type="cnpj" placeholder="00.000.000/0000-00">
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-bold uppercase text-white/40 tracking-widest pl-1">WhatsApp</label>
                        <input type="text" name="phone" required class="input-premium w-full p-4" placeholder="(00) 00000-0000">
                    </div>

                    <div class="space-y-2 col-span-1 md:col-span-2">
                        <label class="text-xs font-bold uppercase text-white/40 tracking-widest pl-1">E-mail</label>
                        <input type="email" name="email" required class="input-premium w-full p-4" placeholder="seuemail@exemplo.com">
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-bold uppercase text-white/40 tracking-widest pl-1 pl-1">CEP</label>
                        <input type="text" name="postcode" id="reg_postcode" required class="input-premium w-full p-4" placeholder="00000-000">
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-bold uppercase text-white/40 tracking-widest pl-1">Número</label>
                        <input type="text" name="address_number" id="reg_number" required class="input-premium w-full p-4" placeholder="123">
                    </div>
                    
                    <div class="space-y-2 col-span-1 md:col-span-2">
                        <label class="text-xs font-bold uppercase text-white/40 tracking-widest pl-1">Complemento (Opcional)</label>
                        <input type="text" name="address_complement" class="input-premium w-full p-4" placeholder="Ex: Apto 42, Bloco B">
                    </div>

                    <!-- Hidden Address Fields for Auto-filling -->
                    <input type="hidden" name="neighborhood" id="reg_neighborhood">
                    <input type="hidden" name="city" id="reg_city">
                    <input type="hidden" name="state" id="reg_state">
                    <input type="hidden" name="address_1" id="reg_address">

                    <div class="space-y-2 col-span-1 md:col-span-2">
                        <label class="text-xs font-bold uppercase text-white/40 tracking-widest pl-1">Crie sua Senha</label>
                        <div class="relative">
                            <input type="password" name="password" required class="input-premium w-full p-4 pr-12" id="password-register">
                            <button type="button" class="absolute right-4 top-1/2 -translate-y-1/2 text-white/40 hover:text-white js-toggle-pwd" data-target="#password-register">
                                <span class="material-symbols-outlined">visibility</span>
                            </button>
                        </div>
                        <div class="h-1 bg-white/5 rounded-full mt-2 overflow-hidden">
                            <div id="pwd-strength" class="h-full w-0 transition-all duration-500"></div>
                        </div>
                    </div>

                    <!-- Simple Math Captcha -->
                    <div class="space-y-2 col-span-1 md:col-span-2 p-4 bg-white/5 rounded-2xl flex items-center justify-between border border-white/5 mt-4">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-primary">verified_user</span>
                            <span class="text-sm font-medium">Controle de Segurança: <br><strong class="text-lg"><?php echo $math_captcha_question; ?> = ?</strong></span>
                        </div>
                        <input type="number" name="captcha_answer" required class="input-premium w-24 p-3 text-center text-xl" placeholder="??">
                    </div>
                </div>

                <div id="register-error" class="text-red-400 text-sm font-medium hidden text-center border-l-2 border-red-500 p-3 bg-red-500/10"></div>

                <button type="submit" class="btn-primary w-full p-5 mt-6 flex items-center justify-center gap-3">
                    Finalizar Cadastro e Comprar
                    <span class="material-symbols-outlined">shopping_cart_checkout</span>
                </button>
            </form>
        </div>

        <div class="text-center mt-8 text-white/30 text-[10px] uppercase font-bold tracking-widest flex items-center justify-center gap-2">
            <span class="material-symbols-outlined text-sm">lock</span>
            Ambiente 100% Criptografado e Seguro
        </div>
    </div>

    <?php wp_footer(); ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <script>
        jQuery(document).ready(function($) {
            // Masks
            $('input[name="cpf"]').mask('000.000.000-00');
            $('input[name="cnpj"]').mask('00.000.000/0000-00');
            $('input[name="phone"]').mask('(00) 00000-0000');
            $('#reg_postcode').mask('00000-000');

            // Tabs toggle
            $('#tab-login').click(function() {
                $(this).addClass('border-primary text-white').removeClass('border-transparent text-white/40');
                $('#tab-register').addClass('border-transparent text-white/40').removeClass('border-primary text-white');
                $('#form-login').show(); $('#form-register').hide();
            });
            $('#tab-register').click(function() {
                $(this).addClass('border-primary text-white').removeClass('border-transparent text-white/40');
                $('#tab-login').addClass('border-transparent text-white/40').removeClass('border-primary text-white');
                $('#form-register').show(); $('#form-login').hide();
            });

            // Person type toggle
            $('.js-type-toggle').click(function() {
                $('.js-type-toggle').removeClass('active');
                $(this).addClass('active');
                var type = $(this).data('type');
                $('#input-persontype').val(type);
                if (type == 1) {
                    $('.js-field-pf').show(); $('.js-field-pj').hide();
                } else {
                    $('.js-field-pf').hide(); $('.js-field-pj').show();
                }
            });

            // Pwd toggle
            $('.js-toggle-pwd').click(function() {
                var target = $(this).data('target');
                var type = $(target).attr('type') === 'password' ? 'text' : 'password';
                $(target).attr('type', type);
                $(this).find('span').text(type === 'password' ? 'visibility' : 'visibility_off');
            });

            // ViaCEP
            $('#reg_postcode').on('keyup blur', function() {
                var cep = $(this).val().replace(/\D/g, '');
                if (cep.length === 8) {
                    $.getJSON('https://viacep.com.br/ws/' + cep + '/json/?callback=?', function(data) {
                        if (!("erro" in data)) {
                            $('#reg_neighborhood').val(data.bairro);
                            $('#reg_city').val(data.localidade);
                            $('#reg_state').val(data.uf);
                            $('#reg_address').val(data.logradouro);
                            $('#reg_number').focus();
                        }
                    });
                }
            });

            // Check Document Uniqueness
            $('.js-check-doc').on('blur', function() {
                var $input = $(this);
                var doc = $input.val();
                var type = $input.data('type');
                if (!doc) return;
                
                $.post('<?php echo admin_url('admin-ajax.php'); ?>', {
                    action: 'sc_check_document',
                    document: doc,
                    type: type
                }, function(res) {
                    if (!res.success) {
                        alert(res.data);
                        $input.val('').focus();
                    }
                });
            });

            // Forms Submit
            $('#form-login').on('submit', function(e) {
                e.preventDefault();
                var btn = $(this).find('button[type="submit"]');
                btn.prop('disabled', true).html('<div class="loader"></div> Validando...');
                
                $.post('<?php echo admin_url('admin-ajax.php'); ?>', $(this).serialize() + '&action=sc_quick_login&security=<?php echo $nonce; ?>', function(res) {
                    if (res.success) {
                        window.location.href = res.data.redirect;
                    } else {
                        $('#login-error').text(res.data).fadeIn();
                        btn.prop('disabled', false).html('Continuar para o Pagamento <span class="material-symbols-outlined">chevron_right</span>');
                    }
                });
            });

            $('#form-register').on('submit', function(e) {
                e.preventDefault();
                var btn = $(this).find('button[type="submit"]');
                btn.prop('disabled', true).html('<div class="loader"></div> Criando conta...');
                
                $.post('<?php echo admin_url('admin-ajax.php'); ?>', $(this).serialize() + '&action=sc_quick_register&security=<?php echo $nonce; ?>', function(res) {
                    if (res.success) {
                        window.location.href = res.data.redirect;
                    } else {
                        $('#register-error').text(res.data).fadeIn();
                        btn.prop('disabled', false).html('Finalizar Cadastro e Comprar <span class="material-symbols-outlined">shopping_cart_checkout</span>');
                    }
                });
            });
        });
    </script>
</body>
</html>
