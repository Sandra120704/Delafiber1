<?= $header ?>
<!-- Hoja de estilos de Bootstrap 5 -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Estilos personalizados para el tablero -->
<link rel="stylesheet" href="<?= base_url('css/leads.css') ?>">
<!-- Estilos para SweetAlert2 -->
<link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-bootstrap-4/bootstrap-4.min.css" rel="stylesheet">
<!-- Tailwind CSS -->
<script src="https://cdn.tailwindcss.com"></script>
<!-- Font Awesome -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

<style>
    .tarea-badge {
        position: absolute;
        top: -5px;
        right: -5px;
        min-width: 18px;
        height: 18px;
        border-radius: 50%;
        font-size: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .kanban-card {
        position: relative;
    }
    .tarea-indicador {
        font-size: 11px;
        padding: 2px 6px;
        border-radius: 10px;
        margin: 2px 0;
    }
    .lead-actions {
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .kanban-card:hover .lead-actions {
        opacity: 1;
    }
    .floating-task-summary {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 1050;
        min-width: 300px;
    }
</style>

<div class="p-8 bg-gray-100 min-h-screen font-sans antialiased">
    <!-- Resumen flotante de tareas -->
    <div class="floating-task-summary">
        <div class="card border-0 shadow-lg">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0"><i class="fas fa-tasks"></i> Resumen de Tareas</h6>
            </div>
            <div class="card-body p-3">
                <div class="row text-center">
                    <div class="col-3">
                        <div class="text-warning">
                            <i class="fas fa-clock"></i>
                            <div class="fw-bold" id="tareas-hoy">0</div>
                            <small>Hoy</small>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="text-danger">
                            <i class="fas fa-exclamation"></i>
                            <div class="fw-bold" id="tareas-vencidas">0</div>
                            <small>Vencidas</small>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="text-info">
                            <i class="fas fa-calendar"></i>
                            <div class="fw-bold" id="tareas-semana">0</div>
                            <small>Semana</small>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="text-success">
                            <i class="fas fa-check"></i>
                            <div class="fw-bold" id="tareas-completadas">0</div>
                            <small>Hechas</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cabecera del tablero -->
    <div class="flex justify-between items-center mb-8">
        <h3 class="text-3xl font-bold text-gray-800">Flujo de Leads con Tareas</h3>
        <div class="flex gap-2">
            <button class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg shadow-xl transition duration-300 transform hover:scale-105" onclick="abrirModalTarea()">
                <i class="fas fa-plus"></i> Nueva Tarea
            </button>
            <button class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg shadow-xl transition duration-300 transform hover:scale-105" data-bs-toggle="modal" data-bs-target="#leadModal">
                <i class="fas fa-user-plus"></i> Nuevo Lead
            </button>
            <button class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 px-6 rounded-lg shadow-xl transition duration-300 transform hover:scale-105" onclick="verTableroTareas()">
                <i class="fas fa-list"></i> Ver Tareas
            </button>
        </div>
    </div>

    <!-- Contenedor del Kanban -->
    <div class="kanban-container grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
      <?php foreach ($etapas as $etapa): ?>
        <div class="kanban-column bg-white rounded-xl shadow-lg p-6" id="kanban-column-<?= $etapa['idetapa'] ?>" data-etapa="<?= $etapa['idetapa'] ?>">
            <div class="kanban-stage text-center font-semibold text-lg mb-4 text-gray-700"><?= htmlspecialchars($etapa['nombre']) ?></div>
            <?php
              $leadsEtapa = $leadsPorEtapa[$etapa['idetapa']] ?? [];
            ?>
            <?php foreach ($leadsEtapa as $lead): ?>
              <div class="kanban-card bg-white rounded-lg shadow-md mb-4 p-4 cursor-pointer transition-transform transform hover:scale-105 hover:shadow-xl" 
                    id="kanban-card-<?= $lead['idlead'] ?>" 
                    data-id="<?= $lead['idlead'] ?>" 
                    draggable="true" 
                    style="border-left:5px solid <?= htmlspecialchars($lead['estatus_color'] ?? '#007bff') ?>;">
                
                <!-- Badge de tareas pendientes -->
                <span class="tarea-badge bg-warning text-dark d-none" id="badge-tareas-<?= $lead['idlead'] ?>">0</span>
                
                <div class="card-title text-sm font-bold text-gray-900 mb-1"><?= htmlspecialchars($lead['nombres'].' '.$lead['apellidos']) ?></div>
                <div class="card-info text-xs text-gray-500">
                  <small class="block truncate"><?= htmlspecialchars($lead['telefono']) ?> | <?= htmlspecialchars($lead['correo']) ?></small>
                  <small class="block truncate"><?= htmlspecialchars($lead['campania'] ?? '') ?> - <?= htmlspecialchars($lead['medio'] ?? '') ?></small>
                  <small class="block truncate">Usuario: <?= htmlspecialchars($lead['usuario'] ?? 'Sin asignar') ?></small>
                </div>

                <!-- Indicadores de tareas -->
                <div class="mt-2" id="tareas-info-<?= $lead['idlead'] ?>">
                    <!-- Se llena dinámicamente con JS -->
                </div>

                <!-- Acciones rápidas -->
                <div class="lead-actions mt-3 d-flex gap-1">
                    <button class="btn btn-sm btn-outline-primary flex-1" onclick="event.stopPropagation(); crearTareaRapida(<?= $lead['idlead'] ?>, '<?= htmlspecialchars($lead['nombres'].' '.$lead['apellidos']) ?>')">
                        <i class="fas fa-plus"></i> Tarea
                    </button>
                    <button class="btn btn-sm btn-outline-info" onclick="event.stopPropagation(); verTareasLead(<?= $lead['idlead'] ?>)">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-success" onclick="event.stopPropagation(); completarTareaPendiente(<?= $lead['idlead'] ?>)">
                        <i class="fas fa-check"></i>
                    </button>
                </div>
              </div>
            <?php endforeach; ?>
            
            <button class="btn btn-sm btn-outline-primary w-full mt-2 text-blue-500 hover:bg-blue-500 hover:text-white transition duration-300 rounded-lg border border-blue-500 py-2" onclick="crearLeadEnEtapa(<?= $etapa['idetapa'] ?>)">
                + Agregar Lead
            </button>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- Modal de Detalle (el contenido se carga con JS) -->
    <div class="modal fade" id="modalLeadDetalle" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content rounded-xl shadow-2xl"></div>
      </div>
    </div>

    <!-- Modal de Creación de Lead -->
    <div class="modal fade" id="leadModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
      <div class="modal-dialog modal-lg">
        <div class="modal-content rounded-xl shadow-2xl border-t-4 border-blue-500">
          <div class="modal-header border-b border-gray-200 p-4 flex justify-between items-center">
            <h5 class="modal-title text-xl font-bold text-gray-800">Registrar Lead</h5>
            <button type="button" class="btn-close text-gray-400 hover:text-gray-600 transition-colors" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body p-6">
            <form id="leadForm">
              <input type="hidden" id="idpersona" name="idpersona" value="">
              <input type="hidden" id="idetapa" name="idetapa" value="1">
    
              <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">DNI:</label>
                <input type="text" id="dni" class="form-control mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="DNI">
              </div>
              <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Nombres:</label>
                <input type="text" id="nombres" name="nombres" class="form-control mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
              </div>
              <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Apellidos:</label>
                <input type="text" id="apellidos" name="apellidos" class="form-control mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
              </div>
              <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Teléfono:</label>
                <input type="text" id="telefono" name="telefono" class="form-control mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
              </div>
              <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Correo:</label>
                <input type="email" id="correo" name="correo" class="form-control mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
              </div>
              
              <div class="modal-footer border-t border-gray-200 p-4 flex justify-end gap-2">
                <button type="button" class="btn btn-secondary rounded-md shadow-sm px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 transition-colors" data-bs-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn btn-success rounded-md shadow-sm px-4 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 transition-colors">Registrar Lead</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de Tarea Rápida -->
    <div class="modal fade" id="modalTareaRapida" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content border-0 shadow-2xl">
          <div class="modal-header bg-success text-white">
            <h5 class="modal-title">
              <i class="fas fa-plus"></i> Nueva Tarea
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <form id="formTareaRapida">
              <input type="hidden" id="tarea-idlead" name="idlead">
              <div class="mb-3">
                <label class="form-label fw-bold">Lead:</label>
                <div id="tarea-lead-nombre" class="form-control-plaintext fw-bold text-primary"></div>
              </div>
              <div class="mb-3">
                <label class="form-label fw-bold">Tipo de Tarea:</label>
                <select class="form-select" name="tipo_tarea" required>
                  <option value="llamada">📞 Llamada</option>
                  <option value="whatsapp">💬 WhatsApp</option>
                  <option value="email">📧 Email</option>
                  <option value="visita">🏠 Visita</option>
                  <option value="reunion">👥 Reunión</option>
                  <option value="seguimiento">👁️ Seguimiento</option>
                  <option value="documentacion">📋 Documentación</option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label fw-bold">Título:</label>
                <input type="text" class="form-control" name="titulo" required>
              </div>
              <div class="mb-3">
                <label class="form-label fw-bold">Descripción:</label>
                <textarea class="form-control" name="descripcion" rows="2"></textarea>
              </div>
              <div class="row">
                <div class="col-6">
                  <label class="form-label fw-bold">Prioridad:</label>
                  <select class="form-select" name="prioridad">
                    <option value="baja">Baja</option>
                    <option value="media" selected>Media</option>
                    <option value="alta">Alta</option>
                    <option value="urgente">Urgente</option>
                  </select>
                </div>
                <div class="col-6">
                  <label class="form-label fw-bold">Vencimiento:</label>
                  <select class="form-select" id="select-vencimiento" onchange="manejarVencimiento()">
                    <option value="1h">En 1 hora</option>
                    <option value="3h">En 3 horas</option>
                    <option value="1d" selected>Mañana</option>
                    <option value="3d">En 3 días</option>
                    <option value="custom">Personalizado</option>
                  </select>
                  <input type="datetime-local" class="form-control mt-2 d-none" name="fecha_vencimiento" id="fecha-personalizada">
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-success" onclick="guardarTareaRapida()">
              <i class="fas fa-save"></i> Crear Tarea
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de Tareas del Lead -->
    <div class="modal fade" id="modalTareasLead" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-2xl">
          <div class="modal-header bg-info text-white">
            <h5 class="modal-title">
              <i class="fas fa-tasks"></i> Tareas del Lead
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div id="contenido-tareas-lead">
              <!-- Se carga dinámicamente -->
            </div>
          </div>
        </div>
      </div>
    </div>
</div>

<?= $footer ?>

<script>
    const base_url = "<?= rtrim(base_url(), '/') ?>";
</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Variables globales
let tareasData = {};

// Cargar datos al inicializar
$(document).ready(function() {
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
    
    $('#modalTareaRapida').modal('show');
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
            Swal.fire({
                icon: 'success',
                title: '¡Tarea creada!',
                text: 'La tarea se creó exitosamente',
                timer: 2000,
                showConfirmButton: false
            });
            
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
                    Swal.fire({
                        icon: 'success',
                        title: '¡Completada!',
                        timer: 1500,
                        showConfirmButton: false
                    });
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
    // Redirigir a página de tareas o abrir modal más completo
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
</script>

<script type="module" src="<?= base_url('js/leadsJS/index.js') ?>"></script>