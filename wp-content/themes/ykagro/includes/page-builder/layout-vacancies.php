<?php
/**
 * Page builder layout: vacancies — heading + two-column grid of vacancy cards.
 *
 * Pulls every published vacancy; ordering comes from the "Порядок" field so an
 * editor can promote a role without touching dates.
 *
 * @package ykagro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title  = (string) get_sub_field( 'title' );
$anchor = sanitize_title( (string) get_sub_field( 'anchor' ) );

$vacancy_ids = get_posts(
	[
		'post_type'      => 'vacancy',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'orderby'        => [ 'menu_order' => 'ASC', 'date' => 'DESC' ],
		'fields'         => 'ids',
	]
);

if ( empty( $vacancy_ids ) ) {
	return;
}
?>
<section class="vacancies"<?php echo ! empty( $anchor ) ? ' id="' . esc_attr( $anchor ) . '"' : ''; ?>>
	<div class="vacancies__inner">
		<?php if ( ! empty( $title ) ) { ?>
			<h2 class="vacancies__title h2 clr-black"><?php echo esc_html( $title ); ?></h2>
		<?php } ?>

		<div class="vacancies__grid">
			<?php
			foreach ( $vacancy_ids as $vacancy_id ) {
				$vacancy_title = get_the_title( $vacancy_id );
				$permalink     = get_permalink( $vacancy_id );
				$meta          = get_field( 'meta', $vacancy_id );
				$lead          = (string) get_field( 'lead', $vacancy_id );
				?>
				<article class="vacancy-card">
					<div class="vacancy-card__head">
						<div class="vacancy-card__top">
							<h3 class="vacancy-card__title h6">
								<a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $vacancy_title ); ?></a>
							</h3>
							<a href="<?php echo esc_url( $permalink ); ?>" class="vacancy-card__link" aria-label="<?php
								/* translators: %s: vacancy title. */
								echo esc_attr( sprintf( __( 'Детальніше — %s', 'ykagro' ), $vacancy_title ) );
							?>">
								<span class="icon"><?php yka_icon( 'icons/arrow-diagonal.svg' ); ?></span>
							</a>
						</div>
						<?php if ( is_array( $meta ) && ! empty( $meta ) ) { ?>
							<ul class="vacancy-card__meta">
								<?php
								foreach ( $meta as $meta_item ) {
									if ( empty( $meta_item['text'] ) ) {
										continue;
									}
									?>
									<li class="text-sm fw-500">
										<span class="vacancy-card__meta-icon"><?php yka_icon( 'icons/pin.svg' ); ?></span>
										<?php echo esc_html( $meta_item['text'] ); ?>
									</li>
									<?php
								}
								?>
							</ul>
						<?php } ?>
					</div>
					<?php if ( ! empty( $lead ) ) { ?>
						<p class="vacancy-card__text text-lg clr-muted"><?php echo esc_html( wp_trim_words( wp_strip_all_tags( $lead ), 22, '…' ) ); ?></p>
					<?php } ?>
				</article>
				<?php
			}
			?>
		</div>
	</div>
</section>
