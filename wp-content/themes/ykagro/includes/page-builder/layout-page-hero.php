<?php
/**
 * Page builder layout: inner-page hero — banner, logo, breadcrumbs, H1.
 *
 * Leaving the image empty is a supported layout, not a fallback: several pages in
 * the markup deliberately have no hero banner (products, for one — "Logo +
 * breadcrumbs, no hero banner"). In that case the block renders page-head plus
 * the listing heading, which carries its own class and max-width.
 *
 * @package ykagro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title     = (string) get_sub_field( 'title' );
$image     = get_sub_field( 'image' );
$image_mob = get_sub_field( 'image_mob' );

if ( empty( $title ) ) {
	$title = get_the_title();
}

$trail = [ [ 'label' => get_the_title() ] ];

if ( empty( $image['ID'] ) ) {
	get_template_part( 'template-parts/components/page-head', null, [ 'items' => $trail ] );

	if ( ! empty( $title ) ) {
		?>
		<div class="container">
			<h1 class="products-title h2"><?php echo yka_heading( $title ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- yka_heading returns escaped HTML. ?></h1>
		</div>
		<?php
	}

	return;
}
?>
<section class="page-hero page-hero--has-title-panel">
	<?php
	get_template_part(
		'template-parts/components/banner',
		null,
		[
			'image'     => $image,
			'image_mob' => $image_mob,
			'eager'     => true,
		]
	);
	?>

	<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="page-hero__logo" aria-label="<?php esc_attr_e( 'YKagro — на головну', 'ykagro' ); ?>">
		<?php yka_icon( 'logo.svg' ); ?>
	</a>

	<div class="page-hero__breadcrumbs">
		<?php get_template_part( 'template-parts/components/breadcrumbs', null, [ 'items' => $trail ] ); ?>
	</div>

	<?php // The heading sits in its own white panel, not over the photo. ?>
	<div class="page-hero__title-panel">
		<h1 class="page-hero__title h3"><?php echo yka_heading( $title ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- yka_heading returns escaped HTML. ?></h1>
	</div>
</section>
