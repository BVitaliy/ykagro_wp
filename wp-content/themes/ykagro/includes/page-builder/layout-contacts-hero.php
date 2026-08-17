<?php
/**
 * Page builder layout: contacts hero — title + socials + address cards on the
 * left, map frame on the right.
 *
 * Cards, socials and pin coordinates all come from Site Settings, so the same
 * data feeds the footer, the cards and the map pins instead of being retyped.
 *
 * @package ykagro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title     = (string) get_sub_field( 'title' );
$map_mode  = (string) get_sub_field( 'map_mode' );
$map_image = get_sub_field( 'map_image' );

$cards   = get_field( 'contact_cards', 'options' );
$socials = get_field( 'footer_socials', 'options' );
$api_key = (string) get_field( 'google_maps_key', 'options' );

// A live map without a key would just render an empty grey box.
if ( 'live' === $map_mode && empty( $api_key ) ) {
	$map_mode = 'none';
}

if ( 'static' === $map_mode && empty( $map_image['ID'] ) ) {
	$map_mode = 'none';
}

/**
 * Builds a Google Maps route URL for a card.
 *
 * @param array $card Contact card row.
 */
$route_url = static function ( array $card ): string {
	$query = ! empty( $card['address_query'] ) ? $card['address_query'] : ( $card['address'] ?? '' );

	if ( empty( $query ) ) {
		return '';
	}

	return 'https://maps.google.com/?q=' . rawurlencode( $query );
};

/**
 * Renders the shared contact rows (address, phone, email, schedule).
 *
 * @param array  $card  Contact card row.
 * @param string $route Route URL.
 */
$contact_rows = static function ( array $card, string $route ): void {
	?>
	<ul class="contacts-card__list">
		<?php if ( ! empty( $card['address'] ) ) { ?>
			<li class="contacts-card__row">
				<span class="contacts-card__icon icon"><?php yka_icon( 'icons/pin.svg' ); ?></span>
				<?php if ( ! empty( $route ) ) { ?>
					<a class="text-md fw-500" href="<?php echo esc_url( $route ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $card['address'] ); ?></a>
				<?php } else { ?>
					<span class="text-md fw-500"><?php echo esc_html( $card['address'] ); ?></span>
				<?php } ?>
			</li>
		<?php } ?>
		<?php if ( ! empty( $card['phone'] ) ) { ?>
			<li class="contacts-card__row">
				<span class="contacts-card__icon icon"><?php yka_icon( 'icons/phone.svg' ); ?></span>
				<a class="text-md fw-500" href="tel:<?php echo esc_attr( preg_replace( '/[^\d+]/', '', $card['phone'] ) ); ?>"><?php echo esc_html( $card['phone'] ); ?></a>
			</li>
		<?php } ?>
		<?php if ( ! empty( $card['email'] ) && is_email( $card['email'] ) ) { ?>
			<li class="contacts-card__row">
				<span class="contacts-card__icon icon"><?php yka_icon( 'icons/email.svg' ); ?></span>
				<a class="text-md fw-500" href="mailto:<?php echo esc_attr( $card['email'] ); ?>"><?php echo esc_html( $card['email'] ); ?></a>
			</li>
		<?php } ?>
		<?php if ( ! empty( $card['schedule'] ) ) { ?>
			<li class="contacts-card__row">
				<span class="contacts-card__icon icon"><?php yka_icon( 'icons/clock.svg' ); ?></span>
				<span class="text-md fw-500"><?php echo esc_html( $card['schedule'] ); ?></span>
			</li>
		<?php } ?>
	</ul>
	<?php
};
?>
<?php // This page has no hero banner in the markup — just logo + breadcrumbs above the block. ?>
<?php get_template_part( 'template-parts/components/page-head', null, [ 'items' => [ [ 'label' => get_the_title() ] ] ] ); ?>

<section class="contacts-hero" aria-label="<?php esc_attr_e( 'Контакти', 'ykagro' ); ?>">
	<div class="contacts-hero__inner">
		<div class="contacts-hero__intro">
			<?php if ( ! empty( $title ) ) { ?>
				<h1 class="contacts-hero__title h3"><?php echo yka_heading( $title ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- yka_heading returns escaped HTML. ?></h1>
			<?php } ?>

			<?php if ( is_array( $socials ) && ! empty( $socials ) ) { ?>
				<div class="contacts-hero__socials">
					<?php
					foreach ( $socials as $social ) {
						if ( empty( $social['link'] ) || empty( $social['icon'] ) ) {
							continue;
						}
						?>
						<a href="<?php echo esc_url( $social['link'] ); ?>" class="contacts-hero__social" aria-label="<?php echo esc_attr( $social['label'] ?? '' ); ?>" target="_blank" rel="noopener">
							<span class="icon"><?php yka_icon( 'icons/' . sanitize_file_name( $social['icon'] ) . '.svg' ); ?></span>
						</a>
						<?php
					}
					?>
				</div>
			<?php } ?>

			<?php if ( is_array( $cards ) && ! empty( $cards ) ) { ?>
				<div class="contacts-hero__cards">
					<?php
					foreach ( $cards as $card ) {
						if ( empty( $card['title'] ) ) {
							continue;
						}
						?>
						<article class="contacts-card">
							<h2 class="contacts-card__title" data-title-anim="off"><?php echo esc_html( $card['title'] ); ?></h2>
							<?php $contact_rows( $card, $route_url( $card ) ); ?>
						</article>
						<?php
					}
					?>
				</div>
			<?php } ?>
		</div>

		<?php if ( 'none' !== $map_mode && is_array( $cards ) && ! empty( $cards ) ) { ?>
			<div class="contacts-map contacts-map--<?php echo esc_attr( $map_mode ); ?>" data-contacts-map>
				<div class="contacts-map__frame">
					<?php if ( 'static' === $map_mode ) { ?>
						<picture class="contacts-map__image">
							<?php
							echo wp_get_attachment_image(
								(int) $map_image['ID'],
								'full',
								false,
								[
									'alt'      => __( 'Розташування підрозділів YKagro на карті', 'ykagro' ),
									'loading'  => 'lazy',
									'decoding' => 'async',
								]
							);
							?>
						</picture>
					<?php } else { ?>
						<div class="contacts-map__canvas" data-contacts-map-canvas role="region" aria-label="<?php esc_attr_e( 'Google Maps — розташування YKagro', 'ykagro' ); ?>"></div>
					<?php } ?>

					<?php
					foreach ( $cards as $index => $card ) {
						if ( empty( $card['title'] ) ) {
							continue;
						}

						$pin_id   = 'point-' . ( $index + 1 );
						$position = 'static' === $map_mode
							? sprintf( 'left: %s%%; top: %s%%;', (float) ( $card['pin_x'] ?? 0 ), (float) ( $card['pin_y'] ?? 0 ) )
							: '';
						?>
						<button
							class="contacts-map__pin"
							type="button"
							data-contacts-map-pin="<?php echo esc_attr( $pin_id ); ?>"
							data-lat="<?php echo esc_attr( (string) ( $card['lat'] ?? '' ) ); ?>"
							data-lng="<?php echo esc_attr( (string) ( $card['lng'] ?? '' ) ); ?>"
							<?php echo ! empty( $position ) ? 'style="' . esc_attr( $position ) . '"' : ''; ?>
							aria-expanded="false"
							aria-controls="map-popup-<?php echo esc_attr( $pin_id ); ?>"
							aria-label="<?php
								/* translators: %s: contact point title. */
								echo esc_attr( sprintf( __( '%s — показати контакти', 'ykagro' ), $card['title'] ) );
							?>">
							<?php yka_icon( 'icons/map-pin.svg' ); ?>
						</button>
						<?php
					}

					foreach ( $cards as $index => $card ) {
						if ( empty( $card['title'] ) ) {
							continue;
						}

						$pin_id   = 'point-' . ( $index + 1 );
						$route    = $route_url( $card );
						$position = 'static' === $map_mode
							? sprintf( 'left: %s%%; top: %s%%;', (float) ( $card['pin_x'] ?? 0 ), (float) ( $card['pin_y'] ?? 0 ) )
							: '';
						?>
						<div
							class="contacts-map__popup is-hidden"
							id="map-popup-<?php echo esc_attr( $pin_id ); ?>"
							data-contacts-map-popup="<?php echo esc_attr( $pin_id ); ?>"
							<?php echo ! empty( $position ) ? 'style="' . esc_attr( $position ) . '"' : ''; ?>
							role="dialog"
							aria-label="<?php echo esc_attr( $card['title'] ); ?>">
							<button class="contacts-map__close" type="button" data-contacts-map-close aria-label="<?php esc_attr_e( 'Закрити', 'ykagro' ); ?>">
								<?php yka_icon( 'icons/close.svg' ); ?>
							</button>

							<h3 class="contacts-map__popup-title text-md fw-700" data-title-anim="off"><?php echo esc_html( $card['title'] ); ?></h3>

							<?php $contact_rows( $card, $route ); ?>

							<?php if ( ! empty( $route ) ) { ?>
								<a class="link-more contacts-map__route" href="<?php echo esc_url( $route ); ?>" target="_blank" rel="noopener noreferrer">
									<?php esc_html_e( 'Прокласти маршрут', 'ykagro' ); ?>
									<span class="link-more__icon"><?php yka_icon( 'icons/arrow-diagonal.svg' ); ?></span>
								</a>
							<?php } ?>
						</div>
						<?php
					}
					?>
				</div>
			</div>
		<?php } ?>
	</div>
</section>
