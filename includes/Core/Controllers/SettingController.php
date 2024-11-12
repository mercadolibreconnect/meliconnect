<?php

namespace StoreSync\Meliconnect\Core\Controllers;

use StoreSync\Meliconnect\Core\Helpers\FormHelper;
use StoreSync\Meliconnect\Core\Helpers\Helper;
use StoreSync\Meliconnect\Core\Interfaces\ControllerInterface;

class SettingController implements ControllerInterface
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
        return $data;
    }

    public function loadAssets()
    {
        /* if (is_page('meliconnect-settings')) { */
        wp_enqueue_media();

        wp_enqueue_style('melicon-setting', MC_PLUGIN_URL . 'includes/Core/Assets/Css/melicon-setting.css', [], '1.0.0');

        wp_enqueue_script('melicon-setting', MC_PLUGIN_URL . 'includes/Core/Assets/Js/melicon-setting.js', ['jquery'], '1.0.0', true);
        /* } */
    }


    /* START HANDLE AJAX METHODS */
    public static function handleSettingsGetGeneralHtml()
    {
        $general_data = Helper::getMeliconnectOptions('general');

        /* echo PHP_EOL . '-------------------- general_data --------------------' . PHP_EOL;
        echo '<pre>' . var_export($general_data , true) . '</pre>';
        echo PHP_EOL . '-------------------  FINISHED  ---------------------' . PHP_EOL;
wp_die(); */
        header('Content-Type:  text/html');

        include(MC_PLUGIN_ROOT . 'includes/Core/Views/Partials/Settings/general.php');

        die();
    }

    public static function handleSettingsGetExportHtml()
    {
        $export_data = Helper::getMeliconnectOptions('export');

        header('Content-Type:  text/html');

        include(MC_PLUGIN_ROOT . 'includes/Core/Views/Partials/Settings/export.php');

        die();
    }

    public static function handleSettingsGetImportHtml()
    {
        $import_data = Helper::getMeliconnectOptions('import');

        header('Content-Type:  text/html');

        include(MC_PLUGIN_ROOT . 'includes/Core/Views/Partials/Settings/import.php');

        die();
    }

    public static function handleSettingsGetSyncHtml()
    {
        $sync_data = Helper::getMeliconnectOptions('sync');

        header('Content-Type:  text/html');

        include(MC_PLUGIN_ROOT . 'includes/Core/Views/Partials/Settings/sync.php');

        die();
    }

    public static function handleSaveGeneralSettings()
    {
        // Verifica los permisos del usuario
        if (!current_user_can('meliconnect_manage_plugin')) {
            wp_send_json_error(__('You do not have permission to perform this action', 'meliconnect'));
            return;
        }

        // Obtiene los datos enviados por AJAX
        $data = $_POST;

        // Verifica si la solicitud está vacía
        if (empty($data)) {
            wp_send_json_error(__('Invalid request data', 'meliconnect'));
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
        wp_send_json_success('__(Settings saved successfully , meliconnect)');
    }

    public static function handleSaveOthersSettings()
    {
        // Verifica los permisos del usuario
        if (!current_user_can('meliconnect_manage_plugin')) {
            wp_send_json_error(__('You do not have permission to perform this action', 'meliconnect'));
            return;
        }

        // Obtiene los datos enviados por AJAX
        $form_data = $_POST;

        // Verifica si la solicitud está vacía
        if (empty($form_data)) {
            wp_send_json_error(__('Invalid request data', 'meliconnect'));
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

        wp_send_json_success(__('Settings saved successfully', 'meliconnect'));
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
                'always' => __('Always', 'meliconnect'),
                'on_update' => __('On Update', 'meliconnect'),
                'on_create' => __('On Create', 'meliconnect')
            ];
        }

        echo '<div class="columns">
            <div class="column is-4">
                <label for="' . esc_attr($key) . '" class="label">' . esc_html($label) . '</label>
            </div>
            <div class="column is-8">
                <div class="melicon-select select is-fullwidth">
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
