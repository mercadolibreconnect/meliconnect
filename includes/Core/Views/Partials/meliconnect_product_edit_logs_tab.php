<div id="meliconnect_logs_product_data" class="panel wc-metaboxes-wrapper hidden" style="display: block;">
    <div class="melicon-container melicon-m-4">
        <?php if(empty($item_export_error) && empty($description_export_error)): ?>
            <p class="melicon-is-size-5 melicon-m-2 melicon-has-text-success"><?php esc_html_e('Last export was successful', 'meliconnect'); ?></p>
        <?php endif; ?>

        <?php if ($last_export_time != ''): ?>
        <p class="melicon-is-size-5 melicon-m-2"><?php esc_html_e('Last export time: ', 'meliconnect'); ?> <?php echo esc_html($last_export_time); ?></p>
        <?php endif; ?>

        <?php if ($export_error_time != ''): ?>
        <p class="melicon-is-size-5 melicon-m-2"><?php esc_html_e('Last time error: ', 'meliconnect'); ?> <?php echo esc_html($export_error_time); ?></p>
        <?php endif; ?>
        <div class="wc-metaboxes melicon-mt-2">

            <div class="melicon_log_container wc-metabox postbox   closed open">
                <h3 class="">
                    <div class="handlediv" aria-expanded="true"></div>
                    <div class="tips sort"></div>
                    <strong class="log_name melicon-is-size-6"><?php esc_html_e('Listing', 'meliconnect'); ?></strong>
                </h3>
                <div class="woocommerce_log_data wc-metabox-content hidden" style="display: block;">

                    <pre><?php echo wp_json_encode($item_export_error, JSON_PRETTY_PRINT); ?></pre>
                </div>
            </div>
            <div class="melicon_log_container wc-metabox postbox closed ">
                <h3 class="">
                    <div class="handlediv" aria-expanded="false"></div>
                    <div class="tips sort"></div>
                    <strong class="log_name melicon-is-size-6"><?php esc_html_e('Description', 'meliconnect'); ?></strong>
                </h3>
                <div class="woocommerce_log_data wc-metabox-content hidden" style="display: none;">
                    <pre><?php echo wp_json_encode($description_export_error, JSON_PRETTY_PRINT); ?></pre>
                </div>
            </div>

        </div>
        <div class="melicon_log_container wc-metabox postbox   closed open">
            <h3 class="">
                <div class="handlediv" aria-expanded="true"></div>
                <div class="tips sort"></div>
                <strong class="log_name melicon-is-size-6"><?php esc_html_e('Json', 'meliconnect'); ?></strong>
            </h3>
            <div class="woocommerce_log_data wc-metabox-content hidden" style="display: block;">
                <button type="button" style="position:absolute;right:20px;" class="melicon-button melicon-is-link melicon-m-2 melicon-is-small" id="copy-last-json-button">
                    <?php esc_html_e('Copy to clipboard', 'meliconnect'); ?>
                </button>
                <pre id="meliconnect-json-to-copy"><?php echo wp_json_encode($last_json_sent, JSON_PRETTY_PRINT); ?></pre>
            </div>
        </div>
        <div class="melicon-mt-4">
            <button type="button" class="melicon-button melicon-is-primary "><?php esc_html_e('Clean Logs', 'meliconnect'); ?></button>
        </div>
    </div>


</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const copyBtn = document.getElementById('copy-last-json-button');
    const jsonPre = document.getElementById('meliconnect-json-to-copy');

    if (copyBtn && jsonPre) {
        copyBtn.addEventListener('click', function () {
            const text = jsonPre.innerText;

            navigator.clipboard.writeText(text).then(function () {
                copyBtn.innerText = 'Copied!';
                setTimeout(() => {
                    copyBtn.innerText = 'Copy last JSON sent';
                }, 2000);
            }).catch(function (err) {
                alert('Error copying JSON: ' + err);
            });
        });
    }
});
</script>