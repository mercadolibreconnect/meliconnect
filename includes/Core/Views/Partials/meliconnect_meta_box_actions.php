<?php
if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>
<div id="meliconnect-loader" class="melicon-has-text-centered">
    <i class="fas fa-spinner fa-spin fa-2x"></i>
    <p><?php esc_html_e('Loading...', 'meliconnect'); ?></p>
</div>

<div id="meliconnect-box" style="display: none;">
    <div class="melicon-field">
        <input id="mely_sync_all"
            type="checkbox"
            name="mely_sync_all"
            class="switch melicon-is-rounded is-warning"
            checked>
        <label for="mely_sync_all" class="melicon-label"><?php esc_html_e('Sync All Data', 'meliconnect'); ?></label>
    </div>


    <div class="melicon-field select-data-to-sync-field">
        <label class="melicon-label"><?php esc_html_e('Select Data to Sync', 'meliconnect'); ?></label>
        <div class="melicon-control">
            <select id="meli_sync_options" multiple style="width:100%">
                <option value="title"><?php esc_html_e('Title', 'meliconnect'); ?></option>
                <option value="sku"><?php esc_html_e('SKU', 'meliconnect'); ?></option>
                <option value="gtin"><?php esc_html_e('GTIN', 'meliconnect'); ?></option>
                <option value="attributes"><?php esc_html_e('Attributes', 'meliconnect'); ?></option>
                <option value="categories"><?php esc_html_e('Categories', 'meliconnect'); ?></option>
                <option value="description"><?php esc_html_e('Description', 'meliconnect'); ?></option>
                <option value="images"><?php esc_html_e('Images', 'meliconnect'); ?></option>
                <option value="price"><?php esc_html_e('Price', 'meliconnect'); ?></option>
                <option value="stock"><?php esc_html_e('Stock', 'meliconnect'); ?></option>
                <option value="status"><?php esc_html_e('Status', 'meliconnect'); ?></option>
                <option value="variations"><?php esc_html_e('Variations', 'meliconnect'); ?></option>
            </select>
        </div>
    </div>

    <div class="melicon-field">
        <label class="melicon-label"><?php esc_html_e('Action', 'meliconnect'); ?></label>
        <div class="melicon-control">
            <div class="melicon-select">
                <select id="meli_sync_action">
                    <option value="export"><?php esc_html_e('Export to MercadoLibre', 'meliconnect'); ?></option>
                    <option value="import"><?php esc_html_e('Import from MercadoLibre', 'meliconnect'); ?></option>
                </select>
            </div>
        </div>
    </div>

    <button id="sync-button" class="melicon-button melicon-is-primary melicon-is-fullwidth" type="button"
        data-meli-listing-id="<?php echo esc_attr($meli_listing_id); ?>"
        data-woo-product-id="<?php echo esc_attr($woo_product_id); ?>"
        data-template-id="<?php echo esc_attr($template_id); ?>"
        data-seller-id="<?php echo esc_attr($seller_id); ?>">
        <span class="melicon-icon melicon-is-small"><i class="fas fa-sync-alt"></i></span>
        <span> <?php esc_html_e('Sync Now', 'meliconnect'); ?> </span>
    </button>
</div>