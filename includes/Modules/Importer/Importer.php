<?php

namespace StoreSync\Meliconnect\Modules\Importer;

use StoreSync\Meliconnect\Core\Helpers\Helper;
use StoreSync\Meliconnect\Modules\ModuleInterface;

class Importer implements ModuleInterface {
    public function init() {
        $this->registerSubmenus();
    }

    public function registerSubmenus()
    {
        add_submenu_page(
            'meliconnect',
            __('Importer', 'meliconnect'),
            __('Importer', 'meliconnect'),
            'meliconnect_manage_plugin',
            'meliconnect-importer',
            [$this, 'renderImporterPage'],
            4
        );
    }

    public function renderImporterPage()
    {
        include MC_PLUGIN_ROOT . 'includes/Modules/Importer/Views/importer.php';
    }
}