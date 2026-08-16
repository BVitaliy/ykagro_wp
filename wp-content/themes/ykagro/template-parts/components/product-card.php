<?php
/**
 * Glass product card — square photo + title + "детальніше".
 * Used in the hero as a floating promo card.
 *
 * @param array $args {
 *     @type array  $image       ACF image array.
 *     @type string $title
 *     @type string $href
 *     @type bool   $dismissible Show the close button.
 * }
 *
 * @package ykagro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$image       = $args['image'] ?? null;
$title       = $args['title'] ?? '';
$href        = $args['href'] ?? '';
$dismissible = ! empty( $args['dismissible'] );

if ( empty( $title ) ) {
	return;
}
?>
<div class="product-card js-product-card">
	<?php if ( $dismissible ) { ?>
		<button class="product-card__close js-product-card-close" type="button" aria-label="<?php esc_attr_e( 'Закрити', 'ykagro' ); ?>">
			<?php yka_icon( 'icons/close.svg' ); ?>
		</button>
	<?php } ?>
	<a class="product-card__link" href="<?php echo esc_url( $href ); ?>">
		<span class="product-card__media">
			<?php
			if ( ! empty( $image['ID'] ) ) {
				echo wp_get_attachment_image(
					(int) $image['ID'],
					'thumbnail',
					false,
					[
						'alt'      => $image['alt'] ?: $title,
						'width'    => 150,
						'height'   => 150,
						'loading'  => 'lazy',
						'decoding' => 'async',
					]
				);
			}
			?>
		</span>
		<span class="product-card__body">
			<span class="product-card__title"><?php echo esc_html( $title ); ?></span>
			<span class="product-card__more link-more">
				<?php esc_html_e( 'детальніше', 'ykagro' ); ?>
				<span class="link-more__icon"><?php yka_icon( 'icons/arrow-diagonal.svg' ); ?></span>
			</span>
		</span>
	</a>
</div>
