<?php
/**
 * "Інші товари у категорії" — heading + CTA, then catalog cards full-bleed.
 *
 * Same slider as the other card rows on the site, so it reads as a 3-up grid on
 * desktop and swipes on mobile.
 *
 * @package ykagro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_id = get_the_ID();
$terms   = wp_get_object_terms( $post_id, 'product_cat', [ 'fields' => 'ids' ] );

$args = [
	'post_type'      => 'product',
	'post_status'    => 'publish',
	'posts_per_page' => 8,
	'post__not_in'   => [ $post_id ],
	'orderby'        => [ 'menu_order' => 'ASC', 'date' => 'DESC' ],
	'fields'         => 'ids',
];

// Prefer siblings from the same category; fall back to any other product so the
// row is never empty on a thin catalogue.
if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
	$args['tax_query'] = [
		[
			'taxonomy' => 'product_cat',
			'field'    => 'term_id',
			'terms'    => $terms,
		],
	];
}

$related = get_posts( $args );

if ( empty( $related ) ) {
	unset( $args['tax_query'] );
	$related = get_posts( $args );
}

if ( empty( $related ) ) {
	return;
}

$products_page = get_page_by_path( 'products' );
?>
<section class="product-related">
	<div class="container product-related__head">
		<h2 class="product-related__title h2 clr-black"><?php esc_html_e( 'Інші товари у категорії', 'ykagro' ); ?></h2>
		<?php if ( $products_page ) { ?>
			<a href="<?php echo esc_url( get_permalink( $products_page ) ); ?>" class="btn product-related__cta">
				<span class="btn__icon"><?php yka_icon( 'icons/arrow-diagonal.svg' ); ?></span>
				<?php esc_html_e( 'Вся продукція', 'ykagro' ); ?>
			</a>
		<?php } ?>
	</div>

	<div class="container-full">
		<div class="product-related__slider swiper js-products-slider">
			<div class="swiper-wrapper">
				<?php
				foreach ( $related as $related_id ) {
					?>
					<div class="swiper-slide product-related__slide">
						<?php get_template_part( 'template-parts/components/catalog-card', null, [ 'post_id' => $related_id ] ); ?>
					</div>
					<?php
				}
				?>
			</div>
			<div class="product-related__pagination swiper-pagination"></div>
		</div>
	</div>
</section>
