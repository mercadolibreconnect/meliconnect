<div id="mercadolibre_product_data" class="melicon-product-edit-meli-tab wc-metaboxes-wrapper panel woocommerce_options_panel hidden">
    <div class="options_group">
        <p class="form-field melicon_general_seller_meli_id_field">
            <?php

            /* echo PHP_EOL . '-------------------- $select_options --------------------' . PHP_EOL;
            echo '<pre>' . wp_json_encode($select_options) . '</pre>';
            echo PHP_EOL . '-------------------  FINISHED  ---------------------' . PHP_EOL;

            echo PHP_EOL . '-------------------- $form_values --------------------' . PHP_EOL;
            echo '<pre>' . wp_json_encode($form_values) . '</pre>';
            echo PHP_EOL . '-------------------  FINISHED  ---------------------' . PHP_EOL;

            wp_die();  */

            woocommerce_wp_select(array(
                'id' => 'template[seller_meli_id]',
                'label'   => esc_html__('Seller', 'meliconnect'),
                'options' => $sellers_ids_and_names,
                'value'   => $seller_meli_id,
                'custom_attributes' => $disabled_seller ? array('disabled' => 'disabled') : array(), // Deshabilitar si hay un solo seller
            ));

            ?>
        </p>
    </div>
    <div class="options_group">
        <p class="form-field melicon_general_category_id_field">
            <label for="melicon_general_category_id">
                <?php esc_html_e('Category', 'meliconnect'); ?>
            </label>
            <select id="melicon_general_category_id" class="wc-enhanced-select select2-category" style="width: 50%;">
                <option value=""><?php esc_html_e('Select a category', 'meliconnect'); ?></option>
            </select>
        </p>

        <input type="text" id="melicon_general_category_id_input" name="template[category_id]" value="<?php echo esc_attr($form_values['category_id']); ?>" style="display: none;">

        <p>
        <div id="subcategory-tree-container"></div>
        </p>

        <input type="text" id="subcategory-tree-input" name="template[subcategory_tree]" style="display:none;" value="">

        <input type="text" id="melicon-category-name-input" name="template[category_name]" style="display:none;" value="">


    </div>
    <div id="melicon-meessage-category-container" class="options_group melicon_show_if_change_category melicon-m-1">
        <?php echo '<div class="notice notice-warning"><p>'.esc_html__('You must select a category and save product template to see category options', 'meliconnect') .'</p></div>'; ?>
        
    </div>
    <div class="options_group melicon_hide_if_change_category ">
        <p class="form-field melicon_general_buying_mode_field">
            <?php
            woocommerce_wp_select(array(
                'id' => 'template[meta][buying_mode]',
                'label' => esc_html__('Buying Mode', 'meliconnect'),
                'options' => $select_options['buying_modes'],
                'value' => $form_values['buying_mode'],
            ));
            ?>
        </p>

        <p class="form-field melicon_general_listing_type_id_field">
            <?php
            woocommerce_wp_select(array(
                'id' => 'template[meta][listing_type_id]',
                'label' => esc_html__('Listing Type', 'meliconnect'),
                'options' => $select_options['listing_types'],
                'value' => $form_values['listing_type'],
            ));
            ?>
        </p>




        <p class="form-field melicon_general_condition_field">
            <?php
            woocommerce_wp_select(array(
                'id' => 'template[meta][condition]',
                'label' => esc_html__('Condition', 'meliconnect'),
                'options' => $select_options['conditions'],
                'value' => $form_values['condition'],
            ));
            ?>
        </p>



        <p class="form-field melicon_general_warranty_type_field">
            <label for="template[meta][warranty_type]"><?php esc_html_e('Warranty', 'meliconnect'); ?> </label>
            <span class="wrap">
                <select id="template[warranty_type]" name="template[meta][warranty_type]">
                    <?php foreach ($select_options['warranty_types'] as $key => $value) { ?>
                        <option value="<?php echo esc_attr($key); ?>" <?php selected($key, $form_values['warranty_type'], true); ?>><?php echo esc_html($value); ?></option>
                    <?php } ?>
                </select>
                <input
                    id="melicon_general_warranty_time"
                    style="max-width:60px"
                    class="input-text wc_input_decimal"
                    type="number"
                    min="0"
                    name="template[meta][warranty_time]"
                    value="<?php echo esc_attr($form_values['warranty_time']); ?>" />
                <select id="template[warranty_time_unit]" name="template[meta][warranty_time_unit]">
                    <?php foreach ($select_options['warranty_time_units'] as $key => $value) { ?>
                        <option value="<?php echo esc_attr($key); ?>" <?php selected($key, $form_values['warranty_time_unit'], true); ?>><?php echo esc_html($value); ?></option>
                    <?php } ?>
                </select>
            </span>
        </p>


        <p class="form-field melicon_general_currency_field">
            <?php
            woocommerce_wp_select(array(
                'id' => 'template[meta][currency_id]',
                'label' => esc_html__('Currency', 'meliconnect'),
                'options' => $select_options['currencies'],
                'value' => $form_values['currency'],
            ));
            ?>
        </p>

        <p class="form-field melicon_general_channels_field">
            <?php
            woocommerce_wp_select(array(
                'id' => 'template[channels]',
                'label' => esc_html__('Channels', 'meliconnect'),
                'options' => $select_options['channels'],
                'value' => $form_values['channel'],
            ));
            ?>
        </p>

        <p class="form-field melicon_general_shipping_method_field">
            <?php
            woocommerce_wp_select(array(
                'id' => 'template[meta][shipping_method]',
                'label' => esc_html__('Shipping Method', 'meliconnect'),
                'options' => $select_options['shipping_methods'],
                'value' => $form_values['shipping_method'],
            ));
            ?>
        </p>


        <p class="form-field melicon_general_local_pickup_field">
        <p class="form-field template[meta][local_pick_up]_field ">
            <label for="template[meta][local_pick_up]"><?php esc_html_e('Local Pick Up', 'meliconnect'); ?></label>
            <input type="checkbox" name="template[meta][local_pick_up]" id="template[meta][local_pick_up]" value="1" class="melicon-checkbox"
                <?php echo (isset($form_values['local_pick_up']) && $form_values['local_pick_up'] == '1') ? 'checked' : ''; ?>>
            <span class="description"><?php esc_html_e('Enable Local Pick Up', 'meliconnect'); ?></span>
        </p>
        <p class="form-field template[meta][free_shipping]_field ">
            <label for="template[meta][free_shipping]"></label>
            <input type="checkbox" name="template[meta][free_shipping]" id="template[meta][free_shipping]" value="1" class="melicon-checkbox"
                <?php echo (isset($form_values['free_shipping']) && $form_values['free_shipping'] == '1') ? 'checked' : ''; ?>>
            <span class="description"><?php esc_html_e('Enable Free Shipping', 'meliconnect'); ?></span>
        </p>


        </p>


        <p class="form-field melicon_general_manufacturing_time_field">
            <label for="template[manufacturing_time]"><?php esc_html_e('Manufacturing Time', 'meliconnect'); ?> </label>
            <span class="wrap">
                <input
                    id="melicon_general_manufacturing_time"
                    style="max-width:60px"
                    class="input-text wc_input_decimal"
                    type="number"
                    min="0"
                    max="45"
                    name="template[meta][manufacturing_time]"
                    value="<?php echo esc_attr($form_values['manufacturing_time']); ?>" />
                <select id="template[manufacturing_time_unit]" name="template[meta][manufacturing_time_unit]">
                    <?php foreach ($select_options['manufacturing_time_units'] as $key => $value) { ?>
                        <option value="<?php echo esc_attr($key); ?>" <?php selected($key, $form_values['manufacturing_time_unit'], true); ?>><?php echo esc_html($value); ?></option>
                    <?php } ?>
                </select>
            </span>
        </p>



        <?php if (!empty($select_options['official_stores'])) { ?>
            <p class="form-field melicon_general_official_store_field">
                <?php
                woocommerce_wp_select(array(
                    'id' => 'template[meta][official_store_id]',
                    'label' => esc_html__('Official Store', 'meliconnect'),
                    'options' => $select_options['official_stores'],
                    'value' => $form_values['official_store'],
                ));
                ?>
            </p>
        <?php } ?>


        <p class="form-field template[meta][catalog_listing]_field">
            <label for="template[meta][catalog_listing]"><?php esc_html_e('Catalog Listing', 'meliconnect'); ?></label>
            <input type="checkbox" name="template[meta][catalog_listing]" id="template[meta][catalog_listing]" value="1" class="melicon-checkbox"
                <?php echo (isset($form_values['catalog_listing']) && $form_values['catalog_listing'] == '1') ? 'checked' : ''; ?>>
            <span class="description"><?php esc_html_e('Enable Catalog Listing', 'meliconnect') ?></span>
        </p>

        <p class="form-field melicon_general_status_field">
            <?php
            woocommerce_wp_select(array(
                'id' => 'template[meta][status]',
                'label' => esc_html__('Listing Status', 'meliconnect'),
                'options' => $select_options['status'],
                'value' => $form_values['status'],
            ));
            ?>
        </p>


    </div>

    <div class="options_group ">
        <p class="form-field melicon_general_title_structure_field">
            <label for="melicon_general_title_structure">
                <?php esc_html_e('Title Structure', 'meliconnect'); ?>
            </label>
            <input id="melicon_general_title_structure" class="input-text" type="text" name="template[meta][title_structure]" value="<?php echo esc_attr($form_values['title_structure']); ?>" />
        </p>
        <p class="form-field melicon_general_description_structure_field">
            <label for="melicon_general_description_structure">
                <?php esc_html_e('Description Structure', 'meliconnect'); ?>
            </label>
            <input id="melicon_general_description_structure" class="input-text" type="text" name="template[meta][description_structure]" value="<?php echo esc_attr($form_values['description_structure']); ?>" />
        </p>
        <p class="form-field melicon_general_how_price_formed">
            <?php
            woocommerce_wp_select(array(
                'id' => 'template[meta][price_create_method]',
                'label' => esc_html__('How is the price formed? (*)', 'meliconnect'),
                'options' => array(
                    'sale_price' => esc_html__('Sale price', 'meliconnect'),
                    'regular_price' => esc_html__('Only regular price', 'meliconnect'),
                ),
                'value' => $form_values['price_create_method'],
            ));
            ?>
        </p>
        <p class="form-field melicon_general_has_sync_field">
            <?php
            woocommerce_wp_select(array(
                'id' => 'template[meta][has_sync]',
                'label' => esc_html__('Has Sync', 'meliconnect'),
                'options' => array(
                    '1' => esc_html__('Yes', 'meliconnect'),
                    '0' => esc_html__('No', 'meliconnect'),
                ),
                'value' => $form_values['has_sync'],
            ));
            ?>
        </p>
    </div>



    <div class="options_group melicon_product_edit_mercadolibre_tab_save">
        <div class="toolbar toolbar-buttons">
            <button id="melicon_save_template_button"
                type="button"
                class="melicon-button melicon-is-primary">
                <?php esc_html_e('Save Template', 'meliconnect'); ?>
            </button>
        </div>
    </div>
</div>

<input type="hidden" id="melicon_current_category_id" value="<?php echo esc_attr($form_values['category_id']); ?>">
<input type="hidden" id="melicon_woo_product_id" value="<?php echo esc_attr($woo_product_id); ?>">



