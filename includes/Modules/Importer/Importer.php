<?php

namespace Meliconnect\Meliconnect\Modules\Importer;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Meliconnect\Meliconnect\Core\Helpers\Helper;
use Meliconnect\Meliconnect\Modules\ModuleInterface;
use Meliconnect\Meliconnect\Core\Helpers\HelperJSTranslations;

class Importer implements ModuleInterface {
	public function init() {
		add_action( 'admin_menu', array( $this, 'registerSubmenus' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'registerModuleStyles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'registerModuleScripts' ) );
	}

	public function registerSubmenus() {
		add_submenu_page(
			'meliconnect',
			esc_html__( 'Importer', 'meliconnect' ),
			esc_html__( 'Importer', 'meliconnect' ),
			'meliconnect_manage_plugin',
			'meliconnect-importer',
			array( $this, 'renderImporterPage' ),
			4
		);
	}

	public function renderImporterPage() {
		include MELICONNECT_PLUGIN_ROOT . 'includes/Modules/Importer/Views/importer.php';
	}

	public function registerModuleStyles( $hook ) {
		wp_enqueue_style( 'meliconnect-importer', MELICONNECT_PLUGIN_URL . 'includes/Modules/Importer/Assets/Css/meliconnect-importer.css', array(), '1.0.0' );
	}

	public function registerModuleScripts( $hook ) {
		wp_register_script( 'meliconnect-importer-js', MELICONNECT_PLUGIN_URL . 'includes/Modules/Importer/Assets/Js/meliconnect-importer.js', array( 'jquery' ), '1.0.0', true );
		HelperJSTranslations::localizeScript( 'meliconnect-importer-js' );
		wp_enqueue_script( 'meliconnect-importer-js' );
	}
}
