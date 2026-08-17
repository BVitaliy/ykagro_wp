<?php
/**
 * Page builder layout: directions as a two-column grid of photo tiles.
 * An odd last tile keeps the half-width column rather than stretching.
 *
 * @package ykagro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$picked = get_sub_field( 'items' );

if ( ! empty( $picked ) && is_array( $picked ) ) {
	$direction_ids = array_map( 'intval', $picked );
} else {
	$direction_ids = get_posts(
		[
			'post_type'      => 'direction',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => [ 'menu_order' => 'ASC', 'date' => 'DESC' ],
			'fields'         => 'ids',
		]
	);
}

if ( empty( $direction_ids ) ) {
	return;
}

$total = str_pad( (string) count( $direction_ids ), 2, '0', STR_PAD_LEFT );
?>
<section class="directions-list">
	<div class="container">
		<div class="directions-list__grid">
			<?php
			foreach ( $direction_ids as $index => $direction_id ) {
				get_template_part(
					'template-parts/components/direction-tile',
					null,
					[
						'post_id' => $direction_id,
						'num'     => str_pad( (string) ( $index + 1 ), 2, '0', STR_PAD_LEFT ),
						'total'   => $total,
						// The first row is above the fold on this page.
						'eager'   => $index < 2,
					]
				);
			}
			?>
		</div>
	</div>
</section>
