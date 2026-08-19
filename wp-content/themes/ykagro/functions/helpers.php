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
 * Renders an icon that may come from the media library or from the theme.
 *
 * An uploaded SVG is inlined so CSS can still recolour it (`currentColor`), which
 * is how every icon in this design behaves on hover. It is re-sanitized on the
 * way out: files uploaded before the sanitizer existed, or dropped in over FTP,
 * never went through the upload filter.
 *
 * A PNG cannot be recoloured, so it is output as <img> — the editor chose a
 * raster icon and gets a raster icon.
 *
 * @param mixed  $uploaded ACF image field value. An empty ACF field returns
 *                         `false`, not null, so this stays untyped rather than
 *                         `?array` — a type hint here throws a TypeError and
 *                         takes the whole section down.
 * @param string $fallback Path relative to img/, e.g. 'icons/direction-broiler.svg'.
 * @param string $alt      Alt text for the raster case.
 */
function yka_icon_field( $uploaded, string $fallback = '', string $alt = '' ): void {
	$attachment_id = is_array( $uploaded ) && ! empty( $uploaded['ID'] ) ? (int) $uploaded['ID'] : 0;

	if ( ! $attachment_id ) {
		if ( ! empty( $fallback ) ) {
			yka_icon( $fallback );
		}

		return;
	}

	$mime = get_post_mime_type( $attachment_id );

	if ( 'image/svg+xml' === $mime ) {
		$path = get_attached_file( $attachment_id );

		if ( $path && is_readable( $path ) ) {
			$raw = file_get_contents( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- local upload.

			if ( false !== $raw ) {
				$sanitizer = new YKA_Svg_Sanitizer();
				$clean     = $sanitizer->sanitize( $raw );

				if ( '' !== $clean ) {
					echo $clean; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- sanitized against an allow-list.

					return;
				}
			}
		}

		// Unreadable or unsafe — fall back rather than showing a broken icon.
		if ( ! empty( $fallback ) ) {
			yka_icon( $fallback );
		}

		return;
	}

	echo wp_get_attachment_image(
		$attachment_id,
		'full',
		false,
		[
			'alt'      => ! empty( $alt ) ? $alt : ( $uploaded['alt'] ?? '' ),
			'loading'  => 'lazy',
			'decoding' => 'async',
		]
	);
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
 * Badge icon for a product, inherited from its category.
 *
 * The badge is a per-category mark, not a per-product one, so it is set once on
 * the category and every product in it picks it up. When a product sits in
 * several categories the first one that actually defines an icon wins.
 *
 * @param int $post_id Product ID.
 * @return array|null ACF image array, or null to fall back to the theme icon.
 */
function yka_product_badge( int $post_id ): ?array {
	$terms = wp_get_object_terms( $post_id, 'product_cat', [ 'fields' => 'ids' ] );

	if ( is_wp_error( $terms ) || empty( $terms ) ) {
		return null;
	}

	foreach ( $terms as $term_id ) {
		$icon = get_field( 'badge_icon', 'product_cat_' . (int) $term_id );

		if ( is_array( $icon ) && ! empty( $icon['ID'] ) ) {
			return $icon;
		}
	}

	return null;
}

/**
 * Breadcrumb trail for the current singular view.
 *
 * A CPT entry sits under its listing page (a direction under "Напрями
 * діяльності"), so that level is inserted automatically instead of every
 * template rebuilding the same trail.
 */
function yka_trail(): array {
	$trail = [];

	$parents = [
		'direction' => 'directions',
		'product'   => 'products',
		'vacancy'   => 'career',
	];

	$type = get_post_type();

	if ( isset( $parents[ $type ] ) ) {
		$page = get_page_by_path( $parents[ $type ] );

		if ( $page ) {
			$trail[] = [
				'label' => get_the_title( $page ),
				'href'  => get_permalink( $page ),
			];
		}
	}

	$trail[] = [ 'label' => get_the_title() ];

	return $trail;
}

/**
 * Does the current page open with a full-width banner hero?
 *
 * The decorative scroll line starts below the fold by default, which is right
 * when a tall banner sits at the top. On light inner pages (products, contacts,
 * a product detail) the content begins immediately, so the line has to start at
 * the very top — that is what `main.is-doc` switches on.
 *
 * Read from post meta rather than have_rows(): this runs while building <main>,
 * and starting an ACF loop here would disturb the row pointer the page builder
 * uses moments later.
 */
function yka_has_banner_hero(): bool {
	if ( ! is_singular() ) {
		return false;
	}

	$post_id = get_queried_object_id();

	if ( ! $post_id ) {
		return false;
	}

	$rows = get_post_meta( $post_id, 'page_builder', true );

	if ( ! is_array( $rows ) ) {
		return false;
	}

	foreach ( $rows as $index => $layout ) {
		if ( ! is_string( $layout ) ) {
			continue;
		}

		// The homepage hero and the about scene always carry media.
		if ( in_array( $layout, [ 'hero', 'about_scene' ], true ) ) {
			return true;
		}

		// A page hero counts only when an image is actually set.
		if ( 'page_hero' === $layout && get_post_meta( $post_id, sprintf( 'page_builder_%d_image', $index ), true ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Page-scoped wrapper class from the markup, e.g. `cooperation-page`.
 *
 * The markup wraps most page bodies in a div named after the page file, and a
 * few rules hang off it (.about-page clips the horizontal overflow of the hero
 * scene, .cooperation-page widens the gallery heading). Templates with a file
 * of their own carry that wrapper inline; only the pages sharing
 * templates/page-builder.php need it resolved at runtime, and their slug
 * matches the markup file name one to one.
 *
 * Pages the markup leaves unwrapped (contacts, privacy) stay unwrapped: the
 * wrapper is what `main > *:not(.scroll-line)` lifts above the scroll line, so
 * adding one where the markup has none would move that stacking context.
 */
function yka_page_wrapper_class(): string {
	$wrapped = [ 'about', 'career', 'cooperation', 'directions', 'production', 'products', 'responsibility' ];
	$slug    = get_post_field( 'post_name', get_queried_object_id() );

	if ( ! in_array( $slug, $wrapped, true ) ) {
		return '';
	}

	return $slug . '-page';
}

/**
 * Class list for the <main> element.
 *
 * @param string $extra Extra classes to prepend.
 */
function yka_main_class( string $extra = '' ): string {
	$classes = $extra ? [ $extra ] : [];

	if ( ! yka_has_banner_hero() ) {
		$classes[] = 'is-doc';
	}

	return implode( ' ', $classes );
}

/**
 * Image for a taxonomy term, as an ACF-shaped array.
 *
 * WordPress has no native image field for terms, so this comes from the term's
 * ACF `image` field. The `thumbnail_id` term meta is checked as a fallback: it is
 * the de-facto convention other plugins (and this theme's own seeding) write to,
 * so a category set up that way keeps working.
 *
 * @param int    $term_id  Term ID.
 * @param string $taxonomy Taxonomy name.
 * @return array|null ACF-style image array, or null when there is none.
 */
function yka_term_image( int $term_id, string $taxonomy = 'product_cat' ): ?array {
	if ( ! $term_id ) {
		return null;
	}

	if ( function_exists( 'get_field' ) ) {
		$image = get_field( 'image', $taxonomy . '_' . $term_id );

		if ( is_array( $image ) && ! empty( $image['ID'] ) ) {
			return $image;
		}
	}

	$thumb_id = (int) get_term_meta( $term_id, 'thumbnail_id', true );

	if ( ! $thumb_id ) {
		return null;
	}

	$term = get_term( $term_id, $taxonomy );

	return [
		'ID'  => $thumb_id,
		'alt' => $term instanceof WP_Term ? $term->name : '',
	];
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
 * Rebases a URL authored on a development host onto the current site.
 *
 * ACF stores absolute URLs, so links picked in the admin outlive the host they
 * were created on. This keeps them working after a move even if the database
 * was migrated without a search-replace. External links are left alone.
 *
 * @param string $url Stored URL.
 * @return string URL on the current host, or the original when it is external.
 */
function yka_rebase_url( string $url ): string {
	$host = wp_parse_url( $url, PHP_URL_HOST );

	// Relative paths, anchors, mailto:, tel: — nothing to rebase.
	if ( empty( $host ) ) {
		return $url;
	}

	$home = untrailingslashit( home_url() );

	if ( $host === wp_parse_url( $home, PHP_URL_HOST ) ) {
		return $url;
	}

	// A known authoring base: drop it whole, including its install sub-directory.
	foreach ( YKA_LEGACY_URLS as $base ) {
		$base = untrailingslashit( $base );

		if ( $url === $base || 0 === strpos( $url, $base . '/' ) ) {
			return $home . substr( $url, strlen( $base ) );
		}
	}

	// Any other local host: swap the origin, keep the path.
	if ( ! preg_match( '/^(localhost|127\.0\.0\.1|\[::1\])$|\.(local|test|localhost)$/i', $host ) ) {
		return $url;
	}

	$parts = wp_parse_url( $url );
	$path  = $parts['path'] ?? '';

	if ( ! empty( $parts['query'] ) ) {
		$path .= '?' . $parts['query'];
	}

	if ( ! empty( $parts['fragment'] ) ) {
		$path .= '#' . $parts['fragment'];
	}

	return $home . $path;
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
