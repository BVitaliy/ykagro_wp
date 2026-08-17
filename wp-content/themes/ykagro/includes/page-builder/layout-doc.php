<?php
/**
 * Page builder layout: legal / document page.
 *
 * Logo + breadcrumbs, then a single wysiwyg column. Content tags carry no
 * classes — everything is styled from .doc-page__content, so an editor cannot
 * break the typography by pasting from Word.
 *
 * @package ykagro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title   = (string) get_sub_field( 'title' );
$content = (string) get_sub_field( 'content' );

if ( empty( $title ) ) {
	$title = get_the_title();
}

get_template_part( 'template-parts/components/page-head', null, [ 'items' => [ [ 'label' => get_the_title() ] ] ] );
?>
<section class="doc-page">
	<div class="doc-page__inner">
		<h1 class="doc-page__title h3"><?php echo esc_html( $title ); ?></h1>

		<?php if ( ! empty( $content ) ) { ?>
			<div class="doc-page__content">
				<?php echo wp_kses_post( yka_strip_table_attrs( $content ) ); ?>
			</div>
		<?php } ?>
	</div>
</section>
