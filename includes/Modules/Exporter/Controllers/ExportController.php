<?php

namespace StoreSync\Meliconnect\Modules\Exporter\Controllers;

use StoreSync\Meliconnect\Core\Helpers\Helper;
use StoreSync\Meliconnect\Core\Helpers\HelperJSTranslations;
use StoreSync\Meliconnect\Core\Interfaces\ControllerInterface;
use StoreSync\Meliconnect\Core\Models\Process;
use StoreSync\Meliconnect\Core\Models\ProcessItems;
use StoreSync\Meliconnect\Modules\Exporter\Models\ProductToExport;

class ExportController implements ControllerInterface
{
    public function __construct()
    {
        // Inits hooks or another configurations
        $this->loadAssets();
    }

    public function getData()
    {
        $data = [];
        $process = Process::getCurrentProcessData('custom-export');
        $process_finished = Process::getCurrentProcessData('custom-export', 'finished');

        $data = [
            'export_process_finished' => $process_finished,
            'export_process_data' => $process,
            'execution_time' => Process::calculateExecutionTime($process),
            'products_to_export_count' => ProductToExport::count_products_to_export(),
            'woo_total_active_products' => Helper::get_woo_active_products_count(),
            'woo_total_vinculated_products' => count(Helper::getProductsWithMeta('melicon_meli_listing_id')),
        ];

        return $data;
    }

    public function loadAssets()
    {
        /* if (is_page('meliconnect-exporter')) { */
        wp_enqueue_style('melicon-exporter', MC_PLUGIN_URL . 'includes/Modules/Exporter/Assets/Css/melicon-exporter.css', [], '1.0.0');

        wp_register_script('melicon-exporter-js', MC_PLUGIN_URL . 'includes/Modules/Exporter/Assets/Js/melicon-exporter.js', ['jquery'], '1.0.0', true);

        HelperJSTranslations::localizeScript('melicon-exporter-js');
        wp_enqueue_script('melicon-exporter-js');

        /* } */
    }


    /* START HANDLE AJAX METHODS */

    
    public static function handleCancelCustomExport()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'cancel_custom_export_nonce')) {
            wp_send_json_error(__('Invalid nonce', 'meliconnect'));
            return;
        }

        // Verifica los permisos del usuario
        if (!current_user_can('meliconnect_manage_plugin')) {
            wp_send_json_error(__('You do not have permission to perform this action', 'meliconnect'));
            return;
        }

        $process_id = $_POST['process_id'];

        update_option('custom_export_cancel_requested', true);

        sleep(2);

        Process::cancelProcess($process_id);

        //unlock custom export process cron
        delete_option('melicon_custom_export_lock');

        wp_send_json_success();
    }

    public static function handleBulkExportAction()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'export_bulk_action_nonce')) {
            Helper::logData('Invalid nonce', 'bulk-actions-export');
            wp_send_json_error(__('Invalid nonce', 'meliconnect'));
            return;
        }

        // Verifica los permisos del usuario
        if (!current_user_can('meliconnect_manage_plugin')) {
            Helper::logData('Permission denied', 'bulk-actions-export');
            wp_send_json_error(__('You do not have permission to perform this action', 'meliconnect'));
            return;
        }

        $selected_ids = isset($_POST['products_ids']) ? sanitize_text_field($_POST['products_ids']) : '';
        $action = isset($_POST['action_to_do']) ? sanitize_text_field($_POST['action_to_do']) : '';

        if (!$selected_ids || !$action || $action == -1) {
            Helper::logData('Invalid data: missing selected_ids or action', 'bulk-actions-export');
            wp_send_json_error(__('Invalid data', 'meliconnect'));
        }

        $selected_ids_arr = explode(',', $selected_ids);
        Helper::logData('Processing action: ' . $action, 'bulk-actions-export');
        Helper::logData('Selected IDs: ' . implode(',', $selected_ids_arr), 'bulk-actions-export');

        switch ($action) {
            case 'export-selected':
                Helper::logData('Initiating export process', 'bulk-actions-export');
                self::initExportProcess($selected_ids_arr);
                break;

            case 'desvinculate-products':
            case 'desvinculate-products-and-pause':
            case 'desvinculate-products-and-delete':
                $deleting = ($action == 'desvinculate-products-and-delete');
                $pausing = ($action == 'desvinculate-products-and-pause');
                $new_status = $pausing ? 'paused' : ($deleting ? 'closed' : '');

                Helper::logData('Unlinking listings', 'bulk-actions-export');
                Helper::logData('Deleting: ' . ($deleting ? 'yes' : 'no'), 'bulk-actions-export');
                Helper::logData('Pausing: ' . ($pausing ? 'yes' : 'no'), 'bulk-actions-export');

                foreach ($selected_ids_arr as $woo_product_id) {
                    $update_status = false;

                    if (!empty($new_status)) {
                        // Actualizar el estado del producto en MercadoLibre
                        $update_status = self::updateListingStatus($woo_product_id, $new_status);

                        if ($update_status) {
                            Helper::logData("Product {$woo_product_id} status updated to {$new_status}", 'bulk-actions-export');
                        } else {
                            Helper::logData("Failed to update status for product {$woo_product_id}", 'bulk-actions-export');
                        }
                    }

                    // Solo desvincula si no se requiere cambio de estado o si el estado se actualizó correctamente
                    if (empty($new_status) || $update_status) {
                        Helper::unlinkProduct($woo_product_id);
                        ProductToExport::unlink_woo_product($woo_product_id);
                        Helper::logData("Successfully unlinked product {$woo_product_id} from WooCommerce", 'bulk-actions-export');
                    } else {
                        Helper::logData("Failed to unlink product {$woo_product_id} in WooCommerce due to status update failure", 'bulk-actions-export');
                    }
                }
                break;

            default:
                Helper::logData('Unknown action', 'bulk-actions-export');
                break;
        }


        wp_send_json_success(array('message' => 'Bulk action processed successfully.'));
    }


    private static function updateListingStatus($woo_product_id, string $status)
    {
        $updated_status = Helper::change_meli_listing_status($woo_product_id, $status);

        //error_log('updated_status: ' . var_export($updated_status, true));

        if (!$updated_status) {
            Helper::logData("Error updating listing status: {$status} for product {$woo_product_id}", 'bulk-actions-export');
            return false;
        } else {
            Helper::logData("Updated listing status for product {$woo_product_id} to {$status}", 'bulk-actions-export');
            return true;
        }
    }

    public static function initExportProcess($products_ids = [])
    {
        // Obtener los listados de productos a exportar, si se proporcionan IDs específicos
        $items = ProductToExport::get_products_to_export($products_ids);

        // Si no hay ítems, detener el proceso
        if (empty($items)) {
            return false;
        }

        // Formatear los ítems en un solo paso
        $formatedItems = [];
        foreach ($items as $item) {
            $formatedItems[] = [
                'meli_user_id' => $item->meli_seller_id,
                'meli_listing_id' => $item->vinculated_listing_id,
                'woo_product_id' => $item->woo_product_id,
                'template_id' => $item->vinculated_template_id,
                'process_status' => 'pending',
            ];
        }

        // Registrar el proceso inicial en la tabla wp_melicon_processes
        $process_id = Process::createProcess('custom-export', $formatedItems);

        // Si no se puede crear el proceso, detener el proceso
        if (!$process_id) {
            return false;
        }

        // Actualizar el estado de los ítems en un solo paso
        $woo_product_ids = array_column($items, 'woo_product_id');
        ProductToExport::update_product_to_export_status($woo_product_ids, 'processing');

        return true;
    }

    public static function handleDesvinculateListing(){
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'desvinculate_product_nonce')) {
            wp_send_json_error(__('Invalid nonce', 'meliconnect'));
            return;
        }

        // Verifica los permisos del usuario
        if (!current_user_can('meliconnect_manage_plugin')) {
            wp_send_json_error(__('You do not have permission to perform this action', 'meliconnect'));
            return;
        }

        $wooProductId = isset($_POST['wooProductId']) ? sanitize_text_field($_POST['wooProductId']) : '';

        if (!$wooProductId ) {
            wp_send_json_error(__('Invalid data', 'meliconnect'));
        }

        Helper::unlinkProduct($wooProductId);

        wp_send_json_success(array('message' => __('Product desvinculated successfully.', 'meliconnect')));
    }

    public static function handleCleanCustomExportProcess()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'clean_custom_export_nonce')) {
            wp_send_json_error(__('Invalid nonce', 'meliconnect'));
            return;
        }

        // Verifica los permisos del usuario
        if (!current_user_can('meliconnect_manage_plugin')) {
            wp_send_json_error(__('You do not have permission to perform this action', 'meliconnect'));
            return;
        }
        
        $process_id = isset($_POST['processId']) ? sanitize_text_field($_POST['processId']) : '';

        if (!$process_id) {
            wp_send_json_error(__('Invalid data', 'meliconnect'));
        }

        ProcessItems::deleteItems($process_id);

        $deleted = Process::cancelFinishedProcesses('custom-export');

        if (!$deleted) {
            wp_send_json_error();
        }

        ProductToExport::update_product_to_export_status('all', 'pending', '');

        //unlock custom import process cron
        delete_option('melicon_custom_export_lock');

        wp_send_json_success();
    }



    /* START CUSTOM METHODS */
}
