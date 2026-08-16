<?php
/**
 * Custom post types and taxonomies.
 *
 * URL design — listing pages are ordinary WordPress pages using a
 * templates/*.php template, so an editor can manage their hero, intro and SEO
 * copy like any other page. The post types therefore have no archive of their
 * own; only the product category taxonomy gets a real archive, because there is
 * one listing per category.
 *
 *   /products/            page + templates/products.php   (category overview)
 *   /products/{cat}/      product_cat taxonomy archive
 *   /product/{slug}/      single product
 *   /directions/          page + templates/directions.php
 *   /directions/{slug}/   single direction
 *   /career/              page + templates/career.php
 *   /career/{slug}/       single vacancy
 *
 * @package ykagro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class YKA_Post_Types {

	public function __construct() {
		add_action( 'init', [ $this, 'register_post_types' ] );
		add_action( 'init', [ $this, 'register_taxonomies' ] );
	}

	public function register_post_types(): void {
		register_post_type(
			'direction',
			[
				'labels'          => $this->labels(
					'Напрям діяльності',
					'Напрями діяльності',
					'напрям'
				),
				'public'          => true,
				'has_archive'     => false,
				'menu_icon'       => 'dashicons-networking',
				'menu_position'   => 21,
				'supports'        => [ 'title', 'thumbnail', 'excerpt', 'page-attributes' ],
				'rewrite'         => [ 'slug' => 'directions', 'with_front' => false ],
				'show_in_rest'    => true,
				'capability_type' => 'post',
			]
		);

		register_post_type(
			'product',
			[
				'labels'          => $this->labels(
					'Продукт',
					'Продукція',
					'продукт'
				),
				'public'          => true,
				'has_archive'     => false,
				'menu_icon'       => 'dashicons-carrot',
				'menu_position'   => 22,
				'supports'        => [ 'title', 'thumbnail', 'excerpt', 'page-attributes' ],
				'rewrite'         => [ 'slug' => 'product', 'with_front' => false ],
				'show_in_rest'    => true,
				'capability_type' => 'post',
			]
		);

		register_post_type(
			'vacancy',
			[
				'labels'          => $this->labels(
					'Вакансія',
					'Вакансії',
					'вакансію'
				),
				'public'          => true,
				'has_archive'     => false,
				'menu_icon'       => 'dashicons-groups',
				'menu_position'   => 23,
				'supports'        => [ 'title', 'page-attributes' ],
				'rewrite'         => [ 'slug' => 'career', 'with_front' => false ],
				'show_in_rest'    => true,
				'capability_type' => 'post',
			]
		);
	}

	public function register_taxonomies(): void {
		register_taxonomy(
			'product_cat',
			[ 'product' ],
			[
				'labels'            => [
					'name'              => __( 'Категорії продукції', 'ykagro' ),
					'singular_name'     => __( 'Категорія продукції', 'ykagro' ),
					'search_items'      => __( 'Шукати категорії', 'ykagro' ),
					'all_items'         => __( 'Усі категорії', 'ykagro' ),
					'parent_item'       => __( 'Батьківська категорія', 'ykagro' ),
					'parent_item_colon' => __( 'Батьківська категорія:', 'ykagro' ),
					'edit_item'         => __( 'Редагувати категорію', 'ykagro' ),
					'update_item'       => __( 'Оновити категорію', 'ykagro' ),
					'add_new_item'      => __( 'Додати категорію', 'ykagro' ),
					'new_item_name'     => __( 'Назва нової категорії', 'ykagro' ),
					'menu_name'         => __( 'Категорії', 'ykagro' ),
					'not_found'         => __( 'Категорій не знайдено', 'ykagro' ),
				],
				'public'            => true,
				'hierarchical'      => true,
				'show_admin_column' => true,
				'show_in_rest'      => true,
				'rewrite'           => [ 'slug' => 'products', 'with_front' => false, 'hierarchical' => false ],
			]
		);
	}

	/**
	 * Builds a label set from the three forms Ukrainian needs.
	 *
	 * @param string $singular   Nominative singular — "Продукт".
	 * @param string $plural     Nominative plural — "Продукція".
	 * @param string $accusative Accusative singular, for "Додати %s" — "продукт".
	 */
	private function labels( string $singular, string $plural, string $accusative ): array {
		return [
			'name'               => $plural,
			'singular_name'      => $singular,
			'menu_name'          => $plural,
			'all_items'          => sprintf( __( 'Усі — %s', 'ykagro' ), $plural ),
			/* translators: %s: post type name in the accusative case. */
			'add_new_item'       => sprintf( __( 'Додати %s', 'ykagro' ), $accusative ),
			'add_new'            => __( 'Додати новий', 'ykagro' ),
			/* translators: %s: post type name in the accusative case. */
			'edit_item'          => sprintf( __( 'Редагувати %s', 'ykagro' ), $accusative ),
			/* translators: %s: post type name in the accusative case. */
			'new_item'           => sprintf( __( 'Новий — %s', 'ykagro' ), $singular ),
			/* translators: %s: post type name in the accusative case. */
			'view_item'          => sprintf( __( 'Переглянути %s', 'ykagro' ), $accusative ),
			/* translators: %s: post type name in the plural. */
			'search_items'       => sprintf( __( 'Шукати — %s', 'ykagro' ), $plural ),
			'not_found'          => __( 'Нічого не знайдено', 'ykagro' ),
			'not_found_in_trash' => __( 'У кошику порожньо', 'ykagro' ),
		];
	}
}

new YKA_Post_Types();
