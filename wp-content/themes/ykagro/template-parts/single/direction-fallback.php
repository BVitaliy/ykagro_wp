<?php
/**
 * Direction with no builder blocks yet — banner, title and the card text.
 *
 * Keeps a freshly created direction readable in the admin preview before an
 * editor has composed the page.
 *
 * @package ykagro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_id = get_the_ID();
$text    = (string) get_field( 'card_text', $post_id );
$image   = get_field( 'tile_image', $post_id );

if ( empty( $image['ID'] ) && has_post_thumbnail( $post_id ) ) {
	$image = [ 'ID' => get_post_thumbnail_id( $post_id ), 'alt' => get_the_title() ];
}
?>
<?php if ( ! empty( $image['ID'] ) ) { ?>
	<section class="page-hero page-hero--has-title-panel">
		<?php get_template_part( 'template-parts/components/banner', null, [ 'image' => $image, 'eager' => true ] ); ?>

		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="page-hero__logo" aria-label="<?php esc_attr_e( 'YKagro — на головну', 'ykagro' ); ?>">
			<?php yka_icon( 'logo.svg' ); ?>
		</a>

		<div class="page-hero__breadcrumbs">
			<?php get_template_part( 'template-parts/components/breadcrumbs', null, [ 'items' => yka_trail() ] ); ?>
		</div>

		<div class="page-hero__title-panel">
			<h1 class="page-hero__title h3"><?php the_title(); ?></h1>
		</div>
	</section>
<?php } else { ?>
	<?php get_template_part( 'template-parts/components/page-head', null, [ 'items' => yka_trail() ] ); ?>
	<div class="container">
		<h1 class="products-title h2"><?php the_title(); ?></h1>
	</div>
<?php } ?>

<?php if ( ! empty( $text ) ) { ?>
	<div class="spacer-xl"></div>
	<div class="container">
		<p class="text-lg clr-muted"><?php echo esc_html( $text ); ?></p>
	</div>
<?php } ?>
