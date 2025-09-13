<?php

namespace Meliconnect\Meliconnect\Modules\Importer\Models;

if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

use Error;
use Meliconnect\Meliconnect\Core\Helpers\Helper;

class UserListingToImport
{
    private static $table_name;

    // Este método se llama automáticamente cuando se accede por primera vez a la clase
    public static function init()
    {
        global $wpdb;

        self::$table_name = $wpdb->prefix . "meliconnect_user_listings_to_import";
    }

    public static function create_or_skip_meli_user_listings_ids_to_import($meli_user, $meli_user_listings_ids)
    {
        global $wpdb;

        self::init();

        $table_name = self::$table_name;

        $import_process_id = hash('sha256', $meli_user->user_id . time() . bin2hex(random_bytes(8)));


        foreach ($meli_user_listings_ids as $listing_id) {

            $existing_row = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT COUNT(*) FROM $table_name WHERE meli_user_id = %d AND meli_listing_id = %s",
                    $meli_user->user_id,
                    $listing_id
                )
            );

            if (!$existing_row) {
                // Only cretaes the row if it doesn't exist
                $wpdb->insert(
                    $table_name,
                    [
                        'process_id' => $import_process_id,
                        'meli_user_id' => $meli_user->user_id,
                        'meli_listing_id' => $listing_id
                    ]
                );
            }
        }

        return true;
    }

    public static function apply_item_manual_match($meli_item_id, $woo_product_id) {}

    public static function update_meli_user_listings_extra_data_to_import($meli_user_listings_data)
    {
        global $wpdb;

        self::init();

        $table_name = self::$table_name;

        $vinculated_products = Helper::getPostsWithMetaArray('meliconnect_meli_listing_id');
        $vinculated_templates = Helper::getPostsWithMetaArray('meliconnect_meli_template_id');

        //Helper::logData('vinculated_products: ' . wp_json_encode($vinculated_products));

        foreach ($meli_user_listings_data as $meli_listing_id => $listing_data) {

            $listing_data['vinculated_product_id'] = '';
            $listing_data['vinculated_template_id'] = '';

            //Order array by keys
            ksort($listing_data);


            if (isset($vinculated_products[$meli_listing_id])) {
                $listing_data['vinculated_product_id'] = $vinculated_products[$meli_listing_id];
            }


            if (isset($vinculated_templates[$meli_listing_id])) {
                $listing_data['vinculated_template_id'] = $vinculated_templates[$meli_listing_id];
            }



            $wpdb->update(
                $table_name,
                [
                    'meli_response' => wp_json_encode($listing_data),
                    'meli_status' => (isset($listing_data['status']) ? $listing_data['status'] : ''),
                    'meli_sub_status' => (isset($listing_data['sub_status']) ? wp_json_encode($listing_data['sub_status']) : ''),
                    'meli_product_type' => (isset($listing_data['variations']) && !empty($listing_data['variations'])) ? 'variable' : 'simple',
                    'meli_listing_title' => (isset($listing_data['title']) ? $listing_data['title'] : ''),
                    'vinculated_product_id' =>  $listing_data['vinculated_product_id'],
                    'vinculated_template_id' =>  $listing_data['vinculated_template_id'],
                ],
                [
                    'meli_listing_id' => $meli_listing_id
                ]
            );
        }

        return true;
    }

    public static function reset_meli_user_listings()
    {
        global $wpdb;
        self::init();

        $table_name = self::$table_name;

        $result = $wpdb->query("DELETE FROM {$table_name}");

        // Verificación del resultado
        if ($result) {
            wp_send_json_success(esc_html__('User listings reset successfully', 'meliconnect'));
            return;
        }

        wp_send_json_error(esc_html__('There was an error resetting the user listings', 'meliconnect'));
        return;
    }



    public static function get_user_listings_count()
    {
        global $wpdb;
        self::init();

        $table_name = self::$table_name;

        return $wpdb->get_var("SELECT COUNT(*) FROM {$table_name}");
    }

    public static function get_user_listings_to_import($meli_listings_ids = [])
    {
        global $wpdb;
        self::init();

        $table_name = self::$table_name;

        if (!empty($meli_listings_ids) && is_array($meli_listings_ids)) {
            // Construir los placeholders manualmente
            $placeholders = implode(',', array_fill(0, count($meli_listings_ids), '%s'));

            // Ejecutar la consulta preparada directamente
            return $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT * FROM {$table_name} WHERE meli_listing_id IN ($placeholders)",
                    ...$meli_listings_ids
                )
            );
        }

        // Consulta sin parámetros si $meli_listings_ids está vacío
        return $wpdb->get_results("SELECT * FROM {$table_name}");
    }


    public static function update_vinculated_product($user_listing_id, $product_id, $match_by)
    {
        global $wpdb;

        self::init();

        $table_name = self::$table_name;

        Helper::logData('Updating user listing with id: ' . $user_listing_id, 'import');

        $is_match_by_sku = ($match_by == 'sku') ? 1 : 0;
        $is_match_by_name = ($match_by == 'name') ? 1 : 0;
        $is_match_manually = ($match_by == 'manual') ? 1 : 0;

        $data_to_update = [
            'vinculated_product_id' => $product_id,
            'is_product_match_by_sku' => $is_match_by_sku,
            'is_product_match_by_name' => $is_match_by_name,
            'is_product_match_manually' => $is_match_manually,
            'updated_at' => current_time('mysql')
        ];

        Helper::logData('Data to update:' . wp_json_encode($data_to_update), 'import');

        $result = $wpdb->update(
            $table_name,
            $data_to_update,
            [
                'id' => $user_listing_id
            ],
            [
                '%d',  // Formato para vinculated_product_id
                '%d',  // Formato para is_product_match_by_sku
                '%d',  // Formato para is_product_match_by_name
                '%s'   // Formato para updated_at
            ],
            [
                '%d'   // Formato para el campo id
            ]
        );

        if ($result === false) {
            Helper::logData('Failed to update the vinculated product with id:' . $product_id, 'import');
            $error = $wpdb->last_error;
            Helper::logData($error, 'import');
            return false;
        }

        return true;
    }

    public static function update_processing_listings($status)
    {
        global $wpdb;

        self::init();

        $table_name = self::$table_name;

        // Validar el status
        if (empty($status)) {
            Helper::logData('Invalid status provided for update', 'import');
            return false;
        }

        $result = $wpdb->update(
            $table_name,
            ['import_status' => $status],
            ['import_status' => 'processing'],
            ['%s'],
            ['%s']
        );

        if ($result === false) {
            $error = $wpdb->last_error;
            Helper::logData($error, 'import');
            return false;
        }

        // Retornar el número de filas afectadas
        return $result;
    }

    public static function update_vinculated_product_ids($meliListingIds)
    {
        global $wpdb;

        if (empty($meliListingIds)) {
            return;
        }

        // Aseguramos que los IDs estén formateados correctamente para la consulta SQL
        $placeholders = implode(',', array_fill(0, count($meliListingIds), '%s'));


        // Ejecutamos la consulta y obtenemos los resultados
        $postmeta_results = $wpdb->get_results($wpdb->prepare("
            SELECT post_id, meta_value
            FROM {$wpdb->postmeta}
            WHERE meta_key = 'meliconnect_meli_listing_id'
            AND meta_value IN ($placeholders)
        ", ...$meliListingIds));

        if (!empty($postmeta_results)) {
            foreach ($postmeta_results as $postmeta) {
                // Actualizamos la tabla wp_meliconnect_user_listings_to_import con el post_id correspondiente
                $wpdb->update(
                    'wp_meliconnect_user_listings_to_import',
                    array('vinculated_product_id' => $postmeta->post_id),
                    array('meli_listing_id' => $postmeta->meta_value),
                    array('%d'),  // Formato de vinculated_product_id
                    array('%s')   // Formato de meli_listing_id
                );
            }
        }
    }

    public static function clear_matches($meli_listing_ids = 'all')
    {
        global $wpdb;

        // Definir la tabla
        $table_name = $wpdb->prefix . 'meliconnect_user_listings_to_import';

        // Manejar un solo valor de meli_listing_id convirtiéndolo en un array
        if (!is_array($meli_listing_ids) && $meli_listing_ids !== 'all') {
            $meli_listing_ids = [$meli_listing_ids];
        }

        Helper::logData('meli_listing_ids: ' . wp_json_encode($meli_listing_ids));

        // Construir consulta según el tipo de entrada
        if (is_array($meli_listing_ids) && !empty($meli_listing_ids)) {
            // Preparar placeholders para las IDs
            $placeholders = implode(',', array_fill(0, count($meli_listing_ids), '%s'));

            // Ejecutar la consulta preparada directamente
            $result = $wpdb->query(
                $wpdb->prepare(
                    "
                    UPDATE {$table_name}
                    SET vinculated_product_id = NULL,
                        is_product_match_by_sku = 0,
                        is_product_match_by_name = 0,
                        is_product_match_manually = 0,
                        updated_at = %s
                    WHERE meli_listing_id IN ($placeholders) 
                    AND (is_product_match_by_sku = 1 
                    OR is_product_match_by_name = 1 
                    OR is_product_match_manually = 1)
                    ",
                    current_time('mysql'),
                    ...$meli_listing_ids
                )
            );
        } else {
            // Si no se pasan IDs específicas, limpiar todas las coincidencias
            $result = $wpdb->query(
                $wpdb->prepare(
                    "
                    UPDATE {$table_name}
                    SET vinculated_product_id = NULL,
                        is_product_match_by_sku = 0,
                        is_product_match_by_name = 0,
                        is_product_match_manually = 0,
                        updated_at = %s
                    WHERE is_product_match_by_sku = 1 
                    OR is_product_match_by_name = 1 
                    OR is_product_match_manually = 1
                    ",
                    current_time('mysql')
                )
            );
        }

        // Manejo de errores
        if ($result === false) {
            $error_message = $wpdb->last_error;
            Helper::logData('Failed to clear the matches: ' . $error_message);
            return false;
        }

        return true;
    }





    public static function get_not_vinculated_user_listings_to_import()
    {
        global $wpdb;
        self::init();

        $table_name = self::$table_name;


        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$table_name} 
            WHERE vinculated_product_id IS NULL 
            OR vinculated_product_id = '' 
            OR vinculated_product_id = %d",
            0
        ));
    }


    public static function update_user_listing_item_import_status($meli_listing_ids, $import_status)
    {
        global $wpdb;
        self::init();

        $table_name = self::$table_name;

        if (empty($meli_listing_ids) || empty($import_status)) {
            return false;
        }

        if (!is_array($meli_listing_ids)) {
            $meli_listing_ids = [$meli_listing_ids];
        }

        $placeholders = implode(',', array_fill(0, count($meli_listing_ids), '%s'));

        return $wpdb->query($wpdb->prepare(
            "UPDATE {$table_name} SET import_status = %s WHERE meli_listing_id IN ($placeholders)",
            array_merge([$import_status], $meli_listing_ids)
        ));
    }

    public static function get_user_listings_count_by_status($status)
    {
        global $wpdb;
        self::init();

        $table_name = self::$table_name;

        return $wpdb->get_var("SELECT COUNT(*) FROM {$table_name} WHERE meli_status = '{$status}'");
    }

    public static function get_user_listing_by_listing_id($meli_listing_id)
    {
        global $wpdb;
        self::init();

        $table_name = self::$table_name;


        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$table_name} WHERE meli_listing_id = %s",
            $meli_listing_id
        ));
    }





    public static function unlink_woo_product($meli_listing_id)
    {
        if (empty($meli_listing_id)) {
            return false;
        }

        global $wpdb;

        self::init();

        $table_name = self::$table_name; // Asegúrate de que esta propiedad tenga un valor válido

        // Placeholder sin comillas
        $result = $wpdb->query($wpdb->prepare(
            "UPDATE {$table_name} SET vinculated_product_id = 0 WHERE meli_listing_id = %s",
            $meli_listing_id
        ));

        // Verificamos si hubo algún error
        if ($result === false) {
            Helper::logData('Error unlinking product from user listing: ' . $wpdb->last_error);
            return false;
        }

        // Si no hubo error, devolvemos el número de filas afectadas
        return $result;
    }

}
