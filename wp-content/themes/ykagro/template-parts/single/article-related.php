<?php
/**
 * "Схожі статті" — the home articles block reused at the bottom of an article.
 * Grid on desktop, Swiper on mobile.
 *
 * @package ykagro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_id = get_the_ID();

$related = get_posts(
	[
		'post_type'      => 'post',
		'post_status'    => 'publish',
		'posts_per_page' => 3,
		'post__not_in'   => [ $post_id ],
		'orderby'        => 'date',
		'order'          => 'DESC',
		'fields'         => 'ids',
	]
);

if ( empty( $related ) ) {
	return;
}

$blog_id = (int) get_option( 'page_for_posts' );
?>
<section class="home-articles">
	<div class="container">
		<div class="home-articles__head">
			<h2 class="home-articles__title h2 clr-black"><?php esc_html_e( 'Схожі статті', 'ykagro' ); ?></h2>
			<?php if ( $blog_id ) { ?>
				<a href="<?php echo esc_url( get_permalink( $blog_id ) ); ?>" class="btn btn--soft home-articles__cta">
					<span class="btn__icon"><?php yka_icon( 'icons/arrow-diagonal.svg' ); ?></span>
					<?php esc_html_e( 'Всі статті', 'ykagro' ); ?>
				</a>
			<?php } ?>
		</div>

		<div class="home-articles__slider swiper js-articles-slider">
			<div class="home-articles__grid swiper-wrapper">
				<?php
				foreach ( $related as $related_id ) {
					?>
					<div class="home-articles__slide swiper-slide">
						<?php get_template_part( 'template-parts/components/article-card', null, [ 'post_id' => $related_id ] ); ?>
					</div>
					<?php
				}
				?>
			</div>
			<div class="home-articles__pagination swiper-pagination"></div>
		</div>
	</div>
</section>
