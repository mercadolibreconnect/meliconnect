<?php

namespace StoreSync\Meliconnect\Core\Helpers;

use StoreSync\Meliconnect\Core\Models\Template;
use StoreSync\Meliconnect\Core\Models\UserConnection;

class Helper
{

    private static $optionPrefix = 'meliconnect_';

    public static function getOption($optionName, $default = null)
    {
        return get_option(self::$optionPrefix . $optionName, $default);
    }

    public static function setOption($optionName, $optionValue, $autoLoad = null)
    {
        return update_option(self::$optionPrefix . $optionName, $optionValue, $autoLoad);
    }

    public static function deleteOption($optionName)
    {
        return delete_option(self::$optionPrefix . $optionName);
    }

    public static function meliconnectPrintTag($name, $class)
    {
        return '<span class="melicon-tag tag ' . $class . '"> ' . $name . '</span>';
    }

    public static function meliconnectPrintColorText($name, $class = 'has-text-info')
    {
        return '<span class="melicon-color-text ' . $class . '"> ' . $name . '</span>';
    }

    /**
     * Method printMessageBox
     * Generates HTML for a message box to display information based on the parameters provided.
     * 
     * @param string $text The text to be displayed in the message box.
     * @param string $alert Optional. The type of alert for the message box (e.g., 'is-info', 'is-warning', etc.). Defaults to 'is-info'.
     * @param bool $canDelete Optional. Whether a delete button should be included in the message box. Defaults to false.
     *
     * @return string HTML markup for the message box.
     */
    public static function printMessageBox($text, $alert = 'is-info', $canDelete = false)
    {
        // Escape the text to prevent XSS attacks
        $escapedText = esc_html($text);
        $alertClass = esc_attr($alert);

        if (empty($escapedText)) {
            return '';
        }

        $deleteButton = $canDelete ? '<button class="delete"></button>' : '';

        $messageBox = sprintf(
            '<div class="notification %s">%s%s</div>',
            $alertClass,
            $deleteButton,
            $escapedText
        );

        return $messageBox;
    }

    public static function getMeliconnectOptions($type = 'all')
    {
        global $wpdb;

        $options = [];

        switch ($type) {
            case 'general':
                $prefix = 'melicon_general';
                break;
            case 'export':
                $prefix = 'melicon_export';
                break;
            case 'import':
                $prefix = 'melicon_import';
                break;
            case 'sync':
                $prefix = 'melicon_sync';
                break;
            default:
                $prefix = 'melicon_';
                break;
        }

        if ($type === 'all') {
            // Recuperar todas las opciones que comienzan con 'melicon_'
            $query = "SELECT option_name, option_value FROM {$wpdb->options} WHERE option_name LIKE 'melicon_%'";
        } else {
            // Recuperar las opciones específicas según el prefijo
            $query = $wpdb->prepare("SELECT option_name, option_value FROM {$wpdb->options} WHERE option_name LIKE %s", $wpdb->esc_like($prefix) . '%');
        }

        $results = $wpdb->get_results($query, ARRAY_A);

        if ($results) {
            foreach ($results as $row) {
                $options[$row['option_name']] = maybe_unserialize($row['option_value']);
            }
        }

        // Agregar 'melicon_general_sync_type' si el tipo es 'export' o 'import'
        if ($type === 'export' || $type === 'import') {
            $general_sync_type = get_option('melicon_general_sync_type');
            $options['melicon_general_sync_type'] = maybe_unserialize($general_sync_type);
        }

        return $options;
    }

    public static function getSellersIdsAndNames()
    {
        // Obtener los usuarios vendedores
        $sellers = UserConnection::getUser();

        // Inicializar array de sellers
        $sellersList = [];

        // Verificar si hay vendedores disponibles
        if (!empty($sellers) && is_array($sellers)) {
            // Recorrer cada vendedor y agregar al array
            foreach ($sellers as $seller) {
                // Asegurarse de que las propiedades existan antes de usarlas
                if (isset($seller->user_id) && isset($seller->nickname)) {
                    $sellersList[$seller->user_id] = $seller->nickname;
                }
            }
        }

        return $sellersList;
    }

    public static function getSellersSelect($selectName = 'user_id', $addAll = false, $default = null)
    {
        $sellers = UserConnection::getUser();

        $output = '';

        if (empty($sellers)) {
            $output .= '<p>' . __('Please connect a user to your account.', 'meliconnect') . '</p>';
        } elseif (count($sellers) === 1) {
            $seller = $sellers[0];
            $output .= '<p>' . esc_html($seller->nickname) . '</p>';
            $output .= '<input type="hidden" name="' . esc_attr($selectName) . '" value="' . esc_attr($seller->user_id) . '">';
        } else {
            $output .= '<div class="melicon-control control">';
            $output .= '<div class="melicon-select select">';
            $output .= '<select name="' . esc_attr($selectName) . '">';

            if ($addAll) {
                $output .= '<option value="all"' . selected($default, 'all', false) . '>' . __('All Sellers', 'meliconnect') . '</option>';
            }

            foreach ($sellers as $seller) {
                $output .= '<option value="' . esc_attr($seller->user_id) . '"'
                    . selected($default, $seller->user_id, false) . '>'
                    . esc_html($seller->nickname) . '</option>';
            }

            $output .= '</select>';
            $output .= '</div>';
            $output .= '</div>';
        }

        return $output;
    }

    public static function logData($data, $log_name)
    {
        $logger = new \WC_Logger();
        $context = array('source' => 'melicon-' . $log_name);

        if (is_array($data) || is_object($data)) {
            $data = json_encode($data, JSON_PRETTY_PRINT);
        }

        $logger->info($data, $context);
    }

    public static function getProductsWithMeta($meta_key)
    {
        $args = array(
            'post_type'  => 'product',
            'post_status' => 'publish',
            'meta_key'   => $meta_key,  // Meta key to filter
            'meta_compare' => 'EXISTS', // Ensures that the meta key exists
            'fields'     => 'ids',     // Retrieve only product IDs
            'posts_per_page' => -1
        );

        $query = new \WP_Query($args);
        return $query->posts; // Returns an array of product IDs
    }

    public static function getPostsWithMetaArray($meta_key)
    {
        global $wpdb;

        // Preparar la consulta
        $query = $wpdb->prepare(
            "SELECT post_id, meta_value 
             FROM {$wpdb->postmeta} 
             WHERE meta_key = %s",
            $meta_key
        );

        // Ejecutar la consulta y obtener los resultados
        $results = $wpdb->get_results($query, ARRAY_A);

        // Procesar los resultados
        $formatted_results = array();
        foreach ($results as $row) {
            $formatted_results[$row['meta_value']] = $row['post_id'];
        }

        return $formatted_results;
    }

    public static function getPostByMeta($meta_key, $meta_value)
    {
        global $wpdb;

        $post_id = $wpdb->get_var($wpdb->prepare("
            SELECT post_id
            FROM $wpdb->postmeta
            WHERE meta_key = %s
            AND meta_value = %s
            LIMIT 1
        ", $meta_key, $meta_value));

        if ($post_id) {
            return get_post($post_id);
        }

        return null;
    }


    public static function unlinkProduct($woo_product_id)
    {
        // Delete product template
        /* $template_id = get_post_meta($woo_product_id, 'melicon_asoc_template_id', true); */


        /* if (!empty($template_id)) {
            //Delete template, template metas and template attributes
            Template::deleteTemplate($template_id);
        } */

        // Lista de metakeys a eliminar
        $meta_keys = [
            'melicon_meli_listing_id',
            'melicon_meli_permalink',
            'melicon_meli_listing_type_id',
            /* 'melicon_meli_category_id', */
            'melicon_meli_status',
            'melicon_meli_sub_status',
            'melicon_meli_site_id',
            'melicon_meli_catalog_product_id',
            'melicon_meli_domain_id',
            'melicon_meli_channels',
            'melicon_meli_sold_quantity',
            'melicon_meli_shipping_mode',
            /* 'melicon_asoc_template_id' */
            /* 'melicon_meli_seller_id', */
        ];

        foreach ($meta_keys as $meta_key) {
            delete_post_meta($woo_product_id, $meta_key);
        }

        return true;
    }

    public static function change_woo_product_status($product_id, $status = 'draft')
    {

        if (empty($product_id) || !is_numeric($product_id)) {
            return false;
        }

        $post_data = array(
            'ID'            => $product_id,
            'post_status'   => $status
        );

        $result = wp_update_post($post_data, true);

        if (is_wp_error($result)) {
            error_log('Error al cambiar el estado del producto ' . $product_id . ' a borrador: ' . $result->get_error_message());
            return false;
        }

        return true;
    }


    public static function get_product_by_name($name)
    {
        $args = [
            'post_type' => 'product',
            'title' => trim($name),
            'posts_per_page' => 1,
            'post_status' => 'publish',
        ];

        $products = get_posts($args);

        return !empty($products) ? $products[0] : null;
    }

    public static function get_product_by_sku($sku)
    {
        global $wpdb;

        $product_id = $wpdb->get_var($wpdb->prepare("
            SELECT pm.post_id
            FROM {$wpdb->postmeta} pm
            JOIN {$wpdb->posts} p ON p.ID = pm.post_id
            WHERE pm.meta_key = '_sku' AND pm.meta_value = %s AND p.post_type = 'product' AND p.post_status = 'publish'
            LIMIT 1
        ", $sku));

        return $product_id ? get_post($product_id) : null;
    }

    public static function get_woo_active_products()
    {
        global $wpdb;

        // Consulta para obtener los productos y sus metadatos
        $products = $wpdb->get_results("
        SELECT 
            p.ID AS product_id,
            p.post_title AS product_name,
            pm_sku.meta_value AS sku,
            pm_gtin.meta_value AS gtin,
            pm_asoc_template.meta_value AS vinculated_template_id,
            pm_asoc_listing.meta_value AS vinculated_listing_id,
            pm_listing_permalink.meta_value AS meli_permalink,
            pm_listing_seller_id.meta_value AS meli_seller_id,
            p.post_status AS status
        FROM {$wpdb->posts} p
        LEFT JOIN {$wpdb->postmeta} pm_sku ON p.ID = pm_sku.post_id AND pm_sku.meta_key = '_sku'
        LEFT JOIN {$wpdb->postmeta} pm_gtin ON p.ID = pm_gtin.post_id AND pm_gtin.meta_key = '_global_unique_id'
        LEFT JOIN {$wpdb->postmeta} pm_asoc_template ON p.ID = pm_asoc_template.post_id AND pm_asoc_template.meta_key = 'melicon_asoc_template_id'
        LEFT JOIN {$wpdb->postmeta} pm_asoc_listing ON p.ID = pm_asoc_listing.post_id AND pm_asoc_listing.meta_key = 'melicon_meli_listing_id'
        LEFT JOIN {$wpdb->postmeta} pm_listing_permalink ON p.ID = pm_listing_permalink.post_id AND pm_listing_permalink.meta_key = 'melicon_meli_permalink'
        LEFT JOIN {$wpdb->postmeta} pm_listing_seller_id ON p.ID = pm_listing_seller_id.post_id AND pm_listing_seller_id.meta_key = 'melicon_meli_seller_id'
        WHERE p.post_type = 'product'
        AND p.post_status = 'publish'
    ", ARRAY_A);

        // Obtener IDs de todos los productos para comprobar variaciones
        $product_ids = array_column($products, 'product_id');

        if (empty($product_ids)) {
            return $products;
        }

        // Consulta para obtener los IDs de las variaciones
        $variations = $wpdb->get_col("
        SELECT DISTINCT post_parent 
        FROM {$wpdb->posts}
        WHERE post_type = 'product_variation'
        AND post_parent IN (" . implode(',', array_map('absint', $product_ids)) . ")
    ");

        $variations = array_map('absint', $variations);
        $variations = array_flip($variations);

        // Añadir el tipo de producto (variable o simple)
        foreach ($products as &$product) {
            $product_id = $product['product_id'];
            if (isset($variations[$product_id])) {
                $product['product_type'] = 'variable';
            } else {
                $product['product_type'] = 'simple';
            }
        }

        return $products;
    }

    public static function normalizeString($string)
    {
        // Convertir a minúsculas
        $string = strtolower($string);

        // Reemplazar caracteres acentuados
        $string = str_replace(
            ['á', 'é', 'í', 'ó', 'ú', 'ñ'],
            ['a', 'e', 'i', 'o', 'u', 'n'],
            $string
        );

        // Reemplazar espacios y guiones por una cadena vacía
        $string = str_replace([' '], '-', $string);

        return $string;
    }


    public static function get_woo_active_products_count()
    {
        global $wpdb;

        $count = $wpdb->get_var("
        SELECT COUNT(*)
        FROM {$wpdb->prefix}posts
        WHERE post_type = 'product'
        AND post_status = 'publish'
    ");

        return (int) $count;
    }

    public static function change_meli_listing_status($product_id, $status)
    {
        // Obtener IDs asociados
        $asoc_listing = get_post_meta($product_id, 'melicon_meli_listing_id', true);
        $seller_id = get_post_meta($product_id, 'melicon_meli_seller_id', true);

        // Validar si los datos requeridos están presentes
        if (!$asoc_listing || !$seller_id) {
            error_log("Error changing product {$product_id} status: 'asoc_listing' or 'seller_id' not found.");
            return false;
        }

        // Obtener datos del vendedor
        $seller_data = UserConnection::getUser($seller_id);
        if (!$seller_data) {
            error_log("Error changing product {$product_id} status: Seller data not found for seller_id {$seller_id}.");
            return false;
        }

        // Crear instancia de MeliconMeli
        $meli = new MeliconMeli($seller_data->app_id, $seller_data->secret_key, $seller_data->access_token);

        // Actualizar el estado del producto en MercadoLibre
        $response = $meli->put("/items/{$asoc_listing}", ['status' => $status], ['access_token' => $seller_data->access_token]);

        // Verificar el código de respuesta HTTP
        if ($response['httpCode'] !== 200) {
            error_log("Error changing status of Listing {$asoc_listing} for product {$product_id}: {$response['body']}");
            return false;
        }

        // Verificar si el estado se actualizó correctamente
        if (isset($response['body']->status) && $response['body']->status === $status) {
            return $response['body'];
        }

        error_log("Unexpected error: Listing {$asoc_listing} status was not correctly updated for product {$product_id}.");
        return false;
    }

    public static function load_partial($template_path, $data = [])
    {

        if (file_exists(MC_PLUGIN_ROOT . $template_path)) {

            extract($data); // Extract variables for use in the template
            include_once(MC_PLUGIN_ROOT . $template_path);
        } else {
            echo __('View not found.', 'meliconnect');
        }
    }

    public static function handleLoadMeliCategories()
    {
        $category_id = isset($_POST['category_id']) ? sanitize_text_field($_POST['category_id']) : null;
        $seller_id = isset($_POST['seller_id']) ? sanitize_text_field($_POST['seller_id']) : null;

        if (empty($seller_id)) {
            wp_send_json_error(['message' => 'Seller ID is required']);
            wp_die();
        }

        $seller_data = UserConnection::getUser($seller_id);

        if (!$seller_data) {
            wp_send_json_error(['message' => 'Seller data not found']);
            wp_die();
        }

        $meli = new MeliconMeli($seller_data->app_id, $seller_data->secret_key, $seller_data->access_token);
        $categories = [];
        $path_from_root = [];
        $category_name = '';

        if (empty($category_id)) {
            $response = $meli->get('/sites/' . $seller_data->site_id . '/categories', ['access_token' => $seller_data->access_token]);
            $categories = $response['body'];
        } else {
            $response = $meli->get('/categories/' . $category_id, ['access_token' => $seller_data->access_token]);
            if (isset($response['body']->children_categories) && !empty($response['body']->children_categories)) {
                $categories = $response['body']->children_categories;
            }
            if (isset($response['body']->path_from_root) && !empty($response['body']->path_from_root)) {
                $path_from_root = $response['body']->path_from_root;
            }

            $category_name = $response['body']->name;
        }

        if ($response['httpCode'] !== 200 || empty($response['body'])) {
            wp_send_json_error(['message' => 'Error fetching categories', 'response' => $response]);
            wp_die();
        }

        // Generar opciones para el select
        if (!empty($categories)) {
            $options = '<option value="">' . __('Select a category', 'meliconnect') . '</option>';
            foreach ($categories as $category) {
                $options .= '<option value="' . esc_attr($category->id) . '">' . esc_html($category->name) . '</option>';
            }
        } else {
            $options = NULL;
        }


        // Generar HTML para path_from_root
        $path_from_route_html = '<p class="melicon-category-path">';


        $path_from_route_html .= '<span class="melicon-selected-category-span">' . __('Category Root') . ': </span>';

        foreach ($path_from_root as $key => $parent) {
            if ($key > 0) {
                $path_from_route_html .= ' > ';
            }
            $path_from_route_html .= '<a href="" class="melicon-category-link" data-category-id="' . esc_attr($parent->id) . '">' . esc_html($parent->name) . '</a>';
        }

        if (!empty($path_from_root)) {
            $path_from_route_html .= '<a href="" class="melicon-category-link" data-category-id="0"><i class="fa fa-times melicon-ml-2" aria-hidden="true"></i></a>';
        }

        $path_from_route_html .= '</p>';

        // Devolver ambos valores como JSON
        wp_send_json_success([
            'options' => $options,
            'path_from_route_html' => $path_from_route_html,
            'path_from_route_json' => $path_from_root,
            'category_name' => $category_name
        ]);

        wp_die();
    }

    public static function handleUpdateMeliCategory()
    {
        $category_id = isset($_POST['category_id']) ? sanitize_text_field($_POST['category_id']) : null;
        $woo_product_id = isset($_POST['woo_product_id']) ? sanitize_text_field($_POST['woo_product_id']) : null;
        $product_title = isset($_POST['product_title']) ? sanitize_text_field($_POST['product_title']) : null;
        $seller_meli_id = isset($_POST['seller_meli_id']) ? sanitize_text_field($_POST['seller_meli_id']) : null;
        $category_tree = isset($_POST['category_tree']) ? sanitize_text_field($_POST['category_tree']) : null;
        $category_name = isset($_POST['category_name']) ? sanitize_text_field($_POST['category_name']) : null;

        if (empty($category_id) || empty($woo_product_id)) {
            wp_send_json_error(['message' => 'Category ID or product ID is required']);
            wp_die();
        }

        $template_id = get_post_meta($woo_product_id, 'melicon_asoc_template_id', true);

        if (empty($template_id)) {

            $template_data = [
                'used_by'            => 'product',
                'used_asoc_id'       => $woo_product_id,
                'seller_meli_id'     => $seller_meli_id,
                'name'               => $product_title,
                'short_description'  => 'Template created on edit product page',
                'category_id'        => $category_id,
                'category_name'      => $category_name,
                'category_path'      => $category_tree,
                'status'             => 1,
            ];

            $template_id = Template::createUpdateTemplate($template_data);

            if (!$template_id) {
                wp_send_json_error(['message' => 'Error creating template']);
                wp_die();
            }

            update_post_meta($woo_product_id, 'melicon_asoc_template_id', $template_id);
            update_post_meta($woo_product_id, 'melicon_meli_seller_id', $seller_meli_id);
            update_post_meta($woo_product_id, 'melicon_meli_category_id', $category_id);

            wp_send_json_success(['message' => 'Template created successfully', 'template_id' => $template_id]);
            wp_die();
        }

        $template_data = Template::deleteCategoryRelatedDataFromTemplate($template_id);

        if ($template_data) {
            Template::updateTemplateData($template_id, ['category_id' => $category_id]);

            wp_send_json_success(['message' => 'Category updated successfully']);
        }
    }
}
