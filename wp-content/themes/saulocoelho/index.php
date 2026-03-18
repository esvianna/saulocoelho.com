<?php
get_header(); ?>

<main class="bg-background-dark-alt">
    <!-- Hero Section -->
    <section class="relative min-h-screen flex items-center pt-20 overflow-hidden">
        <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div class="order-2 lg:order-1 space-y-8 z-10">
                <div class="space-y-4">
                    <span class="text-primary font-bold tracking-[0.3em] uppercase text-sm block">Alta Performance & Liderança</span>
                    <h1 class="text-5xl md:text-7xl font-black leading-[1.1] tracking-tight text-slate-100">
                        Desperte o seu <br/> <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary to-blue-400">potencial máximo</span>
                    </h1>
                    <p class="text-lg text-slate-400 max-w-lg leading-relaxed font-light">
                        Transforme sua trajetória com as metodologias aplicadas por Saulo Coelho nos maiores ecossistemas corporativos do Brasil.
                    </p>
                </div>
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="#" class="bg-primary text-white font-bold uppercase tracking-widest px-8 py-5 rounded-lg text-sm hover:scale-[1.02] active:scale-[0.98] transition-all text-center">
                        Conheça os Programas
                    </a>
                    <a href="#" class="border border-white/20 hover:bg-white/5 text-white font-bold uppercase tracking-widest px-8 py-5 rounded-lg text-sm transition-all text-center">
                        Masterclass
                    </a>
                </div>
            </div>
            <div class="order-1 lg:order-2 relative z-10">
                <div class="absolute -inset-10 bg-primary/20 blur-[120px] rounded-full"></div>
                <div class="relative aspect-[4/5] overflow-hidden rounded-2xl shadow-2xl hover:grayscale-0 transition-all duration-700">
                    <img alt="Saulo Coelho" class="w-full h-full object-cover" src="https://sccr.com.br/wp-content/uploads/2025/05/WhatsApp-Image-2025-05-07-at-12.43.04-1.jpeg" />
                </div>
            </div>
        </div>
    </section>

    <!-- Trusted By Section -->
    <section class="py-24 border-y border-white/5 bg-slate-900/50">
        <div class="max-w-7xl mx-auto px-6">
            <h4 class="text-slate-500 text-xs font-bold uppercase tracking-[0.4em] text-center mb-16">Empresas que confiam no nosso trabalho</h4>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-12 items-center opacity-40 grayscale hover:grayscale-0 transition-all duration-500">
                <!-- Logos placeholders from mockup -->
                <div class="flex justify-center h-12 text-white font-bold text-2xl">TECHCORP</div>
                <div class="flex justify-center h-12 text-white font-bold text-2xl">GLOBALINC</div>
                <div class="flex justify-center h-12 text-white font-bold text-2xl">INNOVATE</div>
                <div class="flex justify-center h-12 text-white font-bold text-2xl">NEXUS</div>
                <div class="flex justify-center h-12 text-white font-bold text-2xl">SYNERGY</div>
            </div>
        </div>
    </section>

    <!-- Features/Services Section -->
    <section class="py-32 relative">
        <div class="max-w-7xl mx-auto px-6">
            <div class="mb-20 space-y-4">
                <h2 class="text-4xl md:text-5xl font-black tracking-tight text-white uppercase">Autoridade e Experiência</h2>
                <div class="h-1 w-20 bg-primary"></div>
                <p class="text-slate-400 text-lg max-w-2xl font-light">Impactando resultados através de metodologias testadas e aprovadas por líderes de grandes corporações.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="glass-card p-10 rounded-xl space-y-6 hover:border-primary/50 transition-colors group">
                    <div class="w-16 h-16 rounded-lg bg-primary/10 flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-white transition-all duration-500">
                        <span class="material-symbols-outlined text-3xl">present_to_all</span>
                    </div>
                    <div class="space-y-3">
                        <h3 class="text-xl font-bold text-white uppercase tracking-wider">Palestras</h3>
                        <p class="text-slate-400 text-sm leading-relaxed">Conteúdo disruptivo, inspiração e estratégia prática para grandes convenções e públicos corporativos.</p>
                    </div>
                </div>
                <!-- ... other features ... -->
            </div>
        </div>
    </section>
</main>

<?php
get_footer();
