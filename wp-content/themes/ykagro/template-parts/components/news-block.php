<?php
/**
 * News block — text column + angled-cut photo, image left or right.
 *
 * The body children carry no classes on purpose: they are styled from the
 * .news-block__body parent, so a wysiwyg blob renders identically.
 *
 * @param array $args {
 *     @type string $side  'left' or 'right'. Default 'right'.
 *     @type string $tag   Optional pill above the title.
 *     @type string $title
 *     @type string $text  Wysiwyg HTML.
 *     @type array  $items List rows, each ['text' => string].
 *     @type array  $image ACF image array.
 *     @type array  $cta   ACF group with 'label' and 'link'.
 * }
 *
 * @package ykagro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$side  = ( isset( $args['side'] ) && 'left' === $args['side'] ) ? 'left' : 'right';
$tag   = $args['tag'] ?? '';
$title = $args['title'] ?? '';
$text  = $args['text'] ?? '';
$items = $args['items'] ?? [];
$image = $args['image'] ?? null;
$cta   = $args['cta'] ?? null;
?>
<div class="news-block news-block--img-<?php echo esc_attr( $side ); ?>">
	<div class="news-block__body">
		<?php if ( ! empty( $tag ) ) { ?>
			<span class="tag news-block__tag"><?php echo esc_html( $tag ); ?></span>
		<?php } ?>

		<?php if ( ! empty( $title ) ) { ?>
			<h3 class="news-block__title h4 clr-black"><?php echo yka_heading( $title ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- yka_heading escapes and returns safe HTML. ?></h3>
		<?php } ?>

		<?php if ( ! empty( $text ) ) { ?>
			<?php echo wp_kses_post( $text ); ?>
		<?php } ?>

		<?php if ( is_array( $items ) && ! empty( $items ) ) { ?>
			<ul>
				<?php
				foreach ( $items as $item ) {
					if ( empty( $item['text'] ) ) {
						continue;
					}
					?>
					<li class="news-block__item text-lg fw-500 clr-black"><?php echo esc_html( $item['text'] ); ?></li>
					<?php
				}
				?>
			</ul>
		<?php } ?>

		<?php yka_cta( $cta, 'btn news-block__cta' ); ?>
	</div>

	<div class="news-block__media" data-parallax-media data-parallax-intensity="120">
		<picture data-parallax-target>
			<?php
			if ( ! empty( $image['ID'] ) ) {
				echo wp_get_attachment_image(
					(int) $image['ID'],
					'yka-news',
					false,
					[
						'alt'      => $image['alt'] ?? '',
						'loading'  => 'lazy',
						'decoding' => 'async',
					]
				);
			}
			?>
		</picture>
	</div>
</div>
