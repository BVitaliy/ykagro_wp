<?php
/**
 * Page builder layout: benefits — cards with a line icon.
 * Grid on desktop, Swiper below 992.
 *
 * @package ykagro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title = (string) get_sub_field( 'title' );
$cta   = get_sub_field( 'cta' );
$items = get_sub_field( 'items' );

if ( ! is_array( $items ) || empty( $items ) ) {
	return;
}
?>
<section class="benefits-section">
	<div class="benefits-section__inner container-full">
		<?php if ( ! empty( $title ) ) { ?>
			<h2 class="benefits-section__title h3"><?php echo yka_heading( $title ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- yka_heading returns escaped HTML. ?></h2>
		<?php } ?>

		<div class="benefits-section__slider swiper js-benefits-slider">
			<div class="benefits-section__grid swiper-wrapper">
				<?php
				foreach ( $items as $item ) {
					if ( empty( $item['title'] ) ) {
						continue;
					}
					?>
					<article class="benefit-card swiper-slide">
						<h3 class="benefit-card__title h5"><?php echo esc_html( $item['title'] ); ?></h3>
						<div class="benefit-card__foot">
							<p class="benefit-card__text text-lg clr-muted"><?php echo esc_html( $item['text'] ?? '' ); ?></p>
							<?php if ( ! empty( $item['icon'] ) || ! empty( $item['custom_icon'] ) ) { ?>
								<span class="benefit-card__icon" aria-hidden="true">
									<?php
									yka_icon_field(
										$item['custom_icon'] ?? null,
										! empty( $item['icon'] ) ? 'icons/' . sanitize_file_name( $item['icon'] ) . '.svg' : '',
										$item['title'] ?? ''
									);
									?>
								</span>
							<?php } ?>
						</div>
					</article>
					<?php
				}
				?>
			</div>
			<div class="benefits-section__pagination swiper-pagination"></div>
		</div>

		<?php yka_cta( $cta, 'btn benefits-section__cta' ); ?>
	</div>
</section>
