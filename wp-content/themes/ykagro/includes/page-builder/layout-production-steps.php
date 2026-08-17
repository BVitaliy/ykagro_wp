<?php
/**
 * Page builder layout: production steps.
 *
 * One fullpage slider — the section pins for N screens of scroll and the panels
 * slide through (GSAP ScrollTrigger, so it rides Lenis instead of fighting it).
 * Below lg the pin is dropped and panels simply stack; the markup is identical
 * either way.
 *
 * @package ykagro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$items = get_sub_field( 'items' );

if ( ! is_array( $items ) || empty( $items ) ) {
	return;
}

$steps = array_values(
	array_filter(
		$items,
		static function ( $item ) {
			return ! empty( $item['title'] );
		}
	)
);

if ( empty( $steps ) ) {
	return;
}

$total = count( $steps );
?>
<section class="production-steps js-production-steps" aria-label="<?php esc_attr_e( 'Етапи виробництва', 'ykagro' ); ?>">
	<div class="production-steps__viewport">
		<?php // stage clips inside the page-hero-style inset, so neighbouring panels never peek through the padding band ?>
		<div class="production-steps__stage swiper js-production-slider">
			<div class="production-steps__track swiper-wrapper">
				<?php
				foreach ( $steps as $index => $step ) {
					// Cards alternate right / left, starting on the right.
					$side  = 0 === $index % 2 ? 'right' : 'left';
					$video = (string) ( $step['video'] ?? '' );
					?>
					<article class="production-steps__panel production-steps__panel--<?php echo esc_attr( $side ); ?> swiper-slide">
						<?php
						get_template_part(
							'template-parts/components/banner',
							null,
							[
								'image'      => empty( $video ) ? ( $step['image'] ?? null ) : null,
								'image_mob'  => empty( $video ) ? ( $step['image_mob'] ?? null ) : null,
								'video'      => $video,
								'video_mob'  => (string) ( $step['video_mob'] ?? '' ),
								'poster'     => $step['image'] ?? null,
								'poster_mob' => $step['image_mob'] ?? null,
								'eager'      => 0 === $index,
								'class'      => 'production-steps__banner',
							]
						);
						?>

						<?php // outer element carries the drop-shadow, inner the clip-path: a box-shadow would be cut away by the mask ?>
						<div class="production-steps__card">
							<div class="production-steps__card-inner">
								<div class="production-steps__card-head">
									<?php // opt out of the shared title reveal: these headings enter the viewport by transform inside a pinned section, so a scroll-position trigger would fire at the wrong time ?>
									<h2 class="production-steps__card-title" data-title-anim="off"><?php echo esc_html( $step['title'] ); ?></h2>
									<span class="tag production-steps__card-num"><?php echo esc_html( sprintf( '%02d - %02d', $index + 1, $total ) ); ?></span>
								</div>
								<div class="production-steps__card-body">
									<?php if ( ! empty( $step['text'] ) ) { ?>
										<p class="production-steps__card-text"><?php echo esc_html( $step['text'] ); ?></p>
									<?php } ?>
									<?php
									if ( ! empty( $step['icon']['url'] ) ) {
										$scale = ! empty( $step['icon_scale'] ) ? (float) $step['icon_scale'] : 1;
										?>
										<span class="production-steps__card-icon" style="--step-icon-scale: <?php echo esc_attr( (string) $scale ); ?>;" aria-hidden="true">
											<?php // GIFs must not go through wp_get_attachment_image — a resized copy loses the animation. ?>
											<img src="<?php echo esc_url( $step['icon']['url'] ); ?>" alt="" width="185" height="185" loading="lazy" decoding="async">
										</span>
										<?php
									}
									?>
								</div>
							</div>
						</div>
					</article>
					<?php
				}
				?>
			</div>
		</div>
	</div>
</section>
