<?php
/**
 * Page builder layout: stats — full-bleed image, heading top-left, a row of
 * figures along the bottom.
 *
 * @package ykagro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$tag    = (string) get_sub_field( 'tag' );
$title  = (string) get_sub_field( 'title' );
$lead   = (string) get_sub_field( 'lead' );
$bg     = get_sub_field( 'bg' );
$bg_mob = get_sub_field( 'bg_mob' );
$items  = get_sub_field( 'items' );
?>
<section class="home-stats">
	<div class="home-stats__inner">
		<div class="home-stats__bg">
			<picture>
				<?php if ( ! empty( $bg_mob['ID'] ) ) { ?>
					<source srcset="<?php echo esc_url( wp_get_attachment_image_url( (int) $bg_mob['ID'], 'large' ) ); ?>" media="(max-width: 767px)">
				<?php } ?>
				<?php
				if ( ! empty( $bg['ID'] ) ) {
					echo wp_get_attachment_image(
						(int) $bg['ID'],
						'full',
						false,
						[ 'alt' => '', 'loading' => 'lazy', 'decoding' => 'async' ]
					);
				}
				?>
			</picture>
			<div class="home-stats__overlay"></div>
			<div class="home-stats__scrim"></div>
		</div>

		<div class="home-stats__content">
			<div class="home-stats__head">
				<?php if ( ! empty( $tag ) ) { ?>
					<span class="tag home-stats__tag"><?php echo esc_html( $tag ); ?></span>
				<?php } ?>
				<?php if ( ! empty( $title ) ) { ?>
					<h2 class="home-stats__title clr-white"><?php echo yka_heading( $title ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- yka_heading returns escaped HTML. ?></h2>
				<?php } ?>
				<?php if ( ! empty( $lead ) ) { ?>
					<p class="home-stats__lead text-lg clr-white"><?php echo esc_html( $lead ); ?></p>
				<?php } ?>
			</div>

			<?php if ( is_array( $items ) && ! empty( $items ) ) { ?>
				<div class="home-stats__grid">
					<?php
					foreach ( $items as $item ) {
						if ( empty( $item['num'] ) ) {
							continue;
						}
						?>
						<div class="home-stats__item stat">
							<span class="stat__num clr-white"><?php echo esc_html( $item['num'] ); ?></span>
							<span class="stat__label">
								<?php if ( ! empty( $item['icon'] ) ) { ?>
									<span class="stat__icon"><?php yka_icon( 'icons/' . sanitize_file_name( $item['icon'] ) . '.svg' ); ?></span>
								<?php } ?>
								<span class="stat__text clr-white"><?php echo esc_html( $item['label'] ?? '' ); ?></span>
							</span>
						</div>
						<?php
					}
					?>
				</div>
			<?php } ?>
		</div>
	</div>
</section>
