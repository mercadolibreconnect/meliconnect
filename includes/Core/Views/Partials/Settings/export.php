<form id="melicon-export-settings-form">
    <input type="hidden" name="checkbox_fields" id="checkbox_fields" value="melicon_export_is_disabled,melicon_export_state_paused,melicon_export_state_closed">
    <section class="melicon-section">
        <div class="melicon-container">

            <div class="melicon-columns">
                <div class="melicon-column">
                    <h2 class="melicon-title melicon-is-5"><?php esc_html_e('Export', 'meliconnect'); ?></h2>
                    <div class="melicon-content">

                        <div class="mb-6">
                            <?php self::print_setting_checkbox('melicon_export_is_disabled', esc_html__('Disable Export', 'meliconnect'), $export_data['melicon_export_is_disabled'], 'true'); ?>
                        </div>

                        <?php if (isset($export_data['melicon_general_sync_type']) && $export_data['melicon_general_sync_type'] != 'export'): ?>
                            <div class="melicon-notification melicon-is-warning">
                                <?php esc_html_e('Exporting is disabled for automatic jobs in the general plugin settings. The current settings will only be applied to bulk or individual custom exports.', 'meliconnect'); ?>
                            </div>
                        <?php endif; ?>
                        <div id="melicon-export-settings-melicon-columns" class="melicon-columns">
                            <div id="export-setting-left-melicon-column" class="melicon-column">
                                <div class="melicon-columns">
                                    <div class="melicon-column">
                                        <h3 class="melicon-title melicon-is-6"><?php esc_html_e('Product Information', 'meliconnect'); ?></h3>
                                    </div>
                                </div>

                                <div id="melicon-export-product-info">
                                    <?php self::print_setting_select('melicon_export_title', esc_html__('Title', 'meliconnect'), $export_data['melicon_export_title'], ['always' => esc_html__('Always', 'meliconnect'), 'on_create' => esc_html__('On Create', 'meliconnect')]); ?>

                                    <?php self::print_setting_select('melicon_export_stock', esc_html__('Stock', 'meliconnect'), $export_data['melicon_export_stock'], ['always' => esc_html__('Always', 'meliconnect'), 'on_create' => esc_html__('On Create', 'meliconnect')]); ?>

                                    <?php self::print_setting_select('melicon_export_price', esc_html__('Price', 'meliconnect'), $export_data['melicon_export_price'], ['always' => esc_html__('Always', 'meliconnect'), 'on_create' => esc_html__('On Create', 'meliconnect')]); ?>

                                    <?php self::print_setting_select('melicon_export_images', esc_html__('Images', 'meliconnect'), $export_data['melicon_export_images']); ?>

                                    <?php self::print_setting_select('melicon_export_sku', esc_html__('SKU', 'meliconnect'), $export_data['melicon_export_sku']); ?>

                                    <?php self::print_setting_select('melicon_export_product_attributes', esc_html__('Product Attributes', 'meliconnect'), $export_data['melicon_export_product_attributes']); ?>

                                    <?php self::print_setting_select('melicon_export_ml_status', esc_html__('Woo Status', 'meliconnect'), $export_data['melicon_export_ml_status']); ?>

                                    <?php self::print_setting_select('melicon_export_variations', esc_html__('Variations', 'meliconnect'), $export_data['melicon_export_variations']); ?>

                                    <?php self::print_setting_select('melicon_export_description', esc_html__('Description', 'meliconnect'), $export_data['melicon_export_description']); ?>

                                    <?php self::print_setting_select('melicon_export_description_to', esc_html__('Use description:', 'meliconnect'), $export_data['melicon_export_description_to'], ['description' => esc_html__('Long Description', 'meliconnect'), 'short_description' => esc_html__('Short Description', 'meliconnect')]); ?>
                                </div>
                            </div>
                            <div id="export-setting-right-melicon-column" class="melicon-column">
                                <div class="melicon-columns">
                                    <div class="melicon-column">
                                        <h3 class="melicon-title melicon-is-6"><?php esc_html_e('Actions', 'meliconnect'); ?></h3>
                                    </div>
                                </div>

                                <div class="melicon-columns melicon-mt-5">
                                    <div class="melicon-column melicon-is-4">
                                        <label for="melicon_export_type" class="melicon-label"><?php esc_html_e('Exporter Actions', 'meliconnect'); ?></label>
                                    </div>
                                    <div class="melicon-column melicon-is-8">
                                        <div class="melicon-select  melicon-is-fullwidth">
                                            <select name="melicon_export_type" id="melicon_export_type">
                                                <option value="onlyUpdate" <?php selected($export_data['melicon_export_type'], 'onlyUpdate'); ?>><?php esc_html_e('Update Products Only', 'meliconnect'); ?></option>
                                                <option value="createAndUpdate" <?php selected($export_data['melicon_export_type'], 'createAndUpdate'); ?>><?php esc_html_e('Create and Update Products', 'meliconnect'); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="melicon-columns melicon-mt-5">
                                    <div class="melicon-column melicon-is-4">
                                        <label for="melicon_export_finalize_ml" class="melicon-label"><?php esc_html_e('When deleting the product', 'meliconnect'); ?></label>
                                    </div>
                                    <div class="melicon-column melicon-is-8">
                                        <div class="melicon-select melicon-is-fullwidth">
                                            <select name="melicon_export_finalize_ml" id="melicon_export_finalize_ml">
                                                <option value="none" <?php selected($export_data['melicon_export_finalize_ml'], 'none'); ?>><?php esc_html_e('Do nothing', 'meliconnect'); ?></option>
                                                <option value="pause" <?php selected($export_data['melicon_export_finalize_ml'], 'pause'); ?>><?php esc_html_e('Pause publication', 'meliconnect'); ?></option>
                                                <option value="delete" <?php selected($export_data['melicon_export_finalize_ml'], 'delete'); ?>><?php esc_html_e('Delete publication', 'meliconnect'); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <?php self::print_setting_checkbox('melicon_export_state_paused', esc_html__('Create product from paused publications', 'meliconnect'), $export_data['melicon_export_state_paused'], 'true'); ?>

                                <?php self::print_setting_checkbox('melicon_export_state_closed', esc_html__('If the publication ends, move to trash', 'meliconnect'), $export_data['melicon_export_state_closed'], 'true'); ?>

                            </div>
                        </div>

                        <hr>
                        <div class="melicon-columns">
                            <div class="melicon-column">
                                <div class="melicon-level">
                                    <div class="melicon-level-left"></div>
                                    <div class="melicon-level-right">
                                        <div class="melicon-field melicon-is-grouped">
                                            <p class="melicon-control">
                                                <button id="save-export-button" type="submit" class="melicon-button  melicon-is-primary"><?php esc_html_e('Save export settings', 'meliconnect'); ?></button>
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
