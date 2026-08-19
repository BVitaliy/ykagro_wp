<?php
/**
 * Listing footer: pager, optionally with a "Показати більше" button above it.
 *
 * Shared by the blog list, the author archive and the product catalog, so it
 * owns its own top margin and centring.
 *
 * Page links come from paginate_links() with 'format' => '' so they respect
 * whatever query args are already on the URL (?q=…&sort=…) — a hand-built
 * base would drop the active filters on page 2.
 *
 * @param array $args {
 *     @type WP_Query $query     Query to page through. Defaults to the main query.
 *     @type string   $more_text Label for the "load more" button. Empty = no button.
 *     @type string   $grid      CSS selector of the container the cards live in.
 *                               Set it to let app-load-more.js append the next
 *                               page instead of navigating; empty = plain link.
 * }
 *
 * @package ykagro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$query = ! empty( $args['query'] ) && $args['query'] instanceof WP_Query ? $args['query'] : $GLOBALS['wp_query'];
$total = (int) $query->max_num_pages;

if ( $total < 2 ) {
	return;
}

$current   = max( 1, (int) $query->get( 'paged' ) ?: (int) get_query_var( 'paged' ) ?: 1 );
$more_text = ! empty( $args['more_text'] ) ? (string) $args['more_text'] : '';
$grid      = ! empty( $args['grid'] ) ? (string) $args['grid'] : '';

$pages = paginate_links(
	[
		'total'     => $total,
		'current'   => $current,
		'type'      => 'array',
		'prev_next' => false,
		'end_size'  => 1,
		'mid_size'  => 2,
	]
);

if ( empty( $pages ) ) {
	return;
}

$next_url = $current < $total ? get_pagenum_link( $current + 1 ) : '';
$prev_url = $current > 1 ? get_pagenum_link( $current - 1 ) : '';
?>
<div class="pagination-block">
	<?php if ( ! empty( $more_text ) && ! empty( $next_url ) ) { ?>
		<a href="<?php echo esc_url( $next_url ); ?>" class="btn pagination-block__more"<?php echo ! empty( $grid ) ? ' data-load-more="' . esc_attr( $grid ) . '"' : ''; ?>>
			<span class="btn__icon"><?php yka_icon( 'icons/arrow-diagonal.svg' ); ?></span>
			<?php echo esc_html( $more_text ); ?>
		</a>
	<?php } ?>

	<nav class="pagination" aria-label="<?php esc_attr_e( 'Пагінація', 'ykagro' ); ?>">
		<a class="pagination__arrow <?php echo empty( $prev_url ) ? 'is-disabled' : ''; ?>" href="<?php echo empty( $prev_url ) ? '#' : esc_url( $prev_url ); ?>" aria-label="<?php esc_attr_e( 'Попередня сторінка', 'ykagro' ); ?>">
			<span class="icon"><?php yka_icon( 'icons/arrow-pagination.svg' ); ?></span>
		</a>

		<div class="pagination__pages">
			<?php
			foreach ( $pages as $page_link ) {
				// paginate_links() emits <a class="page-numbers"> / <span class="current">;
				// remap to the design's classes without rebuilding the URLs.
				$page_link = str_replace(
					[ 'page-numbers current', 'page-numbers dots', 'page-numbers' ],
					[ 'pagination__page is-active', 'pagination__page is-dots', 'pagination__page' ],
					$page_link
				);

				echo wp_kses(
					$page_link,
					[
						'a'    => [ 'class' => [], 'href' => [], 'aria-current' => [] ],
						'span' => [ 'class' => [], 'aria-current' => [] ],
					]
				);
			}
			?>
		</div>

		<a class="pagination__arrow <?php echo empty( $next_url ) ? 'is-disabled' : ''; ?>" href="<?php echo empty( $next_url ) ? '#' : esc_url( $next_url ); ?>" aria-label="<?php esc_attr_e( 'Наступна сторінка', 'ykagro' ); ?>">
			<span class="icon"><?php yka_icon( 'icons/arrow-pagination.svg' ); ?></span>
		</a>
	</nav>
</div>
