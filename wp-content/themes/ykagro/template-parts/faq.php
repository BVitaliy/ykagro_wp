<?php
/**
 * FAQ section — left heading + right accordion.
 *
 * The questions live in Site Settings, not per page: the markup uses the same
 * list on the homepage and on product pages, so duplicating it would guarantee
 * drift. Shared by the page-builder block and the single templates.
 *
 * @param array $args {
 *     @type array $link ACF link array for the "Зв'язатись" button. Falls back
 *                       to the contacts page.
 * }
 *
 * @package ykagro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title = (string) get_field( 'faq_title', 'options' );
$note  = (string) get_field( 'faq_note', 'options' );
$items = get_field( 'faq_items', 'options' );

if ( ! is_array( $items ) || empty( $items ) ) {
	return;
}

$link  = $args['link'] ?? null;
$label = ! empty( $link['title'] ) ? $link['title'] : __( "Зв'язатись", 'ykagro' );
$url   = $link['url'] ?? '';

if ( empty( $url ) ) {
	$contacts = get_page_by_path( 'contacts' );
	$url      = $contacts ? get_permalink( $contacts ) : '';
}
?>
<section class="home-faq">
	<div class="container home-faq__inner">
		<div class="home-faq__head">
			<?php if ( ! empty( $title ) ) { ?>
				<h2 class="home-faq__title h3 clr-black"><?php echo esc_html( $title ); ?></h2>
			<?php } ?>
			<?php if ( ! empty( $note ) ) { ?>
				<p class="home-faq__note text-big clr-black"><?php echo esc_html( $note ); ?></p>
			<?php } ?>
			<?php if ( ! empty( $url ) ) { ?>
				<a href="<?php echo esc_url( $url ); ?>" class="link-more">
					<?php echo esc_html( $label ); ?>
					<span class="link-more__icon"><?php yka_icon( 'icons/arrow-diagonal.svg' ); ?></span>
				</a>
			<?php } ?>
		</div>

		<div class="home-faq__list">
			<?php
			foreach ( $items as $item ) {
				if ( empty( $item['question'] ) ) {
					continue;
				}
				?>
				<div class="faq-item js-faq-item">
					<button class="faq-item__q" type="button" aria-expanded="false">
						<span class="faq-item__icon"><?php yka_icon( 'icons/chat-question.svg' ); ?></span>
						<span class="faq-item__text"><?php echo esc_html( $item['question'] ); ?></span>
						<span class="faq-item__toggle"><?php yka_icon( 'icons/plus.svg' ); ?></span>
					</button>
					<div class="faq-item__a">
						<div class="faq-item__a-inner">
							<p class="text-lg"><?php echo esc_html( $item['answer'] ?? '' ); ?></p>
						</div>
					</div>
				</div>
				<?php
			}
			?>
		</div>
	</div>
</section>
