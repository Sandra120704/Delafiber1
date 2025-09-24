/**
 * 🚀 SISTEMA DE LEADS CONSOLIDADO Y OPTIMIZADO
 * Este archivo unifica todas las funcionalidades de leads en un solo lugar
 * Elimina duplicaciones y conflictos entre archivos JavaScript
 * 
 * Funcionalidades incluidas:
 * - Kanban con drag & drop optimizado
 * - Modal de detalle con tabs
 * - Gestión completa de tareas
 * - Formularios de creación
 * - Notificaciones y validaciones
 * - Cache inteligente y lazy loading
 * - Performance tracking
 */

// =====================================================
// ⚡ VARIABLES GLOBALES UNIFICADAS Y OPTIMIZADAS
// =====================================================
const LeadsSystem = {
    // Configuración
    base_url: typeof base_url !== 'undefined' ? base_url : '',
    
    // Estado del sistema
    leadActual: null,
    tareasLead: [],
    sortableInstances: [],
    
    // Cache inteligente
    leadCache: new Map(),
    tareasCache: new Map(),
    performanceCache: new Map(),
    
    // Performance metrics
    metrics: {
        dragOperations: 0,
        apiCalls: 0,
        cacheHits: 0,
        cacheMisses: 0,
        loadTimes: []
    },
    
    // Debounced functions storage
    debouncedFunctions: new Map(),
    
    // Lazy loading queue
    lazyQueue: new Set(),
    
    // Request queue para batch operations
    requestQueue: [],
    requestTimer: null
};

// =====================================================
// ⚡ UTILIDADES DE PERFORMANCE Y OPTIMIZACIÓN
// =====================================================

/**
 * Performance tracking y métricas
 */
const PerformanceTracker = {
    startTime: (label) => {
        const start = performance.now();
        LeadsSystem.performanceCache.set(label + '_start', start);
        return start;
    },
    
    endTime: (label) => {
        const start = LeadsSystem.performanceCache.get(label + '_start');
        const end = performance.now();
        const duration = end - start;
        
        LeadsSystem.metrics.loadTimes.push({
            operation: label,
            duration: duration,
            timestamp: new Date()
        });
        
        // Limpiar cache temporal
        LeadsSystem.performanceCache.delete(label + '_start');
        
        // Log para debugging (solo en desarrollo)
        if (window.location.hostname === 'localhost') {
            console.log(`⚡ ${label}: ${duration.toFixed(2)}ms`);
        }
        
        return duration;
    }
};

/**
 * Debounce function optimizada
 */
const debounce = (func, wait, immediate = false) => {
    const key = func.toString();
    
    if (LeadsSystem.debouncedFunctions.has(key)) {
        return LeadsSystem.debouncedFunctions.get(key);
    }
    
    let timeout;
    const debouncedFunc = function executedFunction(...args) {
        const later = () => {
            timeout = null;
            if (!immediate) func.apply(this, args);
        };
        
        const callNow = immediate && !timeout;
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
        
        if (callNow) func.apply(this, args);
    };
    
    LeadsSystem.debouncedFunctions.set(key, debouncedFunc);
    return debouncedFunc;
};

/**
 * Throttle function para eventos de alta frecuencia
 */
const throttle = (func, delay) => {
    let timeoutId;
    let lastExecTime = 0;
    return function (...args) {
        const currentTime = Date.now();
        
        if (currentTime - lastExecTime > delay) {
            func.apply(this, args);
            lastExecTime = currentTime;
        } else {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(() => {
                func.apply(this, args);
                lastExecTime = Date.now();
            }, delay - (currentTime - lastExecTime));
        }
    };
};

/**
 * Cache inteligente con TTL
 */
const CacheManager = {
    set: (key, value, ttl = 300000) => { // 5 minutos por defecto
        const item = {
            value: value,
            timestamp: Date.now(),
            ttl: ttl
        };
        LeadsSystem.leadCache.set(key, item);
        LeadsSystem.metrics.cacheHits++;
    },
    
    get: (key) => {
        const item = LeadsSystem.leadCache.get(key);
        
        if (!item) {
            LeadsSystem.metrics.cacheMisses++;
            return null;
        }
        
        // Verificar TTL
        if (Date.now() - item.timestamp > item.ttl) {
            LeadsSystem.leadCache.delete(key);
            LeadsSystem.metrics.cacheMisses++;
            return null;
        }
        
        LeadsSystem.metrics.cacheHits++;
        return item.value;
    },
    
    clear: (pattern = null) => {
        if (pattern) {
            for (const key of LeadsSystem.leadCache.keys()) {
                if (key.includes(pattern)) {
                    LeadsSystem.leadCache.delete(key);
                }
            }
        } else {
            LeadsSystem.leadCache.clear();
        }
    }
};

/**
 * Lazy Loading Manager
 */
const LazyLoader = {
    observe: (element, callback) => {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    callback(entry.target);
                    observer.unobserve(entry.target);
                }
            });
        }, {
            root: null,
            rootMargin: '50px',
            threshold: 0.1
        });
        
        observer.observe(element);
        return observer;
    },
    
    loadImages: () => {
        const images = document.querySelectorAll('img[data-src]');
        images.forEach(img => {
            LazyLoader.observe(img, (element) => {
                element.src = element.dataset.src;
                element.removeAttribute('data-src');
                element.classList.add('loaded');
            });
        });
    }
};

/**
 * Request Queue Manager para batch operations
 */
const RequestQueue = {
    add: (request) => {
        LeadsSystem.requestQueue.push(request);
        
        // Procesar cola después de 100ms si no hay más requests
        clearTimeout(LeadsSystem.requestTimer);
        LeadsSystem.requestTimer = setTimeout(() => {
            RequestQueue.process();
        }, 100);
    },
    
    process: async () => {
        if (LeadsSystem.requestQueue.length === 0) return;
        
        const requests = [...LeadsSystem.requestQueue];
        LeadsSystem.requestQueue = [];
        
        // Agrupar requests similares
        const grouped = requests.reduce((acc, req) => {
            const key = `${req.method}_${req.url}`;
            if (!acc[key]) acc[key] = [];
            acc[key].push(req);
            return acc;
        }, {});
        
        // Ejecutar en paralelo
        const results = await Promise.allSettled(
            Object.values(grouped).map(group => {
                if (group.length === 1) {
                    return group[0].execute();
                } else {
                    // Batch request
                    return RequestQueue.batchExecute(group);
                }
            })
        );
        
        return results;
    },
    
    batchExecute: async (requests) => {
        // Implementar batch request si el backend lo soporta
        // Por ahora, ejecutar en secuencia con throttling
        const results = [];
        for (const req of requests) {
            await new Promise(resolve => setTimeout(resolve, 50)); // 50ms delay
            results.push(await req.execute());
        }
        return results;
    }
};

/**
 * Mobile Support System para optimizar la experiencia móvil
 */
const MobileSupport = {
    isMobile: /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent),
    isTablet: /iPad|Android/i.test(navigator.userAgent),
    isTouchDevice: 'ontouchstart' in window || navigator.maxTouchPoints > 0,
    
    init: function() {
        if (this.isTouchDevice) {
            console.log('📱 Inicializando soporte móvil...');
            this.setupTouchEvents();
            this.optimizeForMobile();
            this.addPullToRefresh();
        }
    },
    
    setupTouchEvents: function() {
        // Mejorar drag & drop para dispositivos táctiles
        document.addEventListener('touchstart', function(e) {
            // Prevenir scroll durante drag en kanban cards
            if (e.target.closest('.kanban-card')) {
                e.target.style.touchAction = 'none';
            }
        }, { passive: false });
        
        // Haptic feedback para acciones importantes
        if ('vibrate' in navigator) {
            $(document).on('dragstart', '.kanban-card', function() {
                navigator.vibrate(50); // Vibración ligera
            });
            
            $(document).on('dragend', '.kanban-card', function() {
                navigator.vibrate([30, 20, 30]); // Patrón de confirmación
            });
        }
    },
    
    optimizeForMobile: function() {
        // Ajustar viewport para dispositivos móviles
        if (!document.querySelector('meta[name="viewport"]')) {
            const viewport = document.createElement('meta');
            viewport.name = 'viewport';
            viewport.content = 'width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no';
            document.head.appendChild(viewport);
        }
        
        // Añadir clases específicas para móviles
        document.body.classList.toggle('mobile-device', this.isMobile);
        document.body.classList.toggle('tablet-device', this.isTablet);
        document.body.classList.toggle('touch-device', this.isTouchDevice);
        
        // Optimizar modales para móviles
        if (this.isMobile) {
            $(document).on('show.bs.modal', '.modal', function() {
                $('body').addClass('modal-open-mobile');
            });
            
            $(document).on('hidden.bs.modal', '.modal', function() {
                $('body').removeClass('modal-open-mobile');
            });
        }
    },
    
    addPullToRefresh: function() {
        let startY = 0;
        let currentY = 0;
        let pullDistance = 0;
        let refreshThreshold = 80;
        let isRefreshing = false;
        
        // Crear indicador de pull to refresh
        const indicator = $(`
            <div class="pull-to-refresh" id="pullToRefreshIndicator">
                <i class="fas fa-sync-alt"></i> Suelta para actualizar
            </div>
        `);
        $('body').append(indicator);
        
        $(document).on('touchstart', '.kanban-board', function(e) {
            if (window.scrollY === 0 && !isRefreshing) {
                startY = e.originalEvent.touches[0].pageY;
            }
        });
        
        $(document).on('touchmove', '.kanban-board', function(e) {
            if (startY === 0 || isRefreshing) return;
            
            currentY = e.originalEvent.touches[0].pageY;
            pullDistance = currentY - startY;
            
            if (pullDistance > 0 && window.scrollY === 0) {
                e.preventDefault();
                
                // Mostrar indicador
                const opacity = Math.min(pullDistance / refreshThreshold, 1);
                indicator.css('opacity', opacity);
                
                if (pullDistance > refreshThreshold) {
                    indicator.addClass('active').html('<i class="fas fa-sync-alt fa-spin"></i> Suelta para actualizar');
                } else {
                    indicator.removeClass('active').html('<i class="fas fa-sync-alt"></i> Desliza hacia abajo');
                }
            }
        });
        
        $(document).on('touchend', '.kanban-board', function() {
            if (pullDistance > refreshThreshold && !isRefreshing) {
                isRefreshing = true;
                indicator.html('<i class="fas fa-sync-alt fa-spin"></i> Actualizando...').addClass('active');
                
                // Simular refresh
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
                
                // Haptic feedback
                if ('vibrate' in navigator) {
                    navigator.vibrate(100);
                }
            }
            
            // Reset
            startY = 0;
            pullDistance = 0;
            
            // Ocultar indicador
            setTimeout(() => {
                indicator.removeClass('active').css('opacity', 0);
            }, 300);
        });
    },
    
    // Detectar orientación del dispositivo
    handleOrientationChange: function() {
        $(window).on('orientationchange resize', debounce(function() {
            // Reajustar kanban después del cambio de orientación
            LeadsSystem.sortableInstances.forEach(instance => {
                if (instance.option) {
                    instance.option('disabled', false);
                }
            });
            
            // Forzar repaint
            $('.kanban-board').hide().show();
            
            console.log('📱 Orientación cambiada - Kanban reajustado');
        }, 500));
    }
};

// =====================================================
// INICIALIZACIÓN DEL SISTEMA
// =====================================================
$(document).ready(function() {
    console.log('Iniciando Sistema de Leads Consolidado...');
    
    // Inicializar performance tracking
    PerformanceTracker.startTime('initialization');
    
    // Verificar dependencias críticas
    if (!LeadsSystem.verificarDependencias()) {
        console.error(' Dependencias faltantes - Sistema no iniciado');
        return;
    }
    
    // Inicializar componentes en orden optimizado
    LeadsSystem.inicializarKanban();
    LeadsSystem.inicializarModales();
    LeadsSystem.inicializarEventos();
    LeadsSystem.inicializarValidaciones();
    
    // Inicializar optimizaciones de performance
    LazyLoader.loadImages();
    
    // Inicializar soporte móvil
    MobileSupport.init();
    
    // Limpiar cache antigua periódicamente (cada 10 minutos)
    setInterval(() => {
        CacheManager.clear();
        console.log(' Cache limpiado automáticamente');
    }, 600000);
    
    // Finalizar tracking de inicialización
    PerformanceTracker.endTime('initialization');
    
    // Inicializar validaciones en tiempo real
    ValidationSystem.setupRealTimeValidation('#dni', 'dni');
    ValidationSystem.setupRealTimeValidation('#telefono', 'telefono');
    ValidationSystem.setupRealTimeValidation('#email', 'email');
    ValidationSystem.setupRealTimeValidation('#nombres', 'nombres');
    ValidationSystem.setupRealTimeValidation('#apellidos', 'apellidos');
    
    PerformanceTracker.endTime('modales_init');
    console.log(' Sistema de modales inicializado');
});

// =====================================================
// VERIFICACIÓN DE DEPENDENCIAS
// =====================================================
LeadsSystem.verificarDependencias = function() {
    const dependencias = [
        { nombre: 'jQuery', objeto: window.$ },
        { nombre: 'Bootstrap', objeto: window.bootstrap },
        { nombre: 'SweetAlert2', objeto: window.Swal },
        { nombre: 'SortableJS', objeto: window.Sortable }
    ];
    
    let todasPresentes = true;
    
    dependencias.forEach(dep => {
        if (typeof dep.objeto === 'undefined') {
            console.error(`❌ ${dep.nombre} no está cargado`);
            todasPresentes = false;
        }
    });
    
    return todasPresentes;
};

// =====================================================
// SISTEMA KANBAN CON DRAG & DROP
// =====================================================
LeadsSystem.inicializarKanban = function() {
    PerformanceTracker.startTime('kanban_init');
    console.log('📋 Inicializando Kanban optimizado...');
    
    // Limpiar instancias previas
    LeadsSystem.sortableInstances.forEach(instance => instance.destroy());
    LeadsSystem.sortableInstances = [];
    
    // Inicializar SortableJS en cada columna con optimizaciones
    document.querySelectorAll('.leads-container').forEach(container => {
        const columna = container.closest('.kanban-column');
        const etapaId = columna.dataset.etapa;
        
        const sortable = new Sortable(container, {
            group: 'kanban-leads',
            animation: 200,
            ghostClass: 'sortable-ghost',
            chosenClass: 'lead-card-chosen',
            dragClass: 'lead-card-drag',
            
            // Optimización: throttle para onEnd
            onEnd: throttle(function(evt) {
                PerformanceTracker.startTime('drag_operation');
                LeadsSystem.metrics.dragOperations++;
                
                const leadElement = evt.item;
                const leadId = leadElement.dataset.leadId;
                const nuevaEtapaContainer = evt.to.closest('.kanban-column');
                const nuevaEtapaId = nuevaEtapaContainer.dataset.etapa;
                const etapaAnteriorContainer = evt.from.closest('.kanban-column');
                
                if (!leadId || !nuevaEtapaId) {
                    console.error('❌ Error en drag & drop: datos incompletos');
                    // Revertir movimiento
                    evt.from.insertBefore(leadElement, evt.from.children[evt.oldIndex]);
                    return;
                }
                
                console.log(`🔄 Moviendo lead ${leadId} a etapa ${nuevaEtapaId}`);
                
                // Actualizar con cache y optimizaciones
                LeadsSystem.actualizarEtapaLead(leadId, nuevaEtapaId, leadElement, etapaAnteriorContainer)
                    .finally(() => {
                        PerformanceTracker.endTime('drag_operation');
                    });
            }, 300), // Throttle de 300ms
            
            // Validación optimizada antes del drag
            onMove: function(evt) {
                const leadCard = evt.dragged;
                const targetContainer = evt.to;
                
                // Validaciones rápidas
                if (!leadCard.dataset.leadId) {
                    console.warn('⚠️ Lead sin ID, movimiento cancelado');
                    return false;
                }
                
                if (!targetContainer.classList.contains('leads-container')) {
                    return false;
                }
                
                return true;
            }
        });
        
        LeadsSystem.sortableInstances.push(sortable);
    });
    
    PerformanceTracker.endTime('kanban_init');
    console.log('✅ Kanban inicializado con optimizaciones');
};

// =====================================================
// UTILIDADES KANBAN
// =====================================================

LeadsSystem.mostrarIndicadoresDrop = function() {
    document.querySelectorAll('.drop-indicator').forEach(indicator => {
        indicator.style.display = 'block';
    });
};

LeadsSystem.ocultarIndicadoresDrop = function() {
    document.querySelectorAll('.drop-indicator').forEach(indicator => {
        indicator.style.display = 'none';
    });
};

LeadsSystem.actualizarEtapaLead = async function(leadId, nuevaEtapaId, leadElement, etapaAnterior) {
    PerformanceTracker.startTime('update_etapa');
    LeadsSystem.metrics.apiCalls++;
    
    // Validar datos antes de continuar
    if (!leadId || !nuevaEtapaId) {
        Swal.fire({
            icon: 'error',
            title: 'Error de datos',
            text: 'ID de lead o etapa no válidos',
            toast: true,
            position: 'top-end',
            timer: 3000
        });
        return;
    }
    
    // Verificar cache primero
    const cacheKey = `etapa_${leadId}_${nuevaEtapaId}`;
    const cached = CacheManager.get(cacheKey);
    
    if (cached) {
        console.log('💾 Usando resultado cacheado para actualización de etapa');
        PerformanceTracker.endTime('update_etapa');
        return cached;
    }
    
    console.log(`🔄 Actualizando lead ${leadId} a etapa ${nuevaEtapaId}`);
    
    // Mostrar indicador visual optimista
    if (leadElement) {
        leadElement.style.opacity = '0.7';
        leadElement.style.transform = 'scale(0.98)';
    }
    
    try {
        const response = await fetch(`${LeadsSystem.base_url}leads/actualizarEtapa`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: new URLSearchParams({
                idlead: leadId,
                idetapa: nuevaEtapaId
            })
        });
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const result = await response.json();
        console.log('✅ Respuesta del servidor:', result);
        
        // Cachear resultado exitoso
        if (result.success) {
            CacheManager.set(cacheKey, result, 120000); // 2 minutos
        }
        
        // Restaurar elemento visual
        if (leadElement) {
            leadElement.style.opacity = '1';
            leadElement.style.transform = 'scale(1)';
        }
        
        // Notificación optimizada
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: result.success ? 2000 : 4000,
            timerProgressBar: true,
            customClass: {
                popup: result.success ? 'swal-toast-success' : 'swal-toast-error'
            }
        });
        
        Toast.fire({
            icon: result.success ? 'success' : 'error',
            title: result.success ? '✅ Etapa actualizada' : '❌ Error al actualizar',
            text: result.message
        });
        
        if (result.success) {
            // Actualizar contadores con debounce
            const updateCounters = debounce(() => {
                LeadsSystem.actualizarContadoresEtapas();
            }, 1000);
            updateCounters();
            
            // Limpiar cache relacionado
            CacheManager.clear('contador_');
        }
        
        PerformanceTracker.endTime('update_etapa');
        return result;
        
    } catch (error) {
        console.error('❌ Error al actualizar etapa:', error);
        
        // Revertir movimiento visual si hay error
        if (leadElement && etapaAnterior) {
            const containerAnterior = etapaAnterior.querySelector('.leads-container');
            if (containerAnterior) {
                containerAnterior.appendChild(leadElement);
                leadElement.style.opacity = '1';
                leadElement.style.transform = 'scale(1)';
            }
        }
        
        // Error notification
        Swal.fire({
            icon: 'error',
            title: 'Error de conexión',
            text: error.message || 'No se pudo conectar con el servidor',
            toast: true,
            position: 'top-end',
            timer: 4000,
            customClass: {
                popup: 'swal-toast-error'
            }
        });
        
        PerformanceTracker.endTime('update_etapa');
        throw error;
    }
};

// =====================================================
// SISTEMA DE MODALES OPTIMIZADO
// =====================================================
LeadsSystem.inicializarModales = function() {
    PerformanceTracker.startTime('modales_init');
    console.log('🪟 Inicializando sistema de modales...');
    
    // Modal de creación de lead con funcionalidad mejorada
    $('#leadForm').off('submit').on('submit', LeadsSystem.manejarCreacionLead);
    
    // Funcionalidad de búsqueda de DNI
    $('#buscar-dni').off('click').on('click', LeadsSystem.buscarPersonaPorDNI);
    
    // Limpiar formulario al abrir modal
    $('#leadModal').on('show.bs.modal', function() {
        LeadsSystem.limpiarFormularioLead();
    });
    
    // Validación en tiempo real
    $('#dni').off('input').on('input', LeadsSystem.validarDNI);
    $('#telefono').off('input').on('input', LeadsSystem.validarTelefono);
    
    // Modal de detalle mejorado - se inicializa cuando se abre
};

LeadsSystem.limpiarFormularioLead = function() {
    $('#leadForm')[0].reset();
    $('#idpersona').val('');
    $('#estado-busqueda').addClass('d-none');
    $('#leadForm input').removeClass('is-valid is-invalid');
    $('#leadForm select').removeClass('is-valid is-invalid');
};

LeadsSystem.buscarPersonaPorDNI = function() {
    const dni = $('#dni').val().trim();
    
    if (dni.length !== 8) {
        LeadsSystem.mostrarEstadoBusqueda('warning', 'El DNI debe tener 8 dígitos');
        return;
    }

    if (!/^\d+$/.test(dni)) {
        LeadsSystem.mostrarEstadoBusqueda('danger', 'El DNI solo debe contener números');
        return;
    }

    LeadsSystem.mostrarEstadoBusqueda('info', 'Buscando persona...');
    $('#buscar-dni').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Buscando...');

    $.ajax({
        url: `${LeadsSystem.base_url}/personas/buscardni/${dni}`,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success && response.persona) {
                // Persona encontrada - llenar formulario
                const persona = response.persona;
                $('#idpersona').val(persona.idpersona);
                $('#apellidos').val(persona.apellidos);
                $('#nombres').val(persona.nombres);
                $('#telefono').val(persona.telefono);
                $('#correo').val(persona.correo || '');
                $('#direccion').val(persona.direccion || '');
                $('#referencias').val(persona.referencias || '');
                
                if (persona.iddistrito) {
                    $('#iddistrito').val(persona.iddistrito);
                }

                LeadsSystem.mostrarEstadoBusqueda('success', `Persona encontrada: ${persona.nombres} ${persona.apellidos}`);
                
                // Marcar campos como válidos
                $('#nombres, #apellidos, #telefono').addClass('is-valid');
                
            } else {
                // Persona no encontrada - permitir registro manual
                $('#idpersona').val('');
                LeadsSystem.mostrarEstadoBusqueda('warning', 'Persona no encontrada. Complete manualmente los datos.');
                
                // Habilitar campos para edición
                $('#nombres, #apellidos, #telefono, #correo').prop('readonly', false);
            }
        },
        error: function() {
            LeadsSystem.mostrarEstadoBusqueda('danger', 'Error al buscar la persona. Intente nuevamente.');
        },
        complete: function() {
            $('#buscar-dni').prop('disabled', false).html('<i class="fas fa-search"></i> Buscar');
        }
    });
};

LeadsSystem.mostrarEstadoBusqueda = function(tipo, mensaje) {
    const estado = $('#estado-busqueda');
    const iconos = {
        'info': 'fas fa-info-circle',
        'success': 'fas fa-check-circle',
        'warning': 'fas fa-exclamation-triangle',
        'danger': 'fas fa-times-circle'
    };

    estado.removeClass('alert-info alert-success alert-warning alert-danger')
          .addClass(`alert-${tipo}`)
          .removeClass('d-none');
    
    $('#mensaje-busqueda').html(`<i class="${iconos[tipo]}"></i> ${mensaje}`);
};

// =====================================================
// 🔧 SISTEMA DE VALIDACIONES MEJORADO
// =====================================================

/**
 * Validador principal con reglas personalizables
 */
const ValidationSystem = {
    rules: {
        dni: {
            pattern: /^\d{8}$/,
            message: 'El DNI debe tener exactamente 8 dígitos',
            transform: (value) => value.replace(/\D/g, '')
        },
        telefono: {
            pattern: /^9\d{8}$/,
            message: 'El teléfono debe empezar con 9 y tener 9 dígitos',
            transform: (value) => value.replace(/\D/g, '')
        },
        email: {
            pattern: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
            message: 'Ingrese un email válido'
        },
        nombres: {
            pattern: /^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]{2,50}$/,
            message: 'Los nombres deben tener entre 2 y 50 caracteres',
            transform: (value) => value.replace(/\s+/g, ' ').trim()
        },
        apellidos: {
            pattern: /^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]{2,50}$/,
            message: 'Los apellidos deben tener entre 2 y 50 caracteres',
            transform: (value) => value.replace(/\s+/g, ' ').trim()
        }
    },
    
    validate: (field, value, ruleName) => {
        const rule = ValidationSystem.rules[ruleName];
        if (!rule) return { valid: true };
        
        // Transformar valor si hay transformador
        const transformedValue = rule.transform ? rule.transform(value) : value;
        
        // Validar patrón
        const valid = rule.pattern.test(transformedValue);
        
        return {
            valid: valid,
            message: valid ? '' : rule.message,
            transformedValue: transformedValue
        };
    },
    
    // Validación en tiempo real con debounce
    setupRealTimeValidation: (selector, ruleName) => {
        const debouncedValidate = debounce((element) => {
            const value = element.val().trim();
            const result = ValidationSystem.validate(element[0], value, ruleName);
            
            // Actualizar valor transformado
            if (result.transformedValue !== value) {
                element.val(result.transformedValue);
            }
            
            // Aplicar clases de validación
            ValidationSystem.applyValidationClasses(element, result);
            
            // Mostrar mensaje de error
            ValidationSystem.showFieldMessage(element, result);
            
        }, 500);
        
        $(document).on('input blur', selector, function() {
            debouncedValidate($(this));
        });
    },
    
    applyValidationClasses: (element, result) => {
        element.removeClass('is-valid is-invalid');
        
        if (element.val().trim() !== '') {
            element.addClass(result.valid ? 'is-valid' : 'is-invalid');
        }
    },
    
    showFieldMessage: (element, result) => {
        const messageId = element.attr('id') + '-message';
        let messageElement = $(`#${messageId}`);
        
        // Crear elemento de mensaje si no existe
        if (messageElement.length === 0) {
            messageElement = $(`<div id="${messageId}" class="invalid-feedback"></div>`);
            element.after(messageElement);
        }
        
        messageElement.text(result.message);
    },
    
    // Validación de formulario completo
    validateForm: (formSelector) => {
        const form = $(formSelector);
        const fields = form.find('[data-validation]');
        let isValid = true;
        const errors = [];
        
        fields.each(function() {
            const field = $(this);
            const ruleName = field.data('validation');
            const value = field.val().trim();
            const fieldName = field.data('field-name') || field.attr('name');
            
            const result = ValidationSystem.validate(field[0], value, ruleName);
            
            if (!result.valid) {
                isValid = false;
                errors.push({
                    field: fieldName,
                    message: result.message
                });
            }
            
            ValidationSystem.applyValidationClasses(field, result);
            ValidationSystem.showFieldMessage(field, result);
        });
        
        return {
            valid: isValid,
            errors: errors
        };
    }
};

// Funciones de validación legacy (mantenidas para compatibilidad)
LeadsSystem.validarDNI = function() {
    const element = $(this);
    const value = element.val().trim();
    const result = ValidationSystem.validate(this, value, 'dni');
    
    ValidationSystem.applyValidationClasses(element, result);
    ValidationSystem.showFieldMessage(element, result);
    
    // Actualizar valor transformado
    if (result.transformedValue !== value) {
        element.val(result.transformedValue);
    }
};

LeadsSystem.validarTelefono = function() {
    const element = $(this);
    const value = element.val().trim();
    const result = ValidationSystem.validate(this, value, 'telefono');
    
    ValidationSystem.applyValidationClasses(element, result);
    ValidationSystem.showFieldMessage(element, result);
    
    // Actualizar valor transformado
    if (result.transformedValue !== value) {
        element.val(result.transformedValue);
    }
};

LeadsSystem.manejarCreacionLead = function(e) {
    e.preventDefault();
    PerformanceTracker.startTime('create_lead');
    
    const form = $(this);
    const submitBtn = form.find('button[type="submit"]');
    
    // Deshabilitar botón para evitar doble envío
    submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Guardando...');
    
    // Validar formulario con el nuevo sistema
    const validation = ValidationSystem.validateForm('#leadForm');
    
    if (!validation.valid) {
        // Mostrar errores de validación
        const errorMessages = validation.errors.map(error => 
            `• ${error.field}: ${error.message}`
        ).join('\n');
        
        Swal.fire({
            icon: 'warning',
            title: 'Datos incompletos',
            text: 'Por favor corrige los siguientes errores:\n' + errorMessages,
            customClass: {
                popup: 'swal-validation-error'
            }
        });
        
        // Rehabilitar botón
        submitBtn.prop('disabled', false).html('Crear Lead');
        PerformanceTracker.endTime('create_lead');
        return;
    }
    
    // Preparar datos del formulario
    const formData = new FormData(form[0]);
    
    // Verificar cache de personas similares
    const dni = formData.get('dni');
    const telefono = formData.get('telefono');
    const cacheKey = `persona_${dni}_${telefono}`;
    const cachedPerson = CacheManager.get(cacheKey);
    
    if (cachedPerson) {
        Swal.fire({
            icon: 'question',
            title: 'Persona existente',
            text: `Ya existe una persona con estos datos. ¿Desea crear un nuevo lead para ${cachedPerson.nombres} ${cachedPerson.apellidos}?`,
            showCancelButton: true,
            confirmButtonText: 'Sí, crear lead',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                LeadsSystem.enviarFormularioLead(formData, submitBtn);
            } else {
                submitBtn.prop('disabled', false).html('Crear Lead');
            }
        });
        
        PerformanceTracker.endTime('create_lead');
        return;
    }
    
    // Enviar formulario
    LeadsSystem.enviarFormularioLead(formData, submitBtn);
};

/**
 * Enviar formulario de lead con optimizaciones
 */
LeadsSystem.enviarFormularioLead = async function(formData, submitBtn) {
    try {
        const response = await fetch(`${LeadsSystem.base_url}leads/guardar`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const result = await response.json();
        console.log('✅ Respuesta crear lead:', result);
        
        if (result.success) {
            // Cachear persona creada
            if (result.persona) {
                const cacheKey = `persona_${result.persona.dni}_${result.persona.telefono}`;
                CacheManager.set(cacheKey, result.persona, 3600000); // 1 hora
            }
            
            // Notificación de éxito
            Swal.fire({
                icon: 'success',
                title: '¡Lead creado!',
                text: result.message,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                customClass: {
                    popup: 'swal-toast-success'
                }
            });
            
            // Cerrar modal y recargar
            $('#modalNuevoLead').modal('hide');
            $('#leadForm')[0].reset();
            
            // Recargar página con debounce
            const reloadPage = debounce(() => {
                window.location.reload();
            }, 2000);
            reloadPage();
            
        } else {
            throw new Error(result.message || 'Error desconocido');
        }
        
    } catch (error) {
        console.error('❌ Error al crear lead:', error);
        
        Swal.fire({
            icon: 'error',
            title: 'Error al crear lead',
            text: error.message || 'No se pudo conectar con el servidor',
            customClass: {
                popup: 'swal-error'
            }
        });
    } finally {
        // Rehabilitar botón
        submitBtn.prop('disabled', false).html('Crear Lead');
        PerformanceTracker.endTime('create_lead');
    }
};
    
    for (const [campo, nombre] of Object.entries(camposRequeridos)) {
        const valor = $(`#${campo}`).val().trim();
        if (!valor) {
            errores.push(`${nombre} es requerido`);
            $(`#${campo}`).addClass('is-invalid');
        }
    }
    
    // Validar DNI si se ingresó
    const dni = $('#dni').val().trim();
    if (dni && (dni.length !== 8 || !/^\d+$/.test(dni))) {
        errores.push('El DNI debe tener 8 dígitos numéricos');
        $('#dni').addClass('is-invalid');
    }
    
    // Validar teléfono
    const telefono = $('#telefono').val().trim();
    if (telefono && (telefono.length !== 9 || !/^\d+$/.test(telefono))) {
        errores.push('El teléfono debe tener 9 dígitos numéricos');
        $('#telefono').addClass('is-invalid');
    }
    
    if (errores.length > 0) {
        Swal.fire({
            icon: 'error',
            title: 'Errores en el formulario',
            html: '<ul class="text-left">' + errores.map(error => `<li>${error}</li>`).join('') + '</ul>',
            confirmButtonText: 'Entendido'
        });
        return;
    }
    
    const formData = $(this).serialize();
    const submitBtn = $(this).find('button[type="submit"]');
    const originalText = submitBtn.html();
    
    // Estado de loading
    submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Registrando...').prop('disabled', true);
    
    $.post(`${LeadsSystem.base_url}/leads/guardar`, formData, function(res) {
        if (res.success) {
            // Cerrar modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('leadModal'));
            modal.hide();
            
            // Agregar lead al kanban
            LeadsSystem.agregarLeadAKanban(res);
            
            // Limpiar formulario
            LeadsSystem.limpiarFormularioLead();
            
            Swal.fire({
                icon: 'success',
                title: '¡Lead registrado!',
                text: 'El lead se ha registrado correctamente',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true
            });
            
        } else {
            Swal.fire('Error', res.message, 'error');
        }
    }, 'json').fail(function() {
        Swal.fire('Error', 'Error de conexión al registrar lead', 'error');
    }).always(function() {
        submitBtn.html(originalText).prop('disabled', false);
    });

// =====================================================
// UTILIDADES DE KANBAN
// =====================================================

LeadsSystem.agregarLeadAKanban = function(leadData) {
    const etapa = leadData.idetapa || 1;
    const column = $(`#leads-container-${etapa}`);
    
    const leadCard = $(`
        <div class="kanban-card bg-white rounded-lg shadow-md mb-4 p-4 cursor-pointer transition-all duration-300 hover:shadow-xl hover:transform hover:scale-[1.02] border-l-4" 
             id="kanban-card-${leadData.idlead}"
             data-id="${leadData.idlead}"
             data-etapa="${etapa}"
             style="border-left-color: #007bff;"
             onclick="LeadsSystem.abrirModalDetalle(${leadData.idlead})">
             
            <div class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-6 h-6 flex items-center justify-center font-bold shadow-lg d-none" id="badge-tareas-${leadData.idlead}">
                0
            </div>
            
            <div class="lead-header mb-3">
                <div class="card-title text-sm font-bold text-gray-900 mb-1 flex items-center justify-between">
                    <span>${leadData.nombres} ${leadData.apellidos}</span>
                    <div class="flex items-center space-x-1">
                        <span class="w-2 h-2 rounded-full bg-green-400" title="Lead activo"></span>
                    </div>
                </div>
                
                <div class="card-info text-xs text-gray-600 space-y-1">
                    <div class="flex items-center">
                        <i class="fas fa-phone text-blue-500 w-4"></i>
                        <span class="ml-1">${leadData.telefono || ''}</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-envelope text-green-500 w-4"></i>
                        <span class="ml-1 truncate">${leadData.correo || ''}</span>
                    </div>
                </div>
            </div>

            <div class="tareas-preview border-t pt-2 mt-2" id="tareas-info-${leadData.idlead}">
                <div class="flex items-center justify-between text-xs text-gray-500">
                    <span id="tareas-resumen-${leadData.idlead}">📋 Clic para ver tareas</span>
                    <span class="text-blue-500">→ Gestionar</span>
                </div>
            </div>
        </div>
    `);
    
    column.append(leadCard);
    LeadsSystem.actualizarContadoresEtapas();
};

// =====================================================
// MODAL DE DETALLE MEJORADO
// =====================================================
LeadsSystem.abrirModalDetalle = function(idlead) {
    LeadsSystem.leadActual = idlead;
    
    // Mostrar modal
    const modal = new bootstrap.Modal(document.getElementById('modalLeadDetalle'));
    modal.show();
    
    // Cargar datos
    LeadsSystem.cargarDatosLead(idlead);
    LeadsSystem.cargarTareasLead(idlead);
    
    // Configurar formulario de tarea
    $('#tarea-idlead-inline').val(idlead);
    
    // Fecha por defecto para nueva tarea
    const manana = new Date();
    manana.setDate(manana.getDate() + 1);
    manana.setHours(9, 0, 0, 0);
    $('input[name="fecha_inicio"]').val(manana.toISOString().slice(0, 16));
};

LeadsSystem.cargarDatosLead = function(idlead) {
    // Verificar cache primero
    if (LeadsSystem.leadCache.has(idlead)) {
        const cached = LeadsSystem.leadCache.get(idlead);
        $('#detalle-lead-content').html(cached.html);
        LeadsSystem.configurarEventosDetalle();
        return;
    }
    
    $.ajax({
        url: `${LeadsSystem.base_url}/leads/detalle/${idlead}`,
        type: 'GET',
        dataType: 'json',
        success: function(res) {
            if (res.success) {
                $('#detalle-lead-content').html(res.html);
                
                // Actualizar badge de etapa
                if (res.lead && res.lead.etapa) {
                    $('#lead-etapa-badge').text(res.lead.etapa)
                        .removeClass('bg-light text-dark')
                        .addClass('bg-success text-white');
                }
                
                // Guardar en cache
                LeadsSystem.leadCache.set(idlead, {
                    html: res.html,
                    lead: res.lead,
                    timestamp: Date.now()
                });
                
                LeadsSystem.configurarEventosDetalle();
            }
        },
        error: function() {
            $('#detalle-lead-content').html(`
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> 
                    Error al cargar los detalles del lead
                </div>
            `);
        }
    });
};

// =====================================================
// GESTIÓN DE TAREAS INTEGRADA
// =====================================================
LeadsSystem.cargarTareasLead = function(idlead) {
    $.ajax({
        url: `${LeadsSystem.base_url}/leads/obtenerTareas/${idlead}`,
        type: 'GET',
        dataType: 'json',
        success: function(res) {
            if (res.success) {
                LeadsSystem.tareasLead = res.tareas || [];
                LeadsSystem.mostrarTareasEnPanel(LeadsSystem.tareasLead);
                LeadsSystem.actualizarContadorTareas();
                LeadsSystem.actualizarIndicadorCard(idlead, LeadsSystem.tareasLead);
            } else {
                LeadsSystem.tareasLead = [];
                LeadsSystem.mostrarTareasVacias();
            }
        },
        error: function() {
            LeadsSystem.tareasLead = [];
            LeadsSystem.mostrarTareasVacias();
        }
    });
};

LeadsSystem.mostrarTareasEnPanel = function(tareas) {
    const contenedor = $('#lista-tareas-lead');
    
    if (tareas.length === 0) {
        LeadsSystem.mostrarTareasVacias();
        return;
    }
    
    let html = '';
    tareas.forEach(tarea => {
        const prioridadColor = {
            'baja': 'success',
            'media': 'warning', 
            'alta': 'orange',
            'urgente': 'danger'
        };
        
        const estadoIcon = tarea.estado === 'Completada' ? 'fas fa-check-circle text-success' : 'far fa-circle text-muted';
        const isCompleted = tarea.estado === 'Completada';
        
        html += `
            <div class="tarea-item border rounded p-3 mb-2 ${isCompleted ? 'bg-light' : 'bg-white'}" data-tarea-id="${tarea.idtarea}">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center mb-1">
                            <i class="${estadoIcon} me-2 cursor-pointer" onclick="LeadsSystem.toggleTareaEstado(${tarea.idtarea})"></i>
                            <h6 class="mb-0 ${isCompleted ? 'text-decoration-line-through text-muted' : ''}">${tarea.titulo || tarea.descripcion}</h6>
                            <span class="badge bg-${prioridadColor[tarea.prioridad] || 'secondary'} ms-2">${tarea.prioridad}</span>
                        </div>
                        ${tarea.descripcion && tarea.descripcion !== tarea.titulo ? `<p class="small text-muted mb-1">${tarea.descripcion}</p>` : ''}
                        <small class="text-muted">
                            <i class="fas fa-clock"></i> ${LeadsSystem.formatearFecha(tarea.fecha_inicio)}
                            ${tarea.tipo_tarea ? `| <i class="fas fa-tag"></i> ${tarea.tipo_tarea}` : ''}
                            ${tarea.fecha_completado ? `| <i class="fas fa-check"></i> Completada: ${LeadsSystem.formatearFecha(tarea.fecha_completado)}` : ''}
                        </small>
                    </div>
                </div>
            </div>
        `;
    });
    
    contenedor.html(html);
};

LeadsSystem.mostrarTareasVacias = function() {
    $('#lista-tareas-lead').html(`
        <div class="text-center text-muted py-4">
            <i class="fas fa-tasks fa-3x mb-3 opacity-50"></i>
            <h6>No hay tareas registradas</h6>
            <p class="small">Utiliza el formulario de arriba para crear la primera tarea</p>
        </div>
    `);
};

// =====================================================
// EVENTOS Y VALIDACIONES
// =====================================================
LeadsSystem.inicializarEventos = function() {
    // Formulario de tarea inline
    $(document).off('submit', '#tareaFormInline').on('submit', '#tareaFormInline', LeadsSystem.manejarCreacionTarea);
    
    // Eventos de cards (delegados)
    $(document).off('click', '.kanban-card').on('click', '.kanban-card', function(e) {
        e.preventDefault();
        const idlead = $(this).data('id');
        LeadsSystem.abrirModalDetalle(idlead);
    });
};

LeadsSystem.manejarCreacionTarea = function(e) {
    e.preventDefault();
    
    const descripcion = $(this).find('input[name="descripcion"]').val().trim();
    
    if (descripcion.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Descripción requerida',
            text: 'Por favor ingresa una descripción para la tarea'
        });
        return;
    }
    
    const submitBtn = $(this).find('button[type="submit"]');
    const originalText = submitBtn.html();
    submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Guardando...').prop('disabled', true);
    
    $.post(`${LeadsSystem.base_url}/leads/guardarTarea`, $(this).serialize(), function(res) {
        if (res.success) {
            $('#tareaFormInline')[0].reset();
            $('#contador-caracteres').text('0').removeClass().addClass('text-muted');
            
            LeadsSystem.cargarTareasLead(LeadsSystem.leadActual);
            
            Swal.fire({
                icon: 'success',
                title: '¡Tarea creada!',
                text: res.message,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true
            });
            
        } else {
            Swal.fire('Error', res.message, 'error');
        }
    }, 'json').fail(function() {
        Swal.fire('Error', 'Error de conexión', 'error');
    }).always(function() {
        submitBtn.html(originalText).prop('disabled', false);
    });
};

// =====================================================
// UTILIDADES
// =====================================================
LeadsSystem.formatearFecha = function(fechaStr) {
    if (!fechaStr) return 'Sin fecha';
    const fecha = new Date(fechaStr);
    return fecha.toLocaleDateString() + ' ' + fecha.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
};

LeadsSystem.actualizarContadoresEtapas = function() {
    document.querySelectorAll('.kanban-column').forEach(column => {
        const etapaId = column.dataset.etapa;
        const leads = column.querySelectorAll('.kanban-card').length;
        const contador = document.getElementById(`count-${etapaId}`);
        if (contador) contador.textContent = leads;
    });
};

LeadsSystem.configurarEventosDetalle = function() {
    // Eventos específicos del modal de detalle original
    $('#btnDesistirLead').off('click').on('click', function() {
        const idlead = LeadsSystem.leadActual;
        
        Swal.fire({
            title: '¿Descartar lead?',
            text: 'Esta acción no se puede deshacer',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, descartar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post(`${LeadsSystem.base_url}/leads/eliminar`, { idlead }, function(res){
                    if(res.success){
                        Swal.fire('¡Eliminado!', res.message, 'success');
                        const modal = bootstrap.Modal.getInstance(document.getElementById('modalLeadDetalle'));
                        modal.hide();
                        $(`#kanban-card-${idlead}`).remove();
                        LeadsSystem.actualizarContadoresEtapas();
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                }, 'json');
            }
        });
    });
};

// =====================================================
// FUNCIONES GLOBALES (para compatibilidad)
// =====================================================
window.abrirDetalleLeadModal = LeadsSystem.abrirModalDetalle;
window.LeadsSystem = LeadsSystem;