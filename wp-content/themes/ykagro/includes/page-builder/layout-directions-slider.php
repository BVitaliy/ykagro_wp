<?php
/**
 * Page builder layout: "Більше напрямів нашої роботи".
 *
 * The same tiles as the listing page, but as a 2-up slider with pagination.
 * On a direction page the current entry is dropped from the list.
 *
 * @package ykagro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title = (string) get_sub_field( 'title' );
$cta   = get_sub_field( 'cta' );

$current = is_singular( 'direction' ) ? get_the_ID() : 0;

$related = get_posts(
	[
		'post_type'      => 'direction',
		'post_status'    => 'publish',
		'posts_per_page' => 8,
		'post__not_in'   => $current ? [ $current ] : [],
		'orderby'        => [ 'menu_order' => 'ASC', 'date' => 'DESC' ],
		'fields'         => 'ids',
	]
);

if ( empty( $related ) ) {
	return;
}

if ( empty( $title ) ) {
	$title = __( 'Більше напрямів нашої роботи', 'ykagro' );
}

$total = str_pad( (string) count( $related ), 2, '0', STR_PAD_LEFT );
?>
<section class="directions-slider">
	<div class="container">
		<div class="directions-slider__head">
			<h2 class="directions-slider__title h2 clr-black"><?php echo esc_html( $title ); ?></h2>
			<?php yka_cta( $cta, 'btn directions-slider__cta' ); ?>
		</div>

		<div class="directions-slider__slider swiper js-directions-slider">
			<div class="swiper-wrapper">
				<?php
				foreach ( $related as $index => $related_id ) {
					?>
					<div class="swiper-slide directions-slider__slide">
						<?php
						get_template_part(
							'template-parts/components/direction-tile',
							null,
							[
								'post_id' => $related_id,
								'num'     => str_pad( (string) ( $index + 1 ), 2, '0', STR_PAD_LEFT ),
								'total'   => $total,
							]
						);
						?>
					</div>
					<?php
				}
				?>
			</div>
			<div class="directions-slider__pagination swiper-pagination"></div>
		</div>
	</div>
</section>
