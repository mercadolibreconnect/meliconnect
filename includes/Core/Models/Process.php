<?php

namespace Meliconnect\Meliconnect\Core\Models;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
class Process {

	private static $table_name;

	// Este método se llama automáticamente cuando se accede por primera vez a la clase
	public static function init() {
		global $wpdb;
		self::$table_name = $wpdb->prefix . 'meliconnect_processes';
	}



	public static function cancelProcess( $process_id ) {
		global $wpdb;

		self::init();

		$table_name = self::$table_name;

		// Delete process items
		ProcessItems::deleteItems( $process_id );

        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$result = $wpdb->query( $wpdb->prepare( "DELETE FROM {$table_name} WHERE process_id = %s", $process_id ) );

		return $result;
	}

	public static function createProcess( $process_type, $items ) {
		global $wpdb;

		self::init();

		$table_name = self::$table_name;

		// Genera un ID único para el proceso
		$process_id = bin2hex( random_bytes( 16 ) );

		$data = array(
			'process_id'    => $process_id,
			'status'        => 'processing',
			'executed'      => 0,
			'total'         => count( $items ),
			'process_type'  => $process_type,
			'total_success' => 0,
			'total_fails'   => 0,
			'error_log'     => null,
			'starts_at'     => current_time( 'mysql' ),
		);

		$result = $wpdb->insert( $table_name, $data );

		// Create Process items
		$result = ProcessItems::createProcessItems( $process_id, $items );

		if ( $result === false ) {
			return new \WP_Error( 'db_insert_error', esc_html__( 'Error inserting into database.', 'meliconnect' ), $wpdb->last_error );
		}

		return $process_id;
	}


	public static function getCurrentProcessData( $process_type, $status = array( 'processing' ) ) {
		global $wpdb;

		self::init();

		$table_name = self::$table_name;

		// Convertir status a array si no es
		$status = (array) $status;

		// Crear la consulta directamente dentro del prepare()
		$placeholders = implode( ',', array_fill( 0, count( $status ), '%s' ) );
		$params       = array_merge( array( $process_type ), $status );

		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		// Ejecutar la consulta con prepare
		$result = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM $table_name WHERE process_type = %s AND status IN ($placeholders) ORDER BY created_at DESC LIMIT 1",
				...$params
			)
		);
	    // phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared

		return $result;
	}

	public static function calculateExecutionTime( $process ) {
		if ( isset( $process->starts_at ) ) {
			// Convertir `starts_at` a marca de tiempo Unix
			$starts_at = strtotime( $process->starts_at );

			// Obtener la hora actual en el formato de la base de datos y convertirla a marca de tiempo Unix
			$ends_at = strtotime( current_time( 'mysql' ) );

			// Calcular el tiempo de ejecución en segundos
			$execution_time_seconds = $ends_at - $starts_at;

			// Verificar que la diferencia no sea negativa
			if ( $execution_time_seconds < 0 ) {
				$execution_time_seconds = 0;
			}

			// Calcular horas, minutos y segundos
			$hours   = floor( $execution_time_seconds / 3600 );
			$minutes = floor( ( $execution_time_seconds % 3600 ) / 60 );
			$seconds = $execution_time_seconds % 60;

			// Formato en H:i:s
			$execution_time_formatted = sprintf( '%02d:%02d:%02d', $hours, $minutes, $seconds );

			return $execution_time_formatted;
		}

		return null;
	}

	public static function getProcessProgress( $process_id ) {
		global $wpdb;

		self::init();

		$table_name = self::$table_name;

        // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$process_data = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT executed, total, status, total_success, total_fails, starts_at 
             FROM $table_name 
             WHERE process_id = %s",
				$process_id
			)
		);
        // phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        
		if ( $process_data ) {
			$total         = intval( $process_data->total );
			$executed      = intval( $process_data->executed );
			$total_fails   = intval( $process_data->total_fails );
			$total_success = intval( $process_data->total_success );

			$executed = $total_fails + $total_success;

			$progress_value = ( $total > 0 ) ? ( $executed / $total ) * 100 : 0;

			return array(
				'progress_value' => round( $progress_value ), // porcentaje completado
				'executed'       => $executed,
				'status'         => $process_data->status,
				'total'          => $total,
				'total_success'  => $total_success,
				'total_fails'    => $total_fails,
				'execution_time' => self::calculateExecutionTime( $process_data ),
			);
		} else {
			return false;
		}
	}


	public static function updateProcessProgress( $process_id, $is_success = true ) {
		global $wpdb;

		self::init();

		$table_name = self::$table_name;
		$ends_at    = null;

		// Obtener los detalles del proceso
		$process = self::getProcessProgress( $process_id );

		if ( ! $process ) {
			return false; // Retorna false si no se encuentra el proceso
		}

		// Determinar los valores para total_success y total_fails
		$total_success = $process['total_success'];
		$total_fails   = $process['total_fails'];

		if ( $is_success ) {
			++$total_success;
		} else {
			++$total_fails;
		}

		// Verificar si se ha procesado el total de ítems
		$status = ( $total_success + $total_fails >= $process['total'] ) ? 'finished' : $process['status'];

		if ( $status === 'finished' ) {
			$ends_at = current_time( 'mysql' );
		}

		// Actualizar el proceso
		$result = $wpdb->update(
			$table_name,
			array(
				'total_success' => $total_success,
				'total_fails'   => $total_fails,
				'status'        => $status,
				'ends_at'       => $ends_at,
				'updated_at'    => current_time( 'mysql' ),
			),
			array( 'process_id' => $process_id ),
			array( '%d', '%d', '%s', '%s', '%s' ),
			array( '%s' )
		);

		return $result !== false;
	}

	public static function cancelFinishedProcesses( $process_type ) {
		global $wpdb;

		self::init();

		$table_name = self::$table_name;

		// Ejecutar la consulta para eliminar
		$result = $wpdb->delete(
			$table_name,
			array(
				'status'       => 'finished',
				'process_type' => $process_type,
			),
			array( '%s', '%s' )
		);

		return $result !== false;
	}
}
