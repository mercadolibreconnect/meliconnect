
const MeliconSwal = Swal.mixin({
    customClass: {
        container: 'melicon-swal-container',  // Clase personalizada para el contenedor
        popup: 'melicon-swal-popup',  // Clase personalizada para el popup
        title: 'melicon-swal-title',  // Clase personalizada para el título
        content: 'melicon-swal-content',  // Clase personalizada para el contenido
        confirmButton: 'melicon-swal-confirm-button',  // Clase personalizada para el botón de confirmación
        cancelButton: 'melicon-swal-cancel-button'  // Clase personalizada para el botón de cancelación (opcional)
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