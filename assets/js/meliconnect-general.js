//Applied in plugin custom pages and wordpress pages used by plugin 
const MeliconSwal = Swal.mixin({
    customClass: {
        container: 'meliconnect-swal-container',  // Clase personalizada para el contenedor
        popup: 'meliconnect-swal-popup',  // Clase personalizada para el popup
        title: 'meliconnect-swal-title',  // Clase personalizada para el título
        content: 'meliconnect-swal-content',  // Clase personalizada para el contenido
        confirmButton: 'meliconnect-button meliconnect-is-primary',
        cancelButton: 'meliconnect-button meliconnect-is-secondary'
    },

    buttonsStyling: false,  // Desactiva los estilos predeterminados de los botones
});

const Toast = Swal.mixin({
    toast: true,
    position: 'bottom-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    customClass: {
        container: 'meliconnect_custom_toast' // Clase personalizada para ajustar la posición
    },
    didOpen: (toast) => {
        toast.onmouseenter = Swal.stopTimer;
        toast.onmouseleave = Swal.resumeTimer;
    }
});


document.addEventListener('DOMContentLoaded', () => {
    // Funciones para abrir y cerrar un modal
    function openModal($el) {
        $el.classList.add('meliconnect-is-active');
    }

    function closeModal($el) {
        $el.classList.remove('meliconnect-is-active');
    }

    function closeAllModals() {
        (document.querySelectorAll('.meliconnect-modal') || []).forEach(($modal) => {
            closeModal($modal);
        });
    }

    // Agregar evento a los botones que abren modales
    (document.querySelectorAll('.meliconnect-js-modal-trigger') || []).forEach(($trigger) => {
        const modal = $trigger.dataset.target;
        const $target = document.getElementById(modal);

        $trigger.addEventListener('click', () => {
            openModal($target);
        });
    });

    // Agregar evento a los elementos que cierran el modal
    (document.querySelectorAll('.meliconnect-modal-background, .meliconnect-modal-close, .meliconnect-modal-card-head .meliconnect-delete, .meliconnect-modal-card-foot .meliconnect-button') || []).forEach(($close) => {
        const $target = $close.closest('.meliconnect-modal');

        $close.addEventListener('click', () => {
            closeModal($target);
        });
    });

    // Cerrar modal con tecla Escape
    document.addEventListener('keydown', (event) => {
        if (event.key === "Escape") {
            closeAllModals();
        }
    });
});

jQuery(document).ready(function () {
    jQuery('.meliconnect-sidebar-toggle').on('click', function (e) {
        e.preventDefault();
        toggleSidebar();
    });
});

function toggleSidebar() {

    var sidebar = document.getElementById('meliconnect-sidebar');
    var sidebarOverlay = document.getElementById('meliconnect-sidebar-overlay');

    sidebar.classList.toggle('open');
    sidebarOverlay.classList.toggle('open');
}

function dismissMessage(event, notificationId) {
    event.preventDefault();

    jQuery.ajax({
        url: mcTranslations.admin_ajax_url,
        type: 'POST',
        data: {
            action: 'meliconnect_dismiss_message',
            notificationId: notificationId // ID de notificación a eliminar, all en caso de que sean todas
        },
        success: function (response) {
            console.log(response);

            if (response.status) {
                if (response.notification_id == 'all') {
                    jQuery('.meliconnect-sidebar-inner-content-item').remove();
                    jQuery('#meliconnect-total-notifications').hide();
                } else {
                    jQuery('.meliconnect-sidebar-inner-content-item[data-notificationId="' + response.notification_id + '"]').remove();
                    //Se descuenta uno al total de notificaciones
                    jQuery('#meliconnect-total-notifications').text(parseInt(jQuery('#meliconnect-total-notifications').text()) - 1);
                }

            }
        },
        error: function (xhr, status, error) {
            console.log(error);
        }
    });
}