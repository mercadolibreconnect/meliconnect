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

<script>
    jQuery(document).ready(function($) {

        $('#mely_sync_all').on('change', function() {
            if (this.checked) {
                $('.select-data-to-sync-field').hide();
                $('#meli_sync_options').val(null).trigger('change');
            } else {
                $('.select-data-to-sync-field').show();
            }
        });

        if ($('#mely_sync_all').prop('checked')) {
            $('.select-data-to-sync-field').hide();
        }

        setTimeout(function() {
            $('#meliconnect-loader').hide();
            $('#meliconnect-box').fadeIn();
        }, 2000);

        $('#meli_sync_options').select2({
            placeholder: "<?php esc_html_e('Select fields to sync', 'meliconnect'); ?>",
            allowClear: true,
            closeOnSelect: false,
            templateResult: function(option) {
                if (!option.id) {
                    return option.text;
                }

                // Crear un checkbox dentro del dropdown
                let $checkbox = $('<input type="checkbox" class="sync-checkbox" value="' + option.id + '"/>');

                // Verificar si el elemento está seleccionado y marcar el checkbox
                let selectedValues = $('#meli_sync_options').val() || [];
                if (selectedValues.includes(option.id)) {
                    $checkbox.prop('checked', true);
                }

                return $('<span>').append($checkbox).append(' ' + option.text);
            },
            templateSelection: function(option) {
                return option.text;
            }
        });

        // Evento para actualizar los checkboxes al seleccionar/deseleccionar
        $('#meli_sync_options').on('select2:select select2:unselect', function() {
            $('.sync-checkbox').each(function() {
                let checkbox = $(this);
                let value = checkbox.val();
                let selectedValues = $('#meli_sync_options').val() || [];
                checkbox.prop('checked', selectedValues.includes(value));
            });

            // Si hay al menos un elemento seleccionado, desmarcar "All Data"
            if ($(this).val().length > 0) {
                $('#meli_sync_all').prop('checked', false);
            }
        });

        $('#meli_sync_all').on('change', function() {
            if (this.checked) {
                $('#meli_sync_options').val(null).trigger('change');
            }
        });


    });
</script>