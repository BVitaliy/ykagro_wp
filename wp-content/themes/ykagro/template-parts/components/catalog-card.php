<?php
/**
 * Catalog card — product tile on category listings: rounded photo + centred name.
 *
 * @param array $args {
 *     @type int $post_id Product post. Defaults to the current post.
 * }
 *
 * @package ykagro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_id = ! empty( $args['post_id'] ) ? (int) $args['post_id'] : get_the_ID();

if ( ! $post_id ) {
	return;
}

$title = get_the_title( $post_id );

// The gallery's first shot is the product's face; the featured image is a fallback.
$gallery  = get_field( 'gallery', $post_id );
$image_id = ! empty( $gallery[0]['ID'] ) ? (int) $gallery[0]['ID'] : 0;

if ( ! $image_id && has_post_thumbnail( $post_id ) ) {
	$image_id = get_post_thumbnail_id( $post_id );
}
?>
<a class="catalog-card" href="<?php echo esc_url( get_permalink( $post_id ) ); ?>">
	<span class="catalog-card__media">
		<?php
		if ( $image_id ) {
			echo wp_get_attachment_image(
				$image_id,
				'yka-catalog',
				false,
				[
					'alt'      => $title,
					'loading'  => 'lazy',
					'decoding' => 'async',
				]
			);
		}
		?>
	</span>
	<span class="catalog-card__body">
		<span class="catalog-card__title h6"><?php echo esc_html( $title ); ?></span>
		<span class="catalog-card__more link-more">
			<?php esc_html_e( 'Детальніше', 'ykagro' ); ?>
			<span class="link-more__icon"><?php yka_icon( 'icons/arrow-diagonal.svg' ); ?></span>
		</span>
	</span>
</a>
