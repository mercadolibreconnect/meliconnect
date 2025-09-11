<?php

if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

global $wpdb;

// Prefijo de tablas personalizado
$prefix = $wpdb->prefix . 'melicon_';

// Listado de tablas personalizadas del plugin
$tables = [
    'notifications',
    'templates',
    'template_metas',
    'processes',
    'process_items',
    'products_to_export',
    'user_listings_to_import'
];

foreach ($tables as $table) {
    $wpdb->query("DROP TABLE IF EXISTS {$prefix}{$table}");
}

// Eliminar todas las opciones que comiencen con 'meliconnect_' o 'melicon_'
$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE 'meliconnect_%' OR option_name LIKE 'melicon_%'");