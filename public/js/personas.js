$(document).ready(function () {

    // Cargar el formulario al presionar "Nueva Persona"
    $("#btnNuevaPersona").on("click", function () {
        $.get("<?= base_url('persona/form') ?>", function (data) {
            $("#contenido-persona").html(data);
        });
    });

    // Delegación para volver a la lista
    $("#contenido-persona").on("click", "#btnVolverLista", function () {
        location.reload(); // recarga la página para mostrar la lista
    });

});