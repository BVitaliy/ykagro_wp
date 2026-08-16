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
		'yka-modals'      => 'js/app-modals.js',
		'yka-scroll-line' => 'js/app-scroll-line.js',
	];

	public function __construct() {
		add_action( 'after_setup_theme', [ $this, 'setup' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ] );
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
	 * Per-template script map, ported from the static pages' closing script tags.
	 *
	 * Each entry's `when` is evaluated once per request; the corresponding static
	 * page names are listed so the mapping stays auditable against the markup.
	 */
	private function conditional_scripts(): array {
		return [
			// index, about, product-detail, article, cooperation, direction-detail
			'yka-swiper-init' => [
				'src'  => 'js/app-swiper.js',
				'deps' => [ 'yka-swiper' ],
				'when' => $this->needs_swiper(),
			],
			// index, about, career, cooperation, direction-detail, responsibility
			'yka-home' => [
				'src'  => 'js/app-home.js',
				'when' => is_front_page()
					|| $this->is_template( [ 'about', 'career', 'cooperation', 'responsibility' ] )
					|| is_singular( 'direction' ),
			],
			// index, about, products, product-category, contacts, career, production, cooperation, direction-detail
			'yka-contact-parallax' => [
				'src'  => 'js/app-contact-parallax.js',
				'when' => is_front_page()
					|| $this->is_template( [ 'about', 'career', 'contacts', 'cooperation', 'production', 'products' ] )
					|| is_tax( 'product_cat' )
					|| is_singular( 'direction' ),
			],
			// career, production, blog, directions, responsibility, cooperation, direction-detail
			'yka-hero-parallax' => [
				'src'  => 'js/app-hero-parallax.js',
				'when' => $this->is_template( [ 'career', 'directions', 'production', 'responsibility', 'cooperation' ] )
					|| is_home()
					|| is_singular( 'direction' ),
			],
			// about, direction-detail
			'yka-about' => [
				'src'  => 'js/app-about.js',
				'when' => $this->is_template( [ 'about' ] ) || is_singular( 'direction' ),
			],
			// responsibility, cooperation, direction-detail
			'yka-resp-cta' => [
				'src'  => 'js/app-resp-cta.js',
				'when' => $this->is_template( [ 'responsibility', 'cooperation' ] ) || is_singular( 'direction' ),
			],
			// production
			'yka-production' => [
				'src'  => 'js/app-production.js',
				'when' => $this->is_template( [ 'production' ] ),
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
			// product-category
			'yka-catalog-filter' => [
				'src'  => 'js/app-catalog-filter.js',
				'when' => is_tax( 'product_cat' ),
			],
			'yka-select' => [
				'src'  => 'js/app-select.js',
				'when' => is_tax( 'product_cat' ),
			],
			// 404
			'yka-404' => [
				'src'  => 'js/app-404.js',
				'when' => is_404(),
			],
		];
	}

	/**
	 * index, about, product-detail, article, cooperation, direction-detail
	 */
	private function needs_swiper(): bool {
		return is_front_page()
			|| $this->is_template( [ 'about', 'cooperation' ] )
			|| is_singular( [ 'post', 'product', 'direction' ] );
	}

	/**
	 * index, product-detail, cooperation, direction-detail
	 */
	private function needs_lightgallery(): bool {
		return is_front_page()
			|| $this->is_template( [ 'cooperation' ] )
			|| is_singular( [ 'product', 'direction' ] );
	}

	/**
	 * Is the current page using one of the given templates/<slug>.php files?
	 *
	 * @param string[] $slugs Template slugs without the templates/ prefix or .php suffix.
	 */
	private function is_template( array $slugs ): bool {
		if ( ! is_page() ) {
			return false;
		}

		foreach ( $slugs as $slug ) {
			if ( is_page_template( 'templates/' . $slug . '.php' ) ) {
				return true;
			}
		}

		return false;
	}
}

new YKA_Theme();
