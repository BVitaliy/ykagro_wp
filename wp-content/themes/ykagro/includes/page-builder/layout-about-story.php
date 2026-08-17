<?php
/**
 * Page builder layout: history timeline. Periods switch the photo and the copy
 * (photo wipes in top→bottom, copy pieces rise with a stagger — app-about.js).
 *
 * The body reuses the .news-block--img-left component's classes; only the
 * stacked photos and the slide wrapper are specific to this block, so the markup
 * is written out here rather than going through the shared partial.
 *
 * @package ykagro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title  = (string) get_sub_field( 'title' );
$cta    = get_sub_field( 'cta' );
$items  = get_sub_field( 'items' );
$active = (int) get_sub_field( 'active' );

if ( ! is_array( $items ) || empty( $items ) ) {
	return;
}

$items = array_values(
	array_filter(
		$items,
		static function ( $item ) {
			return ! empty( $item['period'] );
		}
	)
);

if ( empty( $items ) ) {
	return;
}

// The field is 1-based for editors; the markup indexes from 0.
$active = $active > 0 ? $active - 1 : 0;

if ( $active >= count( $items ) ) {
	$active = 0;
}
?>
<section class="about-story js-about-story" data-story-active="<?php echo esc_attr( (string) $active ); ?>">
	<div class="container">
		<div class="about-story__head">
			<?php if ( ! empty( $title ) ) { ?>
				<h2 class="about-story__title h3 clr-black"><?php echo yka_heading( $title ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- yka_heading returns escaped HTML. ?></h2>
			<?php } ?>
			<?php yka_cta( $cta, 'btn about-story__cta' ); ?>
		</div>
	</div>

	<?php // Full-width horizontal rail — finger drag must not be swallowed by Lenis. ?>
	<div class="about-story__periods" data-lenis-prevent-touch>
		<div class="container">
			<div class="about-story__periods-track" role="tablist" aria-label="<?php esc_attr_e( 'Періоди розвитку компанії', 'ykagro' ); ?>">
				<?php
				foreach ( $items as $index => $item ) {
					$is_active = $index === $active;
					?>
					<button class="about-story__period<?php echo $is_active ? ' is-active' : ''; ?>"
						type="button" role="tab" data-story-period="<?php echo esc_attr( (string) $index ); ?>"
						aria-selected="<?php echo $is_active ? 'true' : 'false'; ?>">
						<?php echo esc_html( $item['period'] ); ?>
					</button>
					<?php
				}
				?>
				<span class="about-story__period-indicator" aria-hidden="true"></span>
			</div>
		</div>
	</div>

	<div class="container">
		<div class="news-block news-block--img-left about-story__body">
			<div class="news-block__media about-story__media">
				<?php
				foreach ( $items as $index => $item ) {
					$is_active = $index === $active;
					?>
					<div class="about-story__img<?php echo $is_active ? ' is-active' : ''; ?>" data-story-img="<?php echo esc_attr( (string) $index ); ?>">
						<picture>
							<?php
							if ( ! empty( $item['image']['ID'] ) ) {
								echo wp_get_attachment_image(
									(int) $item['image']['ID'],
									'yka-news',
									false,
									[
										'alt'      => $item['image']['alt'] ?? '',
										'loading'  => $is_active ? 'eager' : 'lazy',
										'decoding' => 'async',
									]
								);
							}
							?>
						</picture>
					</div>
					<?php
				}
				?>
			</div>

			<div class="news-block__body about-story__slides">
				<?php
				foreach ( $items as $index => $item ) {
					$is_active = $index === $active;
					?>
					<div class="about-story__slide<?php echo $is_active ? ' is-active' : ''; ?>" data-story-slide="<?php echo esc_attr( (string) $index ); ?>">
						<?php if ( ! empty( $item['tag'] ) ) { ?>
							<span class="tag news-block__tag" data-story-piece><?php echo esc_html( $item['tag'] ); ?></span>
						<?php } ?>
						<?php if ( ! empty( $item['title'] ) ) { ?>
							<h3 class="h4 clr-black" data-story-piece><?php echo esc_html( $item['title'] ); ?></h3>
						<?php } ?>
						<?php if ( ! empty( $item['text'] ) ) { ?>
							<p class="text-lg clr-muted" data-story-piece><?php echo esc_html( $item['text'] ); ?></p>
						<?php } ?>
						<?php
						$slide_cta = $item['cta'] ?? null;

						if ( ! empty( $slide_cta['link']['url'] ) && ! empty( $slide_cta['label'] ) ) {
							?>
							<a href="<?php echo esc_url( $slide_cta['link']['url'] ); ?>" class="btn news-block__cta" data-story-piece>
								<span class="btn__icon"><?php yka_icon( 'icons/arrow-diagonal.svg' ); ?></span>
								<?php echo esc_html( $slide_cta['label'] ); ?>
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
	</div>
</section>
