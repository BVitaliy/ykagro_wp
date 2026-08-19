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

// Some blocks carry their own bottom margin, so the spacer after them would
// double the gap. The banner-less page hero is one: its heading uses
// .products-title, which already sets margin-bottom.
$yka_prev_owns_gap = false;

while ( have_rows( 'page_builder' ) ) {
	the_row();

	$yka_layout = get_row_layout();

	if ( get_sub_field( $yka_layout . '_hide' ) ) {
		continue;
	}

	// A page hero with no image renders as page-head + heading, and that heading
	// owns the gap below it — matching products.php, which has no spacer there.
	$yka_owns_gap = ( 'page_hero' === $yka_layout && ! get_sub_field( 'image' ) );

	// The markup separates every section with a spacer, except before the hero
	// and after a block that already provides the gap itself.
	if ( $yka_row_index > 0 && 'hero' !== $yka_layout && ! $yka_prev_owns_gap ) {
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
		case 'directions_slider':   require $yka_partials_dir . 'layout-directions-slider.php';   break;
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
		case 'challenges':          require $yka_partials_dir . 'layout-challenges.php';          break;
	}

	$yka_prev_owns_gap = $yka_owns_gap;

	++$yka_row_index;
}
