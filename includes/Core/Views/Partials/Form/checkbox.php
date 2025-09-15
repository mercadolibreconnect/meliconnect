<?php
/**
 * Partial for rendering a checkbox field.
 *
 * @param string $key              The key used for the input's `id` and `name` attributes.
 * @param string $label            The label for the checkbox.
 * @param string $value            The current value of the checkbox.
 * @param bool   $check_compare_value The value to compare for the `checked` state.
 * @param string $helpText         Optional help text to display below the checkbox.
 */


?>

<div class="meliconnect-field">
	<input id="<?php echo esc_attr( $key ); ?>"
			type="checkbox"
			name="<?php echo esc_attr( $key ); ?>"
			class="switch meliconnect-is-rounded is-warning"
			<?php echo checked( $value, $check_compare_value, false ); ?>
			value="<?php echo esc_attr( $check_compare_value ); ?>">
	<label for="<?php echo esc_attr( $key ); ?>" class="meliconnect-label"><?php echo esc_html( $label ); ?></label>
</div>

<?php if ( ! empty( $helpText ) ) : ?>
	<div class="meliconnect-columns">
		<div class="meliconnect-column meliconnect-is-1"></div>
		<div class="meliconnect-column meliconnect-is-11 pl-5">
			<p class="help" style="min-height: 120px"><?php echo esc_html( $helpText ); ?></p>
		</div>
	</div>
<?php endif; ?>