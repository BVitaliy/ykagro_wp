<?php
/**
 * Fallback template.
 *
 * Every context that has no more specific template lands here. Dedicated
 * templates (front-page.php, single-*.php, archive-*.php, templates/*.php) are
 * added as each section of the markup is integrated.
 *
 * @package ykagro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main class="is-doc">
	<?php get_template_part( 'includes/scroll-line' ); ?>

	<div class="container">
		<?php if ( have_posts() ) { ?>
			<div class="spacer-xl"></div>

			<?php if ( is_search() ) { ?>
				<h1 class="h2 clr-black">
					<?php
					/* translators: %s: search query. */
					printf( esc_html__( 'Результати пошуку: %s', 'ykagro' ), '<span>' . esc_html( get_search_query() ) . '</span>' );
					?>
				</h1>
			<?php } elseif ( ! is_front_page() ) { ?>
				<h1 class="h2 clr-black"><?php echo esc_html( wp_strip_all_tags( get_the_archive_title() ) ); ?></h1>
			<?php } ?>

			<div class="spacer-md"></div>

			<?php
			while ( have_posts() ) {
				the_post();
				?>
				<article <?php post_class(); ?>>
					<h2 class="h4 clr-black">
						<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
					</h2>
					<p class="text-md clr-muted"><?php echo esc_html( yka_excerpt() ); ?></p>
				</article>
				<div class="spacer-sm"></div>
				<?php
			}

			the_posts_pagination(
				[
					'mid_size'  => 1,
					'prev_text' => __( 'Назад', 'ykagro' ),
					'next_text' => __( 'Далі', 'ykagro' ),
				]
			);
			?>
		<?php } else { ?>
			<div class="spacer-xl"></div>
			<h1 class="h2 clr-black"><?php esc_html_e( 'Нічого не знайдено', 'ykagro' ); ?></h1>
			<p class="text-md clr-muted"><?php esc_html_e( 'Спробуйте змінити запит або перейдіть на головну сторінку.', 'ykagro' ); ?></p>
		<?php } ?>
	</div>

	<div class="spacer-xl"></div>
</main>

<?php
get_footer();
