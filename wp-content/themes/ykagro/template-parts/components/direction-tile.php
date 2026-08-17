<?php
/**
 * Direction tile — full-bleed photo card used on the directions listing.
 *
 * The whole tile is the link, so the "Детальніше" button is a <span> to stay
 * valid inside <a>.
 *
 * Art direction matters here: below sm the tile box turns portrait, so the
 * mobile crop is a different image, not just a smaller file.
 *
 * @param array $args {
 *     @type int    $post_id Direction post.
 *     @type string $num     Zero-padded index.
 *     @type string $total   Zero-padded total.
 *     @type bool   $eager   Above the fold.
 * }
 *
 * @package ykagro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_id = ! empty( $args['post_id'] ) ? (int) $args['post_id'] : 0;

if ( ! $post_id ) {
	return;
}

$title = get_the_title( $post_id );
$text  = (string) get_field( 'card_text', $post_id );
$num   = $args['num'] ?? '';
$total = $args['total'] ?? '';
$eager = ! empty( $args['eager'] );

$image     = get_field( 'tile_image', $post_id );
$image_mob = get_field( 'tile_image_mob', $post_id );

// Fall back to the featured image so a tile is never blank.
if ( empty( $image['ID'] ) && has_post_thumbnail( $post_id ) ) {
	$image = [ 'ID' => get_post_thumbnail_id( $post_id ), 'alt' => $title ];
}
?>
<a class="direction-tile" href="<?php echo esc_url( get_permalink( $post_id ) ); ?>">
	<span class="direction-tile__media">
		<picture>
			<?php if ( ! empty( $image_mob['ID'] ) ) { ?>
				<source srcset="<?php echo esc_url( wp_get_attachment_image_url( (int) $image_mob['ID'], 'yka-banner-mob' ) ); ?>" media="(max-width: 575.98px)">
			<?php } ?>
			<?php
			if ( ! empty( $image['ID'] ) ) {
				echo wp_get_attachment_image(
					(int) $image['ID'],
					'yka-tile',
					false,
					[
						'alt'      => $image['alt'] ?: $title,
						'loading'  => $eager ? 'eager' : 'lazy',
						'decoding' => 'async',
					]
				);
			}
			?>
		</picture>
	</span>

	<span class="direction-tile__head">
		<h3 class="direction-tile__title h5 clr-white"><?php echo esc_html( $title ); ?></h3>
		<?php if ( ! empty( $num ) && ! empty( $total ) ) { ?>
			<span class="direction-tile__count"><?php echo esc_html( $num ); ?> - <?php echo esc_html( $total ); ?></span>
		<?php } ?>
	</span>

	<span class="direction-tile__body">
		<?php if ( ! empty( $text ) ) { ?>
			<span class="direction-tile__text text-lg"><?php echo esc_html( $text ); ?></span>
		<?php } ?>
		<span class="btn btn--light direction-tile__more">
			<span class="btn__icon"><?php yka_icon( 'icons/arrow-diagonal.svg' ); ?></span>
			<?php esc_html_e( 'Детальніше', 'ykagro' ); ?>
		</span>
	</span>
</a>
