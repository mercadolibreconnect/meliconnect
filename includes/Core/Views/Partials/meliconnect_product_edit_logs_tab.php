<div id="meliconnect_logs_product_data" class="panel wc-metaboxes-wrapper hidden" style="display: block;">
    <p><?php _e('Last time error: ', 'meliconnect'); ?> <?php echo esc_html($export_error_time); ?></p>
    <div class="wc-metaboxes">

        <div class="melicon_log_container wc-metabox postbox   closed open">
            <h3 class="">
                <div class="handlediv" aria-expanded="true"></div>
                <div class="tips sort"></div>
                <strong class="log_name"><?php _e('Listing', 'meliconnect'); ?></strong>
            </h3>
            <div class="woocommerce_log_data wc-metabox-content hidden" style="display: block;">

                <pre><?php echo json_encode($item_export_error, JSON_PRETTY_PRINT); ?></pre>
            </div>
        </div>
        <div class="melicon_log_container wc-metabox postbox closed ">
            <h3 class="">
                <div class="handlediv" aria-expanded="false"></div>
                <div class="tips sort"></div>
                <strong class="log_name"><?php _e('Description', 'meliconnect'); ?></strong>
            </h3>
            <div class="woocommerce_log_data wc-metabox-content hidden" style="display: none;">
                <pre><?php echo json_encode($description_export_error, JSON_PRETTY_PRINT); ?></pre>
            </div>
        </div>

    </div>
    <div class="melicon_log_container wc-metabox postbox   closed open">
        <h3 class="">
            <div class="handlediv" aria-expanded="true"></div>
            <div class="tips sort"></div>
            <strong class="log_name"><?php _e('Json', 'meliconnect'); ?></strong>
        </h3>
        <div class="woocommerce_log_data wc-metabox-content hidden" style="display: block;">
            <pre><?php echo json_encode($last_json_sent, JSON_PRETTY_PRINT); ?></pre>
        </div>
    </div>
    <div class="toolbar toolbar-buttons">
        <button type="button" class="button button-primary"><?php _e('Clean Logs', 'meliconnect'); ?></button>
    </div>

</div>