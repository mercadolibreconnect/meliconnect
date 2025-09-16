<?php

namespace Meliconnect\Meliconnect\Core\Models;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Meliconnect\Meliconnect\Core\Helpers\Helper;

class Template {

	private static $table_name;

	// shipping_mode, free_shipping, has_free_shipping_mandatory, logistic_type, store_pick_up, local_pick_up, warranty_type, warranty_time

	private static $template_listing_metas = array(
		'site_id',
		'permalink',
		'user_product_id',
		'family_name',
		'official_store_id',
		'currency_id',
		'shipping',
		'tags',
		'warranty',
		'catalog_domain',
		'catalog_listing',
		'catalog_product_id',
		'domain_id',
		'inventory_id',
		'manufacturing_time',
		'condition',
		'listing_type_id',
		'sale_terms',
		'manufacturing_time_unit',
		'warranty_type',
		'warranty_time',
		'warranty_time_unit',
		'buying_mode',
		'status',
		'local_pick_up',
		'free_shipping',
	);

	private static $template_custom_metas = array(
		'price_create_method'   => 'regular_price',
		'price_operand'         => null,
		'price_amount'          => null,
		'price_type'            => null,
		'stock_operand'         => null,
		'stock_amount'          => null,
		'stock_type'            => null,
		'has_sync'              => 1,
		'has_multiple_products' => 0,
		'title_structure'       => null,
		'description_structure' => null,
	);

	// Metas that will be deleted and updated when user saves post
	public static $template_woo_post_metas = array(
		'currency_id',
		'manufacturing_time',
		'condition',
		'listing_type_id',
		'manufacturing_time_unit',
		'warranty_type',
		'warranty_time',
		'warranty_time_unit',
		'buying_mode',
		'catalog_listing',
		'status',
		'official_store_id',
	);

	// Este método se llama automáticamente cuando se accede por primera vez a la clase
	public static function init() {
		global $wpdb;

		self::$table_name = $wpdb->prefix . 'meliconnect_templates';
	}



	public static function createUpdateProductTemplateFromPost( $template_post_data, $woo_product_id, $woo_product_title ) {

		$category_tree = $template_post_data['subcategory_tree'];

		$data = array(
			'used_by'            => 'product',
			'used_asoc_id'       => $woo_product_id,
			'template_parent_id' => null,
			'seller_meli_id'     => $template_post_data['seller_meli_id'],
			'name'               => $woo_product_title,
			'short_description'  => '',
			'category_id'        => $template_post_data['category_id'],
			'category_name'      => $template_post_data['category_name'],
			'category_path'      => stripslashes( $category_tree ),
			'channels'           => $template_post_data['channels'],
			'status'             => 1,
		);

		$template_id = self::createUpdateTemplate( $data );

		if ( ! $template_id ) {
			return false;
		}

		self::addCheckboxesValuesToRequest( $template_post_data['meta'] );

		// Adds values not present in edit form to request as meta
		self::addCurrentTemplateMetasToRequest( $template_id, $template_post_data['meta'] );

		self::deleteTemplatesMetas( $template_id );

		// template specific metas
		self::createCustomTemplateMetas( $template_id, $template_post_data['meta'] );

		// meli listings metas
		self::createTemplateMetasFromPost( $template_id, $template_post_data['meta'] );

		self::deleteTemplatesAttributes( $template_id );

		self::createProductTemplateAttrsFromPostData( $template_post_data, $template_id, $woo_product_id );

		return $template_id;
	}


	public static function createProductTemplateAttrsFromPostData( $template_post_data, $template_id, $product_id ) {
		global $wpdb;

		$table_name = $wpdb->prefix . 'meliconnect_template_attributes';

		if ( empty( $template_post_data['attrs'] ) ) {
			Helper::logData( 'attrs not found in post data' );
			return false;
		}

		foreach ( $template_post_data['attrs'] as $key => $attr ) {
			$decoded_attr = json_decode( stripslashes( $attr ), true ); // Decodificar JSON a array

			if ( json_last_error() === JSON_ERROR_NONE ) {

				$woo_taxonomy_slug = $decoded_attr['woo']['name'];

				$value_data = self::getMeliOrWooValue( $decoded_attr, $product_id, $woo_taxonomy_slug );

				$meli_value_id   = $value_data['meli_value_id'];
				$meli_value_name = $value_data['meli_value_name'];

				// Preparar los datos para la inserción en la tabla
				$data_to_insert = array(
					'template_id'             => $template_id,
					'used_by'                 => 'product',
					'used_asoc_id'            => $product_id,
					'meli_variation_id'       => null, // Se puede ajustar según el caso
					'meli_attribute_id'       => $decoded_attr['meli']['id'],
					'meli_attribute_name'     => $decoded_attr['meli']['name'],
					'meli_value_id'           => $meli_value_id,
					'meli_value_name'         => $meli_value_name,
					'meli_value_type'         => $decoded_attr['meli']['value_type'],
					'woo_attribute_id'        => $decoded_attr['woo']['attribute_id'],
					'allow_variations_tag'    => isset( $decoded_attr['meli']['tags']['allow_variations'] ) ? (int) $decoded_attr['meli']['tags']['allow_variations'] : 0,
					'variation_attribute_tag' => isset( $decoded_attr['meli']['tags']['variation_attribute'] ) ? (int) $decoded_attr['meli']['tags']['variation_attribute'] : 0,
					'required_tag'            => isset( $decoded_attr['meli']['tags']['required'] ) ? (int) $decoded_attr['meli']['tags']['required'] : 0,
					'not_apply'               => 0, // Puedes ajustar esto según tus reglas
				);

				/*
				echo PHP_EOL . '-------------------- $data_to_insert --------------------' . PHP_EOL;
				echo '<pre>' . wp_json_encode($data_to_insert) . '</pre>';
				echo PHP_EOL . '-------------------  FINISHED  ---------------------' . PHP_EOL;

				echo PHP_EOL . '-------------------- $decoded_attr --------------------' . PHP_EOL;
				echo '<pre>' . wp_json_encode($decoded_attr) . '</pre>';
				echo PHP_EOL . '-------------------  FINISHED  ---------------------' . PHP_EOL;
				wp_die(); */

				// Insertar los datos en la base de datos
				$wpdb->insert( $table_name, $data_to_insert );
			} else {
				// Manejar el error de JSON si no se puede decodificar
				Helper::logData( "Error decodificando el atributo en línea $key: " . json_last_error() );
			}
		}
	}

	public static function getMeliOrWooValue( $decoded_attr, $product_id, $woo_taxonomy_slug ) {
		// Verificar si el atributo de MercadoLibre tiene valores posibles
		if ( ! empty( $decoded_attr['meli']['values'] ) ) {
			// Intentar hacer coincidir la taxonomía de WooCommerce con el atributo de MercadoLibre
			$matched_value = self::matchWooTaxonomyWithMeli( $product_id, $decoded_attr['meli'], $woo_taxonomy_slug );

			if ( $matched_value ) {
				// Si hay coincidencia, usar el valor de MercadoLibre coincidente
				return array(
					'meli_value_id'   => $matched_value['id'],
					'meli_value_name' => $matched_value['name'],
				);
			} else {
				// Si no hay coincidencia, usar los valores por defecto de MercadoLibre
				return array(
					'meli_value_id'   => isset( $decoded_attr['meli']['values'][0]['id'] ) ? $decoded_attr['meli']['values'][0]['id'] : null,
					'meli_value_name' => isset( $decoded_attr['meli']['values'][0]['name'] ) ? $decoded_attr['meli']['values'][0]['name'] : null,
				);
			}
		} else {
			// Si no hay valores posibles, usar el valor de la taxonomía de WooCommerce directamente
			$woo_taxonomy_values = wp_get_post_terms( $product_id, $woo_taxonomy_slug, array( 'fields' => 'names' ) );

			if ( ! empty( $woo_taxonomy_values ) && ! is_wp_error( $woo_taxonomy_values ) ) {
				return array(
					'meli_value_id'   => null, // No se requiere ID para valores sin coincidencias de MercadoLibre
					'meli_value_name' => $woo_taxonomy_values[0],
				);
			} else {
				return array(
					'meli_value_id'   => null,
					'meli_value_name' => null, // No hay valor disponible
				);
			}
		}
	}

	/**
	 * Función para hacer coincidir la taxonomía de WooCommerce con el atributo de MercadoLibre
	 */
	public static function matchWooTaxonomyWithMeli( $product_id, $meli_attr, $woo_taxonomy_slug ) {
		// Obtener el valor actual de la taxonomía de WooCommerce para el producto
		$woo_taxonomy_values = wp_get_post_terms( $product_id, $woo_taxonomy_slug, array( 'fields' => 'names' ) );

		if ( empty( $woo_taxonomy_values ) || is_wp_error( $woo_taxonomy_values ) ) {
			return null; // No hay valor para la taxonomía
		}

		// Obtener el primer valor de la taxonomía
		$woo_value = $woo_taxonomy_values[0];

		// Iterar sobre los posibles valores de MercadoLibre para encontrar una coincidencia
		foreach ( $meli_attr['values'] as $meli_value ) {
			if ( strcasecmp( $woo_value, $meli_value['name'] ) == 0 ) {
				// Coincidencia encontrada
				return $meli_value;
			}
		}

		// Si no hay coincidencia, devolver null
		return null;
	}

	public static function addCheckboxesValuesToRequest( &$post ) {
		$form_checkboxes_keys = array( 'local_pick_up', 'free_shipping', 'catalog_listing' );

		foreach ( $form_checkboxes_keys as $checkbox_key ) {
			if ( ! isset( $post[ $checkbox_key ] ) || empty( $post[ $checkbox_key ] ) ) {
				$post[ $checkbox_key ] = false;
			} else {
				$post[ $checkbox_key ] = true;
			}
		}
	}

	public static function addCurrentTemplateMetasToRequest( $template_id, &$post ) {

		$current_template_metas = self::getTemplateData( $template_id );
		$added_keys             = array();

		foreach ( $current_template_metas as $current_meta_key => $current_meta_value ) {
			// Validar que la clave no esté en el array $template_custom_metas
			if ( in_array( $current_meta_key, self::$template_listing_metas ) ) {
				if ( ! array_key_exists( $current_meta_key, $post ) ) {
					// Si la clave no existe, agregar al post con el valor actual
					$post[ $current_meta_key ] = $current_meta_value;
					$added_keys[]              = $current_meta_key;
				}
			}
		}

		/*
		echo PHP_EOL . '-------------------- added_keys --------------------' . PHP_EOL;
		echo '<pre>' . wp_json_encode( $added_keys) . '</pre>';
		echo PHP_EOL . '-------------------  FINISHED  ---------------------' . PHP_EOL;

		wp_die(); */
	}


	public static function createUpdateTemplateFromMeliListing( $used_by, $used_asoc_id, $meli_listing_data, $template_parent_id = null ) {

		$channels       = isset( $meli_listing_data->channels ) ? $meli_listing_data->channels : array();
		$channels_value = self::determineChannelsValue( $channels );

		$data = array(
			'used_by'            => $used_by,
			'used_asoc_id'       => $used_asoc_id,
			'template_parent_id' => $template_parent_id,
			'seller_meli_id'     => $meli_listing_data->seller_id,
			'name'               => $meli_listing_data->title,
			'short_description'  => esc_html__( 'Template autogenerated from meli listing', 'meliconnect' ),
			'category_id'        => $meli_listing_data->category_id,
			'category_name'      => null,
			'category_path'      => null,
			'channels'           => $channels_value,
			'status'             => 1,
		);

		return self::createUpdateTemplate( $data );
	}

	public static function createUpdateTemplate( $data ) {
		if ( ! isset( $data['used_by'] ) || empty( $data['used_by'] ) ) {
			Helper::logData( 'Error creating template: used_by is required' );
			return false;
		}

		if ( ! isset( $data['used_asoc_id'] ) || empty( $data['used_asoc_id'] ) ) {
			Helper::logData( 'Error creating template: used_asoc_id is required' );
			return false;
		}

		global $wpdb;

		self::init();

		$template_table_name = self::$table_name;

        // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$existing_template = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT id FROM $template_table_name WHERE used_by = %s AND used_asoc_id = %d",
				$data['used_by'],
				$data['used_asoc_id']
			)
		);

		if ( $existing_template ) {
			// Realizar un UPDATE si el template ya existe
			$wpdb->update(
				$template_table_name,
				$data,
				array( 'id' => $existing_template->id )
			);

			if ( $wpdb->last_error ) {
				Helper::logData( 'Error updating template: ' );
				Helper::logData( $wpdb->last_error );
				Helper::logData( 'SQL: ' . $wpdb->last_query );
				return false;
			}

			return $existing_template->id;
		} else {
			// Realizar un INSERT si el template no existe
			$wpdb->insert( $template_table_name, $data );

			if ( $wpdb->last_error ) {
				Helper::logData( 'Error creating template: ' );
				Helper::logData( $wpdb->last_error );
				Helper::logData( 'SQL: ' . $wpdb->last_query );
				return false;
			}

			return $wpdb->insert_id;
		}

        // phpcs:enable
	}

	public static function deleteCreateTemplatesMetasFromMeliListing( $template_id, $meli_listing_data, $template_data = array() ) {
		self::deleteTemplatesMetas( $template_id );

		self::createTemplateMetasFromMeliListing( $template_id, $meli_listing_data );

		self::createCustomTemplateMetas( $template_id, $template_data );

		return true;
	}

	public static function deleteTemplatesMetas( $template_id, $meta_keys = array( 'all' ) ) {
		global $wpdb;
        // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare, WordPress.DB.DirectDatabaseQuery.NoCaching
		$template_metas_table_name = $wpdb->prefix . 'meliconnect_template_metas';

		if ( in_array( 'all', $meta_keys ) ) {
			$deleted = $wpdb->delete( $template_metas_table_name, array( 'template_id' => $template_id ) ) !== false;

			if ( $wpdb->last_error ) {
				Helper::logData( 'Error deleting all template metas: ' . $wpdb->last_error );
				return false;
			}

			return $deleted;
		}

		// Preparar la lista de meta_keys para la consulta SQL
		$placeholders = implode( ',', array_fill( 0, count( $meta_keys ), '%s' ) );

		$deleted = $wpdb->query(
			$wpdb->prepare(
				"DELETE FROM $template_metas_table_name WHERE template_id = %d AND meta_key IN ($placeholders)",
				array_merge( array( $template_id ), $meta_keys )
			)
		);

		if ( $wpdb->last_error ) {
			Helper::logData( 'Error deleting specific template metas: ' );
			Helper::logData( $wpdb->last_error );
			Helper::logData( 'SQL: ' . $wpdb->last_query );
			return false;
		}
        // phpcs:enable

		return $deleted !== false;
	}

	public static function createTemplateMetasFromPost( $template_id, $post_meta_data ) {
		$template_listing_metas = self::$template_listing_metas;

		foreach ( $template_listing_metas as $meta_key ) {
			$meta_value = isset( $post_meta_data[ $meta_key ] ) ? $post_meta_data[ $meta_key ] : null;
			self::createTemplateMeta( $template_id, $meta_key, $meta_value );
		}
	}


	public static function createTemplateMetasFromMeliListing( $template_id, $meli_listing_data ) {
		$template_listing_metas = self::$template_listing_metas;

		foreach ( $template_listing_metas as $meta_key ) {
			$meta_value = isset( $meli_listing_data->$meta_key ) ? $meli_listing_data->$meta_key : null;
			self::createTemplateMeta( $template_id, $meta_key, $meta_value );
		}

		// Some metas like sale terms are not present directly in meli listing data and need to be added manually
		$sale_terms = isset( $meli_listing_data->sale_terms ) ? $meli_listing_data->sale_terms : array();

		foreach ( $sale_terms as $key => $term ) {
			if ( ! isset( $term->id ) ) {
				continue;
			}

			switch ( $term->id ) {
				case 'MANUFACTURING_TIME':
					self::createTemplateMeta( $template_id, 'manufacturing_time', $term->value_struct->number );
					self::createTemplateMeta( $template_id, 'manufacturing_time_unit', $term->value_struct->unit );
					break;
				case 'WARRANTY_TYPE':
					self::createTemplateMeta( $template_id, 'warranty_type_id', $term->value_id );
					self::createTemplateMeta( $template_id, 'warranty_type_name', $term->value_name );
					break;
				case 'WARRANTY_TIME':
					self::createTemplateMeta( $template_id, 'warranty_time', $term->value_struct->number );
					self::createTemplateMeta( $template_id, 'warranty_time_unit', $term->value_struct->unit );
					break;
				default:
					break;
			}
		}

		return true;
	}

	public static function createCustomTemplateMetas( $template_id, $template_data ) {
		$template_custom_metas = self::$template_custom_metas;

		foreach ( $template_custom_metas as $meta_key => $default_value ) {
			// Verificar si el meta_key ya existe en $template_data
			if ( ! isset( $template_data[ $meta_key ] ) ) {
				// Si no existe, usar el valor por defecto
				$meta_value = $default_value;
			} else {
				// Si existe, usar el valor proporcionado en $template_data
				$meta_value = $template_data[ $meta_key ];
			}

			self::createTemplateMeta( $template_id, $meta_key, $meta_value );
		}

		return true;
	}

	public static function createTemplateMeta( $template_id, $meta_key, $meta_value ) {
		global $wpdb;
		$template_metas_table_name = $wpdb->prefix . 'meliconnect_template_metas';

		$template_id = intval( $template_id );
		$meta_key    = sanitize_text_field( $meta_key );
		$meta_value  = maybe_serialize( $meta_value );

		$data = array(
			'meta_key'    => $meta_key,
			'meta_value'  => $meta_value,
			'template_id' => $template_id,
		);

		$wpdb->insert( $template_metas_table_name, $data );

		if ( $wpdb->last_error ) {
			Helper::logData( 'Error creating template meta: ' . $wpdb->last_error );
			Helper::logData( 'Data: ' . wp_json_encode( $data ) );
			Helper::logData( 'SQL: ' . $wpdb->last_query );
			return false;
		}

		return $wpdb->insert_id;
	}

	public static function determineChannelsValue( $channels ) {
		if ( in_array( 'marketplace', $channels ) && in_array( 'mercadoshop', $channels ) ) {
			return 'all';
		} elseif ( in_array( 'marketplace', $channels ) ) {
			return 'mercadolibre';
		} elseif ( in_array( 'mercadoshop', $channels ) ) {
			return 'mercadoshop';
		}

		return '';
	}

	public static function deleteCategoryRelatedDataFromTemplate( $template_id ) {
		global $wpdb;
		self::init();

		// updates form template table (category_id,category_name, category_path)
		$template_data = array(
			'category_id'   => '',
			'category_name' => '',
			'category_path' => '',
		);

		$wpdb->update(
			self::$table_name,
			$template_data,
			array( 'id' => $template_id )
		);

		if ( $wpdb->last_error ) {
			Helper::logData( 'Error deleting template category: ' );
			Helper::logData( $wpdb->last_error );
			Helper::logData( 'SQL: ' . $wpdb->last_query );
			return false;
		}

		// Delete template attributes
		self::deleteTemplatesAttributes( $template_id );

		// Deletes template metas (currency_id, listing_type_id, shipping, condition, catalog_domain, catalog_listing, official_store_id, warranty, permalink)
		self::deleteTemplatesMetas( $template_id, array( 'currency_id', 'listing_type_id', 'shipping', 'condition', 'catalog_domain', 'catalog_listing', 'official_store_id', 'warranty', 'permalink' ) );

		return true;
	}

	public static function deleteTemplate( $template_id ) {
		// Delete template attributes
		self::deleteTemplatesAttributes( $template_id );

		// Delete template metas
		self::deleteTemplatesMetas( $template_id );

		// Delete template
		global $wpdb;
		self::init();

		$deleted = $wpdb->delete(
			self::$table_name,
			array( 'id' => $template_id )
		);

		if ( $wpdb->last_error ) {
			Helper::logData( 'Error deleting template: ' );
			Helper::logData( $wpdb->last_error );
			return false;
		}

		return $deleted !== false;
	}

	public static function deleteCreateTemplatesAttributesFromMeliListing( $template_id, $meli_listing_data, $woo_product_id = '' ) {
		self::deleteTemplatesAttributes( $template_id );

		self::createMainProductAttributes( $template_id, $meli_listing_data, $woo_product_id );

		if ( isset( $meli_listing_data->variations ) && ! empty( $meli_listing_data->variations ) ) {
			self::createVariationCombinationAttributes( $template_id, $meli_listing_data, $woo_product_id );
			self::createVariationAttributes( $template_id, $meli_listing_data, $woo_product_id );
		}
	}

	public static function deleteTemplatesAttributes( $template_id ) {
		global $wpdb;
		$template_attributes_table_name = $wpdb->prefix . 'meliconnect_template_attributes';

		$deleted = $wpdb->delete(
			$template_attributes_table_name,
			array( 'template_id' => $template_id )
		);

		if ( $wpdb->last_error ) {
			Helper::logData( 'Error deleting template attrs: ' );
			Helper::logData( $wpdb->last_error );
			Helper::logData( 'SQL: ' . $wpdb->last_query );
			return false;
		}

		return $deleted !== false;
	}

	public static function createUpdateTemplateAttributes( $used_by, $used_asoc_id, $meli_attribute_id, $data ) {
		global $wpdb;
		$template_attributes_table_name = $wpdb->prefix . 'meliconnect_template_attributes';

		// Verificar si ya existe un registro con used_by, used_asoc_id, y meli_attribute_id
        // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching

		$existing_row = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT id FROM $template_attributes_table_name WHERE used_by = %s AND used_asoc_id = %d AND meli_attribute_id = %s",
				$used_by,
				$used_asoc_id,
				$meli_attribute_id
			)
		);
        // phpcs:enable

		if ( $existing_row ) {
			// Intentar actualizar el registro existente
			$result = $wpdb->update(
				$template_attributes_table_name,
				$data,
				array( 'id' => $existing_row->id )
			);

			if ( $result === false ) {
				// En caso de error, registrar el error en el log y la última consulta SQL
				Helper::logData( 'Error updating the record in the table ' . $template_attributes_table_name );
				Helper::logData( 'Last SQL: ' . $wpdb->last_query );
				Helper::logData( 'Data sent: ' . wp_json_encode( $data, true ) );
			}
		} else {
			// Intentar insertar un nuevo registro
			$result = $wpdb->insert(
				$template_attributes_table_name,
				$data
			);

			if ( $result === false ) {
				// En caso de error, registrar el error en el log y la última consulta SQL
				Helper::logData( 'Error inserting a new record in the table ' . $template_attributes_table_name );
				Helper::logData( 'Last SQL: ' . $wpdb->last_query );
				Helper::logData( 'Data sent: ' . wp_json_encode( $data, true ) );
			}
		}
	}



	public static function createMainProductAttributes( $template_id, $meli_listing_data, $woo_product_id ) {
		global $wpdb;
		$template_attributes_table_name = $wpdb->prefix . 'meliconnect_template_attributes';
		$product                        = wc_get_product( $woo_product_id );

		if ( ! $product ) {
			Helper::logData( "Product with ID $woo_product_id not found." );
			return false;
		}
		// Helper::logData('main attrs');
		foreach ( $meli_listing_data->attributes as $main_attr ) {
			$woo_attribute_id = self::get_woo_attribute_id( $product, $main_attr->name );
			// $woo_attribute_id = '';

			$data = array(
				'template_id'             => $template_id,
				'used_by'                 => 'product',
				'used_asoc_id'            => $woo_product_id,
				'meli_attribute_id'       => $main_attr->id,
				'meli_attribute_name'     => $main_attr->name,
				'meli_value_id'           => $main_attr->value_id,
				'meli_value_name'         => $main_attr->value_name,
				'meli_value_type'         => $main_attr->value_type,
				'woo_attribute_id'        => $woo_attribute_id,
				'product_parent_id'       => 0,
				'allow_variations_tag'    => 0,
				'variation_attribute_tag' => 0,
			);

			if ( ! self::insert_template_attributes( $data, $template_attributes_table_name ) ) {
				return false;
			}
		}

		return true;
	}




	public static function createVariationCombinationAttributes( $template_id, $meli_listing_data, $woo_product_id ) {
		global $wpdb;
		$template_attributes_table_name = $wpdb->prefix . 'meliconnect_template_attributes';

		// Obtén el producto de WooCommerce por ID
		$product = wc_get_product( $woo_product_id );

		if ( ! $product ) {
			Helper::logData( "Product with ID $woo_product_id not found." );
			return false;
		}

		foreach ( $meli_listing_data->variations as $variation ) {

			$meli_variation_id = $variation->id;

			$woo_variation = Helper::getPostByMeta( 'meliconnect_meli_asoc_variation_id', $meli_variation_id );

			$woo_variation_id = isset( $woo_variation->ID ) ? $woo_variation->ID : '';

			if ( isset( $variation->attribute_combinations ) && ! empty( $variation->attribute_combinations ) ) {
				foreach ( $variation->attribute_combinations as $attr_combination ) {

					$data = array(
						'template_id'             => $template_id,
						'used_by'                 => 'variation',
						'used_asoc_id'            => $woo_variation_id,
						'meli_variation_id'       => $meli_variation_id,
						'meli_attribute_id'       => $attr_combination->id,
						'meli_attribute_name'     => $attr_combination->name,
						'meli_value_id'           => $attr_combination->value_id,
						'meli_value_name'         => $attr_combination->value_name,
						'meli_value_type'         => $attr_combination->value_type,
						'woo_attribute_id'        => '',
						'product_parent_id'       => $woo_product_id,
						'allow_variations_tag'    => 1, // Atributo utilizado en la combinación de variaciones
						'variation_attribute_tag' => 0, // Atributo utilizado como atributo de variación
					);

					$wpdb->insert( $template_attributes_table_name, $data );

					if ( $wpdb->last_error ) {
						Helper::logData( 'Error creating variation combination product templates attributes: ' );
						Helper::logData( $wpdb->last_error );
						Helper::logData( 'SQL: ' . $wpdb->last_query );
						return false;
					}
				}
			}
		}

		return true;
	}


	public static function createVariationAttributes( $template_id, $meli_listing_data, $woo_product_id ) {
		global $wpdb;
		$template_attributes_table_name = $wpdb->prefix . 'meliconnect_template_attributes';
		$product                        = wc_get_product( $woo_product_id );

		if ( ! $product ) {
			Helper::logData( "Product with ID $woo_product_id not found." );
			return false;
		}
		Helper::logData( 'variation attrs' );
		foreach ( $meli_listing_data->variations as $variation ) {
			$meli_variation_id = $variation->id;

			$woo_variation = Helper::getPostByMeta( 'meliconnect_meli_asoc_variation_id', $meli_variation_id );

			$woo_variation_id = isset( $woo_variation->ID ) ? $woo_variation->ID : '';

			if ( isset( $variation->attributes ) && ! empty( $variation->attributes ) ) {
				foreach ( $variation->attributes as $variation_attr ) {
					// $woo_attribute_id = self::get_woo_attribute_id($product, $variation_attr->name);
					$woo_attribute_id = '';

					$data = array(
						'template_id'             => $template_id,
						'used_by'                 => 'variation',
						'used_asoc_id'            => $woo_variation_id,
						'meli_variation_id'       => $meli_variation_id,
						'meli_attribute_id'       => $variation_attr->id,
						'meli_attribute_name'     => $variation_attr->name,
						'meli_value_id'           => $variation_attr->value_id,
						'meli_value_name'         => $variation_attr->value_name,
						'meli_value_type'         => $variation_attr->value_type,
						'woo_attribute_id'        => $woo_attribute_id,
						'product_parent_id'       => $woo_product_id,
						'allow_variations_tag'    => 0,
						'variation_attribute_tag' => 1,
					);

					if ( ! self::insert_template_attributes( $data, $template_attributes_table_name ) ) {
						return false;
					}
				}
			}
		}

		return true;
	}


	public static function insert_template_attributes( $data, $table_name ) {
		global $wpdb;
		$wpdb->insert( $table_name, $data );

		if ( $wpdb->last_error ) {
			Helper::logData( 'Error creating template attributes: ' );
			Helper::logData( $wpdb->last_error );
			Helper::logData( 'SQL: ' . $wpdb->last_query );
			return false;
		}

		return true;
	}

	public static function get_woo_attribute_id( $product, $attribute_name ) {
		$product_attributes = $product->get_attributes();

		// Helper::logData("Product attributes: " . wp_json_encode($product_attributes, true));

		foreach ( $product_attributes as $attr ) {
			// Helper::logData("-------------------");
			// Helper::logData("Attribute name received: " . wc_sanitize_taxonomy_name($attribute_name));
			// Helper::logData("Attribute name: " . $attr->get_name());

			if ( strcasecmp( $attr->get_name(), 'pa_' . wc_sanitize_taxonomy_name( $attribute_name ) ) === 0 || strcasecmp( $attr->get_name(), $attribute_name ) === 0 ) {
				// Helper::logData("Attribute found: " . $attr->get_id());
				return $attr->get_id();
			}
		}

		return '';
	}

	public static function getTemplateData( $template_id ) {
		global $wpdb;
		$template_table_name       = $wpdb->prefix . 'meliconnect_templates';
		$template_metas_table_name = $wpdb->prefix . 'meliconnect_template_metas';

        // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching

		// Obtener la data del template
		$template_data = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM {$template_table_name} WHERE id = %d",
				$template_id
			),
			ARRAY_A
		);

		// Verificar si se encontró el template
		if ( ! $template_data ) {
			return null; // Retorna null si no se encontró el template
		}

		// Obtener los postmetas del template
		$template_meta = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT meta_key, meta_value FROM {$template_metas_table_name} WHERE template_id = %d",
				$template_id
			),
			ARRAY_A
		);
        // phpcs:enable

		// Agregar los metadatos directamente al array del template
		foreach ( $template_meta as $meta ) {
			$template_data[ $meta['meta_key'] ] = maybe_unserialize( $meta['meta_value'] );
		}

		return $template_data;
	}

	public static function getTemplateAttributes( $template_id ) {
		global $wpdb;
		$template_attributes_table_name = $wpdb->prefix . 'meliconnect_template_attributes';

        // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching

		$template_attributes            = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM {$template_attributes_table_name} WHERE template_id = %d",
				$template_id
			),
			ARRAY_A
		);

        // phpcs:enable

		return $template_attributes;
	}

	public static function selectCustomTemplateData( $template_id, array $data ) {
		global $wpdb;
		$template_table_name = $wpdb->prefix . 'meliconnect_templates';

		// Verificar que $data sea un string o un array de strings
		if ( is_string( $data ) ) {
			$data = array( $data ); // Convertir string a array
		}

		if ( ! is_array( $data ) || empty( $data ) ) {
			return false; // Retornar false si no es un array válido
		}

		// Lista de columnas permitidas basada en la estructura de la tabla wp_meliconnect_templates
		$allowed_columns = array(
			'id',
			'used_by',
			'used_asoc_id',
			'template_parent_id',
			'seller_meli_id',
			'name',
			'short_description',
			'category_id',
			'category_name',
			'category_path',
			'channels',
			'status',
			'created_at',
			'updated_at',
		);

		// Filtrar el array para solo permitir columnas válidas
		$filtered_columns = array_intersect( $data, $allowed_columns );

		if ( empty( $filtered_columns ) ) {
			return false; // Retornar false si no hay columnas válidas
		}

		// Convertir el array de columnas en una cadena separada por comas
		$columns = implode( ', ', $filtered_columns );

        // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching

		// Obtener la data del template
		$template_data = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT {$columns} FROM {$template_table_name} WHERE id = %d",
				$template_id
			),
			ARRAY_A
		);
        // phpcs:enable

		return $template_data;
	}

	public static function updateTemplateData( $template_id, array $data ) {
		global $wpdb;

		// Nombre de la tabla
		$table_name = $wpdb->prefix . 'meliconnect_templates';

		// Validamos si el array de datos no está vacío
		if ( empty( $data ) ) {
			return false; // No hay datos para actualizar
		}

		// Actualizamos los datos en la tabla
		$updated = $wpdb->update(
			$table_name,
			$data,
			array( 'id' => $template_id ),
			null,               // Formato de los valores a actualizar (null para que sea detectado automáticamente)
			array( '%d' )              // Formato del where (en este caso, %d porque es un id numérico)
		);

		if ( $wpdb->last_error ) {
			Helper::logData( 'Error updating template data: ' );
			Helper::logData( $wpdb->last_error );
			Helper::logData( 'SQL: ' . $wpdb->last_query );
			return false;
		}

		return true;
	}
}
