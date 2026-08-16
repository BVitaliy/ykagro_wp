<?php
/**
 * Page builder layout: news — one or more "text + angled photo" blocks.
 *
 * Each block is its own <section> with a spacer between, matching the markup.
 *
 * @package ykagro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$items = get_sub_field( 'items' );

if ( ! is_array( $items ) || empty( $items ) ) {
	return;
}

$first = true;

foreach ( $items as $item ) {
	if ( empty( $item['title'] ) && empty( $item['text'] ) ) {
		continue;
	}

	if ( ! $first ) {
		echo '<div class="spacer-xl"></div>';
	}

	$first = false;
	?>
	<section class="home-news">
		<div class="container">
			<?php
			get_template_part(
				'template-parts/components/news-block',
				null,
				[
					'side'  => $item['side'] ?? 'right',
					'tag'   => $item['tag'] ?? '',
					'title' => $item['title'] ?? '',
					'text'  => $item['text'] ?? '',
					'items' => $item['items'] ?? [],
					'image' => $item['image'] ?? null,
					'cta'   => $item['cta'] ?? null,
				]
			);
			?>
		</div>
	</section>
	<?php
}
