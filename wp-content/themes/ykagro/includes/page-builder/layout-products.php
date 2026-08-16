<?php
/**
 * Page builder layout: products — heading + CTA + slider of category tiles.
 *
 * The source switch lets one block show either product categories or hand-picked
 * products; both render through the same card component.
 *
 * @package ykagro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title  = (string) get_sub_field( 'title' );
$cta    = get_sub_field( 'cta' );
$source = (string) get_sub_field( 'source' );

$cards = [];

if ( 'products' === $source ) {
	$product_ids = get_sub_field( 'products' );

	if ( ! empty( $product_ids ) && is_array( $product_ids ) ) {
		foreach ( $product_ids as $product_id ) {
			$product_id = (int) $product_id;
			$gallery    = get_field( 'gallery', $product_id );
			$image      = ! empty( $gallery[0] ) ? $gallery[0] : null;

			if ( empty( $image ) && has_post_thumbnail( $product_id ) ) {
				$image = [ 'ID' => get_post_thumbnail_id( $product_id ), 'alt' => '' ];
			}

			$cards[] = [
				'image' => $image,
				'title' => get_the_title( $product_id ),
				'href'  => get_permalink( $product_id ),
			];
		}
	}
} else {
	$term_ids = get_sub_field( 'categories' );

	$terms = get_terms(
		[
			'taxonomy'   => 'product_cat',
			'hide_empty' => false,
			'include'    => ! empty( $term_ids ) && is_array( $term_ids ) ? array_map( 'intval', $term_ids ) : [],
			'parent'     => ! empty( $term_ids ) ? '' : 0,
			'orderby'    => ! empty( $term_ids ) ? 'include' : 'name',
		]
	);

	if ( ! is_wp_error( $terms ) ) {
		foreach ( $terms as $term ) {
			$thumb_id = (int) get_term_meta( $term->term_id, 'thumbnail_id', true );

			$cards[] = [
				'image' => $thumb_id ? [ 'ID' => $thumb_id, 'alt' => $term->name ] : null,
				'title' => $term->name,
				'href'  => get_term_link( $term ),
			];
		}
	}
}

if ( empty( $cards ) ) {
	return;
}
?>
<section class="home-products">
	<div class="container">
		<div class="home-products__head">
			<?php if ( ! empty( $title ) ) { ?>
				<h2 class="home-products__title h2 clr-black"><?php echo yka_heading( $title ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- yka_heading returns escaped HTML. ?></h2>
			<?php } ?>
			<?php yka_cta( $cta, 'btn home-products__cta' ); ?>
		</div>

		<div class="home-products__slider swiper js-products-slider">
			<div class="swiper-wrapper">
				<?php
				foreach ( $cards as $card ) {
					?>
					<div class="swiper-slide">
						<?php get_template_part( 'template-parts/components/category-card', null, $card ); ?>
					</div>
					<?php
				}
				?>
			</div>
			<div class="home-products__pagination swiper-pagination"></div>
		</div>
	</div>
</section>
