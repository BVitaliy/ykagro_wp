<?php
/**
 * "Більше напрямів нашої роботи" — slider of the other directions.
 *
 * @package ykagro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_id = get_the_ID();

$related = get_posts(
	[
		'post_type'      => 'direction',
		'post_status'    => 'publish',
		'posts_per_page' => 8,
		'post__not_in'   => [ $post_id ],
		'orderby'        => [ 'menu_order' => 'ASC', 'date' => 'DESC' ],
		'fields'         => 'ids',
	]
);

if ( empty( $related ) ) {
	return;
}

$list_page = get_page_by_path( 'directions' );
$total     = str_pad( (string) count( $related ), 2, '0', STR_PAD_LEFT );
?>
<section class="product-related">
	<div class="container product-related__head">
		<h2 class="product-related__title h2 clr-black"><?php esc_html_e( 'Більше напрямів нашої роботи', 'ykagro' ); ?></h2>
		<?php if ( $list_page ) { ?>
			<a href="<?php echo esc_url( get_permalink( $list_page ) ); ?>" class="btn product-related__cta">
				<span class="btn__icon"><?php yka_icon( 'icons/arrow-diagonal.svg' ); ?></span>
				<?php esc_html_e( 'Усі напрями', 'ykagro' ); ?>
			</a>
		<?php } ?>
	</div>

	<div class="container-full">
		<div class="product-related__slider swiper js-products-slider">
			<div class="swiper-wrapper">
				<?php
				foreach ( $related as $index => $related_id ) {
					?>
					<div class="swiper-slide product-related__slide">
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
			<div class="product-related__pagination swiper-pagination"></div>
		</div>
	</div>
</section>
