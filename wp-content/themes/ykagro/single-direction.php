<?php
/**
 * Single direction — banner + breadcrumbs, then the "text + photo" sections
 * from the direction's own fields, and a slider of the other directions.
 *
 * @package ykagro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

the_post();

$yka_post_id   = get_the_ID();
$yka_banner    = get_field( 'banner' );
$yka_banner_m  = get_field( 'banner_mob' );
$yka_sections  = get_field( 'sections' );
$yka_list_page = get_page_by_path( 'directions' );

$yka_trail = [];

if ( $yka_list_page ) {
	$yka_trail[] = [
		'label' => get_the_title( $yka_list_page ),
		'href'  => get_permalink( $yka_list_page ),
	];
}

$yka_trail[] = [ 'label' => get_the_title() ];
?>

<main>
	<?php get_template_part( 'includes/scroll-line' ); ?>

	<?php if ( ! empty( $yka_banner['ID'] ) ) { ?>
		<section class="page-hero">
			<?php
			get_template_part(
				'template-parts/components/banner',
				null,
				[
					'image'     => $yka_banner,
					'image_mob' => $yka_banner_m,
					'eager'     => true,
				]
			);
			?>

			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="page-hero__logo" aria-label="<?php esc_attr_e( 'YKagro — на головну', 'ykagro' ); ?>">
				<?php yka_icon( 'logo.svg' ); ?>
			</a>

			<div class="page-hero__inner container">
				<div class="breadcrumbs-scroll">
					<?php get_template_part( 'template-parts/components/breadcrumbs', null, [ 'items' => $yka_trail ] ); ?>
				</div>
				<h1 class="page-hero__title clr-white"><?php the_title(); ?></h1>
			</div>
		</section>
	<?php } else { ?>
		<?php get_template_part( 'template-parts/components/page-head', null, [ 'items' => $yka_trail ] ); ?>
		<div class="container">
			<h1 class="h2 clr-black"><?php the_title(); ?></h1>
		</div>
	<?php } ?>

	<?php
	if ( is_array( $yka_sections ) && ! empty( $yka_sections ) ) {
		foreach ( $yka_sections as $yka_section ) {
			if ( empty( $yka_section['title'] ) && empty( $yka_section['text'] ) ) {
				continue;
			}
			?>
			<div class="spacer-xl"></div>
			<section class="home-news">
				<div class="container">
					<?php
					get_template_part(
						'template-parts/components/news-block',
						null,
						[
							'side'  => $yka_section['side'] ?? 'right',
							'tag'   => $yka_section['tag'] ?? '',
							'title' => $yka_section['title'] ?? '',
							'text'  => $yka_section['text'] ?? '',
							'items' => $yka_section['items'] ?? [],
							'image' => $yka_section['image'] ?? null,
						]
					);
					?>
				</div>
			</section>
			<?php
		}
	}
	?>

	<div class="spacer-xl"></div>
	<?php get_template_part( 'template-parts/single/direction-related' ); ?>

	<div class="spacer-xl"></div>
</main>

<?php
get_footer();
