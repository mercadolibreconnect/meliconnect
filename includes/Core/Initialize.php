<?php

namespace Meliconnect\Meliconnect\Core;

use Meliconnect\Meliconnect\Core\Helpers\Helper;
use Meliconnect\Meliconnect\Core\Helpers\HelperJSTranslations;
use Meliconnect\Meliconnect\Core\Services\ProductEdit;

/**
 * Class Initialize
 * @package Meliconnect\Meliconnect
 */
class Initialize
{

    /**
     * @var AddonLoader[]
     */
    public static $addons = [];

    /**
     * @var ModuleInterface[]
     */
    public static $modules = [];
    public static $css_pre = 'melicon-';
    public static $js_pre  = 'melicon-';


    public function __construct()
    {
        if (Helper::getOption('is_updating', null, false) !== null) {
            add_action('admin_notices', function () {
                echo '<div class="notice notice-warning"><p>' . esc_html__('Meliconnect is updating, please wait.', 'meliconnect') . '</p></div>';
            });
            return;
        }

        add_action('plugins_loaded', function () {
            static::$addons = apply_filters('meliconnect_addons_load', []);
        });

        new AjaxManager();
        new ApiManager();

        add_action('admin_notices', [$this, 'showAdminNotices']);
        add_action('init', [$this, 'initApp'], 10);
        //add_action('init', [$this, 'testCode'], 10);


        register_activation_hook(MC_PLUGIN_ROOT, array($this, 'activate'));
        register_deactivation_hook(MC_PLUGIN_ROOT, array('Initialize', 'deactivate'));
        register_uninstall_hook(MC_PLUGIN_ROOT, array('Initialize', 'uninstall'));
    }

    public function showAdminNotices()
    {
        //update_option('melicon_pending_connection_notifications', 'It works!');
        $pending_connections = get_option('melicon_pending_connection_notifications', []);


        if (!empty($pending_connections)) {


            echo '<div class="melicon-notification melicon-is-link">
                    <button class="delete"></button>
                    ' . esc_html($pending_connections) . '
                </div>';


            // Limpiar las notificaciones después de mostrarlas
            delete_option('melicon_pending_connection_notifications');
        }
    }

    public static function activate()
    {
        $db_manager = new DatabaseManager();
        $db_manager->create_or_update_tables();

        self::createDefaultOptions();

        update_option('meliconnect_db_version', MC_DATABASE_VERSION);
    }

    public static function createDefaultOptions()
    {
        // Options with default values
        $options = [
            'melicon_general_image_attachment_ids' => [],
            'melicon_general_description_template' => '',
            'melicon_general_sync_type' => 'deactive',
            'melicon_general_sync_items_batch' => 10,
            'melicon_general_sync_frecuency_minutes' => 10,
            'melicon_general_sync_method' => 'wordpress',

            'melicon_export_is_disabled' => false,
            'melicon_export_title' => 'always',
            'melicon_export_stock' => 'always',
            'melicon_export_price' => 'regular_price',
            'melicon_export_images' => 'always',
            'melicon_export_sku' => 'always',
            'melicon_export_product_attributes' => 'always',
            'melicon_export_ml_status' => 'always',
            'melicon_export_variations' => 'always',
            'melicon_export_description' => 'always',
            'melicon_export_description_to' => 'description',
            'melicon_export_type' => 'createAndUpdate',
            'melicon_export_finalize_ml' => 'none',
            'melicon_export_state_paused' => false,
            'melicon_export_state_closed' => false,


            'melicon_import_is_disabled' => false,
            'melicon_import_title' => 'always',
            'melicon_import_stock' => 'always',
            'melicon_import_price' => 'always',
            'melicon_import_images' => 'always',
            'melicon_import_sku' => 'always',
            'melicon_import_categories' => 'always',
            'melicon_import_product_attributes' => 'always',
            'melicon_import_ml_status' => 'always',
            'melicon_import_variations' => 'always',
            'melicon_import_variations_as' => 'always',
            'melicon_import_description' => 'always',
            'melicon_import_description_to' => 'description',
            'melicon_import_type' => 'createAndUpdate',
            'melicon_import_price_variation_operand' => 'sum',
            'melicon_import_price_variation_amount' => 0,
            'melicon_import_price_variation_type' => 'percent',
            'melicon_import_stock_variation_operand' => 'sum',
            'melicon_import_stock_variation_amount' => 0,
            'melicon_import_stock_variation_type' => 'units',
            'melicon_import_state_paused' => false,
            'melicon_import_state_closed' => false,
            'melicon_import_by_sku' => false,
            'melicon_import_attrs' => false,

            'melicon_sync_cron_status' => 'deactive',
            'melicon_sync_cron_items_batch' => 10,
            'melicon_sync_cron_frecuency_minutes' => 10,
            'melicon_sync_cron_method' => 'wordpress',
            'melicon_sync_stock_woo_to_meli' => false,
            'melicon_sync_price_woo_to_meli' => false,
            'melicon_sync_status_woo_to_meli' => false,
            'melicon_sync_stock_meli_to_woo' => false,
            'melicon_sync_price_meli_to_woo' => false,
            'melicon_sync_variations_price_meli_to_woo' => false,
        ];


        foreach ($options as $key => $default_value) {
            if (get_option($key) === false) {
                add_option($key, $default_value);
            }
        }
    }

    public static function deactivate()
    {
        // Desregister cron jobs
        CronManager::deactivate();
    }

    public static function uninstall()
    {
        //Delete tables 
        //Delete options

        delete_option('meliconnect_db_version');
    }




    public function initApp()
    {
        self::registerTextDomain();
        add_action('plugins_loaded', 'mc_register_text_domain');

        do_action('meliconnect_init');

        // Registrar permisos personalizados
        $this->registerWPUserRoles();

        add_action('admin_menu', [$this, 'registerMenus']);

        add_action('admin_enqueue_scripts', [$this, 'registerStyles']);

        add_action('admin_enqueue_scripts', [$this, 'registerScripts']);


        $this->loadHooks();

        $this->loadModules();

        $cron_manager = new CronManager();
        $cron_manager->registerCrons();
        $cron_manager->handleCronExecution();
    }

    private function loadHooks()
    {
        new ProductEdit();
    }


    private function loadModules()
    {
        // Ruta a los módulos
        $modulesPath = __DIR__ . '/../Modules';

        // Obtener módulos activados de la configuración, o un array vacío si no existe la opción
        $activatedModules = get_option('meliconnect_modules', []);

        // Buscar todos los archivos de módulos
        foreach (glob($modulesPath . '/*/*.php') as $file) {
            require_once $file;

            $className = $this->getClassNameFromFile($file);
            if (class_exists($className) && in_array('Meliconnect\Meliconnect\Modules\ModuleInterface', class_implements($className))) {
                $moduleName = (new \ReflectionClass($className))->getShortName();

                // Cargar el módulo si está en la lista de módulos activados o si no hay módulos activados especificados
                if (empty($activatedModules) || in_array($moduleName, $activatedModules)) {
                    $module = new $className();
                    $module->init();
                    self::$modules[] = $module;
                }
            }
        }
    }

    private function getClassNameFromFile($file)
    {
        // Convertir la ruta del archivo a un nombre de clase
        $path = str_replace([__DIR__ . '/../', '.php'], ['', ''], $file);
        return 'Meliconnect\\Meliconnect\\' . str_replace('/', '\\', ucfirst($path));
    }

    public function registerWPUserRoles()
    {
        \add_role('meliconnect_manager', 'Meliconnect Manager', [
            'read'         => false,
            'edit_posts'   => false,
            'upload_files' => false,
            'meliconnect_manage_plugin' => true,
        ]);

        $admin = get_role('administrator');

        if ($admin) {
            $admin->add_cap('meliconnect_manage_plugin');
        }
    }


    public function registerStyles($hook)
    {

        wp_enqueue_style(
            self::$css_pre . 'all-pages',
            MC_PLUGIN_URL . 'assets/css/all-pages.css',
            [],
            '1.0.0'
        );

        // Solo páginas del plugin
        if ($this->is_plugin_page()) {
            wp_enqueue_style(self::$css_pre . 'bulma-divider-css', MC_PLUGIN_URL . 'assets/css/bulma/bulma-divider.min.css', [], '1.0.1');
            wp_enqueue_style(self::$css_pre . 'plugin-pages', MC_PLUGIN_URL . 'assets/css/plugin-pages.css', [], '1.0.0');
        }

        // Si estás en páginas de WordPress que usa tu plugin
        if ($this->is_wordpress_page_used_by_plugin()) {
            wp_enqueue_style(self::$css_pre . 'wordpress-pages', MC_PLUGIN_URL . 'assets/css/wordpress-pages.css', [], '1.0.0');
        }

        // En común para plugin + páginas WP relacionadas
        if ($this->is_plugin_page() || $this->is_wordpress_page_used_by_plugin()) {
            wp_enqueue_style(self::$css_pre . 'font-awesome-5', MC_PLUGIN_URL . 'assets/css/font-awesome/css/all.min.css', [], '5');
            wp_enqueue_style(self::$css_pre . 'font-awesome-brands', MC_PLUGIN_URL . 'assets/css/font-awesome/css/brands.min.css', [], '5');
            wp_enqueue_style(self::$css_pre . 'font-awesome-solid', MC_PLUGIN_URL . 'assets/css/font-awesome/css/solid.min.css', [], '5');
            wp_enqueue_style(self::$css_pre . 'font-awesome-duotone', MC_PLUGIN_URL . 'assets/css/font-awesome/css/duotone.min.css', [], '5');
            wp_enqueue_style(self::$css_pre . 'bulma-switch-css', MC_PLUGIN_URL . 'assets/css/bulma/bulma-switch.min.css', [], '1.0.1');
            wp_enqueue_style(self::$css_pre . 'select2', MC_PLUGIN_URL . 'assets/css/select2/select2.min.css', [], '4.1.0');
            wp_enqueue_style(self::$css_pre . 'swal-css', MC_PLUGIN_URL . 'assets/css/sweetalert/sweetalert2.min.css', [], '11.4.8', false);
            wp_enqueue_style(self::$css_pre . 'melicon-custom', MC_PLUGIN_URL . 'assets/css/melicon-custom.css', [], '1.0.0');

            /* Connection page */
            wp_enqueue_style('melicon-connection', MC_PLUGIN_URL . 'includes/Core/Assets/Css/melicon-connection.css', [], '1.0.0');

            /* Setting page */
            wp_enqueue_style('melicon-setting', MC_PLUGIN_URL . 'includes/Core/Assets/Css/melicon-setting.css', [], '1.0.0');
        }
    }


    protected function is_wordpress_page_used_by_plugin()
    {
        // Load on product edit page
        if (
            isset($_GET['post'], $_GET['action'])
            && sanitize_text_field(wp_unslash($_GET['action'])) === 'edit'
            && get_post_type(absint(wp_unslash($_GET['post']))) === 'product'
        ) {
            if (! $this->is_plugin_page()) {
                return true;
            }
        }

        return false;
    }

    protected function is_plugin_page()
    {
        if ( ! isset( $_GET['page'] ) ) {
            return false;
        }

        $page = sanitize_text_field( wp_unslash( $_GET['page'] ) );

        return strpos( $page, 'meliconnect' ) !== false;
    }

    public function registerScripts($hook)
    {

        if (!$this->is_plugin_page() && !$this->is_wordpress_page_used_by_plugin()) {
            return;
        }

        wp_enqueue_script(
            'melicon-swal-js',
            MC_PLUGIN_URL . 'assets/js/sweetalert/sweetalert2.all.min.js',
            ['jquery'],
            '11.4.8',
            true
        );

        wp_enqueue_script(
            self::$js_pre . 'select-2',
            MC_PLUGIN_URL . 'assets/js/select2/select2.min.js',
            ['jquery'],
            '1.0.0',
            true
        );

        wp_register_script(
            self::$js_pre . 'general-script',
            MC_PLUGIN_URL . 'assets/js/melicon-general.js',
            ['jquery'],
            '1.0.0',
            true
        );

        HelperJSTranslations::localizeScript(self::$js_pre . 'general-script');
        wp_enqueue_script(self::$js_pre . 'general-script');

        /* Connection page */
        wp_enqueue_script('melicon-connection', MC_PLUGIN_URL . 'includes/Core/Assets/Js/melicon-connection.js', ['jquery'], '1.0.0', true);

        /* Setting page */
        wp_enqueue_script('melicon-setting', MC_PLUGIN_URL . 'includes/Core/Assets/Js/melicon-setting.js', ['jquery'], '1.0.0', true);

        /* Product edit page */
        if ($this->is_wordpress_page_used_by_plugin()) {
            $post_id = absint($_GET['post']); // Sanitizar el ID del post

            // Verifica que el tipo de post sea 'product'
            if (get_post_type($post_id) === 'product') {
                wp_enqueue_script('melicon-product-edit-js', MC_PLUGIN_URL . 'includes/Core/Assets/Js/melicon-product-edit.js', ['jquery'], '1.0.0', true);
            }
        }
    }

    public function registerMenus()
    {


        add_menu_page(
            esc_html__('MeliConnect', 'meliconnect'), // page_title
            esc_html__('MeliConnect', 'meliconnect'), // menu_title
            'meliconnect_manage_plugin', // capability
            'meliconnect', // menu_slug
            [$this, 'renderConnectionPage'], // callback
            'dashicons-admin-generic', // icon_url
            6 // position
        );

        add_submenu_page(
            'meliconnect', //parent_slug
            esc_html__('Connection', 'meliconnect'), // page_title
            esc_html__('Connection', 'meliconnect'), // menu_title
            'meliconnect_manage_plugin', // capability
            'meliconnect-connection', // menu_slug
            [$this, 'renderConnectionPage'], // callback,
            1 // position
        );


        add_submenu_page(
            'meliconnect',
            esc_html__('Settings', 'meliconnect'),
            esc_html__('Settings', 'meliconnect'),
            'meliconnect_manage_plugin',
            'meliconnect-settings',
            [$this, 'renderSettingsPage'],
            10
        );

        //Add a page not in submenu
        /* add_submenu_page(
            'meliconnect-no-page'
            , esc_html__('Pagina interna', 'meliconnect'), 
            null, 
            'meliconnect_manage_plugin', 
            'meliconnect-premium-query-gdt', 
            [$this, 'meliconnect_premium_query_gdt']
        ); */
    }

    public function renderMainPage()
    {
        include plugin_dir_path(__FILE__)  . '/Views/main.php';
    }

    public function renderSettingsPage()
    {
        include plugin_dir_path(__FILE__)  . '/Views/settings.php';
    }

    /* public function renderImporterPage()
    {
        include plugin_dir_path(__FILE__)  .  '/Views/importer.php';
    }

    public function renderExporterPage()
    {
        include plugin_dir_path(__FILE__)  . '/Views/exporter.php';
    } */

    public function renderConnectionPage()
    {
        include plugin_dir_path(__FILE__) . '/Views/connection.php';
    }


    public static function registerTextDomain()
    {
        // phpcs:ignore PluginCheck.CodeAnalysis.DiscouragedFunctions.load_plugin_textdomainFound
        load_plugin_textdomain('meliconnect', false, 'meliconnect/languages');
    }
}
