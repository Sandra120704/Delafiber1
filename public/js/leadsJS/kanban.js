
// Variables globales del Kanban
let sortableInstances = [];

$(document).ready(function() {
    console.log('Kanban iniciado - Base URL:', typeof base_url !== 'undefined' ? base_url : 'NO DEFINIDO');
    inicializarKanban();
});

function inicializarKanban() {
    // Inicializar SortableJS para drag & drop avanzado
    document.querySelectorAll('.leads-container').forEach(container => {
        const etapaId = container.closest('.kanban-column').dataset.etapa;
        
        const sortable = new Sortable(container, {
            group: 'kanban-leads',
            animation: 300,
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            dragClass: 'sortable-drag',
            
            onStart: function(evt) {
                // Efecto visual al iniciar drag
                document.querySelectorAll('.kanban-column').forEach(col => {
                    if (col !== evt.from.closest('.kanban-column')) {
                        col.classList.add('drag-over');
                    }
                });
            },
            
            onEnd: function(evt) {
                // Limpiar efectos visuales
                document.querySelectorAll('.kanban-column').forEach(col => {
                    col.classList.remove('drag-over');
                });
                
                // Procesar movimiento si cambió de etapa
                const itemEl = evt.item;
                const newEtapa = evt.to.closest('.kanban-column').dataset.etapa;
                const oldEtapa = evt.from.closest('.kanban-column').dataset.etapa;
                
                console.log('Drag ended:', {
                    item: itemEl.id,
                    newEtapa: newEtapa,
                    oldEtapa: oldEtapa,
                    changed: newEtapa !== oldEtapa
                });
                
                if (newEtapa !== oldEtapa) {
                    const leadId = itemEl.dataset.id;
                    moverLeadAEtapa(leadId, newEtapa, oldEtapa);
                }
            }
        });
        
        sortableInstances.push(sortable);
    });
}

function moverLeadAEtapa(leadId, nuevaEtapa, etapaAnterior) {
    console.log('moverLeadAEtapa:', {leadId, nuevaEtapa, etapaAnterior});
    
    // Validaciones
    if (!leadId || !nuevaEtapa || !etapaAnterior) {
        mostrarNotificacion('error', 'Datos insuficientes para mover el lead');
        return;
    }

    if (typeof base_url === 'undefined') {
        mostrarNotificacion('error', 'Error de configuración: base_url no definido');
        return;
    }

    const card = document.getElementById(`kanban-card-${leadId}`);
    if (!card) {
        mostrarNotificacion('error', 'Card del lead no encontrada');
        return;
    }

    // Estado de loading
    card.style.opacity = '0.6';
    card.style.pointerEvents = 'none';
    card.classList.add('moving');

    // URL con fallback
    let fullUrl = `${base_url}/leads/moverEtapa`;
    if (base_url === '' || base_url === '/') {
        fullUrl = '/delafiber/leads/moverEtapa';
    }

    console.log(`Enviando POST a: ${fullUrl}`);

    $.ajax({
        url: fullUrl,
        method: 'POST',
        data: {
            idlead: leadId,
            nueva_etapa: nuevaEtapa,
            etapa_anterior: etapaAnterior
        },
        timeout: 8000
    })
    .done(function(response) {
        console.log('Respuesta del servidor:', response);
        
        if (response.success) {
            // Actualizar estado de la card
            card.dataset.etapa = nuevaEtapa;
            card.style.opacity = '1';
            card.style.pointerEvents = 'auto';
            card.classList.remove('moving');
            
            actualizarContadores();
            mostrarNotificacion('success', 'Lead movido exitosamente');
        } else {
            console.error('Error del servidor:', response.message);
            revertirMovimiento(leadId, etapaAnterior);
            mostrarNotificacion('error', response.message || 'Error al mover el lead');
        }
    })
    .fail(function(xhr, status, error) {
        console.error('Error de conexión:', {xhr, status, error});
        revertirMovimiento(leadId, etapaAnterior);
        
        let mensaje = 'Error de conexión';
        if (status === 'timeout') mensaje = 'La operación tardó demasiado';
        else if (xhr.status === 404) mensaje = 'Endpoint no encontrado';
        else if (xhr.status === 500) mensaje = 'Error interno del servidor';
        
        mostrarNotificacion('error', mensaje);
    });
}

function revertirMovimiento(leadId, etapaAnterior) {
    console.log(`Revirtiendo movimiento del lead ${leadId} a etapa ${etapaAnterior}`);
    
    const card = document.getElementById(`kanban-card-${leadId}`);
    const contenedorAnterior = document.getElementById(`leads-container-${etapaAnterior}`);
    
    if (card && contenedorAnterior) {
        card.style.opacity = '1';
        card.style.pointerEvents = 'auto';
        card.classList.remove('moving');
        contenedorAnterior.appendChild(card);
        setTimeout(actualizarContadores, 100);
    } else {
        console.error('No se pudo revertir el movimiento - elementos no encontrados');
    }
}

function abrirModalNuevoLead(idetapa) {
    $('#idetapa').val(idetapa);
    const modal = new bootstrap.Modal(document.getElementById('leadModal'));
    modal.show();
}

// Guardar nuevo lead
$('#leadForm').on('submit', function(e) {
    e.preventDefault();
    
    const formData = $(this).serialize();
    const idetapa = $('#idetapa').val();
    
    $.post(`${base_url}/leads/guardar`, formData, function(response) {
        if (response.success) {
            const modal = bootstrap.Modal.getInstance(document.getElementById('leadModal'));
            modal.hide();
            
            // Agregar nueva card al tablero
            agregarNuevaCardAlTablero(response.lead, idetapa);
            
            $('#leadForm')[0].reset();
            mostrarNotificacion('success', 'Lead registrado exitosamente');
        } else {
            mostrarNotificacion('error', response.message || 'Error al guardar el lead');
        }
    }, 'json').fail(function() {
        mostrarNotificacion('error', 'Error de conexión');
    });
});

function agregarNuevaCardAlTablero(lead, idetapa) {
    const container = document.getElementById(`leads-container-${idetapa}`);
    if (!container) return;
    
    const cardHtml = `
        <div class="kanban-card bg-white rounded-lg shadow-md mb-4 p-4 cursor-pointer transition-transform transform hover:scale-105 hover:shadow-xl"
             id="kanban-card-${lead.idlead}"
             data-id="${lead.idlead}"
             data-etapa="${idetapa}"
             draggable="true"
             onclick="abrirDetalleLeadModal(${lead.idlead})"
             style="border-left:5px solid #007bff;">
            
            <div class="card-title text-sm font-bold text-gray-900 mb-1">
                ${lead.nombres} ${lead.apellidos}
            </div>
            <div class="card-info text-xs text-gray-500">
                <small class="block truncate">${lead.telefono || ''} | ${lead.correo || ''}</small>
            </div>
            
            <!-- Indicadores de tareas (se actualizan por registro.js) -->
            <div class="mt-2" id="tareas-info-${lead.idlead}"></div>
            
            <!-- Acciones rápidas -->
            <div class="lead-actions mt-3 d-flex gap-1">
                <button class="btn btn-sm btn-outline-primary flex-1" onclick="event.stopPropagation(); crearTareaRapida(${lead.idlead}, '${lead.nombres} ${lead.apellidos}')">
                    <i class="fas fa-plus"></i> Tarea
                </button>
                <button class="btn btn-sm btn-outline-info" onclick="event.stopPropagation(); verTareasLead(${lead.idlead})">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', cardHtml);
    actualizarContadores();
}

function actualizarContadores() {
    document.querySelectorAll('.kanban-column').forEach(columna => {
        const etapaId = columna.dataset.etapa;
        const contador = columna.querySelector(`#count-${etapaId}`);
        const numLeads = columna.querySelectorAll('.kanban-card').length;
        
        if (contador) {
            contador.textContent = numLeads;
            contador.classList.add('animate-pulse');
            setTimeout(() => contador.classList.remove('animate-pulse'), 800);
        }
    });
}

function mostrarNotificacion(tipo, mensaje) {
    Swal.fire({
        icon: tipo,
        title: tipo === 'success' ? '¡Éxito!' : 'Error',
        text: mensaje,
        timer: tipo === 'success' ? 2000 : 3000,
        showConfirmButton: false,
        toast: true,
        position: 'top-end',
        timerProgressBar: true
    });
}

// Función para crear lead en etapa específica (llamada desde la vista)
function crearLeadEnEtapa(idetapa) {
    abrirModalNuevoLead(idetapa);
}

window.abrirModalNuevoLead = abrirModalNuevoLead;
window.actualizarContadores = actualizarContadores;
window.crearLeadEnEtapa = crearLeadEnEtapa;