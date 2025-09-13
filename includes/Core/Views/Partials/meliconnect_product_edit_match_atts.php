<?php

if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Convertir a array si es objeto, para evitar errores al acceder con []
if (is_object($find_in_meli_attr)) {
    $find_in_meli_attr = json_decode(json_encode($find_in_meli_attr), true);
}
?>
<div class="meliconnect_meli_attribute_info_container meliconnect-container meliconnect-p-4">
    <div class="meliconnect-content">
        <?php if ($find_in_meli_attr): ?>
            <p><strong><?php esc_html_e('Attribute MATCH by name with Meli Attr', 'meliconnect'); ?></strong></p>
            <p><strong><?php esc_html_e('Meli value type:', 'meliconnect'); ?></strong>
                <?php echo esc_html($instance->get_attr_value_type($find_in_meli_attr)); ?>
            </p>

            <?php
            $is_required = in_array($find_in_meli_attr['value_type'], ['list', 'boolean']);
            $values = $find_in_meli_attr['values'] ?? [];

            if (!empty($values)) :
                $label = $is_required ? esc_html__('Required values:', 'meliconnect') : esc_html__('Suggested values:', 'meliconnect');
            ?>
                <p><strong><?php echo esc_html($label); ?></strong></p>
                <p><?php echo esc_html(implode(' | ', array_column($values, 'name'))); ?></p>

                <?php
                if ($is_required) :
                    $not_exportable_values = array_diff($current_attr_options, array_column($values, 'name'));

                    if (!empty($not_exportable_values)) :
                ?>
                        <p class="meliconnect-color-error"><strong><?php esc_html_e('The following attribute values cannot be exported:', 'meliconnect'); ?></strong>
                            <?php echo esc_html(implode(', ', $not_exportable_values)); ?>
                        </p>
                    <?php else : ?>
                        <p class="meliconnect-color-success"><strong><?php esc_html_e('All attribute values can be exported.', 'meliconnect'); ?></strong></p>
                <?php
                    endif;
                endif;
                ?>
            <?php endif; ?>

            <input type="hidden" class="meliconnect-mercadolibre-attr-input"
                name="template[attrs][<?php echo esc_attr($i); ?>]"
                value="<?php echo esc_attr($escaped_attr_value); ?>" />

            <?php if (!empty($attribute_tags_info)) : ?>
                <?php foreach ($attribute_tags_info as $message) : ?>
                    <p class="meliconnect-text-error"><strong><?php echo esc_html($message); ?></strong></p>
                <?php endforeach; ?>
            <?php endif; ?>

        <?php else : ?>
            <p><?php esc_html_e('Attribute NOT MATCH by name with Meli Attr', 'meliconnect'); ?></p>
        <?php endif; ?>
    </div>
</div>