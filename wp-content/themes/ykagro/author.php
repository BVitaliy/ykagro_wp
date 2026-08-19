<?php
/**
 * Author archive — author hero, then their articles.
 *
 * @package ykagro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$yka_author = get_queried_object();

if ( ! $yka_author instanceof WP_User ) {
	$yka_author = get_userdata( (int) get_query_var( 'author' ) );
}

$yka_author_id = $yka_author ? (int) $yka_author->ID : 0;
$yka_name      = $yka_author_id ? get_the_author_meta( 'display_name', $yka_author_id ) : '';
$yka_role      = $yka_author_id ? (string) get_field( 'author_role', 'user_' . $yka_author_id ) : '';
$yka_bio       = $yka_author_id ? get_the_author_meta( 'description', $yka_author_id ) : '';
$yka_photo     = $yka_author_id ? get_field( 'author_photo', 'user_' . $yka_author_id ) : null;
$yka_socials   = $yka_author_id ? get_field( 'author_socials', 'user_' . $yka_author_id ) : [];

$yka_blog_id = (int) get_option( 'page_for_posts' );
$yka_trail   = [];

if ( $yka_blog_id ) {
	$yka_trail[] = [
		'label' => get_the_title( $yka_blog_id ),
		'href'  => get_permalink( $yka_blog_id ),
	];
}

$yka_trail[] = [ 'label' => $yka_name ];
?>

<main class="is-author">
	<?php get_template_part( 'includes/scroll-line', null, [ 'class' => 'scroll-line--blog' ] ); ?>

	<div class="author-page">
		<?php get_template_part( 'template-parts/components/page-head', null, [ 'items' => $yka_trail ] ); ?>

		<section class="author-hero" data-scroll-line-after>
			<div class="author-hero__inner">
				<div class="author-hero__photo">
					<picture>
						<?php
						if ( ! empty( $yka_photo['ID'] ) ) {
							echo wp_get_attachment_image(
								(int) $yka_photo['ID'],
								'yka-author',
								false,
								[
									'alt'           => $yka_name,
									'loading'       => 'eager',
									'fetchpriority' => 'high',
									'decoding'      => 'async',
								]
							);
						} else {
							echo get_avatar( $yka_author_id, 648, '', $yka_name );
						}
						?>
					</picture>
				</div>

				<div class="author-hero__body">
					<div class="author-hero__top">
						<div class="author-hero__head">
							<h1 class="author-hero__name h4"><?php echo esc_html( $yka_name ); ?></h1>
							<?php if ( ! empty( $yka_role ) ) { ?>
								<p class="author-hero__role text-lg clr-black"><?php echo esc_html( $yka_role ); ?></p>
							<?php } ?>
						</div>

						<?php if ( ! empty( $yka_bio ) ) { ?>
							<div class="author-hero__bio text-lg">
								<?php // The profile bio is plain text; wpautop turns blank lines into paragraphs. ?>
								<?php echo wp_kses_post( wpautop( $yka_bio ) ); ?>
							</div>
						<?php } ?>
					</div>

					<?php if ( is_array( $yka_socials ) && ! empty( $yka_socials ) ) { ?>
						<div class="author-hero__socials">
							<?php
							foreach ( $yka_socials as $yka_social ) {
								if ( empty( $yka_social['link'] ) || empty( $yka_social['icon'] ) ) {
									continue;
								}
								?>
								<a href="<?php echo esc_url( $yka_social['link'] ); ?>" class="author-hero__social" aria-label="<?php echo esc_attr( $yka_social['label'] ?? '' ); ?>" target="_blank" rel="noopener noreferrer">
									<?php yka_icon( 'icons/' . sanitize_file_name( $yka_social['icon'] ) . '.svg' ); ?>
								</a>
								<?php
							}
							?>
						</div>
					<?php } ?>
				</div>
			</div>
		</section>

		<div class="spacer-xl"></div>

		<?php if ( have_posts() ) { ?>
			<section class="author-articles blog-list" aria-label="<?php esc_attr_e( 'Статті автора', 'ykagro' ); ?>">
				<h2 class="author-articles__title h2" data-title-anim="off"><?php esc_html_e( 'Статті автора', 'ykagro' ); ?></h2>
				<div class="spacer-md"></div>
				<div class="blog-list__inner">
					<div class="blog-list__grid">
						<?php
						while ( have_posts() ) {
							the_post();
							get_template_part( 'template-parts/components/article-card' );
						}
						?>
					</div>

					<?php
					get_template_part(
						'template-parts/components/pagination',
						null,
						[
							'more_text' => __( 'Показати більше', 'ykagro' ),
							'grid'      => '.blog-list__grid',
						]
					);
					?>
				</div>
			</section>
		<?php } else { ?>
			<div class="container">
				<p class="text-lg clr-muted"><?php esc_html_e( 'У цього автора ще немає публікацій.', 'ykagro' ); ?></p>
			</div>
		<?php } ?>

		<div class="spacer-xl"></div>
	</div>
</main>

<?php
get_footer();
