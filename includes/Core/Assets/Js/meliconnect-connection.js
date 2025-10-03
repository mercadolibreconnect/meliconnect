jQuery(document).ready(function ($) {
    $(".meliconnect-show-listings").on("click", function (e) {
        e.preventDefault();

        var listings = $(this).data("listings");
        if (typeof listings === "string") {
            listings = JSON.parse(listings);
        }

        // Generar lista HTML
        var htmlList = "<ul style='text-align:center'>";
        $.each(listings, function (i, id) {
            htmlList += "<li>" + id + "</li>";
        });
        htmlList += "</ul>";

        MeliconSwal.fire({
            icon: 'info',
            title: listings.length + ' Connected Listings',
            html: htmlList,
            confirmButtonText: meliconnect_translations.close || 'Cerrar',
            customClass: {
                confirmButton: 'meliconnect-button meliconnect-is-primary'
            },
            width: 600
        });
    });
});