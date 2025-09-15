<?php

namespace Meliconnect\Meliconnect\Core\Helpers;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class HubApi {

	private static $_apiUrl = 'https://www.meliconnect.com/apicore/';

	public static function connectHubApi( $urlPath, $args, $method = 'GET', $timeOut = 30 ) {
		$url = self::$_apiUrl . $urlPath;

		$wp_args = array(
			'method'  => $method,
			'timeout' => $timeOut,
		);

		if ( strtoupper( $method ) === 'POST' || strtoupper( $method ) === 'PUT' ) {
			$wp_args['body']    = wp_json_encode( $args );
			$wp_args['headers'] = array(
				'Content-Type' => 'application/json',
			);
		} else {
			$wp_args['body'] = $args;
		}

		// Make the call and store the response in $res
		$res = wp_remote_request( $url, $wp_args );

		if ( is_wp_error( $res ) ) {
			return array(
				'success'    => false,
				'response'   => $res->get_error_messages(),
				'errorCodes' => $res->get_error_codes(),
				'errorData'  => $res->get_error_data(),
			);
		}

		$responseCode = wp_remote_retrieve_response_code( $res );
		$responseBody = json_decode( wp_remote_retrieve_body( $res ) );

		if ( ( $responseCode == 200 || $responseCode == 201 ) && ! isset( $responseBody->error ) ) {
			update_option( 'meliconnect_api_hub_errors', array() );
			return array(
				'success'  => true,
				'response' => $responseBody,
			);
		} else {
			$errorBody = $responseBody->error ?? 'Unknown error';
			update_option( 'meliconnect_api_hub_errors', array( $errorBody ) );
			return array(
				'success'    => false,
				'response'   => $responseBody,
				'errorCodes' => array( 'errorUserId' ),
			);
		}
	}
}
