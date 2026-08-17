<?php
/**
 * Query tweaks and document meta.
 *
 * The static markup set $page_title / $page_description per page. WordPress
 * covers the title through add_theme_support( 'title-tag' ), but nothing emits a
 * description, so that part is reproduced here.
 *
 * This is deliberately minimal: if an SEO plugin (Yoast, Rank Math, SEOPress)
 * is ever activated, it owns the meta tags and this class steps aside rather
 * than duplicating them.
 *
 * @package ykagro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class YKA_Seo {

	/**
	 * Post types the site search should look through.
	 */
	private const SEARCHABLE = [ 'post', 'page', 'product', 'direction', 'vacancy' ];

	public function __construct() {
		add_action( 'pre_get_posts', [ $this, 'widen_search' ] );
		add_action( 'wp_head', [ $this, 'meta_description' ], 3 );
		add_filter( 'document_title_separator', [ $this, 'title_separator' ] );
	}

	/**
	 * Site search covers products, directions and vacancies too — otherwise a
	 * visitor searching for a feed name gets nothing.
	 *
	 * @param WP_Query $query Query object.
	 */
	public function widen_search( WP_Query $query ): void {
		if ( is_admin() || ! $query->is_main_query() || ! $query->is_search() ) {
			return;
		}

		$query->set( 'post_type', self::SEARCHABLE );
	}

	public function title_separator(): string {
		return '—';
	}

	/**
	 * Emits <meta name="description">, unless an SEO plugin already handles it.
	 */
	public function meta_description(): void {
		if ( $this->seo_plugin_active() ) {
			return;
		}

		$description = $this->build_description();

		// A listing page may hold no prose at all (a grid and a FAQ, say). The
		// site tagline is a weak description but a far better one than none.
		if ( empty( $description ) ) {
			$description = get_bloginfo( 'description' );
		}

		if ( empty( $description ) ) {
			return;
		}

		printf(
			'<meta name="description" content="%s">' . "\n",
			esc_attr( wp_trim_words( $description, 30, '' ) )
		);
	}

	/**
	 * Known SEO plugins that output their own description tag.
	 */
	private function seo_plugin_active(): bool {
		return defined( 'WPSEO_VERSION' )
			|| class_exists( 'RankMath' )
			|| defined( 'SEOPRESS_VERSION' )
			|| defined( 'AIOSEO_VERSION' );
	}

	/**
	 * Picks the most specific description available for the current view.
	 */
	private function build_description(): string {
		if ( is_front_page() ) {
			$tagline = get_bloginfo( 'description' );

			// A page builder front page has no excerpt, so fall back to the tagline.
			return ! empty( $tagline ) ? $tagline : $this->from_post( (int) get_option( 'page_on_front' ) );
		}

		if ( is_singular() ) {
			return $this->from_post( get_the_ID() );
		}

		if ( is_tax() || is_category() || is_tag() ) {
			$term = get_queried_object();

			if ( $term instanceof WP_Term && ! empty( $term->description ) ) {
				return $term->description;
			}

			return $term instanceof WP_Term ? $term->name : '';
		}

		if ( is_home() ) {
			$blog_id = (int) get_option( 'page_for_posts' );

			return $blog_id ? $this->from_post( $blog_id ) : get_bloginfo( 'description' );
		}

		if ( is_author() ) {
			$author = get_queried_object();

			return $author instanceof WP_User ? (string) $author->description : '';
		}

		return '';
	}

	/**
	 * Description for one post: its excerpt, else its SEO block, else its content.
	 *
	 * @param int $post_id Post ID.
	 */
	private function from_post( int $post_id ): string {
		if ( ! $post_id ) {
			return '';
		}

		$post = get_post( $post_id );

		if ( ! $post ) {
			return '';
		}

		if ( ! empty( $post->post_excerpt ) ) {
			return $post->post_excerpt;
		}

		// Builder pages and CPTs keep their prose in ACF, so post_content is
		// usually empty. Ordered from most to least specific.
		if ( function_exists( 'get_field' ) ) {
			$seo = get_field( 'seo_block', $post_id );

			if ( ! empty( $seo['text'] ) ) {
				return wp_strip_all_tags( $seo['text'] );
			}

			// short_text: product, card_text: direction, lead: vacancy,
			// blog_intro: the posts page.
			foreach ( [ 'short_text', 'card_text', 'lead', 'blog_intro' ] as $field ) {
				$value = get_field( $field, $post_id );

				if ( ! empty( $value ) && is_string( $value ) ) {
					return wp_strip_all_tags( $value );
				}
			}
		}

		if ( ! empty( $post->post_content ) ) {
			return wp_strip_all_tags( strip_shortcodes( $post->post_content ) );
		}

		return $this->from_builder( $post_id );
	}

	/**
	 * First readable sentence out of the page builder.
	 *
	 * Builder pages hold every word in ACF rows, so without this a page like
	 * "Про компанію" would ship no description at all. Read straight from post
	 * meta — this runs on wp_head, where starting an ACF loop would disturb the
	 * row pointer the template is using.
	 *
	 * @param int $post_id Post ID.
	 */
	private function from_builder( int $post_id ): string {
		$rows = get_post_meta( $post_id, 'page_builder', true );

		if ( ! is_array( $rows ) || empty( $rows ) ) {
			return '';
		}

		// Prose first, headings only as a last resort.
		$fields = [ 'lead', 'text', 'title' ];

		foreach ( $fields as $field ) {
			foreach ( $rows as $index => $layout ) {
				if ( ! is_string( $layout ) || '' === $layout ) {
					continue;
				}

				$value = get_post_meta( $post_id, sprintf( 'page_builder_%d_%s', $index, $field ), true );

				if ( ! is_string( $value ) || '' === trim( $value ) ) {
					continue;
				}

				$value = trim( wp_strip_all_tags( $value ) );

				// Skip one-word labels like a tag or a button caption.
				if ( str_word_count( $value, 0, 'абвгґдеєжзиіїйклмнопрстуфхцчшщьюяАБВГҐДЕЄЖЗИІЇЙКЛМНОПРСТУФХЦЧШЩЬЮЯ' ) < 4 ) {
					continue;
				}

				return $value;
			}
		}

		return '';
	}
}

new YKA_Seo();
