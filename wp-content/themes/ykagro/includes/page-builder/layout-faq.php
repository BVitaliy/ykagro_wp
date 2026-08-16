<?php
/**
 * Page builder layout: FAQ — left heading + right accordion.
 *
 * The questions live in Site Settings, not in the block: the markup uses the
 * same list on the homepage and on product pages, so duplicating it per page
 * would guarantee drift.
 *
 * @package ykagro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$link = get_sub_field( 'link' );

$faq_title = (string) get_field( 'faq_title', 'options' );
$faq_note  = (string) get_field( 'faq_note', 'options' );
$faq_items = get_field( 'faq_items', 'options' );

if ( ! is_array( $faq_items ) || empty( $faq_items ) ) {
	return;
}
?>
<section class="home-faq">
	<div class="container home-faq__inner">
		<div class="home-faq__head">
			<?php if ( ! empty( $faq_title ) ) { ?>
				<h2 class="home-faq__title h3 clr-black"><?php echo esc_html( $faq_title ); ?></h2>
			<?php } ?>
			<?php if ( ! empty( $faq_note ) ) { ?>
				<p class="home-faq__note text-big clr-black"><?php echo esc_html( $faq_note ); ?></p>
			<?php } ?>
			<?php if ( ! empty( $link['url'] ) ) { ?>
				<a href="<?php echo esc_url( $link['url'] ); ?>" class="link-more">
					<?php echo esc_html( ! empty( $link['title'] ) ? $link['title'] : __( 'Зв\'язатись', 'ykagro' ) ); ?>
					<span class="link-more__icon"><?php yka_icon( 'icons/arrow-diagonal.svg' ); ?></span>
				</a>
			<?php } ?>
		</div>

		<div class="home-faq__list">
			<?php
			foreach ( $faq_items as $item ) {
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
