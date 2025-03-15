<?php

namespace StoreSync\Meliconnect\Modules\Exporter;

use StoreSync\Meliconnect\Core\Helpers\Helper;
use StoreSync\Meliconnect\Modules\ModuleInterface;

class Exporter implements ModuleInterface
{

    public function init()
    {
        $this->registerSubmenus();
    }

    public function registerSubmenus()
    {
        add_submenu_page(
            'meliconnect',
            esc_html__('Exporter', 'meliconnect'),
            esc_html__('Exporter', 'meliconnect'),
            'meliconnect_manage_plugin',
            'meliconnect-exporter',
            [$this, 'renderExporterPage'],
            3
        );
    }

    public function renderExporterPage()
    {
        include MC_PLUGIN_ROOT . 'includes/Modules/Exporter/Views/exporter.php';
    }
}
