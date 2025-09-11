<form id="melicon-sync-settings-form">

    <input type="hidden" name="checkbox_fields" id="checkbox_fields" value="melicon_sync_stock_woo_to_meli,melicon_sync_price_woo_to_meli,melicon_sync_status_woo_to_meli,melicon_sync_stock_meli_to_woo,melicon_sync_price_meli_to_woo,melicon_sync_variations_price_meli_to_woo">

    <section class="melicon-section">
        <div class="melicon-container">
            <div class="melicon-columns">
                <div class="melicon-column">
                    <h2 class="melicon-title melicon-is-5"><?php esc_html_e('Automatic Synchronization', 'meliconnect'); ?></h2>

                    <div class="melicon-content">
                        <div class="melicon-columns melicon-is-mobile melicon-is-multiline">
                            <div class="melicon-column melicon-is-4">
                                <div class="melicon-field">
                                    <label class="melicon-label" for="melicon_sync_cron_status"><?php esc_html_e('Apply on', 'meliconnect'); ?></label>
                                    <div class="melicon-control">
                                        <div class="melicon-select melicon-is-fullwidth">
                                            <select name="melicon_sync_cron_status" id="melicon_sync_cron_status">
                                                <option value="deactive" <?php selected($sync_data['melicon_sync_cron_status'], 'deactive'); ?>><?php esc_html_e('Deactivate', 'meliconnect'); ?></option>
                                                <option value="active" <?php selected($sync_data['melicon_sync_cron_status'], 'active'); ?>><?php esc_html_e('Active', 'meliconnect'); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="melicon-column melicon-is-8">
                                <div class="melicon-columns melicon-melicon-is-mobile">
                                    <div class="melicon-column melicon-is-5">
                                        <div class="melicon-field">
                                            <label class="melicon-label" for="melicon_sync_cron_items_batch"><?php esc_html_e('Items per batch', 'meliconnect'); ?></label>
                                            <div class="melicon-control">
                                                <input class="melicon-input" type="number" min="1" name="melicon_sync_cron_items_batch" id="melicon_sync_cron_items_batch" value="<?php echo isset($sync_data['melicon_sync_cron_items_batch']) ? esc_attr($sync_data['melicon_sync_cron_items_batch']) : ''; ?>" min="1" max="1000">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="melicon-column melicon-is-4">
                                        <div class="melicon-field">
                                            <label class="melicon-label" for="melicon_sync_cron_frecuency_minutes"><?php esc_html_e('Frequency (minutes)', 'meliconnect'); ?></label>
                                            <div class="melicon-control">
                                                <input class="melicon-input" type="number" min="1" name="melicon_sync_cron_frecuency_minutes" id="melicon_sync_cron_frecuency_minutes" value="<?php echo isset($sync_data['melicon_sync_cron_frecuency_minutes']) ? esc_attr($sync_data['melicon_sync_cron_frecuency_minutes']) : ''; ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="melicon-column melicon-is-3">
                                        <div class="melicon-field">
                                            <label class="melicon-label" for="melicon_sync_cron_method"><?php esc_html_e('Method', 'meliconnect'); ?></label>
                                            <div class="melicon-control">
                                                <div class="melicon-select melicon-is-fullwidth">
                                                    <select name="melicon_sync_cron_method" id="melicon_sync_cron_method">
                                                        <option value="wordpress" <?php selected($sync_data['melicon_sync_cron_method'], 'wordpress'); ?>><?php esc_html_e('WordPress', 'meliconnect'); ?></option>
                                                        <option value="custom" <?php selected($sync_data['melicon_sync_cron_method'], 'custom'); ?>><?php esc_html_e('Custom', 'meliconnect'); ?></option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="melicon-columns melicon-mt-4">
                            <div class="melicon-column melicon-is-9">
                                <div class="melicon-content">
                                    <?php
                                    $scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';

                                    // Obtener el nombre del host
                                    $host = $_SERVER['HTTP_HOST'];

                                    // Construir la URL base
                                    $sync_url = $scheme . '://' . $host;
                                    ?>
                                    <strong><?php esc_html_e('External automatic synchronization URL (custom):', 'meliconnect'); ?></strong><code><?php echo esc_url($sync_url); ?>/wp-json/meliconnect/v1/cronexternal/sync</code>
                                </div>
                            </div>
                            <div class="melicon-column melicon-is-3">
                                <div class="melicon-field  melicon-is-grouped melicon-is-grouped-right">
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="melicon-columns">
                            <div id="sync-setting-left-melicon-column" class="melicon-column">
                                <div class="melicon-columns">
                                    <div class="melicon-column">
                                        <h3 class="melicon-title melicon-is-6"><?php esc_html_e('From Woo to Meli', 'meliconnect'); ?></h3>
                                    </div>
                                </div>
                                <div class="melicon-columns">
                                    <div class="melicon-column">
                                        <?php

                                        self::print_setting_checkbox(
                                            'melicon_sync_stock_woo_to_meli',
                                            esc_html__('Stock Synchronization', 'meliconnect'),
                                            $sync_data['melicon_sync_stock_woo_to_meli'],
                                            'true',
                                            esc_html__('When changing the STOCK in WooCommerce, WooCommerce hooks are used to capture the new stock and update it in MercadoLibre. <br>(Do not enable this feature if you have different stocks in both channels.)', 'meliconnect')
                                        );

                                        self::print_setting_checkbox(
                                            'melicon_sync_price_woo_to_meli',
                                            esc_html__('Price Synchronization', 'meliconnect'),
                                            $sync_data['melicon_sync_price_woo_to_meli'],
                                            'true',
                                            esc_html__('When changing the PRICE in WooCommerce, WooCommerce hooks are used to capture the new price and update it in MercadoLibre. <br>(Do not enable this feature if you have different prices in both channels.)', 'meliconnect')
                                        );

                                        self::print_setting_checkbox(
                                            'melicon_sync_status_woo_to_meli',
                                            esc_html__('Status Synchronization', 'meliconnect'),
                                            $sync_data['melicon_sync_status_woo_to_meli'],
                                            'true',
                                            esc_html__('When changing the STATUS in WooCommerce, update it in MercadoLibre. <br>(Do not enable this feature if you manage different product statuses in both channels.)', 'meliconnect')
                                        );
                                        ?>
                                    </div>
                                </div>


                            </div>
                            <div id="sync-setting-right-melicon-column" class="melicon-column">
                                <div class="melicon-columns">
                                    <div class="melicon-column">
                                        <h3 class="melicon-title melicon-is-6"><?php esc_html_e('From Meli to Woo', 'meliconnect'); ?></h3>
                                    </div>
                                </div>

                                <div class="melicon-columns">
                                    <div class="melicon-column">
                                        <?php

                                        self::print_setting_checkbox(
                                            'melicon_sync_stock_meli_to_woo',
                                            esc_html__('Stock Synchronization', 'meliconnect'),
                                            $sync_data['melicon_sync_stock_meli_to_woo'],
                                            'true',
                                            esc_html__('When changing the STOCK in MercadoLibre, update the same in WooCommerce. <br>(Do not enable this feature if you have different stocks in both channels.)', 'meliconnect')
                                        );

                                        self::print_setting_checkbox(
                                            'melicon_sync_price_meli_to_woo',
                                            esc_html__('Price Synchronization', 'meliconnect'),
                                            $sync_data['melicon_sync_price_meli_to_woo'],
                                            'true',
                                            esc_html__('When changing the PRICE in MercadoLibre, update the same in WooCommerce. <br>(Do not enable this feature if you have different prices in both channels.)', 'meliconnect')
                                        );

                                        self::print_setting_checkbox(
                                            'melicon_sync_variations_price_meli_to_woo',
                                            esc_html__('Variation Price Synchronization', 'meliconnect'),
                                            $sync_data['melicon_sync_variations_price_meli_to_woo'],
                                            'true',
                                            esc_html__('When changing the PRICE in MercadoLibre, update it across all product variations in WooCommerce. <br>(Do not enable this feature if you have different prices in both channels.)', 'meliconnect')
                                        );


                                        ?>
                                    </div>
                                </div>
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
                                                <button id="save-sync-button" type="submit" class="melicon-button  melicon-is-primary"><?php esc_html_e('Save Sync Settings', 'meliconnect'); ?></button>
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

