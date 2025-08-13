<div class="melicon-container">
    <div class="melicon-has-text-centered inside">
        <?php if (!empty($meli_listing_id)): ?>
            <!-- To Update Section -->
            <span class="melicon-tag melicon-is-primary melicon-is-medium melicon-mt-3"><?php esc_html_e('To Update', 'meliconnect'); ?></span>
            <?php if (!empty($meli_permalink)): ?>
                <div class="melicon-field melicon-m-3">
                    <a class=" melicon-is-size-6 " href="<?php echo esc_url($meli_permalink); ?>" target="_blank">
                        <i class="fas fa-external-link-alt"></i> <?php esc_html_e('View on Meli', 'meliconnect'); ?>
                    </a>
                </div>
            <?php endif; ?>
            <div class="melicon-mt-2">
                <p><strong ><?php esc_html_e('Listing ID', 'meliconnect'); ?>:</strong> <?php echo esc_html($meli_listing_id); ?></p>
                <p><strong ><?php esc_html_e('Seller ID', 'meliconnect'); ?>:</strong> <?php echo esc_html($seller_id); ?></p>
                <p><strong ><?php esc_html_e('Meli Status', 'meliconnect'); ?>:</strong> <span class="melicon-tag melicon-is-primary melicon-is-light"><?php echo esc_html($meli_status); ?></span></p>
            </div>

            <div class="melicon-field melicon-has-addons melicon-is-justify-content-center melicon-mt-5">
                <div class="melicon-control">
                    <div class="melicon-select melicon-is-fullwidth">
                        <select id="melicon-desviculate-type-select">
                            <option value="desvinculate"><?php esc_html_e('Desvinculate', 'meliconnect'); ?></option>
                            <option value="desvinculate_pause"><?php esc_html_e('Desvinculate and Pause on Meli', 'meliconnect'); ?></option>
                            <option value="desvinculate_delete"><?php esc_html_e('Desvinculate and Delete on Meli', 'meliconnect'); ?></option>
                        </select>
                    </div>
                </div>
                <button
                    class="melicon-button melicon-is-warning"
                    id="melicon_unlink_meli_listing"
                    data-woo-product-id="<?php echo esc_attr($woo_product_id); ?>">
                    <span class="melicon-icon"><i class="fas fa-play"></i></span>
                </button>
            </div>
        <?php else: ?>
            <!-- To Create Section -->
            <span class="melicon-tag melicon-is-success melicon-is-medium melicon-mb-3"><?php esc_html_e('To Create', 'meliconnect'); ?></span>

            <?php if (!empty($template_id)): ?>
                <div class="melicon-field">
                    <p class="melicon-control">
                        <button
                            class="melicon-button melicon-is-warning"
                            id="melicon_export_meli"
                            data-meli-listing-id="<?php echo esc_attr($meli_listing_id); ?>"
                            data-woo-product-id="<?php echo esc_attr($woo_product_id); ?>"
                            data-template-id="<?php echo esc_attr($template_id); ?>"
                            data-seller-id="<?php echo esc_attr($seller_id); ?>">
                            <?php esc_html_e('Create Listing', 'meliconnect'); ?>
                        </button>
                    </p>
                </div>
            <?php else: ?>
                <div class="melicon-field">
                    <button type="button" class="melicon-button melicon-is-info" id="melicon_create_template_button"><?php esc_html_e('Create Product Template', 'meliconnect'); ?></button>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
