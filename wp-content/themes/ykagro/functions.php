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
define( 'YKA_VER', '1.0.0' );

require_once YKA_DIR . '/functions/helpers.php';
require_once YKA_DIR . '/functions/class-nav-walker.php';
require_once YKA_DIR . '/functions/post-types.php';
require_once YKA_DIR . '/functions/init.php';
require_once YKA_DIR . '/functions/admin.php';

// ACF field groups live in acf-json/ — see .claude/acf-flexible-content.md.
if ( class_exists( 'ACF' ) ) {
	require_once YKA_DIR . '/includes/acf/register-acf-fields.php';
}
