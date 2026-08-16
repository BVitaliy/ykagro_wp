<?php
/**
 * Site header: document head, SVG sprite, and the floating menu panel.
 *
 * Ported from the markup's inc/_top.php + inc/_header.php.
 *
 * @package ykagro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<?php get_template_part( 'includes/svg-defs' ); ?>

<div class="menu js-menu">
	<div class="menu__overlay js-menu-close"></div>

	<div class="menu__dock">
		<div class="menu__panel">
			<div class="menu__panel-inner">
				<form class="menu__search" action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get" role="search">
					<span class="menu__search-icon icon"><?php yka_icon( 'icons/search.svg' ); ?></span>
					<input
						class="menu__search-input js-menu-search-input"
						type="search"
						name="s"
						value="<?php echo esc_attr( get_search_query() ); ?>"
						placeholder="<?php esc_attr_e( 'Пошук', 'ykagro' ); ?>"
						aria-label="<?php esc_attr_e( 'Пошук', 'ykagro' ); ?>"
					>
				</form>

				<?php if ( has_nav_menu( 'main_menu' ) ) { ?>
					<nav class="menu__nav" aria-label="<?php esc_attr_e( 'Головне меню', 'ykagro' ); ?>">
						<?php
						wp_nav_menu(
							[
								'theme_location' => 'main_menu',
								'container'      => false,
								'items_wrap'     => '%3$s',
								'depth'          => 1,
								'walker'         => new YKA_Nav_Walker( 'menu__link' ),
							]
						);
						?>
					</nav>
				<?php } ?>
			</div>
		</div>

		<div class="menu__bar">
			<button class="menu__pill menu__pill--main js-menu-toggle" type="button" aria-label="<?php esc_attr_e( 'Меню', 'ykagro' ); ?>">
				<span class="menu__toggle-icon">
					<span class="menu__icon-open icon"><?php yka_icon( 'icons/menu.svg' ); ?></span>
					<span class="menu__icon-close icon"><?php yka_icon( 'icons/close.svg' ); ?></span>
				</span>
				<span class="menu__label"><?php esc_html_e( 'Меню', 'ykagro' ); ?></span>
			</button>
		</div>
	</div>
</div>
