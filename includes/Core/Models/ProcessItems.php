<?php

namespace StoreSync\Meliconnect\Core\Models;

use StoreSync\Meliconnect\Core\Helpers\Helper;

class ProcessItems
{
    private static $table_name;

    // Este método se llama automáticamente cuando se accede por primera vez a la clase
    public static function init()
    {
        global $wpdb;
        self::$table_name = $wpdb->prefix . "melicon_process_items";
    }

    public static function updateProcessedItemStatus($item_process_id, $status, $process_id )
    {
        global $wpdb;

        self::init();

        $table_name = self::$table_name;

        $sql_item = "UPDATE {$table_name} SET process_status = %s WHERE id = %s";

        $query_item = $wpdb->prepare($sql_item, $status, $item_process_id);

        $result_item = $wpdb->query($query_item);


        if ($result_item !== false) {

            //update Process
            $process = Process::updateProcessProgress($process_id, true);

            return $process;
        }
    
        return false;
    }


    public static function createProcessItems($process_id, $items)
    {
        global $wpdb;

        self::init();

        $table_name = self::$table_name;

        // Asegúrate de que $items sea un array no vacío y de arrays asociativos
        if (!empty($items) && is_array($items)) {
            foreach ($items as $item) {
                if (is_array($item)) {
                    $wpdb->insert($table_name, [
                        'process_id'      => $process_id,
                        'meli_user_id'    => $item['meli_user_id'],
                        'meli_listing_id' => $item['meli_listing_id'],
                        'woo_product_id'  => $item['woo_product_id'],
                        'template_id'     => $item['template_id'],
                        'process_status'  => 'pending',
                    ]);
                }
            }
        }
    }

    public static function getProcessItems($process_id, $limit = 100, $item_status = 'pending')
    {
        global $wpdb;

        self::init();

        $table_name = self::$table_name;

        $sql = "SELECT * FROM {$table_name} WHERE process_id = %s AND process_status = %s LIMIT %d";

        $query = $wpdb->prepare($sql, $process_id, $item_status, $limit);

        $items = $wpdb->get_results($query);

        return $items;
    }

    public static function deleteItems($process_id)
    {
        global $wpdb;

        self::init();

        $table_name = self::$table_name;

        $sql = "DELETE FROM {$table_name} WHERE process_id = %s";

        $query = $wpdb->prepare($sql, $process_id);

        $result = $wpdb->query($query);

        return $result;
    }
}
