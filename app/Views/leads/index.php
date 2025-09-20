<?= $header ?>
<!-- Hoja de estilos de Bootstrap 5 -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="<?= base_url('css/leads.css') ?>">
<link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-bootstrap-4/bootstrap-4.min.css" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

<div class="p-8 bg-gray-100 min-h-screen font-sans antialiased">
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
    <div class="kanban-container">
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


<script>
    const base_url = "<?= rtrim(base_url(), '/') ?>";
    console.log('Base URL configurada:', base_url);
</script>

<!-- Librerías externas PRIMERO -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Scripts especializados DESPUÉS -->
<script src="<?= base_url('js/leadsJS/kanban.js') ?>"></script>
<script src="<?= base_url('js/leadsJS/tareas.js') ?>"></script>
<script src="<?= base_url('js/leadsJS/detalles.js') ?>"></script>

<!-- Debug y test automático -->
<script>
$(document).ready(function() {
    console.log('Test automático del sistema...');
    
    // Test después de que todo esté cargado
    setTimeout(function() {
        if (typeof window.diagnosticarKanban === 'function') {
            window.diagnosticarKanban();
        }
        
        // Auto-reparar si es necesario
        if (typeof window.repararDragAndDrop === 'function') {
            const cards = document.querySelectorAll('.kanban-card');
            if (cards.length > 0 && window.sortableInstances?.length === 0) {
                console.log('Auto-reparando drag & drop...');
                window.repararDragAndDrop();
            }
        }
    }, 2000);
});
</script>

<?= $footer ?>