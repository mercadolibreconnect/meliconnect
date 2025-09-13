<?php
if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

use Meliconnect\Meliconnect\Core\Controllers\SettingController;

// Crea una instancia del controlador
$settingController = new SettingController();

// Obtén los datos necesarios
$data = $settingController->getData();

$headerTitle = esc_html__('Settings', 'meliconnect');

include MELICONNECT_PLUGIN_ROOT . 'includes/Core/Views/Partials/header.php';

?>
<!-- START MCSYNCAPP -->
<div id="meliconnect-page-core-settings" class="meliconnect-app">
    <div class="meliconnect-main">
        <div class="meliconnect-container">

            <div class="meliconnect-postbox meliconnect-intro meliconnect-level ">
                <div class="meliconnect-level-left">
                    <p><?php esc_html_e('Here you can control the general behavior of export jobs, import jobs, automatic synchronization tasks, and addons configurations.', 'meliconnect'); ?></p>
                </div>
                <div class="meliconnect-level-right">

                </div>
            </div>

            <div id="meliconnect-settings-container" class="meliconnect-container meliconnect-settings-container meliconnect-overflow-x">
                <div id="meliconnect-settings-tabs" class="meliconnect-tabs meliconnect-is-toggle">
                    <ul>
                        <li data-tab="general"><a><span class="meliconnect-icon meliconnect-is-small"><i class="fas fa-cog"></i></span><span><?php esc_html_e('General', 'meliconnect'); ?></span></a></li>
                        <li data-tab="export"><a><span class="meliconnect-icon meliconnect-is-small"><i class="fas fa-file-export"></i></span><span><?php esc_html_e('Exporter', 'meliconnect'); ?></span></a></li>
                        <li data-tab="import"><a><span class="meliconnect-icon meliconnect-is-small"><i class="fas fa-file-import"></i></span><span><?php esc_html_e('Importer', 'meliconnect'); ?></span></a></li>
                        <li data-tab="synchronizer"><a><span class="meliconnect-icon meliconnect-is-small"><i class="fas fa-sync-alt"></i></span><span> <?php esc_html_e('Synchronizer', 'meliconnect'); ?></span></a></li>
                    </ul>
                </div>
                <div id="tab-content" class="meliconnect-box">
                    <div id="setting-loader" style="display: none;">
                        <p><i class="fas fa-spinner fa-spin"></i> Cargando...</p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <?php include MELICONNECT_PLUGIN_ROOT . 'includes/Core/Views/Partials/footer.php'; ?>
</div>
<!-- END MCSYNCAPP -->