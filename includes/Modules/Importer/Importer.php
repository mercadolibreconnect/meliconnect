<?php

namespace Meliconnect\Meliconnect\Modules\Importer;

use Meliconnect\Meliconnect\Core\Helpers\Helper;
use Meliconnect\Meliconnect\Modules\ModuleInterface;

class Importer implements ModuleInterface {
    public function init() {
        $this->registerSubmenus();
    }

    public function registerSubmenus()
    {
        add_submenu_page(
            'meliconnect',
            esc_html__('Importer', 'meliconnect'),
            esc_html__('Importer', 'meliconnect'),
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