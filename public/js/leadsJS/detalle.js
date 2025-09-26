// detalle.js
function bindFormAjax(formSelector, url, successCallback) {
    $(formSelector).off('submit').on('submit', function(e) {
        e.preventDefault();
        $.post(url, $(this).serialize(), function(res) {
            if (res.success) {
                successCallback(res);
                $(formSelector)[0].reset();
            } else {
                Swal.fire('Error', res.message, 'error');
            }
        }, 'json');
    });
}

$(document).ready(function() {

    $(document).on('click', '.kanban-card', function() {
        const idlead = $(this).data('id');

        $.ajax({
            url: `${base_url}/lead/detalle/${idlead}`, // corregido
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                if (res.success) {

                    // Inyecta el HTML parcial en el modal
                    $('#modalLeadDetalleContent').html(res.html);

                    // Inicializa y muestra el modal
                    const modal = new bootstrap.Modal(document.getElementById('modalLeadDetalle'));
                    modal.show();

                    // ----------------------------
                    // Desistir Lead
                    // ----------------------------
                    $('#btnDesistirLead').off('click').on('click', function() {
                        $.post(`${base_url}/lead/eliminar`, { idlead: idlead }, function(res) {
                            if (res.success) {
                                Swal.fire('¡Listo!', res.message, 'success');
                                modal.hide();
                                $(`#kanban-card-${idlead}`).remove();
                            } else {
                                Swal.fire('Error', res.message, 'error');
                            }
                        }, 'json');
                    });

                    // ----------------------------
                    // Agregar Tareas
                    // ----------------------------
                    bindFormAjax('#tareaForm', `${base_url}/lead/guardarTarea`, function(res) {
                        $('#listaTareas').append(`<li>${res.tarea.descripcion} <small class="text-muted">${res.tarea.fecha_registro}</small></li>`);
                    });

                    // ----------------------------
                    // Agregar Seguimientos
                    // ----------------------------
                    bindFormAjax('#seguimientoForm', `${base_url}/lead/guardarSeguimiento`, function(res) {
                        $('#listaSeguimientos').append(`<li>${res.seguimiento.comentario} <small class="text-muted">${res.seguimiento.fecha}</small></li>`);
                    });

                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            },
            error: function(xhr) {
                console.error(xhr);
                Swal.fire('Error', 'No se pudo cargar el detalle', 'error');
            }
        });

    });

});
