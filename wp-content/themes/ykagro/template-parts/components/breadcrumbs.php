<?php
/**
 * Breadcrumbs — home icon + N levels, the last one is the current page.
 *
 * Called with an explicit trail so each template stays in control of its own
 * hierarchy (a product's trail skips the category level, for instance).
 *
 * @param array $args {
 *     @type array[] $items Each ['label' => string, 'href' => string]. Omit
 *                          'href' on the last item.
 * }
 *
 * @package ykagro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$items = $args['items'] ?? [];

if ( ! is_array( $items ) || empty( $items ) ) {
	return;
}
?>
<nav class="breadcrumbs" aria-label="<?php esc_attr_e( 'Хлібні крихти', 'ykagro' ); ?>">
	<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="breadcrumbs__home" aria-label="<?php esc_attr_e( 'Головна', 'ykagro' ); ?>">
		<?php yka_icon( 'icons/home.svg' ); ?>
	</a>
	<?php
	foreach ( $items as $item ) {
		if ( empty( $item['label'] ) ) {
			continue;
		}
		?>
		<span class="breadcrumbs__dot" aria-hidden="true"></span>
		<?php if ( ! empty( $item['href'] ) ) { ?>
			<a href="<?php echo esc_url( $item['href'] ); ?>" class="breadcrumbs__link"><?php echo esc_html( $item['label'] ); ?></a>
		<?php } else { ?>
			<span class="breadcrumbs__current"><?php echo esc_html( $item['label'] ); ?></span>
		<?php } ?>
		<?php
	}
	?>
</nav>
