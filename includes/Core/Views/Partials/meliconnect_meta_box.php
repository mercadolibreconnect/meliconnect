<div class="container">
    <div class="meliconnect-box has-text-centered">
        <?php if (!empty($meli_listing_id)): ?>
            <!-- To Update Section -->
            <span class="tag is-primary is-medium"><?php _e('To Update', 'meliconnect'); ?></span>
            <?php if (!empty($meli_permalink)): ?>
                <div class="field">
                    <a class="mt-2" href="<?php echo esc_url($meli_permalink); ?>" target="_blank">
                        <?php _e('View on Meli', 'meliconnect'); ?>
                    </a>
                </div>
            <?php endif; ?>
            <div>
                <p><strong><?php _e('Listing ID', 'meliconnect'); ?>:</strong> <?php echo esc_html($meli_listing_id); ?></p>
                <p><strong><?php _e('Seller ID', 'meliconnect'); ?>:</strong> <?php echo esc_html($seller_id); ?></p>
                <p><strong><?php _e('Meli Status', 'meliconnect'); ?>:</strong> <?php echo esc_html($meli_status); ?></p>
            </div>
            <div class="field is-grouped is-justify-content-center">
                <p class="control">
                    <button
                        class="button is-primary"
                        id="melicon_import_meli"
                        data-meli-listing-id="<?php echo esc_attr($meli_listing_id); ?>"
                        data-woo-product-id="<?php echo esc_attr($woo_product_id); ?>"
                        data-template-id="<?php echo esc_attr($template_id); ?>"
                        data-seller-id="<?php echo esc_attr($seller_id); ?>">
                        <?php _e('Import', 'meliconnect'); ?>
                    </button>
                </p>
                <p class="control">
                    <button
                        class="button is-success"
                        id="melicon_export_meli"
                        data-meli-listing-id="<?php echo esc_attr($meli_listing_id); ?>"
                        data-woo-product-id="<?php echo esc_attr($woo_product_id); ?>"
                        data-template-id="<?php echo esc_attr($template_id); ?>"
                        data-seller-id="<?php echo esc_attr($seller_id); ?>">
                        <?php _e('Export', 'meliconnect'); ?>
                    </button>
                </p>
            </div>
            <hr>
            <div class="field has-addons is-justify-content-center">
                <div class="control melicon-control">
                    <div class="select is-fullwidth melicon-select">
                        <select id="melicon-desviculate-type-select">
                            <option value="desvinculate"><?php _e('Desvinculate', 'meliconnect'); ?></option>
                            <option value="desvinculate_pause"><?php _e('Desvinculate and Pause on Meli', 'meliconnect'); ?></option>
                            <option value="desvinculate_delete"><?php _e('Desvinculate and Delete on Meli', 'meliconnect'); ?></option>
                        </select>
                    </div>
                </div>
                <button
                    class="button is-warning melicon-button"
                    id="melicon_unlink_meli_listing"
                    data-woo-product-id="<?php echo esc_attr($woo_product_id); ?>
                ">

                    <span class="icon"><i class="fas fa-play"></i></span>
                </button>
            </div>


        <?php else: ?>
            <!-- To Create Section -->
            <span class="tag is-success is-medium"><?php _e('To Create', 'meliconnect'); ?></span>
            <?php if (!empty($template_id)): ?>
                <!-- <div class="field">
                    <label class="label"><?php _e('Meli Listing ID:', 'meliconnect'); ?></label>
                    <div class="control">
                        <input class="input" type="text" id="meli_listing_id_input" placeholder="<?php _e('Enter Meli Listing ID', 'meliconnect'); ?>">
                    </div>
                </div>
                <div class="field">
                    <button class="button is-primary" id="save-meli-listing-id"><?php _e('Save Meli Listing ID', 'meliconnect'); ?></button>
                </div> -->
                <div class="field">
                    <p class="control">
                        <button
                            class="button is-success"
                            id="melicon_export_meli"
                            data-meli-listing-id="<?php echo esc_attr($meli_listing_id); ?>"
                            data-woo-product-id="<?php echo esc_attr($woo_product_id); ?>"
                            data-template-id="<?php echo esc_attr($template_id); ?>"
                            data-seller-id="<?php echo esc_attr($seller_id); ?>">
                            <?php _e('Export', 'meliconnect'); ?>
                        </button>
                    </p>
                </div>
            <?php else: ?>
                <div class="field">
                    <button type="button" class="button is-info" id="melicon_create_template_button"><?php _e('Create Product Template', 'meliconnect'); ?></button>
                </div>
            <?php endif; ?>
            <!-- -->
        <?php endif; ?>
    </div>
</div>