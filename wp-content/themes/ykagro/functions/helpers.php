<?php
/**
 * Template helpers.
 *
 * These are called from templates rather than hooked, so they stay plain
 * functions (see .claude/php-standards.md).
 *
 * @package ykagro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Inline an SVG from the theme's img/ directory.
 *
 * The static markup did `include 'img/icons/search.svg'`; this is the theme
 * equivalent. Files are read once per request and cached in memory, because the
 * same icon is often output many times on one page.
 *
 * @param string $path Path relative to img/, without leading slash. E.g. 'icons/search.svg'.
 */
function yka_icon( string $path ): void {
	static $cache = [];

	if ( ! isset( $cache[ $path ] ) ) {
		// Reject traversal — $path is developer-supplied, but keep the guard cheap and explicit.
		if ( str_contains( $path, '..' ) ) {
			return;
		}

		$file = YKA_DIR . '/img/' . ltrim( $path, '/' );

		if ( ! is_readable( $file ) || 'svg' !== strtolower( pathinfo( $file, PATHINFO_EXTENSION ) ) ) {
			$cache[ $path ] = '';
		} else {
			$cache[ $path ] = (string) file_get_contents( $file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- local theme asset, not a remote request.
		}
	}

	if ( ! empty( $cache[ $path ] ) ) {
		echo $cache[ $path ]; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted theme SVG file.
	}
}

/**
 * Theme image URL.
 *
 * @param string $path Path relative to img/.
 */
function yka_img( string $path ): string {
	return YKA_URI . '/img/' . ltrim( $path, '/' );
}

/**
 * Render a `<picture>` with a webp source when the sibling file exists.
 *
 * Mirrors the markup convention: webp first, original as fallback, optional
 * `-mob` crop for narrow viewports.
 *
 * @param array $args {
 *     @type string $src      Required. Path relative to img/, e.g. 'home/hero.jpg'.
 *     @type string $mob      Optional. Mobile crop relative to img/.
 *     @type string $alt      Optional. Alt text.
 *     @type bool   $eager    Optional. Above-the-fold image — eager + high priority.
 *     @type string $class    Optional. Class for the <img>.
 *     @type int    $width    Optional.
 *     @type int    $height   Optional.
 * }
 */
function yka_picture( array $args ): void {
	$src = $args['src'] ?? '';

	if ( empty( $src ) ) {
		return;
	}

	$mob    = $args['mob'] ?? '';
	$alt    = $args['alt'] ?? '';
	$eager  = ! empty( $args['eager'] );
	$class  = $args['class'] ?? '';
	$width  = $args['width'] ?? 0;
	$height = $args['height'] ?? 0;

	$webp     = preg_replace( '/\.(jpe?g|png)$/i', '.webp', $src );
	$mob_webp = ! empty( $mob ) ? preg_replace( '/\.(jpe?g|png)$/i', '.webp', $mob ) : '';

	$has_webp     = ! empty( $webp ) && is_readable( YKA_DIR . '/img/' . $webp );
	$has_mob      = ! empty( $mob ) && is_readable( YKA_DIR . '/img/' . $mob );
	$has_mob_webp = ! empty( $mob_webp ) && is_readable( YKA_DIR . '/img/' . $mob_webp );

	$desktop_media = $has_mob ? ' media="(min-width:768px)"' : '';
	?>
	<picture>
		<?php if ( $has_webp ) { ?>
			<source srcset="<?php echo esc_url( yka_img( $webp ) ); ?>" type="image/webp"<?php echo $desktop_media; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- hardcoded media query. ?>>
		<?php } ?>
		<?php if ( $has_mob_webp ) { ?>
			<source srcset="<?php echo esc_url( yka_img( $mob_webp ) ); ?>" type="image/webp" media="(max-width:767px)">
		<?php } ?>
		<?php if ( $has_mob ) { ?>
			<source srcset="<?php echo esc_url( yka_img( $mob ) ); ?>" media="(max-width:767px)">
		<?php } ?>
		<img
			src="<?php echo esc_url( yka_img( $src ) ); ?>"
			alt="<?php echo esc_attr( $alt ); ?>"
			<?php if ( ! empty( $class ) ) { ?>class="<?php echo esc_attr( $class ); ?>"<?php } ?>
			<?php if ( ! empty( $width ) ) { ?>width="<?php echo absint( $width ); ?>"<?php } ?>
			<?php if ( ! empty( $height ) ) { ?>height="<?php echo absint( $height ); ?>"<?php } ?>
			<?php if ( $eager ) { ?>loading="eager" fetchpriority="high"<?php } else { ?>loading="lazy" decoding="async"<?php } ?>
		>
	</picture>
	<?php
}

/**
 * ACF image array → `<img>`, with a WordPress size.
 *
 * @param array|null $image ACF image field (return_format: array).
 * @param string     $size  Registered image size.
 * @param array      $attr  Extra attributes for wp_get_attachment_image().
 */
function yka_acf_image( ?array $image, string $size = 'large', array $attr = [] ): void {
	if ( empty( $image['ID'] ) ) {
		return;
	}

	if ( ! isset( $attr['alt'] ) && ! empty( $image['alt'] ) ) {
		$attr['alt'] = $image['alt'];
	}

	echo wp_get_attachment_image( (int) $image['ID'], $size, false, $attr );
}

/**
 * ACF link array → anchor attributes string, or '' when the link is empty.
 *
 * @param array|null $link ACF link field (return_format: array).
 */
function yka_link_attrs( ?array $link ): string {
	if ( empty( $link['url'] ) ) {
		return '';
	}

	$attrs = 'href="' . esc_url( $link['url'] ) . '"';

	if ( ! empty( $link['target'] ) ) {
		$attrs .= ' target="' . esc_attr( $link['target'] ) . '" rel="noopener"';
	}

	return $attrs;
}

/**
 * Trimmed excerpt that falls back to the post content.
 *
 * @param int $words Word count.
 * @param int $post_id Defaults to the current post.
 */
function yka_excerpt( int $words = 24, int $post_id = 0 ): string {
	$post_id = $post_id ?: get_the_ID();

	if ( ! $post_id ) {
		return '';
	}

	$post = get_post( $post_id );

	if ( ! $post ) {
		return '';
	}

	$text = ! empty( $post->post_excerpt ) ? $post->post_excerpt : $post->post_content;
	$text = strip_shortcodes( $text );
	$text = wp_strip_all_tags( $text );

	return wp_trim_words( $text, $words, '…' );
}
