jQuery(document).ready(function ($) {

    $('#action-to-do').select2();

    $('.match-all-listings-with-products').on('click', function (e) {
        e.preventDefault();
        let match_by = $(this).data('match-by');
        let $button = $(this);

        $button.addClass('disabled melicon-is-loading');

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'melicon_match_listings_with_products',
                match_by: match_by,
                nonce: mcTranslations.match_listings_with_products_nonce
            },
            success: function (response) {
                if (response.success) {
                    // Recargar la página al completar con éxito
                    location.reload();
                } else {
                    // Mostrar un mensaje de error con SweetAlert
                    MeliconSwal.fire({
                        icon: 'error',
                        title: mcTranslations.error,
                        text: response.data.message || mcTranslations.default_error_message
                    });
                }
            },
            error: function (xhr, status, error) {
                // Mostrar un mensaje de error con SweetAlert en caso de error
                MeliconSwal.fire({
                    icon: 'error',
                    title: mcTranslations.error,
                    text: error
                });
            },
            complete: function () {
                // Remover las clases 'disabled' e 'is-loading' cuando la solicitud AJAX termine
                $button.removeClass('disabled melicon-is-loading');
            }
        });
        
    });

    $('#clear-all-matches').on('click', function (e) {
        e.preventDefault();

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'melicon_clear_matches',
                nonce: mcTranslations.clear_all_matches_nonce
            },
            success: function (response) {
                // Recargar la página al completar con éxito
                location.reload();
            },
            error: function (xhr, status, error) {
                // Mostrar un mensaje de error con SweetAlert
                MeliconSwal.fire({
                    icon: 'error',
                    title: mcTranslations.error,
                    text: error || mcTranslations.default_error_message
                });
            }
        });
        
    });

    

    $('.melicon-clear-product-match').on('click', function (e) {
        e.preventDefault();
        let meliListingId = $(this).data('meli-listing-id');

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'melicon_clear_selected_products_match',
                meli_listings_ids: meliListingId,
                nonce: mcTranslations.clear_selected_matches_nonce
            },
            success: function (response) {
                location.reload();
            },
            error: function (xhr, status, error) {
                MeliconSwal.fire({
                    icon: 'error',
                    title: mcTranslations.error,
                    text: error || mcTranslations.default_error_message
                });
            }   
        });
    });

    $('#melicon-importer-view-logs').on('click', function () {
        window.open('/wp-admin/admin.php?page=wc-status&tab=logs&source=melicon-custom-import&orderby=created&order=asc', '_blank');
    });

    $('#melicon-importer-delete-finished').on('click', function (e) {
        e.preventDefault();
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'melicon_cancel_finished_processes',
                nonce: mcTranslations.cancel_finished_processes_nonce
            },
            success: function (response) {
                location.reload();
            },
            error: function (xhr, status, error) {
                MeliconSwal.fire({
                    icon: 'error',
                    title: mcTranslations.error,
                    text: error || mcTranslations.default_error_message
                });
            }
        });
    });

    $('#melicon-get-meli-user-listings').submit(function (e) {
        e.preventDefault();
        var formData = new FormData(this);

        formData.append("action", "melicon_get_meli_user_listings");
        formData.append("nonce", mcTranslations.get_listings_nonce);

        $.ajax({
            type: "POST",
            url: ajaxurl,
            data: formData,
            contentType: false,
            processData: false,
            beforeSend: function () {
                $('#melicon-get-meli-user-listings-button').addClass('is-loading');
            },
            success: function (response) {
                // Mostrar mensaje de éxito con SweetAlert
                MeliconSwal.fire({
                    icon: 'success',
                    title: mcTranslations.success_message,
                    text: mcTranslations.all_items_imported,
                    timer: 2000, // Duración en milisegundos
                    showConfirmButton: false // Ocultar botón de confirmación
                }).then(() => {
                    location.reload(); // Recargar la página tras el cierre de la alerta
                });
        
                $('#melicon-get-meli-user-listings-button').removeClass('is-loading');
            },
            error: function (xhr, status, error) {
                // Manejar error con SweetAlert
                MeliconSwal.fire({
                    icon: 'error',
                    title: mcTranslations.error,
                    text: error || mcTranslations.default_error_message
                });
        
                $('#melicon-get-meli-user-listings-button').removeClass('is-loading');
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
        $('.melicon-import-selected-items-tag-column').toggle(selectedCount > 0);
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

    $('#melicon-clear-selected-items').on('click', function () {
        clearSelectedItems();
    });

    function clearSelectedItems() {
        $('.bulk-checkbox:checked').prop('checked', false);
        localStorage.removeItem(storageKey);
        updateSelectedCount();
    }

    /* END functions to select items table */

    $('#melicon-reset-meli-user-listings-button').on('click', function (e) {
        e.preventDefault();
    
        MeliconSwal.fire({
            icon: 'warning',
            title: mcTranslations.reset_user_listings,
            text: mcTranslations.reset_user_listings_body,
            showCancelButton: true,
            confirmButtonText: mcTranslations.confirm,
            cancelButtonText: mcTranslations.cancel,
            customClass: {
                confirmButton: 'melicon-button melicon-is-primary',
                cancelButton: 'melicon-button melicon-is-secondary'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'melicon_reset_user_listings',
                        nonce: mcTranslations.reset_listings_nonce
                    },
                    success: function (response) {
                        clearSelectedItems();
                        location.reload();
                    },
                    error: function (xhr, status, error) {
                        MeliconSwal.fire({
                            icon: 'error',
                            title: mcTranslations.error,
                            text: error || mcTranslations.default_error_message,
                        });
                    }
                });
            }
        });
    });
    

    

    $('#melicon-process-import-button').on('click', function (e) {
        e.preventDefault();
    
        clearSelectedItems();
    
        MeliconSwal.fire({
            icon: 'info',
            title: mcTranslations.alert_title_import_process_init,
            text: mcTranslations.alert_body_import_process_init,
            showCancelButton: true,
            confirmButtonText: mcTranslations.confirm,
            cancelButtonText: mcTranslations.cancel,
            customClass: {
                confirmButton: 'melicon-button melicon-is-primary',
                cancelButton: 'melicon-button melicon-is-secondary'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'melicon_init_import_process',
                        nonce: mcTranslations.init_import_process_nonce
                    },
                    success: function (response) {
                        location.reload();
                    },
                    error: function (xhr, status, error) {
                        MeliconSwal.fire({
                            icon: 'error',
                            title: mcTranslations.error,
                            text: error || mcTranslations.default_error_message,
                        });
                    }
                });
            }
        });
    });
    

    $('#melicon-importer-cancel-process').on('click', function (e) {
        e.preventDefault();
    
        var processId = $(this).data('process-id');
    
        MeliconSwal.fire({
            icon: 'warning',
            title: mcTranslations.alert_title_cancel_custom_import,
            text: mcTranslations.alert_body_cancel_custom_import,
            showCancelButton: true,
            confirmButtonText: mcTranslations.confirm,
            cancelButtonText: mcTranslations.cancel,
            customClass: {
                confirmButton: 'melicon-button melicon-is-primary',
                cancelButton: 'melicon-button melicon-is-secondary'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'melicon_cancel_custom_import',
                        nonce: mcTranslations.cancel_custom_import_nonce,
                        process_id: processId
                    },
                    success: function (response) {
                        location.reload();
                    },
                    error: function (xhr, status, error) {
                        MeliconSwal.fire({
                            icon: 'error',
                            title: mcTranslations.error,
                            text: error || mcTranslations.default_error_message,
                        });
                    }
                });
            }
        });
    });
    

    function updateProgress() {
        var processId = $('#melicon-process-id-hidden').val();

        if (!processId) {
            return;
        }
        $.ajax({
            url: ajaxurl, // URL de AJAX en WordPress
            method: 'POST',
            data: {
                action: 'melicon_get_process_progress',
                nonce: mcTranslations.get_process_progress_nonce,
                process_id: processId
            },
            success: function (response) {
                if (response.success) {
                    var data = response.data;

                    // Si el proceso está terminado
                    if (data.status === "finished") {
                        $('#melicon-process-progress-bar')
                            .attr('value', 100)
                            .text(100 + '%')
                            .removeClass('is-info')
                            .addClass('is-success'); // Cambiar la clase CSS

                        $('#melicon-process-progress').text(100 + '%');
                        $('#melicon-process-text-title').text(mcTranslations.process_finished);

                        // Recargar la página
                        setTimeout(function () {
                            location.reload();
                        }, 1000); // Esperar un segundo antes de recargar
                    } else {
                        // Actualizar la barra de progreso normalmente
                        $('#melicon-process-progress-bar')
                            .attr('value', data.progress_value)
                            .text(data.progress_value + '%');

                        $('#melicon-process-progress').text(data.progress_value + '%');
                    }

                    // Actualizar otros datos en la UI
                    $('#melicon-process-executed').text(data.executed);
                    $('#melicon-process-total').text(data.total);
                    $('#melicon-process-total-success').text(data.total_success);
                    $('#melicon-process-total-fails').text(data.total_fails);
                    $('#melicon-process-execution-time').text(data.execution_time);
                } else {
                    console.error('Error retrieving process data:', response.data.message);
                }
            }
        });
    }

    setInterval(updateProgress, 5000);


    $('#melicon-import-bulk-actions-form').on('submit', function (e) {
        e.preventDefault();
    
        // Obtener los IDs seleccionados desde localStorage
        let selectedIds = localStorage.getItem('selected-listing-ids');
    
        if (selectedIds) {
            let selectedAction = $('#action-to-do').val();
    
            if (selectedAction === '-1') {
                // Mostrar una alerta si la acción es inválida
                MeliconSwal.fire({
                    icon: 'error',
                    title: mcTranslations.invalid_action,
                    text: mcTranslations.please_select_a_valid_bulk_action,
                });
            } else {
                // Confirmar la acción
                MeliconSwal.fire({
                    icon: 'warning',
                    title: mcTranslations.alert_title_apply_bulk_action,
                    text: mcTranslations.alert_body_apply_bulk_action,
                    showCancelButton: true,
                    confirmButtonText: mcTranslations.confirm,
                    cancelButtonText: mcTranslations.cancel,
                    customClass: {
                        confirmButton: 'melicon-button melicon-is-primary',
                        cancelButton: 'melicon-button melicon-is-secondary'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: ajaxurl,
                            type: 'POST',
                            data: {
                                action: 'melicon_bulk_import_action',
                                action_to_do: selectedAction,
                                meli_listing_ids: selectedIds,
                                nonce: mcTranslations.import_bulk_action_nonce
                            },
                            success: function (response) {
                                if (response.success) {
                                    location.reload();
                                } else {
                                    MeliconSwal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: response.data.message || mcTranslations.default_error_message,
                                    });
                                }
                            },
                            error: function (xhr, status, error) {
                                MeliconSwal.fire({
                                    icon: 'error',
                                    title: mcTranslations.error,
                                    text: error || mcTranslations.default_error_message,
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
                title: mcTranslations.no_items_selected,
                text: mcTranslations.select_items_to_apply_action,
            });
        }
    });
    

    $('.melicon-delete-product-vinculation').on('click', function (e) {
        e.preventDefault();
    
        var productType = $(this).data('product-type');
        var wooProductId = $(this).data('woo-product-id');
        var meliListingId = $(this).data('meli-listing-id');
    
        MeliconSwal.fire({
            icon: 'warning',
            title: mcTranslations.alert_title_desvinculate_product,
            text: mcTranslations.alert_body_desvinculate_product,
            showCancelButton: true,
            confirmButtonText: mcTranslations.confirm,
            cancelButtonText: mcTranslations.cancel,
            customClass: {
                confirmButton: 'melicon-button melicon-is-primary',
                cancelButton: 'melicon-button melicon-is-secondary'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'melicon_desvinculate_woo_product',
                        nonce: mcTranslations.desvinculate_product_nonce,
                        wooProductId: wooProductId,
                        meliListingId: meliListingId,
                    },
                    success: function (response) {
                        location.reload();
                    },
                    error: function (xhr, status, error) {
                        MeliconSwal.fire({
                            icon: 'error',
                            title: mcTranslations.error,
                            text: error || mcTranslations.default_error_message,
                        });
                    }
                });
            }
        });
    });
    

    $('#melicon-apply-match-button').on('click', function (e) {
        e.preventDefault();
    
        var woo_product_id = $('#melicon-match-select-products-select').val();
        var user_listing_id = $("#melicon-match-modal_user-listing-id").val();
    
        if (!woo_product_id || !user_listing_id) {
            MeliconSwal.fire({
                icon: 'error',
                title: mcTranslations.error,
                text: mcTranslations.please_select_a_product_and_a_listing,
            });
            return;
        }
    
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'melicon_apply_match',
                nonce: mcTranslations.apply_match_nonce,
                user_listing_id: user_listing_id,
                woo_product_id: woo_product_id
            },
            success: function (response) {
                location.reload();
            },
            error: function (xhr, status, error) {
                MeliconSwal.fire({
                    icon: 'error',
                    title: mcTranslations.error,
                    text: error || mcTranslations.default_error_message,
                });
            }
        });
    });
    


    $('.melicon-find-product-to-match').on('click', function (e) {
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


        $('#melicon-meli-listing-title-to-match').text(listingTitle);

        // Mostrar el "loading" inicial en la primera columna
        var $listingInfo = $('#melicon-meli-listing-data-to-match');

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

        $("#melicon-match-modal_user-listing-id").val(userListingId);


        $.ajax({
            url: ajaxurl,
            type: 'GET',
            data: {
                action: 'melicon_get_match_available_products',
                nonce: mcTranslations.get_match_available_products_nonce,
                productType: listingType,
            },
            success: function (response) {
                if (response.success) {
                    $('#melicon-match-select-products-select').html(response.data.options);
                    $('#melicon-match-select-products-select').select2({
                        dropdownParent: $("#melicon-find-match-modal")
                    });

                    $('#melicon-match-product-select-container').show();

                    $('#melicon-match-select-products-select').on('select2:select', function (e) {
                        var data = e.params.data;
                        var detailsHtml = `
                            <p><strong>ID:</strong> ${data.id}</p>
                            <p><strong>SKU:</strong> ${data.element.dataset.sku}</p>
                            <p><strong>Type:</strong> ${data.element.dataset.type}</p>
                            <p><strong>Status:</strong> ${data.element.dataset.status}</p>
                            <p><strong>Price:</strong> ${data.element.dataset.price}</p>
                            <p><strong>Stock:</strong> ${data.element.dataset.stock}</p>
                        `;
                        $('#melicon-matched-product-details').html(detailsHtml);
                    });
                    console.log('Products found:', response.data.options);

                } else {
                    console.log('No products found');
                }
            },
            error: function (xhr, status, error) {
                console.log('Error on melicon_get_match_available_products :', error);
            }
        });
    });
});