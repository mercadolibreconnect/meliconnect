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
        add_action('add_meta_boxes', [$this, 'add_meliconnect_meta_box']);
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

        //when user saves variations
        /* add_action('woocommerce_ajax_save_product_variations', [$this, 'melicon_save_template_variations'], 10, 1);

        add_action('woocommerce_ajax_save_attributes', [$this, 'melicon_save_attributes'], 10, 1); */




        $this->loadAssets();

        $this->shipping_modes_names = [
            'custom' => __('Custom', 'meliconnect'),
            'not_specified' => __('No Shipping Configuration', 'meliconnect'),
            'me2' => __('Mercado Envíos - Standard ', 'meliconnect') . '(' . __('ME2', 'meliconnect') . ')',
            'me1' => __('Mercado Envíos - Express ', 'meliconnect') . '(' . __('ME1', 'meliconnect') . ')',
        ];

        $this->select_options = [
            'buying_modes' => ['buy_it_now' => __('Buy It Now', 'meliconnect')],
            'listing_types' => [
                'gold_premium' => __('Gold Premium', 'meliconnect'),
                'gold' => __('Gold', 'meliconnect'),
                'silver' => __('Silver', 'meliconnect'),
            ],
            'conditions' => [
                'new' => __('New', 'meliconnect'),
                'used' => __('Used', 'meliconnect'),
            ],
            'warranty_types' => [
                '2230280' => __('Seller Warranty', 'meliconnect'),
                '2230279' => __('Manufacturer Warranty', 'meliconnect'),
                '6150835' => __('No Warranty', 'meliconnect'),
            ],
            'warranty_time_units' => [
                'días' => __('días', 'meliconnect'),
                'meses' => __('meses', 'meliconnect'),
                'años' => __('años', 'meliconnect'),
            ],
            'currencies' => [
                'USD' => __('USD', 'meliconnect'),
            ],
            'channels' => ['mercadolibre' => __('Mercadolibre', 'meliconnect')],
            'shipping_methods' => [
                'me2' => __('Mercado Envíos - Standard ', 'meliconnect') . '(' . __('ME2', 'meliconnect') . ')',
                'me1' => __('Mercado Envíos - Express ', 'meliconnect') . '(' . __('ME1', 'meliconnect') . ')',
                'not_specified' => __('No Shipping Configuration', 'meliconnect'),
            ],
            'manufacturing_time_units' => [
                'días' => __('días', 'meliconnect'),
            ],
            'status' => [
                'active' => __('Active', 'meliconnect'),
                'paused' => __('Paused', 'meliconnect'),
                'closed' => __('Closed', 'meliconnect'),
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
        $parent_id = $_POST['product_id'];
        $variation_id = $variation->get_id();

        $template_id = get_post_meta($parent_id, 'melicon_asoc_template_id', true);


        foreach ($_POST['template']['variations'] as $variation_key => $variation_values) {
            $current_meli_variation_id = $variation_values['variation_data']['meli_variation_id'] ?? '';

            if(isset($variation_values['variation_data']['disable_sync']) && $variation_values['variation_data']['disable_sync'] == 1){
                update_post_meta( $variation_id, 'melicon_meli_asoc_variation_sync_disabled', 1 );
            }else{
                update_post_meta( $variation_id, 'melicon_meli_asoc_variation_sync_disabled', 0 );
            }

            foreach ($variation_values['attrs'] as $meli_attribute_name => $meli_value_data) {
                
                $meliDataUnescaped = stripslashes($meli_value_data['meli_data']);
                $meliDataArray = json_decode($meliDataUnescaped, true);

                $variation_template_data = [
                    'template_id' =>  $template_id,
                    'used_by' => 'variation',
                    'used_asoc_id' => $variation_id,
                    'product_parent_id' => $_POST['product_id'],
                    'woo_attribute_id' => '',
                    'meli_variation_id' =>  $current_meli_variation_id, // Ej: 181734597045
                    'meli_attribute_id' => $meliDataArray['id'], //Ej: COLOR
                    'meli_attribute_name' => $meliDataArray['name'], //Ej: Color
                    'meli_value_id' => $meli_value_data['value'], // Ej: 52049
                    'meli_value_name' => $meli_attribute_name, //Ej: Negro
                    'meli_value_type' => $meliDataArray['value_type'], //Ej: string 
                    'allow_variations_tag' => (isset($meliDataArray['tags']['allow_variations']) && $meliDataArray['tags']['allow_variations']) ? 1 : 0 , // Ej:1
                    'variation_attribute_tag' => (isset($meliDataArray['tags']['variation_attribute_tag']) && $meliDataArray['tags']['variation_attribute']) ? 1 : 0 , // Ej:0
                    'required_tag' => (isset($meliDataArray['tags']['required_tag']) && $meliDataArray['tags']['required']) ? 1 : 0 , // Ej:0
                    'not_apply' => 0,
                ];
                /* echo PHP_EOL . '-------------------- variation_template_data --------------------' . PHP_EOL;
                echo '<pre>' . var_export($meliDataArray, true) . '</pre>';
                echo '<pre>' . var_export( $variation_template_data, true) . '</pre>';
                echo PHP_EOL . '-------------------  FINISHED  ---------------------' . PHP_EOL;
                wp_die(); */
                Template::createUpdateTemplateAttributes('variation', $variation_id, $meliDataArray['id'], $variation_template_data );  
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

        

        //Prints selects with values as selected
        foreach ($current_variation_attrs as $name => $attr_value) {

            // Normalizar el nombre del atributo de la variación usando Helper::normalizeString
            $normalized_name = Helper::normalizeString($name);

            // Normalizar las claves de los atributos de MercadoLibre
            $meli_attributes_normalized = array_map(function ($key) {
                return Helper::normalizeString($key);
            }, array_keys($this->meli_category_variable_attrs));

            // Verificar si el atributo normalizado existe en los atributos de meli
            $meli_attr_index = array_search($normalized_name, $meli_attributes_normalized);

            if ($meli_attr_index !== false) {
                // Obtener el nombre real del atributo mapeado en el array original
                $meli_attr_name = array_keys($this->meli_category_variable_attrs)[$meli_attr_index];

                // Mostrar el nombre del atributo
                echo "<div class='melicon_variation_row'>";
                echo "<label for='attribute_$meli_attr_name'>$name:</label><br>";

                $meli_attr_data = $this->getCurrentAttrMeliData($name);
                $meli_attr_data_json = json_encode($meli_attr_data);
                $ml_attr_data_escaped = htmlspecialchars($meli_attr_data_json, ENT_QUOTES, 'UTF-8');

                echo '<input type="text" style="display:none" name="template[variations][' . $loop . '][attrs][' . $meli_attr_name . '][meli_data]" value="' . $ml_attr_data_escaped . '">';
               

                // Select dropdown con los valores
                echo "<select name='template[variations][$loop][attrs][$meli_attr_name][value]' id='attribute_$meli_attr_name'>";

                // Opción predeterminada
                echo "<option value=''>Select a value</option>";

                // Recorrer los valores posibles del atributo
                foreach ($this->meli_category_variable_attrs[$meli_attr_name] as $meli_value_id => $meli_value_name) {
                    // Normalizar el nombre y el valor para comparar
                    $normalized_value = Helper::normalizeString($meli_value_name);
                    $normalized_current_value = Helper::normalizeString($attr_value);

                    // Marcar como seleccionado si los valores normalizados coinciden
                    $selected = ($normalized_current_value == $normalized_value) ? 'selected' : '';
                    echo "<option value='$meli_value_id' $selected>$meli_value_name</option>";
                }

                echo "</select>";

                echo "</div><br>";
            } else {
                // Si el atributo no está mapeado
                echo "<p>Attribute '" . esc_html($name) . "' is not mapped.</p>";
            }
        }

        echo '</p>';
        // Agregar el checkbox para deshabilitar la sincronización

        $variation_sync_is_disabled = get_post_meta($variation->ID, 'melicon_meli_asoc_variation_sync_disabled', true);

        $checked_disabled = ($variation_sync_is_disabled) ? 'checked' : '';


        echo '<input type="text" style="display:none" name="template[variations][' . $loop . '][variation_data][meli_variation_id]" value="' . $meli_variation_id . '">';
        echo "<input type='checkbox' name='template[variations][$loop][variation_data][disable_sync]' class='melicon_variation_disable_sync' value='1' $checked_disabled>";
        echo "<label for='disable_sync_$meli_attr_name'>" . _e("Disable sync for this variation", "meliconnect") . "</label>";

        echo '</div>';

        /* $template_attr = $this->findInTemplateAttrs($meli_attr_name, $this->template_attibutes); */
    }

    public function mapVariationWithMeliVariations($variation, $current_variation_attrs)
    {
        //Find saved variation postmeta
        $meli_variation_id = get_post_meta( $variation->ID, 'melicon_meli_asoc_variation_id', true);


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
                echo '<pre>' . var_export($template_attr, true) . '</pre>';
                echo PHP_EOL . '-------------------  FINISHED  ---------------------' . PHP_EOL;
            }
        }


        echo PHP_EOL . '-------------------- VARIATION --------------------' . PHP_EOL;
        echo '<pre>' . var_export($variation, true) . '</pre>';
        echo PHP_EOL . '-------------------  FINISHED  ---------------------' . PHP_EOL;

        echo PHP_EOL . '-------------------- ATTRIBUTES --------------------' . PHP_EOL;
        echo '<pre>' . var_export($current_variation_attrs, true) . '</pre>';
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



    public function melicon_save_template_variations($post_id)
    {

        echo PHP_EOL . '-------------------- $_POST --------------------' . PHP_EOL;
        echo '<pre>' . var_export($_POST, true) . '</pre>';
        echo PHP_EOL . '-------------------  FINISHED  ---------------------' . PHP_EOL;
        wp_die();
    }

    public function melicon_save_attributes($post_id)
    {
        echo 'Función ejecutada correctamente 2';
    }


    public function match_mercadolibre_atts($attribute, $i)
    {

        echo '<div class="melicon_meli_attribute_info_container">';

        // Obtén el objeto de la taxonomía si está presente
        $taxonomy_object = $attribute->get_taxonomy_object();



        // Si existe el objeto de la taxonomía y tiene una etiqueta de atributo
        if (!empty($taxonomy_object) && isset($taxonomy_object->attribute_label) && !empty($taxonomy_object->attribute_label)) {
            $current_attr_name = $taxonomy_object->attribute_label;
        } else {
            // Si no existe, obten el nombre del atributo
            $current_attr_name = $attribute->get_name();
        }

        // Obtén las opciones del atributo (IDs de los términos)
        $current_attr_options = $attribute->get_options();

        // Si es una taxonomía, obten los nombres de los términos
        if ($attribute->is_taxonomy()) {
            $taxonomy_name = $attribute->get_name(); // Nombre de la taxonomía (ej: 'pa_color')

            // Recorre los IDs y obtén los nombres de los términos
            $term_names = [];
            foreach ($current_attr_options as $term_id) {
                // Obtén el término por ID
                $term = get_term_by('id', $term_id, $taxonomy_name);
                if ($term && !is_wp_error($term)) {
                    $term_names[] = $term->name; // Obtén el nombre del término
                }
            }

            // Asigna los nombres al array de opciones
            $current_attr_options = $term_names;
        }

        /* echo PHP_EOL . '-------------------- current_attr_name --------------------' . PHP_EOL;
        echo '<pre>' . var_export($current_attr_name, true) . '</pre>';
        echo PHP_EOL . '-------------------  FINISHED  ---------------------' . PHP_EOL;

        echo PHP_EOL . '-------------------- current_attr_options --------------------' . PHP_EOL;
        echo '<pre>' . var_export($current_attr_options, true) . '</pre>';
        echo PHP_EOL . '-------------------  FINISHED  ---------------------' . PHP_EOL; */



        $find_in_meli_attr = $this->findTaxonomyInMeliAttrs($current_attr_name);


        if ($find_in_meli_attr) {
            // Almacenar la coincidencia de atributos en el array de atributos coincidentes
            $this->matched_attrs[] = $find_in_meli_attr;

            // Crear un array con los valores de MercadoLibre y WooCommerce
            $template_attr_value = [
                'meli' => $find_in_meli_attr,
                'woo'  => [
                    'attribute_id' => $attribute->get_id(),
                    'name' => $current_attr_name,
                    'values' => $current_attr_options
                ]
            ];

            // Escapar el JSON de forma segura
            $escaped_attr_value = htmlspecialchars(json_encode($template_attr_value), ENT_QUOTES, 'UTF-8');

            // Mostrar la coincidencia y los valores posibles de MercadoLibre

            echo '<p><strong>' . __('Attribute MATCH by name with meli Attr', 'meliconnect') . '</strong></p>';
            echo '<p><strong>' . __('Meli value requirements: ', 'meliconnect') . '</strong>' . $this->printPossibleValues($find_in_meli_attr) . '</p>';



            if (isset($find_in_meli_attr['values']) && !empty($find_in_meli_attr['values'])) {
                $values = array_column($find_in_meli_attr['values'], 'name');

                if (in_array($find_in_meli_attr['value_type'], ['list', 'boolean'])) {
                    $this->display_compare_values_messages($values, $current_attr_options);
                }
            }

            // Input HTML para los atributos
            echo '<input type="text" style="display:none" class="melicon-mercadolibre-attr-input" name="template[attrs][' . esc_attr($i) . ']" value="' . $escaped_attr_value . '" />';

            // Mostrar información adicional sobre los atributos
            $this->display_attribute_tags_info($find_in_meli_attr);
        } else {
            // Mensaje de no coincidencia
            echo '<p>' . __('Attribute NOT MATCH by name with meli Attr', 'meliconnect') . '</p>';
        }

        echo '</div>';
    }

    public function display_compare_values_messages($meli_possbile_values, $woo_attr_values)
    {
        /* echo PHP_EOL . '-------------------- meli_category_attrs --------------------' . PHP_EOL;
        echo '<pre>' . var_export($this->meli_category_attrs, true) . '</pre>';
        echo PHP_EOL . '-------------------  FINISHED  ---------------------' . PHP_EOL; */
        /* echo PHP_EOL . '-------------------- $meli_possbile_values --------------------' . PHP_EOL;
        echo '<pre>' . var_export($meli_possbile_values, true) . '</pre>';
        echo PHP_EOL . '-------------------  FINISHED  ---------------------' . PHP_EOL;
        echo PHP_EOL . '-------------------- woo_attr_values --------------------' . PHP_EOL;
        echo '<pre>' . var_export($woo_attr_values, true) . '</pre>';
        echo PHP_EOL . '-------------------  FINISHED  ---------------------' . PHP_EOL; */
        $not_exportable_values = array_diff($woo_attr_values, $meli_possbile_values);

        // Si hay valores que no se pueden exportar
        if (!empty($not_exportable_values)) {
            // Crear el mensaje con los valores que no se pueden exportar
            $message = __('The following attribute values cannot be exported: ', 'meliconnect') . implode(', ', $not_exportable_values);

            // Mostrar el mensaje
            echo '<p class="melicon-color-error"><strong>' . $message . '</strong></p>';
        } else {
            // Todos los valores son exportables
            echo '<p class="melicon-color-success"><strong>' . __('All attributes values can be exported.', 'meliconnect') . '</strong></p>';
        }
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
        // Verifica si estás en la página de edición de productos de WooCommerce
        /* if (isset($_GET['post_type']) && $_GET['post_type'] === 'product' && isset($_GET['action']) && $_GET['action'] === 'edit') {
            wp_enqueue_script('melicon-product-edit-js', MC_PLUGIN_URL . 'includes/Core/Assets/Js/melicon-product-edit.js', ['jquery'], '1.0.0', true);
        } */

        // Alternativamente, también puedes verificar si estás en la página de edición de un producto específico
        if (isset($_GET['post']) && get_post_type($_GET['post']) === 'product') {
            wp_enqueue_script('melicon-product-edit-js', MC_PLUGIN_URL . 'includes/Core/Assets/Js/melicon-product-edit.js', ['jquery'], '1.0.0', true);
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
            $official_stores_by_seller_response = $meli->getWithHeader('users/' . $seller_data->user_id . '/brands', $seller_data->access_token);
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
        echo '<pre>' . var_export( $this->meli_category_sale_terms , true) . '</pre>';
        echo PHP_EOL . '-------------------  FINISHED  ---------------------' . PHP_EOL;
        wp_die(); */
    }

    public function getAttributesWithTag($meli_category_attrs, $tags_filter = ['allow_variations'])
    {
        $meli_category_variable_attrs = [];

        /* echo PHP_EOL . '-------------------- meli_category_attrs --------------------' . PHP_EOL;
        echo '<pre>' . var_export($meli_category_attrs, true) . '</pre>';
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
                $this->select_options['buying_modes']['auction'] = __('Auction', 'meliconnect');
            }

            if (in_array('buy_it_now', $meli_category_data['settings']['buying_modes'])) {
                $this->select_options['buying_modes']['buy_it_now'] = __('Buy It Now', 'meliconnect');
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
                'mercadolibre' => __('Mercadolibre', 'meliconnect'),
                'mercadoshop' => __('Mercadoshop', 'meliconnect'),
                'all' => __('Mercadolibre y Mercadoshop', 'meliconnect'),
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

        /* echo PHP_EOL . '-------------------- meli_attrs --------------------' . PHP_EOL;
        echo '<pre>' . var_export($meli_attrs, true) . '</pre>';
        echo PHP_EOL . '-------------------  FINISHED  ---------------------' . PHP_EOL; */

        echo '<div id="melicon-mercadolibre-attributes" class="melicon_hide_if_change_category  melicon-mt-2">';
        if (!empty($pending_required_attrs_names)) {
            echo '<div class="melicon-mercadolibre-attributes-warning" style="background-color: #ffcccc; padding: 10px; border-radius: 5px; font-size: 14px">';
            echo '<p><strong>' . __('Following Attributes are REQUIRED and are missing in your product to update or create in Mercadolibre: ', 'meliconnect') . '</strong></br>';
            echo '<span style="font-size: 16px">' . implode(', ', $pending_required_attrs_names) . '</span></p>';

            echo '</div>';
        }
        echo '<hr>';
        if (!empty($meli_attrs) && is_array($meli_attrs)) {

            echo '<p><strong>' . __('Mercadolibre Attributes: ', 'meliconnect') . '</strong></p>';
            echo '<p>' . __('You can create following attributes to update or create in Mercadolibre.', 'meliconnect') . '</p>';
            echo '<div class="melicon-mercadolibre-attributes-table" style="max-height: 500px; overflow-y: auto "> ';
            echo '<table class="wp-list-table widefat fixed striped">';
            echo '<thead>';
            echo '<tr>';
            echo '<th scope="col" class="manage-column column-primary" style="font-weight: bold">' . __('Name', 'meliconnect') . '</th>';
            echo '<th scope="col" class="manage-column" style="text-align:center; font-weight: bold">' . __('Possible Values', 'meliconnect') . '</th>';
            echo '<th scope="col" class="manage-column" style="text-align:center; font-weight: bold">' . __('Required', 'meliconnect') . '</th>';
            echo '<th scope="col" class="manage-column" style="text-align:center; font-weight: bold">' . __('Catalog Required', 'meliconnect') . '</th>';
            echo '<th scope="col" class="manage-column" style="text-align:center; font-weight: bold">' . __('Can be used for variations', 'meliconnect') . '</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';

            foreach ($meli_attrs as $attr) {

                $is_matched = $this->attr_is_matched($attr['name']);

                /* if (isset($attr['tags']['hidden']) && $attr['tags']['hidden'] === true) {
                    continue;
                } */

                echo '<tr>';

                echo '<td class="column-primary">';
                echo esc_html($attr['name']) . ' ';
                if ($is_matched) {
                    echo ' <i class="fas fa-solid fa-check melicon-color-success"></i>';
                }
                echo '</td>';
                echo '<td style="text-align:center; min-width: 100px">' . $this->printPossibleValues($attr) . '</td>';

                echo '<td style="text-align:center">';
                if ($this->isRequiredAttribute($attr)) {
                    echo '<span class="melicon-tag melicon-bg-success">' . __('YES', 'meliconnect') . '</span>';
                } else {
                    echo '<span class="melicon-tag melicon-bg-error">' . __('NO', 'meliconnect') . '</span>';
                }
                echo '</td>';

                echo '<td style="text-align:center">';
                if (isset($attr['tags']['catalog_required']) && $attr['tags']['catalog_required'] === true) {
                    echo '<span class="melicon-tag melicon-bg-success">' . __('YES', 'meliconnect') . '</span>';
                } else {
                    echo '<span class="melicon-tag melicon-bg-error">' . __('NO', 'meliconnect') . '</span>';
                }
                echo '</td>';

                echo '<td style="text-align:center">';
                if (isset($attr['tags']['allow_variations']) && $attr['tags']['allow_variations'] === true) {
                    echo '<span class="melicon-tag melicon-bg-success">' . __('YES', 'meliconnect') . '</span>';
                } else {
                    echo '<span class="melicon-tag melicon-bg-error">' . __('NO', 'meliconnect') . '</span>';
                }
                echo '</td>';

                echo '</tr>';
            }

            echo '</tbody>';
            echo '</table>';

            echo '</div>';
        }

        echo '</div>';
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

    public function printPossibleValues($attr)
    {
        $html = '';
        // Determinamos si es requerido por ser de tipo list o boolean
        $is_required = in_array($attr['value_type'], ['list', 'boolean']);

        switch ($attr['value_type']) {
            case 'list':
            case 'boolean':
                $html .= __('Value must be in list', 'meliconnect');
                break;

            case 'string':
                $html .= __('Any text', 'meliconnect');
                break;

            case 'number':
                $html .= __('Any number', 'meliconnect');
                break;

            case 'number_unit':
                if (isset($attr['allowed_units']) && is_array($attr['allowed_units']) && !empty($attr['allowed_units'])) {
                    $allowed_units = array_map('esc_html', array_column($attr['allowed_units'], 'name'));
                    $html .= __('Any number with units in: ', 'meliconnect') . '<br>' . implode(' | ', $allowed_units);
                } else {
                    $html .= __('Any number', 'meliconnect');
                }
                break;

            default:
                $html .= $attr['value_type'];
                break;
        }



        // Mostrar valores sugeridos si no es requerido pero tiene valores posibles
        if (isset($attr['values']) && is_array($attr['values']) && !empty($attr['values'])) {

            if ($is_required) {
                $label = '<b>' . __('Required values:', 'meliconnect') . '</b>';
            } else {
                $label = '<b>' . __('Suggested values:', 'meliconnect') . '</b>';
            }

            $html .= '<br>' . $label . '<br>';
            $values = array_column($attr['values'], 'name'); // Extraer los nombres de los valores
            $html .= implode(' | ', array_map('esc_html', $values));
        }

        return $html;
    }

    public function display_attribute_tags_info($attribute)
    {
        if (isset($attribute['tags']) && is_array($attribute['tags'])) {
            $tags = $attribute['tags'];

            // Variations are allowed for this attribute
            if (isset($tags['allow_variations']) && $tags['allow_variations'] === true) {
                echo '<p>' . __('Variations are allowed for this attribute.', 'meliconnect') . '</p>';
            }

            // It's required to identify the product the item represents
            if (isset($tags['catalog_required']) && $tags['catalog_required'] === true) {
                echo '<p>' . __('It\'s required to identify the product the item represents.', 'meliconnect') . '</p>';
            }

            // Conditional required
            if (isset($tags['conditional_required']) && $tags['conditional_required'] === true) {
                echo '<p>' . __('This attribute is required when certain conditions are met.', 'meliconnect') . '</p>';
            }

            // Defines the variation picture
            if (isset($tags['defines_picture']) && $tags['defines_picture'] === true) {
                echo '<p>' . __('This attribute defines the variation picture.', 'meliconnect') . '</p>';
            }

            // The value is fixed for this category
            if (isset($tags['fixed']) && $tags['fixed'] === true) {
                echo '<p>' . __('The value is fixed for this category. This value can\'t be changed.', 'meliconnect') . '</p>';
            }

            // Can be used as a filter in grid searches
            if (isset($tags['grid_filter']) && $tags['grid_filter'] === true) {
                echo '<p>' . __('This attribute can be used as a filter when searching on category existing grids.', 'meliconnect') . '</p>';
            }

            // Required for technical specifications in the grid
            if (isset($tags['grid_template_required']) && $tags['grid_template_required'] === true) {
                echo '<p>' . __('This attribute is required for technical specifications in the grid defined for the category.', 'meliconnect') . '</p>';
            }

            // Hidden for this category
            if (isset($tags['hidden']) && $tags['hidden'] === true) {
                echo '<p>' . __('This attribute is hidden for this category.', 'meliconnect') . '</p>';
            }

            // Inferred for this category
            if (isset($tags['inferred']) && $tags['inferred'] === true) {
                echo '<p>' . __('The value is inferred for this category.', 'meliconnect') . '</p>';
            }

            // More than one value can be assigned to this attribute
            if (isset($tags['multivalued']) && $tags['multivalued'] === true) {
                echo '<p>' . __('More than one value may be assigned to this attribute.', 'meliconnect') . '</p>';
            }

            // Required when the condition is new
            if (isset($tags['new_required']) && $tags['new_required'] === true) {
                echo '<p>' . __('This attribute is required when the item\'s condition is new.', 'meliconnect') . '</p>';
            }

            // Fixed or restricted for all the category's sisters
            if (isset($tags['others']) && $tags['others'] === true) {
                echo '<p>' . __('This attribute is fixed or restricted but not inferred for all the category\'s sisters.', 'meliconnect') . '</p>';
            }

            // Part of the product's primary key
            if (isset($tags['product_pk']) && $tags['product_pk'] === true) {
                echo '<p>' . __('This attribute is part of the primary key of the product and can identify a unique product in the catalog.', 'meliconnect') . '</p>';
            }

            // Read-only attribute for internal purposes
            if (isset($tags['read_only']) && $tags['read_only'] === true) {
                echo '<p>' . __('This attribute is read-only and is used for internal purposes only.', 'meliconnect') . '</p>';
            }

            // Required attribute
            if (isset($tags['required']) && $tags['required'] === true) {
                echo '<p>' . __('is a REQUIRED attribute for this category.', 'meliconnect') . '</p>';
            }

            // Restricted values for this category
            if (isset($tags['restricted_values']) && $tags['restricted_values'] === true) {
                echo '<p>' . __('The values are restricted for this category.', 'meliconnect') . '</p>';
            }

            // Attribute is used for variations
            if (isset($tags['variation_attribute']) && $tags['variation_attribute'] === true) {
                echo '<p>' . __('This attribute is an attribute of a variation.', 'meliconnect') . '</p>';
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
                    echo '<pre> No match:' . var_export($value['name'] , true) . '</pre>';
                } */
            }
        }

        return false;
    }


    public  function add_mercadolibre_product_tab($tabs)
    {
        $tabs['mercadolibre'] = array(
            'label'    => __('Mercadolibre', 'meliconnect'),
            'target'   => 'mercadolibre_product_data',
            'class'    => array('show_if_simple', 'show_if_variable'), // Mostrar para productos simples y variables
            'priority' => 60,
        );

        $tabs['meliconnect_logs'] = array(
            'label'    => __('Export Logs', 'meliconnect'),
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
        echo '<pre>' . var_export($this->template_data, true) . '</pre>';
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

        $error_time =  get_post_meta($this->woo_product_id, 'melicon_export_meli_error_time', true);

        if (!empty($error_time)) {
            $error_time = date('d-m-Y H:i:s', $error_time);
        }
        $logs_view_data = [
            'item_export_error'             => (isset($item_export_error['item']) && !empty($item_export_error['item'])) ? $item_export_error['item'] : '',
            'description_export_error'      => (isset($item_export_error['description']) && !empty($item_export_error['description'])) ? $item_export_error['description'] : '',
            'export_error_time'             => $error_time,
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
    public function add_meliconnect_meta_box()
    {
        $this->set_product_vars();

        add_meta_box(
            'meliconnect_meta_box', // ID del metabox
            __('Meliconnect', 'meliconnect'), // Título del metabox
            [$this, 'display_meliconnect_meta_box'], // Función para mostrar el contenido
            'product',
            'side', // Contexto, puedes usar 'normal', 'side' o 'advanced'
            'high' // Prioridad
        );
    }

    // Function to display the content of the metabox
    public function display_meliconnect_meta_box($post)
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

        Helper::load_partial('includes/Core/Views/Partials/meliconnect_meta_box.php', $box_view_data);
    }


    public static function handleImportSingleListing()
    {

        /* if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'import_single_listing')) {
            wp_send_json_error(__('Invalid nonce', 'meliconnect'));
            return;
        } */

        if (!current_user_can('meliconnect_manage_plugin')) {
            wp_send_json_error(__('You do not have permission to perform this action', 'meliconnect'));
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

        wp_send_json_success(__('Product imported successfully', 'meliconnect'));

        wp_die();
    }

    public static function handleExportSingleListing()
    {
        if (!current_user_can('meliconnect_manage_plugin')) {
            wp_send_json_error(__('You do not have permission to perform this action', 'meliconnect'));
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
                $item_message = __('Item published successfully.', 'meliconnect');
            } else {
                $item_message = $listing_response['item']['body'] ?? __('Item publication failed.', 'meliconnect');
            }

            // Description validation
            if (isset($listing_response['description']['success']) && $listing_response['description']['success'] == true) {
                $description_success = true;
                $description_message = __('Description published successfully.', 'meliconnect');
            } else {
                $description_message = $listing_response['description']['body'] ?? __('Description publication failed.', 'meliconnect');
            }

            // Handle different success or failure cases
            if ($item_success && $description_success) {
                // Both successful
                wp_send_json_success([
                    'message' => __('Export successful', 'meliconnect'),
                    'item_message' => $item_message,
                    'description_message' => $description_message
                ]);
            } elseif ($item_success && !$description_success) {
                // Item successful, description failed
                wp_send_json_error([
                    'message' => __('Item exported successfully, but there was an issue with the description.', 'meliconnect'),
                    'item_message' => $item_message,
                    'description_message' => $description_message
                ]);
            } elseif (!$item_success && $description_success) {
                // Description successful, item failed
                wp_send_json_error([
                    'message' => __('Description exported successfully, but there was an issue with the item.', 'meliconnect'),
                    'item_message' => $item_message,
                    'description_message' => $description_message
                ]);
            } else {
                // Both failed
                wp_send_json_error([
                    'message' => __('Error exporting item and description.', 'meliconnect'),
                    'item_message' => $item_message,
                    'description_message' => $description_message
                ]);
            }
        } else {
            // General error case
            wp_send_json_error([
                'message' => __('Error during export.', 'meliconnect'),
                'response' => $exportedResponse
            ]);
        }

        wp_die();
    }

    public static function handleUnlinkSingleListing()
    {
        // Verifica permisos
        if (!current_user_can('meliconnect_manage_plugin')) {
            wp_send_json_error(__('You do not have permission to perform this action', 'meliconnect'));
            wp_die();
        }

        // Sanitiza y valida los datos
        $woo_product_id = isset($_POST['woo_product_id']) ? sanitize_text_field($_POST['woo_product_id']) : null;
        $unlink_type = isset($_POST['unlink_type']) ? sanitize_text_field($_POST['unlink_type']) : null;

        if (empty($woo_product_id) || empty($unlink_type)) {
            wp_send_json_error(['message' => __('Invalid data', 'meliconnect')]);
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
            wp_send_json_success(['message' => __('Product desvinculated successfully', 'meliconnect')]);
        } else {
            wp_send_json_error(['message' => __('Failed to update product status', 'meliconnect')]);
        }

        wp_die();
    }

    public static function handleSaveTemplateData()
    {
        if (!current_user_can('meliconnect_manage_plugin')) {
            wp_send_json_error(__('You do not have permission to perform this action', 'meliconnect'));
            wp_die();
        }

        $templateDataJSON = isset($_POST['templateData']) ? $_POST['templateData'] : null;
        $woo_product_id = isset($_POST['woo_product_id']) ? sanitize_text_field($_POST['woo_product_id']) : null;
        $woo_product_title = isset($_POST['woo_product_title']) ? sanitize_text_field($_POST['woo_product_title']) : null;

        if (empty($templateDataJSON) || empty($woo_product_id) || empty($woo_product_title)) {
            wp_send_json_error(['message' => __('Invalid data', 'meliconnect')]);
            wp_die();
        }

        $template_data = json_decode(stripslashes($templateDataJSON), true);

        if (json_last_error() === JSON_ERROR_NONE) {

            // Transforma las claves en un array multidimensional
            $parsed_template_data = [];
            parse_str(http_build_query($template_data), $parsed_template_data);


            $template_id = Template::createUpdateProductTemplateFromPost($parsed_template_data['template'], $woo_product_id, $woo_product_title);

            if (empty($template_id)) {
                wp_send_json_error(['message' => __('Failed to save template', 'meliconnect')]);
                wp_die();
            }

            wp_send_json_success(['message' => __('Template saved successfully', 'meliconnect')]);
        } else {
            wp_send_json_error(['message' => __('Failed to decode Template Data: ' . json_last_error_msg(), 'meliconnect')]);
        }

        wp_die();
    }
}
