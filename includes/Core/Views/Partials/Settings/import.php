<form id="melicon-import-settings-form">

    <input type="hidden" name="checkbox_fields" id="checkbox_fields" value="melicon_import_is_disabled,melicon_import_state_paused, melicon_import_state_closed, melicon_import_by_sku, melicon_import_attrs">

    <section class="section">
        <div class="container">
            <div class="columns">
                <div class="column">
                    <h2 class="title is-5"><?php _e('Import', 'meliconnect'); ?></h2>
                    <div class="mb-6">
                        <?php self::print_setting_checkbox('melicon_import_is_disabled', __('Disable Import', 'meliconnect'), $import_data['melicon_import_is_disabled'], 'true'); ?>
                    </div>
                    <?php if (isset($import_data['melicon_general_sync_type']) && $import_data['melicon_general_sync_type'] != 'import'): ?>
                    <div class="notification is-warning">
                        <?php _e('Importing is disabled for automatic jobs in the general plugin settings. The current settings will only be applied to bulk or individual custom imports.', 'meliconnect'); ?>
                    </div>
                    <?php endif; ?>
                    <div class="content">

                        <div id="melicon-import-settings-columns" class="columns">
                            <div id="import-setting-left-column" class="column">
                                <div class="columns">
                                    <div class="column">
                                        <h3 class="title is-6"><?php _e('Product Information', 'meliconnect'); ?></h3>
                                    </div>
                                </div>

                                <div id="melicon-import-product-info">
                                    <?php self::print_setting_select('melicon_import_title', __('Title', 'meliconnect'), $import_data['melicon_import_title'], ['always' => __('Always', 'meliconnect'), 'on_create' => __('On Create', 'meliconnect')]); ?>

                                    <?php self::print_setting_select('melicon_import_stock', __('Stock', 'meliconnect'), $import_data['melicon_import_stock'], ['always' => __('Always', 'meliconnect'), 'on_create' => __('On Create', 'meliconnect')]); ?>

                                    <?php self::print_setting_select('melicon_import_price', __('Price', 'meliconnect'), $import_data['melicon_import_price'], ['always' => __('Always', 'meliconnect'), 'on_create' => __('On Create', 'meliconnect')]); ?>

                                    <?php self::print_setting_select('melicon_import_images', __('Images', 'meliconnect'), $import_data['melicon_import_images']); ?>

                                    <?php self::print_setting_select('melicon_import_sku', __('SKU', 'meliconnect'), $import_data['melicon_import_sku']); ?>

                                    <?php self::print_setting_select('melicon_import_categories', __('Categories', 'meliconnect'), $import_data['melicon_import_categories']); ?>

                                    <?php self::print_setting_select('melicon_import_product_attributes', __('Product Attributes', 'meliconnect'), $import_data['melicon_import_product_attributes']); ?>

                                    <?php self::print_setting_select('melicon_import_ml_status', __('MercadoLibre Status', 'meliconnect'), $import_data['melicon_import_ml_status']); ?>

                                    <?php self::print_setting_select('melicon_import_variations', __('Variations', 'meliconnect'), $import_data['melicon_import_variations']); ?>

                                    <?php self::print_setting_select('melicon_import_variations_as', __('Import variations as:', 'meliconnect'), $import_data['melicon_import_variations_as'], ['One product with variations' => __('One Product with Variations', 'meliconnect'), 'multiple products' => __('Multiple Products', 'meliconnect')]); ?>

                                    <?php self::print_setting_select('melicon_import_description', __('Description', 'meliconnect'), $import_data['melicon_import_description']); ?>

                                    <?php self::print_setting_select('melicon_import_description_to', __('Apply description to:', 'meliconnect'), $import_data['melicon_import_description_to'], ['description' => __('Long Description', 'meliconnect'), 'short_description' => __('Short Description', 'meliconnect')]); ?>
                                </div>
                            </div>
                            <div id="import-setting-right-column" class="column">
                                <div class="columns">
                                    <div class="column">
                                        <h3 class="title is-6"><?php _e('Actions', 'meliconnect'); ?></h3>
                                    </div>
                                </div>

                                <div class="columns mt-5">
                                    <div class="column is-4">
                                        <label for="finalize_ml" class="label"><?php _e('Importer Actions', 'meliconnect'); ?></label>
                                    </div>
                                    <div class="column is-8">
                                        <div class="melicon-select select is-fullwidth">
                                            <select name="melicon_import_type" id="melicon_import_type">
                                                <option value="onlyUpdate" <?php selected($import_data['melicon_import_type'], 'onlyUpdate'); ?>><?php _e('Update Products Only', 'meliconnect'); ?></option>
                                                <option value="createAndUpdate" <?php selected($import_data['melicon_import_type'], 'createAndUpdate'); ?>><?php _e('Create and Update Products', 'meliconnect'); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="columns">
                                    <div class="column is-12">
                                        <h3 class="title is-6"><?php _e('Variations on Import', 'meliconnect'); ?></h3>
                                    </div>
                                </div>
                                <div class="columns">
                                    <div class="column">
                                        <p><?php _e('Each imported publication will have the following price or stock difference applied. If the product has an associated export template with a price or stock modification, it will be ignored.', 'meliconnect'); ?></p>
                                    </div>
                                </div>

                                <div class="columns">
                                    <div class="column is-12">
                                        <p><strong><?php _e('Price variation', 'meliconnect'); ?></strong></p>
                                    </div>
                                </div>
                                <div class="columns">
                                    <div class="column is-3">
                                        <div class="field">
                                            <div class="control">
                                                <div class="melicon-select select is-fullwidth">
                                                    <select name="melicon_import_price_variation_operand" id="melicon_import_price_variation_operand">
                                                        <option value="sum" <?php selected($import_data['melicon_import_price_variation_operand'], 'sum'); ?>>+</option>
                                                        <option value="rest" <?php selected($import_data['melicon_import_price_variation_operand'], 'rest'); ?>>-</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="column is-5">
                                        <div class="field">
                                            <div class="control">
                                                <input type="number" name="melicon_import_price_variation_amount" value="<?php echo $import_data['melicon_import_price_variation_amount']; ?>" min="0" id="melicon_import_price_variation_amount" class="input">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="column is-4">
                                        <div class="field">
                                            <div class="control">
                                                <div class="melicon-select select is-fullwidth">
                                                    <select name="melicon_import_price_variation_type" id="melicon_import_price_variation_type">
                                                        <option value="percent" <?php selected($import_data['melicon_import_price_variation_type'], 'percent'); ?>><?php _e('Percentage Value (%)', 'meliconnect'); ?></option>
                                                        <option value="price" <?php selected($import_data['melicon_import_price_variation_type'], 'price'); ?>><?php _e('Fixed Value ($)', 'meliconnect'); ?></option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="columns">
                                    <div class="column is-12">
                                        <p><strong><?php _e('Stock variation', 'meliconnect'); ?></strong></p>
                                    </div>
                                </div>
                                <div class="columns">
                                    <div class="column is-3">
                                        <div class="field">
                                            <div class="control">
                                                <div class="melicon-select select is-fullwidth">
                                                    <select name="melicon_import_stock_variation_operand" id="melicon_import_stock_variation_operand">
                                                        <option value="sum" <?php selected($import_data['melicon_import_stock_variation_operand'], 'sum'); ?>>+</option>
                                                        <option value="rest" <?php selected($import_data['melicon_import_stock_variation_operand'], 'rest'); ?>>-</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="column is-5">
                                        <div class="field">
                                            <div class="control">
                                                <input type="number" name="melicon_import_stock_variation_amount" value="<?php echo $import_data['melicon_import_stock_variation_amount']; ?>" min="0" id="melicon_import_stock_variation_amount" class="input">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="column is-4">
                                        <div class="field">
                                            <div class="control">
                                                <div class="melicon-select select is-fullwidth">
                                                    <select name="melicon_import_stock_variation_type" id="melicon_import_stock_variation_type">
                                                        <option value="units" <?php selected($import_data['melicon_import_stock_variation_type'], 'units'); ?>><?php _e('Units', 'meliconnect'); ?></option>
                                                      
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <?php self::print_setting_checkbox('melicon_import_state_paused', __('Create product from paused publications', 'meliconnect'), $import_data['melicon_import_state_paused'], 'true'); ?>

                                <?php self::print_setting_checkbox('melicon_import_state_closed', __('If the publication ends, move to trash', 'meliconnect'), $import_data['melicon_import_state_closed'], 'true'); ?>

                                <?php self::print_setting_checkbox('melicon_import_by_sku', __('If the publication is not linked, match the product by SKU or GTIN', 'meliconnect'), $import_data['melicon_import_by_sku'], 'true'); ?>

                                <?php self::print_setting_checkbox('melicon_import_attrs', __('Create attributes in WooCommerce and assign them to the imported product', 'meliconnect'), $import_data['melicon_import_attrs'], 'true'); ?>

                            </div>
                        </div>

                        <div class="columns">
                            <div class="column">
                                <div class="level">
                                    <div class="level-left">
                                    </div>
                                    <div class="level-right">

                                        <div class="melicon-field field is-grouped">
                                            <p class="melicon-control control">
                                                <button id="save-import-button" type="submit" class="button-meliconnect button is-primary"><?php _e('Save Import Settings', 'meliconnect'); ?></button>
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
                $('#melicon-import-settings-columns input, #melicon-import-settings-columns select, #melicon-import-settings-columns textarea').prop('disabled', true);
            } else {
                $('#melicon-import-settings-columns input, #melicon-import-settings-columns select, #melicon-import-settings-columns textarea').prop('disabled', false);
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
                Bulma().alert({
                    type: 'warning',
                    title: mcTranslations.alert_title_disable_import,
                    body: mcTranslations.alert_body_disable_import,
                    confirm: {
                        label: mcTranslations.confirm,
                        classes: ['button', 'button-meliconnect'],
                        onClick: function() {
                            toggleImportSettings(true);
                        },
                    },
                    cancel: {
                        label: mcTranslations.cancel,
                        classes: ['button', 'button-meliconnect'],
                        onClick: function() {
                            checkbox.prop('checked', !checkbox.prop('checked')); // Revert the checkbox state
                        },
                    },
                });
            }


        });
    });
</script>