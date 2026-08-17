<?php
/**
 * Page builder layout: FAQ.
 *
 * Thin wrapper — the section itself is shared with the single templates, see
 * template-parts/faq.php.
 *
 * @package ykagro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_template_part( 'template-parts/faq', null, [ 'link' => get_sub_field( 'link' ) ] );
