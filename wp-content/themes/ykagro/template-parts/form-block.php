<?php
/**
 * Contact form block — renders a Contact Form 7 form.
 *
 * The static markup shipped a hand-written form; here CF7 owns the fields so
 * submissions are stored (CFDB7) and mailed. The form's own CF7 template must
 * reproduce the markup classes — see the theme README section on forms.
 *
 * @param array $args {
 *     @type int  $form_id CF7 post ID. Falls back to Site Settings.
 *     @type bool $file    Prefer the vacancy form (with a CV upload field).
 * }
 *
 * @package ykagro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$form_id = ! empty( $args['form_id'] ) ? (int) $args['form_id'] : 0;
$wants_file = ! empty( $args['file'] );

if ( ! $form_id && function_exists( 'get_field' ) ) {
	$option_key = $wants_file ? 'vacancy_form_id' : 'default_form_id';
	$form_id    = (int) get_field( $option_key, 'options' );
}

if ( ! $form_id || ! shortcode_exists( 'contact-form-7' ) ) {
	return;
}

// CF7 renders <form class="wpcf7-form">, so the design's own class has to be
// passed in — .form-block is what supplies the flex layout and the 12px gap
// between fields. Without it the inputs sit flush against each other.
echo do_shortcode(
	sprintf(
		'[contact-form-7 id="%d" html_class="form-block js-appointment-form"]',
		$form_id
	)
);
