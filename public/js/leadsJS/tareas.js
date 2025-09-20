
let tareasData = {};

$(document).ready(function() {
    console.log('Sistema de tareas iniciado - Base URL:', typeof base_url !== 'undefined' ? base_url : 'NO DEFINIDO');
    
    cargarResumenTareas();
    cargarTareasLeads();
    
    // Auto-refresh cada 3 minutos
    setInterval(() => {
        cargarResumenTareas();
        cargarTareasLeads();
    }, 180000);
    
    // Configurar fecha por defecto para mañana
    const mañana = new Date();
    mañana.setDate(mañana.getDate() + 1);
    mañana.setHours(9, 0, 0, 0);
    $('#fecha-personalizada').val(mañana.toISOString().slice(0, 16));
});

function cargarResumenTareas() {
    $.get(`${base_url}/tareas/resumen`)
        .done(function(data) {
            $('#tareas-hoy').text(data.pendientes_hoy || 0);
            $('#tareas-vencidas').text(data.vencidas || 0);
            $('#tareas-semana').text(data.total_semana || 0);
            $('#tareas-completadas').text(data.completadas_hoy || 0);
        })
        .fail(function() {
            console.log('Error cargando resumen de tareas');
        });
}

function cargarTareasLeads() {
    $('.kanban-card').each(function() {
        const idlead = $(this).data('id');
        cargarTareasLead(idlead);
    });
}

function cargarTareasLead(idlead) {
    $.get(`${base_url}/tareas/obtenerTareasPorLead/${idlead}`)
        .done(function(response) {
            if (response.success) {
                mostrarInfoTareas(idlead, response.tareas);
            }
        })
        .fail(function() {
            console.log(`Error cargando tareas del lead ${idlead}`);
        });
}

function mostrarInfoTareas(idlead, tareas) {
    const pendientes = tareas.filter(t => ['pendiente', 'en_progreso'].includes(t.estado));
    const vencidas = pendientes.filter(t => new Date(t.fecha_vencimiento) < new Date());
    const proximaTarea = pendientes.sort((a, b) => new Date(a.fecha_vencimiento) - new Date(b.fecha_vencimiento))[0];
    
    // Badge de tareas pendientes
    const badge = $(`#badge-tareas-${idlead}`);
    if (pendientes.length > 0) {
        badge.text(pendientes.length).removeClass('d-none');
        if (vencidas.length > 0) {
            badge.removeClass('bg-warning').addClass('bg-danger text-white');
        }
    } else {
        badge.addClass('d-none');
    }
    
    // Información de tareas en la card
    let infoHtml = '';
    if (proximaTarea) {
        const esVencida = new Date(proximaTarea.fecha_vencimiento) < new Date();
        const colorClase = esVencida ? 'bg-danger text-white' : getColorPrioridad(proximaTarea.prioridad);
        
        infoHtml = `
            <div class="tarea-indicador ${colorClase}">
                <i class="fas fa-${getIconoTipo(proximaTarea.tipo_tarea)}"></i>
                ${proximaTarea.titulo}
                <small class="d-block">${formatearFechaCorta(proximaTarea.fecha_vencimiento)}</small>
            </div>
        `;
    }
    
    $(`#tareas-info-${idlead}`).html(infoHtml);
}

function crearTareaRapida(idlead, nombreLead) {
    $('#tarea-idlead').val(idlead);
    $('#tarea-lead-nombre').text(nombreLead);
    
    $('#formTareaRapida')[0].reset();
    $('#tarea-idlead').val(idlead);
    
    $('#modalLeadDetalle').modal('hide');
    
    setTimeout(() => {
        $('#modalTareaRapida').modal('show');
    }, 300);
}

function guardarTareaRapida() {
    const vencimiento = $('#select-vencimiento').val();
    let fechaVencimiento = new Date();
    
    switch(vencimiento) {
        case '1h':
            fechaVencimiento.setHours(fechaVencimiento.getHours() + 1);
            break;
        case '3h':
            fechaVencimiento.setHours(fechaVencimiento.getHours() + 3);
            break;
        case '1d':
            fechaVencimiento.setDate(fechaVencimiento.getDate() + 1);
            fechaVencimiento.setHours(9, 0, 0, 0);
            break;
        case '3d':
            fechaVencimiento.setDate(fechaVencimiento.getDate() + 3);
            fechaVencimiento.setHours(9, 0, 0, 0);
            break;
        case 'custom':
            fechaVencimiento = new Date($('#fecha-personalizada').val());
            break;
    }
    
    if (vencimiento !== 'custom') {
        $('input[name="fecha_vencimiento"]').val(fechaVencimiento.toISOString().slice(0, 16));
    }
    
    const formData = new FormData($('#formTareaRapida')[0]);
    
    $.ajax({
        url: `${base_url}/tareas/crear`,
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false
    })
    .done(function(response) {
        if (response.success) {
            $('#modalTareaRapida').modal('hide');
            mostrarNotificacionExito('Tarea creada exitosamente');
            
            const idlead = $('#tarea-idlead').val();
            cargarTareasLead(idlead);
            cargarResumenTareas();
        } else {
            Swal.fire('Error', response.message || 'No se pudo crear la tarea', 'error');
        }
    })
    .fail(function() {
        Swal.fire('Error', 'Error de conexión', 'error');
    });
}

function verTareasLead(idlead) {
    $.get(`${base_url}/tareas/obtenerTareasPorLead/${idlead}`)
        .done(function(response) {
            if (response.success) {
                mostrarTareasEnModal(response.tareas);
                $('#modalTareasLead').modal('show');
            }
        });
}

function mostrarTareasEnModal(tareas) {
    let html = '';
    
    if (tareas.length === 0) {
        html = '<div class="text-center text-muted"><i class="fas fa-inbox fa-3x mb-3"></i><p>No hay tareas para este lead</p></div>';
    } else {
        const pendientes = tareas.filter(t => ['pendiente', 'en_progreso'].includes(t.estado));
        const completadas = tareas.filter(t => t.estado === 'completada');
        
        if (pendientes.length > 0) {
            html += '<h6 class="text-warning mb-3"><i class="fas fa-clock"></i> Tareas Pendientes</h6>';
            pendientes.forEach(tarea => {
                const esVencida = new Date(tarea.fecha_vencimiento) < new Date();
                html += `
                    <div class="card mb-2 ${esVencida ? 'border-danger' : ''}">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">${tarea.titulo}</h6>
                                    <p class="mb-1 text-muted small">${tarea.descripcion || 'Sin descripción'}</p>
                                    <small class="text-muted">
                                        <i class="fas fa-${getIconoTipo(tarea.tipo_tarea)}"></i> ${tarea.tipo_tarea} - 
                                        ${formatearFecha(tarea.fecha_vencimiento)}
                                    </small>
                                </div>
                                <div class="d-flex gap-1">
                                    <span class="badge bg-${getColorPrioridad(tarea.prioridad)}">${tarea.prioridad}</span>
                                    <button class="btn btn-success btn-sm" onclick="completarTareaModal(${tarea.idtarea})">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
        }
        
        if (completadas.length > 0) {
            html += '<hr><h6 class="text-success mb-3"><i class="fas fa-check-circle"></i> Completadas</h6>';
            completadas.slice(0, 3).forEach(tarea => {
                html += `
                    <div class="card mb-2 bg-light">
                        <div class="card-body p-2">
                            <small class="text-muted">
                                <i class="fas fa-check text-success"></i> ${tarea.titulo} - 
                                ${formatearFecha(tarea.fecha_completado)}
                            </small>
                        </div>
                    </div>
                `;
            });
        }
    }
    
    $('#contenido-tareas-lead').html(html);
}

function completarTareaPendiente(idlead) {
    $.get(`${base_url}/tareas/obtenerTareasPorLead/${idlead}`)
        .done(function(response) {
            if (response.success) {
                const pendientes = response.tareas.filter(t => ['pendiente', 'en_progreso'].includes(t.estado));
                if (pendientes.length > 0) {
                    const proximaTarea = pendientes.sort((a, b) => new Date(a.fecha_vencimiento) - new Date(b.fecha_vencimiento))[0];
                    completarTareaRapida(proximaTarea.idtarea, proximaTarea.titulo);
                } else {
                    Swal.fire('Info', 'No hay tareas pendientes para este lead', 'info');
                }
            }
        });
}

function completarTareaRapida(idtarea, titulo) {
    Swal.fire({
        title: `¿Completar "${titulo}"?`,
        input: 'textarea',
        inputLabel: 'Notas del resultado:',
        inputPlaceholder: 'Describe qué se logró...',
        showCancelButton: true,
        confirmButtonText: 'Completar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.post(`${base_url}/tareas/completar/${idtarea}`, {
                notas_resultado: result.value
            })
            .done(function(response) {
                if (response.success) {
                    mostrarNotificacionExito('Tarea completada exitosamente');
                    cargarTareasLeads();
                    cargarResumenTareas();
                }
            });
        }
    });
}

function completarTareaModal(idtarea) {
    completarTareaRapida(idtarea, 'tarea');
}

function manejarVencimiento() {
    const valor = $('#select-vencimiento').val();
    if (valor === 'custom') {
        $('#fecha-personalizada').removeClass('d-none');
    } else {
        $('#fecha-personalizada').addClass('d-none');
    }
}

// Crear lead en etapa específica
function crearLeadEnEtapa(idetapa) {
    $('#idetapa').val(idetapa);
    $('#leadModal').modal('show');
}


// Abrir modal de nueva tarea general
function abrirModalTarea() {
    window.open(`${base_url}/tareas`, '_blank');
}

// Ver tablero completo de tareas
function verTableroTareas() {
    window.open(`${base_url}/tareas`, '_blank');
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
    });
}

// Funciones auxiliares para tareas
function getColorPrioridad(prioridad) {
    const colores = {
        'urgente': 'danger',
        'alta': 'warning',
        'media': 'info',
        'baja': 'secondary'
    };
    return colores[prioridad] || 'secondary';
}

function getIconoTipo(tipo) {
    const iconos = {
        'llamada': 'phone',
        'visita': 'map-marker-alt',
        'email': 'envelope',
        'whatsapp': 'comments',
        'reunion': 'users',
        'seguimiento': 'eye',
        'documentacion': 'file-alt',
        'otro': 'tasks'
    };
    return iconos[tipo] || 'tasks';
}

function formatearFecha(fecha) {
    return new Date(fecha).toLocaleString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function formatearFechaCorta(fecha) {
    const ahora = new Date();
    const fechaTarea = new Date(fecha);
    const diffDias = Math.ceil((fechaTarea - ahora) / (1000 * 60 * 60 * 24));
    
    if (diffDias === 0) return 'Hoy';
    if (diffDias === 1) return 'Mañana';
    if (diffDias === -1) return 'Ayer';
    if (diffDias < 0) return `${Math.abs(diffDias)}d atrás`;
    return `${diffDias}d`;
}