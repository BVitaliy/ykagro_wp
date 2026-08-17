<?php
/**
 * Article author block.
 *
 * Photo comes from the user's ACF `author_photo` field, falling back to the
 * Gravatar-backed avatar so the slot is never empty.
 *
 * @param array $args {
 *     @type int $author_id User ID.
 * }
 *
 * @package ykagro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$author_id = ! empty( $args['author_id'] ) ? (int) $args['author_id'] : 0;

if ( ! $author_id ) {
	return;
}

$name  = get_the_author_meta( 'display_name', $author_id );
$bio   = get_the_author_meta( 'description', $author_id );
$photo = function_exists( 'get_field' ) ? get_field( 'author_photo', 'user_' . $author_id ) : null;
?>
<section class="article-author">
	<div class="article-author__inner">
		<div class="article-author__photo">
			<picture>
				<?php
				if ( ! empty( $photo['ID'] ) ) {
					echo wp_get_attachment_image(
						(int) $photo['ID'],
						'yka-team',
						false,
						[ 'alt' => $name, 'loading' => 'lazy', 'decoding' => 'async' ]
					);
				} else {
					echo get_avatar( $author_id, 512, '', $name, [ 'loading' => 'lazy' ] );
				}
				?>
			</picture>
		</div>

		<div class="article-author__body">
			<div class="article-author__top">
				<h2 class="article-author__title h3"><?php esc_html_e( 'Про автора', 'ykagro' ); ?></h2>
				<?php if ( ! empty( $bio ) ) { ?>
					<p class="article-author__text text-lg"><?php echo esc_html( $bio ); ?></p>
				<?php } ?>
				<a href="<?php echo esc_url( get_author_posts_url( $author_id ) ); ?>" class="btn article-author__cta">
					<span class="btn__icon"><?php yka_icon( 'icons/arrow-diagonal.svg' ); ?></span>
					<?php esc_html_e( 'Детальніше', 'ykagro' ); ?>
				</a>
			</div>

			<?php
			$socials = function_exists( 'get_field' ) ? get_field( 'author_socials', 'user_' . $author_id ) : [];

			if ( is_array( $socials ) && ! empty( $socials ) ) {
				?>
				<div class="article-author__socials">
					<?php
					foreach ( $socials as $social ) {
						if ( empty( $social['link'] ) || empty( $social['icon'] ) ) {
							continue;
						}
						?>
						<a href="<?php echo esc_url( $social['link'] ); ?>" class="article-author__social" aria-label="<?php echo esc_attr( $social['label'] ?? '' ); ?>" target="_blank" rel="noopener noreferrer">
							<?php yka_icon( 'icons/' . sanitize_file_name( $social['icon'] ) . '.svg' ); ?>
						</a>
						<?php
					}
					?>
				</div>
				<?php
			}
			?>
		</div>
	</div>
</section>
