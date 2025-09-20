

$(document).ready(function() {
    console.log('Sistema de detalles de leads iniciado - Base URL:', typeof base_url !== 'undefined' ? base_url : 'NO DEFINIDO');
    

    inicializarEventosDetalle();
});

function inicializarEventosDetalle() {
    $(document).on('click', '.kanban-card', function(e) {
        // Solo si no viene del modal principal de tareas
        if (!$(e.target).closest('.modal').length) {
            const idlead = $(this).data('id');
            cargarDetalleLeadCompleto(idlead);
        }
    });
}

function cargarDetalleLeadCompleto(idlead) {
    $.ajax({
        url: `${base_url}/leads/detalle/${idlead}`,
        type: 'GET',
        dataType: 'json',
        beforeSend: function() {
            // Mostrar loading
            mostrarLoadingDetalle();
        }
    })
    .done(function(response) {
        if (response.success) {
            $('#modalLeadDetalleContent').html(response.html);
            
            const modal = new bootstrap.Modal(document.getElementById('modalLeadDetalle'));
            modal.show();
            
            // Configurar eventos específicos del detalle
            configurarEventosDetalle(idlead);
            
        } else {
            mostrarNotificacionError('Error cargando el detalle del lead');
        }
    })
    .fail(function(xhr) {
        console.error('Error en cargarDetalleLeadCompleto:', xhr);
        mostrarNotificacionError('No se pudo cargar el detalle del lead');
    });
}


function configurarEventosDetalle(idlead) {
    
    $('#btnDesistirLead').off('click').on('click', function() {
        Swal.fire({
            title: '¿Estás seguro?',
            text: 'Esta acción eliminará el lead permanentemente',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                eliminarLead(idlead);
            }
        });
    });


    $('#tareaForm').off('submit').on('submit', function(e) {
        e.preventDefault();
        guardarTareaDetalle(idlead, $(this));
    });

    $('#seguimientoForm').off('submit').on('submit', function(e) {
        e.preventDefault();
        guardarSeguimiento(idlead, $(this));
    });

    $('#selectEtapaDetalle').off('change').on('change', function() {
        const nuevaEtapa = $(this).val();
        cambiarEtapaDetalle(idlead, nuevaEtapa);
    });


    $('#btnEditarLead').off('click').on('click', function() {
        habilitarEdicionLead(idlead);
    });
}


// Eliminar lead
function eliminarLead(idlead) {
    $.post(`${base_url}/leads/eliminar`, { idlead: idlead })
    .done(function(response) {
        if (response.success) {
            Swal.fire('¡Eliminado!', response.message, 'success');
            
            // Cerrar modal y remover card del kanban
            $('#modalLeadDetalle').modal('hide');
            $(`#kanban-card-${idlead}`).fadeOut(300, function() {
                $(this).remove();
                actualizarContadoresEtapas();
            });
            
        } else {
            Swal.fire('Error', response.message, 'error');
        }
    })
    .fail(function() {
        Swal.fire('Error', 'No se pudo eliminar el lead', 'error');
    });
}

// Guardar tarea desde detalle
function guardarTareaDetalle(idlead, formulario) {
    const formData = new FormData(formulario[0]);
    formData.append('idlead', idlead);
    
    $.ajax({
        url: `${base_url}/leads/guardarTarea`,
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false
    })
    .done(function(response) {
        if (response.success) {
            // Agregar tarea a la lista visual
            $('#listaTareas').append(`
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <strong>${response.tarea.titulo}</strong>
                        <br><small class="text-muted">${response.tarea.descripcion}</small>
                        <br><small class="text-info">${formatearFecha(response.tarea.fecha_registro)}</small>
                    </div>
                    <span class="badge bg-${getColorPrioridad(response.tarea.prioridad)} rounded-pill">
                        ${response.tarea.prioridad}
                    </span>
                </li>
            `);
            
            // Limpiar formulario
            formulario[0].reset();
            mostrarNotificacionExito('Tarea agregada exitosamente');
            
        } else {
            Swal.fire('Error', response.message, 'error');
        }
    })
    .fail(function() {
        Swal.fire('Error', 'Error de conexión al guardar la tarea', 'error');
    });
}

// Guardar seguimiento
function guardarSeguimiento(idlead, formulario) {
    const formData = new FormData(formulario[0]);
    formData.append('idlead', idlead);
    
    $.ajax({
        url: `${base_url}/leads/guardarSeguimiento`,
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false
    })
    .done(function(response) {
        if (response.success) {
            // Agregar seguimiento a la lista visual
            $('#listaSeguimientos').append(`
                <li class="list-group-item">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="mb-1">${response.seguimiento.comentario}</p>
                            <small class="text-muted">Por: ${response.seguimiento.usuario}</small>
                        </div>
                        <small class="text-muted">${formatearFecha(response.seguimiento.fecha)}</small>
                    </div>
                </li>
            `);
            
            // Limpiar formulario
            formulario[0].reset();
            mostrarNotificacionExito('Seguimiento agregado exitosamente');
            
        } else {
            Swal.fire('Error', response.message, 'error');
        }
    })
    .fail(function() {
        Swal.fire('Error', 'Error de conexión al guardar el seguimiento', 'error');
    });
}

// Cambiar etapa desde detalle
function cambiarEtapaDetalle(idlead, nuevaEtapa) {
    $.post(`${base_url}/leads/cambiarEtapa`, {
        idlead: idlead,
        nueva_etapa: nuevaEtapa
    })
    .done(function(response) {
        if (response.success) {
            mostrarNotificacionExito('Etapa actualizada exitosamente');
            
            // Mover la card en el kanban si está visible
            const $card = $(`#kanban-card-${idlead}`);
            if ($card.length) {
                $card.fadeOut(300, function() {
                    $(`#leads-container-${nuevaEtapa}`).append($card);
                    $card.fadeIn(300);
                    actualizarContadoresEtapas();
                });
            }
            
        } else {
            Swal.fire('Error', response.message, 'error');
        }
    })
    .fail(function() {
        Swal.fire('Error', 'Error al cambiar la etapa', 'error');
    });
}

// Habilitar edición del lead
function habilitarEdicionLead(idlead) {
    // Convertir campos de texto en inputs editables
    $('.campo-editable').each(function() {
        const valor = $(this).text();
        const campo = $(this).data('campo');
        $(this).html(`<input type="text" class="form-control form-control-sm" data-campo="${campo}" value="${valor}">`);
    });
    
    // Cambiar botón de editar por guardar
    $('#btnEditarLead').hide();
    $('#btnGuardarLead').show();
    
    // Configurar evento de guardar
    $('#btnGuardarLead').off('click').on('click', function() {
        guardarCambiosLead(idlead);
    });
}

// Guardar cambios del lead
function guardarCambiosLead(idlead) {
    const datos = { idlead: idlead };
    
    // Recopilar todos los campos editados
    $('.campo-editable input').each(function() {
        const campo = $(this).data('campo');
        const valor = $(this).val();
        datos[campo] = valor;
    });
    
    $.post(`${base_url}/leads/actualizar`, datos)
    .done(function(response) {
        if (response.success) {
            mostrarNotificacionExito('Lead actualizado exitosamente');
            
            // Revertir campos a modo lectura
            $('.campo-editable input').each(function() {
                const valor = $(this).val();
                $(this).parent().text(valor);
            });
            
            // Restaurar botones
            $('#btnGuardarLead').hide();
            $('#btnEditarLead').show();
            
        } else {
            Swal.fire('Error', response.message, 'error');
        }
    })
    .fail(function() {
        Swal.fire('Error', 'Error al actualizar el lead', 'error');
    });
}

// Mostrar loading en detalle
function mostrarLoadingDetalle() {
    const loadingHtml = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando detalle...</span>
            </div>
            <p class="mt-3 text-muted">Cargando información del lead...</p>
        </div>
    `;
    
    // Si existe un modal de detalle, mostrar loading ahí
    if ($('#modalLeadDetalleContent').length) {
        $('#modalLeadDetalleContent').html(loadingHtml);
        const modal = new bootstrap.Modal(document.getElementById('modalLeadDetalle'));
        modal.show();
    }
}

// Actualizar contadores de etapas
function actualizarContadoresEtapas() {
    $('.kanban-column').each(function() {
        const etapaId = $(this).data('etapa');
        const cantidad = $(this).find('.kanban-card').length;
        $(`#count-${etapaId}`).text(cantidad);
    });
}

// Mostrar notificaciones
function mostrarNotificacionExito(mensaje) {
    Swal.fire({
        icon: 'success',
        title: '¡Éxito!',
        text: mensaje,
        timer: 2000,
        showConfirmButton: false,
        toast: true,
        position: 'top-end'
        // Sonido desactivado por solicitud del usuario
    });
}

function mostrarNotificacionError(mensaje) {
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: mensaje,
        timer: 3000,
        showConfirmButton: false,
        toast: true,
        position: 'top-end'
        // Sin sonido - comentado para que no sea molesto
    });
}

// Obtener color según prioridad
function getColorPrioridad(prioridad) {
    const colores = {
        'urgente': 'danger',
        'alta': 'warning',
        'media': 'info',
        'baja': 'secondary'
    };
    return colores[prioridad] || 'secondary';
}

// Formatear fecha
function formatearFecha(fecha) {
    return new Date(fecha).toLocaleString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}