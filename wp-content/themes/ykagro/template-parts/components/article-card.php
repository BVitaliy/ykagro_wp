<?php
/**
 * Article card — image + title + date row.
 *
 * @param array $args {
 *     @type int $post_id Post to render. Defaults to the current post.
 * }
 *
 * @package ykagro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_id = ! empty( $args['post_id'] ) ? (int) $args['post_id'] : get_the_ID();

if ( ! $post_id ) {
	return;
}

$title = get_the_title( $post_id );
?>
<a class="article-card" href="<?php echo esc_url( get_permalink( $post_id ) ); ?>">
	<span class="article-card__media">
		<?php
		if ( has_post_thumbnail( $post_id ) ) {
			echo get_the_post_thumbnail(
				$post_id,
				'yka-article',
				[
					'alt'      => $title,
					'loading'  => 'lazy',
					'decoding' => 'async',
				]
			);
		}
		?>
	</span>
	<span class="article-card__title h6 clr-black"><?php echo esc_html( $title ); ?></span>
	<span class="article-card__foot">
		<span class="article-card__date text-sm clr-gray">
			<span class="article-card__cal"><?php yka_icon( 'icons/calendar.svg' ); ?></span>
			<?php echo esc_html( get_the_date( 'j F Y \р', $post_id ) ); ?>
		</span>
		<span class="article-card__icon"><?php yka_icon( 'icons/arrow-diagonal.svg' ); ?></span>
	</span>
</a>
