jQuery(document).ready(function ($) {

    $('#meliconnect-export-bulk-actions-form').on('submit', function (e) {
        e.preventDefault();
    
        // Obtener los IDs seleccionados desde localStorage
        let selectedIds = localStorage.getItem('selected-export-listing-ids');
    
        // Asegurarse de que haya IDs seleccionados
        if (selectedIds) {
    
            let selectedAction = $('#action-to-do').val();
    
            if (selectedAction === '-1') {
                // Alerta de SweetAlert para acción inválida
                MeliconSwal.fire({
                    icon: 'error',
                    title: meliconnect_translations.invalid_action,
                    text: meliconnect_translations.please_select_a_valid_bulk_action
                });
            } else {
                // Alerta de SweetAlert para confirmar la acción
                MeliconSwal.fire({
                    icon: 'warning',
                    title: meliconnect_translations.alert_title_apply_bulk_action,
                    text: meliconnect_translations.alert_body_apply_bulk_action,
                    showCancelButton: true,
                    confirmButtonText: meliconnect_translations.confirm,
                    cancelButtonText: meliconnect_translations.cancel,
                    customClass: {
                        confirmButton: 'meliconnect-button meliconnect-is-primary',
                        cancelButton: 'meliconnect-button meliconnect-is-secondary'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Enviar la solicitud AJAX
                        $.ajax({
                            url: meliconnect_translations.admin_ajax_url,
                            type: 'POST',
                            data: {
                                action: 'meliconnect_bulk_export_action',
                                action_to_do: selectedAction,
                                products_ids: selectedIds,
                                nonce: meliconnect_translations.export_bulk_action_nonce
                            },
                            success: function (response) {
                                // Manejar la respuesta
                                if (response.success) {
                                    location.reload();
                                } else {
                                    // Manejar el error si es necesario
                                    MeliconSwal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: response.data.message || 'An error occurred.'
                                    });
                                }
                            },
                            error: function (xhr, status, error) {
                                // Manejar error de la solicitud AJAX
                                MeliconSwal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: error
                                });
                            }
                        });
                    }
                });
            }
    
        } else {
            // Mostrar una alerta si no hay IDs seleccionados
            MeliconSwal.fire({
                icon: 'warning',
                title: meliconnect_translations.no_items_selected,
                text: meliconnect_translations.select_items_to_apply_action
            });
        }
    });
    
    /* START functions to select items table */
    const storageKey = 'selected-export-listing-ids'; // Clave para localStorage

    // Manejar el clic en el checkbox "Seleccionar todos"
    $('#cb-select-all-1').on('change', function () {
        // Comprobar si el checkbox "Seleccionar todos" está marcado
        const isChecked = $(this).is(':checked');

        // Seleccionar o deseleccionar todos los checkboxes en la página
        $('.bulk-checkbox').prop('checked', isChecked);

        // Actualizar el conteo y el almacenamiento local
        let selectedIds = [];
        if (isChecked) {
            // Obtener los valores de los checkboxes en la página actual
            $('.bulk-checkbox:checked').each(function () {
                selectedIds.push($(this).val());
            });
        }
        saveSelectedIds(selectedIds);
        updateSelectedCount();
    });

    function updateSelectedCount() {
        // Obtener el total de IDs almacenados en el localStorage
        let savedIds = localStorage.getItem(storageKey);
        let selectedCount = savedIds ? savedIds.split(',').length : 0;
        $('#selected-items-count').text(selectedCount);
        $('.meliconnect-export-selected-items-tag-column').toggle(selectedCount > 0);
    }

    // Cargar los IDs seleccionados del localStorage al cargar la página
    function loadSelectedIds() {
        let savedIds = localStorage.getItem(storageKey);
        if (savedIds) {
            savedIds.split(',').forEach(function (id) {
                $(`.bulk-checkbox[value="${id}"]`).prop('checked', true);
            });
        }
        updateSelectedCount();
    }

    // Guardar los IDs seleccionados en localStorage
    function saveSelectedIds(ids) {
        localStorage.setItem(storageKey, ids.join(','));
        updateSelectedCount();
    }

    // Inicializar la lista de IDs seleccionados
    loadSelectedIds();

    $('.bulk-checkbox').on('change', function () {
        let checkboxValue = $(this).val();
        let currentValues = localStorage.getItem(storageKey) ? localStorage.getItem(storageKey).split(',') : [];

        if ($(this).is(':checked')) {
            if (!currentValues.includes(checkboxValue)) {
                currentValues.push(checkboxValue);
            }
        } else {
            currentValues = currentValues.filter(function (value) {
                return value !== checkboxValue;
            });
        }

        saveSelectedIds(currentValues);
    });

    $('#meliconnect-clear-selected-items').on('click', function () {
        clearSelectedItems();
    });

    function clearSelectedItems() {
        $('.bulk-checkbox:checked').prop('checked', false);
        localStorage.removeItem(storageKey);
        updateSelectedCount();
    }

    /* END functions to select items table */



    /* STRAT update progress */
    function updateProgress() {
        var processId = $('#meliconnect-process-id-hidden').val();

        if (!processId) {
            return;
        }
        $.ajax({
            url: meliconnect_translations.admin_ajax_url, // URL de AJAX en WordPress
            method: 'POST',
            data: {
                action: 'meliconnect_get_process_progress',
                nonce: meliconnect_translations.get_process_progress_nonce,
                process_id: processId
            },
            success: function (response) {
                if (response.success) {
                    var data = response.data;

                    // Si el proceso está terminado
                    if (data.status === "finished") {
                        $('#meliconnect-process-progress-bar')
                            .attr('value', 100)
                            .text(100 + '%')
                            .removeClass('is-info')
                            .addClass('is-success'); // Cambiar la clase CSS

                        $('#meliconnect-process-progress').text(100 + '%');
                        $('#meliconnect-process-text-title').text(meliconnect_translations.process_finished);

                        // Recargar la página
                        setTimeout(function () {
                            location.reload();
                        }, 1000); // Esperar un segundo antes de recargar
                    } else {
                        // Actualizar la barra de progreso normalmente
                        $('#meliconnect-process-progress-bar')
                            .attr('value', data.progress_value)
                            .text(data.progress_value + '%');

                        $('#meliconnect-process-progress').text(data.progress_value + '%');
                    }

                    // Actualizar otros datos en la UI
                    $('#meliconnect-process-executed').text(data.executed);
                    $('#meliconnect-process-total').text(data.total);
                    $('#meliconnect-process-total-success').text(data.total_success);
                    $('#meliconnect-process-total-fails').text(data.total_fails);
                    $('#meliconnect-process-execution-time').text(data.execution_time);
                } else {
                    console.error('Error retrieving process data:', response.data.message);
                }
            }
        });
    }

    setInterval(updateProgress, 5000);

    /* END update progress */

    $('.meliconnect-toggle-error').on('click', function (e) {
        e.preventDefault();

        var error = $(this).data('error');

        // Convertir el error JSON a un formato legible
        var errorContent = '';
        if (typeof error === 'object') {
            // Iterar sobre las claves del objeto y formatear cada una en un párrafo
            for (var key in error) {
                if (error.hasOwnProperty(key)) {
                    var value = JSON.stringify(error[key], null, 2); // Formatear el valor
                    errorContent += `<p><strong>${key}:</strong></p><pre>${value}</pre>`;
                }
            }
        } else {
            errorContent = `<p>${error}</p>`; // Si el error es una cadena simple
        }

        MeliconSwal.fire({
            icon: 'error',
            title: meliconnect_translations.error,
            html: errorContent,
            customClass: {
                popup: 'meliconnect-swal-popup', // Clase personalizada para el popup
                title: 'meliconnect-swal-title', // Clase personalizada para el título
                content: 'meliconnect-swal-content' // Clase personalizada para el contenido
            },
            confirmButtonText: meliconnect_translations.close
        });

    });

    $('.meliconnect-toggle-json').on('click', function (e) {
        e.preventDefault();

        var json_sent = $(this).data('json-sent');

        var jsonContent = '';
        if (typeof json_sent === 'object') {
            jsonContent += `<p class="meliconnect-copy-json-sent-unformated" style="display:none">${JSON.stringify(json_sent, null, 2)}</p>`;

            // Iterar sobre las claves del objeto y formatear cada una en un párrafo
            for (var key in json_sent) {
                if (json_sent.hasOwnProperty(key)) {
                    var value = JSON.stringify(json_sent[key], null, 2);
                    jsonContent += `<p><strong>${key}:</strong></p><pre>${value}</pre>`;
                }
            }
        } else {
            // Si no es un objeto, simplemente mostrar el valor como cadena
            jsonContent = `<p>${json_sent}</p>`;
        }

        MeliconSwal.fire({
            icon: 'info',
            title: meliconnect_translations.alert_last_json_sent_title,
            html: jsonContent, // Mostrar el JSON formateado en formato HTML
            customClass: {
                popup: 'meliconnect-swal-popup', // Clase personalizada para el popup
                title: 'meliconnect-swal-title', // Clase personalizada para el título
                content: 'meliconnect-swal-content' // Clase personalizada para el contenido
            },
            showCancelButton: true,
            confirmButtonText: meliconnect_translations.copy_to_clipboard,
            cancelButtonText: meliconnect_translations.back,
            customClass: {
                confirmButton: 'meliconnect-button meliconnect-is-primary',
                cancelButton: 'meliconnect-button meliconnect-is-secondary'
            },
            preConfirm: function () {
                // Función para copiar el JSON al portapapeles
                var jsonContent = $('.meliconnect-copy-json-sent-unformated').html();
    
                var $temp = $("<div>");
                $("body").append($temp);
                $temp.attr("contenteditable", true)
                    .html(jsonContent).select()
                    .on("focus", function () { document.execCommand('selectAll', false, null); })
                    .focus();
                document.execCommand("copy");
                $temp.remove();
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Alerta de éxito después de copiar al portapapeles
                MeliconSwal.fire({
                    icon: 'success',
                    title: meliconnect_translations.copy_to_clipboard_success
                });
            }
        });

    });



    // Función para copiar el contenido al portapapeles


    $('#meliconnect-exporter-cancel-process').on('click', function (e) {
        e.preventDefault();

        var processId = $(this).data('process-id');

        MeliconSwal.fire({
            icon: 'warning',
            title: meliconnect_translations.alert_title_cancel_custom_export,
            text: meliconnect_translations.alert_body_cancel_custom_export,
            showCancelButton: true,
            confirmButtonText: meliconnect_translations.confirm,
            cancelButtonText: meliconnect_translations.cancel,
            customClass: {
                confirmButton: 'meliconnect-button meliconnect-is-primary',
                cancelButton: 'meliconnect-button meliconnect-is-secondary'
            },
            preConfirm: function () {
                // Realizar la solicitud AJAX si el usuario confirma la acción
                return $.ajax({
                    url: meliconnect_translations.admin_ajax_url,
                    type: 'POST',
                    data: {
                        action: 'meliconnect_cancel_custom_export',
                        nonce: meliconnect_translations.cancel_custom_export_nonce,
                        process_id: processId
                    }
                }).then(function (response) {
                    location.reload();
                }).catch(function (xhr, status, error) {
                    MeliconSwal.fire({
                        icon: 'error',
                        title: meliconnect_translations.error,
                        text: error
                    });
                });
            }
        });
    });


    $('#meliconnect-exporter-view-logs').on('click', function () {
        window.open('/wp-admin/admin.php?page=wc-status&tab=logs&source=meliconnect-custom-export&orderby=created&order=asc', '_blank');
    });


    $('.meliconnect-delete-listing-vinculation').on('click', function (e) {
        e.preventDefault();

        var wooProductId = $(this).data('woo-product-id');

        MeliconSwal.fire({
            icon: 'warning',
            title: meliconnect_translations.alert_title_desvinculate_product,
            text: meliconnect_translations.alert_body_desvinculate_product,
            showCancelButton: true,
            confirmButtonText: meliconnect_translations.confirm,
            cancelButtonText: meliconnect_translations.cancel,
            customClass: {
                confirmButton: 'meliconnect-button meliconnect-is-primary',
                cancelButton: 'meliconnect-button meliconnect-is-secondary'
            },
            preConfirm: function () {
                // Realizar la solicitud AJAX si el usuario confirma la desvinculación
                return $.ajax({
                    url: meliconnect_translations.admin_ajax_url,
                    type: 'POST',
                    data: {
                        action: 'meliconnect_desvinculate_listing',
                        nonce: meliconnect_translations.desvinculate_product_nonce,
                        wooProductId: wooProductId,
                    }
                }).then(function (response) {
                    // Recargar la página al recibir la respuesta
                    location.reload();
                }).catch(function (xhr, status, error) {
                    // Manejar el error si la solicitud AJAX falla
                    MeliconSwal.fire({
                        icon: 'error',
                        title: meliconnect_translations.error,
                        text: error
                    });
                });
            }
        });
        
    });

    $('#meliconnect-strat-new-export').on('click', function (e) {
        e.preventDefault();
        var processId = $(this).data('process-id');

        $.ajax({
            url: meliconnect_translations.admin_ajax_url,
            type: 'POST',
            data: {
                action: 'meliconnect_clean_custom_export_process',
                nonce: meliconnect_translations.clean_custom_export_nonce,
                processId: processId
            },
            success: function (response) {
                location.reload();
            },
            error: function (xhr, status, error) {
                MeliconSwal.fire({
                    icon: 'error',
                    title: meliconnect_translations.error,
                    text: error
                });
            }
        });
    });


});