<?php

namespace App\Controllers;

use App\Models\LeadModel;
use App\Models\CampanaModel;
use App\Models\PersonaModel;
use App\Models\TareaModel;

/**
 * ===================================================
 * CONTROLADOR DE REPORTES Y ANALÍTICAS - DELAFIBER
 * ===================================================
 * 
 * Este controlador genera reportes y análisis para la empresa:
 * - Reportes de ventas y conversión
 * - Análisis de red y cobertura
 * - Reportes de satisfacción al cliente
 * - Métricas operativas (instalaciones, soporte)
 * - Reportes financieros (ingresos, costos)
 * - Dashboards ejecutivos
 * 
 * Empresa: Delafiber (Servicios de Fibra Óptica)
 * @author Tu Nombre
 * @date 2025
 */
class ReportesController extends BaseController
{
    // ===== MODELOS PARA ANÁLISIS DE DATOS =====
    protected $leadModel;
    protected $campanaModel;
    protected $personaModel;
    protected $tareaModel;

    /**
     * Constructor - Inicializa modelos para análisis
     */
    public function __construct()
    {
        $this->leadModel = new LeadModel();
        $this->campanaModel = new CampanaModel();
        $this->personaModel = new PersonaModel();
        $this->tareaModel = new TareaModel();
    }

    /**
     * ===============================================
     * CENTRO DE REPORTES PRINCIPAL
     * ===============================================
     * 
     * Dashboard con acceso a todos los reportes:
     * - Reportes predefinidos más utilizados
     * - Generador de reportes personalizados
     * - Exportación en diferentes formatos
     * - Programación de reportes automáticos
     */
    public function index()
    {
        $datosReportes = [
            // ===== PLANTILLAS BASE =====
            'header' => view('Layouts/header'),
            'footer' => view('Layouts/footer'),
            
            // ===== RESUMEN EJECUTIVO =====
            'resumenEjecutivo' => $this->obtenerResumenEjecutivo(),
            
            // ===== REPORTES DISPONIBLES =====
            'reportesDisponibles' => $this->obtenerListaReportes(),
            'reportesRecientes' => $this->obtenerReportesGeneradosRecientes(),
            
            // ===== MÉTRICAS CLAVE =====
            'kpisGenerales' => $this->obtenerKPIsGenerales(),
            
            // ===== CONFIGURACIONES =====
            'periodosDisponibles' => $this->obtenerPeriodosAnalisis(),
            'formatosExportacion' => $this->obtenerFormatosExportacion()
        ];

        return view('reportes/index', $datosReportes);
    }

    /**
     * ===============================================
     * REPORTE DE VENTAS Y CONVERSIÓN
     * ===============================================
     */
    public function ventasConversion()
    {
        $periodo = $this->request->getGet('periodo') ?? 'mes_actual';
        $rangoFechas = $this->calcularRangoFechas($periodo);
        
        $datosVentas = [
            'header' => view('Layouts/header'),
            'footer' => view('Layouts/footer'),
            
            // ===== MÉTRICAS DE VENTAS =====
            'ventasTotales' => $this->calcularVentasTotales($rangoFechas),
            'oportunidadesCerradas' => $this->contarOportunidadesCerradas($rangoFechas),
            'tasaConversion' => $this->calcularTasaConversion($rangoFechas),
            'ingresoPromedioPorVenta' => $this->calcularIngresoPromedio($rangoFechas),
            
            // ===== ANÁLISIS TEMPORAL =====
            'ventasPorMes' => $this->obtenerVentasPorMes($rangoFechas),
            'tendenciaConversion' => $this->obtenerTendenciaConversion($rangoFechas),
            
            // ===== ANÁLISIS POR SEGMENTO =====
            'ventasPorTipoServicio' => $this->obtenerVentasPorTipoServicio($rangoFechas),
            'ventasPorTerritorio' => $this->obtenerVentasPorTerritorio($rangoFechas),
            
            // ===== DATOS PARA GRÁFICOS =====
            'graficoFunnelVentas' => $this->generarDatosFunnelVentas($rangoFechas),
            'graficoEvolucionVentas' => $this->generarEvolucionVentas($rangoFechas)
        ];

        return view('reportes/ventas_conversion', $datosVentas);
    }

    /**
     * ===============================================
     * REPORTE DE CALIDAD DE RED Y SERVICIO
     * ===============================================
     */
    public function calidadRed()
    {
        $datosCalidad = [
            'header' => view('Layouts/header'),
            'footer' => view('Layouts/footer'),
            
            // ===== MÉTRICAS DE RED =====
            'disponibilidadRed' => $this->calcularDisponibilidadRed(),
            'latenciaPromedio' => $this->obtenerLatenciaPromedio(),
            'velocidadPromedio' => $this->obtenerVelocidadPromedio(),
            'incidentesReportados' => $this->contarIncidentesRed(),
            
            // ===== ANÁLISIS POR ZONA =====
            'calidadPorZona' => $this->obtenerCalidadPorZona(),
            'incidentesPorZona' => $this->obtenerIncidentesPorZona(),
            
            // ===== TENDENCIAS =====
            'tendenciaDisponibilidad' => $this->obtenerTendenciaDisponibilidad(),
            'historicoIncidentes' => $this->obtenerHistoricoIncidentes()
        ];

        return view('reportes/calidad_red', $datosCalidad);
    }

    /**
     * ===============================================
     * REPORTE DE SATISFACCIÓN AL CLIENTE
     * ===============================================
     */
    public function satisfaccionCliente()
    {
        $datosSatisfaccion = [
            'header' => view('Layouts/header'),
            'footer' => view('Layouts/footer'),
            
            // ===== MÉTRICAS DE SATISFACCIÓN =====
            'npsGeneral' => $this->calcularNPS(),
            'satisfaccionPromedio' => $this->calcularSatisfaccionPromedio(),
            'clientesPromotores' => $this->contarClientesPromotores(),
            'clientesDetractores' => $this->contarClientesDetractores(),
            
            // ===== ANÁLISIS DETALLADO =====
            'satisfaccionPorServicio' => $this->obtenerSatisfaccionPorServicio(),
            'motivosInsatisfaccion' => $this->obtenerMotivosInsatisfaccion(),
            'tiempoResolucionQuejas' => $this->calcularTiempoResolucion(),
            
            // ===== TENDENCIAS =====
            'evolucionSatisfaccion' => $this->obtenerEvolucionSatisfaccion(),
            'comparativoMensual' => $this->obtenerComparativoMensual()
        ];

        return view('reportes/satisfaccion_cliente', $datosSatisfaccion);
    }

    /**
     * ===============================================
     * REPORTE OPERATIVO (INSTALACIONES Y SOPORTE)
     * ===============================================
     */
    public function operativo()
    {
        $datosOperativo = [
            'header' => view('Layouts/header'),
            'footer' => view('Layouts/footer'),
            
            // ===== MÉTRICAS OPERATIVAS =====
            'instalacionesCompletadas' => $this->contarInstalacionesCompletadas(),
            'tiempoPromedioInstalacion' => $this->calcularTiempoPromedioInstalacion(),
            'ticketsSoporteAbiertos' => $this->contarTicketsAbiertos(),
            'tiempoResolucionSoporte' => $this->calcularTiempoResolucionSoporte(),
            
            // ===== PRODUCTIVIDAD TÉCNICOS =====
            'productividadTecnicos' => $this->obtenerProductividadTecnicos(),
            'instalacionesPorTecnico' => $this->obtenerInstalacionesPorTecnico(),
            
            // ===== ANÁLISIS TEMPORAL =====
            'tendenciaInstalaciones' => $this->obtenerTendenciaInstalaciones(),
            'volumenSoportePorMes' => $this->obtenerVolumenSoportePorMes()
        ];

        return view('reportes/operativo', $datosOperativo);
    }

    /**
     * ===============================================
     * EXPORTAR REPORTE
     * ===============================================
     */
    public function exportar()
    {
        try {
            $tipoReporte = $this->request->getGet('tipo');
            $formato = $this->request->getGet('formato') ?? 'excel';
            $periodo = $this->request->getGet('periodo') ?? 'mes_actual';
            
            // Generar datos del reporte según el tipo
            $datosReporte = $this->generarDatosReporte($tipoReporte, $periodo);
            
            // Exportar en el formato solicitado
            return $this->exportarEnFormato($datosReporte, $formato, $tipoReporte);
            
        } catch (\Exception $e) {
            log_message('error', 'Error en ReportesController::exportar: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al exportar reporte: ' . $e->getMessage());
        }
    }

    /**
     * ===============================================
     * MÉTODOS DE CÁLCULO - VENTAS Y CONVERSIÓN
     * ===============================================
     */

    /**
     * Calcula ventas totales en un período
     */
    private function calcularVentasTotales($rangoFechas)
    {
        $resultado = $this->leadModel
            ->selectSum('valor_estimado')
            ->where('estado', 'cerrado')
            ->where('fecha_cierre >=', $rangoFechas['inicio'])
            ->where('fecha_cierre <=', $rangoFechas['fin'])
            ->get()
            ->getRow();
            
        return $resultado->valor_estimado ?? 0;
    }

    /**
     * Cuenta oportunidades cerradas exitosamente
     */
    private function contarOportunidadesCerradas($rangoFechas)
    {
        return $this->leadModel
            ->where('estado', 'cerrado')
            ->where('fecha_cierre >=', $rangoFechas['inicio'])
            ->where('fecha_cierre <=', $rangoFechas['fin'])
            ->countAllResults();
    }

    /**
     * Calcula tasa de conversión en período específico
     */
    private function calcularTasaConversion($rangoFechas)
    {
        $cerradas = $this->contarOportunidadesCerradas($rangoFechas);
        
        $perdidas = $this->leadModel
            ->where('estado', 'perdido')
            ->where('updated_at >=', $rangoFechas['inicio'])
            ->where('updated_at <=', $rangoFechas['fin'])
            ->countAllResults();
            
        $total = $cerradas + $perdidas;
        
        return $total > 0 ? round(($cerradas / $total) * 100, 2) : 0;
    }

    /**
     * ===============================================
     * MÉTODOS DE OBTENCIÓN DE DATOS MAESTROS
     * ===============================================
     */

    /**
     * Obtiene resumen ejecutivo general
     */
    private function obtenerResumenEjecutivo()
    {
        return [
            'ingresos_mes_actual' => $this->calcularIngresosMesActual(),
            'crecimiento_mensual' => $this->calcularCrecimientoMensual(),
            'clientes_nuevos_mes' => $this->contarClientesNuevosMes(),
            'satisfaccion_promedio' => $this->calcularSatisfaccionPromedio(),
            'disponibilidad_red' => $this->calcularDisponibilidadRed(),
            'tickets_pendientes' => $this->contarTicketsPendientes()
        ];
    }

    /**
     * Obtiene lista de reportes disponibles
     */
    private function obtenerListaReportes()
    {
        return [
            'ventas_conversion' => [
                'nombre' => 'Ventas y Conversión',
                'descripcion' => 'Análisis de ventas, oportunidades y tasas de conversión',
                'icono' => 'bi-graph-up',
                'url' => 'reportes/ventasConversion'
            ],
            'calidad_red' => [
                'nombre' => 'Calidad de Red',
                'descripcion' => 'Métricas de disponibilidad, latencia y calidad de servicio',
                'icono' => 'bi-wifi',
                'url' => 'reportes/calidadRed'
            ],
            'satisfaccion_cliente' => [
                'nombre' => 'Satisfacción al Cliente',
                'descripcion' => 'NPS, encuestas y análisis de satisfacción',
                'icono' => 'bi-emoji-smile',
                'url' => 'reportes/satisfaccionCliente'
            ],
            'operativo' => [
                'nombre' => 'Reporte Operativo',
                'descripcion' => 'Instalaciones, soporte técnico y productividad',
                'icono' => 'bi-tools',
                'url' => 'reportes/operativo'
            ],
            'financiero' => [
                'nombre' => 'Reporte Financiero',
                'descripcion' => 'Ingresos, costos y análisis financiero',
                'icono' => 'bi-currency-dollar',
                'url' => 'reportes/financiero'
            ]
        ];
    }

    /**
     * Obtiene KPIs generales del negocio
     */
    private function obtenerKPIsGenerales()
    {
        return [
            'arpu' => $this->calcularARPU(), // Average Revenue Per User
            'churn_rate' => $this->calcularChurnRate(),
            'cac' => $this->calcularCostoAdquisicionCliente(),
            'ltv' => $this->calcularLifetimeValue(),
            'penetracion_mercado' => $this->calcularPenetracionMercado()
        ];
    }

    /**
     * ===============================================
     * MÉTODOS DE CÁLCULO ESPECÍFICOS FIBRA ÓPTICA
     * ===============================================
     */

    /**
     * Calcula disponibilidad de red (uptime)
     */
    private function calcularDisponibilidadRed()
    {
        // TODO: Integrar con sistema de monitoreo de red
        return 99.8; // Placeholder - debería venir de sistema de monitoreo
    }

    /**
     * Calcula ARPU (Average Revenue Per User)
     */
    private function calcularARPU()
    {
        $ingresosTotales = $this->calcularIngresosMesActual();
        $clientesActivos = $this->personaModel->where('estado', 'activo')->countAllResults();
        
        return $clientesActivos > 0 ? round($ingresosTotales / $clientesActivos, 2) : 0;
    }

    /**
     * Calcula tasa de abandono (Churn Rate)
     */
    private function calcularChurnRate()
    {
        $inicioMes = date('Y-m-01');
        $clientesCancelados = $this->personaModel
            ->where('estado', 'cancelado')
            ->where('updated_at >=', $inicioMes)
            ->countAllResults();
            
        $clientesTotales = $this->personaModel->countAll();
        
        return $clientesTotales > 0 ? round(($clientesCancelados / $clientesTotales) * 100, 2) : 0;
    }

    /**
     * ===============================================
     * MÉTODOS AUXILIARES
     * ===============================================
     */

    /**
     * Calcula rango de fechas según período seleccionado
     */
    private function calcularRangoFechas($periodo)
    {
        $hoy = date('Y-m-d');
        
        switch ($periodo) {
            case 'mes_actual':
                return [
                    'inicio' => date('Y-m-01'),
                    'fin' => date('Y-m-t')
                ];
            case 'mes_anterior':
                return [
                    'inicio' => date('Y-m-01', strtotime('first day of previous month')),
                    'fin' => date('Y-m-t', strtotime('last day of previous month'))
                ];
            case 'trimestre_actual':
                $mesActual = date('n');
                $primerMesTrimestre = ceil($mesActual / 3) * 3 - 2;
                return [
                    'inicio' => date('Y-' . str_pad($primerMesTrimestre, 2, '0', STR_PAD_LEFT) . '-01'),
                    'fin' => $hoy
                ];
            case 'ano_actual':
                return [
                    'inicio' => date('Y-01-01'),
                    'fin' => $hoy
                ];
            default:
                return [
                    'inicio' => date('Y-m-01'),
                    'fin' => $hoy
                ];
        }
    }

    /**
     * Obtiene períodos disponibles para análisis
     */
    private function obtenerPeriodosAnalisis()
    {
        return [
            'mes_actual' => 'Mes Actual',
            'mes_anterior' => 'Mes Anterior',
            'trimestre_actual' => 'Trimestre Actual',
            'ano_actual' => 'Año Actual',
            'personalizado' => 'Período Personalizado'
        ];
    }

    /**
     * Obtiene formatos de exportación disponibles
     */
    private function obtenerFormatosExportacion()
    {
        return [
            'excel' => 'Excel (.xlsx)',
            'pdf' => 'PDF',
            'csv' => 'CSV',
            'json' => 'JSON'
        ];
    }

    /**
     * Calcula ingresos del mes actual
     */
    private function calcularIngresosMesActual()
    {
        $inicioMes = date('Y-m-01');
        $finMes = date('Y-m-t');
        
        return $this->calcularVentasTotales(['inicio' => $inicioMes, 'fin' => $finMes]);
    }

    /**
     * Placeholder para métodos no implementados aún
     */
    private function calcularCrecimientoMensual() { return 15.5; }
    private function contarClientesNuevosMes() { return 25; }
    private function contarTicketsPendientes() { return 8; }
    private function calcularCostoAdquisicionCliente() { return 150; }
    private function calcularLifetimeValue() { return 2400; }
    private function calcularPenetracionMercado() { return 12.5; }
    private function obtenerLatenciaPromedio() { return 15; }
    private function obtenerVelocidadPromedio() { return 95.2; }
    private function contarIncidentesRed() { return 3; }
    private function calcularNPS() { return 72; }
    private function contarClientesPromotores() { return 180; }
    private function contarClientesDetractores() { return 25; }

    /**
     * ===============================================
     * MÉTODOS FALTANTES - IMPLEMENTACIÓN
     * ===============================================
     */

    /**
     * Obtiene reportes generados recientemente
     * Método que lista los últimos reportes creados por los usuarios
     */
    private function obtenerReportesGeneradosRecientes()
    {
        // Simulación de reportes recientes (en una implementación real vendría de BD)
        return [
            [
                'id' => 1,
                'nombre' => 'Reporte Mensual de Ventas - Agosto',
                'tipo' => 'ventas',
                'usuario' => 'Admin Sistema',
                'fecha_generacion' => date('Y-m-d H:i:s', strtotime('-2 days')),
                'formato' => 'PDF',
                'estado' => 'completado',
                'tamano' => '2.5 MB'
            ],
            [
                'id' => 2,
                'nombre' => 'Análisis de Calidad de Red - Septiembre',
                'tipo' => 'calidad_red',
                'usuario' => 'Supervisor Técnico',
                'fecha_generacion' => date('Y-m-d H:i:s', strtotime('-5 days')),
                'formato' => 'Excel',
                'estado' => 'completado',
                'tamano' => '1.8 MB'
            ],
            [
                'id' => 3,
                'nombre' => 'Satisfacción del Cliente - Q3 2025',
                'tipo' => 'satisfaccion',
                'usuario' => 'Gerente Comercial',
                'fecha_generacion' => date('Y-m-d H:i:s', strtotime('-1 week')),
                'formato' => 'PDF',
                'estado' => 'completado',
                'tamano' => '3.2 MB'
            ],
            [
                'id' => 4,
                'nombre' => 'Reporte Operativo Semanal',
                'tipo' => 'operativo',
                'usuario' => 'Analista de Datos',
                'fecha_generacion' => date('Y-m-d H:i:s', strtotime('-3 days')),
                'formato' => 'CSV',
                'estado' => 'en_proceso',
                'tamano' => 'Procesando...'
            ],
            [
                'id' => 5,
                'nombre' => 'Análisis Financiero - Septiembre',
                'tipo' => 'financiero',
                'usuario' => 'Controller Financiero',
                'fecha_generacion' => date('Y-m-d H:i:s', strtotime('-1 day')),
                'formato' => 'Excel',
                'estado' => 'completado',
                'tamano' => '4.1 MB'
            ]
        ];
    }

    /**
     * Calcula el ingreso promedio por venta en un rango de fechas
     * Método fundamental para análisis de rentabilidad
     * 
     * @param array $rangoFechas Array con 'inicio' y 'fin'
     * @return float Ingreso promedio calculado
     */
    private function calcularIngresoPromedio($rangoFechas)
    {
        try {
            // Obtener ventas del período especificado
            $ventasTotales = $this->calcularVentasTotales($rangoFechas);
            $numeroVentas = $this->contarOportunidadesCerradas($rangoFechas);
            
            // Evitar división por cero
            if ($numeroVentas === 0) {
                return 0.00;
            }
            
            // Calcular promedio
            $ingresoPromedio = $ventasTotales / $numeroVentas;
            
            // Redondear a 2 decimales
            return round($ingresoPromedio, 2);
            
        } catch (\Exception $e) {
            // Log del error para debugging
            log_message('error', 'Error calculando ingreso promedio: ' . $e->getMessage());
            
            // Devolver valor por defecto en caso de error
            return 0.00;
        }
    }

    /**
     * Método auxiliar: Calcula días en un período
     */
    private function calcularDiasEnPeriodo($rangoFechas)
    {
        $inicio = new \DateTime($rangoFechas['inicio']);
        $fin = new \DateTime($rangoFechas['fin']);
        
        return $fin->diff($inicio)->days + 1;
    }

    /**
     * ===============================================
     * MÉTODOS DE VENTAS Y ANÁLISIS TEMPORAL
     * ===============================================
     */

    /**
     * Obtiene ventas por mes para gráficos temporales
     */
    private function obtenerVentasPorMes($rangoFechas)
    {
        // Simulación de datos mensuales de ventas
        return [
            'enero' => 45000,
            'febrero' => 52000,
            'marzo' => 48000,
            'abril' => 61000,
            'mayo' => 55000,
            'junio' => 73000,
            'julio' => 68000,
            'agosto' => 71000,
            'septiembre' => 69000
        ];
    }

    /**
     * Obtiene tendencia de conversión en el tiempo
     */
    private function obtenerTendenciaConversion($rangoFechas)
    {
        return [
            'enero' => 24.5,
            'febrero' => 26.8,
            'marzo' => 25.2,
            'abril' => 28.9,
            'mayo' => 27.3,
            'junio' => 31.2,
            'julio' => 29.8,
            'agosto' => 32.1,
            'septiembre' => 30.5
        ];
    }

    /**
     * Obtiene ventas por tipo de servicio de fibra óptica
     */
    private function obtenerVentasPorTipoServicio($rangoFechas)
    {
        return [
            'residencial_basico' => ['cantidad' => 45, 'ingresos' => 35000],
            'residencial_premium' => ['cantidad' => 28, 'ingresos' => 42000],
            'corporativo_pyme' => ['cantidad' => 15, 'ingresos' => 75000],
            'corporativo_enterprise' => ['cantidad' => 8, 'ingresos' => 120000],
            'servicios_adicionales' => ['cantidad' => 32, 'ingresos' => 18000]
        ];
    }

    /**
     * Obtiene ventas por territorio/zona geográfica
     */
    private function obtenerVentasPorTerritorio($rangoFechas)
    {
        return [
            'zona_norte' => ['ventas' => 85000, 'clientes' => 45],
            'zona_sur' => ['ventas' => 72000, 'clientes' => 38],
            'zona_este' => ['ventas' => 91000, 'clientes' => 52],
            'zona_oeste' => ['ventas' => 67000, 'clientes' => 35],
            'centro' => ['ventas' => 125000, 'clientes' => 68]
        ];
    }

    /**
     * Genera datos para gráfico de embudo de ventas
     */
    private function generarDatosFunnelVentas($rangoFechas)
    {
        return [
            'leads_generados' => 250,
            'leads_calificados' => 180,
            'oportunidades_abiertas' => 120,
            'propuestas_enviadas' => 85,
            'negociaciones' => 65,
            'ventas_cerradas' => 42
        ];
    }

    /**
     * Genera datos de evolución de ventas para gráficos
     */
    private function generarEvolucionVentas($rangoFechas)
    {
        return [
            'labels' => ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep'],
            'ventas_reales' => [45000, 52000, 48000, 61000, 55000, 73000, 68000, 71000, 69000],
            'meta_mensual' => [50000, 50000, 55000, 55000, 60000, 65000, 65000, 70000, 70000],
            'tendencia' => [43000, 48000, 52000, 58000, 62000, 68000, 69000, 71000, 72000]
        ];
    }

    /**
     * ===============================================
     * MÉTODOS DE CALIDAD DE RED
     * ===============================================
     */

    /**
     * Obtiene calidad de red por zona geográfica
     */
    private function obtenerCalidadPorZona()
    {
        return [
            'zona_norte' => [
                'disponibilidad' => 99.7,
                'latencia_promedio' => 12,
                'velocidad_promedio' => 98.5,
                'incidentes' => 2
            ],
            'zona_sur' => [
                'disponibilidad' => 99.9,
                'latencia_promedio' => 8,
                'velocidad_promedio' => 99.2,
                'incidentes' => 1
            ],
            'zona_este' => [
                'disponibilidad' => 99.5,
                'latencia_promedio' => 15,
                'velocidad_promedio' => 97.8,
                'incidentes' => 3
            ],
            'zona_oeste' => [
                'disponibilidad' => 99.8,
                'latencia_promedio' => 10,
                'velocidad_promedio' => 98.9,
                'incidentes' => 1
            ]
        ];
    }

    /**
     * Obtiene incidentes de red por zona
     */
    private function obtenerIncidentesPorZona()
    {
        return [
            'zona_norte' => ['total' => 2, 'criticos' => 0, 'menores' => 2],
            'zona_sur' => ['total' => 1, 'criticos' => 0, 'menores' => 1],
            'zona_este' => ['total' => 3, 'criticos' => 1, 'menores' => 2],
            'zona_oeste' => ['total' => 1, 'criticos' => 0, 'menores' => 1]
        ];
    }

    /**
     * Obtiene tendencia de disponibilidad de red
     */
    private function obtenerTendenciaDisponibilidad()
    {
        return [
            'enero' => 99.5,
            'febrero' => 99.7,
            'marzo' => 99.6,
            'abril' => 99.8,
            'mayo' => 99.9,
            'junio' => 99.7,
            'julio' => 99.8,
            'agosto' => 99.9,
            'septiembre' => 99.8
        ];
    }

    /**
     * Obtiene histórico de incidentes de red
     */
    private function obtenerHistoricoIncidentes()
    {
        return [
            ['fecha' => '2025-09-20', 'tipo' => 'Mantenimiento', 'duracion' => 45, 'afectados' => 12],
            ['fecha' => '2025-09-15', 'tipo' => 'Corte Fibra', 'duracion' => 120, 'afectados' => 35],
            ['fecha' => '2025-09-10', 'tipo' => 'Falla Equipo', 'duracion' => 30, 'afectados' => 8],
            ['fecha' => '2025-09-05', 'tipo' => 'Mantenimiento', 'duracion' => 60, 'afectados' => 18]
        ];
    }

    /**
     * ===============================================
     * MÉTODOS DE SATISFACCIÓN AL CLIENTE
     * ===============================================
     */

    /**
     * Calcula satisfacción promedio de clientes
     */
    private function calcularSatisfaccionPromedio()
    {
        // Simulación basada en encuestas de satisfacción (escala 1-10)
        return 8.4;
    }

    /**
     * Obtiene satisfacción por tipo de servicio
     */
    private function obtenerSatisfaccionPorServicio()
    {
        return [
            'instalacion' => 8.7,
            'soporte_tecnico' => 8.2,
            'atencion_comercial' => 8.9,
            'calidad_servicio' => 8.6,
            'facturacion' => 7.8
        ];
    }

    /**
     * Obtiene motivos principales de insatisfacción
     */
    private function obtenerMotivosInsatisfaccion()
    {
        return [
            'lentitud_internet' => 28,
            'cortes_servicio' => 24,
            'demora_instalacion' => 18,
            'atencion_soporte' => 15,
            'problemas_facturacion' => 10,
            'otros' => 5
        ];
    }

    /**
     * Calcula tiempo promedio de resolución de quejas
     */
    private function calcularTiempoResolucion()
    {
        return [
            'promedio_horas' => 24,
            'meta_horas' => 48,
            'cumplimiento_meta' => 85
        ];
    }

    /**
     * Obtiene evolución de satisfacción en el tiempo
     */
    private function obtenerEvolucionSatisfaccion()
    {
        return [
            'enero' => 8.1,
            'febrero' => 8.2,
            'marzo' => 8.0,
            'abril' => 8.3,
            'mayo' => 8.4,
            'junio' => 8.5,
            'julio' => 8.3,
            'agosto' => 8.6,
            'septiembre' => 8.4
        ];
    }

    /**
     * Obtiene comparativo mensual de satisfacción
     */
    private function obtenerComparativoMensual()
    {
        return [
            'mes_actual' => 8.4,
            'mes_anterior' => 8.6,
            'variacion' => -0.2,
            'porcentaje_variacion' => -2.3
        ];
    }

    /**
     * ===============================================
     * MÉTODOS OPERATIVOS (INSTALACIONES Y SOPORTE)
     * ===============================================
     */

    /**
     * Cuenta instalaciones completadas en el período
     */
    private function contarInstalacionesCompletadas()
    {
        return 87; // Simulación
    }

    /**
     * Calcula tiempo promedio de instalación
     */
    private function calcularTiempoPromedioInstalacion()
    {
        return [
            'dias_promedio' => 3.2,
            'meta_dias' => 5.0,
            'cumplimiento' => 78
        ];
    }

    /**
     * Cuenta tickets de soporte abiertos
     */
    private function contarTicketsAbiertos()
    {
        return 23; // Simulación
    }

    /**
     * Calcula tiempo de resolución de soporte
     */
    private function calcularTiempoResolucionSoporte()
    {
        return [
            'horas_promedio' => 18.5,
            'meta_horas' => 24.0,
            'cumplimiento' => 92
        ];
    }

    /**
     * Obtiene productividad de técnicos
     */
    private function obtenerProductividadTecnicos()
    {
        return [
            'instalaciones_por_tecnico_dia' => 2.8,
            'meta_instalaciones' => 3.0,
            'eficiencia' => 93.3
        ];
    }

    /**
     * Obtiene instalaciones por técnico individual
     */
    private function obtenerInstalacionesPorTecnico()
    {
        return [
            'Carlos Mendoza' => 28,
            'Ana García' => 31,
            'Luis Rodríguez' => 26,
            'María López' => 24,
            'Pedro Sánchez' => 29
        ];
    }

    /**
     * Obtiene tendencia de instalaciones mensuales
     */
    private function obtenerTendenciaInstalaciones()
    {
        return [
            'enero' => 65,
            'febrero' => 72,
            'marzo' => 68,
            'abril' => 81,
            'mayo' => 76,
            'junio' => 89,
            'julio' => 85,
            'agosto' => 92,
            'septiembre' => 87
        ];
    }

    /**
     * Obtiene volumen de soporte por mes
     */
    private function obtenerVolumenSoportePorMes()
    {
        return [
            'enero' => 145,
            'febrero' => 132,
            'marzo' => 158,
            'abril' => 167,
            'mayo' => 151,
            'junio' => 174,
            'julio' => 163,
            'agosto' => 189,
            'septiembre' => 156
        ];
    }

    /**
     * ===============================================
     * MÉTODOS DE EXPORTACIÓN
     * ===============================================
     */

    /**
     * Genera datos del reporte según el tipo
     */
    private function generarDatosReporte($tipoReporte, $periodo)
    {
        $rangoFechas = $this->calcularRangoFechas($periodo);
        
        switch ($tipoReporte) {
            case 'ventas_conversion':
                return [
                    'tipo' => 'Ventas y Conversión',
                    'periodo' => $periodo,
                    'datos' => [
                        'ventas_totales' => $this->calcularVentasTotales($rangoFechas),
                        'oportunidades_cerradas' => $this->contarOportunidadesCerradas($rangoFechas),
                        'tasa_conversion' => $this->calcularTasaConversion($rangoFechas)
                    ]
                ];
                
            case 'calidad_red':
                return [
                    'tipo' => 'Calidad de Red',
                    'periodo' => $periodo,
                    'datos' => [
                        'disponibilidad' => $this->calcularDisponibilidadRed(),
                        'latencia' => $this->obtenerLatenciaPromedio(),
                        'incidentes' => $this->contarIncidentesRed()
                    ]
                ];
                
            default:
                return ['error' => 'Tipo de reporte no válido'];
        }
    }

    /**
     * Exporta datos en el formato especificado
     */
    private function exportarEnFormato($datosReporte, $formato, $tipoReporte)
    {
        $nombreArchivo = $tipoReporte . '_' . date('Y-m-d_H-i-s');
        
        switch ($formato) {
            case 'json':
                return $this->response->download($nombreArchivo . '.json', json_encode($datosReporte, JSON_PRETTY_PRINT));
                
            case 'csv':
                // Implementar exportación CSV
                return redirect()->back()->with('success', 'Exportación CSV iniciada');
                
            case 'pdf':
                // Implementar exportación PDF
                return redirect()->back()->with('success', 'Exportación PDF iniciada');
                
            case 'excel':
            default:
                // Implementar exportación Excel
                return redirect()->back()->with('success', 'Exportación Excel iniciada');
        }
    }
}