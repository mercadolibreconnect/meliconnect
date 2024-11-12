<form id="melicon-sync-settings-form">

    <input type="hidden" name="checkbox_fields" id="checkbox_fields" value="melicon_sync_stock_woo_to_meli,melicon_sync_price_woo_to_meli,melicon_sync_status_woo_to_meli,melicon_sync_stock_meli_to_woo,melicon_sync_price_meli_to_woo,melicon_sync_variations_price_meli_to_woo">

    <section class="section">
        <div class="container">
            <div class="columns">
                <div class="column">
                    <h2 class="title is-5"><?php _e('Automatic Synchronization', 'meliconnect'); ?></h2>

                    <div class="content">
                        <div class="columns is-mobile is-multiline">
                            <div class="column is-4">
                                <div class="melicon-field field">
                                    <label class="label" for="melicon_sync_cron_status"><?php _e('Apply on', 'meliconnect'); ?></label>
                                    <div class="melicon-control control">
                                        <div class="melicon-select select is-fullwidth">
                                            <select name="melicon_sync_cron_status" id="melicon_sync_cron_status">
                                                <option value="deactive" <?php selected($sync_data['melicon_sync_cron_status'], 'deactive'); ?>><?php _e('Deactivate', 'meliconnect'); ?></option>
                                                <option value="active" <?php selected($sync_data['melicon_sync_cron_status'], 'active'); ?>><?php _e('Active', 'meliconnect'); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="column is-8">
                                <div class="columns is-mobile">
                                    <div class="column is-5">
                                        <div class="melicon-field field">
                                            <label class="label" for="melicon_sync_cron_items_batch"><?php _e('Items per batch', 'meliconnect'); ?></label>
                                            <div class="melicon-control control">
                                                <input class="input" type="number" min="1" name="melicon_sync_cron_items_batch" id="melicon_sync_cron_items_batch" value="<?php echo isset($sync_data['melicon_sync_cron_items_batch']) ? esc_attr($sync_data['melicon_sync_cron_items_batch']) : ''; ?>" min="1" max="1000">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="column is-4">
                                        <div class="melicon-field field">
                                            <label class="label" for="melicon_sync_cron_frecuency_minutes"><?php _e('Frequency (minutes)', 'meliconnect'); ?></label>
                                            <div class="melicon-control control">
                                                <input class="input" type="number" min="1" name="melicon_sync_cron_frecuency_minutes" id="melicon_sync_cron_frecuency_minutes" value="<?php echo isset($sync_data['melicon_sync_cron_frecuency_minutes']) ? esc_attr($sync_data['melicon_sync_cron_frecuency_minutes']) : ''; ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="column is-3">
                                        <div class="melicon-field field">
                                            <label class="label" for="melicon_sync_cron_method"><?php _e('Method', 'meliconnect'); ?></label>
                                            <div class="melicon-control control">
                                                <div class="melicon-select select is-fullwidth">
                                                    <select name="melicon_sync_cron_method" id="melicon_sync_cron_method">
                                                        <option value="wordpress" <?php selected($sync_data['melicon_sync_cron_method'], 'wordpress'); ?>><?php _e('WordPress', 'meliconnect'); ?></option>
                                                        <option value="custom" <?php selected($sync_data['melicon_sync_cron_method'], 'custom'); ?>><?php _e('Custom', 'meliconnect'); ?></option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="columns mt-4">
                            <div class="column is-9">
                                <div class="content">
                                    <?php
                                    $scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';

                                    // Obtener el nombre del host
                                    $host = $_SERVER['HTTP_HOST'];

                                    // Construir la URL base
                                    $sync_url = $scheme . '://' . $host;
                                    ?>
                                    <strong><?php _e('External automatic synchronization URL (custom):', 'meliconnect'); ?></strong> <code><?php echo $sync_url; ?>/wp-json/meliconnect/v1/cronexternal/sync</code>
                                </div>
                            </div>
                            <div class="column is-3">
                                <div class="melicon-field field is-grouped is-grouped-right">
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="columns">
                            <div id="sync-setting-left-column" class="column">
                                <div class="columns">
                                    <div class="column">
                                        <h3 class="title is-6"><?php _e('From Woo to Meli', 'meliconnect'); ?></h3>
                                    </div>
                                </div>
                                <div class="columns">
                                    <div class="column">
                                        <?php

                                        self::print_setting_checkbox(
                                            'melicon_sync_stock_woo_to_meli',
                                            __('Stock Synchronization', 'meliconnect'),
                                            $sync_data['melicon_sync_stock_woo_to_meli'],
                                            'true',
                                            __('When changing the STOCK in WooCommerce, WooCommerce hooks are used to capture the new stock and update it in MercadoLibre. <br>(Do not enable this feature if you have different stocks in both channels.)', 'meliconnect')
                                        );

                                        self::print_setting_checkbox(
                                            'melicon_sync_price_woo_to_meli',
                                            __('Price Synchronization', 'meliconnect'),
                                            $sync_data['melicon_sync_price_woo_to_meli'],
                                            'true',
                                            __('When changing the PRICE in WooCommerce, WooCommerce hooks are used to capture the new price and update it in MercadoLibre. <br>(Do not enable this feature if you have different prices in both channels.)', 'meliconnect')
                                        );

                                        self::print_setting_checkbox(
                                            'melicon_sync_status_woo_to_meli',
                                            __('Status Synchronization', 'meliconnect'),
                                            $sync_data['melicon_sync_status_woo_to_meli'],
                                            'true',
                                            __('When changing the STATUS in WooCommerce, update it in MercadoLibre. <br>(Do not enable this feature if you manage different product statuses in both channels.)', 'meliconnect')
                                        );
                                        ?>
                                    </div>
                                </div>


                            </div>
                            <div id="sync-setting-right-column" class="column">
                                <div class="columns">
                                    <div class="column">
                                        <h3 class="title is-6"><?php _e('From Meli to Woo', 'meliconnect'); ?></h3>
                                    </div>
                                </div>

                                <div class="columns">
                                    <div class="column">
                                        <?php

                                        self::print_setting_checkbox(
                                            'melicon_sync_stock_meli_to_woo',
                                            __('Stock Synchronization', 'meliconnect'),
                                            $sync_data['melicon_sync_stock_meli_to_woo'],
                                            'true',
                                            __('When changing the STOCK in MercadoLibre, update the same in WooCommerce. <br>(Do not enable this feature if you have different stocks in both channels.)', 'meliconnect')
                                        );

                                        self::print_setting_checkbox(
                                            'melicon_sync_price_meli_to_woo',
                                            __('Price Synchronization', 'meliconnect'),
                                            $sync_data['melicon_sync_price_meli_to_woo'],
                                            'true',
                                            __('When changing the PRICE in MercadoLibre, update the same in WooCommerce. <br>(Do not enable this feature if you have different prices in both channels.)', 'meliconnect')
                                        );

                                        self::print_setting_checkbox(
                                            'melicon_sync_variations_price_meli_to_woo',
                                            __('Variation Price Synchronization', 'meliconnect'),
                                            $sync_data['melicon_sync_variations_price_meli_to_woo'],
                                            'true',
                                            __('When changing the PRICE in MercadoLibre, update it across all product variations in WooCommerce. <br>(Do not enable this feature if you have different prices in both channels.)', 'meliconnect')
                                        );


                                        ?>
                                    </div>
                                </div>
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
                                                <button id="save-sync-button" type="submit" class="button-meliconnect button is-primary"><?php _e('Save Sync Settings', 'meliconnect'); ?></button>
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
        $('#melicon-sync-settings-form').submit(function(e) {
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
                    $('#save-sync-button').addClass('is-loading');
                },
                success: function(response) {
                    location.reload();
                },
                error: function(xhr, status, error) {
                    $('#save-sync-button').removeClass('is-loading');
                    console.log(xhr.responseText);
                }
            });
        });
    });
</script>