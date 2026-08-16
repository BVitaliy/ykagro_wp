<?php
/**
 * Page builder layout: directions — pinned section, background photo, left
 * heading, right column of direction cards that scroll through.
 *
 * @package ykagro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title  = (string) get_sub_field( 'title' );
$lead   = (string) get_sub_field( 'lead' );
$bg     = get_sub_field( 'bg' );
$bg_mob = get_sub_field( 'bg_mob' );
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
<section class="home-directions js-directions">
	<div class="home-directions__pin js-directions-pin">
		<div class="home-directions__bg js-directions-bg">
			<picture>
				<?php if ( ! empty( $bg_mob['ID'] ) ) { ?>
					<source srcset="<?php echo esc_url( wp_get_attachment_image_url( (int) $bg_mob['ID'], 'large' ) ); ?>" media="(max-width: 767.98px)">
				<?php } ?>
				<?php
				if ( ! empty( $bg['ID'] ) ) {
					echo wp_get_attachment_image(
						(int) $bg['ID'],
						'full',
						false,
						[ 'alt' => '', 'loading' => 'lazy', 'decoding' => 'async' ]
					);
				}
				?>
			</picture>
			<div class="home-directions__bg-overlay"></div>
		</div>

		<div class="home-directions__inner container">
			<div class="home-directions__head">
				<?php if ( ! empty( $title ) ) { ?>
					<h2 class="home-directions__title clr-white"><?php echo yka_heading( $title ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- yka_heading returns escaped HTML. ?></h2>
				<?php } ?>
				<?php if ( ! empty( $lead ) ) { ?>
					<p class="home-directions__lead text-big clr-white"><?php echo esc_html( $lead ); ?></p>
				<?php } ?>
			</div>

			<div class="home-directions__window js-directions-window">
				<div class="home-directions__track js-directions-track">
					<?php
					foreach ( $direction_ids as $index => $direction_id ) {
						get_template_part(
							'template-parts/components/direction-card',
							null,
							[
								'post_id' => $direction_id,
								'num'     => str_pad( (string) ( $index + 1 ), 2, '0', STR_PAD_LEFT ),
								'total'   => $total,
							]
						);
					}
					?>
				</div>
			</div>
		</div>
	</div>
</section>
