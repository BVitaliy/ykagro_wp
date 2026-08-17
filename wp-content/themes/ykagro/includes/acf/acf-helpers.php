<?php
/**
 * ACF helper functions shared across all field groups.
 *
 * Note: this file deliberately contains no PHP field-array builders. Field
 * definitions live in acf-json/ only — see .claude/acf-flexible-content.md.
 *
 * @package ykagro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Converts lightweight markup in section headings to safe HTML.
 *
 * Headings come from ACF textareas so editors can control line breaks, which the
 * title reveal animation (app-titles.js) splits on.
 *
 * Markers:
 *   _word_   → <em>word</em>
 *   newline  → <br>
 *
 * @param string $text Raw value from the ACF heading textarea.
 * @return string Safe HTML (only br and em survive).
 */
function yka_heading( string $text ): string {
	$text = htmlspecialchars( $text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' );
	$text = preg_replace( '/_(.+?)_/u', '<em>$1</em>', $text );

	return nl2br( $text );
}

/**
 * Renders a CTA group field (label + link) as an anchor.
 *
 * Every `.btn` in the design carries the diagonal-arrow icon, so it is part of
 * the component rather than something each caller remembers to add.
 *
 * @param array|null $cta   ACF group with 'label' and 'link' sub-fields.
 * @param string     $class Anchor class.
 * @param bool       $icon  Render the arrow icon inside the button.
 */
function yka_cta( ?array $cta, string $class = 'btn', bool $icon = true ): void {
	if ( empty( $cta['link']['url'] ) ) {
		return;
	}

	$label = ! empty( $cta['label'] ) ? $cta['label'] : ( $cta['link']['title'] ?? '' );

	if ( empty( $label ) ) {
		return;
	}

	$target = ! empty( $cta['link']['target'] ) ? ' target="' . esc_attr( $cta['link']['target'] ) . '" rel="noopener"' : '';
	?>
	<a class="<?php echo esc_attr( $class ); ?>" href="<?php echo esc_url( $cta['link']['url'] ); ?>"<?php echo $target; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above. ?>>
		<?php if ( $icon ) { ?>
			<span class="btn__icon"><?php yka_icon( 'icons/arrow-diagonal.svg' ); ?></span>
		<?php } ?>
		<?php echo esc_html( $label ); ?>
	</a>
	<?php
}

/**
 * Populates a select field with the site's Contact Form 7 forms.
 *
 * Hook it per field key, e.g.:
 *   add_filter( 'acf/load_field/key=field_XXXX', 'yka_acf_load_cf7_choices' );
 *
 * @param array $field ACF field array.
 */
function yka_acf_load_cf7_choices( array $field ): array {
	$field['choices'] = [ '' => __( '— Select a form —', 'ykagro' ) ];

	if ( ! post_type_exists( 'wpcf7_contact_form' ) ) {
		return $field;
	}

	$forms = get_posts(
		[
			'post_type'      => 'wpcf7_contact_form',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'title',
			'order'          => 'ASC',
		]
	);

	foreach ( $forms as $form ) {
		$field['choices'][ $form->ID ] = $form->post_title;
	}

	return $field;
}

// Site Settings → Forms: both selects list the site's CF7 forms.
add_filter( 'acf/load_field/key=field_525fa2e98f43b449', 'yka_acf_load_cf7_choices' );
add_filter( 'acf/load_field/key=field_e40acca166d9d5d1', 'yka_acf_load_cf7_choices' );
// Page builder → form blocks.
add_filter( 'acf/load_field/key=field_5237e6bbfe3e871e', 'yka_acf_load_cf7_choices' );
// Page builder → contact block, form variant.
add_filter( 'acf/load_field/key=field_13df98338bc2c206', 'yka_acf_load_cf7_choices' );

/**
 * Strips presentational inline attributes from TinyMCE tables so wysiwyg tables
 * are styled purely by the stylesheet.
 *
 * @param string $html Raw HTML from a wysiwyg field.
 */
function yka_strip_table_attrs( string $html ): string {
	return preg_replace_callback(
		'/<(table|thead|tbody|tr|td|th)(\s[^>]*)?>/i',
		static function ( array $m ): string {
			if ( empty( $m[2] ) ) {
				return '<' . $m[1] . '>';
			}

			$attrs = $m[2];

			foreach ( [ 'style', 'border', 'cellpadding', 'cellspacing', 'width', 'height' ] as $attr ) {
				$attrs = preg_replace( '/\s+' . $attr . '=["\'][^"\']*["\']|\s+' . $attr . '=\S+/i', '', $attrs );
			}

			return '<' . $m[1] . $attrs . '>';
		},
		$html
	);
}
