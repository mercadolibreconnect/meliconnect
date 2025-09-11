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

include MC_PLUGIN_ROOT . 'includes/Core/Views/Partials/header.php';

?>
<!-- START MCSYNCAPP -->
<div id="melicon-page-core-settings" class="melicon-app">
    <div class="melicon-main">
        <div class="melicon-container">

            <div class="melicon-postbox melicon-intro melicon-level ">
                <div class="melicon-level-left">
                    <p><?php esc_html_e('Here you can control the general behavior of export jobs, import jobs, automatic synchronization tasks, and addons configurations.', 'meliconnect'); ?></p>
                </div>
                <div class="melicon-level-right">

                </div>
            </div>

            <div id="melicon-settings-container" class="melicon-container melicon-settings-container melicon-overflow-x">
                <div id="melicon-settings-tabs" class="melicon-tabs melicon-is-toggle">
                    <ul>
                        <li data-tab="general"><a><span class="melicon-icon melicon-is-small"><i class="fas fa-cog"></i></span><span><?php esc_html_e('General', 'meliconnect'); ?></span></a></li>
                        <li data-tab="export"><a><span class="melicon-icon melicon-is-small"><i class="fas fa-file-export"></i></span><span><?php esc_html_e('Exporter', 'meliconnect'); ?></span></a></li>
                        <li data-tab="import"><a><span class="melicon-icon melicon-is-small"><i class="fas fa-file-import"></i></span><span><?php esc_html_e('Importer', 'meliconnect'); ?></span></a></li>
                        <li data-tab="synchronizer"><a><span class="melicon-icon melicon-is-small"><i class="fas fa-sync-alt"></i></span><span> <?php esc_html_e('Synchronizer', 'meliconnect'); ?></span></a></li>
                    </ul>
                </div>
                <div id="tab-content" class="melicon-box">
                    <div id="setting-loader" style="display: none;">
                        <p><i class="fas fa-spinner fa-spin"></i> Cargando...</p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <?php include MC_PLUGIN_ROOT . 'includes/Core/Views/Partials/footer.php'; ?>
</div>
<!-- END MCSYNCAPP -->