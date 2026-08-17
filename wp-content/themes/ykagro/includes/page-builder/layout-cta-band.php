<?php
/**
 * Page builder layout: cream CTA panel with the hand-drawn hen.
 *
 * @package ykagro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title     = (string) get_sub_field( 'title' );
$cta_label = (string) get_sub_field( 'cta_label' );

if ( empty( $title ) ) {
	return;
}
?>
<section class="resp-cta">
	<div class="container">
		<div class="resp-cta__panel">
			<div class="resp-cta__body">
				<h2 class="resp-cta__title h3 clr-text"><?php echo yka_heading( $title ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- yka_heading returns escaped HTML. ?></h2>
				<?php if ( ! empty( $cta_label ) ) { ?>
					<a href="#" class="btn resp-cta__btn" data-modal-open="appointment">
						<span class="btn__icon"><?php yka_icon( 'icons/arrow-diagonal.svg' ); ?></span>
						<?php echo esc_html( $cta_label ); ?>
					</a>
				<?php } ?>
			</div>
			<div class="resp-cta__art" aria-hidden="true">
				<?php yka_icon( 'responsibility/cta-hen.svg' ); ?>
			</div>
		</div>
	</div>
</section>
