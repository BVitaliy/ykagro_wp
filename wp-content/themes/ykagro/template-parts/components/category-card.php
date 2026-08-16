<?php
/**
 * Category / product tile — rounded photo + label row.
 *
 * @param array $args {
 *     @type array  $image ACF image array or attachment array with an ID.
 *     @type string $title
 *     @type string $href
 * }
 *
 * @package ykagro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$image = $args['image'] ?? null;
$title = $args['title'] ?? '';
$href  = $args['href'] ?? '';

if ( empty( $title ) ) {
	return;
}
?>
<a class="category-card" href="<?php echo esc_url( $href ); ?>">
	<span class="category-card__media">
		<?php
		if ( ! empty( $image['ID'] ) ) {
			echo wp_get_attachment_image(
				(int) $image['ID'],
				'yka-card',
				false,
				[
					'alt'      => $image['alt'] ?: $title,
					'loading'  => 'lazy',
					'decoding' => 'async',
				]
			);
		}
		?>
	</span>
	<span class="category-card__label">
		<span class="category-card__title text-big"><?php echo esc_html( $title ); ?></span>
		<span class="category-card__icon"><?php yka_icon( 'icons/arrow-diagonal.svg' ); ?></span>
	</span>
</a>
