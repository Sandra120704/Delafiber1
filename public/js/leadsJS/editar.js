$(document).ready(function() {

    // -------------------------------
    // Función para abrir modal de nuevo Lead
    // -------------------------------
    window.abrirModalAgregarLead = function(idetapa){
        $('#idetapa').val(idetapa);
        const modal = new bootstrap.Modal(document.getElementById('leadModal'));
        modal.show();
    }

    // -------------------------------
    // Guardar Lead vía AJAX
    // -------------------------------
    $('#leadForm').on('submit', function(e){
        e.preventDefault();

        const formData = $(this).serialize();
        const nombres = $('#nombres').val();
        const apellidos = $('#apellidos').val();
        const telefono = $('#telefono').val();
        const correo = $('#correo').val();
        const idetapa = $('#idetapa').val();

        $.post(`${base_url}/lead/guardar`, formData, function(res){
            if(res.success){

                // Cerrar modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('leadModal'));
                modal.hide();

                // Crear tarjeta nueva
                const column = $('#kanban-column-' + idetapa);
                const leadCard = $(`
                    <div class="kanban-card" id="kanban-card-${res.idlead}" data-id="${res.idlead}" draggable="true" style="border-left:5px solid #007bff;">
                        <div class="card-title">${nombres} ${apellidos}</div>
                        <div class="card-info">
                            <small>${telefono} | ${correo}</small>
                        </div>
                    </div>
                `);

                column.append(leadCard);

                // Reactivar drag & drop
                enableDragAndDrop();

                // Limpiar formulario
                $('#leadForm')[0].reset();

                Swal.fire({
                    icon: 'success',
                    title: 'Lead registrado',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                });

            } else {
                Swal.fire('Error', res.message, 'error');
            }
        }, "json");
    });

    // -------------------------------
    // Abrir modal detalle Lead
    // -------------------------------
    $(document).on('click', '.kanban-card', function() {
        const idlead = $(this).data('id');

        $.ajax({
            url: `${base_url}/lead/detalle/${idlead}`,
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                if(res.success){
                    $('#modalLeadDetalleContent').html(res.html);
                    const modal = new bootstrap.Modal(document.getElementById('modalLeadDetalle'));
                    modal.show();

                    // -------------------------------
                    // Eliminar Lead desde modal
                    // -------------------------------
                    $('#btnDesistirLead').off('click').on('click', function() {
                        $.post(`${base_url}/lead/eliminar`, { idlead: idlead }, function(res){
                            if(res.success){
                                Swal.fire('¡Listo!', res.message, 'success');
                                modal.hide();
                                $(`#kanban-card-${idlead}`).remove();
                            } else {
                                Swal.fire('Error', res.message, 'error');
                            }
                        }, 'json');
                    });

                    // -------------------------------
                    // Guardar Tarea
                    // -------------------------------
                    $('#tareaForm').off('submit').on('submit', function(e){
                        e.preventDefault();
                        $.post(`${base_url}/lead/guardarTarea`, $(this).serialize(), function(res){
                            if(res.success){
                                $('#listaTareas').append(`<li>${res.tarea.descripcion} <small class="text-muted">${res.tarea.fecha_registro}</small></li>`);
                                $('#tareaForm')[0].reset();
                            } else {
                                Swal.fire('Error', res.message, 'error');
                            }
                        }, 'json');
                    });

                    // -------------------------------
                    // Guardar Seguimiento
                    // -------------------------------
                    $('#seguimientoForm').off('submit').on('submit', function(e){
                        e.preventDefault();
                        $.post(`${base_url}/lead/guardarSeguimiento`, $(this).serialize(), function(res){
                            if(res.success){
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
            error: function(xhr){
                console.error(xhr);
                Swal.fire('Error', 'No se pudo cargar el detalle', 'error');
            }
        });
    });

});
