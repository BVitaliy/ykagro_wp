<?php
/**
 * Page builder layout: photo cards (conditions / advantages).
 * Grid on desktop, Swiper below 992.
 *
 * A card with a link becomes an <a>; without one it stays an <article>, matching
 * the markup — that is why the tag is swapped rather than always using <a href="#">.
 *
 * @package ykagro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title = (string) get_sub_field( 'title' );
$items = get_sub_field( 'items' );

if ( ! is_array( $items ) || empty( $items ) ) {
	return;
}
?>
<section class="comfort-cards">
	<div class="container-full">
		<?php if ( ! empty( $title ) ) { ?>
			<h2 class="comfort-cards__title h3 clr-black"><?php echo yka_heading( $title ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- yka_heading returns escaped HTML. ?></h2>
		<?php } ?>

		<div class="comfort-cards__slider swiper js-comfort-slider">
			<div class="comfort-cards__grid swiper-wrapper">
				<?php
				foreach ( $items as $item ) {
					if ( empty( $item['title'] ) ) {
						continue;
					}

					$url      = $item['link']['url'] ?? '';
					$is_link  = ! empty( $url );
					$tag_name = $is_link ? 'a' : 'article';
					$classes  = 'comfort-card' . ( $is_link ? ' comfort-card--link' : '' ) . ' swiper-slide';
					?>
					<<?php echo esc_html( $tag_name ); ?> class="<?php echo esc_attr( $classes ); ?>"<?php echo $is_link ? ' href="' . esc_url( $url ) . '"' : ''; ?>>
						<span class="comfort-card__media">
							<?php
							if ( ! empty( $item['image']['ID'] ) ) {
								echo wp_get_attachment_image(
									(int) $item['image']['ID'],
									'yka-comfort',
									false,
									[
										'alt'      => $item['image']['alt'] ?: $item['title'],
										'loading'  => 'lazy',
										'decoding' => 'async',
									]
								);
							}
							?>
						</span>
						<span class="comfort-card__title h6"><?php echo esc_html( $item['title'] ); ?></span>
						<?php if ( ! empty( $item['text'] ) ) { ?>
							<span class="comfort-card__text text-lg clr-muted"><?php echo esc_html( $item['text'] ); ?></span>
						<?php } ?>
					</<?php echo esc_html( $tag_name ); ?>>
					<?php
				}
				?>
			</div>
			<div class="comfort-cards__pagination swiper-pagination"></div>
		</div>
	</div>
</section>
