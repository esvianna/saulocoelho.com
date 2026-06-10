<?php
$post_id = get_the_ID();
$about_eyebrow = get_post_meta($post_id, 'about_eyebrow', true) ?: 'Autoridade & Excelência';
$about_title_1 = get_post_meta($post_id, 'about_title_1', true) ?: 'Quem é';
$about_title_2 = get_post_meta($post_id, 'about_title_2', true) ?: 'Saulo Coelho?';
$about_desc = get_post_meta($post_id, 'about_description', true) ?: 'Com décadas de experiência, Saulo Coelho transformou o cenário profissional através de liderança estratégica e uma visão institucional sólida e inovadora.';

$stat_1_n = get_post_meta($post_id, 'about_stat_1_number', true) ?: '25+';
$stat_1_l = get_post_meta($post_id, 'about_stat_1_label', true) ?: 'Anos de Carreira';
$stat_2_n = get_post_meta($post_id, 'about_stat_2_number', true) ?: '500+';
$stat_2_l = get_post_meta($post_id, 'about_stat_2_label', true) ?: 'Projetos Liderados';
$about_image = get_post_meta($post_id, 'about_image', true);
?>

<!-- Hero Section -->
<section class="relative py-20 lg:py-32 overflow-hidden">
    <div class="max-w-7xl mx-auto px-6 lg:px-12 grid lg:grid-cols-2 gap-16 items-center">
        <div class="z-10">
            <span class="inline-block px-4 py-1.5 mb-6 text-xs font-bold tracking-widest uppercase text-primary bg-primary/10 rounded-full"><?php echo esc_html($about_eyebrow); ?></span>
            <h1 class="text-4xl md:text-6xl font-black leading-[1.1] text-white mb-8">
                <?php echo esc_html($about_title_1); ?><br/>
                <span class="text-primary"><?php echo esc_html($about_title_2); ?></span>
            </h1>
            <div class="prose prose-invert text-lg lg:text-xl text-slate-400 font-light leading-relaxed mb-10 max-w-xl">
                <?php echo wp_kses_post($about_desc); ?>
            </div>
            <div class="flex items-center gap-10">
                <div class="flex flex-col gap-1">
                    <span class="text-4xl lg:text-5xl font-black text-white"><?php echo esc_html($stat_1_n); ?></span>
                    <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400"><?php echo esc_html($stat_1_l); ?></span>
                </div>
                <div class="w-px h-12 bg-white/10"></div>
                <div class="flex flex-col gap-1">
                    <span class="text-4xl lg:text-5xl font-black text-white"><?php echo esc_html($stat_2_n); ?></span>
                    <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400"><?php echo esc_html($stat_2_l); ?></span>
                </div>
            </div>
        </div>
        <div class="relative">
            <div class="aspect-[4/5] rounded-3xl overflow-hidden shadow-2xl bg-slate-900/50 flex items-center justify-center border border-white/5 relative group">
                <?php if ($about_image) : ?>
                    <img src="<?php echo esc_url($about_image); ?>" alt="<?php the_title(); ?>" class="w-full h-full object-cover">
                <?php else : ?>
                    <span class="material-symbols-outlined text-9xl text-slate-800 group-hover:scale-110 transition-transform duration-500">person</span>
                <?php endif; ?>
                <div class="absolute inset-0 bg-gradient-to-t from-background-dark-alt/60 to-transparent"></div>
            </div>
        </div>
    </div>
</section>

<!-- Institutional History -->
<section class="py-24 bg-background-dark/30">
    <div class="max-w-4xl mx-auto px-6 lg:px-12">
        <?php
        $ms_title = get_post_meta($post_id, 'about_milestones_title', true) ?: 'Trajetória Institucional';
        $milestones = get_post_meta($post_id, 'about_milestones', true);
        if (empty($milestones) && !metadata_exists('post', $post_id, 'about_milestones')) {
            $milestones = [];
            for ($i = 1; $i <= 3; $i++) {
                $year = get_post_meta($post_id, "about_milestone_{$i}_year", true);
                if ($year) {
                    $icons = ['history_edu', 'trending_up', 'verified'];
                    $milestones[] = [
                        'year' => $year,
                        'title' => get_post_meta($post_id, "about_milestone_{$i}_title", true),
                        'desc' => get_post_meta($post_id, "about_milestone_{$i}_desc", true),
                        'icon' => $icons[$i-1] ?? 'verified'
                    ];
                }
            }
        }
        if (!is_array($milestones)) $milestones = [];
        ?>
        <div class="text-center mb-20">
            <h2 class="text-3xl lg:text-4xl font-bold mb-4"><?php echo esc_html($ms_title); ?></h2>
            <div class="w-20 h-1 bg-primary mx-auto"></div>
        </div>
        <!-- Vertical Timeline (Mobile) -->
        <div class="md:hidden space-y-12 relative before:absolute before:inset-0 before:ml-5 before:-translate-x-px before:h-full before:w-0.5 before:bg-gradient-to-b before:from-transparent before:via-slate-800 before:to-transparent">
            <?php 
            foreach ($milestones as $ms) : 
                $year = $ms['year'] ?? '';
                if (!$year) continue;
                $title = $ms['title'] ?? '';
                $desc = $ms['desc'] ?? '';
                $icon = $ms['icon'] ?? 'history_edu';
            ?>
            <div class="relative flex items-center justify-between group">
                <div class="flex items-center justify-center w-10 h-10 rounded-full border border-slate-800 bg-background-dark-alt text-primary font-bold shadow-sm shrink-0 z-10 transition-transform group-hover:scale-110">
                    <span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;"><?php echo esc_html($icon); ?></span>
                </div>
                <div class="w-[calc(100%-4rem)] p-6 rounded-xl bg-background-dark/40 border border-white/5 shadow-xl">
                    <time class="font-bold text-primary block mb-1"><?php echo esc_html($year); ?></time>
                    <h4 class="text-lg font-bold mb-2 text-white"><?php echo esc_html($title); ?></h4>
                    <p class="text-slate-400 text-sm leading-relaxed"><?php echo esc_html($desc); ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Horizontal Timeline (Desktop) -->
        <div class="hidden md:block relative py-40 px-10">
            <!-- Central Line - Thicker and Gradient -->
            <div class="absolute top-1/2 left-4 right-4 h-1 bg-gradient-to-r from-transparent via-slate-800 to-transparent -translate-y-1/2 rounded-full overflow-hidden">
                <div class="h-full w-full bg-slate-700/50"></div>
            </div>
            
            <div class="flex justify-between items-center relative z-10 gap-2">
                <?php 
                foreach ($milestones as $index => $ms) : 
                    $year = $ms['year'] ?? '';
                    if (!$year) continue;
                    $title = $ms['title'] ?? '';
                    $desc = $ms['desc'] ?? '';
                    $icon = $ms['icon'] ?? 'history_edu';
                    $is_even = ($index % 2 == 0);
                    // Handle "Hoje em dia..." wrap
                    $display_year = str_replace('...', '…', $year);
                ?>
                <div class="relative flex flex-col items-center group cursor-pointer milestone-trigger" 
                     data-ms-year="<?php echo esc_attr($year); ?>" 
                     data-ms-title="<?php echo esc_attr($title); ?>" 
                     data-ms-desc="<?php echo esc_attr($desc); ?>" 
                     data-ms-icon="<?php echo esc_attr($icon); ?>">
                    
                    <!-- Content Wrapper: Alternates above/below -->
                    <div class="absolute flex flex-col items-center w-48 text-center transition-all duration-300 <?php echo $is_even ? '-top-28 pt-8' : '-bottom-28 pb-8'; ?>">
                        
                        <?php if ($is_even) : ?>
                            <span class="text-xs font-black tracking-widest text-primary/60 mb-2 whitespace-nowrap"><?php echo esc_html($display_year); ?></span>
                            <div class="opacity-0 group-hover:opacity-100 transform translate-y-2 group-hover:translate-y-0 transition-all duration-300 bg-background-dark-alt/80 backdrop-blur border border-primary/20 px-3 py-1.5 rounded-lg shadow-xl">
                                <h5 class="text-[10px] font-bold text-white uppercase tracking-wider line-clamp-1"><?php echo esc_html($title); ?></h5>
                            </div>
                            <div class="w-px h-6 bg-gradient-to-t from-primary/40 to-transparent mt-2"></div>
                        <?php else : ?>
                            <div class="w-px h-6 bg-gradient-to-b from-primary/40 to-transparent mb-2"></div>
                            <div class="opacity-0 group-hover:opacity-100 transform -translate-y-2 group-hover:translate-y-0 transition-all duration-300 bg-background-dark-alt/80 backdrop-blur border border-primary/20 px-3 py-1.5 rounded-lg shadow-xl mb-2">
                                <h5 class="text-[10px] font-bold text-white uppercase tracking-wider line-clamp-1"><?php echo esc_html($title); ?></h5>
                            </div>
                            <span class="text-xs font-black tracking-widest text-primary/60 whitespace-nowrap"><?php echo esc_html($display_year); ?></span>
                        <?php endif; ?>
                    </div>

                    <!-- Milestone Icon Circle -->
                    <div class="relative z-20 w-14 h-14 rounded-full border border-slate-700 bg-background-dark-alt flex items-center justify-center text-slate-400 transition-all duration-500 group-hover:border-primary group-hover:text-primary group-hover:scale-110 group-hover:shadow-[0_0_30px_rgba(197,160,89,0.4)] shadow-lg overflow-hidden group-hover:shadow-primary/20">
                         <!-- Subtle inner glow -->
                        <div class="absolute inset-0 bg-gradient-to-br from-white/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        <span class="material-symbols-outlined text-2xl" style="font-variation-settings: 'FILL' 1;"><?php echo esc_html($icon); ?></span>
                    </div>

                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>


    <!-- Milestone Modal -->
    <div id="milestone-modal" class="fixed inset-0 z-[100] flex items-center justify-center p-6 opacity-0 pointer-events-none transition-all duration-300">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-background-dark/80 backdrop-blur-md modal-close"></div>
        
        <!-- Content Card -->
        <div class="relative w-full max-w-xl bg-background-dark-alt/90 border border-white/10 rounded-[2rem] p-10 shadow-2xl overflow-hidden glass-card transform scale-95 transition-transform duration-300">
            <!-- Glow effect -->
            <div class="absolute -top-24 -right-24 w-48 h-48 bg-primary/20 rounded-full blur-[80px]"></div>
            
            <button class="absolute top-6 right-6 text-slate-400 hover:text-white transition-colors modal-close">
                <span class="material-symbols-outlined">close</span>
            </button>

            <div class="flex flex-col items-center text-center space-y-6">
                <div id="modal-icon-wrapper" class="w-20 h-20 rounded-2xl bg-primary/10 border border-primary/20 flex items-center justify-center text-primary group">
                    <span id="modal-icon" class="material-symbols-outlined text-4xl" style="font-variation-settings: 'FILL' 1;"></span>
                </div>
                
                <div>
                    <span id="modal-year" class="inline-block px-3 py-1 rounded-full bg-primary/10 text-primary text-xs font-bold tracking-widest uppercase mb-4"></span>
                    <h3 id="modal-title" class="text-3xl font-black text-white leading-tight"></h3>
                </div>

                <div class="w-12 h-1 bg-gradient-to-r from-transparent via-primary to-transparent"></div>

                <p id="modal-desc" class="text-slate-400 text-lg leading-relaxed font-light"></p>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('milestone-modal');
        const modalContent = modal.querySelector('.transform');
        const triggers = document.querySelectorAll('.milestone-trigger');
        const closeButtons = document.querySelectorAll('.modal-close');

        const modalYear = document.getElementById('modal-year');
        const modalTitle = document.getElementById('modal-title');
        const modalDesc = document.getElementById('modal-desc');
        const modalIcon = document.getElementById('modal-icon');

        function openModal(data) {
            modalYear.textContent = data.year;
            modalTitle.textContent = data.title;
            modalDesc.textContent = data.desc;
            modalIcon.textContent = data.icon;

            modal.classList.remove('opacity-0', 'pointer-events-none');
            modalContent.classList.remove('scale-95');
            modalContent.classList.add('scale-100');
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            modal.classList.add('opacity-0', 'pointer-events-none', 'flex');
            modalContent.classList.remove('scale-100');
            modalContent.classList.add('scale-95');
            document.body.style.overflow = '';
        }

        triggers.forEach(trigger => {
            trigger.addEventListener('click', () => {
                const data = {
                    year: trigger.getAttribute('data-ms-year'),
                    title: trigger.getAttribute('data-ms-title'),
                    desc: trigger.getAttribute('data-ms-desc'),
                    icon: trigger.getAttribute('data-ms-icon')
                };
                openModal(data);
            });
        });

        closeButtons.forEach(btn => {
            btn.addEventListener('click', closeModal);
        });

        modal.addEventListener('click', (e) => {
            if (e.target.classList.contains('modal-close')) closeModal();
        });

        // Close on Esc
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeModal();
        });
    });
    </script>
</section>

<?php
// Press Section Logic
$press_cat = get_post_meta($post_id, 'about_press_category', true);
$press_subtitle = get_post_meta($post_id, 'about_press_subtitle', true) ?: 'Recortes & Entrevistas';
$press_title = get_post_meta($post_id, 'about_press_title', true) ?: 'Saulo na Mídia';
$press_limit = get_post_meta($post_id, 'about_press_limit', true) ?: 3;

if ($press_cat) :
    $press_query = new WP_Query([
        'post_type' => 'post',
        'posts_per_page' => $press_limit,
        'cat' => $press_cat,
        'post_status' => 'publish'
    ]);
    
    if ($press_query->have_posts()) :
?>
<!-- Press Section -->
<section class="py-32 bg-background-dark-alt/20 relative overflow-hidden">
    <div class="absolute inset-0 bg-primary/5 blur-[120px] rounded-full -bottom-1/2 -right-1/4 pointer-events-none"></div>
    <div class="max-w-7xl mx-auto px-6 lg:px-12 relative z-10">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-16 gap-8">
            <div class="max-w-2xl">
                <span class="text-xs font-bold tracking-[0.3em] uppercase text-primary mb-4 block"><?php echo esc_html($press_subtitle); ?></span>
                <h2 class="text-4xl lg:text-5xl font-black text-white uppercase leading-none"><?php echo esc_html($press_title); ?></h2>
                <div class="h-1 w-20 bg-primary mt-8"></div>
            </div>
            <a href="<?php echo get_category_link($press_cat); ?>" class="group flex items-center gap-3 text-slate-400 hover:text-white transition-colors">
                <span class="text-xs font-bold uppercase tracking-widest">Ver Todas as Matérias</span>
                <span class="material-symbols-outlined text-sm transition-transform group-hover:translate-x-1">east</span>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12 lg:gap-16">
            <?php 
            while ($press_query->have_posts()) : $press_query->the_post();
                get_template_part('template-parts/content-press-item');
            endwhile;
            wp_reset_postdata();
            ?>
        </div>
    </div>
</section>
<?php 
    endif;
endif; 
?>

<!-- Values/Authority Grid -->
<section class="py-24">
    <div class="max-w-7xl mx-auto px-6 lg:px-12">
        <div class="grid md:grid-cols-3 gap-12">
            <?php 
            for ($i = 1; $i <= 3; $i++) : 
                $icon = get_post_meta($post_id, "about_value_{$i}_icon", true);
                if (!$icon) continue;
                $title = get_post_meta($post_id, "about_value_{$i}_title", true);
                $desc = get_post_meta($post_id, "about_value_{$i}_desc", true);
            ?>
            <div class="space-y-4 p-8 rounded-2xl bg-background-dark/20 border border-white/[0.03] hover:border-primary/20 transition-all group">
                <span class="material-symbols-outlined text-primary text-4xl group-hover:scale-110 transition-transform duration-300"><?php echo esc_html($icon); ?></span>
                <h3 class="text-xl font-bold text-white"><?php echo esc_html($title); ?></h3>
                <p class="text-slate-400 leading-relaxed"><?php echo esc_html($desc); ?></p>
            </div>
            <?php endfor; ?>
        </div>
    </div>
</section>
