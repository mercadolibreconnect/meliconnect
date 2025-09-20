jQuery(document).ready(function ($) {

    $('#action-to-do').select2();

    $('.match-all-listings-with-products').on('click', function (e) {
        e.preventDefault();
        let match_by = $(this).data('match-by');
        let $button = $(this);

        $button.addClass('disabled meliconnect-is-loading');

        $.ajax({
            url: meliconnect_translations.admin_ajax_url,
            type: 'POST',
            data: {
                action: 'meliconnect_match_listings_with_products',
                match_by: match_by,
                nonce: meliconnect_translations.match_listings_with_products_nonce
            },
            success: function (response) {
                if (response.success) {
                    // Recargar la página al completar con éxito
                    location.reload();
                } else {
                    // Mostrar un mensaje de error con SweetAlert
                    MeliconSwal.fire({
                        icon: 'error',
                        title: meliconnect_translations.error,
                        text: response.data.message || meliconnect_translations.default_error_message
                    });
                }
            },
            error: function (xhr, status, error) {
                // Mostrar un mensaje de error con SweetAlert en caso de error
                MeliconSwal.fire({
                    icon: 'error',
                    title: meliconnect_translations.error,
                    text: error
                });
            },
            complete: function () {
                // Remover las clases 'disabled' e 'is-loading' cuando la solicitud AJAX termine
                $button.removeClass('disabled meliconnect-is-loading');
            }
        });
        
    });

    $('#clear-all-matches').on('click', function (e) {
        e.preventDefault();

        $.ajax({
            url: meliconnect_translations.admin_ajax_url,
            type: 'POST',
            data: {
                action: 'meliconnect_clear_matches',
                nonce: meliconnect_translations.clear_all_matches_nonce
            },
            success: function (response) {
                // Recargar la página al completar con éxito
                location.reload();
            },
            error: function (xhr, status, error) {
                // Mostrar un mensaje de error con SweetAlert
                MeliconSwal.fire({
                    icon: 'error',
                    title: meliconnect_translations.error,
                    text: error || meliconnect_translations.default_error_message
                });
            }
        });
        
    });

    

    $('.meliconnect-clear-product-match').on('click', function (e) {
        e.preventDefault();
        let meliListingId = $(this).data('meli-listing-id');

        $.ajax({
            url: meliconnect_translations.admin_ajax_url,
            type: 'POST',
            data: {
                action: 'meliconnect_clear_selected_products_match',
                meli_listings_ids: meliListingId,
                nonce: meliconnect_translations.clear_selected_matches_nonce
            },
            success: function (response) {
                location.reload();
            },
            error: function (xhr, status, error) {
                MeliconSwal.fire({
                    icon: 'error',
                    title: meliconnect_translations.error,
                    text: error || meliconnect_translations.default_error_message
                });
            }   
        });
    });

    $('#meliconnect-importer-view-logs').on('click', function () {
        window.open('/wp-admin/admin.php?page=wc-status&tab=logs&source=meliconnect-custom-import&orderby=created&order=asc', '_blank');
    });

    $('#meliconnect-importer-delete-finished').on('click', function (e) {
        e.preventDefault();
        $.ajax({
            url: meliconnect_translations.admin_ajax_url,
            type: 'POST',
            data: {
                action: 'meliconnect_cancel_finished_processes',
                nonce: meliconnect_translations.cancel_finished_processes_nonce
            },
            success: function (response) {
                location.reload();
            },
            error: function (xhr, status, error) {
                MeliconSwal.fire({
                    icon: 'error',
                    title: meliconnect_translations.error,
                    text: error || meliconnect_translations.default_error_message
                });
            }
        });
    });

    $('#meliconnect-get-meli-user-listings').submit(function (e) {
        e.preventDefault();
        var formData = new FormData(this);

        formData.append("action", "meliconnect_get_meli_user_listings");
        formData.append("nonce", meliconnect_translations.get_listings_nonce);

        $.ajax({
            type: "POST",
            url: meliconnect_translations.admin_ajax_url,
            data: formData,
            contentType: false,
            processData: false,
            beforeSend: function () {
                $('#meliconnect-get-meli-user-listings-button').addClass('is-loading');
            },
            success: function (response) {
                // Mostrar mensaje de éxito con SweetAlert
                MeliconSwal.fire({
                    icon: 'success',
                    title: meliconnect_translations.success_message,
                    text: meliconnect_translations.all_items_imported,
                    timer: 2000, // Duración en milisegundos
                    showConfirmButton: false // Ocultar botón de confirmación
                }).then(() => {
                    location.reload(); // Recargar la página tras el cierre de la alerta
                });
        
                $('#meliconnect-get-meli-user-listings-button').removeClass('is-loading');
            },
            error: function (xhr, status, error) {
                // Manejar error con SweetAlert
                MeliconSwal.fire({
                    icon: 'error',
                    title: meliconnect_translations.error,
                    text: error || meliconnect_translations.default_error_message
                });
        
                $('#meliconnect-get-meli-user-listings-button').removeClass('is-loading');
            }
        });
        
    });

    /* START functions to select items table */
    const storageKey = 'selected-listing-ids'; // Clave para localStorage

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
        $('.meliconnect-import-selected-items-tag-column').toggle(selectedCount > 0);
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

    $('#meliconnect-reset-meli-user-listings-button').on('click', function (e) {
        e.preventDefault();
    
        MeliconSwal.fire({
            icon: 'warning',
            title: meliconnect_translations.reset_user_listings,
            text: meliconnect_translations.reset_user_listings_body,
            showCancelButton: true,
            confirmButtonText: meliconnect_translations.confirm,
            cancelButtonText: meliconnect_translations.cancel,
            customClass: {
                confirmButton: 'meliconnect-button meliconnect-is-primary',
                cancelButton: 'meliconnect-button meliconnect-is-secondary'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: meliconnect_translations.admin_ajax_url,
                    type: 'POST',
                    data: {
                        action: 'meliconnect_reset_user_listings',
                        nonce: meliconnect_translations.reset_listings_nonce
                    },
                    success: function (response) {
                        clearSelectedItems();
                        location.reload();
                    },
                    error: function (xhr, status, error) {
                        MeliconSwal.fire({
                            icon: 'error',
                            title: meliconnect_translations.error,
                            text: error || meliconnect_translations.default_error_message,
                        });
                    }
                });
            }
        });
    });
    

    

    $('#meliconnect-process-import-button').on('click', function (e) {
        e.preventDefault();
    
        clearSelectedItems();
    
        MeliconSwal.fire({
            icon: 'info',
            title: meliconnect_translations.alert_title_import_process_init,
            text: meliconnect_translations.alert_body_import_process_init,
            showCancelButton: true,
            confirmButtonText: meliconnect_translations.confirm,
            cancelButtonText: meliconnect_translations.cancel,
            customClass: {
                confirmButton: 'meliconnect-button meliconnect-is-primary',
                cancelButton: 'meliconnect-button meliconnect-is-secondary'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: meliconnect_translations.admin_ajax_url,
                    type: 'POST',
                    data: {
                        action: 'meliconnect_init_import_process',
                        nonce: meliconnect_translations.init_import_process_nonce
                    },
                    success: function (response) {
                        location.reload();
                    },
                    error: function (xhr, status, error) {
                        MeliconSwal.fire({
                            icon: 'error',
                            title: meliconnect_translations.error,
                            text: error || meliconnect_translations.default_error_message,
                        });
                    }
                });
            }
        });
    });
    

    $('#meliconnect-importer-cancel-process').on('click', function (e) {
        e.preventDefault();
    
        var processId = $(this).data('process-id');
    
        MeliconSwal.fire({
            icon: 'warning',
            title: meliconnect_translations.alert_title_cancel_custom_import,
            text: meliconnect_translations.alert_body_cancel_custom_import,
            showCancelButton: true,
            confirmButtonText: meliconnect_translations.confirm,
            cancelButtonText: meliconnect_translations.cancel,
            customClass: {
                confirmButton: 'meliconnect-button meliconnect-is-primary',
                cancelButton: 'meliconnect-button meliconnect-is-secondary'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: meliconnect_translations.admin_ajax_url,
                    type: 'POST',
                    data: {
                        action: 'meliconnect_cancel_custom_import',
                        nonce: meliconnect_translations.cancel_custom_import_nonce,
                        process_id: processId
                    },
                    success: function (response) {
                        location.reload();
                    },
                    error: function (xhr, status, error) {
                        MeliconSwal.fire({
                            icon: 'error',
                            title: meliconnect_translations.error,
                            text: error || meliconnect_translations.default_error_message,
                        });
                    }
                });
            }
        });
    });
    

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


    $('#meliconnect-import-bulk-actions-form').on('submit', function (e) {
        e.preventDefault();
    
        // Obtener los IDs seleccionados desde localStorage
        let selectedIds = localStorage.getItem('selected-listing-ids');
    
        if (selectedIds) {
            let selectedAction = $('#action-to-do').val();
    
            if (selectedAction === '-1') {
                // Mostrar una alerta si la acción es inválida
                MeliconSwal.fire({
                    icon: 'error',
                    title: meliconnect_translations.invalid_action,
                    text: meliconnect_translations.please_select_a_valid_bulk_action,
                });
            } else {
                // Confirmar la acción
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
                        $.ajax({
                            url: meliconnect_translations.admin_ajax_url,
                            type: 'POST',
                            data: {
                                action: 'meliconnect_bulk_import_action',
                                action_to_do: selectedAction,
                                meli_listing_ids: selectedIds,
                                nonce: meliconnect_translations.import_bulk_action_nonce
                            },
                            success: function (response) {
                                if (response.success) {
                                    location.reload();
                                } else {
                                    MeliconSwal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: response.data.message || meliconnect_translations.default_error_message,
                                    });
                                }
                            },
                            error: function (xhr, status, error) {
                                MeliconSwal.fire({
                                    icon: 'error',
                                    title: meliconnect_translations.error,
                                    text: error || meliconnect_translations.default_error_message,
                                });
                            }
                        });
                    }
                });
            }
        } else {
            // Mostrar alerta si no hay ítems seleccionados
            MeliconSwal.fire({
                icon: 'warning',
                title: meliconnect_translations.no_items_selected,
                text: meliconnect_translations.select_items_to_apply_action,
            });
        }
    });
    

    $('.meliconnect-delete-product-vinculation').on('click', function (e) {
        e.preventDefault();
    
        var productType = $(this).data('product-type');
        var wooProductId = $(this).data('woo-product-id');
        var meliListingId = $(this).data('meli-listing-id');
    
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
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: meliconnect_translations.admin_ajax_url,
                    type: 'POST',
                    data: {
                        action: 'meliconnect_desvinculate_woo_product',
                        nonce: meliconnect_translations.desvinculate_product_nonce,
                        wooProductId: wooProductId,
                        meliListingId: meliListingId,
                    },
                    success: function (response) {
                        location.reload();
                    },
                    error: function (xhr, status, error) {
                        MeliconSwal.fire({
                            icon: 'error',
                            title: meliconnect_translations.error,
                            text: error || meliconnect_translations.default_error_message,
                        });
                    }
                });
            }
        });
    });
    

    $('#meliconnect-apply-match-button').on('click', function (e) {
        e.preventDefault();
    
        var woo_product_id = $('#meliconnect-match-select-products-select').val();
        var user_listing_id = $("#meliconnect-match-modal_user-listing-id").val();
    
        if (!woo_product_id || !user_listing_id) {
            MeliconSwal.fire({
                icon: 'error',
                title: meliconnect_translations.error,
                text: meliconnect_translations.please_select_a_product_and_a_listing,
            });
            return;
        }
    
        $.ajax({
            url: meliconnect_translations.admin_ajax_url,
            type: 'POST',
            data: {
                action: 'meliconnect_apply_match',
                nonce: meliconnect_translations.apply_match_nonce,
                user_listing_id: user_listing_id,
                woo_product_id: woo_product_id
            },
            success: function (response) {
                location.reload();
            },
            error: function (xhr, status, error) {
                MeliconSwal.fire({
                    icon: 'error',
                    title: meliconnect_translations.error,
                    text: error || meliconnect_translations.default_error_message,
                });
            }
        });
    });
    


    $('.meliconnect-find-product-to-match').on('click', function (e) {
        e.preventDefault();

        // Obtener datos del botón
        var userListingId = $(this).data('user-listing-id')//id of user listing table item;
        var listingId = $(this).data('listing-id');
        var listingTitle = $(this).data('meli-listing-title');
        var listingType = $(this).data('meli-listing-type');
        var meliSku = $(this).data('meli-sku');
        var meliStatus = $(this).data('meli-status');
        var price = $(this).data('price');
        var availableQuantity = $(this).data('available-quantity');


        $('#meliconnect-meli-listing-title-to-match').text(listingTitle);

        // Mostrar el "loading" inicial en la primera columna
        var $listingInfo = $('#meliconnect-meli-listing-data-to-match');

        // Simular la carga de datos (podrías reemplazar esto con una llamada AJAX si lo necesitas)
        var listingContent = `
                <p><strong>ID:</strong> ${listingId}</p>
                <p><strong>SKU:</strong> ${meliSku}</p>
                <p><strong>Type:</strong> ${listingType}</p>
                <p><strong>Status:</strong> ${meliStatus}</p>
                <p><strong>Price:</strong> ${price}</p>
                <p><strong>Stock:</strong> ${availableQuantity}</p>        
            `;

        // Reemplazar el "loading" con la información del listing
        $listingInfo.html(listingContent);

        $("#meliconnect-match-modal_user-listing-id").val(userListingId);


        $.ajax({
            url: meliconnect_translations.admin_ajax_url,
            type: 'GET',
            data: {
                action: 'meliconnect_get_match_available_products',
                nonce: meliconnect_translations.get_match_available_products_nonce,
                productType: listingType,
            },
            success: function (response) {
                if (response.success) {
                    $('#meliconnect-match-select-products-select').html(response.data.options);
                    $('#meliconnect-match-select-products-select').select2({
                        dropdownParent: $("#meliconnect-find-match-modal")
                    });

                    $('#meliconnect-match-product-select-container').show();

                    $('#meliconnect-match-select-products-select').on('select2:select', function (e) {
                        var data = e.params.data;
                        var detailsHtml = `
                            <p><strong>ID:</strong> ${data.id}</p>
                            <p><strong>SKU:</strong> ${data.element.dataset.sku}</p>
                            <p><strong>Type:</strong> ${data.element.dataset.type}</p>
                            <p><strong>Status:</strong> ${data.element.dataset.status}</p>
                            <p><strong>Price:</strong> ${data.element.dataset.price}</p>
                            <p><strong>Stock:</strong> ${data.element.dataset.stock}</p>
                        `;
                        $('#meliconnect-matched-product-details').html(detailsHtml);
                    });
                    console.log('Products found:', response.data.options);

                } else {
                    console.log('No products found');
                }
            },
            error: function (xhr, status, error) {
                console.log('Error on meliconnect_get_match_available_products :', error);
            }
        });
    });
});