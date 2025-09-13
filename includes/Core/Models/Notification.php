<?php

namespace Meliconnect\Meliconnect\Core\Models;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class Notification
{
    private static $table_name;

    // Este método se llama automáticamente cuando se accede por primera vez a la clase
    public static function init() {
        global $wpdb;
        self::$table_name = $wpdb->prefix . "meliconnect_notifications";
    }

    public static function getNotifications() {
        global $wpdb;
        
        self::init();

        $table_name = self::$table_name;
        
        // Realiza la consulta para obtener todas las notificaciones guardadas
        $notifications = $wpdb->get_results(
            $wpdb->prepare("
                SELECT *
                FROM {$table_name}
                WHERE
                    status = 'show'
                    AND is_dismissed != 1
                    AND (
                        from_version IS NULL
                        OR from_version <= %s
                    )
                ORDER BY date_from DESC
            ", MELICONNECT_VERSION)
        );

        return $notifications;
    }
}