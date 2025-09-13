<?php
if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>
<div class="meliconnect-product-edit-stock-tab">
    <p class="form-field meliconnect_general_stock_variation_field">
        <label for="meliconnect_general_stock_variation">
            <?php esc_html_e('Mercadolibre stock Variation', 'meliconnect'); ?>
        </label>
        <span class="wrap">
            <!-- Operador de variación de precio -->
            <select id="meliconnect_general_stock_variation_operand" class="input-text wc_input_decimal" name="template[meta][stock_operand]">
                <option value="sum" <?php selected($stock_operand, 'sum'); ?>>+</option>
                <option value="rest" <?php selected($stock_operand, 'rest'); ?>>-</option>
            </select>

            <!-- Cantidad de variación -->
            <input id="meliconnect_general_stock_variation_amount" class="input-text wc_input_decimal" type="number" name="template[meta][stock_amount]" value="<?php echo esc_attr($stock_amount); ?>" min="0" size="6">

            <!-- Tipo de variación -->
            <select id="meliconnect_general_stock_variation_type" class="input-text wc_input_decimal last" name="template[meta][stock_type]">
                <option value="units" <?php selected($stock_type, 'units'); ?>><?php esc_html_e('Units', 'meliconnect'); ?></option>
            </select>
        </span>
        <!-- <span class="woocommerce-help-tip" tabindex="0" aria-label="<?php esc_html_e('stock variation settings for Mercadolibre.', 'meliconnect'); ?>"></span> -->
    </p>

</div>