<?php

namespace Meliconnect\Meliconnect\Modules\Importer\Controllers;

if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

use Meliconnect\Meliconnect\Core\Helpers\Helper;
use Meliconnect\Meliconnect\Core\Helpers\MeliconMeli;
use Meliconnect\Meliconnect\Core\Interfaces\ControllerInterface;
use Meliconnect\Meliconnect\Core\Models\Process;
use Meliconnect\Meliconnect\Core\Models\UserConnection;
use Meliconnect\Meliconnect\Modules\Importer\Models\UserListingToImport;

class ImportController implements ControllerInterface
{


    public function __construct() {}

    public function getData()
    {
        // Logic to get and return data
        $data = [];
        $process = Process::getCurrentProcessData('custom-import');
        $process_finished = Process::getCurrentProcessData('custom-import', ['finished']);

        $data = [
            'import_process_finished' => $process_finished,
            'import_process_data' => $process,
            'execution_time' => Process::calculateExecutionTime($process),
            'meli_user_listings_to_import_count' => UserListingToImport::get_user_listings_count(),
            'meli_user_listings_active_to_import_count' => UserListingToImport::get_user_listings_count_by_status('active'),
            'woo_total_active_products' => Helper::get_woo_active_products_count(),
            'woo_total_vinculated_products' => count(Helper::getProductsWithMeta('meliconnect_meli_listing_id')),
        ];

        return $data;
    }




    /* START HANDLE AJAX METHODS */

    public static function handleClearSelectedProductsMatch()
    {

        // Verificar nonce
        if (! isset($_POST['nonce']) || ! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'clear_selected_matches_nonce')) {
            wp_send_json_error(esc_html__('Invalid nonce', 'meliconnect'));
            return;
        }

        // Verificar permisos
        if (! current_user_can('meliconnect_manage_plugin')) {
            wp_send_json_error(esc_html__('You do not have permission to perform this action', 'meliconnect'));
            return;
        }

        // Obtener IDs seleccionados y sanitizar
        $meli_listings_ids = isset($_POST['meli_listings_ids'])
            ? array_map('sanitize_text_field', wp_unslash($_POST['meli_listings_ids']))
            : null;

        if (! $meli_listings_ids || ! is_array($meli_listings_ids)) {
            wp_send_json_error(esc_html__('Invalid data', 'meliconnect'));
            return;
        }

        // Limpiar matches
        $cleared = UserListingToImport::clear_matches($meli_listings_ids);

        if ($cleared) {
            wp_send_json_success();
        } else {
            wp_send_json_error(esc_html__('Something went wrong', 'meliconnect'));
        }
    }


    public static function handleApplyMatch()
    {
        if (! isset($_POST['nonce']) || ! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'apply_match_nonce')) {
            wp_send_json_error(esc_html__('Invalid nonce', 'meliconnect'));
            return;
        }

        if (! current_user_can('meliconnect_manage_plugin')) {
            wp_send_json_error(esc_html__('You do not have permission to perform this action', 'meliconnect'));
            return;
        }

        $user_listing_id = isset($_POST['user_listing_id']) ? sanitize_text_field(wp_unslash($_POST['user_listing_id'])) : null;
        $woo_product_id  = isset($_POST['woo_product_id']) ? sanitize_text_field(wp_unslash($_POST['woo_product_id'])) : null;

        if (! $user_listing_id || ! $woo_product_id) {
            wp_send_json_error(esc_html__('Invalid data', 'meliconnect'));
            return;
        }

        $applied_match = UserListingToImport::update_vinculated_product($user_listing_id, $woo_product_id, 'manual');

        if ($applied_match) {
            wp_send_json_success();
        } else {
            wp_send_json_error(esc_html__('Error applying match', 'meliconnect'));
        }
    }

    public static function handleMatchListingsWithProducts()
    {

        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'match_listings_with_products_nonce')) {
            wp_send_json_error(esc_html__('Invalid nonce', 'meliconnect'));
            return;
        }

        if (!current_user_can('meliconnect_manage_plugin')) {
            wp_send_json_error(esc_html__('You do not have permission to perform this action', 'meliconnect'));
            return;
        }

        Helper::logData('------ START Match listings with products ------', 'import');

        $match_by = isset($_POST['match_by']) ? sanitize_text_field(wp_unslash($_POST['match_by'])) : 'name';

        Helper::logData('Match by: ' . $match_by . '', 'import');


        $user_listings_to_import = UserListingToImport::get_not_vinculated_user_listings_to_import();


        if (!$user_listings_to_import) {
            Helper::logData('No listings to import with pending vinculation', 'import');
            Helper::logData('------ END Match listings with products ------', 'import');
            wp_send_json_error(esc_html__('No listings to import with pending vinculation', 'meliconnect'));
            return;
        }
        //Helper::logData('Listings to import: ' . wp_json_encode($user_listings_to_import) , 'import');

        foreach ($user_listings_to_import as $user_listing) {
            $product = null;

            if ($match_by == 'name') {
                Helper::logData('NAME to find: ' . $user_listing->meli_listing_title . '', 'import');
                $product = Helper::get_product_by_name($user_listing->meli_listing_title);
            }

            if ($match_by == 'sku') {
                Helper::logData('SKU to find: ' . $user_listing->meli_sku . '', 'import');
                $product = Helper::get_product_by_sku($user_listing->meli_sku);
            }


            if ($product) {
                Helper::logData('Product found: ' . $product->post_title . '', 'import');

                UserListingToImport::update_vinculated_product($user_listing->id, $product->ID, $match_by);
            } else {
                Helper::logData('Product NOT found by name or sku.', 'import');
            }
        }
        Helper::logData('------ END Match listings with products ------', 'import');
        wp_send_json_success(esc_html__('Listings matched with products', 'meliconnect'));
    }

    public static function handleClearMatches()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'clear_all_matches_nonce')) {
            wp_send_json_error(esc_html__('Invalid nonce', 'meliconnect'));
            return;
        }

        if (!current_user_can('meliconnect_manage_plugin')) {
            wp_send_json_error(esc_html__('You do not have permission to perform this action', 'meliconnect'));
            return;
        }

        $clear = UserListingToImport::clear_matches();

        if (!$clear) {
            wp_send_json_error(esc_html__('Problem clearing matches', 'meliconnect'));
            return;
        }

        wp_send_json_success(esc_html__('Matches cleared', 'meliconnect'));
    }

    public static function handleImportProcess()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'get_process_progress_nonce')) {
            wp_send_json_error(esc_html__('Invalid nonce', 'meliconnect'));
            return;
        }

        $process_id = isset($_POST['process_id']) ? sanitize_text_field(wp_unslash($_POST['process_id'])) : null;

        if (! $process_id) {
            wp_send_json_error(esc_html__('Invalid data', 'meliconnect'));
            return;
        }

        $progress_data = Process::getProcessProgress($process_id);

        if (!$progress_data) {
            wp_send_json_error(esc_html__('Process not found', 'meliconnect'));
            return;
        }


        wp_send_json_success($progress_data);
    }

    public static function handleGetMeliUserListings()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'get_meli_user_listings_nonce')) {
            wp_send_json_error(esc_html__('Invalid nonce', 'meliconnect'));
            return;
        }

        // Verifica los permisos del usuario
        if (!current_user_can('meliconnect_manage_plugin')) {
            wp_send_json_error(esc_html__('You do not have permission to perform this action', 'meliconnect'));
            return;
        }

        // Obtiene los datos enviados por AJAX
        $data = $_POST;

        // Verifica si la solicitud está vacía
        if (!isset($data['user_id'])) {
            wp_send_json_error(esc_html__('Invalid request data', 'meliconnect'));
            return;
        }

        $meli_user = UserConnection::getUser($data['user_id']);

        if (!$meli_user) {
            wp_send_json_error(esc_html__('User not found', 'meliconnect'));
            return;
        }

        $meli_user_listings_ids = self::getMeliUserLisntingIds($meli_user);

        if ($meli_user_listings_ids === false) {
            wp_send_json_error(esc_html__('There was an error getting the user listings. Check connections page.', 'meliconnect'));
            return;
        }

        UserListingToImport::create_or_skip_meli_user_listings_ids_to_import($meli_user, $meli_user_listings_ids);

        //Adds extra data to imported listings
        $listings_extra_data = self::multiGetListingsData($meli_user, $meli_user_listings_ids);

        UserListingToImport::update_meli_user_listings_extra_data_to_import($listings_extra_data);

        /* echo PHP_EOL . '-------------------- listings_extra_data --------------------' . PHP_EOL;
        echo '<pre>' . wp_json_encode( $listings_extra_data) . '</pre>';
        echo PHP_EOL . '-------------------  FINISHED  ---------------------' . PHP_EOL; */
    }

    public static function handleResetMeliUserListings()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'reset_listings_nonce')) {
            wp_send_json_error(esc_html__('Invalid nonce', 'meliconnect'));
            return;
        }

        // Verifica los permisos del usuario
        if (!current_user_can('meliconnect_manage_plugin')) {
            wp_send_json_error(esc_html__('You do not have permission to perform this action', 'meliconnect'));
            return;
        }

        UserListingToImport::reset_meli_user_listings();

        wp_send_json_success();
    }

    public static function handleInitImportProcess()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'init_import_process_nonce')) {
            wp_send_json_error(esc_html__('Invalid nonce', 'meliconnect'));
            return;
        }

        // Verifica los permisos del usuario
        if (!current_user_can('meliconnect_manage_plugin')) {
            wp_send_json_error(esc_html__('You do not have permission to perform this action', 'meliconnect'));
            return;
        }


        self::initImportProcess();

        wp_send_json_success();
    }

    public static function handlePauseCustomImport()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'pause_custom_import_nonce')) {
            wp_send_json_error(esc_html__('Invalid nonce', 'meliconnect'));
            return;
        }

        // Verifica los permisos del usuario
        if (!current_user_can('meliconnect_manage_plugin')) {
            wp_send_json_error(esc_html__('You do not have permission to perform this action', 'meliconnect'));
            return;
        }

        //UserListingToImport::pause_import_process();

        wp_send_json_success();
    }

    public static function handleCancelCustomImport()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'cancel_custom_import_nonce')) {
            wp_send_json_error(esc_html__('Invalid nonce', 'meliconnect'));
            return;
        }

        // Verifica los permisos del usuario
        if (!current_user_can('meliconnect_manage_plugin')) {
            wp_send_json_error(esc_html__('You do not have permission to perform this action', 'meliconnect'));
            return;
        }

        $process_id = isset($_POST['process_id']) ? sanitize_text_field(wp_unslash($_POST['process_id'])) : null;

        if (! $process_id) {
            wp_send_json_error(esc_html__('Invalid data', 'meliconnect'));
            return;
        }

        //UserListingToImport::cancel_import_process();

        update_option('custom_import_cancel_requested', true);

        sleep(2);

        Process::cancelProcess($process_id);

        //unlock custom import process cron
        delete_option('meliconnect_custom_import_lock');

        wp_send_json_success();
    }

    public static function handleCancelFinishedProcesses()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'cancel_finished_processes_nonce')) {
            wp_send_json_error(esc_html__('Invalid nonce', 'meliconnect'));
            return;
        }

        // Verifica los permisos del usuario
        if (!current_user_can('meliconnect_manage_plugin')) {
            wp_send_json_error(esc_html__('You do not have permission to perform this action', 'meliconnect'));
            return;
        }

        $deleted = Process::cancelFinishedProcesses('custom-import');

        if (!$deleted) {
            wp_send_json_error();
        }

        //unlock custom import process cron
        delete_option('meliconnect_custom_import_lock');

        wp_send_json_success();
    }

    public static function handleBulkImportAction()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'import_bulk_action_nonce')) {
            Helper::logData('Invalid nonce', 'bulk-actions-import');
            wp_send_json_error(esc_html__('Invalid nonce', 'meliconnect'));
            return;
        }

        // Verifica los permisos del usuario
        if (!current_user_can('meliconnect_manage_plugin')) {
            Helper::logData('Permission denied', 'bulk-actions-import');
            wp_send_json_error(esc_html__('You do not have permission to perform this action', 'meliconnect'));
            return;
        }

        $selected_ids = isset($_POST['meli_listing_ids']) ? sanitize_text_field(wp_unslash($_POST['meli_listing_ids'])) : '';
        $action = isset($_POST['action_to_do']) ? sanitize_text_field(wp_unslash($_POST['action_to_do'])) : '';

        if (!$selected_ids || !$action || $action == -1) {
            Helper::logData('Invalid data: missing selected_ids or action', 'bulk-actions-import');
            wp_send_json_error(esc_html__('Invalid data', 'meliconnect'));
        }

        $selected_ids_arr = explode(',', $selected_ids);
        Helper::logData('Processing action: ' . $action, 'bulk-actions-import');
        Helper::logData('Selected IDs: ' . implode(',', $selected_ids_arr), 'bulk-actions-import');

        switch ($action) {
            case 'import-selected':
                Helper::logData('Initiating import process', 'bulk-actions-import');
                self::initImportProcess($selected_ids_arr);
                break;

            case 'match-items-products-by-name':
            case 'match-items-products-by-sku':
                Helper::logData('Matching items by ' . ($action == 'match-items-products-by-name' ? 'name' : 'SKU'), 'bulk-actions-import');
                $selected_user_listings_to_import = UserListingToImport::get_user_listings_to_import($selected_ids_arr);

                foreach ($selected_user_listings_to_import as $user_listing) {
                    $product = ($action == 'match-items-products-by-name')
                        ? Helper::get_product_by_name($user_listing->meli_listing_title)
                        : Helper::get_product_by_sku($user_listing->meli_sku);

                    if ($product) {
                        Helper::logData("Product matched: {$product->ID} for listing {$user_listing->id}", 'bulk-actions-import');
                        UserListingToImport::update_vinculated_product($user_listing->id, $product->ID, $action == 'match-items-products-by-name' ? 'name' : 'sku');
                    } else {
                        Helper::logData("No product matched for listing {$user_listing->id}", 'bulk-actions-import');
                    }
                }
                break;

            case 'desvinculate-items-products':
            case 'desvinculate-items-and-delete':
                $deleting = ($action == 'desvinculate-items-and-delete');
                Helper::logData('Unlinking items, deleting: ' . ($deleting ? 'yes' : 'no'), 'bulk-actions-import');

                foreach ($selected_ids_arr as $meli_listing_id) {
                    $meliListingdata = UserListingToImport::get_user_listing_by_listing_id($meli_listing_id);

                    if (!isset($meliListingdata->vinculated_product_id) || empty($meliListingdata->vinculated_product_id)) {
                        Helper::logData("No vinculated product found for listing {$meli_listing_id}", 'bulk-actions-import');
                        continue;
                    }

                    Helper::unlinkProduct($meliListingdata->vinculated_product_id);
                    UserListingToImport::unlink_woo_product($meli_listing_id);

                    if ($deleting) {
                        Helper::logData("Setting product {$meliListingdata->vinculated_product_id} to trash", 'bulk-actions-import');
                        Helper::change_woo_product_status($meliListingdata->vinculated_product_id, 'trash');
                    }
                }
                break;

            default:
                Helper::logData('Unknown action', 'bulk-actions-import');
                break;
        }

        wp_send_json_success(array('message' => 'Bulk action processed successfully.'));
    }


    public static function handleDesvinculateWooProduct()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'desvinculate_product_nonce')) {
            wp_send_json_error(esc_html__('Invalid nonce', 'meliconnect'));
            return;
        }

        // Verifica los permisos del usuario
        if (!current_user_can('meliconnect_manage_plugin')) {
            wp_send_json_error(esc_html__('You do not have permission to perform this action', 'meliconnect'));
            return;
        }

        $wooProductId = isset($_POST['wooProductId']) ? sanitize_text_field(wp_unslash($_POST['wooProductId'])) : '';
        $meliListingId = isset($_POST['meliListingId']) ? sanitize_text_field(wp_unslash($_POST['meliListingId'])) : '';

        if (!$wooProductId || !$meliListingId) {
            wp_send_json_error(esc_html__('Invalid data', 'meliconnect'));
        }

        Helper::unlinkProduct($wooProductId);

        if (!empty($meliListingId)) {
            UserListingToImport::unlink_woo_product($meliListingId);
        }

        wp_send_json_success(array('message' => esc_html__('Product desvinculated successfully.', 'meliconnect')));
    }

    public static function handleGetMatchAvailableProducts()
    {

        if (!isset($_GET['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['nonce'])), 'get_match_available_products_nonce')) {
            wp_send_json_error(esc_html__('Invalid nonce', 'meliconnect'));
            return;
        }

        // Verifica los permisos del usuario
        if (!current_user_can('meliconnect_manage_plugin')) {
            wp_send_json_error(esc_html__('You do not have permission to perform this action', 'meliconnect'));
            return;
        }

        global $wpdb;

        $product_type = isset($_GET['productType']) ? sanitize_text_field(wp_unslash($_GET['productType'])) : '';


        // Consulta para obtener productos de WooCommerce que no tienen el postmeta 'meliconnect_meli_listing_id'

        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT p.ID, p.post_title, p.post_type, p.post_status
                FROM {$wpdb->posts} p
                LEFT JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = 'meliconnect_meli_listing_id'
                WHERE pm.post_id IS NULL
                AND p.post_type = 'product'
                ORDER BY p.post_title ASC"
        ), ARRAY_A);


        // Construir la respuesta HTML para Select2
        $options = '';
        foreach ($results as $product) {
            $product_id = $product['ID'];
            $product_obj = wc_get_product($product_id);
            /* $product_type = $product_obj->get_type();
            echo PHP_EOL . '-------------------- product type --------------------' . PHP_EOL;
            echo '<pre>' . wp_json_encode( $product_id) . '</pre>';
            echo '<pre>' . wp_json_encode( $product_type) . '</pre>';
            echo PHP_EOL . '-------------------  FINISHED  ---------------------' . PHP_EOL; */
            if ($product_obj->is_type($product_type)) {
                if ($product_type === 'simple') {
                    $sku = $product_obj->get_sku();
                    $price = $product_obj->get_price();
                    $stock = $product_obj->get_stock_quantity();

                    $options .= sprintf(
                        '<option value="%d" data-status="%s" data-stock="%d" data-price="%s" data-type="%s" data-sku="%s">%s</option>',
                        esc_attr($product_id),
                        esc_attr($product['post_status']),
                        esc_attr($stock),
                        esc_attr($price),
                        esc_attr($product_type),
                        esc_attr($sku),
                        esc_html($product['post_title'])
                    );
                } elseif ($product_type === 'variable') {
                    $variations = $product_obj->get_available_variations();
                    $variations_data = [];

                    foreach ($variations as $variation) {
                        $variations_data[] = [
                            'variation_id' => $variation['variation_id'],
                            'sku' => $variation['sku'],
                            'price' => $variation['display_price'],
                            'stock' => $variation['max_qty'],
                        ];
                    }

                    $options .= sprintf(
                        '<option value="%d" data-status="%s" data-type="%s" data-variations="%s">%s</option>',
                        esc_attr($product_id),
                        esc_attr($product['post_status']),
                        esc_attr($product_type),
                        esc_attr(wp_json_encode($variations_data)),
                        esc_html($product['post_title'])
                    );
                }
            }
        }

        if (empty($options)) {
            wp_send_json_success(esc_html__('No products found', 'meliconnect'));
        } else {
            wp_send_json_success(['options' => $options]);
        }
    }

    /* START CUSTOM METHODS */

    public static function initImportProcess($meli_listings_ids = [])
    {
        // Obtener los listados de usuario a importar, si se proporcionan IDs específicos
        $items = UserListingToImport::get_user_listings_to_import($meli_listings_ids);

        // Si no hay ítems, detener el proceso
        if (empty($items)) {
            return false;
        }

        // Formatear los ítems en un solo paso
        $formatedItems = [];
        foreach ($items as $item) {
            $formatedItems[] = [
                'meli_user_id' => $item->meli_user_id,
                'meli_listing_id' => $item->meli_listing_id,
                'woo_product_id' => $item->vinculated_product_id,
                'template_id' => $item->vinculated_template_id,
                'process_status' => 'pending',
            ];
        }

        // Registrar el proceso inicial en la tabla wp_meliconnect_processes
        $process_id = Process::createProcess('custom-import', $formatedItems);

        // Si no se puede crear el proceso, detener el proceso
        if (!$process_id) {
            return false;
        }

        // Actualizar el estado de los ítems en un solo paso
        $meli_listing_ids = array_column($items, 'meli_listing_id');
        UserListingToImport::update_user_listing_item_import_status($meli_listing_ids, 'processing');

        return true;
    }

    public static function multiGetListingsData($seller_data, $meli_user_listings_ids)
    {
        $meli_user_listings_ids_chunk = array_chunk($meli_user_listings_ids, 20);

        Helper::logData('Getting imported products extra data from seller: ' . $seller_data->user_id, 'importer');

        $base_url = 'items?ids=';

        $meli_user_listings_with_data = [];
        $listings_with_errors = [];

        foreach ($meli_user_listings_ids_chunk as $chunk) {

            $current_url = $base_url . implode(',', $chunk) . '&attributes=id,site_id,title,seller_id,category_id,price,initial_quantity,available_quantity,sold_quantity,listing_type_id,condition,permalink,variations,domain_id,channels,status,sub_status ';

            $listingsWithExtraData = MeliconMeli::getWithHeader($current_url, $seller_data->access_token);


            if (!isset($listingsWithExtraData['body']) || $listingsWithExtraData['httpCode'] !== 200) {
                Helper::logData('Error getting chunk extra data in listings: ' . implode(',', $chunk), 'importer');

                Helper::logData('ListingsWithExtraData: ' . wp_json_encode($listingsWithExtraData, JSON_PRETTY_PRINT), 'importer');

                Helper::logData('Access Token: ' . $seller_data->access_token, 'importer');
                Helper::logData('current_url: ' . $current_url, 'importer');

                continue;
            }

            foreach ($listingsWithExtraData['body'] as $listing) {

                if (!isset($listing->body) || $listing->code !== 200) {
                    $listings_with_errors[] = $listing;
                    continue;
                }

                $meli_user_listings_with_data[$listing->body->id] = (array) $listing->body;
            }
        }


        if (!empty($listings_with_errors)) {
            Helper::logData('Listings with errors: ' . wp_json_encode($listings_with_errors, JSON_PRETTY_PRINT), 'importer');
        }

        Helper::logData('Obteined extra data from:' . count($meli_user_listings_with_data) . ' listings', 'importer');

        return $meli_user_listings_with_data;
    }

    public static function getMeliUserLisntingIds($seller_data)
    {
        $meli_user_listings = [];
        Helper::logData('Getting products from seller: ' . $seller_data->user_id, 'importer');

        $base_url = 'users/' . $seller_data->user_id . '/items/search?search_type=scan';
        $items = MeliconMeli::getWithHeader($base_url, $seller_data->access_token);

        if (!isset($items['body']->results) || !is_iterable($items['body']->results)) {
            Helper::logData('Error getting products from seller: ' . $seller_data->user_id, 'importer');
            return false;
        }

        $meli_user_listings = $items['body']->results;
        $scroll_id = $items['body']->scroll_id;
        $total_pages = intval(ceil($items['body']->paging->total / $items['body']->paging->limit));



        for ($i = 1; $i <= $total_pages; $i++) {
            $current_url = $base_url . '&scroll_id=' . $scroll_id;
            $current_items = MeliconMeli::getWithHeader($current_url, $seller_data->access_token);


            if (isset($current_items['body']->results) && is_iterable($current_items['body']->results)) {
                foreach ($current_items['body']->results as $current_item) {
                    if (!in_array($current_item, $meli_user_listings)) {
                        $meli_user_listings[] = $current_item;
                    }
                }

                // Actualizar el scroll_id para la próxima iteración
                if (isset($current_items['body']->scroll_id)) {
                    $scroll_id = $current_items['body']->scroll_id;
                } else {
                    break; // Si no hay scroll_id, salir del bucle
                }
            } else {
                if (isset($current_items['body']->error) && ($current_items['body']->error == 'client.exception')) {
                    break;
                }
            }
        }

        Helper::logData('Listings from seller: ' . $seller_data->user_id . ' are: ' . wp_json_encode($meli_user_listings, JSON_PRETTY_PRINT), 'importer');

        return $meli_user_listings;
    }

    public static function getPublishedVariableProducts()
    {
        global $wpdb;

        $product_ids = $wpdb->get_col("
            SELECT ID FROM {$wpdb->posts} 
            WHERE post_type = 'product' 
            AND post_status = 'publish'
        ");

        $variable_products = [];

        foreach ($product_ids as $product_id) {
            $product = wc_get_product($product_id);

            if ($product && $product->is_type('variable')) {
                $variations = $product->get_children();

                foreach ($variations as $variation_id) {
                    $variation = wc_get_product($variation_id);

                    if ($variation) {
                        $variable_products[$product_id] = [
                            'product_id' => $product_id,
                            'variation_id' => $variation_id,
                            'sku' => $variation->get_sku(),
                            'price' => $variation->get_price(),
                            'stock_quantity' => $variation->get_stock_quantity(),
                        ];
                    }
                }
            }
        }

        return $variable_products;
    }
}
