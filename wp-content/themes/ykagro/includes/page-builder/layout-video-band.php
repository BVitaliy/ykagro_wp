<?php
/**
 * Page builder layout: full-bleed video band with a play button.
 *
 * The looping background is the shared banner component; its poster covers every
 * case where autoplay is refused (iOS Low Power Mode, Data Saver, reduced
 * motion) — that is what <video poster> shows, so no extra markup is needed.
 * The play button opens the full clip with sound in the shared modal.
 *
 * @package ykagro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$video      = (string) get_sub_field( 'video' );
$video_mob  = (string) get_sub_field( 'video_mob' );
$poster     = get_sub_field( 'poster' );
$poster_mob = get_sub_field( 'poster_mob' );
$full_video = (string) get_sub_field( 'full_video' );
$play_label = (string) get_sub_field( 'play_label' );

if ( empty( $video ) && empty( $poster['ID'] ) ) {
	return;
}

$poster_url = ! empty( $poster['ID'] ) ? wp_get_attachment_image_url( (int) $poster['ID'], 'yka-banner' ) : '';
?>
<section class="about-video">
	<div class="about-video__media">
		<?php
		get_template_part(
			'template-parts/components/banner',
			null,
			[
				// With no video the banner falls back to the poster as a picture.
				'image'      => empty( $video ) ? $poster : null,
				'image_mob'  => empty( $video ) ? $poster_mob : null,
				'video'      => $video,
				'video_mob'  => $video_mob,
				'poster'     => $poster,
				'poster_mob' => $poster_mob,
			]
		);
		?>
	</div>

	<?php if ( ! empty( $full_video ) ) { ?>
		<button class="about-video__play js-video-open" type="button"
			aria-label="<?php echo esc_attr( ! empty( $play_label ) ? $play_label : __( 'Дивитися відео', 'ykagro' ) ); ?>"
			data-video-src="<?php echo esc_url( $full_video ); ?>"
			data-video-poster="<?php echo esc_url( $poster_url ); ?>">
			<?php yka_icon( 'icons/play-circle.svg' ); ?>
		</button>
	<?php } ?>
</section>
