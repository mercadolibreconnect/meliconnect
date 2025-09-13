<?php
if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>
<div id="meliconnect-loader" class="meliconnect-has-text-centered">
    <i class="fas fa-spinner fa-spin fa-2x"></i>
    <p><?php esc_html_e('Loading...', 'meliconnect'); ?></p>
</div>

<div id="meliconnect-box" style="display: none;">
    <div class="meliconnect-field">
        <input id="mely_sync_all"
            type="checkbox"
            name="mely_sync_all"
            class="switch meliconnect-is-rounded is-warning"
            checked>
        <label for="mely_sync_all" class="meliconnect-label"><?php esc_html_e('Sync All Data', 'meliconnect'); ?></label>
    </div>


    <div class="meliconnect-field select-data-to-sync-field">
        <label class="meliconnect-label"><?php esc_html_e('Select Data to Sync', 'meliconnect'); ?></label>
        <div class="meliconnect-control">
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

    <div class="meliconnect-field">
        <label class="meliconnect-label"><?php esc_html_e('Action', 'meliconnect'); ?></label>
        <div class="meliconnect-control">
            <div class="meliconnect-select">
                <select id="meli_sync_action">
                    <option value="export"><?php esc_html_e('Export to MercadoLibre', 'meliconnect'); ?></option>
                    <option value="import"><?php esc_html_e('Import from MercadoLibre', 'meliconnect'); ?></option>
                </select>
            </div>
        </div>
    </div>

    <button id="sync-button" class="meliconnect-button meliconnect-is-primary meliconnect-is-fullwidth" type="button"
        data-meli-listing-id="<?php echo esc_attr($meli_listing_id); ?>"
        data-woo-product-id="<?php echo esc_attr($woo_product_id); ?>"
        data-template-id="<?php echo esc_attr($template_id); ?>"
        data-seller-id="<?php echo esc_attr($seller_id); ?>">
        <span class="meliconnect-icon meliconnect-is-small"><i class="fas fa-sync-alt"></i></span>
        <span> <?php esc_html_e('Sync Now', 'meliconnect'); ?> </span>
    </button>
</div>