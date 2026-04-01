<?php
/**
 * Template Part: Student Review Form
 */

if (!is_user_logged_in()) return;

$pid = get_the_ID();
$enable_reviews = get_post_meta($pid, 'course_enable_reviews', true) === 'yes';
if (!$enable_reviews) return;

$current_user = wp_get_current_user();
$status = isset($_GET['review_status']) ? $_GET['review_status'] : '';
?>

<section class="py-24 bg-slate-900/40 border-t border-white/5" id="avaliar">
    <div class="max-w-3xl mx-auto px-6">
        <div class="bg-background-dark-alt border border-white/10 rounded-[2.5rem] p-8 md:p-16 shadow-2xl relative overflow-hidden glass-card">
            <!-- Glow -->
            <div class="absolute -top-24 -right-24 w-48 h-48 bg-primary/10 blur-[100px] rounded-full pointer-events-none"></div>

            <div class="text-center mb-12">
                <span class="text-xs font-bold tracking-[0.3em] uppercase text-primary mb-4 block">Sua Opinião</span>
                <h2 class="text-3xl md:text-5xl font-black text-white leading-tight">Avalie este Treinamento</h2>
                <p class="text-slate-400 mt-4 font-light">Sua avaliação ajuda outros alunos e nos ajuda a evoluir constantemente.</p>
            </div>

            <?php if ($status === 'submitted') : ?>
                <div class="bg-green-500/10 border border-green-500/20 text-green-400 p-6 rounded-2xl text-center flex flex-col items-center gap-4">
                    <span class="material-symbols-outlined text-4xl">verified</span>
                    <div>
                        <h4 class="font-bold text-lg">Depoimento Enviado com Sucesso!</h4>
                        <p class="text-sm opacity-80">Sua avaliação foi enviada para moderação e aparecerá no site em breve.</p>
                    </div>
                </div>
            <?php else : ?>
                <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post" class="space-y-8">
                    <?php wp_nonce_field('submit_review', 'review_nonce'); ?>
                    <input type="hidden" name="action" value="submit_student_review">
                    <input type="hidden" name="review_product_id" value="<?php echo esc_attr($pid); ?>">
                    
                    <!-- Star Rating -->
                    <div class="flex flex-col items-center gap-4 bg-white/[0.02] border border-white/5 p-8 rounded-3xl">
                        <label class="text-xs font-bold uppercase tracking-widest text-slate-500">Sua Nota</label>
                        <div class="flex flex-row-reverse justify-center gap-2">
                             <?php for ($i=5; $i>=1; $i--) : ?>
                                <input type="radio" id="star-<?php echo $i; ?>" name="review_rating" value="<?php echo $i; ?>" class="hidden peer" <?php checked($i, 5); ?> required />
                                <label for="star-<?php echo $i; ?>" class="material-symbols-outlined text-4xl cursor-pointer text-slate-700 hover:text-primary peer-hover:text-primary peer-checked:text-primary transition-colors" style="font-variation-settings: 'FILL' 1;">star</label>
                             <?php endfor; ?>
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-[10px] font-bold uppercase tracking-widest text-slate-500 ml-4">Nome para Exibição</label>
                            <input type="text" name="review_name" value="<?php echo esc_attr($current_user->display_name); ?>" class="w-full bg-white/[0.03] border border-white/10 rounded-2xl px-6 py-4 text-white focus:border-primary focus:ring-0 transition-all outline-none" required>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-bold uppercase tracking-widest text-slate-500 ml-4">Link de Vídeo (Opcional - YouTube/Vimeo)</label>
                            <input type="url" name="review_video" placeholder="https://..." class="w-full bg-white/[0.03] border border-white/10 rounded-2xl px-6 py-4 text-white focus:border-primary focus:ring-0 transition-all outline-none">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-bold uppercase tracking-widest text-slate-500 ml-4">Seu Depoimento</label>
                        <textarea name="review_text" rows="5" class="w-full bg-white/[0.03] border border-white/10 rounded-2xl px-6 py-4 text-white focus:border-primary focus:ring-0 transition-all outline-none resize-none" placeholder="Conte como foi sua experiência com o treinamento..." required></textarea>
                    </div>

                    <button type="submit" class="w-full bg-primary hover:bg-primary/90 text-white py-6 rounded-2xl text-xs font-black uppercase tracking-[0.3em] shadow-xl shadow-primary/20 transition-all hover:-translate-y-1">
                        Enviar Minha Avaliação
                    </button>
                    
                    <p class="text-center text-[10px] text-slate-600 uppercase tracking-widest">
                        Ao enviar, você autoriza o uso do seu depoimento neste site.
                    </p>
                </form>
            <?php endif; ?>
        </div>
    </div>
</section>

<style>
/* Estilo customizado para o Star Rating pois Tailwind peer-hover reverso precisa de CSS puro ou lógica complexa */
.peer-hover\:text-primary ~ label { color: #137fec !important; }
.peer-checked\:text-primary ~ label { color: #137fec !important; }
</style>
