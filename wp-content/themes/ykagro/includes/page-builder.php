<?php
/**
 * Page builder renderer.
 *
 * Included by front-page.php and templates/page-builder.php. Expects to run
 * inside the WP template context (get_header already called).
 *
 * Dispatcher only — no HTML lives here. One partial per layout, loaded with
 * `require` so a missing file is a fatal error rather than a silently empty
 * section. See .claude/acf-flexible-content.md.
 *
 * @package ykagro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'have_rows' ) || ! have_rows( 'page_builder' ) ) {
	return;
}

$yka_partials_dir = YKA_DIR . '/includes/page-builder/';
$yka_row_index    = 0;

while ( have_rows( 'page_builder' ) ) {
	the_row();

	$yka_layout = get_row_layout();

	if ( get_sub_field( $yka_layout . '_hide' ) ) {
		continue;
	}

	// The markup separates every section with a spacer, except before the hero.
	if ( $yka_row_index > 0 && 'hero' !== $yka_layout ) {
		echo '<div class="spacer-xl"></div>';
	}

	switch ( $yka_layout ) {
		case 'hero':                require $yka_partials_dir . 'layout-hero.php';                break;
		case 'page_hero':           require $yka_partials_dir . 'layout-page-hero.php';           break;
		case 'video_band':          require $yka_partials_dir . 'layout-video-band.php';          break;
		case 'about_scene':         require $yka_partials_dir . 'layout-about-scene.php';         break;
		case 'about_story':         require $yka_partials_dir . 'layout-about-story.php';         break;
		case 'about_team':          require $yka_partials_dir . 'layout-about-team.php';          break;
		case 'production_steps':    require $yka_partials_dir . 'layout-production-steps.php';    break;
		case 'benefits':            require $yka_partials_dir . 'layout-benefits.php';            break;
		case 'vacancies':           require $yka_partials_dir . 'layout-vacancies.php';           break;
		case 'contacts_hero':       require $yka_partials_dir . 'layout-contacts-hero.php';       break;
		case 'intro':               require $yka_partials_dir . 'layout-intro.php';               break;
		case 'directions':          require $yka_partials_dir . 'layout-directions.php';          break;
		case 'directions_list':     require $yka_partials_dir . 'layout-directions-list.php';     break;
		case 'products':            require $yka_partials_dir . 'layout-products.php';            break;
		case 'products_categories': require $yka_partials_dir . 'layout-products-categories.php'; break;
		case 'stats':               require $yka_partials_dir . 'layout-stats.php';               break;
		case 'stats_cards':         require $yka_partials_dir . 'layout-stats-cards.php';         break;
		case 'gallery':             require $yka_partials_dir . 'layout-gallery.php';             break;
		case 'statement':           require $yka_partials_dir . 'layout-statement.php';           break;
		case 'news':                require $yka_partials_dir . 'layout-news.php';                break;
		case 'contact':             require $yka_partials_dir . 'layout-contact.php';             break;
		case 'form_section':        require $yka_partials_dir . 'layout-form-section.php';        break;
		case 'comfort':             require $yka_partials_dir . 'layout-comfort.php';             break;
		case 'doc':                 require $yka_partials_dir . 'layout-doc.php';                 break;
		case 'cta_band':            require $yka_partials_dir . 'layout-cta-band.php';            break;
		case 'faq':                 require $yka_partials_dir . 'layout-faq.php';                 break;
		case 'articles':            require $yka_partials_dir . 'layout-articles.php';            break;
	}

	++$yka_row_index;
}
