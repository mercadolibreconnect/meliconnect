<?php
/*
Plugin Name: Meliconnect
Plugin URI: https://mercadolibre.meliconnect.com/
Description: WooCommerce & Mercado Libre integration to import, export, and synchronize products between your WooCommerce store and Mercado Libre accounts.
Version: 1.0.1
Author: meliconnect
Text Domain: meliconnect
Domain Path: /languages
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Requires at least: 5.8
Requires PHP: 8.0
Requires Plugins: woocommerce
Tags: woocommerce, mercadolibre, integration, marketplace, sync
*/



// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Define constantes del plugin
 */
define('MELICONNECT_VERSION', '1.0.1');
define('MELICONNECT_DATABASE_VERSION', '1.0.0');
define('MELICONNECT_TEXTDOMAIN', 'meliconnect');
define('MELICONNECT_PLUGIN_ROOT', plugin_dir_path(__FILE__));
define('MELICONNECT_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Carga de dependencias
 */
if (file_exists(MELICONNECT_PLUGIN_ROOT . 'vendor/autoload.php')) {
    require_once MELICONNECT_PLUGIN_ROOT . 'vendor/autoload.php';
}

/**
 * Hooks de activación y desinstalación
 */
register_activation_hook(__FILE__, array('Meliconnect\Meliconnect\Core\Initialize', 'activate'));
register_uninstall_hook(__FILE__, array('Meliconnect\Meliconnect\Core\Initialize', 'uninstall'));

/**
 * Inicialización del plugin
 */
new Meliconnect\Meliconnect\Core\Initialize();

/**
 * Funciones de compatibilidad
 */
if (!function_exists('meliconnect_get_plugin_path')) {
    function meliconnect_get_plugin_path() {
        return MELICONNECT_PLUGIN_ROOT;
    }
}

if (!function_exists('meliconnect_get_plugin_url')) {
    function meliconnect_get_plugin_url() {
        return MELICONNECT_PLUGIN_URL;
    }
}

/**
 * Función de activación del plugin
 */
function meliconnect_activate() {
    // Verificar dependencias
    if (!class_exists('WooCommerce')) {
        deactivate_plugins(plugin_basename(__FILE__));
        wp_die(
            esc_html__('WooCommerce no está instalado o activado. Por favor, instálelo e inténtelo nuevamente.', 'meliconnect'),
            esc_html__('Plugin no activado', 'meliconnect'),
            array('response' => 200)
        );
    }
}
register_activation_hook(__FILE__, 'meliconnect_activate');

/**
 * Función de desactivación del plugin
 */
function meliconnect_deactivate() {
    // Limpieza de datos al desactivar
    // Aquí puedes agregar código para limpiar datos si es necesario
}
register_deactivation_hook(__FILE__, 'meliconnect_deactivate');

/**
 * Función de actualización del plugin
 */
function meliconnect_update() {
    // Lógica de actualización aquí
}
add_action('upgrader_process_complete', 'meliconnect_update', 10, 2);
