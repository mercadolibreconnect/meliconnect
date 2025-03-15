<?php

/**
 * Plugin Name:     MeliConnect
 * Plugin URI:      https://meliconnect.com/
 * Description:     Conecta WooCommerce con Mercadolibre - Sincroniza publicaciones y más.
 * Version:         1.0.0
 * Author:          StoreSync
 * Author URI:      https://meliconnect.com/
 * Text Domain:     meliconnect
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Domain Path:     /languages
 * Requires PHP:    8.0
 */



// If this file is called directly, abort.
if (!defined('ABSPATH')) {
	die(esc_html__('We\'re sorry, but you can not directly access this file.', 'meliconnect'));
}


define('MC_VERSION', '1.0.0');
define('MC_DATABASE_VERSION', '1.0.0');
define('MC_TEXTDOMAIN', 'meliconnect');

// Path absoluto al directorio del plugin. Ej: include_once MC_PLUGIN_ROOT . 'includes/class-myclass.php';
define('MC_PLUGIN_ROOT', plugin_dir_path(__FILE__));

//Para encolar scripts, estilos o acceder a otros recursos desde el front. Ej: wp_enqueue_script('my-script', MC_PLUGIN_URL . 'assets/js/my-script.js', array('jquery'), '1.0.0', true);
define('MC_PLUGIN_URL', plugin_dir_url(__FILE__));


// Include Composer autoload
if (file_exists(MC_PLUGIN_ROOT . 'vendor/autoload.php')) {
	require_once MC_PLUGIN_ROOT . 'vendor/autoload.php';
}


register_activation_hook(__FILE__, array('StoreSync\Meliconnect\Core\Initialize', 'activate'));
register_uninstall_hook(__FILE__, array('StoreSync\Meliconnect\Core\Initialize', 'uninstall'));

new StoreSync\Meliconnect\Core\Initialize();
