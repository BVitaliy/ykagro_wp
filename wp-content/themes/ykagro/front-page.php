<?php
/**
 * Front page — rendered entirely by the page builder.
 *
 * @package ykagro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main>
	<?php get_template_part( 'includes/scroll-line' ); ?>
	<?php require YKA_DIR . '/includes/page-builder.php'; ?>
	<div class="spacer-xl"></div>
</main>

<?php
get_footer();
