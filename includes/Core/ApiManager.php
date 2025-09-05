<?php

namespace Meliconnect\Meliconnect\Core;

use Meliconnect\Meliconnect\Core\Helpers\Helper;
use Meliconnect\Meliconnect\Core\Models\UserConnection;

class ApiManager {

    public function __construct() {
        // Registrar la ruta de la API al inicializar el API REST
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    /**
     * Registrar las rutas de la API
     */
    public function register_routes() {
        register_rest_route('meliconnect/v1', '/update_domain', [
            'methods'  => 'POST',
            'callback' => [$this, 'update_domains'],
            'permission_callback' => '__return_true',
        ]);
    }

    /**
     * Callback para procesar el dominio
     */
    public function update_domains($request) {
        $users_in_domain = $request->get_param('users_in_domain');

        if (!is_array($users_in_domain)) {
            Helper::logData('Received bad parameters in request: ' . wp_json_encode($request->get_params())  , 'users_in_domain');

            return rest_ensure_response([
                'message' => 'Missing required parameters.',
                'success' => false
            ]);
        }

        Helper::logData('Received connected users: ' . wp_json_encode($users_in_domain) . '', 'users_in_domain');
      
        UserConnection::update_users_connections($users_in_domain);

        // Responder con el estado de la acción
        return rest_ensure_response([
            'message' => 'Users in domain updated successfully.',
            'success' => true
        ]);
    }

    
}
