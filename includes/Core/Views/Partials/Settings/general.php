<form id="melicon-general-settings-form">
    <section class="melicon-section">
        <div class="melicon-container">
            <div class="melicon-columns">
                <div class="melicon-column">
                    <label class="melicon-label"><?php esc_html_e('Additional Images', 'meliconnect'); ?></label>
                    <p><?php esc_html_e('Here you configure the general parameters that will apply to all publications, such as photos and description template.', 'meliconnect'); ?></p>
                </div>
            </div>
            <div id="melicon-image-uploaders" class="melicon-columns">

                <?php
                $extra_images = $general_data['melicon_general_image_attachment_ids'];
                for ($i = 0; $i <= 4; $i++) :
                ?>
                    <div class="melicon-column melicon-is-one-fifth">
                        <div class="melicon-image-uploader">
                            <button type="button" class="delete-image" style="<?php echo isset($extra_images[$i]) ? '' : 'display: none;'; ?>"><i class="fas fa-times"></i></button>
                            <div class="melicon-file melicon-has-name melicon-is-boxed">
                                <label class="melicon-file-label">
                                    <input class="melicon-file-input upload_image_button" type="button" name="resume">
                                    <span class="melicon-file-cta" style="<?php echo isset($extra_images[$i]) ? 'display: none;' : ''; ?>">
                                        <span class="melicon-file-icon">
                                            <i class="fas fa-upload"></i>
                                        </span>
                                        <span class="melicon-file-label">
                                            <?php esc_html_e('Upload Image…', 'meliconnect'); ?>
                                        </span>
                                    </span>
                                    <!-- Image element where the selected image will be displayed -->
                                    <img src="<?php echo isset($extra_images[$i]) ? esc_url(wp_get_attachment_url($extra_images[$i])) : ''; ?>" class="melicon-image-preview" style="<?php echo isset($extra_images[$i]) ? '' : 'display: none;'; ?>">
                                    <span class="melicon-file-name image-name">
                                        <?php echo isset($extra_images[$i]) ? esc_html(get_the_title($extra_images[$i])) : esc_html__('No file selected', 'meliconnect'); ?>
                                    </span>

                                </label>
                            </div>
                            <input type="hidden" name="melicon_general_image_attachment_ids[<?php echo esc_attr($i); ?>]" class="image_attachment_id" value="<?php echo esc_attr(isset($extra_images[$i]) ? $extra_images[$i] : ''); ?>">

                        </div>
                    </div>
                <?php endfor; ?>

            </div>
            <div class="melicon-columns">
                <div class="melicon-column melicon-is-12">
                    <div class="melicon-field">
                        <label class="melicon-label"><?php esc_html_e('Description Template', 'meliconnect'); ?></label>

                        <div class="melicon-control">
                            <textarea class="melicon-textarea" name="melicon_general_description_template" id="melicon_general_description_template" placeholder="<?php esc_html_e('Description Template', 'meliconnect'); ?>"><?php echo isset($general_data['melicon_general_description_template']) ? esc_textarea($general_data['melicon_general_description_template']) : ''; ?></textarea>
                        </div>
                        <div class="melicon-buttons melicon-tags melicon-mt-4">
                            <a class="melicon-button melicon-is-small insert-tag" data-tag="[title]"><?php esc_html_e('Title', 'meliconnect'); ?></a>
                            <a class="melicon-button melicon-is-small insert-tag" data-tag="[description]"><?php esc_html_e('Product Description', 'meliconnect'); ?></a>
                            <a class="melicon-button melicon-is-small insert-tag" data-tag="[excerpt]"><?php esc_html_e('Short Description', 'meliconnect'); ?></a>
                            <a class="melicon-button melicon-is-small insert-tag" data-tag="[variations]"><?php esc_html_e('Variations', 'meliconnect'); ?></a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <hr>
    <section class="melicon-section">
        <div class="melicon-container">
            <div class="melicon-columns melicon-is-mobile melicon-is-multiline">
                <div class="melicon-column">
                    <h2 class="melicon-title melicon-is-6"><?php esc_html_e('Automatic Import or Export', 'meliconnect'); ?></h2>
                    <p><?php esc_html_e('This will be used to execute background processes for either export or import.', 'meliconnect'); ?></p>
                </div>
            </div>

            <div class="melicon-columns melicon-is-mobile melicon-is-multiline">
                <div class="melicon-column melicon-is-4">
                    <div class="melicon-field">
                        <label class="melicon-label" for="melicon_general_sync_type"><?php esc_html_e('Apply on', 'meliconnect'); ?></label>
                        <div class="melicon-control">
                            <div class="melicon-select melicon-is-fullwidth">
                                <select name="melicon_general_sync_type" id="melicon_general_sync_type">
                                    <option value="deactive" <?php selected($general_data['melicon_general_sync_type'], 'deactive'); ?>><?php esc_html_e('Deactivate', 'meliconnect'); ?></option>
                                    <option value="import" <?php selected($general_data['melicon_general_sync_type'], 'import'); ?>><?php esc_html_e('Import (From Meli to Woo)', 'meliconnect'); ?></option>
                                    <option value="export" <?php selected($general_data['melicon_general_sync_type'], 'export'); ?>><?php esc_html_e('Export (From Woo to Meli)', 'meliconnect'); ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="melicon-column melicon-is-8">
                    <div class="melicon-columns melicon-melicon-is-mobile">
                        <div class="melicon-column melicon-is-5">
                            <div class="melicon-field">
                                <label class="melicon-label" for="melicon_general_sync_items_batch"><?php esc_html_e('Items per batch', 'meliconnect'); ?></label>
                                <div class="melicon-control">
                                    <input class="melicon-input" type="number" min="1" name="melicon_general_sync_items_batch" id="melicon_general_sync_items_batch" value="<?php echo isset($general_data['melicon_general_sync_items_batch']) ? esc_attr($general_data['melicon_general_sync_items_batch']) : ''; ?>" min="1" max="1000">
                                </div>
                            </div>
                        </div>
                        <div class="melicon-column melicon-is-4">
                            <div class="melicon-field">
                                <label class="melicon-label" for="melicon_general_sync_frecuency_minutes"><?php esc_html_e('Frequency (minutes)', 'meliconnect'); ?></label>
                                <div class="melicon-control">
                                    <input class="melicon-input" type="number" min="1" name="melicon_general_sync_frecuency_minutes" id="melicon_general_sync_frecuency_minutes" value="<?php echo isset($general_data['melicon_general_sync_frecuency_minutes']) ? esc_attr($general_data['melicon_general_sync_frecuency_minutes']) : ''; ?>">
                                </div>
                            </div>
                        </div>
                        <div class="melicon-column melicon-is-3">
                            <div class="melicon-field">
                                <label class="melicon-label" for="melicon_general_sync_method"><?php esc_html_e('Method', 'meliconnect'); ?></label>
                                <div class="melicon-control">
                                    <div class="melicon-select melicon-is-fullwidth">
                                        <select name="melicon_general_sync_method" id="melicon_general_sync_method">
                                            <option value="wordpress" <?php selected($general_data['melicon_general_sync_method'], 'wordpress'); ?>><?php esc_html_e('WordPress', 'meliconnect'); ?></option>
                                            <option value="custom" <?php selected($general_data['melicon_general_sync_method'], 'custom'); ?>><?php esc_html_e('Custom', 'meliconnect'); ?></option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="melicon-columns melicon-mt-4">
                <div class="melicon-column melicon-is-9">
                    <div class="melicon-content">
                        <?php
                        // Detectar HTTPS correctamente
                        $https  = isset($_SERVER['HTTPS']) ? sanitize_text_field(wp_unslash($_SERVER['HTTPS'])) : '';
                        $scheme = (! empty($https) && strtolower($https) !== 'off') ? 'https' : 'http';

                        // Obtener el nombre del host (sanitizado)
                        $host = isset($_SERVER['HTTP_HOST']) ? sanitize_text_field(wp_unslash($_SERVER['HTTP_HOST'])) : '';

                        // Construir la URL base
                        $sync_url = $scheme . '://' . $host;
                        ?>
                        <strong><?php esc_html_e('External automatic synchronization URL (custom):', 'meliconnect'); ?></strong>
                        <code><?php echo esc_url($sync_url); ?>/wp-json/meliconnect/v1/cronexternal/export-import</code>
                    </div>
                </div>
                <div class="melicon-column melicon-is-3">
                    <div class="melicon-field melicon-is-grouped melicon-is-grouped-right">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <hr>

    <div class="melicon-container">
        <div class="melicon-columns">
            <div class="melicon-column">
                <div class="melicon-level">
                    <div class="melicon-level-left">
                    </div>
                    <div class="melicon-level-right">
                        <button type="submit" class="melicon-button  melicon-is-primary"><?php esc_html_e('Save General Settings', 'meliconnect'); ?></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</form>