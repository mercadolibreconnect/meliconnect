<?php

namespace StoreSync\Meliconnect\Core\Helpers;


class FormHelper
{
    public static function print_checkbox($key, $label, $value = '', $check_compare_value = true, $helpText = '')
    {
        // Iniciar captura de salida para `checked()`
        ob_start();
        checked($value, $check_compare_value);
        $checked_attribute = ob_get_clean(); // Finaliza captura y limpia el búfer

        $html = '<div class="field">
                    <input id="' . $key . '" type="checkbox" name="' . $key . '" class="switch is-rounded is-info" ' . $checked_attribute . ' value="' . $check_compare_value . '" >
                    <label for="' . $key . '" class="label">' . $label . '</label>
                </div>';


        if ($helpText !== '') {
            $html .= '<div class="columns">
                        <div class="column is-1">
                         </div>
                        <div class="column is-11 pl-5">
                            <p class="help" style="min-height: 120px">' . $helpText . '</p>
                        </div>
                    </div>';
        }

        echo $html;
    }
}