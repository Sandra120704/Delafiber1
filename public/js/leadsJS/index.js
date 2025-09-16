// Base URL global
const base_url = typeof base_url !== 'undefined' ? base_url : '';

// ---------------------------------------------
// Inicialización: Drag & Drop
// ---------------------------------------------
function enableDragAndDrop() {
    $('.kanban-card').on('dragstart', function(e) {
        e.originalEvent.dataTransfer.setData('text/plain', $(this).data('id'));
    });

    $('.kanban-column').on('dragover', function(e) {
        e.preventDefault();
    });

    $('.kanban-column').on('drop', function(e) {
        const id = e.originalEvent.dataTransfer.getData('text/plain');
        const $card = $(`#kanban-card-${id}`);
        $(this).append($card);

        const idetapa = $(this).data('etapa');

        $.post(`${base_url}/lead/actualizarEtapa`, { idlead: id, idetapa }, function(res){
            Swal.fire({
                icon: res.success ? 'success' : 'error',
                title: res.message,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 1500,
                timerProgressBar: true
            });
        }, "json");
    });
}

// ---------------------------------------------
// Abrir modal para nuevo Lead (por etapa)
// ---------------------------------------------
window.abrirModalAgregarLead = function(idetapa){
    $('#idetapa').val(idetapa);
    const modal = new bootstrap.Modal(document.getElementById('leadModal'));
    modal.show();
}

// ---------------------------------------------
// Guardar Lead desde formulario
// ---------------------------------------------
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

            // Agregar tarjeta al tablero
            const column = $(`#kanban-column-${idetapa}`);
            const leadCard = $(`
                <div class="kanban-card bg-white rounded-lg shadow-md mb-4 p-4 cursor-pointer transition-transform transform hover:scale-105 hover:shadow-xl"
                     id="kanban-card-${res.idlead}"
                     data-id="${res.idlead}"
                     draggable="true"
                     style="border-left:5px solid #007bff;">
                    <div class="card-title text-sm font-bold text-gray-900 mb-1">${nombres} ${apellidos}</div>
                    <div class="card-info text-xs text-gray-500">
                        <small class="block truncate">${telefono} | ${correo}</small>
                    </div>
                </div>
            `);

            column.append(leadCard);
            enableDragAndDrop(); // Reactivar drag & drop

            $('#leadForm')[0].reset(); // Limpiar formulario

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
    }, 'json');
});

// ---------------------------------------------
// Detalle del Lead en modal
// ---------------------------------------------
$(document).on('click', '.kanban-card', function() {
    const idlead = $(this).data('id');

    $.ajax({
        url: `${base_url}/lead/detalle/${idlead}`,
        type: 'GET',
        dataType: 'json',
        success: function(res) {
            if(res.success){
                $('#modalLeadDetalle .modal-content').html(res.html);
                const modal = new bootstrap.Modal(document.getElementById('modalLeadDetalle'));
                modal.show();

                // Eliminar Lead
                $('#btnDesistirLead').off('click').on('click', function() {
                    $.post(`${base_url}/lead/eliminar`, { idlead }, function(res){
                        if(res.success){
                            Swal.fire('¡Listo!', res.message, 'success');
                            modal.hide();
                            $(`#kanban-card-${idlead}`).remove();
                        } else {
                            Swal.fire('Error', res.message, 'error');
                        }
                    }, 'json');
                });

                // Guardar tarea
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

                // Guardar seguimiento
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

// ---------------------------------------------
// Inicialización al cargar
// ---------------------------------------------
$(document).ready(function(){
    enableDragAndDrop();
});
