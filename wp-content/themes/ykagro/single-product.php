<?php
/**
 * Single product.
 *
 * No hero banner here — logo + breadcrumbs only, and the trail skips the
 * category level, matching the markup.
 *
 * @package ykagro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

the_post();

$yka_specs         = (string) get_field( 'specs' );
$yka_products_page = get_page_by_path( 'products' );
$yka_trail         = [];

if ( $yka_products_page ) {
	$yka_trail[] = [
		'label' => get_the_title( $yka_products_page ),
		'href'  => get_permalink( $yka_products_page ),
	];
}

$yka_trail[] = [ 'label' => get_the_title() ];
?>

<main>
	<?php get_template_part( 'includes/scroll-line', null, [ 'class' => 'scroll-line--blog' ] ); ?>

	<div class="product-page">
		<?php get_template_part( 'template-parts/components/page-head', null, [ 'items' => $yka_trail ] ); ?>

		<?php get_template_part( 'template-parts/single/product-hero' ); ?>

		<?php if ( ! empty( $yka_specs ) ) { ?>
			<div class="spacer-xl"></div>
			<section class="product-specs">
				<div class="container">
					<div class="product-specs__content article-content">
						<?php echo wp_kses_post( yka_strip_table_attrs( $yka_specs ) ); ?>
					</div>
				</div>
			</section>
		<?php } ?>

		<?php
		$yka_faq_items = get_field( 'faq_items', 'options' );

		if ( is_array( $yka_faq_items ) && ! empty( $yka_faq_items ) ) {
			?>
			<div class="spacer-xl"></div>
			<?php get_template_part( 'template-parts/faq' ); ?>
			<?php
		}
		?>

		<div class="spacer-xl"></div>
		<?php get_template_part( 'template-parts/single/product-related' ); ?>

		<div class="spacer-xl"></div>
	</div>
</main>

<?php
get_footer();
