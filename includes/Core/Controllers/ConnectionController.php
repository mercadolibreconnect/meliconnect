<?php

namespace Meliconnect\Meliconnect\Core\Controllers;

use Meliconnect\Meliconnect\Core\Interfaces\ControllerInterface;
use Meliconnect\Meliconnect\Core\Helpers\Helper;
use Meliconnect\Meliconnect\Core\Helpers\HubApi;
use Meliconnect\Meliconnect\Core\Helpers\MeliconMeli;
use Meliconnect\Meliconnect\Core\Models\UserConnection;

class ConnectionController implements ControllerInterface
{


    public function __construct()
    {
        // Inits hooks or another configurations
        $this->loadAssets();
    }

    public function getData()
    {
        // Logic to get and return data
        $data = [];
        $data['domain'] = Helper::getDomainName();
        $data['users'] = UserConnection::getConnectedUsers();
        return $data;
    }

    public function loadAssets()
    {
        /* if (is_page('meliconnect-connection')) { */
        wp_enqueue_style('melicon-connection', MC_PLUGIN_URL . 'includes/Core/Assets/Css/melicon-connection.css', [], '1.0.0');

        wp_enqueue_script('melicon-connection', MC_PLUGIN_URL . 'includes/Core/Assets/Js/melicon-connection.js', ['jquery'], '1.0.0', true);
        /* } */
    }


    /* START HANDLE AJAX METHODS */ 


    /* START CUSTOM METHODS */

    public static function get_connection_status_tag($status)
    {

        switch ($status) {
            case 'vinculated':
                return Helper::meliconnectPrintTag('VINCULADO', 'is-success');
                break;

            default:
                return Helper::meliconnectPrintTag($status, 'is-white');
                break;
        }
    }

    
}
