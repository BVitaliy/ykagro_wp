<?php
/**
 * Standalone page header for inner pages with no hero panel — logo top-left,
 * optionally followed by breadcrumbs.
 *
 * @param array $args {
 *     @type array[] $items Breadcrumb trail. Empty = no trail (404, for instance).
 * }
 *
 * @package ykagro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$items = $args['items'] ?? [];
?>
<div class="page-head container">
	<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="page-head__logo" aria-label="<?php esc_attr_e( 'YKagro — на головну', 'ykagro' ); ?>">
		<?php yka_icon( 'logo.svg' ); ?>
	</a>
	<?php if ( ! empty( $items ) ) { ?>
		<?php // scroll rail: long trails slide sideways on mobile instead of wrapping ?>
		<div class="breadcrumbs-scroll">
			<?php get_template_part( 'template-parts/components/breadcrumbs', null, [ 'items' => $items ] ); ?>
		</div>
	<?php } ?>
</div>
