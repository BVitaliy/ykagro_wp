<?php
/**
 * Page builder layout: hero banner with logo, H1 and a floating promo card.
 *
 * @package ykagro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title = (string) get_sub_field( 'title' );
$promo = get_sub_field( 'promo' );
?>
<section class="page-hero home-hero">
	<?php
	get_template_part(
		'template-parts/components/banner',
		null,
		[
			'image'      => get_sub_field( 'image' ),
			'image_mob'  => get_sub_field( 'image_mob' ),
			'video'      => (string) get_sub_field( 'video' ),
			'video_mob'  => (string) get_sub_field( 'video_mob' ),
			'poster'     => get_sub_field( 'image' ),
			'poster_mob' => get_sub_field( 'image_mob' ),
			'eager'      => true,
		]
	);
	?>

	<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="page-hero__logo" aria-label="<?php esc_attr_e( 'YKagro — на головну', 'ykagro' ); ?>">
		<?php yka_icon( 'logo.svg' ); ?>
	</a>

	<?php if ( ! empty( $title ) ) { ?>
		<h1 class="home-hero__title clr-white"><?php echo yka_heading( $title ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- yka_heading returns escaped HTML. ?></h1>
	<?php } ?>

	<?php if ( ! empty( $promo['title'] ) ) { ?>
		<div class="home-hero__card">
			<?php
			get_template_part(
				'template-parts/components/product-card',
				null,
				[
					'image' => $promo['image'] ?? null,
					'title' => $promo['title'],
					'href'  => $promo['link']['url'] ?? '',
				]
			);
			?>
		</div>
	<?php } ?>
</section>
