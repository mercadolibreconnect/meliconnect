<?php

namespace Meliconnect\Meliconnect\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


use Meliconnect\Meliconnect\Core\Controllers\SettingController;
use Meliconnect\Meliconnect\Core\Helpers\Helper;
use Meliconnect\Meliconnect\Core\Services\ProductEdit;
use Meliconnect\Meliconnect\Modules\Exporter\Controllers\ExportController;
use Meliconnect\Meliconnect\Modules\Importer\Controllers\ImportController;

class AjaxManager {

	public function __construct() {

		/* Settings Ajax */
		add_action( 'wp_ajax_meliconnect_settings_get_general_html', array( SettingController::class, 'handleSettingsGetGeneralHtml' ) ); // ajax_settings_nonce
		add_action( 'wp_ajax_meliconnect_settings_get_export_html', array( SettingController::class, 'handleSettingsGetExportHtml' ) ); // ajax_settings_nonce
		add_action( 'wp_ajax_meliconnect_settings_get_import_html', array( SettingController::class, 'handleSettingsGetImportHtml' ) ); // ajax_settings_nonce
		add_action( 'wp_ajax_meliconnect_settings_get_sync_html', array( SettingController::class, 'handleSettingsGetSyncHtml' ) ); // ajax_settings_nonce

		add_action( 'wp_ajax_meliconnect_save_general_settings', array( SettingController::class, 'handleSaveGeneralSettings' ) ); // ajax_settings_nonce
        add_action( 'wp_ajax_meliconnect_save_export_settings', array( SettingController::class, 'handleSaveExportSettings' ) ); // ajax_settings_nonce
        add_action( 'wp_ajax_meliconnect_save_import_settings', array( SettingController::class, 'handleSaveImportSettings' ) ); // ajax_settings_nonce
        add_action( 'wp_ajax_meliconnect_save_sync_settings', array( SettingController::class, 'handleSaveSyncSettings' ) ); // ajax_settings_nonce
        

		/* Import Ajax */
		add_action( 'wp_ajax_meliconnect_get_meli_user_listings', array( ImportController::class, 'handleGetMeliUserListings' ) );// get_meli_user_listings_nonce
		add_action( 'wp_ajax_meliconnect_reset_user_listings', array( ImportController::class, 'handleResetMeliUserListings' ) );// reset_listings_nonce
		add_action( 'wp_ajax_meliconnect_init_import_process', array( ImportController::class, 'handleInitImportProcess' ) );// init_import_process_nonce
		add_action( 'wp_ajax_meliconnect_cancel_custom_import', array( ImportController::class, 'handleCancelCustomImport' ) );// cancel_custom_import_nonce
		add_action( 'wp_ajax_meliconnect_cancel_finished_processes', array( ImportController::class, 'handleCancelFinishedProcesses' ) );// cancel_finished_processes_nonce
		add_action( 'wp_ajax_meliconnect_bulk_import_action', array( ImportController::class, 'handleBulkImportAction' ) );// import_bulk_action_nonce
		add_action( 'wp_ajax_meliconnect_desvinculate_woo_product', array( ImportController::class, 'handleDesvinculateWooProduct' ) );// desvinculate_product_nonce
		add_action( 'wp_ajax_meliconnect_match_listings_with_products', array( ImportController::class, 'handleMatchListingsWithProducts' ) );// match_listings_with_products_nonce
		add_action( 'wp_ajax_meliconnect_clear_matches', array( ImportController::class, 'handleClearMatches' ) );// clear_all_matches_nonce
		add_action( 'wp_ajax_meliconnect_get_match_available_products', array( ImportController::class, 'handleGetMatchAvailableProducts' ) );// get_match_available_products_nonce
		add_action( 'wp_ajax_meliconnect_apply_match', array( ImportController::class, 'handleApplyMatch' ) );// apply_match_nonce
		add_action( 'wp_ajax_meliconnect_clear_selected_products_match', array( ImportController::class, 'handleClearSelectedProductsMatch' ) );// get_match_available_products_nonce

		/* Export Ajax */
		add_action( 'wp_ajax_meliconnect_bulk_export_action', array( ExportController::class, 'handleBulkExportAction' ) );// export_bulk_action_nonce
		add_action( 'wp_ajax_meliconnect_cancel_custom_export', array( ExportController::class, 'handleCancelCustomExport' ) );// cancel_custom_export_nonce
		add_action( 'wp_ajax_meliconnect_desvinculate_listing', array( ExportController::class, 'handleDesvinculateListing' ) );// desvinculate_product_nonce
		add_action( 'wp_ajax_meliconnect_clean_custom_export_process', array( ExportController::class, 'handleCleanCustomExportProcess' ) );// clean_custom_export_nonce

		/* Product Edit Ajax */
		add_action( 'wp_ajax_meliconnect_load_meli_categories', array( Helper::class, 'handleLoadMeliCategories' ) ); // meliconnect_load_meli_categories_nonce
		add_action( 'wp_ajax_meliconnect_update_meli_category', array( Helper::class, 'handleUpdateMeliCategory' ) ); // meliconnect_update_meli_category_nonce
		add_action( 'wp_ajax_meliconnect_import_single_listing', array( ProductEdit::class, 'handleImportSingleListing' ) );// meliconnect_import_single_listing_nonce
		add_action( 'wp_ajax_meliconnect_export_single_listing', array( ProductEdit::class, 'handleExportSingleListing' ) );// meliconnect_export_single_listing_nonce
		add_action( 'wp_ajax_meliconnect_unlink_single_listing', array( ProductEdit::class, 'handleUnlinkSingleListing' ) );// meliconnect_unlink_single_listing_nonce
		add_action( 'wp_ajax_meliconnect_save_template_data', array( ProductEdit::class, 'handleSaveTemplateData' ) );// meliconnect_save_template_data_nonce

		/* Processes */
		add_action( 'wp_ajax_meliconnect_get_process_progress', array( ImportController::class, 'handleImportProcess' ) );// get_process_progress_nonce
	}
}
