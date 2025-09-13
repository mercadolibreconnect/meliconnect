<form id="meliconnect-import-settings-form">

    <input type="hidden" name="checkbox_fields" id="checkbox_fields" value="meliconnect_import_is_disabled,meliconnect_import_state_paused, meliconnect_import_state_closed, meliconnect_import_by_sku, meliconnect_import_attrs">

    <section class="meliconnect-section">
        <div class="meliconnect-container">
            <div class="meliconnect-columns">
                <div class="meliconnect-column">
                    <h2 class="meliconnect-title meliconnect-is-5"><?php esc_html_e('Import', 'meliconnect'); ?></h2>
                    <div class="mb-6">
                        <?php self::print_setting_checkbox('meliconnect_import_is_disabled', esc_html__('Disable Import', 'meliconnect'), $import_data['meliconnect_import_is_disabled'], 'true'); ?>
                    </div>
                    <?php if (isset($import_data['meliconnect_general_sync_type']) && $import_data['meliconnect_general_sync_type'] != 'import'): ?>
                        <div class="meliconnect-notification meliconnect-is-warning">
                            <?php esc_html_e('Importing is disabled for automatic jobs in the general plugin settings. The current settings will only be applied to bulk or individual custom imports.', 'meliconnect'); ?>
                        </div>
                    <?php endif; ?>
                    <div class="meliconnect-content">

                        <div id="meliconnect-import-settings-meliconnect-columns" class="meliconnect-columns">
                            <div id="import-setting-left-meliconnect-column" class="meliconnect-column">
                                <div class="meliconnect-columns">
                                    <div class="meliconnect-column">
                                        <h3 class="meliconnect-title meliconnect-is-6"><?php esc_html_e('Product Information', 'meliconnect'); ?></h3>
                                    </div>
                                </div>

                                <div id="meliconnect-import-product-info">
                                    <?php self::print_setting_select('meliconnect_import_title', esc_html__('Title', 'meliconnect'), $import_data['meliconnect_import_title'], ['always' => esc_html__('Always', 'meliconnect'), 'on_create' => esc_html__('On Create', 'meliconnect')]); ?>

                                    <?php self::print_setting_select('meliconnect_import_stock', esc_html__('Stock', 'meliconnect'), $import_data['meliconnect_import_stock'], ['always' => esc_html__('Always', 'meliconnect'), 'on_create' => esc_html__('On Create', 'meliconnect')]); ?>

                                    <?php self::print_setting_select('meliconnect_import_price', esc_html__('Price', 'meliconnect'), $import_data['meliconnect_import_price'], ['always' => esc_html__('Always', 'meliconnect'), 'on_create' => esc_html__('On Create', 'meliconnect')]); ?>

                                    <?php self::print_setting_select('meliconnect_import_images', esc_html__('Images', 'meliconnect'), $import_data['meliconnect_import_images']); ?>

                                    <?php self::print_setting_select('meliconnect_import_sku', esc_html__('SKU', 'meliconnect'), $import_data['meliconnect_import_sku']); ?>

                                    <?php self::print_setting_select('meliconnect_import_categories', esc_html__('Categories', 'meliconnect'), $import_data['meliconnect_import_categories']); ?>

                                    <?php self::print_setting_select('meliconnect_import_product_attributes', esc_html__('Product Attributes', 'meliconnect'), $import_data['meliconnect_import_product_attributes']); ?>

                                    <?php self::print_setting_select('meliconnect_import_ml_status', esc_html__('MercadoLibre Status', 'meliconnect'), $import_data['meliconnect_import_ml_status']); ?>

                                    <?php self::print_setting_select('meliconnect_import_variations', esc_html__('Variations', 'meliconnect'), $import_data['meliconnect_import_variations']); ?>

                                    <?php self::print_setting_select('meliconnect_import_variations_as', esc_html__('Import variations as:', 'meliconnect'), $import_data['meliconnect_import_variations_as'], ['One product with variations' => esc_html__('One Product with Variations', 'meliconnect'), 'multiple products' => esc_html__('Multiple Products', 'meliconnect')]); ?>

                                    <?php self::print_setting_select('meliconnect_import_description', esc_html__('Description', 'meliconnect'), $import_data['meliconnect_import_description']); ?>

                                    <?php self::print_setting_select('meliconnect_import_description_to', esc_html__('Apply description to:', 'meliconnect'), $import_data['meliconnect_import_description_to'], ['description' => esc_html__('Long Description', 'meliconnect'), 'short_description' => esc_html__('Short Description', 'meliconnect')]); ?>
                                </div>
                            </div>
                            <div id="import-setting-right-meliconnect-column" class="meliconnect-column">
                                <div class="meliconnect-columns">
                                    <div class="meliconnect-column">
                                        <h3 class="meliconnect-title meliconnect-is-6"><?php esc_html_e('Actions', 'meliconnect'); ?></h3>
                                    </div>
                                </div>

                                <div class="meliconnect-columns meliconnect-mt-5">
                                    <div class="meliconnect-column meliconnect-is-4">
                                        <label for="finalize_ml" class="meliconnect-label"><?php esc_html_e('Importer Actions', 'meliconnect'); ?></label>
                                    </div>
                                    <div class="meliconnect-column meliconnect-is-8">
                                        <div class="meliconnect-select meliconnect-is-fullwidth">
                                            <select name="meliconnect_import_type" id="meliconnect_import_type">
                                                <option value="onlyUpdate" <?php selected($import_data['meliconnect_import_type'], 'onlyUpdate'); ?>><?php esc_html_e('Update Products Only', 'meliconnect'); ?></option>
                                                <option value="createAndUpdate" <?php selected($import_data['meliconnect_import_type'], 'createAndUpdate'); ?>><?php esc_html_e('Create and Update Products', 'meliconnect'); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="meliconnect-columns">
                                    <div class="meliconnect-column meliconnect-is-12">
                                        <h3 class="meliconnect-title meliconnect-is-6"><?php esc_html_e('Variations on Import', 'meliconnect'); ?></h3>
                                    </div>
                                </div>
                                <div class="meliconnect-columns">
                                    <div class="meliconnect-column">
                                        <p><?php esc_html_e('Each imported publication will have the following price or stock difference applied. If the product has an associated export template with a price or stock modification, it will be ignored.', 'meliconnect'); ?></p>
                                    </div>
                                </div>

                                <div class="meliconnect-columns">
                                    <div class="meliconnect-column meliconnect-is-12">
                                        <p><strong><?php esc_html_e('Price variation', 'meliconnect'); ?></strong></p>
                                    </div>
                                </div>
                                <div class="meliconnect-columns">
                                    <div class="meliconnect-column meliconnect-is-3">
                                        <div class="meliconnect-field">
                                            <div class="meliconnect-control">
                                                <div class="meliconnect-select meliconnect-is-fullwidth">
                                                    <select name="meliconnect_import_price_variation_operand" id="meliconnect_import_price_variation_operand">
                                                        <option value="sum" <?php selected($import_data['meliconnect_import_price_variation_operand'], 'sum'); ?>>+</option>
                                                        <option value="rest" <?php selected($import_data['meliconnect_import_price_variation_operand'], 'rest'); ?>>-</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="meliconnect-column meliconnect-is-5">
                                        <div class="meliconnect-field">
                                            <div class="meliconnect-control">
                                                <input type="number" name="meliconnect_import_price_variation_amount" value="<?php echo esc_attr($import_data['meliconnect_import_price_variation_amount']); ?>" min="0" id="meliconnect_import_price_variation_amount" class="meliconnect-input">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="meliconnect-column meliconnect-is-4">
                                        <div class="meliconnect-field">
                                            <div class="meliconnect-control">
                                                <div class="meliconnect-select meliconnect-is-fullwidth">
                                                    <select name="meliconnect_import_price_variation_type" id="meliconnect_import_price_variation_type">
                                                        <option value="percent" <?php selected($import_data['meliconnect_import_price_variation_type'], 'percent'); ?>><?php esc_html_e('Percentage Value (%)', 'meliconnect'); ?></option>
                                                        <option value="price" <?php selected($import_data['meliconnect_import_price_variation_type'], 'price'); ?>><?php esc_html_e('Fixed Value ($)', 'meliconnect'); ?></option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="meliconnect-columns">
                                    <div class="meliconnect-column meliconnect-is-12">
                                        <p><strong><?php esc_html_e('Stock variation', 'meliconnect'); ?></strong></p>
                                    </div>
                                </div>
                                <div class="meliconnect-columns">
                                    <div class="meliconnect-column meliconnect-is-3">
                                        <div class="meliconnect-field">
                                            <div class="meliconnect-control">
                                                <div class="meliconnect-select meliconnect-is-fullwidth">
                                                    <select name="meliconnect_import_stock_variation_operand" id="meliconnect_import_stock_variation_operand">
                                                        <option value="sum" <?php selected($import_data['meliconnect_import_stock_variation_operand'], 'sum'); ?>>+</option>
                                                        <option value="rest" <?php selected($import_data['meliconnect_import_stock_variation_operand'], 'rest'); ?>>-</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="meliconnect-column meliconnect-is-5">
                                        <div class="meliconnect-field">
                                            <div class="meliconnect-control">
                                                <input type="number" name="meliconnect_import_stock_variation_amount" value="<?php echo esc_attr($import_data['meliconnect_import_stock_variation_amount']); ?>" min="0" id="meliconnect_import_stock_variation_amount" class="meliconnect-input">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="meliconnect-column meliconnect-is-4">
                                        <div class="meliconnect-field">
                                            <div class="meliconnect-control">
                                                <div class="meliconnect-select meliconnect-is-fullwidth">
                                                    <select name="meliconnect_import_stock_variation_type" id="meliconnect_import_stock_variation_type">
                                                        <option value="units" <?php selected($import_data['meliconnect_import_stock_variation_type'], 'units'); ?>><?php esc_html_e('Units', 'meliconnect'); ?></option>

                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <?php self::print_setting_checkbox('meliconnect_import_state_paused', esc_html__('Create product from paused publications', 'meliconnect'), $import_data['meliconnect_import_state_paused'], 'true'); ?>

                                <?php self::print_setting_checkbox('meliconnect_import_state_closed', esc_html__('If the publication ends, move to trash', 'meliconnect'), $import_data['meliconnect_import_state_closed'], 'true'); ?>

                                <?php self::print_setting_checkbox('meliconnect_import_by_sku', esc_html__('If the publication is not linked, match the product by SKU or GTIN', 'meliconnect'), $import_data['meliconnect_import_by_sku'], 'true'); ?>

                                <?php self::print_setting_checkbox('meliconnect_import_attrs', esc_html__('Create attributes in WooCommerce and assign them to the imported product', 'meliconnect'), $import_data['meliconnect_import_attrs'], 'true'); ?>

                            </div>
                        </div>

                        <div class="meliconnect-columns">
                            <div class="meliconnect-column">
                                <div class="meliconnect-level">
                                    <div class="meliconnect-level-left">
                                    </div>
                                    <div class="meliconnect-level-right">

                                        <div class="meliconnect-field meliconnect-is-grouped">
                                            <p class="meliconnect-control">
                                                <button id="save-import-button" type="submit" class="meliconnect-button  meliconnect-is-primary"><?php esc_html_e('Save Import Settings', 'meliconnect'); ?></button>
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
