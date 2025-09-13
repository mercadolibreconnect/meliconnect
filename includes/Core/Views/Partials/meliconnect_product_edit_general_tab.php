<?php
if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>
<div class="meliconnect-product-edit-general-tab">
    <p class="form-field meliconnect_general_price_variation_field">
        <label for="meliconnect_general_price_variation">
            <?php esc_html_e('Mercadolibre Price Variation', 'meliconnect'); ?>
        </label>
        <span class="wrap">
            <!-- Operador de variación de precio -->
            <select id="meliconnect_general_price_variation_operand" class="input-text wc_input_decimal" name="template[meta][price_operand]">
                <option value="sum" <?php selected($price_operand, 'sum'); ?>>+</option>
                <option value="rest" <?php selected($price_operand, 'rest'); ?>>-</option>
            </select>

            <!-- Cantidad de variación -->
            <input id="meliconnect_general_price_variation_amount" class="input-text wc_input_decimal" type="number" name="template[meta][price_amount]" value="<?php echo esc_attr($price_amount); ?>" min="0" size="6">

            <!-- Tipo de variación -->
            <select id="meliconnect_general_price_variation_type" class="input-text wc_input_decimal last" name="template[meta][price_type]">
                <option value="percent" <?php selected($price_type, 'percent'); ?>><?php esc_html_e('Percentage (%)', 'meliconnect'); ?></option>
                <option value="price" <?php selected($price_type, 'price'); ?>><?php esc_html_e('Fixed Value ($)', 'meliconnect'); ?></option>
            </select>

        </span>
        <!-- <span class="woocommerce-help-tip" tabindex="0" aria-label="<?php esc_html_e('Price variation settings for Mercadolibre.', 'meliconnect'); ?>"></span> -->
    </p>

</div>