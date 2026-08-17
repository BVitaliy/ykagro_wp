<?php
/**
 * Product hero — photo slider on the left, info column on the right.
 *
 * @package ykagro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_id = get_the_ID();
$gallery = get_field( 'gallery', $post_id );
$text    = (string) get_field( 'short_text', $post_id );
$items   = get_field( 'features', $post_id );
$cta     = get_field( 'cta', $post_id );

if ( ! is_array( $gallery ) ) {
	$gallery = [];
}

// Fall back to the featured image so the slider is never empty.
if ( empty( $gallery ) && has_post_thumbnail( $post_id ) ) {
	$gallery = [ [ 'ID' => get_post_thumbnail_id( $post_id ), 'alt' => get_the_title( $post_id ) ] ];
}

$cta_label  = ! empty( $cta['label'] ) ? $cta['label'] : __( 'Замовити', 'ykagro' );
$cta_action = ! empty( $cta['action'] ) ? $cta['action'] : 'modal';
$cta_url    = $cta['link']['url'] ?? '';
$is_modal   = 'link' !== $cta_action || empty( $cta_url );
?>
<section class="product-hero">
	<div class="container product-hero__inner">
		<div class="product-hero__gallery js-product-hero-lightbox">
			<div class="product-hero__slider swiper js-product-gallery">
				<div class="swiper-wrapper">
					<?php
					foreach ( $gallery as $index => $image ) {
						if ( empty( $image['ID'] ) ) {
							continue;
						}

						$full = wp_get_attachment_image_url( (int) $image['ID'], 'large' );
						?>
						<div class="swiper-slide product-hero__slide" data-photo-index="<?php echo esc_attr( (string) $index ); ?>">
							<a
								class="product-hero__lightbox"
								href="<?php echo esc_url( $full ); ?>"
								data-lg-src="<?php echo esc_url( $full ); ?>"
								data-thumb="<?php echo esc_url( $full ); ?>"
								data-photo-index="<?php echo esc_attr( (string) $index ); ?>"
								aria-label="<?php
									/* translators: %d: photo number. */
									echo esc_attr( sprintf( __( 'Відкрити фото %d у галереї', 'ykagro' ), $index + 1 ) );
								?>"
							>
								<?php
								echo wp_get_attachment_image(
									(int) $image['ID'],
									'yka-product',
									false,
									[
										'alt'           => $image['alt'] ?: get_the_title( $post_id ),
										'loading'       => 0 === $index ? 'eager' : 'lazy',
										'fetchpriority' => 0 === $index ? 'high' : 'auto',
										'decoding'      => 'async',
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

			<?php // Rooster badge in the notch cut out of the photo ?>
			<span class="product-hero__badge" aria-hidden="true"><?php yka_icon( 'icons/rooster.svg' ); ?></span>

			<?php if ( count( $gallery ) > 1 ) { ?>
				<button class="btn-round btn-round--prev product-hero__arrow product-hero__arrow--prev js-product-gallery-prev" type="button" aria-label="<?php esc_attr_e( 'Попереднє фото', 'ykagro' ); ?>">
					<span class="icon"><?php yka_icon( 'icons/arrow-right.svg' ); ?></span>
				</button>
				<button class="btn-round product-hero__arrow product-hero__arrow--next js-product-gallery-next" type="button" aria-label="<?php esc_attr_e( 'Наступне фото', 'ykagro' ); ?>">
					<span class="icon"><?php yka_icon( 'icons/arrow-right.svg' ); ?></span>
				</button>
			<?php } ?>
		</div>

		<div class="product-hero__body">
			<h1 class="product-hero__title h3 clr-black"><?php the_title(); ?></h1>

			<?php // Editor content: bare tags only, styled from .product-hero__body. ?>
			<?php if ( ! empty( $text ) ) { ?>
				<p><?php echo esc_html( $text ); ?></p>
			<?php } ?>

			<?php if ( is_array( $items ) && ! empty( $items ) ) { ?>
				<ul>
					<?php
					foreach ( $items as $item ) {
						if ( empty( $item['text'] ) ) {
							continue;
						}
						?>
						<li><?php echo esc_html( $item['text'] ); ?></li>
						<?php
					}
					?>
				</ul>
			<?php } ?>

			<a href="<?php echo $is_modal ? '#' : esc_url( $cta_url ); ?>" class="btn product-hero__cta"<?php echo $is_modal ? ' data-modal-open="appointment"' : ''; ?>>
				<span class="btn__icon"><?php yka_icon( 'icons/arrow-diagonal.svg' ); ?></span>
				<?php echo esc_html( $cta_label ); ?>
			</a>
		</div>
	</div>
</section>
