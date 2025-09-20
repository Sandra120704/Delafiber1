// Variables globales del Kanban
let sortableInstances = [];
let dragCounter = 0;

// ====================================================
// INICIALIZACIÓN CON VALIDACIONES ROBUSTAS
// ====================================================
$(document).ready(function() {
    console.log('🎯 Iniciando Kanban CRM...');
    
    // Verificar dependencias
    if (typeof Sortable === 'undefined') {
        console.error('SortableJS no está cargado');
        return;
    }
    
    if (typeof $ === 'undefined') {
        console.error( 'jQuery no está cargado');
        return;
    }
    
    console.log('Dependencias verificadas');
    
    // Esperar un poco para que el DOM esté completamente listo
    setTimeout(() => {
        console.log(' Inicializando componentes...');
        inicializarKanban();
        inicializarEfectosVisuales();
        activarDragEnCards();
        
        // Test de funcionamiento
        setTimeout(diagnosticarKanban, 1000);
    }, 800);
});

function inicializarEfectosVisuales() {
    // Efecto de entrada para las cards
    animarEntradaCards();
    
    // Efecto parallax sutil en las columnas
    if (window.DeviceMotionEvent) {
        window.addEventListener('deviceorientation', aplicarParallax);
    }
    
    // Efectos de hover mejorados
    agregarEfectosHover();
}

function animarEntradaCards() {
    document.querySelectorAll('.kanban-card').forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px) scale(0.9)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.6s cubic-bezier(0.25, 0.8, 0.25, 1)';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0) scale(1)';
        }, index * 100);
    });
}

function aplicarParallax(e) {
    const rotateX = (e.beta - 90) / 10;
    const rotateY = e.gamma / 10;
    
    document.querySelectorAll('.kanban-column').forEach(column => {
        column.style.transform = `perspective(1000px) rotateX(${rotateX * 0.1}deg) rotateY(${rotateY * 0.1}deg)`;
    });
}

function agregarEfectosHover() {
    document.querySelectorAll('.kanban-card').forEach(card => {
        card.addEventListener('mouseenter', () => {
            // Efecto de elevación con sonido sutil (solo si el usuario interactúa)
            if (dragCounter > 0) {
                reproducirSonidoSutil('hover');
            }
        });
        
        card.addEventListener('mouseleave', () => {
            card.style.transform = '';
        });
    });
}

function inicializarKanban() {
    console.log('🔧 Inicializando Kanban...');
    
    // Destruir instancias existentes si las hay
    if (sortableInstances.length > 0) {
        console.log('🧹 Limpiando instancias anteriores...');
        sortableInstances.forEach(instance => {
            if (instance && typeof instance.destroy === 'function') {
                try {
                    instance.destroy();
                } catch(e) {
                    console.log('Instancia ya destruida:', e);
                }
            }
        });
        sortableInstances = [];
    }
    
    // Buscar contenedores
    const containers = document.querySelectorAll('.leads-container');
    console.log(`📦 Contenedores encontrados: ${containers.length}`);
    
    if (containers.length === 0) {
        console.error('❌ No se encontraron contenedores .leads-container');
        return;
    }
    
    // Inicializar SortableJS en cada contenedor
    containers.forEach((container, index) => {
        const kanbanColumn = container.closest('.kanban-column');
        if (!kanbanColumn) {
            console.error(`❌ Contenedor ${index} no tiene .kanban-column padre`);
            return;
        }
        
        const etapaId = kanbanColumn.dataset.etapa;
        console.log(`🎯 Inicializando sortable para etapa: ${etapaId}`);
        
        try {
            const sortable = new Sortable(container, {
                group: {
                    name: 'kanban-leads',
                    pull: true,
                    put: true
                },
                animation: 300,
                easing: "cubic-bezier(0.25, 0.8, 0.25, 1)",
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                dragClass: 'sortable-drag',
                forceFallback: false,
                fallbackTolerance: 3,
                delay: 0,
                delayOnTouchStart: false,
                touchStartThreshold: 5,
                swapThreshold: 0.65,
                
                // Filtros para evitar conflictos
                filter: '.no-drag',
                preventOnFilter: false,
                
                onStart: function(evt) {
                    console.log('🎯 Drag iniciado:', evt.item.dataset.id);
                    dragCounter++;
                    
                    // Efectos visuales
                    document.querySelectorAll('.kanban-column').forEach(col => {
                        if (col !== evt.from.closest('.kanban-column')) {
                            col.classList.add('drag-over');
                        }
                    });
                    
                    // Feedback sensorial
                    if (navigator.vibrate) navigator.vibrate(50);
                    reproducirSonidoSutil('start');
                    
                    // Marcar item
                    evt.item.classList.add('dragging');
                    
                    // Ocultar badges temporalmente
                    evt.item.querySelectorAll('.tarea-badge').forEach(badge => {
                        badge.style.opacity = '0.3';
                    });
                },
            
                onMove: function(evt) {
                    // Efecto visual de indicador de drop dinámico
                    actualizarIndicadorDrop(evt.to, evt.related);
                    
                    // Efecto de magnetismo visual
                    if (evt.related) {
                        evt.related.style.transform = 'translateY(3px)';
                        setTimeout(() => {
                            if (evt.related) evt.related.style.transform = '';
                        }, 150);
                    }
                    
                    return true;
                },
                
                onEnd: function(evt) {
                    console.log('✅ Drag finalizado:', evt.item.dataset.id);
                    
                    // Limpiar efectos visuales
                    document.querySelectorAll('.kanban-column').forEach(col => {
                        col.classList.remove('drag-over');
                        col.style.animation = '';
                    });
                    
                    // Restaurar badges
                    evt.item.querySelectorAll('.tarea-badge').forEach(badge => {
                        badge.style.opacity = '';
                    });
                    
                    // Remover clase dragging
                    evt.item.classList.remove('dragging');
                    
                    // Procesar movimiento
                    const itemEl = evt.item;
                    const newEtapa = evt.to.closest('.kanban-column').dataset.etapa;
                    const oldEtapa = evt.from.closest('.kanban-column').dataset.etapa;

                    console.log('📊 Movimiento detectado:', {
                        item: itemEl.dataset.id,
                        de: oldEtapa,
                        a: newEtapa,
                        cambio: newEtapa !== oldEtapa
                    });
                    
                    if (newEtapa !== oldEtapa) {
                        const leadId = itemEl.dataset.id;
                        
                        // Efecto de éxito
                        itemEl.style.animation = 'successGlow 0.8s ease-out';
                        setTimeout(() => itemEl.style.animation = '', 800);
                        
                        // Feedback sensorial
                        if (navigator.vibrate) navigator.vibrate([100, 50, 100]);
                        reproducirSonidoSutil('success');
                        
                        moverLeadAEtapa(leadId, newEtapa, oldEtapa);
                    } else {
                        reproducirSonidoSutil('cancel');
                    }
                    
                    // Limpiar indicadores
                    document.querySelectorAll('.drop-indicator').forEach(indicator => {
                        indicator.classList.remove('active');
                    });
                }
            });
            
            sortableInstances.push(sortable);
            console.log(`✅ Sortable creado para etapa ${etapaId}`);
            
        } catch (error) {
            console.error(`❌ Error creando sortable para etapa ${etapaId}:`, error);
        }
    });
    
    console.log(`🎉 Kanban inicializado: ${sortableInstances.length} instancias creadas`);
}

function actualizarIndicadorDrop(container, related) {
    // Limpiar indicadores previos
    document.querySelectorAll('.drop-indicator').forEach(indicator => {
        indicator.classList.remove('active');
    });
    
    // Activar indicador en la zona actual
    const indicator = container.querySelector('.drop-indicator');
    if (indicator) {
        indicator.classList.add('active');
    }
}

function reproducirSonidoSutil(tipo) {
    // Solo reproducir sonidos si el usuario ha interactuado
    if (dragCounter === 0) return;
    
    const AudioContext = window.AudioContext || window.webkitAudioContext;
    if (!AudioContext) return;
    
    try {
        const audioCtx = new AudioContext();
        const oscillator = audioCtx.createOscillator();
        const gainNode = audioCtx.createGain();
        
        oscillator.connect(gainNode);
        gainNode.connect(audioCtx.destination);
        
        // Configurar sonidos según el tipo
        switch(tipo) {
            case 'start':
                oscillator.frequency.setValueAtTime(800, audioCtx.currentTime);
                gainNode.gain.setValueAtTime(0.1, audioCtx.currentTime);
                break;
            case 'success':
                oscillator.frequency.setValueAtTime(1000, audioCtx.currentTime);
                oscillator.frequency.exponentialRampToValueAtTime(1200, audioCtx.currentTime + 0.1);
                gainNode.gain.setValueAtTime(0.15, audioCtx.currentTime);
                break;
            case 'cancel':
                oscillator.frequency.setValueAtTime(600, audioCtx.currentTime);
                gainNode.gain.setValueAtTime(0.05, audioCtx.currentTime);
                break;
            case 'hover':
                oscillator.frequency.setValueAtTime(900, audioCtx.currentTime);
                gainNode.gain.setValueAtTime(0.03, audioCtx.currentTime);
                break;
        }
        
        gainNode.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.2);
        
        oscillator.start();
        oscillator.stop(audioCtx.currentTime + 0.2);
        
    } catch (e) {
        // Silenciar errores de audio
    }
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
    
    // Activar drag en la nueva card
    const nuevaCard = container.lastElementChild;
    activarDragEnCard(nuevaCard);
    
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

// Función para crear lead en etapa específica 
function crearLeadEnEtapa(idetapa) {
    abrirModalNuevoLead(idetapa);
}

// Función para forzar la activación del drag en una card específica
function activarDragEnCard(card) {
    if (!card) return;
    
    // Asegurar que la card sea draggable
    card.draggable = true;
    card.style.cursor = 'grab';
    
    // Eventos de drag nativos como fallback
    card.addEventListener('dragstart', function(e) {
        console.log('Drag iniciado en card:', e.target.dataset.id);
        e.target.style.opacity = '0.5';
    });
    
    card.addEventListener('dragend', function(e) {
        console.log('Drag finalizado en card:', e.target.dataset.id);
        e.target.style.opacity = '1';
    });
}

// Función para forzar la activación del drag en todas las cards
function activarDragEnCards() {
    console.log('🔧 Activando drag en todas las cards...');
    
    document.querySelectorAll('.kanban-card').forEach(card => {
        activarDragEnCard(card);
    });
    
    console.log('Drag activado en', document.querySelectorAll('.kanban-card').length, 'cards');
}

// Fix automático mejorado
function repararDragAndDrop() {
    console.log('🔧 === REPARACIÓN AUTOMÁTICA ===');
    
    // 1. Diagnóstico inicial
    const diagnostico = diagnosticarKanban();
    
    // 2. Verificar dependencias críticas
    if (!diagnostico.dependencias.sortable) {
        console.error('❌ SortableJS no disponible - cargando dinámicamente...');
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js';
        script.onload = () => {
            console.log('✅ SortableJS cargado dinámicamente');
            setTimeout(repararDragAndDrop, 500);
        };
        document.head.appendChild(script);
        return;
    }
    
    // 3. Limpiar instancias problemáticas
    console.log('🧹 Limpiando instancias...');
    sortableInstances.forEach((instance, index) => {
        if (instance && typeof instance.destroy === 'function') {
            try {
                instance.destroy();
                console.log(`✅ Instancia ${index} destruida`);
            } catch(e) {
                console.log(`⚠️ Error destruyendo instancia ${index}:`, e);
            }
        }
    });
    sortableInstances = [];
    
    // 4. Reparar atributos de las tarjetas
    console.log('🎯 Reparando tarjetas...');
    const cards = document.querySelectorAll('.kanban-card');
    cards.forEach((card, index) => {
        // Asegurar draggable
        if (!card.hasAttribute('draggable')) {
            card.draggable = true;
            console.log(`✅ Draggable añadido a card ${index}`);
        }
        
        // Asegurar data-id
        if (!card.dataset.id) {
            console.warn(`⚠️ Card ${index} sin data-id`);
        }
        
        // Asegurar cursor
        card.style.cursor = 'grab';
        
        // Remover clases problemáticas
        card.classList.remove('dragging', 'moving');
    });
    
    // 5. Verificar contenedores
    console.log('📦 Verificando contenedores...');
    const containers = document.querySelectorAll('.leads-container');
    if (containers.length === 0) {
        console.error('❌ CRÍTICO: No hay contenedores .leads-container');
        return false;
    }
    
    // 6. Reinicializar Kanban
    console.log('🚀 Reinicializando Kanban...');
    setTimeout(() => {
        inicializarKanban();
        
        // 7. Verificar éxito
        setTimeout(() => {
            const nuevosDiagnostico = diagnosticarKanban();
            if (nuevosDiagnostico.sortable.instancias > 0) {
                console.log('🎉 REPARACIÓN EXITOSA');
                return true;
            } else {
                console.error('❌ REPARACIÓN FALLÓ');
                return false;
            }
        }, 500);
    }, 200);
    
    console.log('=== FIN REPARACIÓN ===');
}
// Función de diagnóstico mejorada
function diagnosticarKanban() {
    console.log('🔍 === DIAGNÓSTICO COMPLETO DEL KANBAN ===');
    
    // 1. Verificar dependencias
    console.log('📚 Dependencias:');
    console.log('- jQuery:', typeof $ !== 'undefined' ? '✅' : '❌');
    console.log('- SortableJS:', typeof Sortable !== 'undefined' ? '✅' : '❌');
    console.log('- Base URL:', typeof base_url !== 'undefined' ? base_url : '❌ NO DEFINIDO');
    
    // 2. Verificar estructura DOM
    console.log('\n🏗️ Estructura DOM:');
    const columns = document.querySelectorAll('.kanban-column');
    const containers = document.querySelectorAll('.leads-container');
    const cards = document.querySelectorAll('.kanban-card');
    
    console.log(`- Columnas Kanban: ${columns.length}`);
    console.log(`- Contenedores leads: ${containers.length}`);
    console.log(`- Tarjetas totales: ${cards.length}`);
    
    // 3. Verificar cada contenedor
    console.log('\n📦 Contenedores por etapa:');
    containers.forEach((container, index) => {
        const column = container.closest('.kanban-column');
        const etapaId = column?.dataset.etapa || 'SIN ETAPA';
        const cardsEnContainer = container.querySelectorAll('.kanban-card').length;
        console.log(`- Etapa ${etapaId}: ${cardsEnContainer} cards`);
    });
    
    // 4. Verificar instancias de Sortable
    console.log('\n🎯 Instancias Sortable:');
    console.log(`- Instancias creadas: ${sortableInstances.length}`);
    console.log(`- Instancias activas: ${sortableInstances.filter(s => s && s.el).length}`);
    
    // 5. Verificar atributos de drag
    console.log('\n🖱️ Atributos de drag:');
    const cardsNoDraggable = document.querySelectorAll('.kanban-card:not([draggable])');
    const cardsSinId = document.querySelectorAll('.kanban-card:not([data-id])');
    
    console.log(`- Cards sin draggable: ${cardsNoDraggable.length}`);
    console.log(`- Cards sin data-id: ${cardsSinId.length}`);
    
    // 6. Test de funcionalidad
    console.log('\n🧪 Test de funcionalidad:');
    if (sortableInstances.length > 0) {
        console.log('✅ Sortable inicializado correctamente');
    } else {
        console.log('❌ Sortable NO inicializado');
    }
    
    // 7. Recomendaciones
    console.log('\n💡 Recomendaciones:');
    if (containers.length === 0) {
        console.log('❌ CRÍTICO: No hay contenedores .leads-container');
    }
    if (cards.length === 0) {
        console.log('⚠️ No hay tarjetas .kanban-card para mover');
    }
    if (sortableInstances.length !== containers.length) {
        console.log('⚠️ Número de instancias no coincide con contenedores');
    }
    
    console.log('\n=== FIN DIAGNÓSTICO ===');
    
    // Retornar resumen para usar programáticamente
    return {
        dependencias: {
            jquery: typeof $ !== 'undefined',
            sortable: typeof Sortable !== 'undefined',
            baseUrl: typeof base_url !== 'undefined'
        },
        estructura: {
            columnas: columns.length,
            contenedores: containers.length,
            tarjetas: cards.length
        },
        sortable: {
            instancias: sortableInstances.length,
            activas: sortableInstances.filter(s => s && s.el).length
        },
        problemas: {
            sinDraggable: cardsNoDraggable.length,
            sinId: cardsSinId.length
        }
    };
}

// Exponer funciones para debug y uso global
window.abrirModalNuevoLead = abrirModalNuevoLead;
window.actualizarContadores = actualizarContadores;
window.crearLeadEnEtapa = crearLeadEnEtapa;
window.repararDragAndDrop = repararDragAndDrop;
window.diagnosticarKanban = diagnosticarKanban;
window.activarDragEnCards = activarDragEnCards;
window.sortableInstances = sortableInstances; // Para debug

// Funciones de test rápido desde consola
window.testKanban = function() {
    console.log('🧪 === TEST RÁPIDO KANBAN ===');
    const diagnostico = diagnosticarKanban();
    
    if (!diagnostico.dependencias.jquery) {
        console.error('❌ jQuery no disponible');
        return false;
    }
    
    if (!diagnostico.dependencias.sortable) {
        console.error('❌ SortableJS no disponible');
        return false;
    }
    
    if (diagnostico.estructura.tarjetas === 0) {
        console.warn('⚠️ No hay tarjetas para probar');
        return false;
    }
    
    if (diagnostico.sortable.instancias === 0) {
        console.warn('⚠️ Sortable no inicializado - intentando reparar...');
        repararDragAndDrop();
        return false;
    }
    
    console.log('✅ Todo parece estar funcionando correctamente');
    console.log('💡 Prueba arrastrar una tarjeta para confirmar');
    return true;
};

// Comando de emergencia
window.emergenciaKanban = function() {
    console.log('🚨 === MODO EMERGENCIA ===');
    console.log('Reiniciando completamente el sistema...');
    
    // Limpiar todo
    if (window.sortableInstances) {
        window.sortableInstances.forEach(instance => {
            if (instance && instance.destroy) {
                try { instance.destroy(); } catch(e) {}
            }
        });
        window.sortableInstances = [];
    }
    
    // Recargar página como último recurso
    setTimeout(() => {
        console.log('🔄 Recargando página...');
        location.reload();
    }, 1000);
};