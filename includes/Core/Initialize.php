<?php

namespace Meliconnect\Meliconnect\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Meliconnect\Meliconnect\Core\Helpers\Helper;
use Meliconnect\Meliconnect\Core\Helpers\HelperJSTranslations;
use Meliconnect\Meliconnect\Core\Services\ProductEdit;

/**
 * Class Initialize
 *
 * @package Meliconnect\Meliconnect
 */
class Initialize {


	/**
	 * @var AddonLoader[]
	 */
	public static $addons = array();

	/**
	 * @var ModuleInterface[]
	 */
	public static $modules = array();
	public static $css_pre = 'meliconnect-';
	public static $js_pre  = 'meliconnect-';


	public function __construct() {
		if ( Helper::getOption( 'is_updating', null, false ) !== null ) {
			add_action(
				'admin_notices',
				function () {
					echo '<div class="notice notice-warning"><p>' . esc_html__( 'Meliconnect is updating, please wait.', 'meliconnect' ) . '</p></div>';
				}
			);
			return;
		}

		add_action(
			'plugins_loaded',
			function () {
				static::$addons = apply_filters( 'meliconnect_addons_load', array() );
			}
		);

		new AjaxManager();
		new ApiManager();

		add_action( 'admin_notices', array( $this, 'showAdminNotices' ) );
		add_action( 'init', array( $this, 'initApp' ), 10 );
		// add_action('init', [$this, 'testCode'], 10);

		register_activation_hook( MELICONNECT_PLUGIN_ROOT, array( $this, 'activate' ) );
		register_deactivation_hook( MELICONNECT_PLUGIN_ROOT, array( 'Initialize', 'deactivate' ) );
		register_uninstall_hook( MELICONNECT_PLUGIN_ROOT, array( 'Initialize', 'uninstall' ) );
	}

	public function showAdminNotices() {
		// update_option('meliconnect_pending_connection_notifications', 'It works!');
		$pending_connections = get_option( 'meliconnect_pending_connection_notifications', array() );

		if ( ! empty( $pending_connections ) ) {

			echo '<div class="meliconnect-notification meliconnect-is-link">
                    <button class="delete"></button>
                    ' . esc_html( $pending_connections ) . '
                </div>';

			// Limpiar las notificaciones después de mostrarlas
			delete_option( 'meliconnect_pending_connection_notifications' );
		}
	}

	public static function activate() {
		$db_manager = new DatabaseManager();
		$db_manager->create_or_update_tables();

		self::createDefaultOptions();

		update_option( 'meliconnect_db_version', MELICONNECT_DATABASE_VERSION );
	}

    

	public static function createDefaultOptions() {
		// Options with default values
		$options = array(
			'meliconnect_general_image_attachment_ids'   => array(),
			'meliconnect_general_description_template'   => '',
			'meliconnect_general_sync_type'              => 'deactive',
			'meliconnect_general_sync_items_batch'       => 10,
			'meliconnect_general_sync_frecuency_minutes' => 10,
			'meliconnect_general_sync_method'            => 'wordpress',

			'meliconnect_export_is_disabled'             => false,
			'meliconnect_export_title'                   => 'always',
			'meliconnect_export_stock'                   => 'always',
			'meliconnect_export_price'                   => 'regular_price',
			'meliconnect_export_images'                  => 'always',
			'meliconnect_export_sku'                     => 'always',
			'meliconnect_export_product_attributes'      => 'always',
			'meliconnect_export_ml_status'               => 'always',
			'meliconnect_export_variations'              => 'always',
			'meliconnect_export_description'             => 'always',
			'meliconnect_export_description_to'          => 'description',
			'meliconnect_export_type'                    => 'createAndUpdate',
			'meliconnect_export_finalize_ml'             => 'none',
			'meliconnect_export_state_paused'            => false,
			'meliconnect_export_state_closed'            => false,

			'meliconnect_import_is_disabled'             => false,
			'meliconnect_import_title'                   => 'always',
			'meliconnect_import_stock'                   => 'always',
			'meliconnect_import_price'                   => 'always',
			'meliconnect_import_images'                  => 'always',
			'meliconnect_import_sku'                     => 'always',
			'meliconnect_import_categories'              => 'always',
			'meliconnect_import_product_attributes'      => 'always',
			'meliconnect_import_ml_status'               => 'always',
			'meliconnect_import_variations'              => 'always',
			'meliconnect_import_variations_as'           => 'always',
			'meliconnect_import_description'             => 'always',
			'meliconnect_import_description_to'          => 'description',
			'meliconnect_import_type'                    => 'createAndUpdate',
			'meliconnect_import_price_variation_operand' => 'sum',
			'meliconnect_import_price_variation_amount'  => 0,
			'meliconnect_import_price_variation_type'    => 'percent',
			'meliconnect_import_stock_variation_operand' => 'sum',
			'meliconnect_import_stock_variation_amount'  => 0,
			'meliconnect_import_stock_variation_type'    => 'units',
			'meliconnect_import_state_paused'            => false,
			'meliconnect_import_state_closed'            => false,
			'meliconnect_import_by_sku'                  => false,
			'meliconnect_import_attrs'                   => false,

			'meliconnect_sync_cron_status'               => 'deactive',
			'meliconnect_sync_cron_items_batch'          => 10,
			'meliconnect_sync_cron_frecuency_minutes'    => 10,
			'meliconnect_sync_cron_method'               => 'wordpress',
			'meliconnect_sync_stock_woo_to_meli'         => false,
			'meliconnect_sync_price_woo_to_meli'         => false,
			'meliconnect_sync_status_woo_to_meli'        => false,
			'meliconnect_sync_stock_meli_to_woo'         => false,
			'meliconnect_sync_price_meli_to_woo'         => false,
			'meliconnect_sync_variations_price_meli_to_woo' => false,
		);

		foreach ( $options as $key => $default_value ) {
			if ( get_option( $key ) === false ) {
				add_option( $key, $default_value );
			}
		}
	}

	public static function deactivate() {
		// Desregister cron jobs
		CronManager::deactivate();
	}

	public static function uninstall() {
		// Delete tables
		// Delete options

		delete_option( 'meliconnect_db_version' );
	}




	public function initApp() {
		add_action( 'plugins_loaded', 'meliconnect_register_text_domain' );

		do_action( 'meliconnect_init' );

		// Registrar permisos personalizados
		$this->registerWPUserRoles();

		add_action( 'admin_menu', array( $this, 'registerMenus' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'registerStyles' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'registerScripts' ) );

		$this->loadHooks();

		$this->loadModules();

		$cron_manager = new CronManager();
		$cron_manager->registerCrons();
		$cron_manager->handleCronExecution();
	}

	private function loadHooks() {
		new ProductEdit();
	}


	private function loadModules() {
		// Ruta a los módulos
		$modulesPath = __DIR__ . '/../Modules';

		// Obtener módulos activados de la configuración, o un array vacío si no existe la opción
		$activatedModules = get_option( 'meliconnect_modules', array() );

		// Buscar todos los archivos de módulos
		foreach ( glob( $modulesPath . '/*/*.php' ) as $file ) {
			require_once $file;

			$className = $this->getClassNameFromFile( $file );
			if ( class_exists( $className ) && in_array( 'Meliconnect\Meliconnect\Modules\ModuleInterface', class_implements( $className ) ) ) {
				$moduleName = ( new \ReflectionClass( $className ) )->getShortName();

				// Cargar el módulo si está en la lista de módulos activados o si no hay módulos activados especificados
				if ( empty( $activatedModules ) || in_array( $moduleName, $activatedModules ) ) {
					$module = new $className();
					$module->init();
					self::$modules[] = $module;
				}
			}
		}
	}

	private function getClassNameFromFile( $file ) {
		// Convertir la ruta del archivo a un nombre de clase
		$path = str_replace( array( __DIR__ . '/../', '.php' ), array( '', '' ), $file );
		return 'Meliconnect\\Meliconnect\\' . str_replace( '/', '\\', ucfirst( $path ) );
	}

	public function registerWPUserRoles() {
		\add_role(
			'meliconnect_manager',
			'Meliconnect Manager',
			array(
				'read'                      => false,
				'edit_posts'                => false,
				'upload_files'              => false,
				'meliconnect_manage_plugin' => true,
			)
		);

		$admin = get_role( 'administrator' );

		if ( $admin ) {
			$admin->add_cap( 'meliconnect_manage_plugin' );
		}
	}


	public function registerStyles( $hook ) {

		wp_enqueue_style(
			self::$css_pre . 'all-pages',
			MELICONNECT_PLUGIN_URL . 'assets/css/all-pages.css',
			array(),
			'1.0.0'
		);

		// Solo páginas del plugin
		if ( $this->is_plugin_page() ) {
			wp_enqueue_style( self::$css_pre . 'bulma-divider-css', MELICONNECT_PLUGIN_URL . 'assets/css/bulma/bulma-divider.min.css', array(), '1.0.1' );
			wp_enqueue_style( self::$css_pre . 'plugin-pages', MELICONNECT_PLUGIN_URL . 'assets/css/plugin-pages.css', array(), '1.0.0' );
		}

		// Si estás en páginas de WordPress que usa tu plugin
		if ( $this->is_wordpress_page_used_by_plugin() ) {
			wp_enqueue_style( self::$css_pre . 'wordpress-pages', MELICONNECT_PLUGIN_URL . 'assets/css/wordpress-pages.css', array(), '1.0.0' );
		}

		// En común para plugin + páginas WP relacionadas
		if ( $this->is_plugin_page() || $this->is_wordpress_page_used_by_plugin() ) {
			wp_enqueue_style( self::$css_pre . 'font-awesome-5', MELICONNECT_PLUGIN_URL . 'assets/css/font-awesome/css/all.min.css', array(), '5' );
			wp_enqueue_style( self::$css_pre . 'font-awesome-brands', MELICONNECT_PLUGIN_URL . 'assets/css/font-awesome/css/brands.min.css', array(), '5' );
			wp_enqueue_style( self::$css_pre . 'font-awesome-solid', MELICONNECT_PLUGIN_URL . 'assets/css/font-awesome/css/solid.min.css', array(), '5' );
			wp_enqueue_style( self::$css_pre . 'font-awesome-duotone', MELICONNECT_PLUGIN_URL . 'assets/css/font-awesome/css/duotone.min.css', array(), '5' );
			wp_enqueue_style( self::$css_pre . 'bulma-switch-css', MELICONNECT_PLUGIN_URL . 'assets/css/bulma/bulma-switch.min.css', array(), '1.0.1' );
			wp_enqueue_style( self::$css_pre . 'select2', MELICONNECT_PLUGIN_URL . 'assets/css/select2/select2.min.css', array(), '4.1.0' );
			wp_enqueue_style( self::$css_pre . 'swal-css', MELICONNECT_PLUGIN_URL . 'assets/css/sweetalert/sweetalert2.min.css', array(), '11.4.8', false );
			wp_enqueue_style( self::$css_pre . 'meliconnect-custom', MELICONNECT_PLUGIN_URL . 'assets/css/meliconnect-custom.css', array(), '1.0.0' );

			/* Connection page */
			wp_enqueue_style( 'meliconnect-connection', MELICONNECT_PLUGIN_URL . 'includes/Core/Assets/Css/meliconnect-connection.css', array(), '1.0.0' );

			/* Setting page */
			wp_enqueue_style( 'meliconnect-setting', MELICONNECT_PLUGIN_URL . 'includes/Core/Assets/Css/meliconnect-setting.css', array(), '1.0.0' );
		}
	}


	protected function is_wordpress_page_used_by_plugin() {
		// phpcs:disable WordPress.Security.NonceVerification.Recommended -- Solo lectura de parámetros de URL.
		if (
		isset( $_GET['post'], $_GET['action'] )
		&& sanitize_text_field( wp_unslash( $_GET['action'] ) ) === 'edit'
		&& get_post_type( absint( wp_unslash( $_GET['post'] ) ) ) === 'product'
		) {
			if ( ! $this->is_plugin_page() ) {
				return true;
			}
		}
        // phpcs:enable WordPress.Security.NonceVerification.Recommended

		return false;
	}

	protected function is_plugin_page() {
        // phpcs:disable WordPress.Security.NonceVerification.Recommended -- Solo lectura de parámetros de URL.
		if ( ! isset( $_GET['page'] ) ) {
			return false;
		}

		$page = sanitize_text_field( wp_unslash( $_GET['page'] ) );
        // phpcs:enable WordPress.Security.NonceVerification.Recommended

		return strpos( $page, 'meliconnect' ) !== false;
	}

	public function registerScripts( $hook ) {

		if ( ! $this->is_plugin_page() && ! $this->is_wordpress_page_used_by_plugin() ) {
			return;
		}

		wp_enqueue_script(
			'meliconnect-swal-js',
			MELICONNECT_PLUGIN_URL . 'assets/js/sweetalert/sweetalert2.all.min.js',
			array( 'jquery' ),
			'11.4.8',
			true
		);

		wp_enqueue_script(
			self::$js_pre . 'select-2',
			MELICONNECT_PLUGIN_URL . 'assets/js/select2/select2.min.js',
			array( 'jquery' ),
			'1.0.0',
			true
		);

		wp_register_script(
			self::$js_pre . 'general-script',
			MELICONNECT_PLUGIN_URL . 'assets/js/meliconnect-general.js',
			array( 'jquery' ),
			'1.0.0',
			true
		);

		HelperJSTranslations::localizeScript( self::$js_pre . 'general-script' );
		wp_enqueue_script( self::$js_pre . 'general-script' );

		/* Connection page */
		wp_enqueue_script( 'meliconnect-connection', MELICONNECT_PLUGIN_URL . 'includes/Core/Assets/Js/meliconnect-connection.js', array( 'jquery' ), '1.0.0', true );

		/* Setting page */
		wp_enqueue_script( 'meliconnect-setting', MELICONNECT_PLUGIN_URL . 'includes/Core/Assets/Js/meliconnect-setting.js', array( 'jquery' ), '1.0.0', true );

		/* Product edit page */
		if ( $this->is_wordpress_page_used_by_plugin() ) {
            // phpcs:disable WordPress.Security.NonceVerification.Recommended -- Solo lectura de parámetros de URL.
			$post_id = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : 0;
            // phpcs:enable WordPress.Security.NonceVerification.Recommended

			// Verifica que el tipo de post sea 'product'
			if ( get_post_type( $post_id ) === 'product' ) {
				wp_enqueue_script( 'meliconnect-product-edit-js', MELICONNECT_PLUGIN_URL . 'includes/Core/Assets/Js/meliconnect-product-edit.js', array( 'jquery' ), '1.0.0', true );
			}
		}
	}

	public function registerMenus() {

		add_menu_page(
			esc_html__( 'MeliConnect', 'meliconnect' ), // page_title
			esc_html__( 'MeliConnect', 'meliconnect' ), // menu_title
			'meliconnect_manage_plugin', // capability
			'meliconnect', // menu_slug
			array( $this, 'renderConnectionPage' ), // callback
			'dashicons-admin-generic', // icon_url
			6 // position
		);

		add_submenu_page(
			'meliconnect', // parent_slug
			esc_html__( 'Connection', 'meliconnect' ), // page_title
			esc_html__( 'Connection', 'meliconnect' ), // menu_title
			'meliconnect_manage_plugin', // capability
			'meliconnect-connection', // menu_slug
			array( $this, 'renderConnectionPage' ), // callback,
			1 // position
		);

		add_submenu_page(
			'meliconnect',
			esc_html__( 'Settings', 'meliconnect' ),
			esc_html__( 'Settings', 'meliconnect' ),
			'meliconnect_manage_plugin',
			'meliconnect-settings',
			array( $this, 'renderSettingsPage' ),
			10
		);
		add_submenu_page(
			'meliconnect',                     // Slug del menú padre
			__( 'Logs', 'meliconnect' ),    // Título de la página
			__( 'Logs', 'meliconnect' ),    // Texto en el menú
			'meliconnect_manage_plugin',      // Capacidad requerida
			'meliconnect-logs',               // Slug del submenu
			function () {
				// Redirección automática al abrir el submenú
				wp_redirect( admin_url( 'admin.php?page=wc-status&tab=logs&source=meliconnect-' ) );
				exit;
			}
		);

		// Add a page not in submenu
		/*
		add_submenu_page(
			'meliconnect-no-page'
			, esc_html__('Pagina interna', 'meliconnect'),
			null,
			'meliconnect_manage_plugin',
			'meliconnect-premium-query-gdt',
			[$this, 'meliconnect_premium_query_gdt']
		); */
	}

	public function renderMainPage() {
		include plugin_dir_path( __FILE__ ) . '/Views/main.php';
	}

	public function renderSettingsPage() {
		include plugin_dir_path( __FILE__ ) . '/Views/settings.php';
	}


	public function renderConnectionPage() {
		include plugin_dir_path( __FILE__ ) . '/Views/connection.php';
	}
}
