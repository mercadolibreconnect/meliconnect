<?php

namespace Meliconnect\Meliconnect\Core\Helpers;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
class FormHelper
{
    public static function print_checkbox($key, $label, $value = '', $check_compare_value = true, $helpText = '')
    {
        // Ruta del archivo parcial
        $partial_path = MELICONNECT_PLUGIN_ROOT . 'includes/Core/Views/Partials/Form/checkbox.php';

        // Verificar si el archivo existe
        if (file_exists($partial_path)) {
            // Extraer variables para usarlas en el scope del archivo
            $key = esc_attr($key);
            $label = esc_html($label);
            $value = esc_attr($value);
            $check_compare_value = esc_attr($check_compare_value);
            $helpText = esc_html($helpText);

            include $partial_path;
        } else {
            echo '<p>Error: Checkbox partial not found.</p>';
        }
    }
}
