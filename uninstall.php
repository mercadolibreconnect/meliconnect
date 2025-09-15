<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

// Prefijo de tablas personalizado
$prefix = $wpdb->prefix . 'meliconnect_';

// Listado de tablas personalizadas del plugin
$tables = array(
	'notifications',
	'templates',
	'template_metas',
	'processes',
	'process_items',
	'products_to_export',
	'user_listings_to_import',
);

foreach ( $tables as $table ) {
	$wpdb->query( "DROP TABLE IF EXISTS {$prefix}{$table}" );
}

$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE 'meliconnect_%'" );

wp_cache_flush();
