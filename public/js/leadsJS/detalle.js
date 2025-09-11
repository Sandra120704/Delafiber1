// detalle.js
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
                    $('#tareaForm').off('submit').on('submit', function(e) {
                        e.preventDefault();
                        $.post(`${base_url}/lead/guardarTarea`, $(this).serialize(), function(res) {
                            if (res.success) {
                                $('#listaTareas').append(`<li>${res.tarea.descripcion} <small class="text-muted">${res.tarea.fecha_registro}</small></li>`);
                                $('#tareaForm')[0].reset();
                            } else {
                                Swal.fire('Error', res.message, 'error');
                            }
                        }, 'json');
                    });

                    // ----------------------------
                    // Agregar Seguimientos
                    // ----------------------------
                    $('#seguimientoForm').off('submit').on('submit', function(e) {
                        e.preventDefault();
                        $.post(`${base_url}/lead/guardarSeguimiento`, $(this).serialize(), function(res) {
                            if (res.success) {
                                $('#listaSeguimientos').append(`<li>${res.seguimiento.comentario} <small class="text-muted">${res.seguimiento.fecha}</small></li>`);
                                $('#seguimientoForm')[0].reset();
                            } else {
                                Swal.fire('Error', res.message, 'error');
                            }
                        }, 'json');
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
