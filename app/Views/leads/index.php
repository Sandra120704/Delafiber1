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
        <div>
            <h3 class="text-3xl font-bold text-gray-800">Gestión de Leads</h3>
            <p class="text-gray-600 mt-1">Haz clic en cualquier lead para gestionar tareas y detalles</p>
        </div>
        <div class="flex gap-2">
            <button class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg shadow-xl transition duration-300 transform hover:scale-105" onclick="window.location.href='<?= base_url('personas/crear') ?>'">
                <i class="fas fa-user-plus"></i> Nuevo Lead
            </button>
            <button class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 px-6 rounded-lg shadow-xl transition duration-300 transform hover:scale-105" onclick="verTableroTareas()">
                <i class="fas fa-calendar-alt"></i> Calendario Tareas
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
                  <div class="kanban-card bg-white rounded-lg shadow-md mb-4 p-4 cursor-pointer transition-all duration-300 hover:shadow-xl hover:transform hover:scale-[1.02] border-l-4" 
                        id="kanban-card-<?= $lead['idlead'] ?>" 
                        data-id="<?= $lead['idlead'] ?>" 
                        data-etapa="<?= $etapa['idetapa'] ?>"
                        draggable="true" 
                        onclick="abrirDetalleLeadModal(<?= $lead['idlead'] ?>)"
                        style="border-left-color: <?= htmlspecialchars($lead['estatus_color'] ?? '#007bff') ?>;">
                    
                    <!-- Indicador de tareas pendientes (flotante) -->
                    <div class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-6 h-6 flex items-center justify-center font-bold shadow-lg d-none" id="badge-tareas-<?= $lead['idlead'] ?>">
                        0
                    </div>
                    
                    <!-- Información principal del lead -->
                    <div class="lead-header mb-3">
                        <div class="card-title text-sm font-bold text-gray-900 mb-1 flex items-center justify-between">
                            <span><?= htmlspecialchars($lead['nombres'].' '.$lead['apellidos']) ?></span>
                            <div class="flex items-center space-x-1">
                                <span class="w-2 h-2 rounded-full bg-green-400" title="Lead activo"></span>
                            </div>
                        </div>
                        
                        <div class="card-info text-xs text-gray-600 space-y-1">
                            <div class="flex items-center">
                                <i class="fas fa-phone text-blue-500 w-4"></i>
                                <span class="ml-1"><?= htmlspecialchars($lead['telefono']) ?></span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-envelope text-green-500 w-4"></i>
                                <span class="ml-1 truncate"><?= htmlspecialchars($lead['correo']) ?></span>
                            </div>
                            <?php if (!empty($lead['campania'])): ?>
                            <div class="flex items-center">
                                <i class="fas fa-bullhorn text-purple-500 w-4"></i>
                                <span class="ml-1 text-xs"><?= htmlspecialchars($lead['campania']) ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Información de tareas (se actualiza dinámicamente) -->
                    <div class="tareas-preview border-t pt-2 mt-2" id="tareas-info-<?= $lead['idlead'] ?>">
                        <div class="flex items-center justify-between text-xs text-gray-500">
                            <span id="tareas-resumen-<?= $lead['idlead'] ?>">📋 Clic para ver tareas</span>
                            <span class="text-blue-500">→ Gestionar</span>
                        </div>
                    </div>

                    <!-- Acciones rápidas ocultas por defecto, se muestran al hover -->
                    <div class="lead-actions mt-2 opacity-0 transition-opacity duration-300 flex gap-1" onmouseenter="this.style.opacity='1'" onmouseleave="this.style.opacity='0'">
                        <button class="btn btn-sm btn-outline-success flex-1 text-xs py-1" onclick="event.stopPropagation(); abrirDetalleLeadModal(<?= $lead['idlead'] ?>)" title="Ver detalles y tareas">
                            <i class="fas fa-tasks"></i> Tareas
                        </button>
                        <button class="btn btn-sm btn-outline-info text-xs py-1" onclick="event.stopPropagation(); marcarComoContactado(<?= $lead['idlead'] ?>)" title="Marcar como contactado">
                            <i class="fas fa-phone"></i>
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

    <!-- Modal de Detalle de Lead MEJORADO con Gestión de Tareas Integrada -->
    <div class="modal fade" id="modalLeadDetalle" tabindex="-1" data-bs-backdrop="static">
      <div class="modal-dialog modal-xl">
        <div class="modal-content border-0 shadow-2xl">
          <div class="modal-header bg-gradient-to-r from-blue-600 to-purple-600 text-white">
            <div class="d-flex align-items-center">
              <i class="fas fa-user-circle me-2"></i>
              <div>
                <h5 class="modal-title mb-0">Gestión Completa del Lead</h5>
                <small id="lead-etapa-badge" class="badge bg-light text-dark">Cargando...</small>
              </div>
            </div>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          
          <div class="modal-body p-0">
            <!-- Tabs de navegación -->
            <ul class="nav nav-tabs border-bottom-0" id="leadTabs" role="tablist">
              <li class="nav-item" role="presentation">
                <button class="nav-link active fw-bold" id="info-tab" data-bs-toggle="tab" data-bs-target="#info-panel" type="button">
                  <i class="fas fa-info-circle"></i> Información
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold" id="tareas-tab" data-bs-toggle="tab" data-bs-target="#tareas-panel" type="button">
                  <i class="fas fa-tasks"></i> Tareas (<span id="contador-tareas">0</span>)
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold" id="seguimientos-tab" data-bs-toggle="tab" data-bs-target="#seguimientos-panel" type="button">
                  <i class="fas fa-comments"></i> Seguimientos
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold" id="historial-tab" data-bs-toggle="tab" data-bs-target="#historial-panel" type="button">
                  <i class="fas fa-history"></i> Historial
                </button>
              </li>
            </ul>

            <!-- Contenido de los tabs -->
            <div class="tab-content p-4" id="leadTabContent">
              <!-- Panel de Información -->
              <div class="tab-pane fade show active" id="info-panel" role="tabpanel">
                <div id="detalle-lead-content">
                  <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                      <span class="visually-hidden">Cargando...</span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Panel de Tareas MEJORADO -->
              <div class="tab-pane fade" id="tareas-panel" role="tabpanel">
                <!-- Formulario de nueva tarea inline -->
                <div class="card border-success mb-4">
                  <div class="card-header bg-success bg-opacity-10 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 text-success fw-bold">
                      <i class="fas fa-plus-circle"></i> Nueva Tarea
                    </h6>
                    <button class="btn btn-sm btn-success" onclick="contraerFormularioTarea()" id="btn-contraer-tarea">
                      <i class="fas fa-minus"></i>
                    </button>
                  </div>
                  <div class="card-body" id="form-nueva-tarea">
                    <form id="tareaFormInline">
                      <input type="hidden" id="tarea-idlead-inline" name="idlead">
                      <div class="row g-3">
                        <div class="col-md-8">
                          <label class="form-label fw-bold">Descripción de la tarea:</label>
                          <input type="text" 
                                 class="form-control form-control-sm" 
                                 name="descripcion" 
                                 placeholder="Ej: Llamar para confirmar disponibilidad y agendar visita..." 
                                 maxlength="1000"
                                 required
                                 oninput="actualizarContadorCaracteres(this)">
                          <small class="form-text text-muted">
                            <span id="contador-caracteres">0</span>/1000 caracteres
                          </small>
                        </div>
                        <div class="col-md-4">
                          <label class="form-label fw-bold">Fecha/Hora:</label>
                          <input type="datetime-local" class="form-control form-control-sm" name="fecha_inicio" required>
                        </div>
                        <div class="col-md-6">
                          <label class="form-label fw-bold">Tipo:</label>
                          <select class="form-select form-select-sm" name="tipo">
                            <option value="llamada">📞 Llamada</option>
                            <option value="whatsapp">💬 WhatsApp</option>
                            <option value="email">📧 Email</option>
                            <option value="visita">🏠 Visita</option>
                            <option value="reunion">👥 Reunión</option>
                            <option value="seguimiento" selected>👁️ Seguimiento</option>
                          </select>
                        </div>
                        <div class="col-md-6">
                          <label class="form-label fw-bold">Prioridad:</label>
                          <select class="form-select form-select-sm" name="prioridad">
                            <option value="baja">🟢 Baja</option>
                            <option value="media" selected>🟡 Media</option>
                            <option value="alta">🟠 Alta</option>
                            <option value="urgente">🔴 Urgente</option>
                          </select>
                        </div>
                        <div class="col-12 d-flex gap-2 justify-content-end">
                          <button type="button" class="btn btn-sm btn-secondary" onclick="limpiarFormularioTarea()">
                            <i class="fas fa-eraser"></i> Limpiar
                          </button>
                          <button type="submit" class="btn btn-sm btn-success">
                            <i class="fas fa-save"></i> Crear Tarea
                          </button>
                        </div>
                      </div>
                    </form>
                  </div>
                </div>

                <!-- Lista de tareas -->
                <div class="card">
                  <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold">
                      <i class="fas fa-list"></i> Tareas del Lead
                    </h6>
                    <div class="d-flex gap-1">
                      <button class="btn btn-sm btn-outline-primary" onclick="filtrarTareas('pendiente')">Pendientes</button>
                      <button class="btn btn-sm btn-outline-success" onclick="filtrarTareas('completada')">Completadas</button>
                      <button class="btn btn-sm btn-outline-secondary" onclick="filtrarTareas('todas')">Todas</button>
                    </div>
                  </div>
                  <div class="card-body">
                    <div id="lista-tareas-lead" class="tareas-container">
                      <div class="text-center text-muted py-3">
                        <i class="fas fa-tasks fa-2x mb-2"></i>
                        <p>No hay tareas aún. ¡Crea la primera!</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Panel de Seguimientos -->
              <div class="tab-pane fade" id="seguimientos-panel" role="tabpanel">
                <div id="seguimientos-content">
                  <div class="text-center py-5">
                    <div class="spinner-border text-info" role="status">
                      <span class="visually-hidden">Cargando seguimientos...</span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Panel de Historial -->
              <div class="tab-pane fade" id="historial-panel" role="tabpanel">
                <div class="timeline">
                  <div class="text-center text-muted py-4">
                    <i class="fas fa-history fa-2x mb-2"></i>
                    <p>Historial de actividades se cargará aquí</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <div class="modal-footer border-top bg-light">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
              <i class="fas fa-times"></i> Cerrar
            </button>
            <button type="button" class="btn btn-info" onclick="exportarDatosLead()">
              <i class="fas fa-download"></i> Exportar
            </button>
            <button type="button" class="btn btn-warning" onclick="enviarResumenLead()">
              <i class="fas fa-share"></i> Compartir
            </button>
          </div>
        </div>
      </div>
    </div>

</div>


<script>
    const base_url = "<?= rtrim(base_url(), '/') ?>";
    console.log('Base URL configurada:', base_url);
    
    // Función global para el contador de caracteres
    function actualizarContadorCaracteres(input) {
        const contador = document.getElementById('contador-caracteres');
        if (!contador) return;
        
        const longitud = input.value.length;
        contador.textContent = longitud;
        
        // Cambiar color según la longitud (sin validación estricta de mínimo)
        if (longitud === 0) {
            contador.className = 'text-muted';
            input.classList.remove('is-invalid', 'is-valid');
        } else if (longitud > 0 && longitud <= 1000) {
            contador.className = 'text-success fw-bold';
            input.classList.remove('is-invalid');
            input.classList.add('is-valid');
        } else {
            contador.className = 'text-danger fw-bold';
            input.classList.add('is-invalid');
            input.classList.remove('is-valid');
        }
    }
</script>

<!-- Librerías externas PRIMERO -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- ÚNICO ARCHIVO CONSOLIDADO - Reemplaza todos los otros -->
    <!-- Sistema de leads -->
    <script src="<?= base_url('js/leadsJS/leads.js') ?>"></script>

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