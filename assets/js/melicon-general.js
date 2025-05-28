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