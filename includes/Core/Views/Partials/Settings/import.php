<form id="melicon-import-settings-form">

    <input type="hidden" name="checkbox_fields" id="checkbox_fields" value="melicon_import_is_disabled,melicon_import_state_paused, melicon_import_state_closed, melicon_import_by_sku, melicon_import_attrs">

    <section class="melicon-section">
        <div class="melicon-container">
            <div class="melicon-columns">
                <div class="melicon-column">
                    <h2 class="melicon-title melicon-is-5"><?php esc_html_e('Import', 'meliconnect'); ?></h2>
                    <div class="mb-6">
                        <?php self::print_setting_checkbox('melicon_import_is_disabled', esc_html__('Disable Import', 'meliconnect'), $import_data['melicon_import_is_disabled'], 'true'); ?>
                    </div>
                    <?php if (isset($import_data['melicon_general_sync_type']) && $import_data['melicon_general_sync_type'] != 'import'): ?>
                        <div class="melicon-notification melicon-is-warning">
                            <?php esc_html_e('Importing is disabled for automatic jobs in the general plugin settings. The current settings will only be applied to bulk or individual custom imports.', 'meliconnect'); ?>
                        </div>
                    <?php endif; ?>
                    <div class="melicon-content">

                        <div id="melicon-import-settings-melicon-columns" class="melicon-columns">
                            <div id="import-setting-left-melicon-column" class="melicon-column">
                                <div class="melicon-columns">
                                    <div class="melicon-column">
                                        <h3 class="melicon-title melicon-is-6"><?php esc_html_e('Product Information', 'meliconnect'); ?></h3>
                                    </div>
                                </div>

                                <div id="melicon-import-product-info">
                                    <?php self::print_setting_select('melicon_import_title', esc_html__('Title', 'meliconnect'), $import_data['melicon_import_title'], ['always' => esc_html__('Always', 'meliconnect'), 'on_create' => esc_html__('On Create', 'meliconnect')]); ?>

                                    <?php self::print_setting_select('melicon_import_stock', esc_html__('Stock', 'meliconnect'), $import_data['melicon_import_stock'], ['always' => esc_html__('Always', 'meliconnect'), 'on_create' => esc_html__('On Create', 'meliconnect')]); ?>

                                    <?php self::print_setting_select('melicon_import_price', esc_html__('Price', 'meliconnect'), $import_data['melicon_import_price'], ['always' => esc_html__('Always', 'meliconnect'), 'on_create' => esc_html__('On Create', 'meliconnect')]); ?>

                                    <?php self::print_setting_select('melicon_import_images', esc_html__('Images', 'meliconnect'), $import_data['melicon_import_images']); ?>

                                    <?php self::print_setting_select('melicon_import_sku', esc_html__('SKU', 'meliconnect'), $import_data['melicon_import_sku']); ?>

                                    <?php self::print_setting_select('melicon_import_categories', esc_html__('Categories', 'meliconnect'), $import_data['melicon_import_categories']); ?>

                                    <?php self::print_setting_select('melicon_import_product_attributes', esc_html__('Product Attributes', 'meliconnect'), $import_data['melicon_import_product_attributes']); ?>

                                    <?php self::print_setting_select('melicon_import_ml_status', esc_html__('MercadoLibre Status', 'meliconnect'), $import_data['melicon_import_ml_status']); ?>

                                    <?php self::print_setting_select('melicon_import_variations', esc_html__('Variations', 'meliconnect'), $import_data['melicon_import_variations']); ?>

                                    <?php self::print_setting_select('melicon_import_variations_as', esc_html__('Import variations as:', 'meliconnect'), $import_data['melicon_import_variations_as'], ['One product with variations' => esc_html__('One Product with Variations', 'meliconnect'), 'multiple products' => esc_html__('Multiple Products', 'meliconnect')]); ?>

                                    <?php self::print_setting_select('melicon_import_description', esc_html__('Description', 'meliconnect'), $import_data['melicon_import_description']); ?>

                                    <?php self::print_setting_select('melicon_import_description_to', esc_html__('Apply description to:', 'meliconnect'), $import_data['melicon_import_description_to'], ['description' => esc_html__('Long Description', 'meliconnect'), 'short_description' => esc_html__('Short Description', 'meliconnect')]); ?>
                                </div>
                            </div>
                            <div id="import-setting-right-melicon-column" class="melicon-column">
                                <div class="melicon-columns">
                                    <div class="melicon-column">
                                        <h3 class="melicon-title melicon-is-6"><?php esc_html_e('Actions', 'meliconnect'); ?></h3>
                                    </div>
                                </div>

                                <div class="melicon-columns melicon-mt-5">
                                    <div class="melicon-column melicon-is-4">
                                        <label for="finalize_ml" class="melicon-label"><?php esc_html_e('Importer Actions', 'meliconnect'); ?></label>
                                    </div>
                                    <div class="melicon-column melicon-is-8">
                                        <div class="melicon-select melicon-is-fullwidth">
                                            <select name="melicon_import_type" id="melicon_import_type">
                                                <option value="onlyUpdate" <?php selected($import_data['melicon_import_type'], 'onlyUpdate'); ?>><?php esc_html_e('Update Products Only', 'meliconnect'); ?></option>
                                                <option value="createAndUpdate" <?php selected($import_data['melicon_import_type'], 'createAndUpdate'); ?>><?php esc_html_e('Create and Update Products', 'meliconnect'); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="melicon-columns">
                                    <div class="melicon-column melicon-is-12">
                                        <h3 class="melicon-title melicon-is-6"><?php esc_html_e('Variations on Import', 'meliconnect'); ?></h3>
                                    </div>
                                </div>
                                <div class="melicon-columns">
                                    <div class="melicon-column">
                                        <p><?php esc_html_e('Each imported publication will have the following price or stock difference applied. If the product has an associated export template with a price or stock modification, it will be ignored.', 'meliconnect'); ?></p>
                                    </div>
                                </div>

                                <div class="melicon-columns">
                                    <div class="melicon-column melicon-is-12">
                                        <p><strong><?php esc_html_e('Price variation', 'meliconnect'); ?></strong></p>
                                    </div>
                                </div>
                                <div class="melicon-columns">
                                    <div class="melicon-column melicon-is-3">
                                        <div class="melicon-field">
                                            <div class="melicon-control">
                                                <div class="melicon-select melicon-is-fullwidth">
                                                    <select name="melicon_import_price_variation_operand" id="melicon_import_price_variation_operand">
                                                        <option value="sum" <?php selected($import_data['melicon_import_price_variation_operand'], 'sum'); ?>>+</option>
                                                        <option value="rest" <?php selected($import_data['melicon_import_price_variation_operand'], 'rest'); ?>>-</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="melicon-column melicon-is-5">
                                        <div class="melicon-field">
                                            <div class="melicon-control">
                                                <input type="number" name="melicon_import_price_variation_amount" value="<?php echo esc_attr($import_data['melicon_import_price_variation_amount']); ?>" min="0" id="melicon_import_price_variation_amount" class="melicon-input">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="melicon-column melicon-is-4">
                                        <div class="melicon-field">
                                            <div class="melicon-control">
                                                <div class="melicon-select melicon-is-fullwidth">
                                                    <select name="melicon_import_price_variation_type" id="melicon_import_price_variation_type">
                                                        <option value="percent" <?php selected($import_data['melicon_import_price_variation_type'], 'percent'); ?>><?php esc_html_e('Percentage Value (%)', 'meliconnect'); ?></option>
                                                        <option value="price" <?php selected($import_data['melicon_import_price_variation_type'], 'price'); ?>><?php esc_html_e('Fixed Value ($)', 'meliconnect'); ?></option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="melicon-columns">
                                    <div class="melicon-column melicon-is-12">
                                        <p><strong><?php esc_html_e('Stock variation', 'meliconnect'); ?></strong></p>
                                    </div>
                                </div>
                                <div class="melicon-columns">
                                    <div class="melicon-column melicon-is-3">
                                        <div class="melicon-field">
                                            <div class="melicon-control">
                                                <div class="melicon-select melicon-is-fullwidth">
                                                    <select name="melicon_import_stock_variation_operand" id="melicon_import_stock_variation_operand">
                                                        <option value="sum" <?php selected($import_data['melicon_import_stock_variation_operand'], 'sum'); ?>>+</option>
                                                        <option value="rest" <?php selected($import_data['melicon_import_stock_variation_operand'], 'rest'); ?>>-</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="melicon-column melicon-is-5">
                                        <div class="melicon-field">
                                            <div class="melicon-control">
                                                <input type="number" name="melicon_import_stock_variation_amount" value="<?php echo esc_attr($import_data['melicon_import_stock_variation_amount']); ?>" min="0" id="melicon_import_stock_variation_amount" class="melicon-input">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="melicon-column melicon-is-4">
                                        <div class="melicon-field">
                                            <div class="melicon-control">
                                                <div class="melicon-select melicon-is-fullwidth">
                                                    <select name="melicon_import_stock_variation_type" id="melicon_import_stock_variation_type">
                                                        <option value="units" <?php selected($import_data['melicon_import_stock_variation_type'], 'units'); ?>><?php esc_html_e('Units', 'meliconnect'); ?></option>

                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <?php self::print_setting_checkbox('melicon_import_state_paused', esc_html__('Create product from paused publications', 'meliconnect'), $import_data['melicon_import_state_paused'], 'true'); ?>

                                <?php self::print_setting_checkbox('melicon_import_state_closed', esc_html__('If the publication ends, move to trash', 'meliconnect'), $import_data['melicon_import_state_closed'], 'true'); ?>

                                <?php self::print_setting_checkbox('melicon_import_by_sku', esc_html__('If the publication is not linked, match the product by SKU or GTIN', 'meliconnect'), $import_data['melicon_import_by_sku'], 'true'); ?>

                                <?php self::print_setting_checkbox('melicon_import_attrs', esc_html__('Create attributes in WooCommerce and assign them to the imported product', 'meliconnect'), $import_data['melicon_import_attrs'], 'true'); ?>

                            </div>
                        </div>

                        <div class="melicon-columns">
                            <div class="melicon-column">
                                <div class="melicon-level">
                                    <div class="melicon-level-left">
                                    </div>
                                    <div class="melicon-level-right">

                                        <div class="melicon-field melicon-is-grouped">
                                            <p class="melicon-control">
                                                <button id="save-import-button" type="submit" class="melicon-button  melicon-is-primary"><?php esc_html_e('Save Import Settings', 'meliconnect'); ?></button>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </section>
</form>
<script>
    jQuery(document).ready(function($) {

        function toggleImportSettings(isDisabled) {
            if (isDisabled) {
                $('#melicon-import-settings-melicon-columns input, #melicon-import-settings-melicon-columns select, #melicon-import-settings-melicon-columns textarea').prop('disabled', true);
            } else {
                $('#melicon-import-settings-melicon-columns input, #melicon-import-settings-melicon-columns select, #melicon-import-settings-melicon-columns textarea').prop('disabled', false);
            }
        }

        toggleImportSettings($('#melicon_import_is_disabled').prop('checked'));


        $('#melicon-import-settings-form').submit(function(e) {
            e.preventDefault();
            var formData = new FormData(this); // Collect form data

            formData.append("action", "melicon_save_others_settings");

            $.ajax({
                type: "POST",
                url: ajaxurl,
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    $('#save-import-button').addClass('is-loading');
                },
                success: function(response) {
                    location.reload();
                },
                error: function(xhr, status, error) {
                    $('#save-import-button').removeClass('is-loading');
                    console.log(xhr.responseText);
                }
            });
        });

        $('#melicon_import_is_disabled').on('change', function(e) {
            var checkbox = $(this);

            // Prevent default checkbox action
            e.preventDefault();

            if (!checkbox.prop('checked')) {
                toggleImportSettings(false);
            } else {
                MeliconSwal.fire({
                    icon: 'warning',
                    title: mcTranslations.alert_title_disable_import,
                    text: mcTranslations.alert_body_disable_import,
                    showCancelButton: true,
                    confirmButtonText: mcTranslations.confirm,
                    cancelButtonText: mcTranslations.cancel,
                    customClass: {
                        confirmButton: 'melicon-button melicon-is-primary',
                        cancelButton: 'melicon-button melicon-is-secondary'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Lógica para confirmar la acción
                        toggleImportSettings(true);
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        // Revertir el estado del checkbox si se cancela
                        checkbox.prop('checked', !checkbox.prop('checked'));
                    }
                });

            }


        });
    });
</script>