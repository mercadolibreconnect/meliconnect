<form id="melicon-export-settings-form">
    <input type="hidden" name="checkbox_fields" id="checkbox_fields" value="melicon_export_is_disabled,melicon_export_state_paused,melicon_export_state_closed">
    <section class="section">
        <div class="container">

            <div class="columns">
                <div class="column">
                    <h2 class="title is-5"><?php _e('Export', 'meliconnect'); ?></h2>
                    <div class="content">

                        <div class="mb-6">
                            <?php self::print_setting_checkbox('melicon_export_is_disabled', __('Disable Export', 'meliconnect'), $export_data['melicon_export_is_disabled'], 'true'); ?>
                        </div>

                        <?php if (isset($export_data['melicon_general_sync_type']) && $export_data['melicon_general_sync_type'] != 'export'): ?>
                            <div class="notification is-warning">
                                <?php _e('Exporting is disabled for automatic jobs in the general plugin settings. The current settings will only be applied to bulk or individual custom exports.', 'meliconnect'); ?>
                            </div>
                        <?php endif; ?>
                        <div id="melicon-export-settings-columns" class="columns">
                            <div id="export-setting-left-column" class="column">
                                <div class="columns">
                                    <div class="column">
                                        <h3 class="title is-6"><?php _e('Product Information', 'meliconnect'); ?></h3>
                                    </div>
                                </div>

                                <div id="melicon-export-product-info">
                                    <?php self::print_setting_select('melicon_export_title', __('Title', 'meliconnect'), $export_data['melicon_export_title'], ['always' => __('Always', 'meliconnect'), 'on_create' => __('On Create', 'meliconnect')]); ?>

                                    <?php self::print_setting_select('melicon_export_stock', __('Stock', 'meliconnect'), $export_data['melicon_export_stock'], ['always' => __('Always', 'meliconnect'), 'on_create' => __('On Create', 'meliconnect')]); ?>

                                    <?php self::print_setting_select('melicon_export_price', __('Price', 'meliconnect'), $export_data['melicon_export_price'], ['always' => __('Always', 'meliconnect'), 'on_create' => __('On Create', 'meliconnect')]); ?>

                                    <?php self::print_setting_select('melicon_export_images', __('Images', 'meliconnect'), $export_data['melicon_export_images']); ?>

                                    <?php self::print_setting_select('melicon_export_sku', __('SKU', 'meliconnect'), $export_data['melicon_export_sku']); ?>

                                    <?php self::print_setting_select('melicon_export_product_attributes', __('Product Attributes', 'meliconnect'), $export_data['melicon_export_product_attributes']); ?>

                                    <?php self::print_setting_select('melicon_export_ml_status', __('Woo Status', 'meliconnect'), $export_data['melicon_export_ml_status']); ?>

                                    <?php self::print_setting_select('melicon_export_variations', __('Variations', 'meliconnect'), $export_data['melicon_export_variations']); ?>

                                    <?php self::print_setting_select('melicon_export_description', __('Description', 'meliconnect'), $export_data['melicon_export_description']); ?>

                                    <?php self::print_setting_select('melicon_export_description_to', __('Use description:', 'meliconnect'), $export_data['melicon_export_description_to'], ['description' => __('Long Description', 'meliconnect'), 'short_description' => __('Short Description', 'meliconnect')]); ?>
                                </div>
                            </div>
                            <div id="export-setting-right-column" class="column">
                                <div class="columns">
                                    <div class="column">
                                        <h3 class="title is-6"><?php _e('Actions', 'meliconnect'); ?></h3>
                                    </div>
                                </div>

                                <div class="columns mt-5">
                                    <div class="column is-4">
                                        <label for="melicon_export_type" class="label"><?php _e('Exporter Actions', 'meliconnect'); ?></label>
                                    </div>
                                    <div class="column is-8">
                                        <div class="melicon-select select is-fullwidth">
                                            <select name="melicon_export_type" id="melicon_export_type">
                                                <option value="onlyUpdate" <?php selected($export_data['melicon_export_type'], 'onlyUpdate'); ?>><?php _e('Update Products Only', 'meliconnect'); ?></option>
                                                <option value="createAndUpdate" <?php selected($export_data['melicon_export_type'], 'createAndUpdate'); ?>><?php _e('Create and Update Products', 'meliconnect'); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="columns mt-5">
                                    <div class="column is-4">
                                        <label for="melicon_export_finalize_ml" class="label"><?php _e('When deleting the product', 'meliconnect'); ?></label>
                                    </div>
                                    <div class="column is-8">
                                        <div class="melicon-select select is-fullwidth">
                                            <select name="melicon_export_finalize_ml" id="melicon_export_finalize_ml">
                                                <option value="none" <?php selected($export_data['melicon_export_finalize_ml'], 'none'); ?>><?php _e('Do nothing', 'meliconnect'); ?></option>
                                                <option value="pause" <?php selected($export_data['melicon_export_finalize_ml'], 'pause'); ?>><?php _e('Pause publication', 'meliconnect'); ?></option>
                                                <option value="delete" <?php selected($export_data['melicon_export_finalize_ml'], 'delete'); ?>><?php _e('Delete publication', 'meliconnect'); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <?php self::print_setting_checkbox('melicon_export_state_paused', __('Create product from paused publications', 'meliconnect'), $export_data['melicon_export_state_paused'], 'true'); ?>

                                <?php self::print_setting_checkbox('melicon_export_state_closed', __('If the publication ends, move to trash', 'meliconnect'), $export_data['melicon_export_state_closed'], 'true'); ?>

                            </div>
                        </div>

                        <hr>
                        <div class="columns">
                            <div class="column">
                                <div class="level">
                                    <div class="level-left"></div>
                                    <div class="level-right">
                                        <div class="melicon-field field is-grouped">
                                            <p class="melicon-control control">
                                                <button id="save-export-button" type="submit" class="button-meliconnect button is-primary"><?php _e('Save export settings', 'meliconnect'); ?></button>
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

        function toggleExportSettings(isDisabled) {
            if (isDisabled) {
                $('#melicon-export-settings-columns input, #melicon-export-settings-columns select, #melicon-export-settings-columns textarea').prop('disabled', true);
            } else {
                $('#melicon-export-settings-columns input, #melicon-export-settings-columns select, #melicon-export-settings-columns textarea').prop('disabled', false);
            }
        }

        toggleExportSettings($('#melicon_export_is_disabled').prop('checked'));

        $('#melicon-export-settings-form').submit(function(e) {
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
                    $('#save-export-button').addClass('is-loading');
                },
                success: function(response) {
                    location.reload();
                },
                error: function(xhr, status, error) {
                    $('#save-export-button').removeClass('is-loading');
                    console.log(xhr.responseText);
                }
            });
        });

        $('#melicon_export_is_disabled').on('change', function(e) {
            var checkbox = $(this);

            // Prevent default checkbox action
            e.preventDefault();

            if (!checkbox.prop('checked')) {
                toggleExportSettings(false);
            } else {
                Bulma().alert({
                    type: 'warning',
                    title: mcTranslations.alert_title_disable_export,
                    body: mcTranslations.alert_body_disable_export,
                    confirm: {
                        label: mcTranslations.confirm,
                        classes: ['button', 'button-meliconnect'],
                        onClick: function() {
                            toggleExportSettings(true);
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