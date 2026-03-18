<footer class="py-20 bg-background-dark-alt border-t border-white/5">
    <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-4 gap-12">
        <div class="col-span-1 md:col-span-2 space-y-6">
            <div class="flex items-center gap-3">
                <div class="text-primary">
                    <span class="material-symbols-outlined text-2xl">terminal</span>
                </div>
                <h2 class="text-lg font-bold tracking-tighter uppercase text-white"><?php bloginfo( 'name' ); ?></h2>
            </div>
            <p class="text-slate-500 max-w-xs text-sm"><?php bloginfo( 'description' ); ?></p>
            <div class="flex gap-4">
                <a href="#" class="w-10 h-10 rounded-full border border-white/10 flex items-center justify-center text-slate-400 hover:text-primary hover:border-primary transition-all">
                    <span class="material-symbols-outlined text-sm">share</span>
                </a>
                <a href="#" class="w-10 h-10 rounded-full border border-white/10 flex items-center justify-center text-slate-400 hover:text-primary hover:border-primary transition-all">
                    <span class="material-symbols-outlined text-sm">mail</span>
                </a>
            </div>
        </div>
        <div class="space-y-6">
            <h4 class="text-white text-xs font-bold uppercase tracking-widest">Links</h4>
            <ul class="space-y-4 text-sm text-slate-400">
                <li><a href="#" class="hover:text-primary transition-colors">Programas</a></li>
                <li><a href="#" class="hover:text-primary transition-colors">Sobre</a></li>
                <li><a href="#" class="hover:text-primary transition-colors">Loja</a></li>
            </ul>
        </div>
        <div class="space-y-6">
            <h4 class="text-white text-xs font-bold uppercase tracking-widest">Contato</h4>
            <ul class="space-y-4 text-sm text-slate-400">
                <li class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-primary text-lg">call</span>
                    +55 (11) 99999-9999
                </li>
                <li class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-primary text-lg">location_on</span>
                    São Paulo, SP
                </li>
            </ul>
        </div>
    </div>
    <div class="max-w-7xl mx-auto px-6 pt-20 mt-20 border-t border-white/5 flex flex-col md:flex-row justify-between items-center gap-4 text-xs text-slate-600 uppercase tracking-widest">
        <p>© <?php echo date('Y'); ?> <?php bloginfo( 'name' ); ?>. Todos os direitos reservados.</p>
        <div class="flex gap-8">
            <a href="#" class="hover:text-slate-400">Privacidade</a>
            <a href="#" class="hover:text-slate-400">Termos</a>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
