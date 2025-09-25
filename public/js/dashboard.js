/**
 * Dashboard JavaScript CONSOLIDADO - Funcionalidad completa del dashboard
 * Sistema Delafiber - Gestión de Fibra Óptica
 * 
 * CONSOLIDACIÓN: Combina funcionalidades originales + nuevas funcionalidades
 */

(function($) {
    'use strict';

    // Variables globales del dashboard
    let dashboardData = {};
    let refreshInterval = null;
    let charts = {};

    // =============================================
    // FUNCIONALIDADES ORIGINALES (Chart.js específicos)
    // =============================================
    
    /**
     * Inicializar gráficos originales del sistema
     * Mantiene compatibilidad con las vistas PHP existentes
     */
    function initializeOriginalCharts() {
        // Datos del Pipeline (original)
        if (typeof pipelineData !== 'undefined' && document.getElementById('pipelineChart')) {
            const pipelineCtx = document.getElementById('pipelineChart').getContext('2d');
            charts.pipeline = new Chart(pipelineCtx, {
                type: 'doughnut',
                data: {
                    labels: pipelineData.map(item => item.nombre),
                    datasets: [{
                        data: pipelineData.map(item => item.total),
                        backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }

        // Datos de Campañas (original)
        if (typeof campanasData !== 'undefined' && campanasData.length > 0 && document.getElementById('campanasChart')) {
            const campanasCtx = document.getElementById('campanasChart').getContext('2d');
            charts.campanas = new Chart(campanasCtx, {
                type: 'bar',
                data: {
                    labels: campanasData.map(item => item.nombre),
                    datasets: [{
                        label: 'Leads Generados',
                        data: campanasData.map(item => item.total_leads),
                        backgroundColor: '#4e73df',
                        borderColor: '#4e73df',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
    }

    // =============================================
    // FUNCIONALIDADES NUEVAS (Delafiber específicas)
    // =============================================

    // Inicialización cuando el documento esté listo
    $(document).ready(function() {
        // Primero inicializar gráficos originales
        initializeOriginalCharts();
        
        // Luego nuevas funcionalidades
        initializeDashboard();
        loadDashboardData();
        initializeCharts();
        initializeRealTimeUpdates();
        initializeWidgets();
    });

    /**
     * Inicializar dashboard principal
     */
    function initializeDashboard() {
        // Configurar tooltips
        $('[data-toggle="tooltip"]').tooltip();
        
        // Configurar popovers
        $('[data-toggle="popover"]').popover();
        
        // Inicializar refresh automático
        initializeAutoRefresh();
        
        // Inicializar filtros de fecha
        initializeDateFilters();
        
        // Configurar eventos de widgets
        setupWidgetEvents();
    }

    /**
     * Cargar datos del dashboard
     */
    function loadDashboardData() {
        $.ajax({
            url: baseUrl + 'dashboard/estadisticas',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    dashboardData = response.stats;
                    updateDashboardWidgets(dashboardData);
                    updateTimestamp(response.timestamp);
                }
            },
            error: function() {
                console.log('Error al cargar datos del dashboard');
                showDashboardError('Error al cargar los datos');
            }
        });
    }

    /**
     * Actualizar widgets del dashboard
     */
    function updateDashboardWidgets(data) {
        // Actualizar contadores principales
        updateCounterWidget('leads-hoy', data.leads_hoy, 'Leads Hoy');
        updateCounterWidget('clientes-activos', data.clientes_activos, 'Clientes Activos');
        updateCounterWidget('tareas-pendientes', data.tareas_pendientes, 'Tareas Pendientes');
        updateCounterWidget('ingresos-mes', 'S/ ' + data.ingresos_mes, 'Ingresos del Mes');
        
        // Actualizar métricas de calidad
        updateProgressWidget('conectividad', data.conectividad, 'Conectividad');
        updateProgressWidget('satisfaccion', data.satisfaccion, 'Satisfacción');
        
        // Actualizar badges de estado
        updateStatusBadges(data);
    }

    /**
     * Actualizar widget tipo contador
     */
    function updateCounterWidget(widgetId, value, label) {
        const widget = $('#' + widgetId);
        if (widget.length) {
            widget.find('.counter-value').text(value);
            widget.find('.counter-label').text(label);
            
            // Animación de actualización
            widget.addClass('updated');
            setTimeout(() => widget.removeClass('updated'), 1000);
        }
    }

    /**
     * Actualizar widget tipo progreso
     */
    function updateProgressWidget(widgetId, value, label) {
        const widget = $('#' + widgetId);
        if (widget.length) {
            const numericValue = parseInt(value.replace('%', ''));
            
            widget.find('.progress-bar').css('width', value).text(value);
            widget.find('.progress-label').text(label);
            
            // Cambiar color según el valor
            const progressBar = widget.find('.progress-bar');
            progressBar.removeClass('bg-success bg-warning bg-danger');
            
            if (numericValue >= 90) {
                progressBar.addClass('bg-success');
            } else if (numericValue >= 70) {
                progressBar.addClass('bg-warning');
            } else {
                progressBar.addClass('bg-danger');
            }
        }
    }

    /**
     * Actualizar badges de estado
     */
    function updateStatusBadges(data) {
        // Estado de la red
        const networkStatus = data.conectividad.replace('%', '') >= 95 ? 'Excelente' : 'Normal';
        $('#network-status').text(networkStatus)
            .removeClass('badge-success badge-warning badge-danger')
            .addClass(networkStatus === 'Excelente' ? 'badge-success' : 'badge-warning');
        
        // Estado del servicio
        const serviceStatus = data.satisfaccion.replace('%', '') >= 90 ? 'Óptimo' : 'Bueno';
        $('#service-status').text(serviceStatus)
            .removeClass('badge-success badge-warning badge-danger')
            .addClass(serviceStatus === 'Óptimo' ? 'badge-success' : 'badge-warning');
    }

    /**
     * Inicializar gráficos
     */
    function initializeCharts() {
        // Solo inicializar si Chart.js está disponible
        if (typeof Chart !== 'undefined') {
            initializeMainChart();
            initializeMetricsChart();
            initializeTrendChart();
        }
    }

    /**
     * Gráfico principal del dashboard
     */
    function initializeMainChart() {
        const ctx = document.getElementById('mainChart');
        if (ctx) {
            charts.main = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'],
                    datasets: [{
                        label: 'Clientes Nuevos',
                        data: [12, 19, 15, 25, 22, 30],
                        borderColor: '#4e73df',
                        backgroundColor: 'rgba(78, 115, 223, 0.1)',
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
    }

    /**
     * Gráfico de métricas
     */
    function initializeMetricsChart() {
        const ctx = document.getElementById('metricsChart');
        if (ctx) {
            charts.metrics = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Instalaciones', 'Soporte', 'Ventas'],
                    datasets: [{
                        data: [45, 30, 25],
                        backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc']
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }
    }

    /**
     * Gráfico de tendencias
     */
    function initializeTrendChart() {
        const ctx = document.getElementById('trendChart');
        if (ctx) {
            charts.trend = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'],
                    datasets: [{
                        label: 'Actividad',
                        data: [65, 78, 45, 88, 92, 34, 12],
                        backgroundColor: '#1cc88a'
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }
    }

    /**
     * Configurar actualizaciones en tiempo real
     */
    function initializeRealTimeUpdates() {
        // Actualizar cada 30 segundos
        refreshInterval = setInterval(function() {
            loadDashboardData();
        }, 30000);

        // Botón de refresh manual
        $('#refresh-dashboard').on('click', function() {
            $(this).addClass('fa-spin');
            loadDashboardData();
            
            setTimeout(() => {
                $(this).removeClass('fa-spin');
            }, 1000);
        });
    }

    /**
     * Configurar refresh automático
     */
    function initializeAutoRefresh() {
        // Toggle auto-refresh
        $('#auto-refresh').on('change', function() {
            if ($(this).is(':checked')) {
                if (!refreshInterval) {
                    initializeRealTimeUpdates();
                }
            } else {
                if (refreshInterval) {
                    clearInterval(refreshInterval);
                    refreshInterval = null;
                }
            }
        });
    }

    /**
     * Inicializar filtros de fecha
     */
    function initializeDateFilters() {
        $('.date-filter').on('change', function() {
            const filterValue = $(this).val();
            applyDateFilter(filterValue);
        });

        // Botones de período rápido
        $('.period-btn').on('click', function() {
            const period = $(this).data('period');
            $('.period-btn').removeClass('active');
            $(this).addClass('active');
            applyPeriodFilter(period);
        });
    }

    /**
     * Aplicar filtro de fecha
     */
    function applyDateFilter(filter) {
        // Implementar lógica de filtrado
        console.log('Aplicando filtro de fecha:', filter);
        // Recargar datos con filtro
        loadDashboardData();
    }

    /**
     * Aplicar filtro de período
     */
    function applyPeriodFilter(period) {
        console.log('Aplicando filtro de período:', period);
        // Implementar lógica de filtrado por período
        loadDashboardData();
    }

    /**
     * Configurar eventos de widgets
     */
    function setupWidgetEvents() {
        // Minimizar/maximizar widgets
        $('.widget-toggle').on('click', function() {
            const widget = $(this).closest('.card');
            const body = widget.find('.card-body');
            
            body.slideToggle(300);
            $(this).find('i').toggleClass('fa-minus fa-plus');
        });

        // Configuración de widgets
        $('.widget-config').on('click', function() {
            const widgetId = $(this).closest('.card').attr('id');
            openWidgetConfig(widgetId);
        });
    }

    /**
     * Abrir configuración de widget
     */
    function openWidgetConfig(widgetId) {
        // Implementar modal de configuración
        console.log('Abriendo configuración para widget:', widgetId);
    }

    /**
     * Inicializar widgets personalizables
     */
    function initializeWidgets() {
        // Hacer widgets arrastrables si jQuery UI está disponible
        if ($.fn.sortable) {
            $('.dashboard-widgets').sortable({
                handle: '.card-header',
                placeholder: 'widget-placeholder',
                update: function(event, ui) {
                    saveWidgetOrder();
                }
            });
        }
    }

    /**
     * Guardar orden de widgets
     */
    function saveWidgetOrder() {
        const order = $('.dashboard-widgets .card').map(function() {
            return $(this).attr('id');
        }).get();

        $.ajax({
            url: baseUrl + 'dashboard/guardar-orden-widgets',
            method: 'POST',
            data: {
                order: JSON.stringify(order),
                csrf_token: window.csrfHash
            },
            success: function(response) {
                if (response.success) {
                    showToast('Orden de widgets guardado', 'success');
                }
            }
        });
    }

    /**
     * Actualizar timestamp de última actualización
     */
    function updateTimestamp(timestamp) {
        $('#last-update').text('Última actualización: ' + formatTimestamp(timestamp));
    }

    /**
     * Formatear timestamp
     */
    function formatTimestamp(timestamp) {
        const date = new Date(timestamp);
        return date.toLocaleString('es-PE');
    }

    /**
     * Mostrar error en dashboard
     */
    function showDashboardError(message) {
        const errorHtml = `
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                ${message}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        `;
        
        $('#dashboard-alerts').html(errorHtml);
        
        // Auto-ocultar después de 5 segundos
        setTimeout(function() {
            $('.alert').fadeOut();
        }, 5000);
    }

    /**
     * Mostrar toast de notificación
     */
    function showToast(message, type = 'info') {
        const toastClass = type === 'success' ? 'bg-success' : type === 'error' ? 'bg-danger' : 'bg-info';
        const toastHtml = `
            <div class="toast ${toastClass} text-white" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999;">
                <div class="toast-body">
                    ${message}
                    <button type="button" class="btn-close btn-close-white float-end" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `;
        
        $('body').append(toastHtml);
        $('.toast:last').toast('show');
        
        // Auto-remover después de 3 segundos
        setTimeout(function() {
            $('.toast:last').fadeOut(300, function() {
                $(this).remove();
            });
        }, 3000);
    }

    // Limpiar intervalos al salir de la página
    $(window).on('beforeunload', function() {
        if (refreshInterval) {
            clearInterval(refreshInterval);
        }
    });

    // Variable global para baseUrl (debe definirse en el layout principal)
    if (typeof baseUrl === 'undefined') {
        window.baseUrl = window.location.origin + '/';
    }

})(jQuery);