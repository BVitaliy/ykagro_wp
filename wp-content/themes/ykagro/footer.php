<?php
/**
 * Site footer.
 *
 * Ported from the markup's inc/_footer.php + inc/_bottom.php.
 *
 * The SEO text block is optional per page. The markup toggled it with a
 * $footer_show_seo flag, but a flag set in a template cannot reach here —
 * get_footer() loads this file through load_template(), which does not inherit
 * the caller's local scope. The real condition is simply whether the page's ACF
 * `seo_block` holds any text, so that is what we check.
 *
 * @package ykagro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$yka_seo       = function_exists( 'get_field' ) ? get_field( 'seo_block' ) : null;
$yka_address   = function_exists( 'get_field' ) ? get_field( 'contact_address', 'options' ) : '';
$yka_phone     = function_exists( 'get_field' ) ? get_field( 'contact_phone', 'options' ) : '';
$yka_email     = function_exists( 'get_field' ) ? get_field( 'contact_email', 'options' ) : '';
$yka_socials   = function_exists( 'get_field' ) ? get_field( 'footer_socials', 'options' ) : [];
$yka_copyright = function_exists( 'get_field' ) ? get_field( 'footer_copyright', 'options' ) : '';

if ( empty( $yka_copyright ) ) {
	/* translators: %s: site name. */
	$yka_copyright = sprintf( __( '©%s. Всі права захищено', 'ykagro' ), get_bloginfo( 'name' ) );
}
?>

<footer class="footer">
	<div class="footer__block">

		<?php if ( ! empty( $yka_seo['text'] ) || ! empty( $yka_seo['more'] ) ) { ?>
			<div class="footer__top js-seo-block">
				<div class="footer__seo text-md">
					<?php if ( ! empty( $yka_seo['text'] ) ) { ?>
						<div class="footer__seo-visible">
							<?php echo wp_kses_post( yka_strip_table_attrs( $yka_seo['text'] ) ); ?>
						</div>
					<?php } ?>

					<?php if ( ! empty( $yka_seo['more'] ) ) { ?>
						<div class="seo-block__more">
							<div class="seo-block__more-inner">
								<?php echo wp_kses_post( yka_strip_table_attrs( $yka_seo['more'] ) ); ?>
							</div>
						</div>
					<?php } ?>
				</div>

				<?php if ( ! empty( $yka_seo['more'] ) ) { ?>
					<button class="link-more link-more--light footer__more js-seo-toggle" type="button" aria-expanded="false">
						<span class="seo-block__more-label"><?php esc_html_e( 'читати більше', 'ykagro' ); ?></span>
						<span class="seo-block__less-label"><?php esc_html_e( 'читати менше', 'ykagro' ); ?></span>
						<span class="link-more__icon"><?php yka_icon( 'icons/arrow-diagonal.svg' ); ?></span>
					</button>
				<?php } ?>
			</div>
		<?php } ?>

		<div class="footer__grid">
			<?php
			foreach ( [ 'footer_menu_1', 'footer_menu_2' ] as $yka_footer_location ) {
				if ( ! has_nav_menu( $yka_footer_location ) ) {
					continue;
				}

				wp_nav_menu(
					[
						'theme_location' => $yka_footer_location,
						'container'      => false,
						'items_wrap'     => '<ul class="footer__menu">%3$s</ul>',
						'depth'          => 1,
						'walker'         => new YKA_Footer_Nav_Walker(),
					]
				);
			}
			?>

			<address class="footer__contacts">
				<?php if ( ! empty( $yka_address ) ) { ?>
					<p><?php echo esc_html( $yka_address ); ?></p>
				<?php } ?>
				<?php if ( ! empty( $yka_phone ) ) { ?>
					<a href="tel:<?php echo esc_attr( preg_replace( '/[^\d+]/', '', $yka_phone ) ); ?>"><?php echo esc_html( $yka_phone ); ?></a>
				<?php } ?>
				<?php if ( ! empty( $yka_email ) && is_email( $yka_email ) ) { ?>
					<a href="mailto:<?php echo esc_attr( $yka_email ); ?>"><?php echo esc_html( $yka_email ); ?></a>
				<?php } ?>
			</address>

			<div class="footer__aside">
				<?php if ( is_array( $yka_socials ) && ! empty( $yka_socials ) ) { ?>
					<div class="footer__socials">
						<?php
						foreach ( $yka_socials as $yka_social ) {
							if ( empty( $yka_social['link'] ) || empty( $yka_social['icon'] ) ) {
								continue;
							}
							?>
							<a href="<?php echo esc_url( $yka_social['link'] ); ?>" aria-label="<?php echo esc_attr( $yka_social['label'] ?? '' ); ?>" target="_blank" rel="noopener">
								<span class="icon"><?php yka_icon( 'icons/' . sanitize_file_name( $yka_social['icon'] ) . '.svg' ); ?></span>
							</a>
							<?php
						}
						?>
					</div>
				<?php } ?>

				<div class="footer__copy text-sm">
					<p><?php echo esc_html( $yka_copyright ); ?></p>
					<span class="footer__dev"><?php esc_html_e( 'Створення сайтів', 'ykagro' ); ?>
						<a href="https://redstone.agency/" target="_blank" rel="noopener" aria-label="Redstone media">
							<img src="<?php echo esc_url( yka_img( 'redstone-logo.svg' ) ); ?>" alt="Redstone media" width="83" height="12" loading="lazy" decoding="async">
						</a>
					</span>
				</div>
			</div>
		</div>

		<div class="footer__logo" aria-hidden="true">
			<?php yka_icon( 'logo-footer.svg' ); ?>
		</div>
	</div>
</footer>

<?php get_template_part( 'includes/modals' ); ?>

<?php wp_footer(); ?>
</body>
</html>
