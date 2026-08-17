<?php
/**
 * Page builder layout: lean cream card with a slanted cut and a contact form.
 *
 * @package ykagro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title   = (string) get_sub_field( 'title' );
$anchor  = sanitize_title( (string) get_sub_field( 'anchor' ) );
$form_id = (int) get_sub_field( 'form_id' );
?>
<section class="coop-form"<?php echo ! empty( $anchor ) ? ' id="' . esc_attr( $anchor ) . '"' : ''; ?> aria-label="<?php esc_attr_e( 'Форма співпраці', 'ykagro' ); ?>">
	<div class="container">
		<div class="coop-form__card">
			<?php if ( ! empty( $title ) ) { ?>
				<h2 class="coop-form__title h2 clr-text"><?php echo yka_heading( $title ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- yka_heading returns escaped HTML. ?></h2>
			<?php } ?>
			<?php get_template_part( 'template-parts/form-block', null, [ 'form_id' => $form_id ] ); ?>
		</div>
	</div>
</section>
