<?php
/**
 * Page builder layout: statement — large centred lead text that fills on scroll.
 *
 * @package ykagro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$text = (string) get_sub_field( 'text' );

if ( empty( $text ) ) {
	return;
}
?>
<section class="home-statement">
	<div class="container">
		<p class="home-statement__text h1 js-statement-fill">
			<?php echo esc_html( $text ); ?>
		</p>
	</div>
</section>
