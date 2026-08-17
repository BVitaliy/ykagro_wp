<?php
/**
 * Direction card — title + counter badge + text + "детальніше" + line icon.
 *
 * @param array $args {
 *     @type int    $post_id Direction post. Supplies title, text, icon and link.
 *     @type string $num     Zero-padded index, e.g. '01'.
 *     @type string $total   Zero-padded total, e.g. '05'.
 * }
 *
 * @package ykagro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_id = ! empty( $args['post_id'] ) ? (int) $args['post_id'] : 0;

if ( ! $post_id ) {
	return;
}

$title       = get_the_title( $post_id );
$text        = (string) get_field( 'card_text', $post_id );
$icon        = (string) get_field( 'icon', $post_id );
$custom_icon = get_field( 'custom_icon', $post_id );
$num         = $args['num'] ?? '';
$total       = $args['total'] ?? '';

if ( empty( $icon ) ) {
	$icon = 'direction-broiler';
}
?>
<article class="direction-card">
	<div class="direction-card__head">
		<h3 class="direction-card__title h5"><?php echo esc_html( $title ); ?></h3>
		<?php if ( ! empty( $num ) && ! empty( $total ) ) { ?>
			<span class="direction-card__count"><?php echo esc_html( $num ); ?> - <?php echo esc_html( $total ); ?></span>
		<?php } ?>
	</div>
	<div class="direction-card__body">
		<?php if ( ! empty( $text ) ) { ?>
			<p class="direction-card__text text-lg"><?php echo esc_html( $text ); ?></p>
		<?php } ?>
		<a href="<?php echo esc_url( get_permalink( $post_id ) ); ?>" class="btn btn--soft direction-card__more">
			<span class="btn__icon"><?php yka_icon( 'icons/arrow-diagonal.svg' ); ?></span>
			<?php esc_html_e( 'Детальніше', 'ykagro' ); ?>
		</a>
	</div>
	<span class="direction-card__icon"><?php yka_icon_field( $custom_icon, 'icons/' . sanitize_file_name( $icon ) . '.svg', $title ); ?></span>
</article>
