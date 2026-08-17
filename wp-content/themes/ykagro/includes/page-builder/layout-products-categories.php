<?php
/**
 * Page builder layout: product categories as a three-column grid.
 * Same card component as the homepage slider.
 *
 * @package ykagro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$term_ids = get_sub_field( 'categories' );
$picked   = ! empty( $term_ids ) && is_array( $term_ids );

$terms = get_terms(
	[
		'taxonomy'   => 'product_cat',
		'hide_empty' => false,
		'include'    => $picked ? array_map( 'intval', $term_ids ) : [],
		'parent'     => $picked ? '' : 0,
		'orderby'    => $picked ? 'include' : 'name',
	]
);

if ( is_wp_error( $terms ) || empty( $terms ) ) {
	return;
}
?>
<section class="products-categories">
	<div class="container-full">
		<div class="products-categories__grid">
			<?php
			foreach ( $terms as $term ) {
				get_template_part(
					'template-parts/components/category-card',
					null,
					[
						'image' => yka_term_image( (int) $term->term_id ),
						'title' => $term->name,
						'href'  => get_term_link( $term ),
					]
				);
			}
			?>
		</div>
	</div>
</section>
