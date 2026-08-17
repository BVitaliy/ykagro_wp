<?php
/**
 * Search results.
 *
 * The header's search field posts here. Results mix post types, so cards are
 * picked per type rather than assuming articles.
 *
 * @package ykagro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$yka_query_string = get_search_query();
$yka_found        = (int) $GLOBALS['wp_query']->found_posts;
?>

<main>
	<?php get_template_part( 'includes/scroll-line', null, [ 'class' => 'scroll-line--blog' ] ); ?>

	<?php
	get_template_part(
		'template-parts/components/page-head',
		null,
		[ 'items' => [ [ 'label' => __( 'Пошук', 'ykagro' ) ] ] ]
	);
	?>

	<div class="container">
		<h1 class="h2 clr-black">
			<?php
			if ( ! empty( $yka_query_string ) ) {
				/* translators: %s: search query. */
				printf( esc_html__( 'Результати пошуку: %s', 'ykagro' ), '<span>' . esc_html( $yka_query_string ) . '</span>' );
			} else {
				esc_html_e( 'Пошук', 'ykagro' );
			}
			?>
		</h1>

		<?php if ( ! empty( $yka_query_string ) ) { ?>
			<p class="text-lg clr-muted">
				<?php
				printf(
					/* translators: %d: number of results. */
					esc_html( _n( 'Знайдено %d результат', 'Знайдено %d результатів', $yka_found, 'ykagro' ) ),
					$yka_found // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- integer.
				);
				?>
			</p>
		<?php } ?>

		<form class="catalog__search" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<span class="catalog__search-icon" aria-hidden="true"><?php yka_icon( 'icons/search.svg' ); ?></span>
			<input class="catalog__search-input" type="search" name="s" value="<?php echo esc_attr( $yka_query_string ); ?>"
				placeholder="<?php esc_attr_e( 'Шукати...', 'ykagro' ); ?>" aria-label="<?php esc_attr_e( 'Пошук по сайту', 'ykagro' ); ?>">
		</form>
	</div>

	<div class="spacer-md"></div>

	<?php if ( have_posts() ) { ?>
		<section class="blog-list" aria-label="<?php esc_attr_e( 'Результати пошуку', 'ykagro' ); ?>">
			<div class="blog-list__inner">
				<div class="blog-list__grid">
					<?php
					while ( have_posts() ) {
						the_post();

						// A product's card is the catalog tile; everything else reads as an article.
						if ( 'product' === get_post_type() ) {
							get_template_part( 'template-parts/components/catalog-card' );
						} else {
							get_template_part( 'template-parts/components/article-card' );
						}
					}
					?>
				</div>

				<?php
				get_template_part(
					'template-parts/components/pagination',
					null,
					[ 'more_text' => __( 'Показати більше', 'ykagro' ) ]
				);
				?>
			</div>
		</section>
	<?php } else { ?>
		<div class="container">
			<p class="text-lg clr-muted"><?php esc_html_e( 'За цим запитом нічого не знайдено. Спробуйте інші слова.', 'ykagro' ); ?></p>
		</div>
	<?php } ?>

	<div class="spacer-xl"></div>
</main>

<?php
get_footer();
