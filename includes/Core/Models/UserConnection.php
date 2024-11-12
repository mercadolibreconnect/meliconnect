<?php

namespace StoreSync\Meliconnect\Core\Models;

use StoreSync\Meliconnect\Core\Helpers\Helper;
use StoreSync\Meliconnect\Core\Helpers\MeliconMeli;

class UserConnection
{
    private static $table_name;

    // Este método se llama automáticamente cuando se accede por primera vez a la clase
    public static function init()
    {
        global $wpdb;

        self::$table_name = $wpdb->prefix . "melicon_user_connection";
    }

    public static function getAllUsers()
    {
        global $wpdb;

        self::init();

        $table_name = self::$table_name;;

        // Realizar la consulta para obtener todos los usuarios
        $query = "SELECT * FROM {$table_name}";

        // Obtener los resultados
        $results = $wpdb->get_results($query);

        return $results;
    }

    public static function getConnectedUsers()
    {
        global $wpdb;

        self::init();

        $table_name = self::$table_name;

        // Construir la consulta SQL
        $query = "SELECT * FROM {$table_name}";

        // Preparar y ejecutar la consulta
        $results = $wpdb->get_results($query);

        return $results;
    }

    public static function getUser($user_id = null, $nickname = null)
    {
        global $wpdb;

        self::init();

        $table_name = self::$table_name;

        // Construir la consulta SQL
        $query = "SELECT * FROM {$table_name} WHERE 1=1";
        $params = array();

        // Añadir condiciones a la consulta según los parámetros
        if (!is_null($user_id)) {
            $query .= " AND user_id = %d";
            $params[] = $user_id;
        }

        if (!is_null($nickname)) {
            $query .= " AND nickname = %s";
            $params[] = $nickname;
        }

        // Preparar y ejecutar la consulta
        if (!empty($params)) {
            $query = $wpdb->prepare($query, $params);
        }

        // Obtener los resultados
        $result = $wpdb->get_results($query);

        if (count($result) === 1 && (!empty($user_id) || !empty($nickname))) {
            $result = $result[0];
        }

        return $result;
    }

    /**
     * Callback para procesar el dominio y actualizar las conexiones de usuarios.
     */
    public static function update_users_connections($users_in_domain)
    {

        global $wpdb;

        self::init();

        $table_name = self::$table_name;


        // Eliminar todas las conexiones existentes antes de insertar las nuevas
        $wpdb->query("DELETE FROM {$table_name}");

        // Insertar cada usuario recibido en la tabla `wp_melicon_user_connection`
        foreach ($users_in_domain as $user) {

            $params = ['access_token' => $user['access_token']];
            $meli = new MeliconMeli($user['app_id'], $user['secret_key'], $user['access_token']);
            $meli_user_data = $meli->get('/users/' . $user['user_id'], $params);

            //Helper::logData('Meli user data: ' . var_export($meli_user_data, true)  , 'users_in_domain');

            $insert_data = [
                'access_token'    => $user['access_token'],
                'app_id'          => $user['app_id'],
                'secret_key'      => $user['secret_key'],
                'user_id'         => $user['user_id'],
                'nickname'        => $user['nickname'],
                'permalink'       => $user['permalink'] ?? 'no-data',
                'site_id'         => $user['site_id'],
                'status'          => $user['status'], 
                'country'         => $user['country'], 
                'has_mercadoshops' => (isset($meli_user_data['body']->tags) && is_array($meli_user_data['body']->tags) && in_array('mshops', $meli_user_data['body']->tags)) ? 1 : 0,
                'meli_user_data'  => maybe_serialize($meli_user_data),
                'api_token'       => $user['api_token'],
                'created_at'      => current_time('mysql'),
                'updated_at'      => current_time('mysql'),
            ];

            $wpdb->insert($table_name, $insert_data);

            if($wpdb->last_error) {
                Helper::logData('Error creating user connection: ' . $wpdb->last_error  , 'users_in_domain');
            }else{
                Helper::logData('User connection created: ' . print_r($insert_data, true)  , 'users_in_domain');
            }


        }

        // Responder con el estado de la acción
        return true;
    }






    public static function create_update_user_connection($userData, $meli_user_data = null)
    {
        global $wpdb;

        self::init();

        $table_name = self::$table_name;

        // Obtener los datos actuales de la tabla para determinar qué usuarios están desactualizados
        $existing_users = $wpdb->get_results("SELECT user_id FROM {$table_name}", ARRAY_A);
        $existing_user_ids = array_column($existing_users, 'user_id');

        // Datos a actualizar o insertar
        $userDataToUpdate = [
            'app_id' => sanitize_text_field($userData->app_id),
            'secret_key' => sanitize_text_field($userData->secret_key),
            'access_token' => sanitize_text_field($userData->access_token),
            'refresh_token' => sanitize_text_field($userData->refresh_token),
            'expires_in' => intval($userData->expires_in),
            'user_id' => intval($userData->user_id),
            'nickname' => sanitize_text_field($userData->nickname),
            'permalink' => esc_url($userData->permalink),
            'site_id' => sanitize_text_field($userData->site_id),
            'country' => sanitize_text_field($userData->country),
            'has_mercadoshops' => (isset($meli_user_data['body']->tags) && is_array($meli_user_data['body']->tags) && in_array('mshops', $meli_user_data['body']->tags)) ? 1 : 0,
            'status' => 'vinculated',
            'updated_at' => current_time('mysql')
        ];


        // Actualizar o insertar el usuario
        $wpdb->replace(
            $table_name,
            $userDataToUpdate,
            [
                '%s', // app_id
                '%s', // secret_key
                '%s', // access_token
                '%s', // refresh_token
                '%d', // expires_in
                '%d', // user_id
                '%s', // nickname
                '%s', // permalink
                '%s', // site_id
                '%s', // country
                '%d', // has_mercadoshops
                '%s', // status
                '%s'  // updated_at
            ]
        );


        // Actualizar el estado de los usuarios que no están en los datos recibidos
        foreach ($existing_user_ids as $id) {
            if ($id != $userData->user_id) {
                $wpdb->update(
                    $table_name,
                    ['status' => 'desvinculated'],
                    ['user_id' => $id],
                    ['%s'],
                    ['%d']
                );
            }
        }

        return 'updated';
    }
}
