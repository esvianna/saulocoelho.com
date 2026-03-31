<?php
/**
 * Template Name: Página de Contato Premium
 * Description: layout exclusivo para a página de contato com grid estratégico e formulário.
 */

get_header();

// Fetch contact details
$email = get_theme_mod('footer_email', 'contato@saulocoelho.com.br');
$phone = get_theme_mod('footer_phone', '+55 (11) 99999-9999');
$linkedin = get_theme_mod('footer_social_linkedin', '#');
$whatsapp_link = "https://wa.me/" . preg_replace('/[^0-9]/', '', $phone);

// Fetch customizer contact page settings
$hero_title = get_theme_mod('contact_hero_title', 'Prepare-se para o Próximo Nível');
$form_title = get_theme_mod('contact_form_title', 'Envie uma Mensagem');
$form_desc = get_theme_mod('contact_form_desc', 'Preencha os campos abaixo para iniciarmos uma conversa estratégica sobre seus objetivos.');
$form_shortcode = get_theme_mod('contact_form_shortcode', '');

?>

<main class="bg-background-dark min-h-screen text-white pb-32">
    <!-- Hero Section -->
    <section class="contact-hero relative pt-48 pb-32 overflow-hidden border-b border-white/5 bg-background-dark-alt/50">
        <div class="max-w-7xl mx-auto px-6 lg:px-10 relative z-10 text-center">
            <span class="text-primary font-black tracking-[0.4em] uppercase text-[10px] mb-6 block animate-fade-in">Direct Access</span>
            <h1 class="text-5xl md:text-8xl font-black leading-[0.95] tracking-tighter text-white uppercase drop-shadow-2xl">
                <?php echo esc_html($hero_title); ?>
            </h1>
        </div>
        
        <!-- Background Accents -->
        <div class="absolute -top-32 -left-32 w-[600px] h-[600px] bg-primary/10 blur-[150px] rounded-full pointer-events-none opacity-50"></div>
        <div class="absolute -bottom-32 -right-32 w-[600px] h-[600px] bg-primary/5 blur-[150px] rounded-full pointer-events-none opacity-30"></div>
    </section>

    <!-- Content Grid -->
    <section class="max-w-7xl mx-auto px-6 lg:px-10 -mt-16 relative z-20">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Contact Cards -->
            <div class="lg:col-span-1 space-y-8">
                <!-- WhatsApp Card -->
                <a href="<?php echo esc_url($whatsapp_link); ?>" target="_blank" class="group block p-10 rounded-3xl bg-background-dark/40 backdrop-blur-xl border border-white/5 hover:border-primary/50 transition-all duration-500 hover:-translate-y-2">
                    <div class="flex items-start justify-between mb-8">
                        <div class="w-14 h-14 rounded-2xl bg-primary/10 flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-white transition-all duration-500 shadow-lg shadow-primary/20">
                            <span class="material-symbols-outlined text-3xl">chat</span>
                        </div>
                        <span class="text-[10px] font-black uppercase tracking-widest text-white/20 group-hover:text-primary transition-colors">Instantâneo</span>
                    </div>
                    <h3 class="text-2xl font-black text-white uppercase tracking-tight mb-2">WhatsApp</h3>
                    <p class="text-slate-400 font-light leading-relaxed mb-6">Para respostas rápidas e agendamentos imediatos.</p>
                    <div class="flex items-center gap-2 text-primary font-bold text-sm tracking-widest uppercase">
                        <span>Iniciar Conversa</span>
                        <span class="material-symbols-outlined text-sm group-hover:translate-x-2 transition-transform">east</span>
                    </div>
                </a>

                <!-- Email Card -->
                <a href="mailto:<?php echo esc_attr($email); ?>" class="group block p-10 rounded-3xl bg-background-dark/40 backdrop-blur-xl border border-white/5 hover:border-primary/50 transition-all duration-500 hover:-translate-y-2">
                    <div class="flex items-start justify-between mb-8">
                        <div class="w-14 h-14 rounded-2xl bg-white/5 flex items-center justify-center text-white group-hover:bg-primary group-hover:text-white transition-all duration-500 shadow-lg shadow-primary/20 border border-white/5">
                            <span class="material-symbols-outlined text-3xl">mail</span>
                        </div>
                        <span class="text-[10px] font-black uppercase tracking-widest text-white/20 group-hover:text-primary transition-colors">Estratégico</span>
                    </div>
                    <h3 class="text-2xl font-black text-white uppercase tracking-tight mb-2">E-mail</h3>
                    <p class="text-slate-400 font-light leading-relaxed mb-6">Propostas comerciais, parcerias e consultas formais.</p>
                    <div class="text-slate-200 font-bold text-sm truncate"><?php echo esc_html($email); ?></div>
                </a>

                <!-- LinkedIn Card -->
                <a href="<?php echo esc_url($linkedin); ?>" target="_blank" class="group block p-10 rounded-3xl bg-background-dark/40 backdrop-blur-xl border border-white/5 hover:border-primary/50 transition-all duration-500 hover:-translate-y-2">
                    <div class="flex items-start justify-between mb-8">
                        <div class="w-14 h-14 rounded-2xl bg-white/5 flex items-center justify-center text-white group-hover:bg-primary group-hover:text-white transition-all duration-500 shadow-lg shadow-white/10 border border-white/5">
                            <span class="material-symbols-outlined text-3xl">hub</span>
                        </div>
                        <span class="text-[10px] font-black uppercase tracking-widest text-white/20 group-hover:text-primary transition-colors">Professional</span>
                    </div>
                    <h3 class="text-2xl font-black text-white uppercase tracking-tight mb-2">LinkedIn</h3>
                    <p class="text-slate-400 font-light leading-relaxed">Conecte-se profissionalmente e acompanhe artigos exclusivos.</p>
                </a>
            </div>

            <!-- Form Area -->
            <div class="lg:col-span-2">
                <div class="h-full p-10 md:p-20 rounded-[3rem] bg-background-dark-alt border border-white/5 relative overflow-hidden shadow-2xl">
                    <!-- Subtle Interior Glow -->
                    <div class="absolute -top-1/2 -right-1/2 w-full h-full bg-primary/5 blur-[120px] rounded-full pointer-events-none"></div>

                    <div class="relative z-10 max-w-xl">
                        <h2 class="text-4xl md:text-6xl font-black text-white uppercase tracking-tighter mb-6 leading-none">
                            <?php echo esc_html($form_title); ?>
                        </h2>
                        <p class="text-slate-400 text-lg font-light leading-relaxed mb-12">
                            <?php echo esc_html($form_desc); ?>
                        </p>

                        <div class="prose-premium prose-invert max-w-none">
                            <?php 
                            if ($form_shortcode) {
                                echo do_shortcode($form_shortcode);
                            } else {
                                ?>
                                <div class="p-12 rounded-3xl border border-white/5 bg-white/5 text-center">
                                    <span class="material-symbols-outlined text-5xl text-white/10 mb-6 italic">contact_mail</span>
                                    <p class="text-slate-500 italic mb-8">Insira o shortcode do seu formulário no Personalizador do Tema para exibi-lo aqui.</p>
                                    <div class="space-y-4">
                                        <div class="h-12 bg-white/5 rounded-xl border border-white/5 animate-pulse"></div>
                                        <div class="h-12 bg-white/5 rounded-xl border border-white/5 animate-pulse"></div>
                                        <div class="h-32 bg-white/5 rounded-xl border border-white/5 animate-pulse"></div>
                                        <div class="h-14 bg-primary/20 rounded-xl border border-primary/20 animate-pulse"></div>
                                    </div>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- Location Footer (Optional context) -->
    <section class="max-w-7xl mx-auto px-6 py-32 text-center">
        <div class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-white/5 border border-white/10 text-slate-400 text-xs font-bold uppercase tracking-widest">
            <span class="material-symbols-outlined text-primary text-lg">location_on</span>
            <span><?php echo get_theme_mod('footer_location', 'São Paulo, Brasil'); ?></span>
        </div>
    </section>
</main>

<?php
get_footer();
