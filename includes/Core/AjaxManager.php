<?php

namespace StoreSync\Meliconnect\Core;

use StoreSync\Meliconnect\Core\Controllers\SettingController;
use StoreSync\Meliconnect\Core\Helpers\Helper;
use StoreSync\Meliconnect\Core\Services\ProductEdit;
use StoreSync\Meliconnect\Modules\Exporter\Controllers\ExportController;
use StoreSync\Meliconnect\Modules\Importer\Controllers\ImportController;

class AjaxManager {

    public function __construct() {
    

        /* Settings Ajax */
        add_action('wp_ajax_melicon_settings_get_general_html', [SettingController::class, 'handleSettingsGetGeneralHtml']);
        add_action('wp_ajax_melicon_settings_get_export_html', [SettingController::class, 'handleSettingsGetExportHtml']);
        add_action('wp_ajax_melicon_settings_get_import_html', [SettingController::class, 'handleSettingsGetImportHtml']);
        add_action('wp_ajax_melicon_settings_get_sync_html', [SettingController::class, 'handleSettingsGetSyncHtml']);

        add_action('wp_ajax_melicon_save_general_settings', [SettingController::class, 'handleSaveGeneralSettings']);
        add_action('wp_ajax_melicon_save_others_settings', [SettingController::class, 'handleSaveOthersSettings']);
        add_action('wp_ajax_melicon_download_settings', [SettingController::class, 'handleDownloadSettings']);

        /* Import Ajax */
        add_action('wp_ajax_melicon_get_meli_user_listings', [ImportController::class, 'handleGetMeliUserListings']);
        add_action('wp_ajax_melicon_reset_user_listings', [ImportController::class, 'handleResetMeliUserListings']);
        add_action('wp_ajax_melicon_init_import_process', [ImportController::class, 'handleInitImportProcess']);
        add_action('wp_ajax_melicon_pause_import_process', [ImportController::class, 'handlePauseImportProcess']);
        add_action('wp_ajax_melicon_cancel_custom_import', [ImportController::class, 'handleCancelCustomImport']);
        add_action('wp_ajax_melicon_cancel_finished_processes', [ImportController::class, 'handleCancelFinishedProcesses']);
        add_action('wp_ajax_melicon_bulk_import_action', [ImportController::class, 'handleBulkImportAction']);
        add_action('wp_ajax_melicon_desvinculate_woo_product', [ImportController::class, 'handleDesvinculateWooProduct']);
        add_action('wp_ajax_melicon_match_listings_with_products', [ImportController::class, 'handleMatchListingsWithProducts']);
        add_action('wp_ajax_melicon_clear_matches', [ImportController::class, 'handleClearMatches']);
        add_action ('wp_ajax_melicon_get_match_available_products', [ImportController::class, 'handleGetMatchAvailableProducts']);
        add_action('wp_ajax_melicon_apply_match', [ImportController::class, 'handleApplyMatch']);
        add_action('wp_ajax_melicon_clear_selected_products_match', [ImportController::class, 'handleClearSelectedProductsMatch']);

        /* Export Ajax */
        add_action('wp_ajax_melicon_bulk_export_action', [ExportController::class, 'handleBulkExportAction']);
        add_action('wp_ajax_melicon_cancel_custom_export', [ExportController::class, 'handleCancelCustomExport']);
        add_action('wp_ajax_melicon_desvinculate_listing', [ExportController::class, 'handleDesvinculateListing']);
        add_action('wp_ajax_melicon_clean_custom_export_process', [ExportController::class, 'handleCleanCustomExportProcess']);



        /* Product Edit Ajax */
        add_action('wp_ajax_melicon_load_meli_categories', [Helper::class, 'handleLoadMeliCategories']);
        add_action('wp_ajax_melicon_update_meli_category', [Helper::class, 'handleUpdateMeliCategory']);
        add_action('wp_ajax_melicon_import_single_listing', [ProductEdit::class, 'handleImportSingleListing']);
        add_action('wp_ajax_melicon_export_single_listing', [ProductEdit::class, 'handleExportSingleListing']);
        add_action('wp_ajax_melicon_unlink_single_listing', [ProductEdit::class, 'handleUnlinkSingleListing']);
        add_action('wp_ajax_melicon_save_template_data', [ProductEdit::class, 'handleSaveTemplateData']);

        /* Processes */
        add_action('wp_ajax_melicon_get_process_progress', [ImportController::class, 'handleImportProcess']);

    }
}
