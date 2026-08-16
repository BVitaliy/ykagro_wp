<?php
/**
 * Page builder layout: articles — heading + CTA + latest posts.
 * Grid on desktop, Swiper slider on mobile.
 *
 * @package ykagro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title = (string) get_sub_field( 'title' );
$cta   = get_sub_field( 'cta' );
$count = (int) get_sub_field( 'count' );

if ( $count < 1 ) {
	$count = 3;
}

$post_ids = get_posts(
	[
		'post_type'      => 'post',
		'post_status'    => 'publish',
		'posts_per_page' => $count,
		'orderby'        => 'date',
		'order'          => 'DESC',
		'fields'         => 'ids',
	]
);

if ( empty( $post_ids ) ) {
	return;
}
?>
<section class="home-articles">
	<div class="container">
		<div class="home-articles__head">
			<?php if ( ! empty( $title ) ) { ?>
				<h2 class="home-articles__title h2 clr-black"><?php echo esc_html( $title ); ?></h2>
			<?php } ?>
			<?php yka_cta( $cta, 'btn btn--soft home-articles__cta' ); ?>
		</div>

		<div class="home-articles__slider swiper js-articles-slider">
			<div class="home-articles__grid swiper-wrapper">
				<?php
				foreach ( $post_ids as $post_id ) {
					?>
					<div class="home-articles__slide swiper-slide">
						<?php get_template_part( 'template-parts/components/article-card', null, [ 'post_id' => $post_id ] ); ?>
					</div>
					<?php
				}
				?>
			</div>
			<div class="home-articles__pagination swiper-pagination"></div>
		</div>
	</div>
</section>
