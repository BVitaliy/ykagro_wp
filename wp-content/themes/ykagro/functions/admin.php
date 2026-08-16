<?php
/**
 * Admin-side tweaks.
 *
 * @package ykagro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class YKA_Admin {

	public function __construct() {
		add_filter( 'upload_mimes', [ $this, 'allow_svg_upload' ] );
		add_filter( 'wp_check_filetype_and_ext', [ $this, 'fix_svg_filetype' ], 10, 4 );
		add_action( 'admin_head', [ $this, 'svg_thumbnail_css' ] );
		add_filter( 'wpcf7_autop_or_not', '__return_false' );
	}

	/**
	 * SVG uploads — administrators only.
	 *
	 * SVG can carry scripts, so this stays behind `unfiltered_html`, which
	 * editors and authors do not have on a standard install.
	 *
	 * @param array $mimes Allowed mime types.
	 */
	public function allow_svg_upload( array $mimes ): array {
		if ( ! current_user_can( 'unfiltered_html' ) ) {
			return $mimes;
		}

		$mimes['svg'] = 'image/svg+xml';

		return $mimes;
	}

	/**
	 * WordPress' real-mime sniffing rejects SVG without this correction.
	 *
	 * @param array  $data     File data.
	 * @param string $file     Full path to the file.
	 * @param string $filename The name of the file.
	 * @param array  $mimes    Allowed mimes.
	 */
	public function fix_svg_filetype( array $data, $file, $filename, $mimes ): array {
		if ( ! current_user_can( 'unfiltered_html' ) ) {
			return $data;
		}

		if ( str_ends_with( strtolower( (string) $filename ), '.svg' ) ) {
			$data['ext']  = 'svg';
			$data['type'] = 'image/svg+xml';
		}

		return $data;
	}

	/**
	 * Without a width, SVGs render as a sliver in the media grid.
	 */
	public function svg_thumbnail_css(): void {
		echo '<style>.attachment-266x266, .thumbnail img[src$=".svg"] { width: 100% !important; height: auto !important; }</style>';
	}
}

new YKA_Admin();
