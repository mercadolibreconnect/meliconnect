jQuery(document).ready(function ($) {

    var current_category = $('#melicon_general_category_id_input').val();
    var current_category_id = $('#melicon_current_category_id').val();
    var woo_product_id = $('#melicon_woo_product_id').val();


    showExportErrors();

    if (current_category == '') {
        $('.melicon_hide_if_change_category').hide();
        $('.melicon_show_if_change_category').show();
    }

    $('.wc-tabs li a').on('click', function (e) {
        e.preventDefault();

        // Obtener la clase del tab clicado
        var tabClass = $(this).closest('li').attr('class').split('_')[0];

        // Ocultar el contenido de Mercadolibre si se hace clic en otro tab
        if (tabClass !== 'mercadolibre') {
            $('#mercadolibre_product_data').addClass('hidden').removeClass('block');
            $('#mercadolibre_product_data').hide('fast');
        }

        // Ocultar todo el contenido de los tabs
        $('.wc-metaboxes-wrapper').addClass('hidden');

        // Mostrar el contenido correspondiente al tab clicado

        //console.log('#' + tabClass + '_product_data');
        $('#' + tabClass + '_product_data').removeClass('hidden').addClass('block');
    });

    function showExportErrors() {
        var urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('show_sync_error') === 'true') {
            // Ejecutar el evento que simula el clic y realiza las acciones
            $('.meliconnect_logs_tab a').trigger('click');

            $('html, body').animate({
                scrollTop: $("#woocommerce-product-data").offset().top - 80
            }, 1000);
        }
    }



    // Inicializar Select2
    function initializeCategorySelect2(selectElement) {
        selectElement.select2({
            placeholder: "Select a category",
            width: '50%'
        });
    }

    function loadCategories(category_id) {
        var seller_id = $('#template\\[seller_meli_id\\]').val();

        $.ajax({
            url: mcTranslations.admin_ajax_url,
            type: 'POST',
            data: {
                action: 'melicon_load_meli_categories',
                category_id: category_id,
                seller_id: seller_id,
                nonce: mcTranslations.melicon_load_meli_categories_nonce
            },
            success: function (response) {
                if (response.success) {

                    if (response.data.options !== null && response.data.options.trim() !== '') {
                        // Destruir la instancia anterior de Select2
                        $('#melicon_general_category_id').select2('destroy');

                        // Actualizar el <select> con las nuevas opciones
                        $('#melicon_general_category_id').html(response.data.options);

                        initializeCategorySelect2($('#melicon_general_category_id'));

                        $('#melicon_general_category_id_input').val('');

                    } else {
                        $('.melicon_general_category_id_field').hide();

                        $('#melicon_general_category_id_input').val(category_id).trigger('change');
                    }

                    // Actualizar el árbol de subcategorías
                    $('#subcategory-tree-container').html(response.data.path_from_route_html);

                    $('#subcategory-tree-input').val(JSON.stringify(response.data.path_from_route_json));
                    $('#melicon-category-name-input').val(response.data.category_name);

                } else {
                    console.error(response.data.message);
                }
            }
        });
    }


    // Cargar categorías principales al cargar la página
    loadCategories(current_category_id);

    // Manejar la selección de la primera categoría para cargar subcategorías
    $('#melicon_general_category_id').change(function () {
        var selectedCategory = $(this).val();
        if (selectedCategory) {
            loadCategories(selectedCategory);
        }
    });


    //When changes seller reload categories
    $('body').on('change', '#template\\[seller_meli_id\\]', function (e) {
        loadCategories(0);
        $('.melicon_general_category_id_field').show();
    });

    $('body').on('click', '.melicon-category-link', function (e) {
        e.preventDefault();

        var category_id = $(this).data('category-id');

        loadCategories(category_id);

        $('.melicon_general_category_id_field').show();
        $('.melicon_hide_if_change_category').hide();
    });



    $('body').on('change', '#melicon_general_category_id_input', function (e) {

        var newCategoryId = $(this).val();
        var product_title = $('#title').val();
        var seller_meli_id = $('#template\\[seller_meli_id\\]').val();
        var category_tree = $('#subcategory-tree-input').val();
        var category_name = $('#melicon-category-name-input').val();


        if (current_category_id !== newCategoryId) {
            // Only if selected category is different from the current category
            $('#melicon-update-category-button-container').show();
            $('.melicon_hide_if_change_category').hide();
            $('.melicon_show_if_change_category').show();

            var meliconSwalTitle, meliconSwalText;

            if (current_category_id === null || current_category_id === '') {
                meliconSwalTitle = 'You have selected a category';
                meliconSwalText = 'You must save template category to see the template attributes related. Page will be reloaded.';
                meliconSwalConfirmButtonText = 'Save changes';
                meliconSwalCancelButtonText = 'Maybe later';
            } else {
                meliconSwalTitle = 'Are you sure you want to change the category?';
                meliconSwalText = 'You have selected a different category. If you save the changes, page will be reloaded and related information will be updated.';
                meliconSwalConfirmButtonText = 'Yes, save changes';
                meliconSwalCancelButtonText = 'No, keep current category';
            }

            // Disparar el modal de SweetAlert2
            MeliconSwal.fire({
                title: meliconSwalTitle,
                text: meliconSwalText,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: meliconSwalConfirmButtonText,
                cancelButtonText: meliconSwalCancelButtonText,
                customClass: {
                    confirmButton: 'melicon-button melicon-is-primary',
                    cancelButton: 'melicon-button melicon-is-secondary'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    var category_id = $('#melicon_general_category_id_input').val();
                    if (category_id) {

                        $.ajax({
                            url: mcTranslations.admin_ajax_url,
                            type: 'POST',
                            data: {
                                action: 'melicon_update_meli_category',
                                nonce: mcTranslations.melicon_update_meli_category_nonce,
                                category_id: category_id,
                                woo_product_id: woo_product_id,
                                product_title: product_title,
                                seller_meli_id: seller_meli_id,
                                category_tree: category_tree,
                                category_name: category_name,
                            },
                            success: function (response) {
                                if (response.success) {
                                    Toast.fire({
                                        icon: 'success',
                                        title: response.data.message,
                                        timer: 1000,
                                    });
                                    location.reload();

                                } else {
                                    MeliconSwal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: response.data.message
                                    })
                                }
                            }
                        });
                    }

                } else if (result.isDismissed) {
                    $(this).val(current_category_id);

                    loadCategories(current_category_id);
                    $('.melicon_hide_if_change_category').show();
                    $('.melicon_show_if_change_category').hide();
                }
            });
        }

        if (current_category_id === newCategoryId && current_category_id !== '') {
            $('#melicon-update-category-button-container').hide();
            $('.melicon_hide_if_change_category').show();
            $('.melicon_show_if_change_category').hide();
        }

    });



    $('body').on('click', '#sync-button', function (e) {
        e.preventDefault();

        // Get the selected action (import or export)
        var actionType = $('#meli_sync_action').val();

        // Get selected data to sync
        var syncOptions = $('#meli_sync_options').val() || [];

        // Example IDs (modify based on your data source)
        var woo_product_id = $(this).data('woo-product-id');
        var meli_listing_id = $(this).data('meli-listing-id');
        var template_id = $(this).data('template-id');
        var seller_id = $(this).data('seller-id');

        // Disable button to prevent multiple clicks
        $(this).prop('disabled', true);

        // Define the correct action for AJAX
        var ajaxAction = actionType === 'import' ? 'melicon_import_single_listing' : 'melicon_export_single_listing';
        var ajaxNonce = actionType === 'import' ? mcTranslations.melicon_import_single_listing_nonce : mcTranslations.melicon_export_single_listing_nonce;

        // Perform the AJAX request
        $.ajax({
            url: mcTranslations.admin_ajax_url,
            type: 'POST',
            data: {
                action: ajaxAction,
                nonce: ajaxNonce,
                woo_product_id: woo_product_id,
                meli_listing_id: meli_listing_id,
                template_id: template_id,
                seller_id: seller_id,
                sync_options: syncOptions
            },
            success: function (response) {
                console.log(response);

                // Enable the button again
                $('#sync-button').prop('disabled', false);

                if (response.success) {
                    // Show success message
                    MeliconSwal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.data.message,
                        timer: 2000,
                        showConfirmButton: false
                    });

                    // Reload the page if necessary
                    //location.reload();
                } else {
                    // Handle error response
                    var received_message = response.data && response.data.message ? response.data.message : 'Unknown Error';

                    MeliconSwal.fire({
                        icon: 'error',
                        title: 'Error',
                        html: `<p>${received_message}</p><p>Please check the logs for details.</p>`,
                        showCancelButton: true,
                        confirmButtonText: 'Reload Page',
                        cancelButtonText: 'Maybe Later',
                        customClass: {
                            confirmButton: 'melicon-button melicon-is-primary',
                            cancelButton: 'melicon-button melicon-is-secondary'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            var currentUrl = window.location.href;
                            var newUrl = new URL(currentUrl);
                            newUrl.searchParams.set('show_sync_error', 'true');
                            //window.location.href = newUrl;
                        }
                    });
                }
            },
            error: function (xhr, status, error) {
                // Enable the button in case of error
                $('#sync-button').prop('disabled', false);

                // Show error message
                MeliconSwal.fire({
                    icon: 'error',
                    title: 'Request Error',
                    html: `<strong>Status:</strong> ${status}<br><strong>Error:</strong> ${error}<br><strong>Response:</strong> ${xhr.responseText || 'No additional details available'}`,
                    footer: 'There was a problem with the AJAX request'
                });
            }
        });
    });


    /* $('body').on('click', '#melicon_import_meli', function (e) {
        e.preventDefault();

        var woo_product_id = $(this).data('woo-product-id');
        var meli_listing_id = $(this).data('meli-listing-id');
        var template_id = $(this).data('template-id');
        var seller_id = $(this).data('seller-id');

        $.ajax({
            url: mcTranslations.admin_ajax_url,
            type: 'POST',
            data: {
                action: 'melicon_import_single_listing',
                nonce: mcTranslations.melicon_import_single_listing_nonce,
                woo_product_id: woo_product_id,
                meli_listing_id: meli_listing_id,
                template_id: template_id,
                seller_id: seller_id
            },
            success: function (response) {
                if (response.success) {
                    location.reload();
                } else {
                    console.error(response);
                }
            }
        });

    });
    */

    $('body').on('click', '#melicon_export_meli', function (e) {
        e.preventDefault();

        var $exportButton = $(this); // Almacena el botón en una variable
        var woo_product_id = $exportButton.data('woo-product-id');
        var meli_listing_id = $exportButton.data('meli-listing-id');
        var template_id = $exportButton.data('template-id');
        var seller_id = $exportButton.data('seller-id');

        // Deshabilita el botón de exportar para evitar múltiples clics
        $exportButton.prop('disabled', true);

        $.ajax({
            url: mcTranslations.admin_ajax_url,
            type: 'POST',
            data: {
                action: 'melicon_export_single_listing',
                nonce: mcTranslations.melicon_export_single_listing_nonce,
                woo_product_id: woo_product_id,
                meli_listing_id: meli_listing_id,
                template_id: template_id,
                seller_id: seller_id
            },
            success: function (response) {
                console.log(response);

                // Habilitar el botón de exportar nuevamente
                $exportButton.prop('disabled', false);

                // Si la exportación fue exitosa
                if (response.success) {
                    MeliconSwal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.data.message, // Mensaje de éxito
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {

                    if (response.data && response.data.message) {
                        var received_message = response.data.message;
                    } else {
                        var received_message = 'Unknown Error';
                    }

                    MeliconSwal.fire({
                        icon: 'error',
                        title: 'Export Error',
                        html: `<p>${received_message}</p><p>An error occurred. Please reload the page and check in Export Logs tag to see full details.</p>`,
                        showCancelButton: true,
                        confirmButtonText: 'Reload Page',
                        cancelButtonText: 'Maybe Later'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Añadir una variable a la URL y recargar la página
                            var currentUrl = window.location.href;
                            var newUrl = new URL(currentUrl);
                            newUrl.searchParams.set('show_export_error', 'true'); // Añadir variable 'show_export_error'
                            window.location.href = newUrl;
                        }
                    });
                }
            },
            error: function (xhr, status, error) {
                // Habilitar el botón de exportar en caso de error
                $exportButton.prop('disabled', false);

                // Mensaje de error si la solicitud Ajax falla
                MeliconSwal.fire({
                    icon: 'error',
                    title: 'Request Error',
                    html: `<strong>Status:</strong> ${status}<br><strong>Error:</strong> ${error}<br><strong>Response:</strong> ${xhr.responseText || 'No additional details available'}`,
                    footer: 'There was a problem with the AJAX request'
                });
            }
        });
    });



    $('body').on('click', '#melicon_save_template_button', function (e) {
        e.preventDefault();

        // Crear un objeto para almacenar los valores
        var templateData = {};

        // Seleccionar todos los inputs con un name que comience con "template["
        $('input[name^="template["], select[name^="template["], textarea[name^="template["]').each(function () {
            var name = $(this).attr('name'); // Obtener el atributo name
            var value = $(this).val(); // Obtener el valor del input

            // Si es un checkbox, verificar si está marcado
            if ($(this).attr('type') === 'checkbox') {
                // Si no está marcado, saltar la adición a templateData
                if (!$(this).is(':checked')) {
                    return;
                }
            }

            templateData[name] = value;
        });
        /* console.log('templateData');
        console.log(templateData); */

        $.ajax({
            url: mcTranslations.admin_ajax_url,
            type: 'POST',
            data: {
                action: 'melicon_save_template_data',
                nonce: mcTranslations.melicon_save_template_data_nonce,
                templateData: JSON.stringify(templateData),
                woo_product_id: woo_product_id,
                woo_product_title: $('#title').val(),
            },
            success: function (response) {
                console.log(response);
                if (response.success) {
                    console.log(response.data);
                    Toast.fire({
                        icon: "success",
                        title: response.data.message
                    });
                    //location.reload();
                } else {
                    Toast.fire({
                        icon: "error",
                        title: response.data.message
                    });
                }
            }
        });
    });

    $('body').on('click', '#melicon_create_template_button', function (e) {
        e.preventDefault();

        $('.mercadolibre_tab a').trigger('click');

        $('html, body').animate({
            scrollTop: $("#woocommerce-product-data").offset().top - 80
        }, 1000);

    });

    $('body').on('click', '#melicon_unlink_meli_listing', function (e) {
        e.preventDefault();

        MeliconSwal.fire({
            title: '¿Deseas desvincular el anuncio de Mercado Libre?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Desvincular',
            cancelButtonText: 'Cancelar',
            customClass: {
                confirmButton: 'melicon-button melicon-is-primary',
                cancelButton: 'melicon-button melicon-is-secondary'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                var woo_product_id = $(this).data('woo-product-id');
                var unlink_type = $('#melicon-desviculate-type-select').val();

                $.ajax({
                    url: mcTranslations.admin_ajax_url,
                    type: 'POST',
                    data: {
                        action: 'melicon_unlink_single_listing',
                        nonce: mcTranslations.melicon_unlink_single_listing_nonce,
                        woo_product_id: woo_product_id,
                        unlink_type: unlink_type
                    },
                    success: function (response) {
                        if (response.success) {
                            console.log(response.data);
                            Toast.fire({
                                icon: "success",
                                title: response.data.message
                            });
                            location.reload();
                        } else {
                            Toast.fire({
                                icon: "error",
                                title: response.data.message
                            });
                        }
                    }
                });
            }
        });

    });


    $('#mely_sync_all').on('change', function () {
        if (this.checked) {
            $('.select-data-to-sync-field').hide();
            $('#meli_sync_options').val(null).trigger('change');
        } else {
            $('.select-data-to-sync-field').show();
        }
    });

    if ($('#mely_sync_all').prop('checked')) {
        $('.select-data-to-sync-field').hide();
    }

    setTimeout(function () {
        $('#meliconnect-loader').hide();
        $('#meliconnect-box').fadeIn();
    }, 2000);

    $('#meli_sync_options').select2({
        placeholder: "Select fields to sync",
        allowClear: true,
        closeOnSelect: false,
        templateResult: function (option) {
            if (!option.id) {
                return option.text;
            }

            // Crear un checkbox dentro del dropdown
            let $checkbox = $('<input type="checkbox" class="sync-checkbox" value="' + option.id + '"/>');

            // Verificar si el elemento está seleccionado y marcar el checkbox
            let selectedValues = $('#meli_sync_options').val() || [];
            if (selectedValues.includes(option.id)) {
                $checkbox.prop('checked', true);
            }

            return $('<span>').append($checkbox).append(' ' + option.text);
        },
        templateSelection: function (option) {
            return option.text;
        }
    });

    // Evento para actualizar los checkboxes al seleccionar/deseleccionar
    $('#meli_sync_options').on('select2:select select2:unselect', function () {
        $('.sync-checkbox').each(function () {
            let checkbox = $(this);
            let value = checkbox.val();
            let selectedValues = $('#meli_sync_options').val() || [];
            checkbox.prop('checked', selectedValues.includes(value));
        });

        // Si hay al menos un elemento seleccionado, desmarcar "All Data"
        if ($(this).val().length > 0) {
            $('#meli_sync_all').prop('checked', false);
        }
    });

    $('#meli_sync_all').on('change', function () {
        if (this.checked) {
            $('#meli_sync_options').val(null).trigger('change');
        }
    });


    /*Copy last JSON sent to mercadolibre for debug purposes */
    const $copyBtn = $('#copy-last-json-button');
    const $jsonPre = $('#meliconnect-json-to-copy');

    if ($copyBtn.length && $jsonPre.length) {
        $copyBtn.on('click', function () {
            const text = $jsonPre.text();

            navigator.clipboard.writeText(text).then(function () {
                $copyBtn.text('Copied!');
                setTimeout(() => {
                    $copyBtn.text('Copy last JSON sent');
                }, 2000);
            }).catch(function (err) {
                alert('Error copying JSON: ' + err);
            });
        });
    }

});