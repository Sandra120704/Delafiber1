/**
 * Chart.js Rounded Bar Charts Extension
 * Sistema Delafiber - Gráficos con barras redondeadas
 */

(function() {
    'use strict';

    // Verificar que Chart.js esté disponible
    if (typeof Chart === 'undefined') {
        console.warn('Chart.js no está disponible. Las barras redondeadas no funcionarán.');
        return;
    }

    // Plugin para barras redondeadas
    const roundedBarPlugin = {
        id: 'roundedBars',
        
        beforeDatasetDraw: function(chart, args, options) {
            const ctx = chart.ctx;
            const dataset = args.meta.dataset;
            
            if (chart.config.type === 'bar' && options.borderRadius) {
                ctx.save();
                
                // Aplicar bordes redondeados a las barras
                args.meta.data.forEach(function(bar, index) {
                    if (bar.active || !bar.skip) {
                        const x = bar.x;
                        const y = bar.y;
                        const base = bar.base;
                        const width = bar.width;
                        const height = Math.abs(y - base);
                        const borderRadius = Math.min(options.borderRadius, width / 2, height / 2);
                        
                        // Limpiar área del bar original
                        ctx.clearRect(x - width / 2, Math.min(y, base), width, height);
                        
                        // Dibujar barra redondeada
                        ctx.fillStyle = bar.options.backgroundColor;
                        drawRoundedBar(ctx, x - width / 2, Math.min(y, base), width, height, borderRadius);
                    }
                });
                
                ctx.restore();
            }
        }
    };

    /**
     * Dibujar barra con bordes redondeados
     */
    function drawRoundedBar(ctx, x, y, width, height, borderRadius) {
        ctx.beginPath();
        ctx.moveTo(x + borderRadius, y);
        ctx.lineTo(x + width - borderRadius, y);
        ctx.quadraticCurveTo(x + width, y, x + width, y + borderRadius);
        ctx.lineTo(x + width, y + height - borderRadius);
        ctx.quadraticCurveTo(x + width, y + height, x + width - borderRadius, y + height);
        ctx.lineTo(x + borderRadius, y + height);
        ctx.quadraticCurveTo(x, y + height, x, y + height - borderRadius);
        ctx.lineTo(x, y + borderRadius);
        ctx.quadraticCurveTo(x, y, x + borderRadius, y);
        ctx.closePath();
        ctx.fill();
    }

    // Registrar el plugin
    Chart.register(roundedBarPlugin);

    // Configuraciones predefinidas para gráficos de Delafiber
    Chart.defaults.font.family = "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif";
    Chart.defaults.font.size = 12;
    Chart.defaults.color = '#5a5c69';

    // Colores corporativos de Delafiber
    const delafiberColors = {
        primary: '#4e73df',
        success: '#1cc88a',
        info: '#36b9cc',
        warning: '#f6c23e',
        danger: '#e74a3b',
        secondary: '#858796',
        light: '#f8f9fc',
        dark: '#3a3b45'
    };

    // Configuración personalizada para gráficos de barras redondeadas
    const roundedBarDefaults = {
        borderRadius: 8,
        backgroundColor: delafiberColors.primary,
        borderColor: delafiberColors.primary,
        borderWidth: 0,
        borderSkipped: false
    };

    // Función helper para crear gráficos de barras redondeadas
    window.createRoundedBarChart = function(canvasId, data, options = {}) {
        const ctx = document.getElementById(canvasId);
        if (!ctx) {
            console.error('Canvas con ID "' + canvasId + '" no encontrado');
            return null;
        }

        const defaultOptions = {
            type: 'bar',
            data: {
                labels: data.labels || [],
                datasets: [{
                    data: data.values || [],
                    ...roundedBarDefaults,
                    ...data.dataset
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    roundedBars: {
                        borderRadius: 8
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: delafiberColors.secondary
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            color: delafiberColors.secondary
                        }
                    }
                },
                elements: {
                    bar: {
                        borderRadius: 8
                    }
                },
                ...options
            }
        };

        return new Chart(ctx, defaultOptions);
    };

    // Función helper para gráficos de métricas de fibra óptica
    window.createFiberMetricsChart = function(canvasId, metricsData) {
        const data = {
            labels: ['Conectividad', 'Velocidad', 'Latencia', 'Disponibilidad'],
            values: [
                metricsData.connectivity || 95,
                metricsData.speed || 88,
                metricsData.latency || 92,
                metricsData.availability || 99
            ],
            dataset: {
                backgroundColor: [
                    delafiberColors.success,
                    delafiberColors.info,
                    delafiberColors.warning,
                    delafiberColors.primary
                ]
            }
        };

        const options = {
            plugins: {
                title: {
                    display: true,
                    text: 'Métricas de Red - Fibra Óptica',
                    color: delafiberColors.dark,
                    font: {
                        size: 16,
                        weight: 'bold'
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ' + context.parsed.y + '%';
                        }
                    }
                }
            },
            scales: {
                y: {
                    max: 100,
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    }
                }
            }
        };

        return createRoundedBarChart(canvasId, data, options);
    };

    // Función helper para gráficos de crecimiento de clientes
    window.createClientGrowthChart = function(canvasId, growthData) {
        const data = {
            labels: growthData.months || ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'],
            values: growthData.clients || [15, 28, 35, 42, 58, 73],
            dataset: {
                backgroundColor: delafiberColors.primary,
                borderColor: delafiberColors.primary,
                borderWidth: 2
            }
        };

        const options = {
            plugins: {
                title: {
                    display: true,
                    text: 'Crecimiento de Clientes',
                    color: delafiberColors.dark,
                    font: {
                        size: 16,
                        weight: 'bold'
                    }
                }
            },
            scales: {
                y: {
                    ticks: {
                        callback: function(value) {
                            return value + ' clientes';
                        }
                    }
                }
            }
        };

        return createRoundedBarChart(canvasId, data, options);
    };

    // Función helper para gráficos de ingresos
    window.createRevenueChart = function(canvasId, revenueData) {
        const data = {
            labels: revenueData.periods || ['Q1', 'Q2', 'Q3', 'Q4'],
            values: revenueData.amounts || [45000, 52000, 48000, 61000],
            dataset: {
                backgroundColor: delafiberColors.success,
                borderColor: delafiberColors.success
            }
        };

        const options = {
            plugins: {
                title: {
                    display: true,
                    text: 'Ingresos por Trimestre',
                    color: delafiberColors.dark,
                    font: {
                        size: 16,
                        weight: 'bold'
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'S/ ' + context.parsed.y.toLocaleString('es-PE');
                        }
                    }
                }
            },
            scales: {
                y: {
                    ticks: {
                        callback: function(value) {
                            return 'S/ ' + value.toLocaleString('es-PE');
                        }
                    }
                }
            }
        };

        return createRoundedBarChart(canvasId, data, options);
    };

    // Exportar configuraciones y colores para uso global
    window.DelafiberCharts = {
        colors: delafiberColors,
        roundedBarDefaults: roundedBarDefaults,
        createRoundedBarChart: createRoundedBarChart,
        createFiberMetricsChart: createFiberMetricsChart,
        createClientGrowthChart: createClientGrowthChart,
        createRevenueChart: createRevenueChart
    };

    // Mensaje de confirmación de carga
    console.log('✅ Chart.js Rounded Bar Charts cargado correctamente - Sistema Delafiber');

})();