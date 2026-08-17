<?php
/**
 * Product category listing — tab pills, search + sort row, card grid, pager.
 *
 * Search and sort travel as plain GET params (?q=…&sort=…), the same contract
 * the markup used, so the mobile filter sheet works by simple navigation and no
 * AJAX is involved.
 *
 * @package ykagro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$yka_term = get_queried_object();

$yka_sorts = [
	[ 'value' => 'popular', 'label' => __( 'Популярні', 'ykagro' ) ],
	[ 'value' => 'name-asc', 'label' => __( 'Від А до Я', 'ykagro' ) ],
	[ 'value' => 'name-desc', 'label' => __( 'Від Я до А', 'ykagro' ) ],
];

$yka_sort_values = wp_list_pluck( $yka_sorts, 'value' );
$yka_sort        = isset( $_GET['sort'] ) ? sanitize_key( wp_unslash( $_GET['sort'] ) ) : '';

if ( ! in_array( $yka_sort, $yka_sort_values, true ) ) {
	$yka_sort = 'popular';
}

$yka_search = isset( $_GET['q'] ) ? sanitize_text_field( wp_unslash( $_GET['q'] ) ) : '';

$yka_args = [
	'post_type'      => 'product',
	'post_status'    => 'publish',
	'posts_per_page' => 12,
	'paged'          => max( 1, (int) get_query_var( 'paged' ) ),
	'tax_query'      => [
		[
			'taxonomy' => 'product_cat',
			'field'    => 'term_id',
			'terms'    => (int) $yka_term->term_id,
		],
	],
];

if ( ! empty( $yka_search ) ) {
	$yka_args['s'] = $yka_search;
}

if ( 'name-asc' === $yka_sort ) {
	$yka_args['orderby'] = 'title';
	$yka_args['order']   = 'ASC';
} elseif ( 'name-desc' === $yka_sort ) {
	$yka_args['orderby'] = 'title';
	$yka_args['order']   = 'DESC';
} else {
	// "Популярні" has no metric behind it yet — fall back to the editor's order.
	$yka_args['orderby'] = [ 'menu_order' => 'ASC', 'date' => 'DESC' ];
}

$yka_query = new WP_Query( $yka_args );

$yka_terms = get_terms(
	[
		'taxonomy'   => 'product_cat',
		'hide_empty' => false,
		'parent'     => 0,
		'orderby'    => 'name',
	]
);

$yka_products_page = get_page_by_path( 'products' );
$yka_trail         = [];

if ( $yka_products_page ) {
	$yka_trail[] = [
		'label' => get_the_title( $yka_products_page ),
		'href'  => get_permalink( $yka_products_page ),
	];
}

$yka_trail[] = [ 'label' => $yka_term->name ];
?>

<main>
	<?php get_template_part( 'includes/scroll-line' ); ?>

	<?php get_template_part( 'template-parts/components/page-head', null, [ 'items' => $yka_trail ] ); ?>

	<div class="container">
		<h1 class="h2 clr-black"><?php echo esc_html( $yka_term->name ); ?></h1>
	</div>

	<div class="spacer-md"></div>

	<section class="catalog">
		<div class="container">
			<?php if ( ! is_wp_error( $yka_terms ) && ! empty( $yka_terms ) ) { ?>
				<nav class="catalog__tabs-rail" aria-label="<?php esc_attr_e( 'Категорії продукції', 'ykagro' ); ?>">
					<ul class="catalog__tabs">
						<?php
						foreach ( $yka_terms as $yka_tab ) {
							$yka_is_active = (int) $yka_tab->term_id === (int) $yka_term->term_id;
							?>
							<li>
								<a href="<?php echo esc_url( get_term_link( $yka_tab ) ); ?>"
									class="catalog__tab<?php echo $yka_is_active ? ' is-active' : ''; ?>"
									<?php echo $yka_is_active ? ' aria-current="page"' : ''; ?>><?php echo esc_html( $yka_tab->name ); ?></a>
							</li>
							<?php
						}
						?>
					</ul>
				</nav>
			<?php } ?>

			<?php // One GET form: ?q=…&sort=… — both fields land on the backend together. ?>
			<form class="catalog__bar" role="search" method="get" action="<?php echo esc_url( get_term_link( $yka_term ) ); ?>">
				<label class="catalog__search">
					<span class="catalog__search-icon" aria-hidden="true"><?php yka_icon( 'icons/search.svg' ); ?></span>
					<input class="catalog__search-input" type="search" name="q" placeholder="<?php esc_attr_e( 'Шукати...', 'ykagro' ); ?>" aria-label="<?php esc_attr_e( 'Пошук продукції', 'ykagro' ); ?>"
						value="<?php echo esc_attr( $yka_search ); ?>">
				</label>

				<?php // Mobile only: tabs + sort move into the filter sheet. ?>
				<button class="catalog__filter-btn js-catalog-filter-open" type="button"
					aria-controls="catalog-filter" aria-expanded="false" aria-label="<?php esc_attr_e( 'Фільтри', 'ykagro' ); ?>">
					<span class="catalog__filter-icon" aria-hidden="true"><?php yka_icon( 'icons/filters.svg' ); ?></span>
				</button>

				<div class="catalog__sort">
					<span class="catalog__sort-label" id="catalog-sort-label"><?php esc_html_e( 'Сортувати', 'ykagro' ); ?></span>
					<?php
					get_template_part(
						'template-parts/components/select',
						null,
						[
							'name'       => 'sort',
							'id'         => 'catalog-sort',
							'options'    => $yka_sorts,
							'value'      => $yka_sort,
							'labelledby' => 'catalog-sort-label',
							'class'      => 'catalog__select',
							'submit'     => true,
						]
					);
					?>
				</div>
			</form>
		</div>

		<div class="container-full">
			<?php if ( $yka_query->have_posts() ) { ?>
				<div class="catalog__grid">
					<?php
					while ( $yka_query->have_posts() ) {
						$yka_query->the_post();
						get_template_part( 'template-parts/components/catalog-card' );
					}
					?>
				</div>

				<?php
				get_template_part(
					'template-parts/components/pagination',
					null,
					[
						'query'     => $yka_query,
						'more_text' => __( 'Показати більше', 'ykagro' ),
					]
				);
				?>
			<?php } else { ?>
				<div class="container">
					<p class="text-lg clr-muted"><?php esc_html_e( 'За цим запитом продукцію не знайдено.', 'ykagro' ); ?></p>
				</div>
			<?php } ?>
		</div>

		<?php
		get_template_part(
			'template-parts/single/catalog-filter',
			null,
			[
				'terms'   => $yka_terms,
				'current' => (int) $yka_term->term_id,
				'sorts'   => $yka_sorts,
				'sort'    => $yka_sort,
			]
		);
		?>
	</section>

	<div class="spacer-xl"></div>
</main>

<?php
wp_reset_postdata();

get_footer();
