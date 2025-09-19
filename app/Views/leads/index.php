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
<!-- SortableJS para Drag & Drop -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

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
        transition: all 0.3s ease;
        cursor: grab;
    }
    
    .kanban-card:active {
        cursor: grabbing;
    }
    
    /* Estilos para drag & drop */
    .sortable-ghost {
        opacity: 0.4;
        background: #f8f9fa;
        transform: rotate(2deg);
    }
    
    .sortable-chosen {
        transform: scale(1.05);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        z-index: 999;
    }
    
    .sortable-drag {
        transform: rotate(3deg);
        opacity: 0.8;
    }
    
    /* Zona de drop activa */
    .kanban-column.drag-over {
        background: linear-gradient(145deg, #e3f2fd, #f3e5f5);
        border: 2px dashed #2196f3;
        transform: scale(1.02);
    }
    
    /* Indicador visual de drop */
    .drop-indicator {
        height: 4px;
        background: linear-gradient(90deg, #4CAF50, #2196F3);
        border-radius: 2px;
        margin: 8px 0;
        opacity: 0;
        animation: pulse 1s infinite;
    }
    
    .drop-indicator.active {
        opacity: 1;
    }
    
    @keyframes pulse {
        0%, 100% { opacity: 0.5; }
        50% { opacity: 1; }
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
    
    /* Estilos para modales mejorados */
    .modal-content {
        border: none;
        border-radius: 15px;
        overflow: hidden;
    }
    
    .modal-header-gradient {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    /* Loading state */
    .loading-card {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: loading 1.5s infinite;
    }
    
    @keyframes loading {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }
    
    /* Mejorar responsividad */
    @media (max-width: 768px) {
        .floating-task-summary {
            position: relative;
            top: auto;
            right: auto;
            margin-bottom: 20px;
        }
        
        .kanban-container {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="p-8 bg-gray-100 min-h-screen font-sans antialiased">
    <!-- Resumen flotante de tareas -->
<!--     <div class="floating-task-summary">
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
    </div> -->

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

    <!-- Contenedor del Kanban con Drag & Drop -->
    <div class="kanban-container grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
      <?php foreach ($etapas as $etapa): ?>
        <div class="kanban-column bg-white rounded-xl shadow-lg p-6 sortable-container" 
             id="kanban-column-<?= $etapa['idetapa'] ?>" 
             data-etapa="<?= $etapa['idetapa'] ?>">
             
            <div class="kanban-stage text-center font-semibold text-lg mb-4 text-gray-700 flex items-center justify-between">
                <span><?= htmlspecialchars($etapa['nombre']) ?></span>
                <span class="bg-gray-200 text-gray-600 text-xs px-2 py-1 rounded-full" id="count-<?= $etapa['idetapa'] ?>">
                    <?= count($leadsPorEtapa[$etapa['idetapa']] ?? []) ?>
                </span>
            </div>
            
            <!-- Indicador de drop -->
            <div class="drop-indicator" id="drop-indicator-<?= $etapa['idetapa'] ?>"></div>
            
            <!-- Container para las cards (aquí van los leads) -->
            <div class="leads-container" id="leads-container-<?= $etapa['idetapa'] ?>">
                <?php
                $leadsEtapa = $leadsPorEtapa[$etapa['idetapa']] ?? [];
                foreach ($leadsEtapa as $lead): 
                ?>
                  <div class="kanban-card bg-white rounded-lg shadow-md mb-4 p-4 cursor-pointer transition-transform transform hover:scale-105 hover:shadow-xl" 
                        id="kanban-card-<?= $lead['idlead'] ?>" 
                        data-id="<?= $lead['idlead'] ?>" 
                        data-etapa="<?= $etapa['idetapa'] ?>"
                        draggable="true" 
                        onclick="abrirDetalleLeadModal(<?= $lead['idlead'] ?>)"
                        style="border-left:5px solid <?= htmlspecialchars($lead['estatus_color'] ?? '#007bff') ?>;">
                    
                    <!-- Badge de tareas pendientes -->
                    <span class="tarea-badge bg-warning text-dark d-none" id="badge-tareas-<?= $lead['idlead'] ?>">0</span>
                    
                    <div class="card-title text-sm font-bold text-gray-900 mb-1">
                        <?= htmlspecialchars($lead['nombres'].' '.$lead['apellidos']) ?>
                    </div>
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
            </div>
            
            <button class="btn btn-sm btn-outline-primary w-full mt-2 text-blue-500 hover:bg-blue-500 hover:text-white transition duration-300 rounded-lg border border-blue-500 py-2" onclick="crearLeadEnEtapa(<?= $etapa['idetapa'] ?>)">
                + Agregar Lead
            </button>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- Modal de Detalle de Lead (MEJORADO) -->
    <div class="modal fade" id="modalLeadDetalle" tabindex="-1" data-bs-backdrop="static">
      <div class="modal-dialog modal-xl">
        <div class="modal-content">
          <div class="modal-header modal-header-gradient">
            <h5 class="modal-title">
              <i class="fas fa-user-circle"></i> Detalle del Lead
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body" id="detalle-lead-content">
            <div class="text-center py-5">
              <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de Creación de Lead (SIN CAMBIOS) -->
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

    <!-- Modal de Tarea Rápida (SIN CAMBIOS IMPORTANTES) -->
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

    <!-- Modal de Tareas del Lead (SIN CAMBIOS) -->
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

// ===============================
// NUEVA FUNCIONALIDAD: DRAG & DROP
// ===============================

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
        url: `${base_url}/leads/cambiarEtapa`,
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

// ===============================
// NUEVA FUNCIONALIDAD: MODAL DE DETALLE MEJORADO
// ===============================

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

// ===============================
// FUNCIONES DE ACCIONES RÁPIDAS
// ===============================

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

// ===============================
// FUNCIONES AUXILIARES MEJORADAS
// ===============================

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

// ===============================
// FUNCIONES ORIGINALES (MANTENIDAS)
// ===============================

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
</script>

<script type="module" src="<?= base_url('js/leadsJS/index.js') ?>"></script>