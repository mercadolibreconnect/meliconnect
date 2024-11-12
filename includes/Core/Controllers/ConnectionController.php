<?php

namespace StoreSync\Meliconnect\Core\Controllers;

use StoreSync\Meliconnect\Core\Interfaces\ControllerInterface;
use StoreSync\Meliconnect\Core\Helpers\Helper;
use StoreSync\Meliconnect\Core\Helpers\HubApi;
use StoreSync\Meliconnect\Core\Helpers\MeliconMeli;
use StoreSync\Meliconnect\Core\Models\UserConnection;

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
        $data['domain'] = self::getDomainName();
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

    public static function getDomainName()
    {
        // Check if HTTPS is enabled
        $scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';

        // Get the host
        $host = $_SERVER['HTTP_HOST'] ?? '';

        // Get PHP_SELF to build the full URL
        $self = $_SERVER['PHP_SELF'] ?? '';

        // Build the full URL
        $fullUrl = filter_var("$scheme://$host$self", FILTER_SANITIZE_URL);

        // Find the position of 'wp-admin' in the URL
        $wpAdminPos = strpos($fullUrl, '/wp-admin');

        if ($wpAdminPos !== false) {
            // Cut the URL before 'wp-admin'
            $domainUrl = substr($fullUrl, 0, $wpAdminPos);
        } else {
            // If 'wp-admin' is not found, use the full URL
            $domainUrl = $fullUrl;
        }

        // Remove trailing slash if it exists
        $domainUrl = rtrim($domainUrl, '/');

        return $domainUrl;
    }
}
