<?php
/**
 * 404.
 *
 * <main> is the clip parent: tall enough for the sack to bleed under the footer,
 * with overflow-x only. The decorative lines draw themselves on load.
 *
 * @package ykagro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$yka_home_page = (int) get_option( 'page_on_front' );
?>

<div class="error-404__lines" aria-hidden="true">
	<div class="error-404__line error-404__line--left">
		<?php yka_icon( 'errors/404-line-b.svg' ); ?>
	</div>
	<div class="error-404__line error-404__line--right">
		<?php yka_icon( 'errors/404-line-a.svg' ); ?>
	</div>
</div>

<main class="error-404-shell is-404">
	<?php // No breadcrumbs here — there is no trail to a page that does not exist. ?>
	<?php get_template_part( 'template-parts/components/page-head' ); ?>

	<div class="error-404__media" aria-hidden="true">
		<div class="error-404__sack">
			<?php yka_icon( 'errors/404-sack.svg' ); ?>
		</div>
		<div class="error-404__rooster">
			<?php yka_icon( 'errors/404-rooster.svg' ); ?>
		</div>
	</div>

	<section class="error-404" aria-label="<?php esc_attr_e( 'Сторінку не знайдено', 'ykagro' ); ?>">
		<div class="error-404__inner">
			<div class="error-404__content">
				<span class="tag error-404__tag">404</span>
				<h1 class="error-404__title h2" data-title-anim><?php esc_html_e( 'Упсссссс....', 'ykagro' ); ?></h1>
				<p class="error-404__text text-lg"><?php esc_html_e( 'Вибачте, але сторінку яку ви намагались знайти — не існує. Пропонуємо вам перейти на головну сторінку.', 'ykagro' ); ?></p>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn">
					<span class="btn__icon"><?php yka_icon( 'icons/arrow-diagonal.svg' ); ?></span>
					<?php esc_html_e( 'На головну', 'ykagro' ); ?>
				</a>
			</div>
		</div>
	</section>
</main>

<?php
get_footer();
