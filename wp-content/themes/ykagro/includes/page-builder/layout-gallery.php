<?php
/**
 * Page builder layout: gallery — centred heading + horizontal photo slider
 * with a lightbox.
 *
 * The markup repeats the image set three times so the slider reads as endless;
 * data-photo-index keeps the lightbox pointing at the original photo.
 *
 * @package ykagro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title  = (string) get_sub_field( 'title' );
$images = get_sub_field( 'images' );

if ( ! is_array( $images ) || empty( $images ) ) {
	return;
}

$base_count = count( $images );
$slides     = array_merge( $images, $images, $images );
?>
<section class="home-gallery js-gallery">
	<?php if ( ! empty( $title ) ) { ?>
		<div class="container">
			<h2 class="home-gallery__title" style="text-align:center"><?php echo yka_heading( $title ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- yka_heading returns escaped HTML. ?></h2>
		</div>
	<?php } ?>

	<div class="home-gallery__slider swiper js-gallery-slider">
		<div class="swiper-wrapper">
			<?php
			foreach ( $slides as $index => $image ) {
				if ( empty( $image['ID'] ) ) {
					continue;
				}

				$full_url = wp_get_attachment_image_url( (int) $image['ID'], 'large' );
				?>
				<div class="swiper-slide home-gallery__slide" data-photo-index="<?php echo esc_attr( (string) ( $index % $base_count ) ); ?>">
					<a class="home-gallery__media" href="<?php echo esc_url( $full_url ); ?>" data-lg-src="<?php echo esc_url( $full_url ); ?>" aria-label="<?php esc_attr_e( 'Відкрити фото галереї', 'ykagro' ); ?>">
						<?php
						echo wp_get_attachment_image(
							(int) $image['ID'],
							'yka-gallery',
							false,
							[
								'alt'      => $image['alt'] ?? '',
								'loading'  => 'lazy',
								'decoding' => 'async',
							]
						);
						?>
					</a>
				</div>
				<?php
			}
			?>
		</div>
	</div>

	<div class="home-gallery__foot container">
		<button class="btn-round btn-round--prev js-gallery-prev" type="button" aria-label="<?php esc_attr_e( 'Назад', 'ykagro' ); ?>">
			<span class="icon"><?php yka_icon( 'icons/arrow-right.svg' ); ?></span>
		</button>
		<button class="btn-round js-gallery-next" type="button" aria-label="<?php esc_attr_e( 'Далі', 'ykagro' ); ?>">
			<span class="icon"><?php yka_icon( 'icons/arrow-right.svg' ); ?></span>
		</button>
	</div>
</section>
