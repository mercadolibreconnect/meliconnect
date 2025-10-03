<?php

namespace Meliconnect\Meliconnect\Core\Controllers;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Meliconnect\Meliconnect\Core\Helpers\FormHelper;
use Meliconnect\Meliconnect\Core\Helpers\Helper;
use Meliconnect\Meliconnect\Core\Interfaces\ControllerInterface;
use Meliconnect\Meliconnect\Core\Models\UserConnection;

class SettingController implements ControllerInterface {

	public function __construct() {
		wp_enqueue_media();
	}

	public function getData() {
		// Logic to get and return data
		$data = array();
		return $data;
	}



	/* START HANDLE AJAX METHODS */
	public static function handleSettingsGetGeneralHtml() {

		if ( ! isset( $_GET['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['nonce'] ) ), 'ajax_settings_nonce' ) ) {
			wp_send_json_error( array( 'message' => 'Nonce verification failed.' ) );
			wp_die();
		}

		$general_data = Helper::getMeliconnectOptions( 'general' );
        $sellers_with_free_plan = UserConnection::getSellersByPlanComparison( 'free' );

        
		header( 'Content-Type:  text/html' );

		include MELICONNECT_PLUGIN_ROOT . 'includes/Core/Views/Partials/Settings/general.php';

		die();
	}

	public static function handleSettingsGetExportHtml() {
		if ( ! isset( $_GET['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['nonce'] ) ), 'ajax_settings_nonce' ) ) {
			wp_send_json_error( array( 'message' => 'Nonce verification failed.' ) );
			wp_die();
		}

		$export_data = Helper::getMeliconnectOptions( 'export' );

		header( 'Content-Type:  text/html' );

		include MELICONNECT_PLUGIN_ROOT . 'includes/Core/Views/Partials/Settings/export.php';

		die();
	}

	public static function handleSettingsGetImportHtml() {
		if ( ! isset( $_GET['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['nonce'] ) ), 'ajax_settings_nonce' ) ) {
			wp_send_json_error( array( 'message' => 'Nonce verification failed.' ) );
			wp_die();
		}

		$import_data = Helper::getMeliconnectOptions( 'import' );

		header( 'Content-Type:  text/html' );

		include MELICONNECT_PLUGIN_ROOT . 'includes/Core/Views/Partials/Settings/import.php';

		die();
	}

	public static function handleSettingsGetSyncHtml() {
		if ( ! isset( $_GET['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['nonce'] ) ), 'ajax_settings_nonce' ) ) {
			wp_send_json_error( array( 'message' => 'Nonce verification failed.' ) );
			wp_die();
		}

		$sync_data = Helper::getMeliconnectOptions( 'sync' );
        $sellers_with_free_plan = UserConnection::getSellersByPlanComparison( 'free' );

		header( 'Content-Type:  text/html' );

		include MELICONNECT_PLUGIN_ROOT . 'includes/Core/Views/Partials/Settings/sync.php';

		die();
	}

	public static function handleSaveGeneralSettings() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'ajax_settings_nonce' ) ) {
			wp_send_json_error( array( 'message' => 'Nonce verification failed.' ) );
			wp_die();
		}

		// Verifica los permisos del usuario
		if ( ! current_user_can( 'meliconnect_manage_plugin' ) ) {
			wp_send_json_error( esc_html__( 'You do not have permission to perform this action', 'meliconnect' ) );
			return;
		}

		// Obtiene los datos enviados por AJAX
		$general_image_attachment_ids = array();

		if ( isset( $_POST['meliconnect_general_image_attachment_ids'] ) && is_array( $_POST['meliconnect_general_image_attachment_ids'] ) ) {
			$general_image_attachment_ids = array_map(
				'intval',
				wp_unslash( $_POST['meliconnect_general_image_attachment_ids'] )
			);
		}

		$general_description_template = isset( $_POST['meliconnect_general_description_template'] )
		? sanitize_text_field( wp_unslash( $_POST['meliconnect_general_description_template'] ) )
		: '';

		$general_sync_type = isset( $_POST['meliconnect_general_sync_type'] )
		? sanitize_text_field( wp_unslash( $_POST['meliconnect_general_sync_type'] ) )
		: '';

		$general_sync_items_batch = isset( $_POST['meliconnect_general_sync_items_batch'] )
		? intval( wp_unslash( $_POST['meliconnect_general_sync_items_batch'] ) )
		: 0;

		$general_sync_frecuency_minutes = isset( $_POST['meliconnect_general_sync_frecuency_minutes'] )
		? intval( wp_unslash( $_POST['meliconnect_general_sync_frecuency_minutes'] ) )
		: 0;

		$general_sync_method = isset( $_POST['meliconnect_general_sync_method'] )
		? sanitize_text_field( wp_unslash( $_POST['meliconnect_general_sync_method'] ) )
		: '';

		// Guarda los datos en las opciones de WooCommerce
		update_option( 'meliconnect_general_image_attachment_ids', $general_image_attachment_ids );
		update_option( 'meliconnect_general_description_template', $general_description_template );
		update_option( 'meliconnect_general_sync_type', $general_sync_type );
		update_option( 'meliconnect_general_sync_items_batch', $general_sync_items_batch );
		update_option( 'meliconnect_general_sync_frecuency_minutes', $general_sync_frecuency_minutes );
		update_option( 'meliconnect_general_sync_method', $general_sync_method );

		// Envía una respuesta de éxito
		wp_send_json_success( esc_html__( 'Settings saved successfully', 'meliconnect' ) );
	}

	public static function handleSaveExportSettings() {
		// Verificación del nonce
		if ( ! isset( $_POST['nonce'] ) ||
		! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'ajax_settings_nonce' ) ) {
			wp_send_json_error( array( 'message' => 'Nonce verification failed.' ) );
			wp_die();
		}

		// Verificación de permisos
		if ( ! current_user_can( 'meliconnect_manage_plugin' ) ) {
			wp_send_json_error( esc_html__( 'You do not have permission to perform this action', 'meliconnect' ) );
			return;
		}

		$export_title          = isset( $_POST['meliconnect_export_title'] ) ? sanitize_text_field( wp_unslash( $_POST['meliconnect_export_title'] ) ) : '';
		$export_stock          = isset( $_POST['meliconnect_export_stock'] ) ? sanitize_text_field( wp_unslash( $_POST['meliconnect_export_stock'] ) ) : '';
		$export_price          = isset( $_POST['meliconnect_export_price'] ) ? sanitize_text_field( wp_unslash( $_POST['meliconnect_export_price'] ) ) : '';
		$export_images         = isset( $_POST['meliconnect_export_images'] ) ? sanitize_text_field( wp_unslash( $_POST['meliconnect_export_images'] ) ) : '';
		$export_sku            = isset( $_POST['meliconnect_export_sku'] ) ? sanitize_text_field( wp_unslash( $_POST['meliconnect_export_sku'] ) ) : '';
		$export_attributes     = isset( $_POST['meliconnect_export_product_attributes'] ) ? sanitize_text_field( wp_unslash( $_POST['meliconnect_export_product_attributes'] ) ) : '';
		$export_ml_status      = isset( $_POST['meliconnect_export_ml_status'] ) ? sanitize_text_field( wp_unslash( $_POST['meliconnect_export_ml_status'] ) ) : '';
		$export_variations     = isset( $_POST['meliconnect_export_variations'] ) ? sanitize_text_field( wp_unslash( $_POST['meliconnect_export_variations'] ) ) : '';
		$export_description    = isset( $_POST['meliconnect_export_description'] ) ? sanitize_text_field( wp_unslash( $_POST['meliconnect_export_description'] ) ) : '';
		$export_description_to = isset( $_POST['meliconnect_export_description_to'] ) ? sanitize_text_field( wp_unslash( $_POST['meliconnect_export_description_to'] ) ) : '';
		$export_type           = isset( $_POST['meliconnect_export_type'] ) ? sanitize_text_field( wp_unslash( $_POST['meliconnect_export_type'] ) ) : '';
		$export_finalize_ml    = isset( $_POST['meliconnect_export_finalize_ml'] ) ? sanitize_text_field( wp_unslash( $_POST['meliconnect_export_finalize_ml'] ) ) : '';

		update_option( 'meliconnect_export_title', $export_title );
		update_option( 'meliconnect_export_stock', $export_stock );
		update_option( 'meliconnect_export_price', $export_price );
		update_option( 'meliconnect_export_images', $export_images );
		update_option( 'meliconnect_export_sku', $export_sku );
		update_option( 'meliconnect_export_product_attributes', $export_attributes );
		update_option( 'meliconnect_export_ml_status', $export_ml_status );
		update_option( 'meliconnect_export_variations', $export_variations );
		update_option( 'meliconnect_export_description', $export_description );
		update_option( 'meliconnect_export_description_to', $export_description_to );
		update_option( 'meliconnect_export_type', $export_type );
		update_option( 'meliconnect_export_finalize_ml', $export_finalize_ml );

		/* Checkboxes */
		$checkbox_fields = array(
			'meliconnect_export_is_disabled',
			'meliconnect_export_state_paused',
			'meliconnect_export_state_closed',
		);

		foreach ( $checkbox_fields as $checkbox ) {
			update_option( $checkbox, isset( $_POST[ $checkbox ] ) && 'true' === $_POST[ $checkbox ] ? 'true' : 'false' );
		}

		wp_send_json_success( esc_html__( 'Export settings saved successfully', 'meliconnect' ) );
	}

	public static function handleSaveImportSettings() {
		// Verificación del nonce
		if ( ! isset( $_POST['nonce'] ) ||
		! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'ajax_settings_nonce' ) ) {
			wp_send_json_error( array( 'message' => 'Nonce verification failed.' ) );
			wp_die();
		}

		// Verificación de permisos
		if ( ! current_user_can( 'meliconnect_manage_plugin' ) ) {
			wp_send_json_error( esc_html__( 'You do not have permission to perform this action', 'meliconnect' ) );
			return;
		}

		$import_title          = isset( $_POST['meliconnect_import_title'] ) ? sanitize_text_field( wp_unslash( $_POST['meliconnect_import_title'] ) ) : '';
		$import_stock          = isset( $_POST['meliconnect_import_stock'] ) ? sanitize_text_field( wp_unslash( $_POST['meliconnect_import_stock'] ) ) : '';
		$import_price          = isset( $_POST['meliconnect_import_price'] ) ? sanitize_text_field( wp_unslash( $_POST['meliconnect_import_price'] ) ) : '';
		$import_images         = isset( $_POST['meliconnect_import_images'] ) ? sanitize_text_field( wp_unslash( $_POST['meliconnect_import_images'] ) ) : '';
		$import_sku            = isset( $_POST['meliconnect_import_sku'] ) ? sanitize_text_field( wp_unslash( $_POST['meliconnect_import_sku'] ) ) : '';
		$import_categories     = isset( $_POST['meliconnect_import_categories'] ) ? sanitize_text_field( wp_unslash( $_POST['meliconnect_import_categories'] ) ) : '';
		$import_attributes     = isset( $_POST['meliconnect_import_product_attributes'] ) ? sanitize_text_field( wp_unslash( $_POST['meliconnect_import_product_attributes'] ) ) : '';
		$import_ml_status      = isset( $_POST['meliconnect_import_ml_status'] ) ? sanitize_text_field( wp_unslash( $_POST['meliconnect_import_ml_status'] ) ) : '';
		$import_variations     = isset( $_POST['meliconnect_import_variations'] ) ? sanitize_text_field( wp_unslash( $_POST['meliconnect_import_variations'] ) ) : '';
		$import_variations_as  = isset( $_POST['meliconnect_import_variations_as'] ) ? sanitize_text_field( wp_unslash( $_POST['meliconnect_import_variations_as'] ) ) : '';
		$import_description    = isset( $_POST['meliconnect_import_description'] ) ? sanitize_text_field( wp_unslash( $_POST['meliconnect_import_description'] ) ) : '';
		$import_description_to = isset( $_POST['meliconnect_import_description_to'] ) ? sanitize_text_field( wp_unslash( $_POST['meliconnect_import_description_to'] ) ) : '';
		$import_type           = isset( $_POST['meliconnect_import_type'] ) ? sanitize_text_field( wp_unslash( $_POST['meliconnect_import_type'] ) ) : '';

		// price variation
		$import_price_variation_operand = isset( $_POST['meliconnect_import_price_variation_operand'] ) ? sanitize_text_field( wp_unslash( $_POST['meliconnect_import_price_variation_operand'] ) ) : '';
		$import_price_variation_amount  = isset( $_POST['meliconnect_import_price_variation_amount'] ) ? intval( wp_unslash( $_POST['meliconnect_import_price_variation_amount'] ) ) : 0;
		$import_price_variation_type    = isset( $_POST['meliconnect_import_price_variation_type'] ) ? sanitize_text_field( wp_unslash( $_POST['meliconnect_import_price_variation_type'] ) ) : '';

		// stock variation
		$import_stock_variation_operand = isset( $_POST['meliconnect_import_stock_variation_operand'] ) ? sanitize_text_field( wp_unslash( $_POST['meliconnect_import_stock_variation_operand'] ) ) : '';
		$import_stock_variation_amount  = isset( $_POST['meliconnect_import_stock_variation_amount'] ) ? intval( wp_unslash( $_POST['meliconnect_import_stock_variation_amount'] ) ) : 0;
		$import_stock_variation_type    = isset( $_POST['meliconnect_import_stock_variation_type'] ) ? sanitize_text_field( wp_unslash( $_POST['meliconnect_import_stock_variation_type'] ) ) : '';

		update_option( 'meliconnect_import_title', $import_title );
		update_option( 'meliconnect_import_stock', $import_stock );
		update_option( 'meliconnect_import_price', $import_price );
		update_option( 'meliconnect_import_images', $import_images );
		update_option( 'meliconnect_import_sku', $import_sku );
		update_option( 'meliconnect_import_categories', $import_categories );
		update_option( 'meliconnect_import_product_attributes', $import_attributes );
		update_option( 'meliconnect_import_ml_status', $import_ml_status );
		update_option( 'meliconnect_import_variations', $import_variations );
		update_option( 'meliconnect_import_variations_as', $import_variations_as );
		update_option( 'meliconnect_import_description', $import_description );
		update_option( 'meliconnect_import_description_to', $import_description_to );
		update_option( 'meliconnect_import_type', $import_type );

		update_option( 'meliconnect_import_price_variation_operand', $import_price_variation_operand );
		update_option( 'meliconnect_import_price_variation_amount', $import_price_variation_amount );
		update_option( 'meliconnect_import_price_variation_type', $import_price_variation_type );

		update_option( 'meliconnect_import_stock_variation_operand', $import_stock_variation_operand );
		update_option( 'meliconnect_import_stock_variation_amount', $import_stock_variation_amount );
		update_option( 'meliconnect_import_stock_variation_type', $import_stock_variation_type );

		$checkbox_fields = array(
			'meliconnect_import_is_disabled',
			'meliconnect_import_state_paused',
			'meliconnect_import_state_closed',
			'meliconnect_import_by_sku',
			'meliconnect_import_attrs',
		);

		foreach ( $checkbox_fields as $checkbox ) {
			update_option( $checkbox, isset( $_POST[ $checkbox ] ) && 'true' === $_POST[ $checkbox ] ? 'true' : 'false' );
		}

		wp_send_json_success( esc_html__( 'Import settings saved successfully', 'meliconnect' ) );
	}


	public static function handleSaveSyncSettings() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'ajax_settings_nonce' ) ) {
			wp_send_json_error( array( 'message' => 'Nonce verification failed.' ) );
			wp_die();
		}

		// Verifica permisos
		if ( ! current_user_can( 'meliconnect_manage_plugin' ) ) {
			wp_send_json_error( esc_html__( 'You do not have permission to perform this action', 'meliconnect' ) );
			return;
		}

		$data = array();

		// Cron status: solo "active" o "inactive"
		$allowed_status                       = array( 'active', 'inactive' );
		$data['meliconnect_sync_cron_status'] = ( isset( $_POST['meliconnect_sync_cron_status'] ) && in_array( $_POST['meliconnect_sync_cron_status'], $allowed_status, true ) )
		? sanitize_text_field( wp_unslash( $_POST['meliconnect_sync_cron_status'] ) )
		: 'inactive';

		// Items batch: número entero
		$data['meliconnect_sync_cron_items_batch'] = isset( $_POST['meliconnect_sync_cron_items_batch'] )
		? intval( $_POST['meliconnect_sync_cron_items_batch'] )
		: 10;

		// Frecuencia: número entero (mínimo 1)
		$data['meliconnect_sync_cron_frecuency_minutes'] = isset( $_POST['meliconnect_sync_cron_frecuency_minutes'] )
		? max( 1, intval( $_POST['meliconnect_sync_cron_frecuency_minutes'] ) )
		: 10;

		// Método: solo "WordPress" o "custom"
		$allowed_methods                      = array( 'wordpress', 'custom' );
		$data['meliconnect_sync_cron_method'] = ( isset( $_POST['meliconnect_sync_cron_method'] ) && in_array( $_POST['meliconnect_sync_cron_method'], $allowed_methods, true ) )
		? sanitize_text_field( wp_unslash( $_POST['meliconnect_sync_cron_method'] ) )
		: 'WordPress';

		// Checkboxes individuales (se guardan como "true"/"false")
		$checkboxes = array(
			'meliconnect_sync_stock_woo_to_meli',
			'meliconnect_sync_price_woo_to_meli',
			'meliconnect_sync_status_woo_to_meli',
			'meliconnect_sync_stock_meli_to_woo',
			'meliconnect_sync_price_meli_to_woo',
			'meliconnect_sync_variations_price_meli_to_woo',
		);

		foreach ( $checkboxes as $cb ) {
			$data[ $cb ] = ( isset( $_POST[ $cb ] ) && 'true' === $_POST[ $cb ] ) ? 'true' : 'false';
		}

		// Guardar en opciones (ejemplo)
		foreach ( $data as $key => $value ) {
			update_option( $key, $value );
		}

		wp_send_json_success(
			array(
				'message' => __( 'Sync settings saved successfully.', 'meliconnect' ),
				'data'    => $data,
			)
		);
	}




	/* START CUSTOM METHODS */
	public static function print_setting_checkbox( $key, $label, $value = '', $check_compare_value = true, $helpText = '' ) {
		FormHelper::print_checkbox( $key, $label, $value, $check_compare_value, $helpText );
	}

	public static function print_setting_select( $key, $label, $selected_value_key = '', $options = null ) {
		// Definir las opciones por defecto traducibles
		if ( $options === null ) {
			$options = array(
				'always'    => esc_html__( 'Always', 'meliconnect' ),
				'on_update' => esc_html__( 'On Update', 'meliconnect' ),
				'on_create' => esc_html__( 'On Create', 'meliconnect' ),
			);
		}

		echo '<div class="meliconnect-columns">
            <div class="meliconnect-column meliconnect-$&">
                <label for="' . esc_attr( $key ) . '" class="meliconnect-label">' . esc_html( $label ) . '</label>
            </div>
            <div class="meliconnect-column meliconnect-is-8">
                <div class="meliconnect-select meliconnect-is-fullwidth">
                    <select name="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '">';

		foreach ( $options as $option_key => $option_value ) {
			echo '<option value="' . esc_attr( $option_key ) . '" ' . selected( $selected_value_key, $option_key, false ) . '>' . esc_html( $option_value ) . '</option>';
		}

		echo '</select>
                </div>
            </div>
        </div>';
	}
}
