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
                </div>
                <div class="level-right">
                </div>
            </div>

            <div id="melicon-settings-container" class="container melicon-settings-container melicon-overflow-x">
                <div id="sync-hub-settings-spinner" class="melicon-card">
                    <div class="columns">
                        <div class="column is-12">
                            <p><i class="fa fa-spinner fa-spin" style="font-size:20px;"></i> <?php echo __('Loading settings', 'meliconnect'); ?> ...</p>
                        </div>
                    </div>

                </div>
                <div id="">
                </div>
            </div>

        </div>
    </div>

    <?php include MC_PLUGIN_ROOT . 'includes/Core/Views/Partials/footer.php'; ?>
</div>
<!-- END MCSYNCAPP -->