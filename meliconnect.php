<?php
/*
Plugin Name: Meliconnect
Plugin URI: https://mercadolibre.meliconnect.com/
Description: WooCommerce & Mercado Libre integration plugin to import, export, and synchronize products between your WooCommerce store and Mercado Libre accounts.
Version: 1.0.0
Author: storesync
Author URI: https://mercadolibre.meliconnect.com/
Text Domain: meliconnect
Domain Path: /languages
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Requires at least: 5.8
Requires PHP: 8.0
Tags: woocommerce, mercadolibre, integration, marketplace, sync
*/



// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Define constantes del plugin
 */
define('MC_VERSION', '1.0.0');
define('MC_DATABASE_VERSION', '1.0.0');
define('MC_TEXTDOMAIN', 'meliconnect');
define('MC_PLUGIN_ROOT', plugin_dir_path(__FILE__));
define('MC_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Carga de dependencias
 */
if (file_exists(MC_PLUGIN_ROOT . 'vendor/autoload.php')) {
    require_once MC_PLUGIN_ROOT . 'vendor/autoload.php';
}

/**
 * Hooks de activación y desinstalación
 */
register_activation_hook(__FILE__, array('StoreSync\Meliconnect\Core\Initialize', 'activate'));
register_uninstall_hook(__FILE__, array('StoreSync\Meliconnect\Core\Initialize', 'uninstall'));

/**
 * Inicialización del plugin
 */
new StoreSync\Meliconnect\Core\Initialize();

/**
 * Funciones de compatibilidad
 */
if (!function_exists('mc_get_plugin_path')) {
    function mc_get_plugin_path() {
        return MC_PLUGIN_ROOT;
    }
}

if (!function_exists('mc_get_plugin_url')) {
    function mc_get_plugin_url() {
        return MC_PLUGIN_URL;
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
