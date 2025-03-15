<?php

namespace StoreSync\Meliconnect\Modules\Exporter\Models;

use Error;
use StoreSync\Meliconnect\Core\Helpers\Helper;

class ProductToExport
{
    private static $table_name;

    // Este método se llama automáticamente cuando se accede por primera vez a la clase
    public static function init()
    {
        global $wpdb;

        self::$table_name = $wpdb->prefix . "melicon_products_to_export";
    }



    public static function count_products_to_export()
    {
        global $wpdb;

        self::init();

        $table_name = self::$table_name;

        return $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
    }

    public static function fill_products_table($products)
    {
        if (empty($products) || !is_array($products)) {
            return;
        }
    
        global $wpdb;
    
        self::init();
    
        $table_name = self::$table_name;
        $process_id = hash('sha256', time() . bin2hex(random_bytes(8)));
    
        foreach ($products as $product) {
    
            // Preparar los valores de la consulta directamente
            $columns = [
                'woo_product_id' => $product['product_id'],
                'woo_product_name' => $product['product_name'],
                'woo_sku' => $product['sku'],
                'woo_gtin' => $product['gtin'],
                'woo_product_type' => $product['product_type'],
                'woo_status' => $product['status'],
                'vinculated_template_id' => $product['vinculated_template_id'] ? intval($product['vinculated_template_id']) : null,
                'vinculated_listing_id' => $product['vinculated_listing_id'] ? $product['vinculated_listing_id'] : null,
                'listing_match_by' => null,
                'template_match_by' => null,
                'meli_permalink' => $product['meli_permalink'] ?? null,
                'meli_seller_id' => $product['meli_seller_id'] ?? null,
                'export_status' => 'pending',
                'export_error' => null,
                'process_id' => $process_id,
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql'),
            ];
    
            // Construir la consulta con placeholders
            $columns_placeholders = implode(', ', array_keys($columns));
            $values_placeholders = implode(', ', array_fill(0, count($columns), '%s'));
    
            // Ejecutar la consulta SQL con ON DUPLICATE KEY UPDATE
            // No actualizamos 'export_status', 'export_error', 'process_id', 'created_at' para mantener sus valores viejos
            $result = $wpdb->query(
                $wpdb->prepare(
                    "INSERT INTO $table_name ($columns_placeholders) VALUES ($values_placeholders) 
                    ON DUPLICATE KEY UPDATE 
                        woo_product_name = VALUES(woo_product_name), 
                        woo_sku = VALUES(woo_sku), 
                        woo_gtin = VALUES(woo_gtin), 
                        woo_product_type = VALUES(woo_product_type), 
                        woo_status = VALUES(woo_status), 
                        vinculated_template_id = VALUES(vinculated_template_id), 
                        vinculated_listing_id = VALUES(vinculated_listing_id), 
                        listing_match_by = VALUES(listing_match_by), 
                        template_match_by = VALUES(template_match_by), 
                        meli_permalink = VALUES(meli_permalink), 
                        meli_seller_id = VALUES(meli_seller_id), 
                        updated_at = VALUES(updated_at)
                    ",
                    array_values($columns)
                )
            );
    
            // Verificar errores
            if ($result === false) {
                Helper::logData('Error filling export products table: ' . $wpdb->last_error);
            }
        }
    }
    


    public static function get_products_to_export($products_ids)
    {
        global $wpdb;
        self::init();

        $table_name = self::$table_name;

        // Si hay productos, construir y preparar la consulta directamente
        if (!empty($products_ids) && is_array($products_ids)) {
            // Construir los placeholders manualmente
            $placeholders = implode(',', array_fill(0, count($products_ids), '%s'));

            // Preparar y ejecutar la consulta con los valores
            return $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM {$table_name} WHERE woo_product_id IN ($placeholders)",
                ...$products_ids
            ));
        }

        // Si no se pasan productos, ejecutar la consulta sin filtro
        return $wpdb->get_results("SELECT * FROM {$table_name}");
    }


    public static function update_product_to_export_status($woo_products_ids, $export_status, $export_error = null)
    {
        global $wpdb;
        self::init();

        $table_name = self::$table_name;

        // Verificar que $export_status no esté vacío
        if (empty($export_status)) {
            return false;
        }

        // Construir los parámetros de la consulta
        $fields_to_update = "export_status = %s";
        $params = [$export_status];

        // Si $export_error no es null, también actualiza el campo export_error
        if ($export_error !== null) {

            // Serializar el array de errores antes de guardarlo
            $serialized_errors = maybe_serialize($export_error);

            $fields_to_update .= ", export_error = %s";
            $params[] = $serialized_errors;
        }

        if ($woo_products_ids === 'all') {
            // Actualizar todos los registros si $woo_products_ids es 'all'
            return $wpdb->query($wpdb->prepare(
                "UPDATE {$table_name} SET {$fields_to_update}",
                $params
            ));
        } else {
            // Actualizar solo los registros con woo_product_id en la lista
            if (!is_array($woo_products_ids)) {
                $woo_products_ids = [$woo_products_ids];
            }

            $placeholders = implode(',', array_fill(0, count($woo_products_ids), '%d'));


            return $wpdb->query($wpdb->prepare(
                "UPDATE {$table_name} SET {$fields_to_update} WHERE woo_product_id IN ($placeholders)",
                array_merge($params, $woo_products_ids)
            ));
        }
    }

    public static function unlink_woo_product($woo_product_id)
    {
        if (empty($woo_product_id)) {
            return false;
        }

        global $wpdb;

        self::init();

        $table_name = self::$table_name; // Asegúrate de que esta propiedad tenga un valor válido


        // Ejecutamos la consulta
        $result = $wpdb->query($wpdb->prepare(
            "UPDATE {$table_name} SET vinculated_listing_id = 0 WHERE woo_product_id = %d",
            $woo_product_id
        ));

        // Verificamos si hubo algún error
        if ($result === false) {

            Helper::logData('Error unlinking product from user listing: ' . $wpdb->last_error);

            /* $last_query = $wpdb->last_query;
            Helper::logData('Última consulta SQL ejecutada: ' . $last_query); */
            return false;
        }

        // Si no hubo error, devolvemos el número de filas afectadas
        return $result;
    }
}
