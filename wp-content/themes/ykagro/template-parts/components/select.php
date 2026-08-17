<?php
/**
 * Custom select — styled dropdown that keeps a REAL <select> in the DOM.
 *
 * The native field stays the source of truth: it submits as $_GET['<name>'],
 * the selected option is rendered server-side (so the closed pill shows the
 * right label after a reload), and picking an option fires a native `change`
 * event. No component-specific API to learn.
 *
 * @param array $args {
 *     @type string $name        Field name, e.g. 'sort'.
 *     @type array  $options     [['value' => …, 'label' => …], …].
 *     @type string $value       Currently selected value.
 *     @type string $id          Select id. Default 'select-{name}'.
 *     @type string $labelledby  Id of the visible label.
 *     @type string $aria        aria-label when there is no visible label.
 *     @type string $class       Extra class on the wrapper.
 *     @type bool   $submit      Submit the closest form on change.
 *     @type string $placeholder Text when nothing is selected.
 * }
 *
 * @package ykagro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$name        = ! empty( $args['name'] ) ? (string) $args['name'] : 'select';
$options     = ! empty( $args['options'] ) && is_array( $args['options'] ) ? $args['options'] : [];
$id          = ! empty( $args['id'] ) ? (string) $args['id'] : 'select-' . preg_replace( '/[^a-z0-9_-]+/i', '-', $name );
$value       = isset( $args['value'] ) && '' !== $args['value'] ? (string) $args['value'] : null;
$labelledby  = ! empty( $args['labelledby'] ) ? (string) $args['labelledby'] : '';
$aria        = ! empty( $args['aria'] ) ? (string) $args['aria'] : '';
$extra_class = ! empty( $args['class'] ) ? (string) $args['class'] : '';
$submit      = ! empty( $args['submit'] );
$placeholder = ! empty( $args['placeholder'] ) ? (string) $args['placeholder'] : __( 'Оберіть…', 'ykagro' );

if ( empty( $options ) ) {
	return;
}

// An unknown or missing value falls back to the first option — the design default.
$values = wp_list_pluck( $options, 'value' );

if ( ! in_array( $value, $values, true ) ) {
	$value = (string) reset( $values );
}

$current = $placeholder;

foreach ( $options as $option ) {
	if ( (string) $option['value'] === $value ) {
		$current = $option['label'];
		break;
	}
}

$list_id = $id . '-list';
$btn_id  = $id . '-button';
?>
<div class="select js-select<?php echo ! empty( $extra_class ) ? ' ' . esc_attr( $extra_class ) : ''; ?>" data-select<?php echo $submit ? ' data-select-submit' : ''; ?>>

	<?php // real form control: what the backend reads, what the form submits ?>
	<select class="select__native js-select-native" name="<?php echo esc_attr( $name ); ?>" id="<?php echo esc_attr( $id ); ?>"
		<?php echo ! empty( $labelledby ) ? ' aria-labelledby="' . esc_attr( $labelledby ) . '"' : ''; ?>
		<?php echo ! empty( $aria ) ? ' aria-label="' . esc_attr( $aria ) . '"' : ''; ?>>
		<?php foreach ( $options as $option ) { ?>
			<option value="<?php echo esc_attr( $option['value'] ); ?>"<?php selected( (string) $option['value'], $value ); ?>><?php echo esc_html( $option['label'] ); ?></option>
		<?php } ?>
	</select>

	<?php // styled proxy: hidden until JS wires it up, so no-JS falls back to the native field ?>
	<button class="select__toggle js-select-toggle" type="button" id="<?php echo esc_attr( $btn_id ); ?>"
		role="combobox" aria-haspopup="listbox" aria-expanded="false" aria-controls="<?php echo esc_attr( $list_id ); ?>"
		<?php echo ! empty( $labelledby ) ? ' aria-labelledby="' . esc_attr( $labelledby . ' ' . $btn_id ) . '"' : ''; ?>
		<?php echo empty( $labelledby ) && ! empty( $aria ) ? ' aria-label="' . esc_attr( $aria ) . '"' : ''; ?>>
		<span class="select__value js-select-value"><?php echo esc_html( $current ); ?></span>
		<span class="select__icon" aria-hidden="true"><?php yka_icon( 'icons/chevron-down.svg' ); ?></span>
	</button>

	<div class="select__dropdown">
		<ul class="select__list js-select-list" id="<?php echo esc_attr( $list_id ); ?>" role="listbox"
			<?php echo ! empty( $labelledby ) ? ' aria-labelledby="' . esc_attr( $labelledby ) . '"' : ''; ?>>
			<?php
			foreach ( $options as $index => $option ) {
				$is_selected = (string) $option['value'] === $value;
				?>
				<li class="select__option<?php echo $is_selected ? ' is-selected' : ''; ?>" role="option"
					id="<?php echo esc_attr( $id . '-opt-' . $index ); ?>"
					data-value="<?php echo esc_attr( $option['value'] ); ?>"
					aria-selected="<?php echo $is_selected ? 'true' : 'false'; ?>">
					<span class="select__option-text"><?php echo esc_html( $option['label'] ); ?></span>
					<span class="select__option-mark" aria-hidden="true"><?php yka_icon( 'icons/check.svg' ); ?></span>
				</li>
				<?php
			}
			?>
		</ul>
	</div>
</div>
