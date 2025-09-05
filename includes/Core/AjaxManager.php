<?php

namespace Meliconnect\Meliconnect\Core;

use Meliconnect\Meliconnect\Core\Controllers\SettingController;
use Meliconnect\Meliconnect\Core\Helpers\Helper;
use Meliconnect\Meliconnect\Core\Services\ProductEdit;
use Meliconnect\Meliconnect\Modules\Exporter\Controllers\ExportController;
use Meliconnect\Meliconnect\Modules\Importer\Controllers\ImportController;

class AjaxManager {

    public function __construct() {
    

        /* Settings Ajax */
        add_action('wp_ajax_melicon_settings_get_general_html', [SettingController::class, 'handleSettingsGetGeneralHtml']); //ajax_settings_nonce
        add_action('wp_ajax_melicon_settings_get_export_html', [SettingController::class, 'handleSettingsGetExportHtml']); //ajax_settings_nonce
        add_action('wp_ajax_melicon_settings_get_import_html', [SettingController::class, 'handleSettingsGetImportHtml']); //ajax_settings_nonce
        add_action('wp_ajax_melicon_settings_get_sync_html', [SettingController::class, 'handleSettingsGetSyncHtml']); //ajax_settings_nonce

        add_action('wp_ajax_melicon_save_general_settings', [SettingController::class, 'handleSaveGeneralSettings']); //ajax_settings_nonce
        add_action('wp_ajax_melicon_save_others_settings', [SettingController::class, 'handleSaveOthersSettings']); //ajax_settings_nonce

        /* Import Ajax */
        add_action('wp_ajax_melicon_get_meli_user_listings', [ImportController::class, 'handleGetMeliUserListings']);// get_meli_user_listings_nonce
        add_action('wp_ajax_melicon_reset_user_listings', [ImportController::class, 'handleResetMeliUserListings']);// reset_listings_nonce
        add_action('wp_ajax_melicon_init_import_process', [ImportController::class, 'handleInitImportProcess']);//init_import_process_nonce
        add_action('wp_ajax_melicon_cancel_custom_import', [ImportController::class, 'handleCancelCustomImport']);//cancel_custom_import_nonce
        add_action('wp_ajax_melicon_cancel_finished_processes', [ImportController::class, 'handleCancelFinishedProcesses']);//cancel_finished_processes_nonce
        add_action('wp_ajax_melicon_bulk_import_action', [ImportController::class, 'handleBulkImportAction']);//import_bulk_action_nonce
        add_action('wp_ajax_melicon_desvinculate_woo_product', [ImportController::class, 'handleDesvinculateWooProduct']);// desvinculate_product_nonce
        add_action('wp_ajax_melicon_match_listings_with_products', [ImportController::class, 'handleMatchListingsWithProducts']);// match_listings_with_products_nonce
        add_action('wp_ajax_melicon_clear_matches', [ImportController::class, 'handleClearMatches']);//clear_all_matches_nonce
        add_action ('wp_ajax_melicon_get_match_available_products', [ImportController::class, 'handleGetMatchAvailableProducts']);//get_match_available_products_nonce
        add_action('wp_ajax_melicon_apply_match', [ImportController::class, 'handleApplyMatch']);//apply_match_nonce
        add_action('wp_ajax_melicon_clear_selected_products_match', [ImportController::class, 'handleClearSelectedProductsMatch']);// get_match_available_products_nonce

        /* Export Ajax */
        add_action('wp_ajax_melicon_bulk_export_action', [ExportController::class, 'handleBulkExportAction']);// export_bulk_action_nonce
        add_action('wp_ajax_melicon_cancel_custom_export', [ExportController::class, 'handleCancelCustomExport']);// cancel_custom_export_nonce
        add_action('wp_ajax_melicon_desvinculate_listing', [ExportController::class, 'handleDesvinculateListing']);// desvinculate_product_nonce
        add_action('wp_ajax_melicon_clean_custom_export_process', [ExportController::class, 'handleCleanCustomExportProcess']);// clean_custom_export_nonce



        /* Product Edit Ajax */
        add_action('wp_ajax_melicon_load_meli_categories', [Helper::class, 'handleLoadMeliCategories']); // melicon_load_meli_categories_nonce
        add_action('wp_ajax_melicon_update_meli_category', [Helper::class, 'handleUpdateMeliCategory']); // melicon_update_meli_category_nonce
        add_action('wp_ajax_melicon_import_single_listing', [ProductEdit::class, 'handleImportSingleListing']);// melicon_import_single_listing_nonce
        add_action('wp_ajax_melicon_export_single_listing', [ProductEdit::class, 'handleExportSingleListing']);// melicon_export_single_listing_nonce
        add_action('wp_ajax_melicon_unlink_single_listing', [ProductEdit::class, 'handleUnlinkSingleListing']);// melicon_unlink_single_listing_nonce
        add_action('wp_ajax_melicon_save_template_data', [ProductEdit::class, 'handleSaveTemplateData']);// melicon_save_template_data_nonce

        /* Processes */
        add_action('wp_ajax_melicon_get_process_progress', [ImportController::class, 'handleImportProcess']);//get_process_progress_nonce

    }
}
