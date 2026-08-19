<?php
/**
 * Theme setup and asset loading.
 *
 * @package ykagro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class YKA_Theme {

	/**
	 * Scripts loaded on every page, in dependency order.
	 * Vendors first — the app modules assume jQuery/GSAP/Lenis are already registered.
	 */
	private const GLOBAL_VENDORS = [
		'yka-jquery'     => 'js/vendors/jquery.min.js',
		'yka-lenis'      => 'js/vendors/lenis.min.js',
		'yka-gsap'       => 'js/vendors/gsap.min.js',
		'yka-scrolltrigger' => 'js/vendors/ScrollTrigger.min.js',
		'yka-split-type' => 'js/vendors/split-type.min.js',
	];

	private const GLOBAL_SCRIPTS = [
		'yka-global'      => 'js/app-global.js',
		'yka-menu'        => 'js/app-menu.js',
		'yka-titles'      => 'js/app-titles.js',
		'yka-faq'         => 'js/app-faq.js',
		'yka-form'        => 'js/app-form.js',
		'yka-cf7'         => 'js/app-cf7.js',
		'yka-modals'      => 'js/app-modals.js',
		'yka-scroll-line' => 'js/app-scroll-line.js',
	];

	public function __construct() {
		add_action( 'after_setup_theme', [ $this, 'setup' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'contacts_map_boot' ], 20 );
		add_action( 'wp_head', [ $this, 'head_preloads' ], 1 );
		add_action( 'wp_head', [ $this, 'head_inline_boot' ], 2 );
		add_action( 'widgets_init', [ $this, 'register_sidebars' ] );
	}

	public function setup(): void {
		load_theme_textdomain( 'ykagro', YKA_DIR . '/languages' );

		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'html5', [ 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ] );
		add_theme_support( 'responsive-embeds' );

		// Crops taken from the design. Hard crop everywhere — these slots have a
		// fixed aspect ratio in CSS, so a soft resize would letterbox.
		add_image_size( 'yka-card', 640, 482, true );        // category / product tile
		add_image_size( 'yka-catalog', 594, 391, true );     // catalog listing card
		add_image_size( 'yka-article', 594, 422, true );     // article card
		add_image_size( 'yka-product', 828, 655, true );     // product gallery slide
		add_image_size( 'yka-news', 849, 682, true );        // news block photo
		add_image_size( 'yka-gallery', 543, 482, true );     // gallery slider slide
		add_image_size( 'yka-intro-left', 900, 600, true );  // intro collage, left
		add_image_size( 'yka-intro-right', 1000, 666, true );// intro collage, right
		add_image_size( 'yka-section', 1000, 700, true );    // direction section photo
		add_image_size( 'yka-tile', 860, 513, true );         // direction listing tile
		add_image_size( 'yka-comfort', 547, 459, true );      // comfort / benefit card
		add_image_size( 'yka-team', 633, 635, true );          // team portrait (card + modal)
		add_image_size( 'yka-author', 648, 650, true );        // author archive portrait
		add_image_size( 'yka-banner', 2880, 1000, true );    // wide page banner
		add_image_size( 'yka-banner-mob', 750, 800, true );  // banner, mobile crop

		register_nav_menus( [
			'main_menu'      => __( 'Main menu (floating panel)', 'ykagro' ),
			'footer_menu_1'  => __( 'Footer menu — column 1', 'ykagro' ),
			'footer_menu_2'  => __( 'Footer menu — column 2', 'ykagro' ),
		] );
	}

	public function register_sidebars(): void {
		// Placeholder — the design has no widget areas. Kept so plugins that
		// expect at least one registered sidebar do not warn.
	}

	/**
	 * Font preload + favicon. Runs before the enqueued stylesheets so the font
	 * request starts as early as possible.
	 */
	public function head_preloads(): void {
		?>
		<link rel="icon" href="<?php echo esc_url( YKA_URI . '/favicon.svg' ); ?>" type="image/svg+xml">
		<meta name="theme-color" content="#FFF7EE">
		<link rel="preload" as="font" href="<?php echo esc_url( YKA_URI . '/fonts/montserrat/montserrat-cyrillic.woff2' ); ?>" type="font/woff2" crossorigin>
		<?php
	}

	/**
	 * Pre-paint boot script, ported from the markup's inc/_top.php.
	 *
	 * `ta-js` must land on <html> before first paint or animated titles flash as
	 * plain text; scrollRestoration must be disabled before the browser restores
	 * a scroll position on back-navigation. Both are inline on purpose.
	 */
	public function head_inline_boot(): void {
		?>
		<script>
			(function () {
				document.documentElement.classList.add("ta-js");
				if ("scrollRestoration" in window.history) {
					window.history.scrollRestoration = "manual";
				}
				if (!window.location.hash) {
					window.scrollTo(0, 0);
					window.addEventListener("pageshow", function () {
						window.scrollTo(0, 0);
					});
				}
			})();
		</script>
		<?php
	}

	public function enqueue_assets(): void {
		// The block library CSS is dead weight — this theme renders no blocks.
		wp_dequeue_style( 'wp-block-library' );
		wp_deregister_style( 'wp-block-library' );
		wp_dequeue_style( 'classic-theme-styles' );

		$needs_swiper        = $this->needs_swiper();
		$needs_lightgallery  = $this->needs_lightgallery();

		// --- Styles ---
		if ( $needs_swiper ) {
			wp_enqueue_style( 'yka-swiper', YKA_URI . '/css/vendors/swiper-bundle.min.css', [], null );
		}

		if ( $needs_lightgallery ) {
			wp_enqueue_style( 'yka-lightgallery', YKA_URI . '/css/vendors/lightgallery/css/lightgallery.css', [], null );
		}

		wp_enqueue_style( 'yka-main', YKA_URI . '/css/main.css', [], YKA_VER );
		wp_enqueue_style( 'yka-style', YKA_URI . '/css/style.css', [ 'yka-main' ], YKA_VER );

		// --- Vendor scripts ---
		foreach ( self::GLOBAL_VENDORS as $handle => $path ) {
			wp_enqueue_script( $handle, YKA_URI . '/' . $path, [], null, [ 'strategy' => 'defer', 'in_footer' => true ] );
		}

		if ( $needs_swiper ) {
			wp_enqueue_script( 'yka-swiper', YKA_URI . '/js/vendors/swiper-bundle.min.js', [], null, [ 'strategy' => 'defer', 'in_footer' => true ] );
		}

		if ( $needs_lightgallery ) {
			wp_enqueue_script( 'yka-lightgallery', YKA_URI . '/js/vendors/lightgallery.min.js', [ 'yka-jquery' ], null, [ 'strategy' => 'defer', 'in_footer' => true ] );
		}

		// --- Global app scripts ---
		foreach ( self::GLOBAL_SCRIPTS as $handle => $path ) {
			wp_enqueue_script( $handle, YKA_URI . '/' . $path, [ 'yka-gsap', 'yka-scrolltrigger' ], YKA_VER, [ 'strategy' => 'defer', 'in_footer' => true ] );
		}

		wp_localize_script(
			'yka-global',
			'ykagro',
			[
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'yka_nonce' ),
				'homeUrl' => YKA_HOME_URL,
			]
		);

		// --- Conditional app scripts ---
		foreach ( $this->conditional_scripts() as $handle => $config ) {
			if ( empty( $config['when'] ) ) {
				continue;
			}

			wp_enqueue_script(
				$handle,
				YKA_URI . '/' . $config['src'],
				$config['deps'] ?? [ 'yka-gsap', 'yka-scrolltrigger' ],
				YKA_VER,
				[ 'strategy' => 'defer', 'in_footer' => true ]
			);
		}
	}

	/**
	 * Conditional script map.
	 *
	 * Pages are assembled from page-builder blocks, so what a page needs follows
	 * from which blocks it actually contains — not from which template file it
	 * uses. That is both more accurate and stable when an editor rearranges a page.
	 *
	 * The three scroll modules (home, contact-parallax, hero-parallax) load on
	 * every content view: the static markup included them on almost every page,
	 * and each module bails when its root element is absent (see
	 * .claude/js-standards.md), so there is nothing to gate them on.
	 */
	private function conditional_scripts(): array {
		$is_content_view = is_singular() || is_home() || is_tax( 'product_cat' );

		return [
			'yka-swiper-init' => [
				'src'  => 'js/app-swiper.js',
				'deps' => [ 'yka-swiper' ],
				'when' => $this->needs_swiper(),
			],
			'yka-home' => [
				'src'  => 'js/app-home.js',
				'when' => $is_content_view,
			],
			'yka-contact-parallax' => [
				'src'  => 'js/app-contact-parallax.js',
				'when' => $is_content_view,
			],
			'yka-hero-parallax' => [
				'src'  => 'js/app-hero-parallax.js',
				'when' => $is_content_view,
			],
			// The scroll scene and story timeline only exist in these blocks.
			'yka-about' => [
				'src'  => 'js/app-about.js',
				'when' => $this->has_layout( [ 'about_scene', 'about_story' ] ) || is_singular( 'direction' ),
			],
			'yka-resp-cta' => [
				'src'  => 'js/app-resp-cta.js',
				'when' => $this->has_layout( [ 'cta_band' ] ),
			],
			'yka-production' => [
				'src'  => 'js/app-production.js',
				'when' => $this->has_layout( [ 'production_steps' ] ),
			],
			// article, article-no-nav
			'yka-article' => [
				'src'  => 'js/app-article.js',
				'deps' => [ 'yka-swiper' ],
				'when' => is_singular( 'post' ),
			],
			// product-detail
			'yka-product-gallery' => [
				'src'  => 'js/app-product-gallery.js',
				'deps' => [ 'yka-lightgallery' ],
				'when' => is_singular( 'product' ),
			],
			// Listings with a "Показати більше" button: catalog, blog, author, search.
			'yka-load-more' => [
				'src'  => 'js/app-load-more.js',
				'when' => is_tax( 'product_cat' ) || is_home() || is_author() || is_search(),
			],
			// product-category
			'yka-catalog-filter' => [
				'src'  => 'js/app-catalog-filter.js',
				'when' => is_tax( 'product_cat' ),
			],
			'yka-select' => [
				'src'  => 'js/app-select.js',
				'when' => is_tax( 'product_cat' ),
			],
			// contacts — map pins and popups
			'yka-contacts' => [
				'src'  => 'js/app-contacts.js',
				'when' => $this->has_layout( [ 'contacts_hero' ] ),
			],
			// 404
			'yka-404' => [
				'src'  => 'js/app-404.js',
				'when' => is_404(),
			],
		];
	}

	/**
	 * Google Maps bootstrap for the contacts block.
	 *
	 * The Maps JS API is ~300KB and the map sits below the fold, so it is loaded
	 * only when it nears the viewport. The API still calls the global init
	 * callback on ready, so pin behaviour is unchanged.
	 */
	public function contacts_map_boot(): void {
		if ( ! $this->has_layout( [ 'contacts_hero' ] ) ) {
			return;
		}

		$api_key = function_exists( 'get_field' ) ? (string) get_field( 'google_maps_key', 'options' ) : '';

		wp_add_inline_script(
			'yka-contacts',
			'window.YKAGRO_CONTACTS_MAP = ' . wp_json_encode(
				[
					'apiKey'  => $api_key,
					'zoom'    => 11,
					'pinIcon' => YKA_URI . '/img/icons/map-pin.svg',
				]
			) . ';'
			. 'window.initYkagroContactsMap = window.initYkagroContactsMap || function () { window.__YKAGRO_CONTACTS_MAP_READY = true; };',
			'before'
		);

		if ( empty( $api_key ) ) {
			return;
		}

		$src = add_query_arg(
			[
				'key'      => $api_key,
				'language' => 'uk',
				'region'   => 'UA',
				'callback' => 'initYkagroContactsMap',
			],
			'https://maps.googleapis.com/maps/api/js'
		);

		wp_add_inline_script(
			'yka-contacts',
			'(function () {
				var url = ' . wp_json_encode( $src ) . ';
				var target = document.querySelector("[data-contacts-map]");
				var loaded = false;
				function load() {
					if (loaded) return;
					loaded = true;
					var s = document.createElement("script");
					s.src = url;
					s.async = true;
					s.defer = true;
					document.body.appendChild(s);
				}
				if (!target || !("IntersectionObserver" in window)) { load(); return; }
				var io = new IntersectionObserver(function (entries) {
					if (entries[0].isIntersecting) { io.disconnect(); load(); }
				}, { rootMargin: "600px 0px" });
				io.observe(target);
			})();'
		);
	}

	/**
	 * Blocks that render a Swiper instance, plus the single templates that do.
	 */
	private function needs_swiper(): bool {
		return $this->has_layout( [ 'products', 'articles', 'comfort', 'stats_cards', 'gallery', 'about_team', 'directions_slider' ] )
			|| is_singular( [ 'post', 'product', 'direction' ] );
	}

	/**
	 * Blocks that open a lightbox.
	 */
	private function needs_lightgallery(): bool {
		return $this->has_layout( [ 'gallery' ] )
			|| is_singular( [ 'product', 'direction' ] );
	}

	/**
	 * Does the current page's builder contain any of these layouts?
	 *
	 * Hidden rows are ignored — a hidden block renders nothing, so it needs nothing.
	 *
	 * @param string[] $names Layout names.
	 */
	private function has_layout( array $names ): bool {
		return (bool) array_intersect( $names, $this->page_layouts() );
	}

	/**
	 * Layout names used by the current page, in order.
	 *
	 * Read straight from post meta rather than through have_rows(), because this
	 * runs on wp_enqueue_scripts where starting an ACF loop would disturb the
	 * row pointer the template later relies on.
	 *
	 * @return string[]
	 */
	private function page_layouts(): array {
		static $cache = null;

		if ( null !== $cache ) {
			return $cache;
		}

		$cache = [];

		if ( ! is_singular() || ! function_exists( 'get_field' ) ) {
			return $cache;
		}

		$post_id = get_queried_object_id();

		if ( ! $post_id ) {
			return $cache;
		}

		$rows = get_post_meta( $post_id, 'page_builder', true );

		if ( ! is_array( $rows ) ) {
			return $cache;
		}

		foreach ( $rows as $index => $layout ) {
			if ( ! is_string( $layout ) || '' === $layout ) {
				continue;
			}

			// Skip rows the editor switched off.
			if ( get_post_meta( $post_id, sprintf( 'page_builder_%d_%s_hide', $index, $layout ), true ) ) {
				continue;
			}

			$cache[] = $layout;
		}

		return $cache;
	}
}

new YKA_Theme();
