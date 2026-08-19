<?php
/**
 * Template Name: Конструктор сторінки
 *
 * Any page that should be assembled from page-builder blocks.
 *
 * @package ykagro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<?php $yka_wrapper = yka_page_wrapper_class(); ?>
<main<?php echo yka_main_class() ? ' class="' . esc_attr( yka_main_class() ) . '"' : ''; ?>>
	<?php get_template_part( 'includes/scroll-line' ); ?>

	<?php if ( ! empty( $yka_wrapper ) ) { ?>
		<div class="<?php echo esc_attr( $yka_wrapper ); ?>">
	<?php } ?>

	<?php require YKA_DIR . '/includes/page-builder.php'; ?>
	<div class="spacer-xl"></div>

	<?php if ( ! empty( $yka_wrapper ) ) { ?>
		</div>
	<?php } ?>
</main>

<?php
get_footer();
