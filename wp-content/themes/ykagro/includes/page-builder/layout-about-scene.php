<?php
/**
 * Page builder layout: about hero scene.
 *
 * One scroll-driven scene: the full-screen hero banner shrinks into the small
 * centred photo of the composition below while the hero copy fades out, the
 * title/lead fade in and the surrounding photos parallax. app-about.js drives it
 * and adds .is-scene; without JS (or with reduced motion) the block degrades to
 * a plain hero followed by the static composition — the target box holds the
 * same photo, so nothing is missing.
 *
 * @package ykagro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$hero_title = (string) get_sub_field( 'hero_title' );
$image      = get_sub_field( 'image' );
$image_mob  = get_sub_field( 'image_mob' );
$video      = (string) get_sub_field( 'video' );
$video_mob  = (string) get_sub_field( 'video_mob' );
$title      = (string) get_sub_field( 'title' );
$lead       = (string) get_sub_field( 'lead' );
$photos     = get_sub_field( 'photos' );

if ( empty( $image['ID'] ) && empty( $video ) ) {
	return;
}

$trail = [ [ 'label' => get_the_title() ] ];
?>
<section class="about-scene js-about-scene">
	<div class="about-scene__stage">

		<?php // Starts pushed down and rises into place while the banner shrinks. ?>
		<div class="about-scene__inner js-about-scene-inner">
			<?php // The box the banner lands in. Holds the same photo, so the scene reads correctly even before/without the animation. ?>
			<div class="about-scene__target js-about-scene-target">
				<picture>
					<?php
					if ( ! empty( $image['ID'] ) ) {
						echo wp_get_attachment_image(
							(int) $image['ID'],
							'full',
							false,
							[ 'alt' => '', 'loading' => 'lazy', 'decoding' => 'async' ]
						);
					}
					?>
				</picture>
			</div>

			<?php
			if ( is_array( $photos ) ) {
				foreach ( $photos as $photo ) {
					if ( empty( $photo['image']['ID'] ) ) {
						continue;
					}

					$slot        = ! empty( $photo['pos'] ) ? sanitize_key( $photo['pos'] ) : 'a';
					$speed       = ! empty( $photo['speed'] ) ? (float) $photo['speed'] : 1;
					$slot_video  = (string) ( $photo['video'] ?? '' );
					$slot_vid_m  = (string) ( $photo['video_mob'] ?? '' );
					$alt         = $photo['image']['alt'] ?? '';
					$poster      = wp_get_attachment_image_url( (int) $photo['image']['ID'], 'large' );
					$poster_mob  = ! empty( $photo['image_mob']['ID'] )
						? wp_get_attachment_image_url( (int) $photo['image_mob']['ID'], 'medium' )
						: $poster;
					?>
					<div class="about-scene__photo about-scene__photo--<?php echo esc_attr( $slot ); ?>" data-scene-speed="<?php echo esc_attr( (string) $speed ); ?>">
						<?php if ( ! empty( $slot_video ) ) { ?>
							<?php // Sources stay in data-src: app-global.js picks the one matching the breakpoint and only then calls load(), so a phone never fetches the desktop clip. ?>
							<video class="about-scene__video" autoplay muted loop playsinline webkit-playsinline preload="none"
								aria-label="<?php echo esc_attr( $alt ); ?>"
								poster="<?php echo esc_url( $poster ); ?>"
								data-poster-desktop="<?php echo esc_url( $poster ); ?>"
								data-poster-mobile="<?php echo esc_url( $poster_mob ); ?>">
								<?php if ( ! empty( $slot_vid_m ) ) { ?>
									<source data-src="<?php echo esc_url( $slot_vid_m ); ?>" data-media="(max-width: 767.98px)" type="video/mp4">
								<?php } ?>
								<source data-src="<?php echo esc_url( $slot_video ); ?>" type="video/mp4">
							</video>
						<?php } else { ?>
							<picture>
								<?php if ( ! empty( $photo['image_mob']['ID'] ) ) { ?>
									<source srcset="<?php echo esc_url( wp_get_attachment_image_url( (int) $photo['image_mob']['ID'], 'medium' ) ); ?>" media="(max-width: 767.98px)">
								<?php } ?>
								<?php
								echo wp_get_attachment_image(
									(int) $photo['image']['ID'],
									'large',
									false,
									[ 'alt' => $alt, 'loading' => 'lazy', 'decoding' => 'async' ]
								);
								?>
							</picture>
						<?php } ?>
					</div>
					<?php
				}
			}
			?>

			<div class="about-scene__copy">
				<?php if ( ! empty( $title ) ) { ?>
					<h2 class="about-scene__title h3 clr-text"><?php echo yka_heading( $title ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- yka_heading returns escaped HTML. ?></h2>
				<?php } ?>
				<?php if ( ! empty( $lead ) ) { ?>
					<p class="about-scene__lead text-lg clr-muted"><?php echo esc_html( $lead ); ?></p>
				<?php } ?>
			</div>
		</div>

		<?php // Straight-up .page-hero (same component as the other inner pages): the scene only transforms its .banner, everything else is stock. ?>
		<div class="page-hero about-scene__hero">
			<?php
			get_template_part(
				'template-parts/components/banner',
				null,
				[
					'image'      => empty( $video ) ? $image : null,
					'image_mob'  => empty( $video ) ? $image_mob : null,
					'video'      => $video,
					'video_mob'  => $video_mob,
					'poster'     => $image,
					'poster_mob' => $image_mob,
					'eager'      => true,
					// The scene animates this element.
					'class'      => 'js-about-scene-frame',
				]
			);
			?>

			<?php // Logo + breadcrumbs stay put and are switched off together in one go (app-about.js) — riding them up on scrub made the white notch look clipped and let the crumbs slide under it. ?>
			<div class="about-scene__hero-top js-about-scene-top">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="page-hero__logo about-scene__logo" aria-label="<?php esc_attr_e( 'YKagro — на головну', 'ykagro' ); ?>">
					<?php yka_icon( 'logo.svg' ); ?>
				</a>

				<div class="page-hero__breadcrumbs">
					<?php get_template_part( 'template-parts/components/breadcrumbs', null, [ 'items' => $trail ] ); ?>
				</div>
			</div>

			<?php // The title rides up and off the screen while the banner shrinks. ?>
			<div class="about-scene__hero-ui js-about-scene-lift">
				<?php if ( ! empty( $hero_title ) ) { ?>
					<h1 class="about-scene__hero-title h1 clr-white"><?php echo yka_heading( $hero_title ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- yka_heading returns escaped HTML. ?></h1>
				<?php } ?>
			</div>
		</div>

	</div>
</section>
