<?php

namespace Meliconnect\Meliconnect\Core\Models;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Meliconnect\Meliconnect\Core\Helpers\Helper;
use Meliconnect\Meliconnect\Core\Helpers\MeliconMeli;

class UserConnection {

	private static $table_name;

	// Este método se llama automáticamente cuando se accede por primera vez a la clase
	public static function init() {
		global $wpdb;

		self::$table_name = $wpdb->prefix . 'meliconnect_user_connection';
	}

	public static function getSellersExceedingLimit() {
		global $wpdb;

		self::init();

		$table_name = self::$table_name;

		// Solo seleccionamos el nickname
	    // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$results = $wpdb->get_col( "SELECT nickname FROM {$table_name} WHERE pending_connections <= 0" );

		// Si no hay resultados, devolvemos null
		return ! empty( $results ) ? $results : null;
	}

	public static function getSellersByPlanComparison( $plan, $operator = '=' ) {
		global $wpdb;
		self::init();

		$table_name = self::$table_name;

		if ( ! in_array( $operator, array( '=', '!=' ), true ) ) {
			$operator = '='; // valor por defecto seguro
		}

	    // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$query = "SELECT nickname FROM {$table_name} WHERE plan_type {$operator} %s";

		$results = $wpdb->get_col( $wpdb->prepare( $query, $plan ) );

		return ! empty( $results ) ? $results : array();
	}



	public static function getConnectedUsers() {
		global $wpdb;

		self::init();

		$table_name = self::$table_name;

		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$results = $wpdb->get_results( "SELECT * FROM {$table_name}" );

		return $results;
	}

	public static function getUser( $user_id = null ) {
		global $wpdb;

		self::init();

		$table_name = self::$table_name;

        // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		// Obtener resultados según si se proporciona $user_id
		if ( ! is_null( $user_id ) ) {
			$result = $wpdb->get_results(
				$wpdb->prepare( "SELECT * FROM {$table_name} WHERE user_id = %d", $user_id )
			);
		} else {
			$result = $wpdb->get_results( "SELECT * FROM {$table_name}" );
		}
        // phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared

		// Si hay exactamente un resultado, retornarlo directamente
		if ( count( $result ) === 1 ) {
			$result = $result[0];
		}

		return $result;
	}

    /**
     * Obtiene el access_token de un seller_id en wp_meliconnect_user_connection
     *
     * @param int|string $seller_id
     * @return string|null
     */
    public static function get_meli_access_token_by_seller( $seller_id ) {
        global $wpdb;

        self::init();

		$table_name = self::$table_name;

        // Nombre real de la tabla (con prefijo de WP)
        $table_name = $wpdb->prefix . 'meliconnect_user_connection';

        // Query segura
        $access_token = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT access_token 
                FROM $table_name 
                WHERE user_id = %s 
                LIMIT 1",
                $seller_id
            )
        );

        return $access_token ?: null;
    }


	/**
	 * Callback para procesar el dominio y actualizar las conexiones de usuarios.
	 */
	public static function update_users_connections( $users_in_domain ) {

		global $wpdb;

		self::init();

		$table_name = self::$table_name;

		$pending_count = 0;

		// Eliminar todas las conexiones existentes antes de insertar las nuevas
        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$wpdb->query( "DELETE FROM {$table_name}" );

		// Insertar cada usuario recibido en la tabla `wp_meliconnect_user_connection`
		foreach ( $users_in_domain as $user ) {

			if ( is_null( $user['access_token'] ) && is_null( $user['user_id'] ) ) {
				// Guardar un mensaje de notificación en caso de estar pendiente
				++$pending_count;
				continue; // Saltar la inserción en la base de datos para este caso
			}

			$params         = array( 'access_token' => $user['access_token'] );
			$meli           = new MeliconMeli( $user['app_id'], $user['secret_key'], $user['access_token'] );
			$meli_user_data = $meli->get( '/users/' . $user['user_id'], $params );

			// Helper::logData('Meli user data: ' . wp_json_encode($meli_user_data)  , 'users_in_domain');
			// Helper::logData('Site ID before insert: ' . wp_json_encode($user['site_id']), 'users_in_domain');

			$insert_data = array(
				'access_token'     => $user['access_token'],
				'app_id'           => $user['app_id'],
				'secret_key'       => $user['secret_key'],
				'user_id'          => $user['user_id'],
				'nickname'         => $user['nickname'],
				'permalink'        => $user['permalink'] ?? 'no-data',
				'site_id'          => ! empty( $user['site_id'] ) ? (string) $user['site_id'] : 'MLA',
				'status'           => $user['status'],
				'country'          => $user['country'],
				'has_mercadoshops' => ( isset( $meli_user_data['body']->tags ) && is_array( $meli_user_data['body']->tags ) && in_array( 'mshops', $meli_user_data['body']->tags ) ) ? 1 : 0,
				'meli_user_data'   => maybe_serialize( $meli_user_data ),
				'api_token'        => $user['api_token'],
                'plan_type'            => $user['plan'] ?? 'free', // nuevo campo
	            'active_connections'   => isset($user['active_connections']) ? (int) $user['active_connections'] : 0,
	            'pending_connections'  => isset($user['pending_connections']) ? (int) $user['pending_connections'] : 0,
	            'connected_listing_ids'=> !empty($user['connected_listing_ids']) ? maybe_serialize($user['connected_listing_ids']) : null,
				'created_at'       => current_time( 'mysql' ),
				'updated_at'       => current_time( 'mysql' ),
			);

			// Helper::logData('Insert data: ' . wp_json_encode($insert_data)  , 'users_in_domain');

			$wpdb->insert(
                $table_name,
                array_map( 'strval', $insert_data ), 
                array(
                    '%s', // access_token
                    '%s', // app_id
                    '%s', // secret_key
                    '%d', // user_id
                    '%s', // nickname
                    '%s', // permalink
                    '%s', // site_id
                    '%s', // status
                    '%s', // country
                    '%d', // has_mercadoshops
                    '%s', // meli_user_data
                    '%s', // api_token
                    '%s', // plan_type
                    '%d', // active_connections
                    '%d', // pending_connections
                    '%s', // connected_listing_ids
                    '%s', // created_at
                    '%s', // updated_at
                )
            );

			// Helper::logData('SQL Query: ' . $wpdb->last_query, 'users_in_domain');

			if ( $wpdb->last_error ) {
				Helper::logData( 'Error creating user connection: ' . $wpdb->last_error, 'users_in_domain' );
			} else {
				Helper::logData( 'User connection created: ' . wp_json_encode( $insert_data, true ), 'users_in_domain' );
			}
		}

		// TO FIX
		if ( $pending_count > 0 ) {
			$message = "You have {$pending_count} connections pending vinculation to MercadoLibre in hub.";
			Helper::logData( $message, 'users_in_domain' );
			update_option( 'meliconnect_pending_connection_notifications', $message );
		} else {
			// Eliminar el mensaje si no hay conexiones pendientes
			delete_option( 'meliconnect_pending_connection_notifications' );
		}

		// Responder con el estado de la acción
		return true;
	}
}
