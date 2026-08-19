<?php
/**
 * Blog listing.
 *
 * The hero banner and intro copy come from the blog page's own ACF fields, so an
 * editor manages them like any other page.
 *
 * @package ykagro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$yka_blog_id = (int) get_option( 'page_for_posts' );
$yka_banner  = $yka_blog_id ? get_field( 'blog_banner', $yka_blog_id ) : null;
$yka_banner_m = $yka_blog_id ? get_field( 'blog_banner_mob', $yka_blog_id ) : null;
$yka_intro   = $yka_blog_id ? (string) get_field( 'blog_intro', $yka_blog_id ) : '';
$yka_title   = $yka_blog_id ? get_the_title( $yka_blog_id ) : __( 'Блог', 'ykagro' );

if ( empty( $yka_intro ) ) {
	$yka_intro = $yka_title;
}
?>

<main>
	<?php get_template_part( 'includes/scroll-line', null, [ 'class' => 'scroll-line--blog' ] ); ?>

	<div class="blog-page">
		<?php if ( ! empty( $yka_banner['ID'] ) ) { ?>
			<section class="page-hero page-hero--has-title-panel">
				<?php
				get_template_part(
					'template-parts/components/banner',
					null,
					[
						'image'     => $yka_banner,
						'image_mob' => $yka_banner_m,
						'eager'     => true,
					]
				);
				?>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="page-hero__logo" aria-label="<?php esc_attr_e( 'YKagro — на головну', 'ykagro' ); ?>">
					<?php yka_icon( 'logo.svg' ); ?>
				</a>
				<div class="page-hero__breadcrumbs">
					<?php get_template_part( 'template-parts/components/breadcrumbs', null, [ 'items' => [ [ 'label' => $yka_title ] ] ] ); ?>
				</div>
				<div class="page-hero__title-panel">
					<h1 class="page-hero__title h3"><?php echo yka_heading( $yka_intro ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- yka_heading returns escaped HTML. ?></h1>
				</div>
			</section>
		<?php } else { ?>
			<?php get_template_part( 'template-parts/components/page-head', null, [ 'items' => [ [ 'label' => $yka_title ] ] ] ); ?>
			<div class="container">
				<h1 class="h2 clr-black"><?php echo esc_html( $yka_title ); ?></h1>
			</div>
		<?php } ?>

		<div class="spacer-xl"></div>

		<?php if ( have_posts() ) { ?>
			<section class="blog-list" aria-label="<?php esc_attr_e( 'Статті блогу', 'ykagro' ); ?>">
				<div class="blog-list__inner">
					<div class="blog-list__grid">
						<?php
						while ( have_posts() ) {
							the_post();
							get_template_part( 'template-parts/components/article-card' );
						}
						?>
					</div>

					<?php
					get_template_part(
						'template-parts/components/pagination',
						null,
						[
							'more_text' => __( 'Показати більше', 'ykagro' ),
							'grid'      => '.blog-list__grid',
						]
					);
					?>
				</div>
			</section>
		<?php } else { ?>
			<div class="container">
				<p class="text-lg clr-muted"><?php esc_html_e( 'Публікацій ще немає.', 'ykagro' ); ?></p>
			</div>
		<?php } ?>

		<div class="spacer-xl"></div>
	</div>
</main>

<?php
get_footer();
