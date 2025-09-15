<?php

namespace Meliconnect\Meliconnect\Modules\Exporter;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Meliconnect\Meliconnect\Modules\ModuleInterface;
use Meliconnect\Meliconnect\Core\Helpers\HelperJSTranslations;

class Exporter implements ModuleInterface {


	public function init() {
		add_action( 'admin_menu', array( $this, 'registerSubmenus' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'registerModuleStyles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'registerModuleScripts' ) );
	}

	public function registerSubmenus() {
		add_submenu_page(
			'meliconnect',
			esc_html__( 'Exporter', 'meliconnect' ),
			esc_html__( 'Exporter', 'meliconnect' ),
			'meliconnect_manage_plugin',
			'meliconnect-exporter',
			array( $this, 'renderExporterPage' ),
			3
		);
	}


	public function renderExporterPage() {

		include MELICONNECT_PLUGIN_ROOT . 'includes/Modules/Exporter/Views/exporter.php';
	}

	public function registerModuleStyles( $hook ) {
		wp_enqueue_style( 'meliconnect-exporter', MELICONNECT_PLUGIN_URL . 'includes/Modules/Exporter/Assets/Css/meliconnect-exporter.css', array(), '1.0.0' );
	}

	public function registerModuleScripts( $hook ) {
		wp_register_script( 'meliconnect-exporter-js', MELICONNECT_PLUGIN_URL . 'includes/Modules/Exporter/Assets/Js/meliconnect-exporter.js', array( 'jquery' ), '1.0.0', true );

		HelperJSTranslations::localizeScript( 'meliconnect-exporter-js' );
		wp_enqueue_script( 'meliconnect-exporter-js' );
	}
}
