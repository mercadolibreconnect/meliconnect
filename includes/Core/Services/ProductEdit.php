<?php

namespace StoreSync\Meliconnect\Core\Services;

use Error;
use StoreSync\Meliconnect\Core\Helpers\Helper;
use StoreSync\Meliconnect\Core\Helpers\MeliconMeli;
use StoreSync\Meliconnect\Core\Models\Template;
use StoreSync\Meliconnect\Core\Models\UserConnection;
use StoreSync\Meliconnect\Modules\Exporter\Models\ProductToExport;
use StoreSync\Meliconnect\Modules\Exporter\Services\ListingDataFacade;
use StoreSync\Meliconnect\Modules\Exporter\Services\MercadoLibreListingAdapter;
use StoreSync\Meliconnect\Modules\Importer\Services\ProductDataFacade;
use StoreSync\Meliconnect\Modules\Importer\Services\WooCommerceProductAdapter;
use StoreSync\Meliconnect\Modules\Importer\Services\WooCommerceProductCreationService;

class ProductEdit
{
    public $woo_product_id = false;
    public $template_data = [];
    public $template_attibutes = [];
    public $meli_category_data = [];
    public $meli_category_attrs = [];
    public $meli_category_variable_attrs = [];
    public $meli_category_sale_terms = []; //warranty, manufacturing time ,etx
    public $matched_attrs = [];

    public $select_options = [];
    public $form_values = [];

    public $listing_types_by_category_and_seller = [];
    public $official_stores_by_seller = [];
    public $category_shipping_preferences = [];

    public static $meliConnection = null;
    public static $seller_data = null;

    public $shipping_modes_names = [];


    public function __construct()
    {
        add_action('add_meta_boxes', [$this, 'add_meliconnect_meta_box_data']);
        add_action('add_meta_boxes', [$this, 'add_meliconnect_meta_box_actions']);
        add_action('woocommerce_product_options_pricing', [$this, 'meliconnect_add_general_tab_html']);
        add_action('woocommerce_product_options_inventory_product_data', [$this, 'meliconnect_add_stock_tab_html']);
        add_filter('woocommerce_product_data_tabs', [$this, 'add_mercadolibre_product_tab']);
        add_action('woocommerce_product_data_panels', [$this, 'add_mercadolibre_product_tab_content']);
        add_action('woocommerce_product_data_panels', [$this, 'add_meliconnect_logs_product_tab_content']);

        add_action('woocommerce_product_options_attributes', [$this, 'add_mercadolibre_attrs_content']);

        add_action('woocommerce_after_product_attribute_settings', [$this, 'match_mercadolibre_atts'], 10, 2);

        add_action('woocommerce_process_product_meta', [$this, 'melicon_save_custom_product_data'], 10, 1);

        add_action('woocommerce_admin_process_variation_object', [$this, 'melicon_woocommerce_save_product_variation'], 10, 2);

        add_action('woocommerce_variation_options',  [$this, 'melicon_custom_variation_fields'], 10, 3);






        $this->loadAssets();

        $this->shipping_modes_names = [
            'custom' => esc_html__('Custom', 'meliconnect'),
            'not_specified' => esc_html__('No Shipping Configuration', 'meliconnect'),
            'me2' => esc_html__('Mercado Envíos - Standard ', 'meliconnect') . '(' . esc_html__('ME2', 'meliconnect') . ')',
            'me1' => esc_html__('Mercado Envíos - Express ', 'meliconnect') . '(' . esc_html__('ME1', 'meliconnect') . ')',
        ];

        $this->select_options = [
            'buying_modes' => ['buy_it_now' => esc_html__('Buy It Now', 'meliconnect')],
            'listing_types' => [
                'gold_premium' => esc_html__('Gold Premium', 'meliconnect'),
                'gold' => esc_html__('Gold', 'meliconnect'),
                'silver' => esc_html__('Silver', 'meliconnect'),
            ],
            'conditions' => [
                'new' => esc_html__('New', 'meliconnect'),
                'used' => esc_html__('Used', 'meliconnect'),
            ],
            'warranty_types' => [
                '2230280' => esc_html__('Seller Warranty', 'meliconnect'),
                '2230279' => esc_html__('Manufacturer Warranty', 'meliconnect'),
                '6150835' => esc_html__('No Warranty', 'meliconnect'),
            ],
            'warranty_time_units' => [
                'días' => esc_html__('días', 'meliconnect'),
                'meses' => esc_html__('meses', 'meliconnect'),
                'años' => esc_html__('años', 'meliconnect'),
            ],
            'currencies' => [
                'USD' => esc_html__('USD', 'meliconnect'),
            ],
            'channels' => ['mercadolibre' => esc_html__('Mercadolibre', 'meliconnect')],
            'shipping_methods' => [
                'me2' => esc_html__('Mercado Envíos - Standard ', 'meliconnect') . '(' . esc_html__('ME2', 'meliconnect') . ')',
                'me1' => esc_html__('Mercado Envíos - Express ', 'meliconnect') . '(' . esc_html__('ME1', 'meliconnect') . ')',
                'not_specified' => esc_html__('No Shipping Configuration', 'meliconnect'),
            ],
            'manufacturing_time_units' => [
                'días' => esc_html__('días', 'meliconnect'),
            ],
            'status' => [
                'active' => esc_html__('Active', 'meliconnect'),
                'paused' => esc_html__('Paused', 'meliconnect'),
                'closed' => esc_html__('Closed', 'meliconnect'),
            ],
            'official_stores' => []
        ];

        //iniiatize with default values
        $this->form_values = [
            'title_structure' => '{title}',
            'description_structure' => '{description}',
            'price_create_method' => 'regular_price',
            'category_id' => '',
            'has_sync' => 'true',
            'warranty_time' => 0,
            'manufacturing_time' => 0,
            'local_pick_up' => false,
            'free_shipping' => false,
            'catalog_listing' => false,
            'buying_mode' => 'buy_it_now',
            'listing_type' => 'gold_premium',
            'condition' => 'new',
            'warranty_type' => '6150835', //Value for: No warranty
            'warranty_time_unit' => 'días',
            'currency' => 'USD',
            'channel' => 'mercadolibre',
            'shipping_method' => 'me2',
            'manufacturing_time_unit' => 'días',
            'official_store_id' => '',
            'status' => 'active',
        ];
    }

    public function melicon_woocommerce_save_product_variation($variation, $i)
    {
        if (! isset($_POST['melicon_variation_nonce']) || ! check_admin_referer('melicon_save_product_variation_nonce', 'melicon_variation_nonce')) {
            // Si el nonce no es válido, aborta la ejecución
            wp_die('Invalid nonce. Please reload the page and try again.');
        }

        if (isset($_POST['product_id'])) {
            $parent_id = sanitize_text_field(wp_unslash($_POST['product_id']));
        } else {
            wp_die('Product ID not found.');
        }

        if (isset($_POST['template']['variations'])) {
            $template_variations = sanitize_text_field(wp_unslash($_POST['template']['variations']));
        } else {
            wp_die('Product ID not found.');
        }

        $variation_id = $variation->get_id();
        $template_id = get_post_meta($parent_id, 'melicon_asoc_template_id', true);


        foreach ($template_variations as $variation_key => $variation_values) {
            $current_meli_variation_id = $variation_values['variation_data']['meli_variation_id'] ?? '';

            if (isset($variation_values['variation_data']['disable_sync']) && $variation_values['variation_data']['disable_sync'] == 1) {
                update_post_meta($variation_id, 'melicon_meli_asoc_variation_sync_disabled', 1);
            } else {
                update_post_meta($variation_id, 'melicon_meli_asoc_variation_sync_disabled', 0);
            }

            foreach ($variation_values['attrs'] as $meli_attribute_name => $meli_value_data) {

                $meliDataUnescaped = stripslashes($meli_value_data['meli_data']);
                $meliDataArray = json_decode($meliDataUnescaped, true);

                $variation_template_data = [
                    'template_id' =>  $template_id,
                    'used_by' => 'variation',
                    'used_asoc_id' => $variation_id,
                    'product_parent_id' => $parent_id,
                    'woo_attribute_id' => '',
                    'meli_variation_id' =>  $current_meli_variation_id, // Ej: 181734597045
                    'meli_attribute_id' => $meliDataArray['id'], //Ej: COLOR
                    'meli_attribute_name' => $meliDataArray['name'], //Ej: Color
                    'meli_value_id' => $meli_value_data['value'], // Ej: 52049
                    'meli_value_name' => $meli_attribute_name, //Ej: Negro
                    'meli_value_type' => $meliDataArray['value_type'], //Ej: string 
                    'allow_variations_tag' => (isset($meliDataArray['tags']['allow_variations']) && $meliDataArray['tags']['allow_variations']) ? 1 : 0, // Ej:1
                    'variation_attribute_tag' => (isset($meliDataArray['tags']['variation_attribute_tag']) && $meliDataArray['tags']['variation_attribute']) ? 1 : 0, // Ej:0
                    'required_tag' => (isset($meliDataArray['tags']['required_tag']) && $meliDataArray['tags']['required']) ? 1 : 0, // Ej:0
                    'not_apply' => 0,
                ];
                /* echo PHP_EOL . '-------------------- variation_template_data --------------------' . PHP_EOL;
                echo '<pre>' . wp_json_encode($meliDataArray) . '</pre>';
                echo '<pre>' . wp_json_encode( $variation_template_data) . '</pre>';
                echo PHP_EOL . '-------------------  FINISHED  ---------------------' . PHP_EOL;
                wp_die(); */
                Template::createUpdateTemplateAttributes('variation', $variation_id, $meliDataArray['id'], $variation_template_data);
            }
        }
    }

    public function melicon_custom_variation_fields($loop, $variation_data, $variation)
    {
        $this->set_product_vars();

        $current_variation_attrs = $this->filterVariationAttributes($variation_data);

        echo '<div class="melicon_meli_attribute_info_container">';
        echo '<h4><strong>' . esc_html__('Select a value per attribute to use in meli variations', 'meliconnect') . '</strong></h4>';
        echo '<p>';

        $meli_variation_id = $this->mapVariationWithMeliVariations($variation, $current_variation_attrs);

        wp_nonce_field('melicon_save_product_variation_nonce', 'melicon_variation_nonce');

        // Prints selects with values as selected
        foreach ($current_variation_attrs as $name => $attr_value) {
            $normalized_name = Helper::normalizeString($name);
            $meli_attributes_normalized = array_map(function ($key) {
                return Helper::normalizeString($key);
            }, array_keys($this->meli_category_variable_attrs));

            $meli_attr_index = array_search($normalized_name, $meli_attributes_normalized);

            if ($meli_attr_index !== false) {
                $meli_attr_name = array_keys($this->meli_category_variable_attrs)[$meli_attr_index];

                echo "<div class='melicon_variation_row'>";
                echo '<label for="attribute_' . esc_attr($meli_attr_name) . '">' . esc_html($name) . ':</label><br>';

                $meli_attr_data = $this->getCurrentAttrMeliData($name);
                $meli_attr_data_json = wp_json_encode($meli_attr_data); // Usar WordPress JSON escape
                echo '<input type="text" style="display:none" name="template[variations][' . esc_attr($loop) . '][attrs][' . esc_attr($meli_attr_name) . '][meli_data]" value="' . esc_attr($meli_attr_data_json) . '">';

                // Select dropdown con los valores
                echo '<select name="template[variations][' . esc_attr($loop) . '][attrs][' . esc_attr($meli_attr_name) . '][value]" id="attribute_' . esc_attr($meli_attr_name) . '">';

                // Opción predeterminada
                echo '<option value="">' . esc_html__('Select a value', 'meliconnect') . '</option>';

                foreach ($this->meli_category_variable_attrs[$meli_attr_name] as $meli_value_id => $meli_value_name) {
                    $normalized_value = Helper::normalizeString($meli_value_name);
                    $normalized_current_value = Helper::normalizeString($attr_value);
                    $selected = ($normalized_current_value === $normalized_value) ? 'selected' : '';

                    echo '<option value="' . esc_attr($meli_value_id) . '" ' . esc_attr($selected) . '>' . esc_html($meli_value_name) . '</option>';
                }

                echo '</select>';
                echo '</div><br>';
            } else {
                echo '<p>' . sprintf(
                    /* translators: %s: is the attribute name that is not mapped */
                    esc_html__(
                        "Attribute '%s' is not mapped.",
                        "meliconnect"
                    ),
                    esc_html($name)
                ) . '</p>';
            }
        }

        echo '</p>';

        // Agregar el checkbox para deshabilitar la sincronización
        $variation_sync_is_disabled = get_post_meta($variation->ID, 'melicon_meli_asoc_variation_sync_disabled', true);
        $checked_disabled = $variation_sync_is_disabled ? 'checked' : '';

        echo '<input type="text" style="display:none" name="template[variations][' . esc_attr($loop) . '][variation_data][meli_variation_id]" value="' . esc_attr($meli_variation_id) . '">';
        echo '<input type="checkbox" name="template[variations][' . esc_attr($loop) . '][variation_data][disable_sync]" class="melicon_variation_disable_sync" value="1" ' . esc_attr($checked_disabled) . '>';
        echo '<label for="disable_sync_' . esc_attr($meli_attr_name) . '">' . esc_html__('Disable sync for this variation', 'meliconnect') . '</label>';

        echo '</div>';
    }


    public function mapVariationWithMeliVariations($variation, $current_variation_attrs)
    {
        //Find saved variation postmeta
        $meli_variation_id = get_post_meta($variation->ID, 'melicon_meli_asoc_variation_id', true);


        //TODO if variation meta not exists. Check if product has a vinculated product and map values

        return $meli_variation_id;
    }

    public function getCurrentAttrMeliData($name)
    {
        $meli_attributes = $this->meli_category_attrs;


        foreach ($meli_attributes as $attr) {

            if (Helper::normalizeString($attr['name']) === $name) {
                return $attr;
            }
        }

        return null;
    }


    /* public function findInTemplateAttrs($variation, $current_variation_attrs)
    {
        
        $template_attrs = $this->template_attibutes;

        foreach ($template_attrs as $template_attr) {
            if (isset($template_attr['allow_variations_tag']) && $template_attr['allow_variations_tag'] == 1) {
                echo PHP_EOL . '-------------------- template_attr --------------------' . PHP_EOL;
                echo '<pre>' . wp_json_encode($template_attr) . '</pre>';
                echo PHP_EOL . '-------------------  FINISHED  ---------------------' . PHP_EOL;
            }
        }


        echo PHP_EOL . '-------------------- VARIATION --------------------' . PHP_EOL;
        echo '<pre>' . wp_json_encode($variation) . '</pre>';
        echo PHP_EOL . '-------------------  FINISHED  ---------------------' . PHP_EOL;

        echo PHP_EOL . '-------------------- ATTRIBUTES --------------------' . PHP_EOL;
        echo '<pre>' . wp_json_encode($current_variation_attrs) . '</pre>';
        echo PHP_EOL . '-------------------  FINISHED  ---------------------' . PHP_EOL;

        //find in current meli variations

        //find in template attrs table if exists 

        //return variation id if exists a variation with same attributes, else return 0
    } */

    public function filterVariationAttributes($variation_data)
    {
        $attrs = [];

        if (empty($variation_data)) {
            return $attrs;
        }

        foreach ($variation_data as $key => $value) {

            if (strpos($key, 'attribute_pa_') !== false) {
                $formated_key = str_replace('attribute_pa_', '', $key);
                $attrs[$formated_key] = $value;
                continue;
            }

            if (strpos($key, 'attribute_') !== false) {
                $formated_key = str_replace('attribute_', '', $key);
                $attrs[$formated_key] = $value;
                continue;
            }
        }

        return $attrs;
    }




    public function melicon_save_attributes($post_id)
    {
        echo 'Función ejecutada correctamente 2';
    }


    public function match_mercadolibre_atts($attribute, $i)
    {
        $data = $this->get_match_data($attribute, $i);
        $data['instance'] = $this;

        Helper::load_partial('includes/Core/Views/Partials/meliconnect_product_edit_match_atts.php', $data, false);
    }

    public function get_match_data($attribute, $i)
    {
        $taxonomy_object = $attribute->get_taxonomy_object();

        // Determinar el nombre del atributo
        if (!empty($taxonomy_object) && isset($taxonomy_object->attribute_label) && !empty($taxonomy_object->attribute_label)) {
            $current_attr_name = $taxonomy_object->attribute_label;
        } else {
            $current_attr_name = $attribute->get_name();
        }

        // Obtener opciones del atributo
        $current_attr_options = $attribute->get_options();

        // Si es una taxonomía, obtener los nombres de los términos
        if ($attribute->is_taxonomy()) {
            $taxonomy_name = $attribute->get_name();
            $term_names = [];
            foreach ($current_attr_options as $term_id) {
                $term = get_term_by('id', $term_id, $taxonomy_name);
                if ($term && !is_wp_error($term)) {
                    $term_names[] = $term->name;
                }
            }
            $current_attr_options = $term_names;
        }

        // Buscar coincidencias en MercadoLibre
        $find_in_meli_attr = $this->findTaxonomyInMeliAttrs($current_attr_name);
        $template_attr_value = null;
        $escaped_attr_value = '';

        if ($find_in_meli_attr) {
            $this->matched_attrs[] = $find_in_meli_attr;
            $template_attr_value = [
                'meli' => $find_in_meli_attr,
                'woo'  => [
                    'attribute_id' => $attribute->get_id(),
                    'name' => $current_attr_name,
                    'values' => $current_attr_options
                ]
            ];
            $escaped_attr_value = htmlspecialchars(wp_json_encode($template_attr_value), ENT_QUOTES, 'UTF-8');
        }



        return [
            'i' => $i,
            'current_attr_name' => $current_attr_name,
            'current_attr_options' => $current_attr_options,
            'find_in_meli_attr' => $find_in_meli_attr,
            'escaped_attr_value' => $escaped_attr_value, 
            'attribute_tags_info' => $this->getAttributeTagsInfo($find_in_meli_attr),
        ];
    }

    public function getAttributeTagsInfo($attribute)
    {
        $messages = [];

        if (isset($attribute['tags']) && is_array($attribute['tags'])) {
            $tags = $attribute['tags'];

            if (!empty($tags['allow_variations'])) {
                $messages[] = __('Variations are allowed for this attribute.', 'meliconnect');
            }
            if (!empty($tags['catalog_required'])) {
                $messages[] = __('It\'s required to identify the product the item represents.', 'meliconnect');
            }
            if (!empty($tags['conditional_required'])) {
                $messages[] = __('This attribute is required when certain conditions are met.', 'meliconnect');
            }
            if (!empty($tags['defines_picture'])) {
                $messages[] = __('This attribute defines the variation picture.', 'meliconnect');
            }
            if (!empty($tags['fixed'])) {
                $messages[] = __('The value is fixed for this category. This value can\'t be changed.', 'meliconnect');
            }
            if (!empty($tags['grid_filter'])) {
                $messages[] = __('This attribute can be used as a filter when searching on category existing grids.', 'meliconnect');
            }
            if (!empty($tags['grid_template_required'])) {
                $messages[] = __('This attribute is required for technical specifications in the grid defined for the category.', 'meliconnect');
            }
            if (!empty($tags['hidden'])) {
                $messages[] = __('This attribute is hidden for this category.', 'meliconnect');
            }
            if (!empty($tags['inferred'])) {
                $messages[] = __('The value is inferred for this category.', 'meliconnect');
            }
            if (!empty($tags['multivalued'])) {
                $messages[] = __('More than one value may be assigned to this attribute.', 'meliconnect');
            }
            if (!empty($tags['new_required'])) {
                $messages[] = __('This attribute is required when the item\'s condition is new.', 'meliconnect');
            }
            if (!empty($tags['others'])) {
                $messages[] = __('This attribute is fixed or restricted but not inferred for all the category\'s sisters.', 'meliconnect');
            }
            if (!empty($tags['product_pk'])) {
                $messages[] = __('This attribute is part of the primary key of the product and can identify a unique product in the catalog.', 'meliconnect');
            }
            if (!empty($tags['read_only'])) {
                $messages[] = __('This attribute is read-only and is used for internal purposes only.', 'meliconnect');
            }
            if (!empty($tags['required'])) {
                $messages[] = __('is a REQUIRED attribute for this category.', 'meliconnect');
            }
            if (!empty($tags['restricted_values'])) {
                $messages[] = __('The values are restricted for this category.', 'meliconnect');
            }
            if (!empty($tags['variation_attribute'])) {
                $messages[] = __('This attribute is an attribute of a variation.', 'meliconnect');
            }
        }

        return $messages;
    }



    public static function getMeliConnection()
    {
        if (self::$meliConnection === null) {
            $seller_data = self::getSellerData(); // Método que puedes definir para obtener seller_data
            self::$seller_data = $seller_data;

            if (!empty($seller_data) && !empty($seller_data->access_token)) {
                self::$meliConnection = new MeliconMeli($seller_data->app_id, $seller_data->secret_key, $seller_data->access_token);
            }
        }

        return self::$meliConnection;
    }

    private static function getSellerData()
    {
        global $post;
        if ($post && $post->post_type === 'product') {
            $woo_product_id = $post->ID;
            $seller_id = get_post_meta($woo_product_id, 'melicon_meli_seller_id', true);
            if (!empty($seller_id)) {
                return UserConnection::getUser($seller_id);
            }
        }
        return null;
    }



    public function loadAssets()
    {

        // Verifica que el parámetro 'post' esté presente en $_GET y que sea un número válido
        if (isset($_GET['post']) && absint($_GET['post']) > 0) {
            $post_id = absint($_GET['post']); // Sanitizar el ID del post

            // Verifica que el tipo de post sea 'product'
            if (get_post_type($post_id) === 'product') {
                wp_enqueue_script('melicon-product-edit-js', MC_PLUGIN_URL . 'includes/Core/Assets/Js/melicon-product-edit.js', ['jquery'], '1.0.0', true);
            }
        }
    }


    public function set_product_vars()
    {

        global $post;

        if (isset($this->woo_product_id) && !empty($this->woo_product_id)) {
            //prevent reload product data in each variation
            return true;
        }

        if ($post && $post->post_type === 'product') {
            $this->woo_product_id = $post->ID;
        }

        $template_id = get_post_meta($this->woo_product_id, 'melicon_asoc_template_id', true);

        if (!empty($template_id)) {
            $this->template_data = Template::getTemplateData($template_id);

            if (!empty($this->template_data)) {
                $this->template_attibutes = Template::getTemplateAttributes($template_id);
            }
        }

        //updates selected default values with template saved values
        $this->updateFormValuesWithTemplateValues();

        $meli_category_id = get_post_meta($this->woo_product_id, 'melicon_meli_category_id', true);

        if (!empty($meli_category_id)) {
            $meli_category_data = MeliconMeli::simpleGet('https://api.mercadolibre.com/categories/' . $meli_category_id);

            if (!empty($meli_category_data)) {
                $this->meli_category_data = $meli_category_data;
                $this->formatCategoryData($meli_category_data); // Condition, Currencies, Buying_modes
            }

            $meli_category_attrs = MeliconMeli::simpleGet('https://api.mercadolibre.com/categories/' . $meli_category_id . '/attributes');



            if (!empty($meli_category_attrs)) {
                $this->meli_category_attrs = $meli_category_attrs;

                $this->meli_category_variable_attrs = $this->getAttributesWithTag($meli_category_attrs, ['allow_variations']);
            }

            $meli_category_sale_terms = MeliconMeli::simpleGet('https://api.mercadolibre.com/categories/' . $meli_category_id . '/sale_terms');

            if (!empty($meli_category_sale_terms)) {
                $this->meli_category_sale_terms = $meli_category_sale_terms;
                //Adds sale terms to view selects array
                $this->formatSaleTerms($meli_category_sale_terms);
            }
        }

        $meli = self::getMeliConnection();
        $seller_data = self::$seller_data;


        if (!empty($meli) && !empty($seller_data)) {

            $this->formatChannels($seller_data);

            //Get available listing types by category and seller                
            $listing_types_by_category_and_seller_response = $meli->getWithHeader('users/' . $seller_data->user_id . '/available_listing_types?category_id=' . $meli_category_id, $seller_data->access_token);

            if (isset($listing_types_by_category_and_seller_response['body']->available)) {
                $this->listing_types_by_category_and_seller = $listing_types_by_category_and_seller_response['body']->available;

                $this->formatListingTypesResponse($listing_types_by_category_and_seller_response['body']->available);
            }

            //Get official stores by category and seller

            //$official_stores_by_seller_response = MeliconMeli::simpleGet('https://api.mercadolibre.com/users/' . $seller_data->user_id . '/brands');

            //$official_stores_by_seller_response = $meli->getWithHeader('users/' . $seller_data->user_id . '/brands', $seller_data->access_token);
            //$official_stores_by_seller_response = $meli->getWithHeader('/users/1477536226/brands', $seller_data->access_token);

            if (isset($official_stores_by_seller_response['body']->brands)) {
                $this->official_stores_by_seller = $this->formatOfficialStroresResponse($official_stores_by_seller_response['body']->brands);
            }

            if (!empty($meli_category_id)) {
                $category_shipping_preferences_response = $meli->getWithHeader('categories/' . $meli_category_id . '/shipping_preferences', $seller_data->access_token);

                if (isset($category_shipping_preferences_response['body']->logistics)) {
                    $this->category_shipping_preferences = $category_shipping_preferences_response['body'];

                    $this->select_options['shipping_methods'] = $this->formatCategoryShippingPreferencesResponse($category_shipping_preferences_response['body']->logistics);
                }
            }
        }

        /* echo PHP_EOL . '-------------------- meli_category_sale_terms --------------------' . PHP_EOL;
        echo '<pre>' . wp_json_encode( $this->meli_category_sale_terms ) . '</pre>';
        echo PHP_EOL . '-------------------  FINISHED  ---------------------' . PHP_EOL;
        wp_die(); */
    }

    public function getAttributesWithTag($meli_category_attrs, $tags_filter = ['allow_variations'])
    {
        $meli_category_variable_attrs = [];

        /* echo PHP_EOL . '-------------------- meli_category_attrs --------------------' . PHP_EOL;
        echo '<pre>' . wp_json_encode($meli_category_attrs) . '</pre>';
        echo PHP_EOL . '-------------------  FINISHED  ---------------------' . PHP_EOL; */

        foreach ($meli_category_attrs as $value) {
            // Verifica si el atributo tiene 'tags'
            if (isset($value['tags']) && is_array($value['tags'])) {
                $current_tags = array_keys($value['tags']); // Usamos las keys de 'tags'

                // Verificamos si TODOS los tags en $tags_filter están presentes en $current_tags
                $has_all_tags = !array_diff($tags_filter, $current_tags);

                if ($has_all_tags) {
                    $current_values = [];

                    foreach ($value['values'] as $possible_value) {

                        $current_values[$possible_value['id']] = $possible_value['name'];
                    }

                    $current_attr_name = strtolower($value['name']);

                    $meli_category_variable_attrs[$current_attr_name] = $current_values;
                }
            }
        }


        return $meli_category_variable_attrs;
    }

    public function updateFormValuesWithTemplateValues()
    {

        if (!empty($this->template_data['title_structure'])) {
            $this->form_values['title_structure'] = $this->template_data['title_structure'];
        }

        if (!empty($this->template_data['description_structure'])) {
            $this->form_values['description_structure'] = $this->template_data['description_structure'];
        }

        if (!empty($this->template_data['price_create_method'])) {
            $this->form_values['price_create_method'] = $this->template_data['price_create_method'];
        }

        if (!empty($this->template_data['has_sync'])) {
            $this->form_values['has_sync'] = $this->template_data['has_sync'];
        }

        if (!empty($this->template_data['category_id'])) {
            $this->form_values['category_id'] = $this->template_data['category_id'];
        }

        if (!empty($this->template_data['warranty_time'])) {
            $this->form_values['warranty_time'] = $this->template_data['warranty_time'];
        }

        if (!empty($this->template_data['manufacturing_time'])) {
            $this->form_values['manufacturing_time'] = $this->template_data['manufacturing_time'];
        }

        if (!empty($this->template_data['local_pick_up'])) {
            $this->form_values['local_pick_up'] = $this->template_data['local_pick_up'];
        }

        if (!empty($this->template_data['free_shipping'])) {
            $this->form_values['free_shipping'] = $this->template_data['free_shipping'];
        }

        if (!empty($this->template_data['listing_type_id'])) {
            $this->form_values['listing_type'] = $this->template_data['listing_type_id'];
        }

        if (!empty($this->template_data['condition'])) {
            $this->form_values['condition'] = $this->template_data['condition'];
        }

        if (!empty($this->template_data['warranty_type_id'])) {
            $this->form_values['warranty_type'] = $this->template_data['warranty_type_id'];
        }

        if (!empty($this->template_data['warranty_time_unit'])) {
            $this->form_values['warranty_time_unit'] = $this->template_data['warranty_time_unit'];
        }

        if (!empty($this->template_data['channels'])) {
            $this->form_values['channel'] = $this->template_data['channels'];
        }

        if (!empty($this->template_data['shipping']) && isset($this->template_data['shipping']->mode) && !empty($this->template_data['shipping']->mode)) {
            $this->form_values['shipping_method'] = $this->template_data['shipping']->mode;
        }

        if (!empty($this->template_data['official_store_id'])) {
            $this->form_values['official_store_id'] = $this->template_data['official_store_id'];
        }

        if (!empty($this->template_data['catalog_listing'])) {
            $this->form_values['catalog_listing'] = $this->template_data['catalog_listing'];
        }

        if (!empty($this->template_data['status'])) {
            $this->form_values['status'] = $this->template_data['status'];
        }
    }

    public function formatCategoryData($meli_category_data)
    {

        if (isset($meli_category_data['settings']['buying_modes'])) {
            $this->select_options['buying_modes'] = [];

            if (in_array('auction', $meli_category_data['settings']['buying_modes'])) {
                $this->select_options['buying_modes']['auction'] = esc_html__('Auction', 'meliconnect');
            }

            if (in_array('buy_it_now', $meli_category_data['settings']['buying_modes'])) {
                $this->select_options['buying_modes']['buy_it_now'] = esc_html__('Buy It Now', 'meliconnect');
            }
        }

        if (isset($meli_category_data['settings']['currencies'])) {
            $this->select_options['currencies'] = [];

            foreach ($meli_category_data['settings']['currencies'] as $currency) {
                $this->select_options['currencies'][$currency] = $currency;
            }
        }

        /* if(isset($meli_category_data['settings']['item_conditions'])){
            $this->select_options['condition'] = [];

            foreach ($this->select_options['currencies'] as $currency) {
                $this->select_options['currencies'][$currency] = $currency;
            }
        } */
    }

    public function formatChannels($seller_data)
    {
        if (isset($seller_data->has_mercadoshops) && $seller_data->has_mercadoshops) {
            $this->select_options['channels'] = [
                'mercadolibre' => esc_html__('Mercadolibre', 'meliconnect'),
                'mercadoshop' => esc_html__('Mercadoshop', 'meliconnect'),
                'all' => esc_html__('Mercadolibre y Mercadoshop', 'meliconnect'),
            ];
        }
    }

    public function formatSaleTerms($sale_terms)
    {
        if (!empty($sale_terms) && is_iterable($sale_terms)) {
            foreach ($sale_terms as $term) {

                if ($term['id'] == 'WARRANTY_TYPE') {
                    $warranty_type = [];

                    foreach ($term['values'] as $value) {
                        $warranty_type[$value['id']] = $value['name'];
                    }

                    $this->select_options['warranty_types'] = $warranty_type;
                }

                if ($term['id'] == 'WARRANTY_TIME') {
                    $time_allowed_units = [];

                    foreach ($term['allowed_units'] as $unit) {
                        $time_allowed_units[$unit['id']] = $unit['name'];
                    }

                    $this->select_options['warranty_time_units'] = $time_allowed_units;
                }

                if ($term['id'] == 'MANUFACTURING_TIME') {
                    $manufacturing_allowed_units = [];

                    foreach ($term['allowed_units'] as $unit) {
                        $manufacturing_allowed_units[$unit['id']] = $unit['name'];
                    }

                    $this->select_options['manufacturing_time_units'] = $manufacturing_allowed_units;
                }
            }
        }
    }


    public function formatCategoryShippingPreferencesResponse($category_shipping_preferences)
    {

        $formatted_category_shipping_preferences = [];

        foreach ($category_shipping_preferences as $key => $category_shipping_preference) {
            $current_mode = $category_shipping_preference->mode;

            $formatted_category_shipping_preferences[$current_mode] = $this->shipping_modes_names[$current_mode] ?? $current_mode;
        }

        return $formatted_category_shipping_preferences;
    }

    public function formatOfficialStroresResponse($official_stores_by_seller)
    {
        $formatted_official_stores_by_seller = [];
        $oficial_sotres_select = [];

        foreach ($official_stores_by_seller as $official_store) {
            $formatted_official_stores_by_seller[$official_store->official_store_id] = [
                'official_store_id' => $official_store->official_store_id,
                'name' => $official_store->fantasy_name,
                'permalink' => $official_store->permalink
            ];

            $oficial_sotres_select[$official_store->official_store_id] = $official_store->fantasy_name;
        }

        $this->select_options['official_stores'] = $oficial_sotres_select;

        return $formatted_official_stores_by_seller;
    }

    public function formatListingTypesResponse($listing_types_by_category_and_seller)
    {

        $formatted_listing_types_by_category_and_seller = [];

        foreach ($listing_types_by_category_and_seller as $listing_type) {
            $formatted_listing_types_by_category_and_seller[$listing_type->id] = $listing_type->name;
        }

        $this->select_options['listing_types'] =  $formatted_listing_types_by_category_and_seller;
    }

    public function melicon_save_custom_product_data($post_id)
    {

        //VERIFY NONCE
        if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'woocommerce_save_product')) {
            //wp_die('Nonce verification failed');
        }

        $post = wp_unslash($_POST);

        $template_id = get_post_meta($post_id, 'melicon_asoc_template_id', true);

        if (!isset($post['template']) || empty($post['template']) || empty($template_id)) {
            return;
        }

        $woo_product_id = $post['post_ID'];
        $woo_product_title = $post['post_title'];
        $template_post_data = $post['template'];

        $template_id = Template::createUpdateProductTemplateFromPost($template_post_data, $woo_product_id, $woo_product_title);
    }



    public function getPendingRequiredAttrs()
    {
        $used_attrs = $this->matched_attrs;
        $meli_attrs = $this->meli_category_attrs;

        // Array de IDs de atributos usados
        $used_attr_ids = array_column($used_attrs, 'id');

        // Inicializar el array que contendrá los atributos pendientes
        $pending_required_attrs_names = [];

        // Recorrer los atributos de meli_attrs
        foreach ($meli_attrs as $meli_attr) {
            // Verificar si el tag 'required' está presente en los tags del atributo
            if ($this->isRequiredAttribute($meli_attr)) {
                // Verificar si el atributo no está en used_attrs
                if (!in_array($meli_attr['id'], $used_attr_ids)) {
                    // Agregar el atributo pendiente al array
                    $pending_required_attrs_names[] = $meli_attr['name'];
                }
            }
        }

        // Devolver el array de atributos requeridos pendientes
        return $pending_required_attrs_names;
    }

    public function isRequiredAttribute($meli_attr)
    {
        return isset($meli_attr['tags']['required']) || isset($meli_attr['tags']['conditional_required']) || in_array($meli_attr['id'], ['BRAND', 'MODEL']);
    }




    public function add_mercadolibre_attrs_content()
    {


        $pending_required_attrs_names = $this->getPendingrequiredAttrs();
        $meli_attrs = $this->meli_category_attrs;

        $logs_view_data = [
            'meli_attrs' => $meli_attrs,
            'pending_required_attrs_names' => $pending_required_attrs_names,
            'instance' => $this
        ];

        Helper::load_partial('includes/Core/Views/Partials/meliconnect_product_edit_atts_tab.php', $logs_view_data);
    }



    public function attr_is_matched($attr_name)
    {
        $matched_attrs = $this->matched_attrs;

        if (!empty($matched_attrs) && is_array($matched_attrs)) {
            foreach ($matched_attrs as $matched) {
                if ($matched['name'] === $attr_name) {
                    return true;
                }
            }
        }

        return false;
    }

    public function get_attr_value_type($attr)
    {
        // Validamos que 'value_type' exista en el atributo
        if (!isset($attr['value_type'])) {
            return esc_html__('Invalid attribute type', 'meliconnect');
        }

        switch ($attr['value_type']) {
            case 'list':
            case 'boolean':
                return esc_html__('Value must be in list', 'meliconnect');

            case 'string':
                return esc_html__('Any text', 'meliconnect');

            case 'number':
                return esc_html__('Any number', 'meliconnect');

            case 'number_unit':
                // Verificamos si 'allowed_units' está disponible y no está vacío
                if (isset($attr['allowed_units']) && is_array($attr['allowed_units']) && !empty($attr['allowed_units'])) {
                    $allowed_units = array_map('esc_html', array_column($attr['allowed_units'], 'name'));
                    return esc_html__('Any number with units in: ', 'meliconnect') . implode(' | ', $allowed_units);
                } else {
                    return esc_html__('Any number', 'meliconnect');
                }

            default:
                // Si no hay coincidencia, retornar el tipo de valor
                return esc_html__('Unknown value type: ', 'meliconnect') . esc_html($attr['value_type']);
        }
    }


    

    public function display_attribute_tags_info($attribute)
    {
        if (isset($attribute['tags']) && is_array($attribute['tags'])) {
            $tags = $attribute['tags'];

            // Variations are allowed for this attribute
            if (isset($tags['allow_variations']) && $tags['allow_variations'] === true) {
                echo '<p>' . esc_html__('Variations are allowed for this attribute.', 'meliconnect') . '</p>';
            }

            // It's required to identify the product the item represents
            if (isset($tags['catalog_required']) && $tags['catalog_required'] === true) {
                echo '<p>' . esc_html__('It\'s required to identify the product the item represents.', 'meliconnect') . '</p>';
            }

            // Conditional required
            if (isset($tags['conditional_required']) && $tags['conditional_required'] === true) {
                echo '<p>' . esc_html__('This attribute is required when certain conditions are met.', 'meliconnect') . '</p>';
            }

            // Defines the variation picture
            if (isset($tags['defines_picture']) && $tags['defines_picture'] === true) {
                echo '<p>' . esc_html__('This attribute defines the variation picture.', 'meliconnect') . '</p>';
            }

            // The value is fixed for this category
            if (isset($tags['fixed']) && $tags['fixed'] === true) {
                echo '<p>' . esc_html__('The value is fixed for this category. This value can\'t be changed.', 'meliconnect') . '</p>';
            }

            // Can be used as a filter in grid searches
            if (isset($tags['grid_filter']) && $tags['grid_filter'] === true) {
                echo '<p>' . esc_html__('This attribute can be used as a filter when searching on category existing grids.', 'meliconnect') . '</p>';
            }

            // Required for technical specifications in the grid
            if (isset($tags['grid_template_required']) && $tags['grid_template_required'] === true) {
                echo '<p>' . esc_html__('This attribute is required for technical specifications in the grid defined for the category.', 'meliconnect') . '</p>';
            }

            // Hidden for this category
            if (isset($tags['hidden']) && $tags['hidden'] === true) {
                echo '<p>' . esc_html__('This attribute is hidden for this category.', 'meliconnect') . '</p>';
            }

            // Inferred for this category
            if (isset($tags['inferred']) && $tags['inferred'] === true) {
                echo '<p>' . esc_html__('The value is inferred for this category.', 'meliconnect') . '</p>';
            }

            // More than one value can be assigned to this attribute
            if (isset($tags['multivalued']) && $tags['multivalued'] === true) {
                echo '<p>' . esc_html__('More than one value may be assigned to this attribute.', 'meliconnect') . '</p>';
            }

            // Required when the condition is new
            if (isset($tags['new_required']) && $tags['new_required'] === true) {
                echo '<p>' . esc_html__('This attribute is required when the item\'s condition is new.', 'meliconnect') . '</p>';
            }

            // Fixed or restricted for all the category's sisters
            if (isset($tags['others']) && $tags['others'] === true) {
                echo '<p>' . esc_html__('This attribute is fixed or restricted but not inferred for all the category\'s sisters.', 'meliconnect') . '</p>';
            }

            // Part of the product's primary key
            if (isset($tags['product_pk']) && $tags['product_pk'] === true) {
                echo '<p>' . esc_html__('This attribute is part of the primary key of the product and can identify a unique product in the catalog.', 'meliconnect') . '</p>';
            }

            // Read-only attribute for internal purposes
            if (isset($tags['read_only']) && $tags['read_only'] === true) {
                echo '<p>' . esc_html__('This attribute is read-only and is used for internal purposes only.', 'meliconnect') . '</p>';
            }

            // Required attribute
            if (isset($tags['required']) && $tags['required'] === true) {
                echo '<p>' . esc_html__('is a REQUIRED attribute for this category.', 'meliconnect') . '</p>';
            }

            // Restricted values for this category
            if (isset($tags['restricted_values']) && $tags['restricted_values'] === true) {
                echo '<p>' . esc_html__('The values are restricted for this category.', 'meliconnect') . '</p>';
            }

            // Attribute is used for variations
            if (isset($tags['variation_attribute']) && $tags['variation_attribute'] === true) {
                echo '<p>' . esc_html__('This attribute is an attribute of a variation.', 'meliconnect') . '</p>';
            }
        }
    }



    public function findTaxonomyInMeliAttrs($attrName)
    {

        if (isset($attrName) && !empty($this->meli_category_attrs) && is_array($this->meli_category_attrs)) {

            foreach ($this->meli_category_attrs as $key => $value) {

                if ($value['name'] === $attrName) {
                    return $value;
                }/* else{
                    echo '<pre> No match:' . wp_json_encode($value['name']) . '</pre>';
                } */
            }
        }

        return false;
    }


    public  function add_mercadolibre_product_tab($tabs)
    {
        $tabs['mercadolibre'] = array(
            'label'    => esc_html__('Mercadolibre', 'meliconnect'),
            'target'   => 'mercadolibre_product_data',
            'class'    => array('show_if_simple', 'show_if_variable'), // Mostrar para productos simples y variables
            'priority' => 60,
        );

        $tabs['meliconnect_logs'] = array(
            'label'    => esc_html__('Export Logs', 'meliconnect'),
            'target'   => 'meliconnect_logs_product_data',
            'class'    => array('show_if_simple', 'show_if_variable'), // Mostrar para productos simples y variables
            'priority' => 60,
        );

        return $tabs;
    }

    public function add_mercadolibre_product_tab_content()
    {
        // Desestructurar los datos del template
        $template = isset($this->template_data) ? $this->template_data : [];
        $seller_ids_and_names = Helper::getSellersIdsAndNames();
        $seller_meli_id = isset($template['seller_meli_id']) ? $template['seller_meli_id'] : '';


        /* echo PHP_EOL . '-------------------- $this->template_data --------------------' . PHP_EOL;
        echo '<pre>' . wp_json_encode($this->template_data) . '</pre>';
        echo PHP_EOL . '-------------------  FINISHED  ---------------------' . PHP_EOL;
        wp_die(); */

        $mercadolibre_view_data = [
            'select_options'           => $this->select_options,
            'form_values'              => $this->form_values,
            'seller_meli_id'           => $seller_meli_id,
            'sellers_ids_and_names'    => $seller_ids_and_names,
            'disabled_seller'          => (count($seller_ids_and_names) === 1 || !empty($seller_meli_id)) ? true : false,
            'woo_product_id'           => $this->woo_product_id,
        ];

        // Cargar la vista con los datos
        Helper::load_partial('includes/Core/Views/Partials/meliconnect_product_edit_mercadolibre_tab.php', $mercadolibre_view_data);
    }

    public function add_meliconnect_logs_product_tab_content()
    {
        $item_export_error = get_post_meta($this->woo_product_id, 'melicon_export_meli_errors', true);

        $item_export_error = !empty($item_export_error) ? maybe_unserialize($item_export_error) : [];

        $last_export_time =  get_post_meta($this->woo_product_id, 'melicon_last_export_time', true);
        $error_time =  get_post_meta($this->woo_product_id, 'melicon_export_meli_error_time', true);

        if (!empty($last_export_time)) {
            $last_export_time = wp_date('d-m-Y H:i:s', $last_export_time);
        }
        
        if (!empty($error_time)) {
            $error_time = wp_date('d-m-Y H:i:s', $error_time);
        }

        $logs_view_data = [
            'item_export_error'             => (isset($item_export_error['item']) && !empty($item_export_error['item'])) ? $item_export_error['item'] : '',
            'description_export_error'      => (isset($item_export_error['description']) && !empty($item_export_error['description'])) ? $item_export_error['description'] : '',
            'export_error_time'             => $error_time,
            'last_export_time'              => $last_export_time,
            'last_json_sent'                => get_post_meta($this->woo_product_id, 'melicon_last_export_json_sent', true),
        ];

        // Cargar la vista con los datos
        Helper::load_partial('includes/Core/Views/Partials/meliconnect_product_edit_logs_tab.php', $logs_view_data);
    }



    public function  meliconnect_add_general_tab_html()
    {

        $general_view_data = [
            'price_operand' =>  isset($this->template_data['price_operand']) ? $this->template_data['price_operand'] : 'sum',
            'price_amount' => isset($this->template_data['price_amount']) ? $this->template_data['price_amount'] : 0,
            'price_type' => isset($this->template_data['price_type']) ? $this->template_data['price_type'] : 'percent',
        ];

        Helper::load_partial('includes/Core/Views/Partials/meliconnect_product_edit_general_tab.php', $general_view_data);
    }

    public function  meliconnect_add_stock_tab_html()
    {
        $stock_view_data = [
            'stock_operand' =>  isset($this->template_data['price_operand']) ? $this->template_data['price_operand'] : 'sum',
            'stock_amount' => isset($this->template_data['price_amount']) ? $this->template_data['price_amount'] : 0,
            'stock_type' => isset($this->template_data['price_type']) ? $this->template_data['price_type'] : 'percent',
        ];

        Helper::load_partial('includes/Core/Views/Partials/meliconnect_product_edit_stock_tab.php', $stock_view_data);
    }

    // Función para agregar el metabox en la página de edición de productos

    public function add_meliconnect_meta_box_data()
    {
        $this->set_product_vars();

        add_meta_box(
            'meliconnect_meta_box_data', // ID del metabox
            esc_html__('Meliconnect Data', 'meliconnect'), // Título del metabox
            [$this, 'display_meliconnect_meta_box_data'], // Función para mostrar el contenido
            'product',
            'side', // Contexto, puedes usar 'normal', 'side' o 'advanced'
            'high' // Prioridad
        );
    }

    public function add_meliconnect_meta_box_actions()
    {
        $this->set_product_vars();

        add_meta_box(
            'meliconnect_meta_box', // ID del metabox
            esc_html__('Meliconnect Actions', 'meliconnect'), // Título del metabox
            [$this, 'display_meliconnect_meta_box_actions'], // Función para mostrar el contenido
            'product',
            'side', // Contexto, puedes usar 'normal', 'side' o 'advanced'
            'high' // Prioridad
        );
    }

    // Function to display the content of the metabox
    public function display_meliconnect_meta_box_data($post)
    {
        // Get postmeta values
        $meli_listing_id = get_post_meta($post->ID, 'melicon_meli_listing_id', true);
        $meli_permalink = get_post_meta($post->ID, 'melicon_meli_permalink', true);
        $template_id = get_post_meta($post->ID, 'melicon_asoc_template_id', true);
        $seller_id = get_post_meta($post->ID, 'melicon_meli_seller_id', true);
        $meli_status = get_post_meta($post->ID, 'melicon_meli_status', true);


        // Pass variables to the template
        $box_view_data = [
            'meli_listing_id' => $meli_listing_id,
            'meli_permalink' => $meli_permalink,
            'template_id' => $template_id,
            'seller_id' => $seller_id,
            'meli_status' => $meli_status,
            'woo_product_id' => get_the_ID(),
        ];

        Helper::load_partial('includes/Core/Views/Partials/meliconnect_meta_box_data.php', $box_view_data);
    }

    public function display_meliconnect_meta_box_actions($post)
    {
        // Get postmeta values
        $meli_listing_id = get_post_meta($post->ID, 'melicon_meli_listing_id', true);
        $meli_permalink = get_post_meta($post->ID, 'melicon_meli_permalink', true);
        $template_id = get_post_meta($post->ID, 'melicon_asoc_template_id', true);
        $seller_id = get_post_meta($post->ID, 'melicon_meli_seller_id', true);
        $meli_status = get_post_meta($post->ID, 'melicon_meli_status', true);


        // Pass variables to the template
        $box_view_data = [
            'meli_listing_id' => $meli_listing_id,
            'meli_permalink' => $meli_permalink,
            'template_id' => $template_id,
            'seller_id' => $seller_id,
            'meli_status' => $meli_status,
            'woo_product_id' => get_the_ID(),
        ];



        Helper::load_partial('includes/Core/Views/Partials/meliconnect_meta_box_actions.php', $box_view_data);
    }


    public static function handleImportSingleListing()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'melicon_import_single_listing_nonce')) {
            wp_send_json_error(esc_html__('Invalid nonce', 'meliconnect'));
            return;
        }

        if (!current_user_can('meliconnect_manage_plugin')) {
            wp_send_json_error(esc_html__('You do not have permission to perform this action', 'meliconnect'));
            return;
        }

        $meli_listing_id = isset($_POST['meli_listing_id']) ? sanitize_text_field($_POST['meli_listing_id']) : null;
        $seller_id = isset($_POST['seller_id']) ? sanitize_text_field($_POST['seller_id']) : null;
        $template_id = isset($_POST['template_id']) ? sanitize_text_field($_POST['template_id']) : null;
        $woo_product_id = isset($_POST['woo_product_id']) ? sanitize_text_field($_POST['woo_product_id']) : null;

        if (empty($seller_id) || empty($meli_listing_id)) {
            wp_send_json_error(['message' => 'Invalid data']);
            wp_die();
        }

        $wooCommerceAdapter = new WooCommerceProductAdapter();
        $productCreationService = new WooCommerceProductCreationService();
        $productDataFacade = new ProductDataFacade($wooCommerceAdapter, $productCreationService);

        // 2- Format items data to send, Send data to API server and Get response and process response creating or updating items in WooCommerce
        $productDataFacade->importAndCreateProduct($meli_listing_id, $seller_id, $template_id, $woo_product_id);

        wp_send_json_success(esc_html__('Product imported successfully', 'meliconnect'));

        wp_die();
    }

    public static function handleExportSingleListing()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'melicon_export_single_listing_nonce')) {
            wp_send_json_error(esc_html__('Invalid nonce', 'meliconnect'));
            return;
        }

        if (!current_user_can('meliconnect_manage_plugin')) {
            wp_send_json_error(esc_html__('You do not have permission to perform this action', 'meliconnect'));
            return;
        }

        $meli_listing_id = isset($_POST['meli_listing_id']) ? sanitize_text_field($_POST['meli_listing_id']) : null;
        $seller_id = isset($_POST['seller_id']) ? sanitize_text_field($_POST['seller_id']) : null;
        $template_id = isset($_POST['template_id']) ? sanitize_text_field($_POST['template_id']) : null;
        $woo_product_id = isset($_POST['woo_product_id']) ? sanitize_text_field($_POST['woo_product_id']) : null;


        if (empty($seller_id) || empty($woo_product_id) || empty($template_id)) {
            wp_send_json_error(['message' => 'Invalid data']);
            wp_die();
        }

        $meliListingAdapter = new MercadoLibreListingAdapter();
        $listingDataFacade = new ListingDataFacade($meliListingAdapter);

        $exportedResponse = $listingDataFacade->getAndExportListing($seller_id, $woo_product_id, $template_id, $meli_listing_id);

        // Variables to handle the success or failure of item and description
        $item_success = false;
        $description_success = false;
        $item_message = '';
        $description_message = '';

        if (isset($exportedResponse['status']) && $exportedResponse['status'] == 200 && isset($exportedResponse['data'][0])) {
            $listing_response = $exportedResponse['data'][0];

            // Item validation
            if (isset($listing_response['item']['success']) && $listing_response['item']['success'] == true) {
                $item_success = true;
                $item_message = esc_html__('Item published successfully.', 'meliconnect');
            } else {
                $item_message = $listing_response['item']['body'] ?? esc_html__('Item publication failed.', 'meliconnect');
            }

            // Description validation
            if (isset($listing_response['description']['success']) && $listing_response['description']['success'] == true) {
                $description_success = true;
                $description_message = esc_html__('Description published successfully.', 'meliconnect');
            } else {
                $description_message = $listing_response['description']['body'] ?? esc_html__('Description publication failed.', 'meliconnect');
            }

            // Handle different success or failure cases
            if ($item_success && $description_success) {
                // Both successful
                wp_send_json_success([
                    'message' => esc_html__('Export successful', 'meliconnect'),
                    'item_message' => $item_message,
                    'description_message' => $description_message
                ]);
            } elseif ($item_success && !$description_success) {
                // Item successful, description failed
                wp_send_json_error([
                    'message' => esc_html__('Item exported successfully, but there was an issue with the description.', 'meliconnect'),
                    'item_message' => $item_message,
                    'description_message' => $description_message
                ]);
            } elseif (!$item_success && $description_success) {
                // Description successful, item failed
                wp_send_json_error([
                    'message' => esc_html__('Description exported successfully, but there was an issue with the item.', 'meliconnect'),
                    'item_message' => $item_message,
                    'description_message' => $description_message
                ]);
            } else {
                // Both failed
                wp_send_json_error([
                    'message' => esc_html__('Error exporting item and description.', 'meliconnect'),
                    'item_message' => $item_message,
                    'description_message' => $description_message
                ]);
            }
        } else {
            // General error case
            wp_send_json_error([
                'message' => esc_html__('Error during export.', 'meliconnect'),
                'response' => $exportedResponse
            ]);
        }

        wp_die();
    }

    public static function handleUnlinkSingleListing()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'melicon_unlink_single_listing_nonce')) {
            wp_send_json_error(esc_html__('Invalid nonce', 'meliconnect'));
            wp_die();
        }

        // Verifica permisos
        if (!current_user_can('meliconnect_manage_plugin')) {
            wp_send_json_error(esc_html__('You do not have permission to perform this action', 'meliconnect'));
            wp_die();
        }

        // Sanitiza y valida los datos
        $woo_product_id = isset($_POST['woo_product_id']) ? sanitize_text_field($_POST['woo_product_id']) : null;
        $unlink_type = isset($_POST['unlink_type']) ? sanitize_text_field($_POST['unlink_type']) : null;

        if (empty($woo_product_id) || empty($unlink_type)) {
            wp_send_json_error(['message' => esc_html__('Invalid data', 'meliconnect')]);
            wp_die();
        }

        // Determina el nuevo estado según el tipo de desvinculación
        switch ($unlink_type) {
            case 'desvinculate_pause':
                $new_status = 'paused';
                break;
            case 'desvinculate_delete':
                $new_status = 'closed';
                break;
            default:
                $new_status = '';
                break;
        }

        $update_status = false;

        // Cambia el estado si es necesario
        if (!empty($new_status)) {
            $update_status = Helper::change_meli_listing_status($woo_product_id, $new_status);
        }

        // Desvincula el producto si no se requiere cambio de estado o si el cambio fue exitoso
        if (empty($new_status) || $update_status) {
            Helper::unlinkProduct($woo_product_id);
            ProductToExport::unlink_woo_product($woo_product_id);
            wp_send_json_success(['message' => esc_html__('Product desvinculated successfully', 'meliconnect')]);
        } else {
            wp_send_json_error(['message' => esc_html__('Failed to update product status', 'meliconnect')]);
        }

        wp_die();
    }

    public static function handleSaveTemplateData()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'melicon_save_template_data_nonce')) {
            wp_send_json_error(esc_html__('Invalid nonce', 'meliconnect'));
            wp_die();
        }

        if (!current_user_can('meliconnect_manage_plugin')) {
            wp_send_json_error(esc_html__('You do not have permission to perform this action', 'meliconnect'));
            wp_die();
        }

        $templateDataJSON = isset($_POST['templateData']) ? $_POST['templateData'] : null;
        $woo_product_id = isset($_POST['woo_product_id']) ? sanitize_text_field($_POST['woo_product_id']) : null;
        $woo_product_title = isset($_POST['woo_product_title']) ? sanitize_text_field($_POST['woo_product_title']) : null;

        if (empty($templateDataJSON) || empty($woo_product_id) || empty($woo_product_title)) {
            wp_send_json_error(['message' => esc_html__('Invalid data', 'meliconnect')]);
            wp_die();
        }

        $template_data = json_decode(stripslashes($templateDataJSON), true);

        if (json_last_error() === JSON_ERROR_NONE) {

            // Transforma las claves en un array multidimensional
            $parsed_template_data = [];
            parse_str(http_build_query($template_data), $parsed_template_data);


            $template_id = Template::createUpdateProductTemplateFromPost($parsed_template_data['template'], $woo_product_id, $woo_product_title);

            if (empty($template_id)) {
                wp_send_json_error(['message' => esc_html__('Failed to save template', 'meliconnect')]);
                wp_die();
            }

            wp_send_json_success(['message' => esc_html__('Template saved successfully', 'meliconnect')]);
        } else {

            $message = sprintf(
                /* translators: %s is the error message returned by json_last_error_msg() */
                esc_html__('Failed to decode Template Data: %s', 'meliconnect'),
                json_last_error_msg()
            );

            wp_send_json_error(['message' => $message]);
        }

        wp_die();
    }
}
