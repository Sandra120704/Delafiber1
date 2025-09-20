// Variables globales
let tareasData = {};
let sortableInstances = [];

// Cargar datos al inicializar
$(document).ready(function() {
    cargarResumenTareas();
    cargarTareasLeads();
    inicializarDragDrop();
    
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

function inicializarDragDrop() {
    // Inicializar SortableJS para cada columna
    document.querySelectorAll('.leads-container').forEach(container => {
        const etapaId = container.closest('.kanban-column').dataset.etapa;
        
        const sortable = new Sortable(container, {
            group: 'kanban-leads', // Permite mover entre columnas
            animation: 300,
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            dragClass: 'sortable-drag',
            
            // Eventos del drag & drop
            onStart: function(evt) {
                // Agregar clase visual a todas las columnas
                document.querySelectorAll('.kanban-column').forEach(col => {
                    if (col !== evt.from.closest('.kanban-column')) {
                        col.classList.add('drag-over');
                    }
                });
            },
            
            onEnd: function(evt) {
                // Remover clases visuales
                document.querySelectorAll('.kanban-column').forEach(col => {
                    col.classList.remove('drag-over');
                });
                
                // Si cambió de columna, actualizar la base de datos
                const itemEl = evt.item;
                const newEtapa = evt.to.closest('.kanban-column').dataset.etapa;
                const oldEtapa = evt.from.closest('.kanban-column').dataset.etapa;
                
                if (newEtapa !== oldEtapa) {
                    const leadId = itemEl.dataset.id;
                    moverLeadEtapa(leadId, newEtapa, oldEtapa);
                }
            },
            
            onMove: function(evt) {
                // Mostrar indicador de drop
                const targetColumn = evt.to.closest('.kanban-column');
                const indicator = targetColumn.querySelector('.drop-indicator');
                if (indicator) {
                    indicator.classList.add('active');
                }
            }
        });
        
        sortableInstances.push(sortable);
    });
}

// Función para mover lead entre etapas
function moverLeadEtapa(leadId, nuevaEtapa, etapaAnterior) {
    // Mostrar loading en la card
    const card = document.getElementById(`kanban-card-${leadId}`);
    if (card) {
        card.style.opacity = '0.6';
        card.style.pointerEvents = 'none';
    }
    
    $.ajax({
        url: `${base_url}/`,
        method: 'POST',
        data: {
            idlead: leadId,
            nueva_etapa: nuevaEtapa,
            etapa_anterior: etapaAnterior
        }
    })
    .done(function(response) {
        if (response.success) {
            // Actualizar datos de la card
            if (card) {
                card.dataset.etapa = nuevaEtapa;
                card.style.opacity = '1';
                card.style.pointerEvents = 'auto';
            }
            
            // Actualizar contadores
            actualizarContadoresEtapas();
            
            // Mostrar notificación
            mostrarNotificacionExito('Lead movido exitosamente');
            
            // Registrar actividad (opcional)
            registrarActividad(leadId, 'moved', `Movido a ${getNombreEtapa(nuevaEtapa)}`);
            
        } else {
            // Revertir el movimiento si falla
            revertirMovimiento(leadId, etapaAnterior);
            mostrarNotificacionError(response.message || 'Error al mover el lead');
        }
    })
    .fail(function() {
        // Revertir el movimiento si falla la conexión
        revertirMovimiento(leadId, etapaAnterior);
        mostrarNotificacionError('Error de conexión');
    });
}

// Revertir movimiento en caso de error
function revertirMovimiento(leadId, etapaAnterior) {
    const card = document.getElementById(`kanban-card-${leadId}`);
    const contenedorAnterior = document.getElementById(`leads-container-${etapaAnterior}`);
    
    if (card && contenedorAnterior) {
        contenedorAnterior.appendChild(card);
        card.style.opacity = '1';
        card.style.pointerEvents = 'auto';
    }
}

// Actualizar contadores de las etapas
function actualizarContadoresEtapas() {
    document.querySelectorAll('.kanban-column').forEach(columna => {
        const etapaId = columna.dataset.etapa;
        const contador = columna.querySelector(`#count-${etapaId}`);
        const numLeads = columna.querySelectorAll('.kanban-card').length;
        
        if (contador) {
            contador.textContent = numLeads;
            
            // Animación del contador
            contador.classList.add('animate-pulse');
            setTimeout(() => {
                contador.classList.remove('animate-pulse');
            }, 1000);
        }
    });
}


function abrirDetalleLeadModal(leadId) {
    const modal = new bootstrap.Modal(document.getElementById('modalLeadDetalle'));
    
    // Mostrar modal inmediatamente con loading
    modal.show();
    
    // Cargar contenido del lead
    $.ajax({
        url: `${base_url}/leads/detalle/${leadId}`,
        method: 'GET'
    })
    .done(function(response) {
        if (response.success) {
            mostrarDetalleLeadContent(response.lead, response.tareas, response.actividades);
        } else {
            $('#detalle-lead-content').html(`
                <div class="alert alert-danger text-center">
                    <i class="fas fa-exclamation-triangle"></i>
                    Error cargando el detalle del lead
                </div>
            `);
        }
    })
    .fail(function() {
        $('#detalle-lead-content').html(`
            <div class="alert alert-danger text-center">
                <i class="fas fa-wifi"></i>
                Error de conexión
            </div>
        `);
    });
}

function mostrarDetalleLeadContent(lead, tareas = [], actividades = []) {
    const tareasPendientes = tareas.filter(t => ['pendiente', 'en_progreso'].includes(t.estado));
    const tareasCompletadas = tareas.filter(t => t.estado === 'completada');
    
    const html = `
        <div class="container-fluid">
            <div class="row">
                <!-- Información Principal -->
                <div class="col-md-8">
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-user"></i> Información Personal</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Nombre:</strong> ${lead.nombres} ${lead.apellidos}</p>
                                    <p><strong>DNI:</strong> ${lead.dni || 'No especificado'}</p>
                                    <p><strong>Teléfono:</strong> ${lead.telefono}</p>
                                    <p><strong>Email:</strong> ${lead.correo}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Campaña:</strong> ${lead.campania || 'No especificada'}</p>
                                    <p><strong>Medio:</strong> ${lead.medio || 'No especificado'}</p>
                                    <p><strong>Usuario Asignado:</strong> ${lead.usuario || 'Sin asignar'}</p>
                                    <p><strong>Fecha de Registro:</strong> ${formatearFecha(lead.fecha_registro)}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Timeline de Actividades -->
                    <div class="card">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <h6 class="mb-0"><i class="fas fa-history"></i> Timeline de Actividades</h6>
                            <button class="btn btn-sm btn-primary" onclick="agregarActividad(${lead.idlead})">
                                <i class="fas fa-plus"></i> Nueva Actividad
                            </button>
                        </div>
                        <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                            ${actividades.length > 0 ? renderTimeline(actividades) : '<p class="text-muted text-center">No hay actividades registradas</p>'}
                        </div>
                    </div>
                </div>
                
                <!-- Panel Lateral - Tareas -->
                <div class="col-md-4">
                    <!-- Resumen de Tareas -->
                    <div class="card mb-3">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0"><i class="fas fa-tasks"></i> Resumen de Tareas</h6>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-6">
                                    <div class="text-warning">
                                        <i class="fas fa-clock fa-2x"></i>
                                        <div class="fw-bold">${tareasPendientes.length}</div>
                                        <small>Pendientes</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-success">
                                        <i class="fas fa-check-circle fa-2x"></i>
                                        <div class="fw-bold">${tareasCompletadas.length}</div>
                                        <small>Completadas</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tareas Pendientes -->
                    <div class="card mb-3">
                        <div class="card-header bg-warning text-white d-flex justify-content-between align-items-center">
                            <h6 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Tareas Pendientes</h6>
                            <button class="btn btn-sm btn-light" onclick="crearTareaRapida(${lead.idlead}, '${lead.nombres} ${lead.apellidos}')">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                        <div class="card-body p-2" style="max-height: 300px; overflow-y: auto;">
                            ${tareasPendientes.length > 0 ? renderTareasPendientes(tareasPendientes) : '<p class="text-muted small text-center">No hay tareas pendientes</p>'}
                        </div>
                    </div>
                    
                    <!-- Acciones Rápidas -->
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0"><i class="fas fa-bolt"></i> Acciones Rápidas</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <button class="btn btn-outline-primary btn-sm" onclick="llamarLead('${lead.telefono}')">
                                    <i class="fas fa-phone"></i> Llamar
                                </button>
                                <button class="btn btn-outline-success btn-sm" onclick="whatsappLead('${lead.telefono}')">
                                    <i class="fab fa-whatsapp"></i> WhatsApp
                                </button>
                                <button class="btn btn-outline-info btn-sm" onclick="emailLead('${lead.correo}')">
                                    <i class="fas fa-envelope"></i> Email
                                </button>
                                <button class="btn btn-outline-secondary btn-sm" onclick="editarLead(${lead.idlead})">
                                    <i class="fas fa-edit"></i> Editar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    $('#detalle-lead-content').html(html);
}

function renderTimeline(actividades) {
    return actividades.map(actividad => `
        <div class="timeline-item mb-3">
            <div class="d-flex align-items-start">
                <div class="timeline-icon me-3">
                    <i class="fas fa-${getIconoActividad(actividad.tipo)} text-primary"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-start mb-1">
                        <strong class="text-sm">${actividad.titulo}</strong>
                        <small class="text-muted">${formatearFechaCorta(actividad.fecha)}</small>
                    </div>
                    <p class="text-muted small mb-1">${actividad.descripcion || ''}</p>
                    <small class="text-muted">Por: ${actividad.usuario}</small>
                </div>
            </div>
        </div>
    `).join('');
}

function renderTareasPendientes(tareas) {
    return tareas.map(tarea => {
        const esVencida = new Date(tarea.fecha_vencimiento) < new Date();
        const colorClass = esVencida ? 'border-danger bg-light' : '';
        
        return `
            <div class="card mb-2 ${colorClass}">
                <div class="card-body p-2">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h6 class="mb-1 text-sm">${tarea.titulo}</h6>
                            <small class="text-muted">
                                <i class="fas fa-${getIconoTipo(tarea.tipo_tarea)}"></i>
                                ${formatearFecha(tarea.fecha_vencimiento)}
                            </small>
                        </div>
                        <div class="d-flex gap-1">
                            <span class="badge bg-${getColorPrioridad(tarea.prioridad)} badge-sm">${tarea.prioridad}</span>
                            <button class="btn btn-success btn-xs" onclick="completarTareaModal(${tarea.idtarea})" title="Completar">
                                <i class="fas fa-check"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }).join('');
}

function llamarLead(telefono) {
    window.open(`tel:${telefono}`, '_self');
    // Registrar la actividad
    registrarActividad(null, 'llamada', `Llamada realizada a ${telefono}`);
}

function whatsappLead(telefono) {
    const mensaje = encodeURIComponent('Hola, me comunico desde [tu empresa] para darle seguimiento a su consulta.');
    window.open(`https://wa.me/${telefono.replace(/[^0-9]/g, '')}?text=${mensaje}`, '_blank');
    registrarActividad(null, 'whatsapp', `WhatsApp enviado a ${telefono}`);
}

function emailLead(email) {
    window.open(`mailto:${email}`, '_self');
    registrarActividad(null, 'email', `Email enviado a ${email}`);
}

function editarLead(leadId) {
    // Cerrar modal actual y abrir modal de edición
    $('#modalLeadDetalle').modal('hide');
    // Aquí cargarías los datos en el formulario de edición
    setTimeout(() => {
        $('#leadModal').modal('show');
        cargarDatosParaEdicion(leadId);
    }, 300);
}

function agregarActividad(leadId) {
    Swal.fire({
        title: 'Nueva Actividad',
        html: `
            <div class="text-start">
                <div class="mb-3">
                    <label class="form-label">Tipo:</label>
                    <select class="form-select" id="tipo-actividad">
                        <option value="llamada">📞 Llamada</option>
                        <option value="reunion">🤝 Reunión</option>
                        <option value="email">📧 Email</option>
                        <option value="whatsapp">💬 WhatsApp</option>
                        <option value="nota">📝 Nota</option>
                        <option value="seguimiento">👁️ Seguimiento</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Título:</label>
                    <input type="text" class="form-control" id="titulo-actividad" placeholder="Resumen de la actividad">
                </div>
                <div class="mb-3">
                    <label class="form-label">Descripción:</label>
                    <textarea class="form-control" id="desc-actividad" rows="3" placeholder="Detalles de lo realizado..."></textarea>
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Guardar Actividad',
        cancelButtonText: 'Cancelar',
        preConfirm: () => {
            const tipo = document.getElementById('tipo-actividad').value;
            const titulo = document.getElementById('titulo-actividad').value;
            const descripcion = document.getElementById('desc-actividad').value;
            
            if (!titulo.trim()) {
                Swal.showValidationMessage('El título es requerido');
                return false;
            }
            
            return { tipo, titulo, descripcion };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            guardarActividad(leadId, result.value);
        }
    });
}

function guardarActividad(leadId, actividad) {
    $.ajax({
        url: `${base_url}/leads/agregarActividad`,
        method: 'POST',
        data: {
            idlead: leadId,
            tipo: actividad.tipo,
            titulo: actividad.titulo,
            descripcion: actividad.descripcion
        }
    })
    .done(function(response) {
        if (response.success) {
            // Recargar el detalle del lead
            abrirDetalleLeadModal(leadId);
            mostrarNotificacionExito('Actividad agregada exitosamente');
        } else {
            mostrarNotificacionError('Error al guardar la actividad');
        }
    })
    .fail(function() {
        mostrarNotificacionError('Error de conexión');
    });
}


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

function registrarActividad(leadId, tipo, descripcion) {
    if (!leadId) return; // Si no hay leadId, no registrar
    
    $.post(`${base_url}/leads/registrarActividad`, {
        idlead: leadId,
        tipo: tipo,
        descripcion: descripcion
    });
}

function getNombreEtapa(etapaId) {
    const etapas = {
        '1': 'Nuevo',
        '2': 'Contactado',
        '3': 'Calificado',
        '4': 'Propuesta',
        '5': 'Cerrado'
    };
    return etapas[etapaId] || 'Etapa ' + etapaId;
}

function getIconoActividad(tipo) {
    const iconos = {
        'llamada': 'phone',
        'reunion': 'handshake',
        'email': 'envelope',
        'whatsapp': 'comment-dots',
        'nota': 'sticky-note',
        'seguimiento': 'eye',
        'moved': 'arrows-alt'
    };
    return iconos[tipo] || 'circle';
}


// Cargar resumen de tareas
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

// Cargar tareas de todos los leads
function cargarTareasLeads() {
    $('.kanban-card').each(function() {
        const idlead = $(this).data('id');
        cargarTareasLead(idlead);
    });
}

// Cargar tareas de un lead específico
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

// Mostrar información de tareas en la card del lead
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

// Abrir modal de tarea rápida
function crearTareaRapida(idlead, nombreLead) {
    $('#tarea-idlead').val(idlead);
    $('#tarea-lead-nombre').text(nombreLead);
    
    // Limpiar formulario
    $('#formTareaRapida')[0].reset();
    $('#tarea-idlead').val(idlead); // Restaurar después del reset
    
    // Cerrar modal de detalle si está abierto
    $('#modalLeadDetalle').modal('hide');
    
    setTimeout(() => {
        $('#modalTareaRapida').modal('show');
    }, 300);
}

// Guardar tarea rápida
function guardarTareaRapida() {
    // Calcular fecha de vencimiento según selección
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
    
    // Actualizar campo de fecha
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
            
            // Recargar tareas del lead
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

// Ver todas las tareas de un lead
function verTareasLead(idlead) {
    $.get(`${base_url}/tareas/obtenerTareasPorLead/${idlead}`)
        .done(function(response) {
            if (response.success) {
                mostrarTareasEnModal(response.tareas);
                $('#modalTareasLead').modal('show');
            }
        });
}

// Mostrar tareas en modal
function mostrarTareasEnModal(tareas) {
    let html = '';
    
    if (tareas.length === 0) {
        html = '<div class="text-center text-muted"><i class="fas fa-inbox fa-3x mb-3"></i><p>No hay tareas para este lead</p></div>';
    } else {
        // Agrupar por estado
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

// Completar tarea pendiente más próxima
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

// Completar tarea rápida
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

// Completar tarea desde modal
function completarTareaModal(idtarea) {
    completarTareaRapida(idtarea, 'tarea');
}

// Manejar cambio de vencimiento
function manejarVencimiento() {
    const valor = $('#select-vencimiento').val();
    if (valor === 'custom') {
        $('#fecha-personalizada').removeClass('d-none');
    } else {
        $('#fecha-personalizada').addClass('d-none');
    }
}

// Abrir modal de nueva tarea general
function abrirModalTarea() {
    window.open(`${base_url}/tareas`, '_blank');
}

// Ver tablero completo de tareas
function verTableroTareas() {
    window.open(`${base_url}/tareas`, '_blank');
}

// Crear lead en etapa específica
function crearLeadEnEtapa(idetapa) {
    $('#idetapa').val(idetapa);
    $('#leadModal').modal('show');
}

// Funciones auxiliares
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