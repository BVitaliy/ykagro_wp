<?php
/**
 * Nav menu walkers.
 *
 * The design uses two flat, one-level menus with no WordPress-generated classes,
 * so the default walker's markup would fight the stylesheet. These emit exactly
 * what the static markup did.
 *
 * @package ykagro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Floating panel menu: bare anchors, no <li> wrapper.
 *
 * Produces: <a class="menu__link" href="...">Label</a>
 * Use with 'items_wrap' => '%3$s'.
 */
class YKA_Nav_Walker extends Walker_Nav_Menu {

	/**
	 * Class applied to every anchor.
	 */
	private string $link_class;

	public function __construct( string $link_class = 'menu__link' ) {
		$this->link_class = $link_class;
	}

	/**
	 * No <ul> wrappers — the menu is flat by design.
	 */
	public function start_lvl( &$output, $depth = 0, $args = null ) {}

	public function end_lvl( &$output, $depth = 0, $args = null ) {}

	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		// Only the top level is rendered; children would have nowhere to live.
		if ( $depth > 0 ) {
			return;
		}

		$classes = [ $this->link_class ];

		if ( ! empty( $item->classes ) && in_array( 'current-menu-item', (array) $item->classes, true ) ) {
			$classes[] = 'is-current';
		}

		$attrs = 'class="' . esc_attr( implode( ' ', $classes ) ) . '"';
		$attrs .= ' href="' . esc_url( $item->url ) . '"';

		if ( ! empty( $item->target ) ) {
			$attrs .= ' target="' . esc_attr( $item->target ) . '" rel="noopener"';
		}

		if ( ! empty( $item->attr_title ) ) {
			$attrs .= ' title="' . esc_attr( $item->attr_title ) . '"';
		}

		$output .= '<a ' . $attrs . '>' . esc_html( $item->title ) . '</a>';
	}

	public function end_el( &$output, $item, $depth = 0, $args = null ) {}
}

/**
 * Footer columns: <li><a href="...">Label</a></li>, no generated classes.
 *
 * Use with 'items_wrap' => '<ul class="footer__menu">%3$s</ul>'.
 */
class YKA_Footer_Nav_Walker extends Walker_Nav_Menu {

	public function start_lvl( &$output, $depth = 0, $args = null ) {}

	public function end_lvl( &$output, $depth = 0, $args = null ) {}

	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		if ( $depth > 0 ) {
			return;
		}

		$attrs = 'href="' . esc_url( $item->url ) . '"';

		if ( ! empty( $item->target ) ) {
			$attrs .= ' target="' . esc_attr( $item->target ) . '" rel="noopener"';
		}

		$output .= '<li><a ' . $attrs . '>' . esc_html( $item->title ) . '</a></li>';
	}

	public function end_el( &$output, $item, $depth = 0, $args = null ) {}
}
