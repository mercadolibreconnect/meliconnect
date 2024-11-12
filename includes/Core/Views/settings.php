<!-- START MCSYNCAPP -->
<div id="melicon-page-core-settings" class="melicon-app">
    <?php

    use StoreSync\Meliconnect\Core\Controllers\SettingController;

    // Crea una instancia del controlador
    $settingController = new SettingController();

    // Obtén los datos necesarios
    $data = $settingController->getData();

    $headerTitle = __('Settings', 'meliconnect');

    include MC_PLUGIN_ROOT . 'includes/Core/Views/Partials/header.php';

    ?>

    <div class="melicon-main">
        <div class="melicon-container">

            <div class="melicon-postbox melicon-intro level">
                <div class="level-left">
                    <p><?php echo __('Here you can control the general behavior of export jobs, import jobs, automatic synchronization tasks, and addons configurations.', 'meliconnect'); ?></p>
                </div>
                <div class="level-right">
                    
                </div>
            </div>

            <div id="melicon-settings-container" class="container melicon-settings-container melicon-overflow-x">
                <div id="melicon-settings-tabs" class="tabs is-toggle">
                    <ul>
                        <li data-tab="general"><a><span class="icon is-small"><i class="fas fa-cog"></i></span><span>Ajustes</span></a></li>
                        <li data-tab="export"><a><span class="icon is-small"><i class="fas fa-file-export"></i></span><span>Exportador</span></a></li>
                        <li data-tab="import"><a><span class="icon is-small"><i class="fas fa-file-import"></i></span><span>Importador</span></a></li>
                        <li data-tab="synchronizer"><a><span class="icon is-small"><i class="fas fa-sync-alt"></i></span><span>Sincronizador</span></a></li>
                    </ul>
                </div>
                <div id="tab-content" class="box">
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