<?php

namespace Meliconnect\Meliconnect\Core\Models;

if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Notification
{
    private static $table_name;

    // Este método se llama automáticamente cuando se accede por primera vez a la clase
    public static function init()
    {
        global $wpdb;
        self::$table_name = $wpdb->prefix . "meliconnect_notifications";
    }

    public static function getNotifications()
    {
        global $wpdb;

        self::init();
        $table_name = self::$table_name;

        // Revisar si hay cache
        $cached = wp_cache_get('meliconnect_notifications', 'meliconnect');
        if (false !== $cached) {
            return $cached;
        }

        // Consulta preparada con MELICONNECT_VERSION como parámetro dinámico
        $notifications = $wpdb->get_results(
            $wpdb->prepare(
                "
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
            ",
                MELICONNECT_VERSION
            ),
            ARRAY_A
        );

        // Guardar resultados en cache por 1 hora
        wp_cache_set('meliconnect_notifications', $notifications, 'meliconnect', 3600);

        return $notifications;
    }
}
