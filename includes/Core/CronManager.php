<?php

namespace StoreSync\Meliconnect\Core;

use StoreSync\Meliconnect\Core\Helpers\Helper;
use StoreSync\Meliconnect\Core\Models\Process;
use StoreSync\Meliconnect\Core\Models\ProcessItems;
use StoreSync\Meliconnect\Core\Models\Template;
use StoreSync\Meliconnect\Modules\Exporter\Models\ProductToExport;
use StoreSync\Meliconnect\Modules\Exporter\Services\MercadoLibreListingAdapter;
use StoreSync\Meliconnect\Modules\Exporter\Services\ListingDataFacade;

use StoreSync\Meliconnect\Modules\Importer\Models\UserListingToImport;
use StoreSync\Meliconnect\Modules\Importer\Services\ProductDataFacade;
use StoreSync\Meliconnect\Modules\Importer\Services\WooCommerceProductAdapter;
use StoreSync\Meliconnect\Modules\Importer\Services\WooCommerceProductCreationService;

class CronManager
{

    private $last_export_import_timestamp_option = 'melicon_last_export_import_timestamp';
    private $last_sync_timestamp_option = 'melicon_last_sync_timestamp';

    private $interval = 'every_minute';

    private $import_hook = 'melicon_process_import_tasks_event';
    private $import_lock_option_name = 'melicon_import_lock';

    private $export_hook = 'melicon_process_export_tasks_event';
    private $export_lock_option_name = 'melicon_export_lock';

    private $sync_hook = 'melicon_process_sync_tasks_event';
    private $sync_lock_option_name = 'melicon_sync_lock';

    private $import_custom_hook = 'melicon_process_user_custom_import';
    private $custom_import_lock_option_name = 'melicon_custom_import_lock';

    private $export_custom_hook = 'melicon_process_user_custom_export';
    private $custom_export_lock_option_name = 'melicon_custom_export_lock';



    private $settings;

    public function __construct()
    {
        add_action('init', [$this, 'registerFiltersAndActions']);
        add_action('init', [$this, 'registerCrons']);

        $this->settings = Helper::getMeliconnectOptions('all');
    }



    public function registerFiltersAndActions()
    {
        add_filter('cron_schedules', function ($schedules) {
            if (!isset($schedules[$this->interval])) {
                $schedules[$this->interval] = [
                    'interval' => 60,
                    'display'  => __('Every Minute')
                ];
            }
            return $schedules;
        });
    }

    public function registerCrons()
    {
        if (isset($this->settings['melicon_import_is_disabled']) && $this->settings['melicon_import_is_disabled'] === 'true') {
            Helper::logData('Import cron not registered because it is disabled.', 'cron_generals');
        } else {
            $this->scheduleCronEvent($this->import_hook, 'Import cron registered.');
        }

        if (isset($this->settings['melicon_export_is_disabled']) && $this->settings['melicon_export_is_disabled'] === 'true') {
            //Helper::logData('Export cron not registered because it is disabled.', 'cron_generals');
        } else {
            $this->scheduleCronEvent($this->export_hook, 'Export cron registered.');
        }

        $this->scheduleCronEvent($this->sync_hook, 'Sync cron registered.');
        $this->scheduleCronEvent($this->import_custom_hook, 'User custom Import cron registered.');
        $this->scheduleCronEvent($this->export_custom_hook, 'User custom Export cron registered.');
    }

    private function scheduleCronEvent($hook, $logMessage)
    {
        if (!wp_next_scheduled($hook)) {
            wp_schedule_event(time(), $this->interval, $hook);
            Helper::logData($logMessage, 'cron_generals');
        }
    }

    public function handleCronExecution()
    {
        if (wp_doing_cron()) {
            add_action($this->import_hook, [$this, 'processImportTasks']);
            add_action($this->export_hook, [$this, 'processExportTasks']);
            add_action($this->sync_hook, [$this, 'processSyncTasks']);
            add_action($this->import_custom_hook, [$this, 'processUserCustomImport']);
            add_action($this->export_custom_hook, [$this, 'processUserCustomExport']);
        }
    }

    public function processUserCustomImport()
    {
        global $wpdb;
        $meli_listing_ids_arr = [];

        $lock = get_option($this->custom_import_lock_option_name);

        if ($lock) {
            Helper::logData('Custom import is in process. Aborting.', 'custom-import');
            return;
        }

        // Update lock
        update_option($this->custom_import_lock_option_name, time());

        try {
            // 1- Get pending items from process items table 
            $items_to_process = $this->get_pending_items_to_process('custom-import');
            

            if (empty($items_to_process)) {
                return;
            }

            Helper::logData('Items to process: ' . count($items_to_process), 'custom-import');

            // Create instances of the dependencies needed for ProductDataFacade
            $wooCommerceAdapter = new WooCommerceProductAdapter();
            $productCreationService = new WooCommerceProductCreationService();
            $productDataFacade = new ProductDataFacade($wooCommerceAdapter, $productCreationService);

            foreach ($items_to_process as $item) {
                // Verificar si se ha solicitado la cancelación
                $cancel_requested = get_option('custom_import_cancel_requested');

                Helper::logData('Cancel requested: ' . $cancel_requested, 'custom-import');
                if ($cancel_requested) {

                    Helper::logData('Custom import canceled by user. Exiting process.', 'custom-import');

                    UserListingToImport::update_processing_listings('canceled');

                    throw new \Exception('Custom import canceled by user.');
                }

                Helper::logData('Processing listing id: ' . $item->meli_listing_id . ' - Status: ' . $item->process_status, 'custom-import');

                try {
                    // Iniciar la transacción para el ítem actual
                    $wpdb->query('START TRANSACTION');

                    // 2- Format items data to send, Send data to API server and Get response and process response creating or updating items in WooCommerce
                    $productDataFacade->importAndCreateProduct($item->meli_listing_id, $item->meli_user_id, $item->template_id, $item->woo_product_id);

                    // 3- Update process and process items table 
                    $process = ProcessItems::updateProcessedItemStatus($item->id, 'processed', $item->process_id);

                    Helper::logData('Process: ' . json_encode($process), 'custom-import');

                    $meli_listing_ids_arr = array_merge($meli_listing_ids_arr, [$item->meli_listing_id]);

                    UserListingToImport::update_user_listing_item_import_status([$item->meli_listing_id], 'finished');

                    //

                    // Commit the transaction if everything is successful for this item
                    $wpdb->query('COMMIT');
                } catch (\Exception $itemException) {
                    // Log individual item error and rollback transaction
                    Helper::logData('Error processing listing id ' . $item->meli_listing_id . ': ' . $itemException->getMessage(), 'custom-import');

                    // Rollback the transaction for the current item
                    $wpdb->query('ROLLBACK');
                }
            }
        } catch (\Exception $e) {
            // Log the error message
            Helper::logData('Error during custom import: ' . $e->getMessage(), 'custom-import');
        } finally {

            UserListingToImport::update_vinculated_product_ids($meli_listing_ids_arr);
            // When cron finishes, remove lock
            delete_option($this->custom_import_lock_option_name);

            //Helper::logData('Custom import process finished. Deleting custom_import_cancel_requested', 'custom-import');

            // Remove the cancel request flag
            delete_option('custom_import_cancel_requested');
        }
    }

    public function processUserCustomExport()
    {
        global $wpdb;

        $lock = get_option($this->custom_export_lock_option_name);

        if ($lock) {
            Helper::logData('Custom export is in process. Aborting.', 'custom-export');
            return;
        }

        // Update lock
        update_option($this->custom_export_lock_option_name, time());

        try {
            // 1- Get pending items from process items table 
            $items_to_process = $this->get_pending_items_to_process('custom-export');
            

            if (empty($items_to_process)) {
                return;
            }

            Helper::logData('Items to process: ' . count($items_to_process), 'custom-export');
            
            // Create instances of the dependencies needed for ProductDataFacade
            $meliListingAdapter = new MercadoLibreListingAdapter();
            $listingDataFacade = new ListingDataFacade($meliListingAdapter);

            foreach ($items_to_process as $item) {
                // Verificar si se ha solicitado la cancelación
                $cancel_requested = get_option('custom_export_cancel_requested');

                if ($cancel_requested) {

                    Helper::logData('Custom export canceled by user. Exiting process.', 'custom-export');

                    //TO CHANGE
                    //UserListingToImport::update_processing_listings('canceled');

                    throw new \Exception('Custom export canceled by user.');
                }

                Helper::logData('Processing woo product id: ' . $item->woo_product_id . ' - Status: ' . $item->process_status, 'custom-export');

                try {
                    if(!isset($item->template_id) || empty($item->template_id) || !isset($item->woo_product_id) || empty($item->woo_product_id)){
                        Helper::logData('No template id  or woo product id found for item: ' . json_encode($item), 'custom-export');
                        continue;
                    }

                    if(isset($item->meli_user_id) && !empty($item->meli_user_id)){
                        $meli_user_id = $item->meli_user_id;
                    }else{
                        $custom_template_data= Template::selectCustomTemplateData($item->template_id, ['seller_meli_id']);
                        
                        if(!isset($custom_template_data['seller_meli_id']) || empty($custom_template_data['seller_meli_id'])){ 
                            Helper::logData('User id not found in template: ' . $item->template_id, 'custom-export');
                        }

                        $meli_user_id = $custom_template_data['seller_meli_id'];
                        Helper::logData('Using Seller id: ' . $meli_user_id . ' from template: ' . $item->template_id, 'custom-export');
                    }

                    // Iniciar la transacción para el ítem actual
                    $wpdb->query('START TRANSACTION');

                    // 2- Format items data to send, Send data to API server and Get response and process response creating or updating items in WooCommerce
                    $listingDataFacade->getAndExportListing($meli_user_id, $item->woo_product_id, $item->template_id, $item->meli_listing_id);

                    // 3- Update process and process items table 
                    $process = ProcessItems::updateProcessedItemStatus($item->id, 'processed', $item->process_id);

                    Helper::logData('Process: ' . json_encode($process), 'custom-export');


                    // Commit the transaction if everything is successful for this item
                    $wpdb->query('COMMIT');
                } catch (\Exception $itemException) {
                    // Log individual item error and rollback transaction
                    Helper::logData('Error processing woo product with id ' . $item->woo_product_id . ': ' . $itemException->getMessage(), 'custom-export');

                    // Rollback the transaction for the current item
                    $wpdb->query('ROLLBACK');
                }
            }
        } catch (\Exception $e) {
            // Log the error message
            Helper::logData('Error during custom export: ' . $e->getMessage(), 'custom-export');
        } finally {

            //TO CHANGE
            //UserListingToImport::update_vinculated_product_ids($meli_listing_ids_arr);
            //ProductToExport::update_vinculated_product_ids($woo_products_ids_arr);

            // When cron finishes, remove lock
            delete_option($this->custom_export_lock_option_name);
            //Helper::logData('Custom export process finished. Deleting custom_export_cancel_requested', 'custom-export');
            // Remove the cancel request flag
            delete_option('custom_export_cancel_requested');
        }
    }

   


    public function processImportTasks()
    {
        $this->processTasks('import', $this->last_export_import_timestamp_option, 'cron_import');
    }

    public function processExportTasks()
    {
        $this->processTasks('export', $this->last_export_import_timestamp_option, 'cron_export');
    }

    public function processSyncTasks()
    {
        $this->processTasks('sync', $this->last_sync_timestamp_option, 'cron_sync');
    }

    private function processTasks($taskType, $timestampOption, $logPrefix)
    {

        // Verificar el estado de habilitación de la sincronización general o específica
        $isActive = true;


        switch ($taskType) {
            case 'import':
            case 'export':
                $isActive = $this->settings['melicon_general_sync_type'] == $taskType &&  $this->settings['melicon_' . $taskType . '_is_disabled'] != 'true' && $this->settings['melicon_general_sync_method'] === 'wordpress';
                break;
            case 'sync':
                $isActive = $this->settings['melicon_sync_cron_status'] === 'active' && $this->settings['melicon_sync_cron_method'] === 'wordpress';
                break;
        }


        if ($isActive) {
            $last_export_timestamp = get_option($timestampOption, 0);
            $current_time = current_time('timestamp');
            $time_difference_minutes = ($current_time - $last_export_timestamp) / 60;


            if ($time_difference_minutes >= $this->settings['melicon_general_sync_frecuency_minutes'] || $last_export_timestamp == 0) {
                Helper::logData('Last export timestamp: ' . $last_export_timestamp, $logPrefix);
                Helper::logData('Current time: ' . $current_time, $logPrefix);
                Helper::logData('Difference: ' . $time_difference_minutes, $logPrefix);
                Helper::logData('Se ejecutó la función de ' . $taskType, $logPrefix);

                switch ($taskType) {
                    case 'import':
                        $this->do_import();
                        break;
                    case 'export':
                        $this->do_export();
                        break;
                    case 'sync':
                        $this->do_sync();
                        break;
                }

                update_option($timestampOption, $current_time);
            } else {
                Helper::logData('Por diff time. NO se ejecutó la función de ' . $taskType, $logPrefix);

                Helper::logData('Last export timestamp: ' . $last_export_timestamp, $logPrefix);
                Helper::logData('Current time: ' . $current_time, $logPrefix);
                Helper::logData('Difference: ' . $time_difference_minutes, $logPrefix);
            }
        } else {
            //Helper::logData('La sincronización ' . $taskType . ' NO se encuentra activa.', $logPrefix);

            /* Helper::logData('Export/import options ', $logPrefix);
            Helper::logData('melicon_' . $taskType . '_is_disabled: ' . $this->settings['melicon_' . $taskType . '_is_disabled'], $logPrefix);
            Helper::logData('melicon_general_sync_type: ' . $this->settings['melicon_general_sync_type'], $logPrefix);
            Helper::logData('melicon_general_sync_method: ' . $this->settings['melicon_general_sync_method'], $logPrefix);

            Helper::logData('Sync options ', $logPrefix);
            Helper::logData('melicon_sync_cron_status: ' . $this->settings['melicon_sync_cron_status'], $logPrefix);
            Helper::logData('melicon_sync_cron_method: ' . $this->settings['melicon_sync_cron_method'], $logPrefix); */
        }
    }


    public function do_import()
    {
        $lock = get_option($this->import_lock_option_name);

        if ($lock) {
            Helper::logData('Import is in process. Aborting.', 'cron_import');
            return;
        }

        //Update lock
        update_option($this->import_lock_option_name, time());

        //Do cron process .....

        //1- Get pending items from process items table 
        $items_to_process = $this->get_pending_items_to_process('import');

        foreach ($items_to_process as $item) {

            //2- Format items data to send

            //3- Send data to API server

            //4 - Get response and process response creating or updating items in woocommerce

            //5- Update process items table

            //6- Update Process table if there is no more items to process
        }


        //End cron process 
        //When cron finish remove lock
        delete_option($this->import_lock_option_name);
    }

    public function do_export() {}

    public function do_sync() {}

    private function get_pending_items_to_process($taskType)
    {
        //Get total items per batch. used in automatic export, import and sync
        $total_items_per_batch = ($taskType == 'sync') ? $this->settings['melicon_sync_cron_items_batch'] : $this->settings['melicon_general_sync_items_batch'];

        $current_items_to_process = [];

        $current_process = Process::getCurrentProcessData($taskType, ['processing', 'pending']);


        if (!$current_process) {
            if ($taskType == 'import' ||  $taskType == 'export' || $taskType == 'sync') {

                //TODO if is import export o sync get total items to process
                $current_process_id = Process::createProcess($taskType, []);
            } else {
                //If is custom import or custom export and there is no current process, return empty array
                return [];
            }
        } else {
            $current_process_id = $current_process->process_id;
            $total_items_per_batch = 100;
        }

        $current_items_to_process = ProcessItems::getProcessItems($current_process_id, $total_items_per_batch, 'pending');

        return $current_items_to_process;
    }



    public static function deactivate()
    {
        $self = new self();
        wp_clear_scheduled_hook($self->import_hook);
        wp_clear_scheduled_hook($self->export_hook);
        wp_clear_scheduled_hook($self->sync_hook);
        wp_clear_scheduled_hook($self->import_custom_hook);
        wp_clear_scheduled_hook($self->export_custom_hook);
    }
}
