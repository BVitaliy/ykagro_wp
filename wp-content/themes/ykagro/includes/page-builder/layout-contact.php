<?php
/**
 * Page builder layout: contact — cream panel with a photo collage around a
 * central card. Two variants: text + CTA, or an inline contact form.
 *
 * The collage is fixed decoration in the design, not editable content, so the
 * five images are pulled from the theme's img/ directory rather than ACF.
 *
 * @package ykagro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$variant   = (string) get_sub_field( 'variant' );
$is_form   = 'form' === $variant;
$tag       = (string) get_sub_field( 'tag' );
$title     = (string) get_sub_field( 'title' );
$text      = (string) get_sub_field( 'text' );
$cta_label = (string) get_sub_field( 'cta_label' );
$form_id   = (int) get_sub_field( 'form_id' );
$anchor    = sanitize_title( (string) get_sub_field( 'anchor' ) );

if ( $is_form && ! $form_id ) {
	$form_id = (int) get_field( 'default_form_id', 'options' );
}

// pos => [width, height], matching the per-position aspect ratio in _contact.scss.
$collage = [
	'intro-broiler.jpg'  => [ 'tl', 296, 357 ],
	'intro-chicks.jpg'   => [ 'tc', 341, 237 ],
	'contact-worker.png' => [ 'r', 296, 416 ],
	'hero-poster.jpg'    => [ 'bl', 388, 398 ],
	'category-1.jpg'     => [ 'br', 392, 286 ],
];
?>
<section class="home-contact<?php echo $is_form ? ' home-contact--form' : ''; ?>"<?php echo ! empty( $anchor ) ? ' id="' . esc_attr( $anchor ) . '"' : ''; ?><?php echo $is_form ? ' aria-label="' . esc_attr__( 'Форма зворотного зв\'язку', 'ykagro' ) . '"' : ''; ?>>
	<div class="home-contact__panel">
		<?php
		foreach ( $collage as $file => $meta ) {
			list( $pos, $width, $height ) = $meta;
			?>
			<div class="home-contact__photo home-contact__photo--<?php echo esc_attr( $pos ); ?>">
				<?php
				yka_picture(
					[
						'src'    => 'home/' . $file,
						'alt'    => '',
						'width'  => $width,
						'height' => $height,
					]
				);
				?>
			</div>
			<?php
		}
		?>

		<div class="home-contact__card">
			<?php if ( ! $is_form && ! empty( $tag ) ) { ?>
				<span class="tag home-contact__tag"><?php echo esc_html( $tag ); ?></span>
			<?php } ?>

			<?php if ( ! empty( $title ) ) { ?>
				<h2 class="home-contact__title<?php echo $is_form ? ' h4' : ' clr-black'; ?>"><?php echo yka_heading( $title ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- yka_heading returns escaped HTML. ?></h2>
			<?php } ?>

			<?php if ( $is_form ) { ?>
				<?php get_template_part( 'template-parts/form-block', null, [ 'form_id' => $form_id ] ); ?>
			<?php } else { ?>
				<?php if ( ! empty( $text ) ) { ?>
					<p class="home-contact__text text-lg clr-gray"><?php echo esc_html( $text ); ?></p>
				<?php } ?>
				<?php if ( ! empty( $cta_label ) ) { ?>
					<a href="#" class="btn home-contact__cta" data-modal-open="appointment">
						<span class="btn__icon"><?php yka_icon( 'icons/arrow-diagonal.svg' ); ?></span>
						<?php echo esc_html( $cta_label ); ?>
					</a>
				<?php } ?>
			<?php } ?>
		</div>
	</div>
</section>
