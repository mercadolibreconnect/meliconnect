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

    }

    public function getData()
    {
        // Logic to get and return data
        $data = [];
        $data['domain'] = Helper::getDomainName();
        $data['users'] = UserConnection::getConnectedUsers();
        return $data;
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
