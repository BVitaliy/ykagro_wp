<?php
/**
 * Page builder layout: centered wysiwyg block ("Глобальні виклики").
 *
 * Editor content reusing .article-content — identical prose treatment to the
 * article body, the wrapper only centers it on the article content width.
 *
 * @package ykagro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$content = (string) get_sub_field( 'content' );

if ( empty( $content ) ) {
	return;
}
?>
<section class="responsibility-challenges">
	<div class="container">
		<div class="responsibility-challenges__content article-content">
			<?php echo wp_kses_post( yka_wrap_content_images( yka_strip_table_attrs( $content ) ) ); ?>
		</div>
	</div>
</section>
