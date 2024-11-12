<?php

namespace StoreSync\Meliconnect\Modules\Exporter\Models;

use Error;

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

            // Preparar los datos para la inserción o actualización
            $data = [
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

            // Preparar los valores para la actualización en caso de duplicados
            $update_fields = array_map(function ($key) {
                // Excluir los campos que no queremos actualizar en caso de duplicados
                if (in_array($key, ['export_status', 'export_error', 'process_id', 'created_at'])) {
                    return "$key = $key";
                }
                return "$key = VALUES($key)";
            }, array_keys($data));

            // Ejecutar la consulta SQL con ON DUPLICATE KEY UPDATE
            $query = "
            INSERT INTO $table_name (" . implode(', ', array_keys($data)) . ")
            VALUES (" . implode(', ', array_fill(0, count($data), '%s')) . ")
            ON DUPLICATE KEY UPDATE " . implode(', ', $update_fields);

            $wpdb->query(
                $wpdb->prepare(
                    $query,
                    array_values($data)
                )
            );
        }
    }

    public static function get_products_to_export($products_ids)
    {
        global $wpdb;
        self::init();

        $table_name = self::$table_name;
        $sql = "SELECT * FROM {$table_name}";

        if (!empty($products_ids) && is_array($products_ids)) {
            // Construir los placeholders manualmente
            $placeholders = implode(',', array_fill(0, count($products_ids), '%s'));

            // Añadir la cláusula WHERE para filtrar por IDs
            $sql .= " WHERE woo_product_id IN ($placeholders)";

            // Preparar la consulta con los valores
            $sql = $wpdb->prepare($sql, ...$products_ids);
        }

        $results = $wpdb->get_results($sql);

        return $results;
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
            $sql = $wpdb->prepare(
                "UPDATE {$table_name} SET {$fields_to_update}",
                $params
            );
        } else {
            // Actualizar solo los registros con woo_product_id en la lista
            if (!is_array($woo_products_ids)) {
                $woo_products_ids = [$woo_products_ids];
            }

            $placeholders = implode(',', array_fill(0, count($woo_products_ids), '%d'));

            $sql = $wpdb->prepare(
                "UPDATE {$table_name} SET {$fields_to_update} WHERE woo_product_id IN ($placeholders)",
                array_merge($params, $woo_products_ids)
            );
        }

        return $wpdb->query($sql);
    }

    public static function unlink_woo_product($woo_product_id)
    {
        if (empty($woo_product_id)) {
            return false;
        }

        global $wpdb;

        self::init();

        $table_name = self::$table_name; // Asegúrate de que esta propiedad tenga un valor válido

        // Aseguramos que meli_listing_id es tratado como un string seguro
        $sql = $wpdb->prepare(
            "UPDATE {$table_name} SET vinculated_listing_id = 0 WHERE woo_product_id = '%d'",
            $woo_product_id
        );

        // Ejecutamos la consulta
        $result = $wpdb->query($sql);

        // Verificamos si hubo algún error
        if ($result === false) {
            error_log('Error unlinking product from user listing: ' . $wpdb->last_error);

            /* $last_query = $wpdb->last_query;
            error_log('Última consulta SQL ejecutada: ' . $last_query); */
            return false;
        }

        // Si no hubo error, devolvemos el número de filas afectadas
        return $result;
    }
}
