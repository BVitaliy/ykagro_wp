<?php
/**
 * Mobile filter sheet — the same tabs + sort options as the desktop bar, as a
 * full-screen picker.
 *
 * app-catalog-filter.js moves it to <body> (it must escape the page stacking
 * context) and applies the choice by navigating to <category>?sort=…&q=… — a
 * plain GET, same as the desktop form. Radios keep it a real form control, so
 * the state is readable without any component API.
 *
 * @param array $args {
 *     @type WP_Term[] $terms   Category tabs.
 *     @type int       $current Active term id.
 *     @type array     $sorts   Sort options.
 *     @type string    $sort    Active sort value.
 * }
 *
 * @package ykagro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$terms   = ! empty( $args['terms'] ) && is_array( $args['terms'] ) ? $args['terms'] : [];
$current = ! empty( $args['current'] ) ? (int) $args['current'] : 0;
$sorts   = ! empty( $args['sorts'] ) && is_array( $args['sorts'] ) ? $args['sorts'] : [];
$sort    = ! empty( $args['sort'] ) ? (string) $args['sort'] : '';

if ( empty( $terms ) && empty( $sorts ) ) {
	return;
}
?>
<div class="catalog-filter js-catalog-filter" id="catalog-filter" aria-hidden="true">
	<?php
	get_template_part(
		'includes/scroll-line',
		null,
		[
			'class'  => 'catalog-filter__decor',
			'static' => true,
		]
	);
	?>

	<div class="catalog-filter__inner" data-lenis-prevent>
		<button class="catalog-filter__close js-catalog-filter-close" type="button" aria-label="<?php esc_attr_e( 'Закрити', 'ykagro' ); ?>">
			<?php yka_icon( 'icons/close.svg' ); ?>
		</button>

		<div class="catalog-filter__body">
			<?php if ( ! empty( $terms ) ) { ?>
				<fieldset class="catalog-filter__cats">
					<legend class="visually-hidden"><?php esc_html_e( 'Категорія', 'ykagro' ); ?></legend>
					<div class="catalog-filter__cats-list">
						<?php
						foreach ( $terms as $term ) {
							?>
							<label class="catalog-filter__cat">
								<input class="catalog-filter__radio js-catalog-filter-cat" type="radio" name="catalog-filter-cat"
									value="<?php echo esc_url( get_term_link( $term ) ); ?>"
									<?php checked( (int) $term->term_id, $current ); ?>>
								<span class="catalog-filter__cat-text"><?php echo esc_html( $term->name ); ?></span>
							</label>
							<?php
						}
						?>
					</div>
				</fieldset>
			<?php } ?>

			<?php if ( ! empty( $sorts ) ) { ?>
				<fieldset class="catalog-filter__sort">
					<legend class="catalog-filter__sort-label text-md clr-muted"><?php esc_html_e( 'Сортувати за', 'ykagro' ); ?></legend>
					<div class="catalog-filter__sort-list">
						<?php
						foreach ( $sorts as $option ) {
							?>
							<label class="catalog-filter__sort-option">
								<input class="catalog-filter__radio js-catalog-filter-sort" type="radio" name="catalog-filter-sort"
									value="<?php echo esc_attr( $option['value'] ); ?>"
									<?php checked( (string) $option['value'], $sort ); ?>>
								<span class="catalog-filter__sort-text text-md"><?php echo esc_html( $option['label'] ); ?></span>
							</label>
							<?php
						}
						?>
					</div>
				</fieldset>
			<?php } ?>
		</div>

		<div class="catalog-filter__actions">
			<button class="link-more js-catalog-filter-cancel" type="button">
				<?php esc_html_e( 'Скасувати', 'ykagro' ); ?>
				<span class="link-more__icon"><?php yka_icon( 'icons/arrow-diagonal.svg' ); ?></span>
			</button>
			<button class="btn catalog-filter__apply js-catalog-filter-apply" type="button">
				<span class="btn__icon"><?php yka_icon( 'icons/arrow-diagonal.svg' ); ?></span>
				<?php esc_html_e( 'Застосувати', 'ykagro' ); ?>
			</button>
		</div>
	</div>
</div>
