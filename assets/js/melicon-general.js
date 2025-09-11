//Applied in plugin custom pages and wordpress pages used by plugin 
const MeliconSwal = Swal.mixin({
    customClass: {
        container: 'melicon-swal-container',  // Clase personalizada para el contenedor
        popup: 'melicon-swal-popup',  // Clase personalizada para el popup
        title: 'melicon-swal-title',  // Clase personalizada para el título
        content: 'melicon-swal-content',  // Clase personalizada para el contenido
        confirmButton: 'melicon-button melicon-is-primary',
        cancelButton: 'melicon-button melicon-is-secondary'
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
        container: 'melicon_custom_toast' // Clase personalizada para ajustar la posición
    },
    didOpen: (toast) => {
        toast.onmouseenter = Swal.stopTimer;
        toast.onmouseleave = Swal.resumeTimer;
    }
});


document.addEventListener('DOMContentLoaded', () => {
    // Funciones para abrir y cerrar un modal
    function openModal($el) {
        $el.classList.add('melicon-is-active');
    }

    function closeModal($el) {
        $el.classList.remove('melicon-is-active');
    }

    function closeAllModals() {
        (document.querySelectorAll('.melicon-modal') || []).forEach(($modal) => {
            closeModal($modal);
        });
    }

    // Agregar evento a los botones que abren modales
    (document.querySelectorAll('.melicon-js-modal-trigger') || []).forEach(($trigger) => {
        const modal = $trigger.dataset.target;
        const $target = document.getElementById(modal);

        $trigger.addEventListener('click', () => {
            openModal($target);
        });
    });

    // Agregar evento a los elementos que cierran el modal
    (document.querySelectorAll('.melicon-modal-background, .melicon-modal-close, .melicon-modal-card-head .melicon-delete, .melicon-modal-card-foot .melicon-button') || []).forEach(($close) => {
        const $target = $close.closest('.melicon-modal');

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
    jQuery('.melicon-sidebar-toggle').on('click', function (e) {
        e.preventDefault();
        toggleSidebar();
    });
});

function toggleSidebar() {

    var sidebar = document.getElementById('melicon-sidebar');
    var sidebarOverlay = document.getElementById('melicon-sidebar-overlay');

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
                    jQuery('.melicon-sidebar-inner-content-item').remove();
                    jQuery('#melicon-total-notifications').hide();
                } else {
                    jQuery('.melicon-sidebar-inner-content-item[data-notificationId="' + response.notification_id + '"]').remove();
                    //Se descuenta uno al total de notificaciones
                    jQuery('#melicon-total-notifications').text(parseInt(jQuery('#melicon-total-notifications').text()) - 1);
                }

            }
        },
        error: function (xhr, status, error) {
            console.log(error);
        }
    });
}