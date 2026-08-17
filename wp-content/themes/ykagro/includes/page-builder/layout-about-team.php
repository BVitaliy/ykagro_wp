<?php
/**
 * Page builder layout: team — header with slider arrows and profile cards.
 *
 * The detail modals are rendered here rather than in includes/modals.php: the
 * members are editable content, so their panels have to be generated from the
 * same rows as the cards.
 *
 * @package ykagro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$tag   = (string) get_sub_field( 'tag' );
$title = (string) get_sub_field( 'title' );
$items = get_sub_field( 'items' );

if ( ! is_array( $items ) || empty( $items ) ) {
	return;
}

$socials = get_field( 'footer_socials', 'options' );

// Modal ids are row-based, not name-based: sanitize_title() percent-encodes
// Cyrillic, which would make the attribute unreadable for no benefit.
$modal_id = static function ( int $index ): string {
	return 'team-' . ( $index + 1 );
};
?>
<section class="about-team">
	<div class="container">
		<div class="about-team__head">
			<div class="about-team__copy">
				<?php if ( ! empty( $tag ) ) { ?>
					<span class="tag about-team__tag"><?php echo esc_html( $tag ); ?></span>
				<?php } ?>
				<?php if ( ! empty( $title ) ) { ?>
					<h2 class="about-team__title h3 clr-black"><?php echo yka_heading( $title ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- yka_heading returns escaped HTML. ?></h2>
				<?php } ?>
			</div>

			<div class="about-team__nav" aria-label="<?php esc_attr_e( 'Навігація команди', 'ykagro' ); ?>">
				<button class="btn-round btn-round--prev about-team__arrow js-team-prev" type="button" aria-label="<?php esc_attr_e( 'Попередній учасник команди', 'ykagro' ); ?>">
					<span class="icon"><?php yka_icon( 'icons/arrow-right.svg' ); ?></span>
				</button>
				<button class="btn-round about-team__arrow js-team-next" type="button" aria-label="<?php esc_attr_e( 'Наступний учасник команди', 'ykagro' ); ?>">
					<span class="icon"><?php yka_icon( 'icons/arrow-right.svg' ); ?></span>
				</button>
			</div>
		</div>

		<div class="about-team__slider swiper js-team-slider">
			<div class="swiper-wrapper">
				<?php
				foreach ( $items as $index => $member ) {
					if ( empty( $member['name'] ) ) {
						continue;
					}

					$has_bio  = ! empty( $member['bio'] );
					$panel_id = $modal_id( $index );
					$position = ! empty( $member['position'] ) ? $member['position'] : '50% 50%';
					?>
					<article
						class="about-team__card swiper-slide"
						<?php if ( $has_bio ) { ?>
							role="button"
							tabindex="0"
							data-modal-open="<?php echo esc_attr( $panel_id ); ?>"
							aria-label="<?php
								/* translators: %s: team member name. */
								echo esc_attr( sprintf( __( 'Детальніше про %s', 'ykagro' ), $member['name'] ) );
							?>"
						<?php } ?>
					>
						<div class="about-team__card-head">
							<h3 class="about-team__name h5 clr-black"><?php echo esc_html( $member['name'] ); ?></h3>
							<?php if ( $has_bio ) { ?>
								<span class="about-team__link" aria-hidden="true">
									<span class="icon"><?php yka_icon( 'icons/arrow-diagonal.svg' ); ?></span>
								</span>
							<?php } ?>
						</div>
						<?php if ( ! empty( $member['role'] ) ) { ?>
							<p class="about-team__role text-lg clr-muted"><?php echo esc_html( $member['role'] ); ?></p>
						<?php } ?>
						<picture class="about-team__photo">
							<?php
							if ( ! empty( $member['photo']['ID'] ) ) {
								echo wp_get_attachment_image(
									(int) $member['photo']['ID'],
									'yka-team',
									false,
									[
										'alt'      => $member['photo']['alt'] ?: $member['name'],
										'loading'  => 'lazy',
										'decoding' => 'async',
										'style'    => 'object-position: ' . esc_attr( $position ) . ';',
									]
								);
							}
							?>
						</picture>
					</article>
					<?php
				}
				?>
			</div>
			<div class="about-team__pagination swiper-pagination"></div>
		</div>
	</div>
</section>

<?php
// Detail panels — YKModal picks them up by data-modal.
foreach ( $items as $index => $member ) {
	if ( empty( $member['name'] ) || empty( $member['bio'] ) ) {
		continue;
	}

	$panel_id = $modal_id( $index );
	?>
	<div class="modal__panel modal__panel--team" data-modal="<?php echo esc_attr( $panel_id ); ?>" data-lenis-prevent>
		<button class="modal__close js-modal-close" type="button" aria-label="<?php esc_attr_e( 'Закрити', 'ykagro' ); ?>">
			<?php yka_icon( 'icons/close.svg' ); ?>
		</button>
		<div class="modal__team">
			<picture class="modal__team-photo">
				<?php
				if ( ! empty( $member['photo']['ID'] ) ) {
					echo wp_get_attachment_image(
						(int) $member['photo']['ID'],
						'large',
						false,
						[
							'alt'      => $member['photo']['alt'] ?: $member['name'],
							'loading'  => 'lazy',
							'decoding' => 'async',
						]
					);
				}
				?>
			</picture>

			<div class="modal__team-body">
				<div class="modal__team-content">
					<div class="modal__team-head">
						<h2 class="modal__team-name h4 clr-black"><?php echo esc_html( $member['name'] ); ?></h2>
						<?php if ( ! empty( $member['role'] ) ) { ?>
							<p class="modal__team-role text-lg clr-text"><?php echo esc_html( $member['role'] ); ?></p>
						<?php } ?>
					</div>
					<div class="modal__team-text text-lg clr-muted">
						<?php echo wp_kses_post( $member['bio'] ); ?>
					</div>
				</div>

				<?php if ( is_array( $socials ) && ! empty( $socials ) ) { ?>
					<div class="modal__team-socials" aria-label="<?php esc_attr_e( 'Соціальні мережі', 'ykagro' ); ?>">
						<?php
						foreach ( $socials as $social ) {
							if ( empty( $social['link'] ) || empty( $social['icon'] ) ) {
								continue;
							}
							?>
							<a href="<?php echo esc_url( $social['link'] ); ?>" class="modal__team-social" aria-label="<?php echo esc_attr( $social['label'] ?? '' ); ?>" target="_blank" rel="noopener noreferrer">
								<?php yka_icon( 'icons/' . sanitize_file_name( $social['icon'] ) . '.svg' ); ?>
							</a>
							<?php
						}
						?>
					</div>
				<?php } ?>
			</div>
		</div>
	</div>
	<?php
}
