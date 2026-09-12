<?php
/**
 * Landing de captura — conteúdo vem da palestra (CPT).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$event = sc_palestra_current_event();
if ( ! $event ) {
	return;
}

$config      = sc_palestra_event_config( $event->ID );
$privacy_url = sc_palestra_privacy_url();
$bg_style    = '';
if ( ! empty( $config['bg_url'] ) ) {
	$bg_style = 'background-image:linear-gradient(180deg,rgba(5,10,20,.72),rgba(10,14,26,.92)),url(' . esc_url( $config['bg_url'] ) . ');background-size:cover;background-position:center;';
}

$meta_bits = array();
if ( $config['date'] ) {
	$meta_bits[] = mysql2date( 'd/m/Y', $config['date'] . ' 00:00:00' );
}
if ( $config['location'] ) {
	$meta_bits[] = $config['location'];
}
?>

<section class="relative pt-36 pb-24 overflow-hidden border-b border-white/5" <?php echo $bg_style ? 'style="' . esc_attr( $bg_style ) . '"' : ''; ?>>
	<?php if ( ! $config['bg_url'] ) : ?>
		<div class="absolute -top-32 -left-32 w-[520px] h-[520px] bg-primary/10 blur-[140px] rounded-full pointer-events-none opacity-50"></div>
		<div class="absolute -bottom-24 -right-24 w-[420px] h-[420px] bg-primary/5 blur-[120px] rounded-full pointer-events-none opacity-40"></div>
	<?php endif; ?>
	<div class="relative z-10 max-w-xl mx-auto px-6 text-center">
		<?php if ( $config['eyebrow'] !== '' ) : ?>
			<span class="text-primary font-bold tracking-[0.35em] uppercase text-[10px] mb-6 block"><?php echo esc_html( $config['eyebrow'] ); ?></span>
		<?php endif; ?>
		<h1 class="font-display text-4xl md:text-6xl font-black leading-[1.05] text-white mb-6">
			<?php echo esc_html( $config['title'] ); ?>
		</h1>
		<?php if ( $meta_bits ) : ?>
			<p class="text-primary/90 text-sm font-semibold tracking-wide uppercase mb-4"><?php echo esc_html( implode( ' · ', $meta_bits ) ); ?></p>
		<?php endif; ?>
		<p class="text-slate-300 text-lg font-light leading-relaxed">
			<?php echo esc_html( $config['support'] ); ?>
		</p>
	</div>
</section>

<section class="relative z-10 max-w-xl mx-auto px-6 -mt-8 pb-32">
	<div class="p-8 md:p-12 rounded-[2rem] bg-background-dark border border-white/5 shadow-2xl">
		<form id="sc-palestra-form" class="space-y-6" novalidate>
			<input type="hidden" name="event_id" value="<?php echo (int) $event->ID; ?>" />
			<div class="hidden" aria-hidden="true">
				<label for="sc-palestra-website"><?php esc_html_e( 'Website', 'saulocoelho' ); ?></label>
				<input type="text" name="website" id="sc-palestra-website" tabindex="-1" autocomplete="off" />
			</div>

			<div>
				<label for="sc-palestra-name" class="block text-[11px] font-bold uppercase tracking-widest text-slate-400 mb-2">Nome</label>
				<input type="text" name="name" id="sc-palestra-name" required autocomplete="name" class="w-full h-12 px-4 rounded-xl bg-white/5 border border-white/10 text-white focus:border-primary focus:outline-none" />
			</div>

			<div>
				<label for="sc-palestra-email" class="block text-[11px] font-bold uppercase tracking-widest text-slate-400 mb-2">E-mail</label>
				<input type="email" name="email" id="sc-palestra-email" required autocomplete="email" class="w-full h-12 px-4 rounded-xl bg-white/5 border border-white/10 text-white focus:border-primary focus:outline-none" />
			</div>

			<?php if ( $config['ask_whatsapp'] === '1' ) : ?>
			<div>
				<label for="sc-palestra-whatsapp" class="block text-[11px] font-bold uppercase tracking-widest text-slate-400 mb-2">WhatsApp</label>
				<input type="tel" name="whatsapp" id="sc-palestra-whatsapp" required autocomplete="tel" inputmode="tel" placeholder="(21) 99999-9999" class="w-full h-12 px-4 rounded-xl bg-white/5 border border-white/10 text-white focus:border-primary focus:outline-none" />
			</div>
			<?php endif; ?>

			<?php foreach ( $config['extras'] as $field ) : ?>
			<div>
				<label for="sc-palestra-extra-<?php echo esc_attr( $field['key'] ); ?>" class="block text-[11px] font-bold uppercase tracking-widest text-slate-400 mb-2"><?php echo esc_html( $field['label'] ); ?></label>
				<input
					type="<?php echo esc_attr( $field['type'] ); ?>"
					name="extra[<?php echo esc_attr( $field['key'] ); ?>]"
					id="sc-palestra-extra-<?php echo esc_attr( $field['key'] ); ?>"
					<?php echo ! empty( $field['required'] ) ? 'required' : ''; ?>
					class="w-full h-12 px-4 rounded-xl bg-white/5 border border-white/10 text-white focus:border-primary focus:outline-none"
				/>
			</div>
			<?php endforeach; ?>

			<label class="flex items-start gap-3 text-sm text-slate-400 font-light leading-relaxed cursor-pointer">
				<input type="checkbox" name="consent" value="1" required class="mt-1 rounded border-white/20 bg-white/5 text-primary focus:ring-primary" />
				<span>
					<?php echo esc_html( $config['consent'] ); ?>
					<a href="<?php echo esc_url( $privacy_url ); ?>" class="text-primary hover:underline" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'política de privacidade', 'saulocoelho' ); ?></a>.
				</span>
			</label>

			<p id="sc-palestra-status" class="text-sm text-slate-400 min-h-[1.25rem]" role="status" aria-live="polite"></p>

			<button type="submit" id="sc-palestra-submit" class="w-full h-14 rounded-xl bg-primary text-background-dark font-black uppercase tracking-widest text-sm hover:bg-primary-light transition-colors disabled:opacity-60">
				<?php echo esc_html( $config['cta'] ); ?>
			</button>
		</form>

		<div id="sc-palestra-thanks" class="hidden text-center space-y-6">
			<p class="text-primary text-[10px] font-bold uppercase tracking-[0.3em]">Pronto</p>
			<h2 class="font-display text-3xl font-black text-white"><?php echo esc_html( $config['thanks_title'] ); ?></h2>
			<p id="sc-palestra-mail-note" class="text-slate-400 font-light leading-relaxed"></p>
			<div id="sc-palestra-downloads" class="space-y-3"></div>
			<p class="text-slate-500 text-sm font-light leading-relaxed">
				<?php echo esc_html( $config['thanks_note'] ); ?>
			</p>
		</div>
	</div>
</section>
