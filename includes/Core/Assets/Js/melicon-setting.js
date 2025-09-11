jQuery(document).ready(function ($) {
    // Function to change the active content and adjust the active state of the tabs
    function setActiveTab(tabId) {
        var $tabs = $('#melicon-settings-tabs li');
        var $targetTab = $tabs.filter('[data-tab="' + tabId + '"]');
        var selectAction = 'melicon_settings_get_general_html';

        // Define the content based on the tab ID
        switch (tabId) {
            case "general":
                selectAction = 'melicon_settings_get_general_html';
                break;
            case "export":
                selectAction = 'melicon_settings_get_export_html';
                break;
            case "import":
                selectAction = 'melicon_settings_get_import_html';
                break;
            case "synchronizer":
                selectAction = 'melicon_settings_get_sync_html';
                break;
            default:
                console.error("Unknown Tab ID: ", tabId);
                break;
        }

        $('#setting-loader').show();

        // Perform the AJAX request
        $.ajax({
            type: "GET",
            url: mcTranslations.admin_ajax_url,
            data: {
                "action": selectAction,
                "nonce": mcTranslations.ajax_settings_nonce
            },
            success: function (response) {
                // Insert the obtained HTML content into the tab container
                $('#tab-content').html(response);

                $tabs.removeClass('melicon-is-active');
                $targetTab.addClass('melicon-is-active');

                switch (tabId) {
                    case 'export':
                        initExportSettings();
                        break;
                    case 'import':
                        initImportSettings();
                        break;
                    case 'synchronizer':
                        initSyncSettings();
                        break;
                    default:
                        initGeneralSettings();
                        break;
                }


            },
            error: function (xhr, status, error) {
                // Handle errors (optional)
                console.error("Error loading tab content: ", error);
                $('#tab-content').html('<p>Error loading content.</p>');
            },
            complete: function () {
                // Hide the loader once the request is complete
                $('#setting-loader').hide();
            }
        });
    }

    // Click handler for the tabs
    $('#melicon-settings-tabs li').click(function () {
        var tabId = $(this).data('tab');
        window.location.hash = tabId; // Update the hash in the URL
        setActiveTab(tabId);
    });


    // Check if there is a hash in the URL when the page loads and show the corresponding tab
    var currentTab = window.location.hash.replace('#', '') || 'general'; // 'general' as fallback
    if ($('#melicon-settings-tabs li[data-tab="' + currentTab + '"]').length) {
        setActiveTab(currentTab);
    } else {
        setActiveTab('general'); // Fallback to 'general' if the hash does not match any tab
    }




});

function initGeneralSettings() {
    jQuery(document).ready(function ($) {
        $('.upload_image_button').click(function (e) {
            e.preventDefault();
            var button = $(this); // Botón que se ha pulsado
            var custom_uploader = wp.media({
                title: mcTranslations.select_or_upload_media,
                library: {
                    type: 'image'
                },
                button: {
                    text: mcTranslations.select_or_upload_media
                },
                multiple: false // Permitir la selección de una sola imagen
            }).on('select', function () {
                var attachment = custom_uploader.state().get('selection').first().toJSON();
                button.closest('.melicon-image-uploader').find('.image_attachment_id').val(attachment.id); // Actualiza el input oculto con el ID de la imagen
                button.closest('.melicon-image-uploader').find('.image-name').text(attachment.name); // Muestra el nombre de la imagen seleccionada

                button.closest('.melicon-image-uploader').find('.melicon-file-cta').hide();
                button.closest('.melicon-image-uploader').find('.delete-image').show();
                // Actualiza el src del elemento img con la URL de la imagen seleccionada y muestra la imagen
                button.closest('.melicon-image-uploader').find('.melicon-image-preview').attr('src', attachment.url).show();
            }).open();
        });
        $('.insert-tag').click(function () {
            var tag = $(this).data('tag');
            var textarea = $('textarea[name="melicon_general_description_template"]');
            var cursorPos = textarea.prop('selectionStart');
            var v = textarea.val();
            var textBefore = v.substring(0, cursorPos);
            var textAfter = v.substring(cursorPos, v.length);

            textarea.val(textBefore + tag + textAfter);
        });

        // Manejador del evento click para el botón de eliminar
        $('.melicon-image-uploader').on('click', '.delete-image', function () {
            var uploader = $(this).closest('.melicon-image-uploader');

            // Restablece el preview y oculta la imagen y el botón de eliminar
            uploader.find('.melicon-image-preview').attr('src', '').hide();
            $(this).hide();

            // Opcional: restablecer el input file o cualquier otro estado necesario
            uploader.find('.image_attachment_id').val('');
            uploader.find('.image-name').text(mcTranslations.no_image_selected);

            // Muestra de nuevo el span .file-cta si se desea
            uploader.find('.melicon-file-cta').show();
        });

        $('#melicon-general-settings-form').submit(function (e) {
            e.preventDefault();
            var formData = new FormData(this); // Recolecta los datos del formulario

            formData.append("action", "melicon_save_general_settings");
            formData.append("nonce", mcTranslations.ajax_settings_nonce);

            $.ajax({
                type: "POST",
                url: mcTranslations.admin_ajax_url,
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function () {
                    $('#save-import-button').addClass('is-loading');
                },
                success: function (response) {
                    location.reload();
                },
                error: function (xhr, status, error) {
                    $('#save-import-button').removeClass('is-loading');
                    console.log(xhr.responseText);
                }
            });


        });
    });
}
function initExportSettings() {
    function toggleExportSettings(isDisabled) {
        if (isDisabled) {
            jQuery('#melicon-export-settings-melicon-columns input, #melicon-export-settings-melicon-columns select, #melicon-export-settings-melicon-columns textarea').prop('disabled', true);
        } else {
            jQuery('#melicon-export-settings-melicon-columns input, #melicon-export-settings-melicon-columns select, #melicon-export-settings-melicon-columns textarea').prop('disabled', false);
        }
    }

    toggleExportSettings(jQuery('#melicon_export_is_disabled').prop('checked'));

    jQuery('#melicon-export-settings-form').off('submit').on('submit', function (e) {
        e.preventDefault();
        var formData = new FormData(this);

        formData.append("action", "melicon_save_others_settings");
        formData.append("nonce", mcTranslations.ajax_settings_nonce);

        jQuery.ajax({
            type: "POST",
            url: mcTranslations.admin_ajax_url,
            data: formData,
            contentType: false,
            processData: false,
            beforeSend: function () {
                jQuery('#save-export-button').addClass('is-loading');
            },
            success: function (response) {
                location.reload();
            },
            error: function (xhr, status, error) {
                jQuery('#save-export-button').removeClass('is-loading');
                console.log(xhr.responseText);
            }
        });
    });

    jQuery('#melicon_export_is_disabled').off('change').on('change', function (e) {
        var checkbox = jQuery(this);
        e.preventDefault();

        if (!checkbox.prop('checked')) {
            toggleExportSettings(false);
        } else {
            MeliconSwal.fire({
                icon: 'warning',
                title: mcTranslations.alert_title_disable_export,
                text: mcTranslations.alert_body_disable_export,
                showCancelButton: true,
                confirmButtonText: mcTranslations.confirm,
                cancelButtonText: mcTranslations.cancel,
                customClass: {
                    confirmButton: 'melicon-button melicon-is-primary',
                    cancelButton: 'melicon-button melicon-is-secondary'
                }

            }).then((result) => {
                if (result.isConfirmed) {
                    toggleExportSettings(true);
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    checkbox.prop('checked', !checkbox.prop('checked'));
                }
            });
        }
    });
}

function initImportSettings() {
    jQuery(document).ready(function ($) {

        function toggleImportSettings(isDisabled) {
            if (isDisabled) {
                $('#melicon-import-settings-melicon-columns input, #melicon-import-settings-melicon-columns select, #melicon-import-settings-melicon-columns textarea').prop('disabled', true);
            } else {
                $('#melicon-import-settings-melicon-columns input, #melicon-import-settings-melicon-columns select, #melicon-import-settings-melicon-columns textarea').prop('disabled', false);
            }
        }

        // Inicializar según el estado actual del checkbox
        toggleImportSettings($('#melicon_import_is_disabled').prop('checked'));

        // Manejo del submit del formulario
        $('#melicon-import-settings-form').submit(function (e) {
            e.preventDefault();
            var formData = new FormData(this);

            formData.append("action", "melicon_save_others_settings");
            formData.append("nonce", mcTranslations.ajax_settings_nonce);

            $.ajax({
                type: "POST",
                url: mcTranslations.admin_ajax_url,
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function () {
                    $('#save-import-button').addClass('is-loading');
                },
                success: function (response) {
                    location.reload();
                },
                error: function (xhr, status, error) {
                    $('#save-import-button').removeClass('is-loading');
                    console.log(xhr.responseText);
                }
            });
        });

        // Manejo del cambio en el checkbox de deshabilitar importación
        $('#melicon_import_is_disabled').on('change', function (e) {
            var checkbox = $(this);

            e.preventDefault();

            if (!checkbox.prop('checked')) {
                toggleImportSettings(false);
            } else {
                MeliconSwal.fire({
                    icon: 'warning',
                    title: mcTranslations.alert_title_disable_import,
                    text: mcTranslations.alert_body_disable_import,
                    showCancelButton: true,
                    confirmButtonText: mcTranslations.confirm,
                    cancelButtonText: mcTranslations.cancel,
                    customClass: {
                        confirmButton: 'melicon-button melicon-is-primary',
                        cancelButton: 'melicon-button melicon-is-secondary'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        toggleImportSettings(true);
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        checkbox.prop('checked', !checkbox.prop('checked'));
                    }
                });
            }
        });
    });
}

function initSyncSettings() {
    jQuery(document).ready(function ($) {
        $('#melicon-sync-settings-form').submit(function (e) {
            e.preventDefault();
            var formData = new FormData(this);

            formData.append("action", "melicon_save_others_settings");
            formData.append("nonce", mcTranslations.ajax_settings_nonce);

            $.ajax({
                type: "POST",
                url: mcTranslations.admin_ajax_url,
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function () {
                    $('#save-sync-button').addClass('is-loading');
                },
                success: function (response) {
                    location.reload();
                },
                error: function (xhr, status, error) {
                    $('#save-sync-button').removeClass('is-loading');
                    console.log(xhr.responseText);
                }
            });
        });
    });
}

