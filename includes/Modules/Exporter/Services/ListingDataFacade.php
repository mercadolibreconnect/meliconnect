<?php

namespace Meliconnect\Meliconnect\Modules\Exporter\Services;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Meliconnect\Meliconnect\Core\Helpers\Helper;
use Meliconnect\Meliconnect\Core\Helpers\MeliconMeli;
use Meliconnect\Meliconnect\Core\Models\Template;
use Meliconnect\Meliconnect\Core\Models\UserConnection;
use Meliconnect\Meliconnect\Modules\Exporter\Models\ProductToExport;
use Meliconnect\Meliconnect\Modules\Exporter\Services\MercadoLibreListingAdapter;

/**
 * Orchestrates the process of obtaining transformed product data from
 * the MercadoLibreListingAdapter and creating or updating products in
 * WooCommerce.
 */

class ListingDataFacade {

	protected $mercadoLibreListingAdapter;

	public function __construct(
		MercadoLibreListingAdapter $mercadoLibreListingAdapter,
	) {
		$this->mercadoLibreListingAdapter = $mercadoLibreListingAdapter;
	}

	public function getAndExportListing( $meli_user_id, $woo_product_id, $template_id, $meliListingId = null, $sync_options = null ) {
		$meli_user_data = UserConnection::getUser( $meli_user_id );

		if ( ! $meli_user_data ) {
			Helper::logData( 'User not found: ' . $meli_user_id, 'custom-export' );
			return false;
		}

		// Obtener datos transformados desde el servidor usando el adaptador
		$exportedResponse = $this->mercadoLibreListingAdapter->getTransformedListingData( $meli_user_data, $woo_product_id, $template_id, $meliListingId, $sync_options );

		if ( ! isset( $exportedResponse['status'] ) || $exportedResponse['status'] !== 200 || ! isset( $exportedResponse['data'] ) || empty( $exportedResponse['data'] ) ) {
			Helper::logData( 'Hub error exporting listing data: ' . wp_json_encode( $exportedResponse ), 'custom-export' );
			return false;
		}

		$this->processExportedResponse( $exportedResponse, $woo_product_id );

		return $exportedResponse;
	}




	private function processExportedResponse( $exportedResponse, $woo_product_id ) {
		if ( ! isset( $exportedResponse['data'] ) || empty( $exportedResponse['data'] ) ) {
			return; // Si no hay datos, salimos del método
		}

		// Array para almacenar todos los errores
		$all_errors = array();

		foreach ( $exportedResponse['data'] as $listing ) {

			$item_errors        = $this->processListingItem( $woo_product_id, $listing['item'], 'item' );
			$description_errors = $this->processListingItem( $woo_product_id, $listing['description'], 'description' );

			// Acumular errores si existen
			if ( $item_errors ) {
				$all_errors['item'] = $item_errors;
			}
			if ( $description_errors ) {
				$all_errors['description'] = $description_errors;
			}
		}

		if ( ! empty( $all_errors ) ) {
			ProductToExport::update_product_to_export_status( $woo_product_id, 'failed', $all_errors );
		} else {
			ProductToExport::update_product_to_export_status( $woo_product_id, 'finished', '' );
		}

		update_post_meta( $woo_product_id, 'meliconnect_last_export_time', time() );

		// Si hay errores, almacenarlos en un array serializado en la base de datos
		if ( ! empty( $all_errors ) ) {
			update_post_meta( $woo_product_id, 'meliconnect_export_meli_errors', maybe_serialize( $all_errors ) );
			update_post_meta( $woo_product_id, 'meliconnect_export_meli_error_time', time() );
		} else {
			// Si no hay errores, eliminamos el campo de errores previo
			delete_post_meta( $woo_product_id, 'meliconnect_export_meli_errors' );
			delete_post_meta( $woo_product_id, 'meliconnect_export_meli_error_time' );
		}
	}



	private function processListingItem( $woo_product_id, $listing_item, $type ) {
		// Helper::logData('listing_item: ' . wp_json_encode($listing_item));

		if ( $listing_item['success'] === true ) {
			delete_post_meta( $woo_product_id, 'meliconnect_export_meli_errors' );
			delete_post_meta( $woo_product_id, 'meliconnect_export_meli_error_time' );

			ProductToExport::update_product_to_export_status( $woo_product_id, 'finished', '' );

			if ( $type === 'item' ) {
				$this->updateProductMetas( $woo_product_id, $listing_item );
			}

			return null; // No hay errores
		} else {
			$errors = isset( $listing_item['errors'] ) ? $listing_item['errors'] : array( 'Unknown error' );

			return $errors; // Retornar los errores para procesarlos en el array global
		}
	}

	private function updateProductMetas( $woo_product_id, $listing_item ) {

		$listing_item = $listing_item['body'];
		Helper::logData( 'listing_item: ' . wp_json_encode( $listing_item ) );

		update_post_meta( $woo_product_id, 'meliconnect_meli_seller_id', $listing_item['seller_id'] );
		update_post_meta( $woo_product_id, 'meliconnect_meli_category_id', $listing_item['category_id'] );
		update_post_meta( $woo_product_id, 'meliconnect_meli_listing_id', $listing_item['id'] );
		update_post_meta( $woo_product_id, 'meliconnect_meli_permalink', $listing_item['permalink'] );
		update_post_meta( $woo_product_id, 'meliconnect_meli_listing_type_id', $listing_item['listing_type_id'] );

		update_post_meta( $woo_product_id, 'meliconnect_meli_status', $listing_item['status'] );
		update_post_meta( $woo_product_id, 'meliconnect_meli_sub_status', $listing_item['sub_status'] );
		update_post_meta( $woo_product_id, 'meliconnect_meli_site_id', $listing_item['site_id'] );
		update_post_meta( $woo_product_id, 'meliconnect_meli_catalog_product_id', $listing_item['catalog_product_id'] );
		update_post_meta( $woo_product_id, 'meliconnect_meli_domain_id', $listing_item['domain_id'] );
		update_post_meta( $woo_product_id, 'meliconnect_meli_sold_quantity', $listing_item['sold_quantity'] );
		update_post_meta( $woo_product_id, 'meliconnect_meli_shipping_mode', $listing_item['shipping']['mode'] );
	}
}
