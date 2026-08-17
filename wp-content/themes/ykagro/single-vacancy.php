<?php
/**
 * Single vacancy — description on the left, sticky application form on the right.
 *
 * @package ykagro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

the_post();

$yka_meta         = get_field( 'meta' );
$yka_lead         = (string) get_field( 'lead' );
$yka_duties_title = (string) get_field( 'duties_title' );
$yka_duties       = get_field( 'duties' );
$yka_offer_title  = (string) get_field( 'offer_title' );
$yka_offer        = get_field( 'offer' );

$yka_list_page = get_page_by_path( 'career' );
$yka_trail     = [];

if ( $yka_list_page ) {
	$yka_trail[] = [
		'label' => get_the_title( $yka_list_page ),
		'href'  => get_permalink( $yka_list_page ),
	];
}

$yka_trail[] = [ 'label' => get_the_title() ];

/**
 * Renders one "title + bullet list" block.
 *
 * @param string $title Block heading.
 * @param mixed  $rows  Repeater rows, each ['text' => string].
 */
$yka_list_block = static function ( string $title, $rows ): void {
	if ( ! is_array( $rows ) || empty( $rows ) ) {
		return;
	}
	?>
	<div class="detail__block">
		<?php if ( ! empty( $title ) ) { ?>
			<h2 class="detail__block-title h6"><?php echo esc_html( $title ); ?></h2>
		<?php } ?>
		<ul class="detail__list text-lg">
			<?php
			foreach ( $rows as $row ) {
				if ( empty( $row['text'] ) ) {
					continue;
				}
				?>
				<li><?php echo esc_html( $row['text'] ); ?></li>
				<?php
			}
			?>
		</ul>
	</div>
	<?php
};
?>

<main class="career-detail-page is-doc">
	<?php get_template_part( 'includes/scroll-line', null, [ 'class' => 'scroll-line--blog' ] ); ?>

	<?php get_template_part( 'template-parts/components/page-head', null, [ 'items' => $yka_trail ] ); ?>

	<div class="container">
		<div class="detail">
			<div class="detail__main">
				<div class="detail__head">
					<h1 class="detail__title h3 clr-black"><?php the_title(); ?></h1>
					<?php if ( is_array( $yka_meta ) && ! empty( $yka_meta ) ) { ?>
						<ul class="detail__meta">
							<?php
							foreach ( $yka_meta as $yka_meta_item ) {
								if ( empty( $yka_meta_item['text'] ) ) {
									continue;
								}
								?>
								<li class="text-sm fw-500">
									<span class="detail__meta-icon"><?php yka_icon( 'icons/pin.svg' ); ?></span>
									<?php echo esc_html( $yka_meta_item['text'] ); ?>
								</li>
								<?php
							}
							?>
						</ul>
					<?php } ?>
				</div>

				<?php if ( ! empty( $yka_lead ) ) { ?>
					<div class="detail__body text-lg clr-muted">
						<?php echo wp_kses_post( $yka_lead ); ?>
					</div>
				<?php } ?>

				<?php
				$yka_list_block( $yka_duties_title, $yka_duties );
				$yka_list_block( $yka_offer_title, $yka_offer );
				?>
			</div>

			<aside class="detail__aside">
				<div class="detail-form">
					<h2 class="detail-form__title h5 clr-text"><?php esc_html_e( 'Станьте частиною нашої команди професіоналів', 'ykagro' ); ?></h2>
					<?php get_template_part( 'template-parts/form-block', null, [ 'file' => true ] ); ?>
				</div>
			</aside>
		</div>
	</div>

	<div class="spacer-xl"></div>
</main>

<?php
get_footer();
