<?php
/**
 * Page builder layout: light stats variant — tag + centred heading, then cream
 * cards with an orange figure. Grid on desktop, Swiper below.
 *
 * @package ykagro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$tag   = (string) get_sub_field( 'tag' );
$title = (string) get_sub_field( 'title' );
$items = get_sub_field( 'items' );

if ( ! is_array( $items ) || empty( $items ) ) {
	return;
}
?>
<section class="about-stats">
	<div class="container">
		<div class="about-stats__head">
			<?php if ( ! empty( $tag ) ) { ?>
				<span class="tag"><?php echo esc_html( $tag ); ?></span>
			<?php } ?>
			<?php if ( ! empty( $title ) ) { ?>
				<h2 class="about-stats__title h1 clr-black"><?php echo yka_heading( $title ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- yka_heading returns escaped HTML. ?></h2>
			<?php } ?>
		</div>

		<div class="about-stats__slider swiper js-about-stats-slider">
			<div class="about-stats__grid swiper-wrapper">
				<?php
				foreach ( $items as $item ) {
					if ( empty( $item['num'] ) ) {
						continue;
					}
					?>
					<div class="about-stats__card swiper-slide">
						<span class="about-stats__num h2 clr-orange"><?php echo esc_html( $item['num'] ); ?></span>
						<p class="about-stats__text text-lg clr-black"><?php echo esc_html( $item['text'] ?? '' ); ?></p>
					</div>
					<?php
				}
				?>
			</div>
			<div class="about-stats__pagination swiper-pagination"></div>
		</div>
	</div>
</section>
