<style>
    .image-uploader {
        position: relative;
    }

    .image-uploader .file-cta {
        min-height: 180px !important;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        overflow: hidden;
    }

    .image-uploader .image-preview {
        border: 1px solid #dbdbdb !important;
    }

    .delete-image {
        position: absolute;
        top: 0;
        right: 0;
        z-index: 10;
    }

    .delete-image:hover {
        cursor: pointer;
    }
</style>
<form id="melicon-general-settings-form">
    <section class="section">
        <div class="container">
            <div class="columns">
                <div class="column">
                    <label class="label"><?php _e('Additional Images', 'meliconnect'); ?></label>
                    <p><?php _e('Here you configure the general parameters that will apply to all publications, such as photos and description template.', 'meliconnect'); ?></p>
                </div>
            </div>
            <div id="image-uploaders" class="columns">

                <?php
                $extra_images = $general_data['melicon_general_image_attachment_ids'];
                for ($i = 0; $i <= 4; $i++) :
                ?>
                    <div class="column is-one-fifth">
                        <div class="image-uploader">
                            <button type="button" class="delete-image" style="<?php echo isset($extra_images[$i]) ? '' : 'display: none;'; ?>"><i class="fas fa-times"></i></button>
                            <div class="file has-name is-boxed">
                                <label class="file-label">
                                    <input class="file-input upload_image_button" type="button" name="resume">
                                    <span class="file-cta" style="<?php echo isset($extra_images[$i]) ? 'display: none;' : ''; ?>">
                                        <span class="file-icon">
                                            <i class="fas fa-upload"></i>
                                        </span>
                                        <span class="file-label">
                                            <?php _e('Upload Image…', 'meliconnect'); ?>
                                        </span>
                                    </span>
                                    <!-- Image element where the selected image will be displayed -->
                                    <img src="<?php echo isset($extra_images[$i]) ? wp_get_attachment_url($extra_images[$i]) : ''; ?>" class="image-preview" style="<?php echo isset($extra_images[$i]) ? '' : 'display: none;'; ?>">
                                    <span class="file-name image-name">
                                        <?php echo isset($extra_images[$i]) ? get_the_title($extra_images[$i]) : __('No file selected', 'meliconnect'); ?>
                                    </span>
                                </label>
                            </div>
                            <input type="hidden" name="melicon_general_image_attachment_ids[<?php echo $i; ?>]" class="image_attachment_id" value="<?php echo isset($extra_images[$i]) ? $extra_images[$i] : ''; ?>">

                        </div>
                    </div>
                <?php endfor; ?>

            </div>
            <div class="columns">
                <div class="column is-12">
                    <div class="field">
                        <label class="label"><?php _e('Description Template', 'meliconnect'); ?></label>

                        <div class="control">
                            <textarea class="textarea" name="melicon_general_description_template" id="melicon_general_description_template" placeholder="<?php _e('Description Template', 'meliconnect'); ?>"><?php echo isset($general_data['melicon_general_description_template']) ? esc_textarea($general_data['melicon_general_description_template']) : ''; ?></textarea>
                        </div>
                        <div class="buttons tags mt-4">
                            <a class="button is-small insert-tag" data-tag="[title]"><?php _e('Title', 'meliconnect'); ?></a>
                            <a class="button is-small insert-tag" data-tag="[description]"><?php _e('Product Description', 'meliconnect'); ?></a>
                            <a class="button is-small insert-tag" data-tag="[excerpt]"><?php _e('Short Description', 'meliconnect'); ?></a>
                            <a class="button is-small insert-tag" data-tag="[variations]"><?php _e('Variations', 'meliconnect'); ?></a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <hr>
    <section class="section">
        <div class="container">
            <div class="columns is-mobile is-multiline">
                <div class="column">
                    <h2 class="title is-6"><?php _e('Automatic Import or Export', 'meliconnect'); ?></h2>
                    <p><?php _e('This will be used to execute background processes for either export or import.', 'meliconnect'); ?></p>
                </div>
            </div>

            <div class="columns is-mobile is-multiline">
                <div class="column is-4">
                    <div class="melicon-field field">
                        <label class="label" for="melicon_general_sync_type"><?php _e('Apply on', 'meliconnect'); ?></label>
                        <div class="melicon-control control">
                            <div class="melicon-select select is-fullwidth">
                                <select name="melicon_general_sync_type" id="melicon_general_sync_type">
                                    <option value="deactive" <?php selected($general_data['melicon_general_sync_type'], 'deactive'); ?>><?php _e('Deactivate', 'meliconnect'); ?></option>
                                    <option value="import" <?php selected($general_data['melicon_general_sync_type'], 'import'); ?>><?php _e('Import (From Meli to Woo)', 'meliconnect'); ?></option>
                                    <option value="export" <?php selected($general_data['melicon_general_sync_type'], 'export'); ?>><?php _e('Export (From Woo to Meli)', 'meliconnect'); ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="column is-8">
                    <div class="columns is-mobile">
                        <div class="column is-5">
                            <div class="melicon-field field">
                                <label class="label" for="melicon_general_sync_items_batch"><?php _e('Items per batch', 'meliconnect'); ?></label>
                                <div class="melicon-control control">
                                    <input class="input" type="number" min="1" name="melicon_general_sync_items_batch" id="melicon_general_sync_items_batch" value="<?php echo isset($general_data['melicon_general_sync_items_batch']) ? esc_attr($general_data['melicon_general_sync_items_batch']) : ''; ?>" min="1" max="1000">
                                </div>
                            </div>
                        </div>
                        <div class="column is-4">
                            <div class="melicon-field field">
                                <label class="label" for="melicon_general_sync_frecuency_minutes"><?php _e('Frequency (minutes)', 'meliconnect'); ?></label>
                                <div class="melicon-control control">
                                    <input class="input" type="number" min="1" name="melicon_general_sync_frecuency_minutes" id="melicon_general_sync_frecuency_minutes" value="<?php echo isset($general_data['melicon_general_sync_frecuency_minutes']) ? esc_attr($general_data['melicon_general_sync_frecuency_minutes']) : ''; ?>">
                                </div>
                            </div>
                        </div>
                        <div class="column is-3">
                            <div class="melicon-field field">
                                <label class="label" for="melicon_general_sync_method"><?php _e('Method', 'meliconnect'); ?></label>
                                <div class="melicon-control control">
                                    <div class="melicon-select select is-fullwidth">
                                        <select name="melicon_general_sync_method" id="melicon_general_sync_method">
                                            <option value="wordpress" <?php selected($general_data['melicon_general_sync_method'], 'wordpress'); ?>><?php _e('WordPress', 'meliconnect'); ?></option>
                                            <option value="custom" <?php selected($general_data['melicon_general_sync_method'], 'custom'); ?>><?php _e('Custom', 'meliconnect'); ?></option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="columns mt-4">
                <div class="column is-9">
                    <div class="content">
                        <?php
                        $scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';

                        // Obtener el nombre del host
                        $host = $_SERVER['HTTP_HOST'];

                        // Construir la URL base
                        $sync_url = $scheme . '://' . $host;
                        ?>
                        <strong><?php _e('External automatic synchronization URL (custom):', 'meliconnect'); ?></strong> <code><?php echo $sync_url; ?>/wp-json/meliconnect/v1/cronexternal/export-import</code>
                    </div>
                </div>
                <div class="column is-3">
                    <div class="melicon-field field is-grouped is-grouped-right">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <hr>

    <div class="container">
        <div class="columns">
            <div class="column">
                <div class="level">
                    <div class="level-left">
                    </div>
                    <div class="level-right">

                        <button type="submit" class="button button-meliconnect is-primary"><?php _e('Save General Settings', 'meliconnect'); ?></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</form>

<script>
    jQuery(document).ready(function($) {
        $('.upload_image_button').click(function(e) {
            e.preventDefault();
            var button = $(this); // Botón que se ha pulsado
            var custom_uploader = wp.media({
                title: '<?php _e('Select or Upload Media', 'meliconnect'); ?>',
                library: {
                    type: 'image'
                },
                button: {
                    text: '<?php _e('Select or Upload Media', 'meliconnect'); ?>'
                },
                multiple: false // Permitir la selección de una sola imagen
            }).on('select', function() {
                var attachment = custom_uploader.state().get('selection').first().toJSON();
                button.closest('.image-uploader').find('.image_attachment_id').val(attachment.id); // Actualiza el input oculto con el ID de la imagen
                button.closest('.image-uploader').find('.image-name').text(attachment.name); // Muestra el nombre de la imagen seleccionada

                button.closest('.image-uploader').find('.file-cta').hide();
                button.closest('.image-uploader').find('.delete-image').show();
                // Actualiza el src del elemento img con la URL de la imagen seleccionada y muestra la imagen
                button.closest('.image-uploader').find('.image-preview').attr('src', attachment.url).show();
            }).open();
        });
        $('.insert-tag').click(function() {
            var tag = $(this).data('tag');
            var textarea = $('textarea[name="product_description"]');
            var cursorPos = textarea.prop('selectionStart');
            var v = textarea.val();
            var textBefore = v.substring(0, cursorPos);
            var textAfter = v.substring(cursorPos, v.length);

            textarea.val(textBefore + tag + textAfter);
        });

        // Manejador del evento click para el botón de eliminar
        $('.image-uploader').on('click', '.delete-image', function() {
            var uploader = $(this).closest('.image-uploader');

            // Restablece el preview y oculta la imagen y el botón de eliminar
            uploader.find('.image-preview').attr('src', '').hide();
            $(this).hide();

            // Opcional: restablecer el input file o cualquier otro estado necesario
            uploader.find('.image_attachment_id').val('');
            uploader.find('.image-name').text("<?php echo __('No image selected', 'meliconnect') ?>");

            // Muestra de nuevo el span .file-cta si se desea
            uploader.find('.file-cta').show();
        });

        $('#melicon-general-settings-form').submit(function(e) {
            e.preventDefault();
            var formData = new FormData(this); // Recolecta los datos del formulario

            formData.append("action", "melicon_save_general_settings");

            $.ajax({
                type: "POST",
                url: ajaxurl,
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    $('#save-import-button').addClass('is-loading');
                },
                success: function(response) {
                    location.reload();
                },
                error: function(xhr, status, error) {
                    $('#save-import-button').removeClass('is-loading');
                    console.log(xhr.responseText);
                }
            });


        });
    });
</script>