<?php
/**
 * Page banner media — video or picture, with an optional mobile source.
 *
 * @param array $args {
 *     @type array  $image      ACF image array (desktop).
 *     @type array  $image_mob  ACF image array (mobile crop).
 *     @type string $video      Video URL (mp4). Takes precedence over $image.
 *     @type string $video_mob  Mobile video URL.
 *     @type array  $poster     ACF image array used as the video poster.
 *     @type array  $poster_mob ACF image array, mobile poster.
 *     @type bool   $eager      Above the fold — eager + fetchpriority high.
 *     @type string $class      Extra class on the wrapper.
 * }
 *
 * @package ykagro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$image      = $args['image'] ?? null;
$image_mob  = $args['image_mob'] ?? null;
$video      = $args['video'] ?? '';
$video_mob  = $args['video_mob'] ?? '';
$poster     = $args['poster'] ?? $image;
$poster_mob = $args['poster_mob'] ?? ( $image_mob ?: $poster );
$eager      = ! empty( $args['eager'] );
$extra      = ! empty( $args['class'] ) ? ' ' . $args['class'] : '';

$poster_url     = ! empty( $poster['url'] ) ? $poster['url'] : '';
$poster_mob_url = ! empty( $poster_mob['url'] ) ? $poster_mob['url'] : $poster_url;
?>
<div class="banner<?php echo esc_attr( $extra ); ?>">
	<?php if ( ! empty( $video ) ) { ?>
		<?php // webkit-playsinline: older iOS still needs the vendor attribute or it goes fullscreen instead of autoplaying inline. ?>
		<video class="banner__video" autoplay muted loop playsinline webkit-playsinline preload="metadata"
			poster="<?php echo esc_url( $poster_url ); ?>"
			data-poster-desktop="<?php echo esc_url( $poster_url ); ?>"
			data-poster-mobile="<?php echo esc_url( $poster_mob_url ); ?>">
			<?php if ( ! empty( $video_mob ) ) { ?>
				<source data-src="<?php echo esc_url( $video_mob ); ?>" data-media="(max-width: 767.98px)" type="video/mp4">
			<?php } ?>
			<source data-src="<?php echo esc_url( $video ); ?>" type="video/mp4">
		</video>
	<?php } elseif ( ! empty( $image['ID'] ) ) { ?>
		<picture>
			<?php if ( ! empty( $image_mob['ID'] ) ) { ?>
				<source srcset="<?php echo esc_url( wp_get_attachment_image_url( (int) $image_mob['ID'], 'yka-banner-mob' ) ); ?>" media="(max-width: 767.98px)">
			<?php } ?>
			<?php
			echo wp_get_attachment_image(
				(int) $image['ID'],
				'yka-banner',
				false,
				[
					'alt'           => $image['alt'] ?? '',
					'loading'       => $eager ? 'eager' : 'lazy',
					'fetchpriority' => $eager ? 'high' : 'auto',
					'decoding'      => 'async',
				]
			);
			?>
		</picture>
	<?php } ?>
	<div class="banner__overlay"></div>
</div>
