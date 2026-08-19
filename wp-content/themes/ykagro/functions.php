<?php
/**
 * YKagro theme bootstrap.
 *
 * This file only defines constants and pulls in feature files.
 * Put logic in functions/ or includes/, never here.
 *
 * @package ykagro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'YKA_URI', get_template_directory_uri() );
define( 'YKA_DIR', get_template_directory() );
define( 'YKA_HOME_URL', home_url( '/' ) );

/**
 * Asset cache buster. Bump on every deploy that changes css/ or js/.
 */
define( 'YKA_VER', '1.0.3' );

/**
 * Base URLs the content was authored on. ACF link fields store absolute URLs,
 * so a link picked in the admin keeps the host it was created with. Anything
 * matching one of these bases is rebased onto home_url() at output time — see
 * yka_rebase_url(). Empty the array once the database has been migrated with a
 * serialization-aware search-replace.
 */
define( 'YKA_LEGACY_URLS', [ 'http://localhost:8888/ykagro_wp' ] );

require_once YKA_DIR . '/functions/class-svg-sanitizer.php';
require_once YKA_DIR . '/functions/helpers.php';
require_once YKA_DIR . '/functions/class-nav-walker.php';
require_once YKA_DIR . '/functions/post-types.php';
require_once YKA_DIR . '/functions/init.php';
require_once YKA_DIR . '/functions/admin.php';
require_once YKA_DIR . '/functions/seo.php';

// ACF 6.8.7+ rejects SVG in image fields; this scopes an exemption to icon fields.
if ( class_exists( 'ACF' ) ) {
	require_once YKA_DIR . '/functions/class-svg-acf.php';
}

// ACF field groups live in acf-json/ — see .claude/acf-flexible-content.md.
if ( class_exists( 'ACF' ) ) {
	require_once YKA_DIR . '/includes/acf/register-acf-fields.php';
}
