<?php
/**
 * Page builder layout: intro — two overlapping curved photos + floating text card.
 * Optional quote variant adds the quote mark and hen illustration.
 *
 * @package ykagro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$is_quote    = (bool) get_sub_field( 'is_quote' );
$tag         = (string) get_sub_field( 'tag' );
$author      = (string) get_sub_field( 'author' );
$title       = (string) get_sub_field( 'title' );
$text        = (string) get_sub_field( 'text' );
$cta         = get_sub_field( 'cta' );
$image_left  = get_sub_field( 'image_left' );
$image_right = get_sub_field( 'image_right' );

$has_cta = ! empty( $cta['link']['url'] ) && ! empty( $cta['label'] );
?>
<section class="home-intro<?php echo $is_quote ? ' home-intro--quote' : ''; ?>">
	<div class="container">
		<div class="home-intro__grid">
			<div class="home-intro__media home-intro__media--left">
				<?php
				if ( ! empty( $image_left['ID'] ) ) {
					echo wp_get_attachment_image(
						(int) $image_left['ID'],
						'yka-intro-left',
						false,
						[ 'alt' => $image_left['alt'] ?? '', 'loading' => 'lazy', 'decoding' => 'async' ]
					);
				}
				?>
			</div>

			<div class="home-intro__media home-intro__media--right">
				<?php
				if ( ! empty( $image_right['ID'] ) ) {
					echo wp_get_attachment_image(
						(int) $image_right['ID'],
						'yka-intro-right',
						false,
						[ 'alt' => $image_right['alt'] ?? '', 'loading' => 'lazy', 'decoding' => 'async' ]
					);
				}
				?>
			</div>

			<div class="home-intro__card">
				<?php if ( $is_quote ) { ?>
					<div class="home-intro__quote-row" aria-hidden="true">
						<img class="home-intro__quote-mark" src="<?php echo esc_url( yka_img( 'icons/quote.gif' ) ); ?>" alt="" width="100" height="100" loading="lazy">
						<span class="home-intro__hen"><?php yka_icon( 'about/quote-hen.svg' ); ?></span>
					</div>
				<?php } ?>

				<?php if ( ! empty( $tag ) ) { ?>
					<span class="tag home-intro__tag"><?php echo esc_html( $tag ); ?></span>
				<?php } ?>

				<?php if ( ! empty( $title ) ) { ?>
					<h2 class="home-intro__title h4"><?php echo yka_heading( $title ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- yka_heading returns escaped HTML. ?></h2>
				<?php } ?>

				<?php if ( ! empty( $author ) ) { ?>
					<p class="home-intro__author"><?php echo esc_html( $author ); ?></p>
				<?php } ?>

				<?php if ( ! empty( $text ) || $has_cta ) { ?>
					<div class="home-intro__card-footer">
						<?php if ( ! empty( $text ) ) { ?>
							<p class="home-intro__text text-lg clr-gray"><?php echo esc_html( $text ); ?></p>
						<?php } ?>
						<?php yka_cta( $cta, 'btn home-intro__cta' ); ?>
					</div>
				<?php } ?>
			</div>
		</div>
	</div>
</section>
