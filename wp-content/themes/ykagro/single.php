<?php
/**
 * Single post (article).
 *
 * The body is editor HTML — bare tags plus a few block wrappers. The table of
 * contents is built client-side from the h2s (app-article.js), so nothing here
 * has to know the article's structure.
 *
 * @package ykagro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

the_post();

$yka_blog_id = (int) get_option( 'page_for_posts' );
$yka_trail   = [];

if ( $yka_blog_id ) {
	$yka_trail[] = [
		'label' => get_the_title( $yka_blog_id ),
		'href'  => get_permalink( $yka_blog_id ),
	];
}

$yka_trail[] = [ 'label' => get_the_title() ];

$yka_author_id  = (int) get_post_field( 'post_author', get_the_ID() );
$yka_has_author = $yka_author_id && ! empty( get_the_author_meta( 'description', $yka_author_id ) );
?>

<main class="is-article">
	<?php get_template_part( 'includes/scroll-line', null, [ 'class' => 'scroll-line--blog' ] ); ?>

	<?php get_template_part( 'template-parts/components/page-head' ); ?>

	<article class="article-page article-page--with-nav" data-article-page>
		<div class="container">
			<div class="article-page__crumbs breadcrumbs-scroll">
				<?php get_template_part( 'template-parts/components/breadcrumbs', null, [ 'items' => $yka_trail ] ); ?>
			</div>

			<header class="article-page__head">
				<div class="article-page__meta">
					<span class="article-page__chip">
						<span class="article-page__chip-icon" aria-hidden="true"><?php yka_icon( 'icons/calendar.svg' ); ?></span>
						<span class="article-page__chip-text"><?php echo esc_html( get_the_date( 'j F Y \р' ) ); ?></span>
					</span>
					<a href="<?php echo esc_url( get_author_posts_url( $yka_author_id ) ); ?>" class="article-page__chip">
						<span class="article-page__chip-icon" aria-hidden="true"><?php yka_icon( 'icons/user.svg' ); ?></span>
						<span class="article-page__chip-text"><?php echo esc_html( get_the_author_meta( 'display_name', $yka_author_id ) ); ?></span>
					</a>
				</div>
				<h1 class="article-page__title h2" data-title-anim="off"><?php the_title(); ?></h1>
			</header>
		</div>

		<?php if ( has_post_thumbnail() ) { ?>
			<figure class="article-page__hero container">
				<?php
				the_post_thumbnail(
					'full',
					[
						'loading'       => 'eager',
						'fetchpriority' => 'high',
						'decoding'      => 'async',
					]
				);
				?>
			</figure>
		<?php } ?>

		<div class="container article-page__layout">
			<div class="article-content">
				<?php the_content(); ?>
			</div>

			<?php // Filled from the article's h2 headings by app-article.js, which
			// hides the whole block when there are none. ?>
			<aside class="article-toc" data-article-toc>
				<button class="article-toc__toggle" type="button" aria-expanded="false" data-article-toc-toggle>
					<span data-article-toc-current><?php esc_html_e( 'Читайте у цій статті', 'ykagro' ); ?></span>
					<span class="article-toc__toggle-icon" aria-hidden="true"><?php yka_icon( 'icons/chevron-down.svg' ); ?></span>
				</button>
				<nav class="article-toc__panel" aria-label="<?php esc_attr_e( 'Навігація по статті', 'ykagro' ); ?>">
					<p class="article-toc__title"><?php esc_html_e( 'Читайте у цій статті', 'ykagro' ); ?></p>
					<ul class="article-toc__list"></ul>
				</nav>
			</aside>
		</div>
	</article>

	<?php if ( $yka_has_author ) { ?>
		<div class="spacer-xl"></div>
		<?php get_template_part( 'template-parts/single/article-author', null, [ 'author_id' => $yka_author_id ] ); ?>
	<?php } ?>

	<div class="spacer-xl"></div>
	<?php get_template_part( 'template-parts/single/article-related' ); ?>

	<div class="spacer-xl"></div>
</main>

<?php
get_footer();
