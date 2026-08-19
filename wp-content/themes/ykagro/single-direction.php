<?php
/**
 * Single direction.
 *
 * The design gives a direction page the same range as a landing page — hero with
 * a CTA, process stages, statement, video, benefits, gallery, FAQ — so it is
 * assembled from the shared page builder rather than a fixed field set. An
 * editor composes it exactly like any other page.
 *
 * @package ykagro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

the_post();
?>

<main<?php echo yka_main_class() ? ' class="' . esc_attr( yka_main_class() ) . '"' : ''; ?>>
	<?php get_template_part( 'includes/scroll-line' ); ?>

	<div class="direction-detail-page">
		<?php
		if ( have_rows( 'page_builder' ) ) {
			require YKA_DIR . '/includes/page-builder.php';
		} else {
			// Nothing composed yet — fall back to the card fields so the entry is
			// never a blank page.
			get_template_part( 'template-parts/single/direction-fallback' );
		}
		?>

		<div class="spacer-xl"></div>
	</div>
</main>

<?php
get_footer();
