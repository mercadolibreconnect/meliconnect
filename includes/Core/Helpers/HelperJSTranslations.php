<?php

namespace StoreSync\Meliconnect\Core\Helpers;

class HelperJSTranslations
{
    public static function getTranslations()
    {
        return array(
            'confirm_message' => __('Are you sure?', 'meliconnect'),
            'cancel_message'  => __('Action cancelled', 'meliconnect'),
            'success_message' => __('Action successful', 'meliconnect'),
            'reset_user_listings' => __('Reset User Listings', 'meliconnect'),
            'reset_user_listings_body' =>__('You are going to reset all user listings pre-imported. Are you sure?', 'meliconnect'),
            'confirm'=>__('Yes, confirm', 'meliconnect'),
            'cancel'=>__('Cancel', 'meliconnect'),
            'reset_listings_nonce' => wp_create_nonce('reset_listings_nonce'),
            'error' => __('Error', 'meliconnect'),
            'all_items_imported' => __('All items imported successfully', 'meliconnect'),
            'alert_title_import_process_init' => __('You are going to start the import process', 'meliconnect'),
            'alert_body_import_process_init' => __('Are you sure you want to create or update woocommerce products as can be seen in the table below?', 'meliconnect'),
            'init_import_process_nonce' => wp_create_nonce('init_import_process_nonce'),
            'cancel_finished_processes_nonce' => wp_create_nonce('cancel_finished_processes_nonce'),
            'pause_import_process_nonce' => wp_create_nonce('pause_import_process_nonce'),
            'alert_title_disable_import' => __('You are going to disable the import functionality', 'meliconnect'),
            'alert_body_disable_import' => __('All pending import processes (custom and automatic) will be terminated, and you will not be able to execute any new imports until it is reactivated. Are you sure you want to proceed?',  'meliconnect'),
            'alert_title_disable_export' => __('You are going to disable the export functionality', 'meliconnect'),
            'alert_body_disable_export' => __('All pending export processes (custom and automatic) will be terminated, and you will not be able to execute any new imports until it is reactivated. Are you sure you want to proceed?',  'meliconnect'),
            'alert_title_cancel_custom_import' => __('You are going to cancel the import process', 'meliconnect'),
            'alert_body_cancel_custom_import' => __('Pending items will be removed from the processing queue. Are you sure you want to proceed?',  'meliconnect'),
            'cancel_custom_import_nonce' => wp_create_nonce('cancel_custom_import_nonce'),
            'pause_custom_import_nonce' => wp_create_nonce('pause_custom_import_nonce'),
            'process_finished'=> __('Process finished', 'meliconnect'),
            'alert_title_apply_bulk_action' => __('Confirm Bulk Action', 'meliconnect'),
            'alert_body_apply_bulk_action' => __('Are you sure you want to apply this bulk action?', 'meliconnect'),
            'no_items_selected' => __('No items selected', 'meliconnect'),
            'select_items_to_apply_action' => __('Please select items before proceeding', 'meliconnect'),
            'import_bulk_action_nonce' => wp_create_nonce('import_bulk_action_nonce'),
            'invalid_action' => __('Invalid Action', 'meliconnect'),
            'please_select_a_valid_bulk_action' => __('Please select a valid bulk action', 'meliconnect'),
            'alert_title_desvinculate_product' => __('Are you sure you want to unlink this product?', 'meliconnect'),
            'alert_body_desvinculate_product' => __('Woocommerce products will be desvinculated from Mercadolibre listing', 'meliconnect'),
            'desvinculate_product_nonce' => wp_create_nonce('desvinculate_product_nonce'),
            'match_listings_with_products_nonce' => wp_create_nonce('match_listings_with_products_nonce'),
            'clear_all_matches_nonce' => wp_create_nonce('clear_all_matches_nonce'),
            'please_select_a_product_and_a_listing' => __('Please select a product and a listing', 'meliconnect'),
            'apply_match_nonce' => wp_create_nonce('apply_match_nonce'),
            'clear_selected_matches_nonce' => wp_create_nonce('clear_selected_matches_nonce'),
            'cancel_custom_export_nonce' => wp_create_nonce('cancel_custom_export_nonce'),
            'alert_title_cancel_custom_export' => __('You are going to cancel the export process', 'meliconnect'),
            'alert_body_cancel_custom_export' => __('Pending items will be removed from the processing queue. Are you sure you want to proceed?',  'meliconnect'),

            'export_bulk_action_nonce' => wp_create_nonce('export_bulk_action_nonce'),
            'clean_custom_export_nonce' => wp_create_nonce('clean_custom_export_nonce'),
            'alert_last_json_sent_title' => __('Last JSON sent to HUB', 'meliconnect'),
            'back' => __('Return', 'meliconnect'),
            'copy_to_clipboard' => __('Copy to clipboard', 'meliconnect'),
            'copy_to_clipboard_success' => __('Successfully copied to clipboard', 'meliconnect'),

        );
    }

    public static function localizeScript($script_handle)
    {
        wp_localize_script($script_handle, 'mcTranslations', self::getTranslations());
    }
}