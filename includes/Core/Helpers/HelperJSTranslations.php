<?php

namespace Meliconnect\Meliconnect\Core\Helpers;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class HelperJSTranslations
{
    public static function getTranslations()
    {
        return array(
            'admin_ajax_url' => admin_url('admin-ajax.php'),
            'ajax_settings_nonce' => wp_create_nonce('ajax_settings_nonce'),
            'confirm_message' => esc_html__('Are you sure?', 'meliconnect'),
            'cancel_message'  => esc_html__('Action cancelled', 'meliconnect'),
            'success_message' => esc_html__('Action successful', 'meliconnect'),
            'reset_user_listings' => esc_html__('Reset User Listings', 'meliconnect'),
            'reset_user_listings_body' =>esc_html__('You are going to reset all user listings pre-imported. Are you sure?', 'meliconnect'),
            'confirm'=>esc_html__('Yes, confirm', 'meliconnect'),
            'cancel'=>esc_html__('Cancel', 'meliconnect'),
            'select_or_upload_media'=>esc_html__('Select or Upload Media', 'meliconnect'),
            'no_image_selected'=>esc_html__('No image selected', 'meliconnect'),


            'reset_listings_nonce' => wp_create_nonce('reset_listings_nonce'),
            'error' => esc_html__('Error', 'meliconnect'),
            'get_listings_nonce' => wp_create_nonce('get_meli_user_listings_nonce'),
            'all_items_imported' => esc_html__('All items imported successfully', 'meliconnect'),
            'alert_title_import_process_init' => esc_html__('You are going to start the import process', 'meliconnect'),
            'alert_body_import_process_init' => esc_html__('Are you sure you want to create or update woocommerce products as can be seen in the table below?', 'meliconnect'),
            'init_import_process_nonce' => wp_create_nonce('init_import_process_nonce'),
            'cancel_finished_processes_nonce' => wp_create_nonce('cancel_finished_processes_nonce'),
            'pause_import_process_nonce' => wp_create_nonce('pause_import_process_nonce'),
            'alert_title_disable_import' => esc_html__('You are going to disable the import functionality', 'meliconnect'),
            'alert_body_disable_import' => esc_html__('All pending import processes (custom and automatic) will be terminated, and you will not be able to execute any new imports until it is reactivated. Are you sure you want to proceed?',  'meliconnect'),
            'alert_title_disable_export' => esc_html__('You are going to disable the export functionality', 'meliconnect'),
            'alert_body_disable_export' => esc_html__('All pending export processes (custom and automatic) will be terminated, and you will not be able to execute any new imports until it is reactivated. Are you sure you want to proceed?',  'meliconnect'),
            'alert_title_cancel_custom_import' => esc_html__('You are going to cancel the import process', 'meliconnect'),
            'alert_body_cancel_custom_import' => esc_html__('Pending items will be removed from the processing queue. Are you sure you want to proceed?',  'meliconnect'),
            'cancel_custom_import_nonce' => wp_create_nonce('cancel_custom_import_nonce'),
            'pause_custom_import_nonce' => wp_create_nonce('pause_custom_import_nonce'),
            'process_finished'=> esc_html__('Process finished', 'meliconnect'),
            'alert_title_apply_bulk_action' => esc_html__('Confirm Bulk Action', 'meliconnect'),
            'alert_body_apply_bulk_action' => esc_html__('Are you sure you want to apply this bulk action?', 'meliconnect'),
            'no_items_selected' => esc_html__('No items selected', 'meliconnect'),
            'select_items_to_apply_action' => esc_html__('Please select items before proceeding', 'meliconnect'),
            'import_bulk_action_nonce' => wp_create_nonce('import_bulk_action_nonce'),
            'invalid_action' => esc_html__('Invalid Action', 'meliconnect'),
            'please_select_a_valid_bulk_action' => esc_html__('Please select a valid bulk action', 'meliconnect'),
            'alert_title_desvinculate_product' => esc_html__('Are you sure you want to unlink this product?', 'meliconnect'),
            'alert_body_desvinculate_product' => esc_html__('Woocommerce products will be desvinculated from Mercadolibre listing', 'meliconnect'),
            'desvinculate_product_nonce' => wp_create_nonce('desvinculate_product_nonce'),
            'match_listings_with_products_nonce' => wp_create_nonce('match_listings_with_products_nonce'),
            'clear_all_matches_nonce' => wp_create_nonce('clear_all_matches_nonce'),
            'please_select_a_product_and_a_listing' => esc_html__('Please select a product and a listing', 'meliconnect'),
            'apply_match_nonce' => wp_create_nonce('apply_match_nonce'),
            'clear_selected_matches_nonce' => wp_create_nonce('clear_selected_matches_nonce'),

            'cancel_custom_export_nonce' => wp_create_nonce('cancel_custom_export_nonce'),
            'alert_title_cancel_custom_export' => esc_html__('You are going to cancel the export process', 'meliconnect'),
            'alert_body_cancel_custom_export' => esc_html__('Pending items will be removed from the processing queue. Are you sure you want to proceed?',  'meliconnect'),
            'get_match_available_products_nonce' => wp_create_nonce('get_match_available_products_nonce'),
            'export_bulk_action_nonce' => wp_create_nonce('export_bulk_action_nonce'),
            'get_process_progress_nonce'=> wp_create_nonce('get_process_progress_nonce'),
            'clean_custom_export_nonce' => wp_create_nonce('clean_custom_export_nonce'),
            'alert_last_json_sent_title' => esc_html__('Last JSON sent to HUB', 'meliconnect'),
            'back' => esc_html__('Return', 'meliconnect'),
            'copy_to_clipboard' => esc_html__('Copy to clipboard', 'meliconnect'),
            'copy_to_clipboard_success' => esc_html__('Successfully copied to clipboard', 'meliconnect'),
            'default_error_message' => esc_html__('Something went wrong', 'meliconnect'),

            'melicon_load_meli_categories_nonce' => wp_create_nonce('melicon_load_meli_categories_nonce'),
            'melicon_update_meli_category_nonce' => wp_create_nonce('melicon_update_meli_category_nonce'),
            'melicon_import_single_listing_nonce' => wp_create_nonce('melicon_import_single_listing_nonce'),
            'melicon_export_single_listing_nonce' => wp_create_nonce('melicon_export_single_listing_nonce'),
            'melicon_unlink_single_listing_nonce' => wp_create_nonce('melicon_unlink_single_listing_nonce'),
            'melicon_save_template_data_nonce' => wp_create_nonce('melicon_save_template_data_nonce'),

            'current_category_id' => isset($form_values['category_id']) ? $form_values['category_id'] : '',
            'woo_product_id'      => isset($woo_product_id) ? $woo_product_id : '',

        );
    }

    public static function localizeScript($script_handle)
    {
        wp_localize_script($script_handle, 'mcTranslations', self::getTranslations());
    }
}