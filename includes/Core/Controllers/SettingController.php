<?php

namespace Meliconnect\Meliconnect\Core\Controllers;

use Meliconnect\Meliconnect\Core\Helpers\FormHelper;
use Meliconnect\Meliconnect\Core\Helpers\Helper;
use Meliconnect\Meliconnect\Core\Interfaces\ControllerInterface;

class SettingController implements ControllerInterface
{
    public function __construct()
    {
        wp_enqueue_media();
    }

    public function getData()
    {
        // Logic to get and return data
        $data = [];
        return $data;
    }



    /* START HANDLE AJAX METHODS */
    public static function handleSettingsGetGeneralHtml()
    {


        if ( ! isset( $_GET['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['nonce'] ) ), 'ajax_settings_nonce'  ) ) {
            wp_send_json_error( array( 'message' => 'Nonce verification failed.' ) );
            wp_die();
        }

        $general_data = Helper::getMeliconnectOptions('general');

        header('Content-Type:  text/html');

        include(MC_PLUGIN_ROOT . 'includes/Core/Views/Partials/Settings/general.php');

        die();
    }

    public static function handleSettingsGetExportHtml()
    {
        if ( ! isset( $_GET['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['nonce'] ) ), 'ajax_settings_nonce'  ) ) {
            wp_send_json_error( array( 'message' => 'Nonce verification failed.' ) );
            wp_die();
        }

        $export_data = Helper::getMeliconnectOptions('export');

        header('Content-Type:  text/html');

        include(MC_PLUGIN_ROOT . 'includes/Core/Views/Partials/Settings/export.php');

        die();
    }

    public static function handleSettingsGetImportHtml()
    {
        if ( ! isset( $_GET['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['nonce'] ) ), 'ajax_settings_nonce'  ) ) {
            wp_send_json_error( array( 'message' => 'Nonce verification failed.' ) );
            wp_die();
        }

        $import_data = Helper::getMeliconnectOptions('import');

        header('Content-Type:  text/html');

        include(MC_PLUGIN_ROOT . 'includes/Core/Views/Partials/Settings/import.php');

        die();
    }

    public static function handleSettingsGetSyncHtml()
    {
        if ( ! isset( $_GET['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['nonce'] ) ), 'ajax_settings_nonce'  ) ) {
            wp_send_json_error( array( 'message' => 'Nonce verification failed.' ) );
            wp_die();
        }

        $sync_data = Helper::getMeliconnectOptions('sync');

        header('Content-Type:  text/html');

        include(MC_PLUGIN_ROOT . 'includes/Core/Views/Partials/Settings/sync.php');

        die();
    }

    public static function handleSaveGeneralSettings()
    {
        if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'ajax_settings_nonce'  ) ) {
            wp_send_json_error( array( 'message' => 'Nonce verification failed.' ) );
            wp_die();
        }

        // Verifica los permisos del usuario
        if (!current_user_can('meliconnect_manage_plugin')) {
            wp_send_json_error(esc_html__('You do not have permission to perform this action', 'meliconnect'));
            return;
        }

        // Obtiene los datos enviados por AJAX
        $data = $_POST;

        // Verifica si la solicitud está vacía
        if (empty($data)) {
            wp_send_json_error(esc_html__('Invalid request data', 'meliconnect'));
            return;
        }

        // Converts to an array and filter empty values
        $general_image_attachment_ids = array_map('intval', array_filter((array) $data['melicon_general_image_attachment_ids']));
        $general_description_template = sanitize_text_field($data['melicon_general_description_template']);
        $general_sync_type = sanitize_text_field($data['melicon_general_sync_type']);
        $general_sync_items_batch = intval($data['melicon_general_sync_items_batch']);
        $general_sync_frecuency_minutes = intval($data['melicon_general_sync_frecuency_minutes']);
        $general_sync_method = sanitize_text_field($data['melicon_general_sync_method']);

        // Guarda los datos en las opciones de WooCommerce
        update_option('melicon_general_image_attachment_ids', $general_image_attachment_ids);
        update_option('melicon_general_description_template', $general_description_template);
        update_option('melicon_general_sync_type', $general_sync_type);
        update_option('melicon_general_sync_items_batch', $general_sync_items_batch);
        update_option('melicon_general_sync_frecuency_minutes', $general_sync_frecuency_minutes);
        update_option('melicon_general_sync_method', $general_sync_method);

        // Envía una respuesta de éxito
        wp_send_json_success('esc_html__(Settings saved successfully , meliconnect)');
    }

    public static function handleSaveOthersSettings()
    {
        if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'ajax_settings_nonce'  ) ) {
            wp_send_json_error( array( 'message' => 'Nonce verification failed.' ) );
            wp_die();
        }
        
        // Verifica los permisos del usuario
        if (!current_user_can('meliconnect_manage_plugin')) {
            wp_send_json_error(esc_html__('You do not have permission to perform this action', 'meliconnect'));
            return;
        }

        // Obtiene los datos enviados por AJAX
        $form_data = $_POST;

        // Verifica si la solicitud está vacía
        if (empty($form_data)) {
            wp_send_json_error(esc_html__('Invalid request data', 'meliconnect'));
            return;
        }

        $checkbox_fields = isset($form_data['checkbox_fields']) ? explode(',', $form_data['checkbox_fields']) : [];



        // Process and saves every option excepts checkboxes 
        foreach ($form_data as $key => $value) {
            if ($key !== 'checkbox_fields' && !in_array($key, $checkbox_fields)) {
                update_option($key, sanitize_text_field($value));
            }
        }

        // Process and saves checkboxes
        foreach ($checkbox_fields as $checkbox) {
            if (!isset($form_data[trim($checkbox)])) {
                update_option($checkbox, 'false');
            } else {
                update_option($checkbox, 'true');
            }
        }

        wp_send_json_success(esc_html__('Settings saved successfully', 'meliconnect'));
    }

    /* START CUSTOM METHODS */
    public static function print_setting_checkbox($key, $label, $value = '', $check_compare_value = true, $helpText = '')
    {
        FormHelper::print_checkbox($key, $label, $value, $check_compare_value, $helpText);
    }

    public static function print_setting_select($key, $label, $selected_value_key = '', $options = null)
    {
        // Definir las opciones por defecto traducibles
        if ($options === null) {
            $options = [
                'always' => esc_html__('Always', 'meliconnect'),
                'on_update' => esc_html__('On Update', 'meliconnect'),
                'on_create' => esc_html__('On Create', 'meliconnect')
            ];
        }

        echo '<div class="melicon-columns">
            <div class="melicon-column melicon-$&">
                <label for="' . esc_attr($key) . '" class="melicon-label">' . esc_html($label) . '</label>
            </div>
            <div class="melicon-column melicon-is-8">
                <div class="melicon-select melicon-is-fullwidth">
                    <select name="' . esc_attr($key) . '" id="' . esc_attr($key) . '">';

        foreach ($options as $option_key => $option_value) {
            echo '<option value="' . esc_attr($option_key) . '" ' . selected($selected_value_key, $option_key, false) . '>' . esc_html($option_value) . '</option>';
        }

        echo        '</select>
                </div>
            </div>
        </div>';
    }
}
