<?php

namespace Meliconnect\Meliconnect\Core\Models;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
class ProcessItems {

	private static $table_name;

	// Este método se llama automáticamente cuando se accede por primera vez a la clase
	public static function init() {
		global $wpdb;
		self::$table_name = $wpdb->prefix . 'meliconnect_process_items';
	}

	public static function updateProcessedItemStatus( $item_process_id, $status, $process_id ) {
		global $wpdb;

		self::init();

		$table_name = self::$table_name;

        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$result_item = $wpdb->query( $wpdb->prepare( "UPDATE {$table_name} SET process_status = %s WHERE id = %s", $status, $item_process_id ) );

		if ( $result_item !== false ) {

			// update Process
			$process = Process::updateProcessProgress( $process_id, true );

			return $process;
		}

		return false;
	}


	public static function createProcessItems( $process_id, $items ) {
		global $wpdb;

		self::init();

		$table_name = self::$table_name;

		// Asegúrate de que $items sea un array no vacío y de arrays asociativos
		if ( ! empty( $items ) && is_array( $items ) ) {
			foreach ( $items as $item ) {
				if ( is_array( $item ) ) {
					$wpdb->insert(
						$table_name,
						array(
							'process_id'      => $process_id,
							'meli_user_id'    => $item['meli_user_id'],
							'meli_listing_id' => $item['meli_listing_id'],
							'woo_product_id'  => $item['woo_product_id'],
							'template_id'     => $item['template_id'],
							'process_status'  => 'pending',
						)
					);
				}
			}
		}
	}

	public static function getProcessItems( $process_id, $limit = 100, $item_status = 'pending' ) {
		global $wpdb;

		self::init();

		$table_name = self::$table_name;

        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$items = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table_name} WHERE process_id = %s AND process_status = %s LIMIT %d", $process_id, $item_status, $limit ) );

		return $items;
	}

	public static function deleteItems( $process_id ) {
		global $wpdb;

		self::init();

		$table_name = self::$table_name;

        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$result = $wpdb->query( $wpdb->prepare( "DELETE FROM {$table_name} WHERE process_id = %s", $process_id ) );

		return $result;
	}
}
