<?php
/**
 * ACF bootstrap — register JSON paths, options pages, and load helpers.
 *
 * Field groups are defined in acf-json/ (JSON is the source of truth — never
 * call acf_add_local_field_group(), never author fields in the admin UI).
 * See .claude/acf-flexible-content.md.
 *
 * @package ykagro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter(
	'acf/settings/save_json',
	function () {
		return YKA_DIR . '/acf-json';
	}
);

add_filter(
	'acf/settings/load_json',
	function ( $paths ) {
		unset( $paths[0] );
		$paths[] = YKA_DIR . '/acf-json';

		return $paths;
	}
);

add_action(
	'acf/init',
	function () {
		if ( ! function_exists( 'acf_add_options_page' ) ) {
			return;
		}

		acf_add_options_page(
			[
				'page_title' => __( 'Site Settings', 'ykagro' ),
				'menu_title' => __( 'Site Settings', 'ykagro' ),
				'menu_slug'  => 'yka-site-settings',
				'capability' => 'edit_posts',
				'redirect'   => false,
				'icon_url'   => 'dashicons-admin-generic',
				'position'   => 59,
			]
		);
	}
);

require_once YKA_DIR . '/includes/acf/acf-helpers.php';
