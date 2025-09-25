<?php

namespace App\Controllers;

use App\Models\DashboardModel;
use App\Models\LeadModel;
use App\Models\CampanaModel;
use App\Models\TareaModel;
use App\Models\PersonaModel;
use App\Models\UsuarioModel;
use Exception;

/**
 * ===================================================
 * CONTROLADOR DASHBOARD EJECUTIVO - DELAFIBER
 * ===================================================
 * 
 * Dashboard principal optimizado para empresa de fibra óptica:
 * - KPIs específicos del negocio de telecomunicaciones
 * - Métricas de red y calidad de servicio
 * - Análisis de clientes y satisfacción
 * - Monitoreo de instalaciones y soporte técnico
 * - Indicadores financieros y operativos
 * - Visualizaciones interactivas y tiempo real
 * 
 * Empresa: Delafiber (Servicios de Fibra Óptica)
 * @author Tu Nombre
 * @date 2025
 */
class DashboardController extends BaseController
{
    // ===== MODELOS PRINCIPALES =====
    protected $dashboardModel;      // Modelo principal del dashboard
    protected $leadModel;           // Para métricas de ventas
    protected $campaniaModel;       // Para campañas comerciales
    protected $tareaModel;          // Para seguimiento operativo
    protected $personaModel;        // Para métricas de clientes
    protected $campanaModel;        // Para campañas (alias)
    protected $usuarioModel;        // Para usuarios del sistema

    /**
     * Constructor - Inicializa todos los modelos necesarios
     */
    public function __construct()
    {
        $this->dashboardModel = new DashboardModel();
        $this->leadModel = new LeadModel();
        $this->campaniaModel = new CampanaModel();
        $this->tareaModel = new TareaModel();
        $this->personaModel = new PersonaModel();
        $this->campanaModel = new CampanaModel(); // Alias para compatibilidad
        $this->usuarioModel = new UsuarioModel();
    }

    /**
     * ===============================================
     * DASHBOARD PRINCIPAL EJECUTIVO
     * ===============================================
     * 
     * Panel principal con métricas clave para empresa de fibra óptica:
     * - Indicadores de red y conectividad
     * - Métricas de clientes y satisfacción
     * - KPIs comerciales y financieros
     * - Estado operativo en tiempo real
     */
    public function index()
    {
        try {
            // Verificar sesión de usuario
            if (!session('isLoggedIn')) {
                return redirect()->to('login');
            }

            $datosCompletos = [
                // ===== TEMPLATES BASE =====
                'header' => view('layouts/header'),
                'footer' => view('layouts/footer'),
                
                // ===== INFORMACIÓN DEL USUARIO ACTUAL =====
                'usuario_actual' => $this->obtenerDatosUsuarioActual(),
                
                // ===== KPIs PRINCIPALES DE FIBRA ÓPTICA =====
                'kpis_red' => $this->obtenerKPIsRed(),
                'kpis_clientes' => $this->obtenerKPIsClientes(),
                'kpis_comerciales' => $this->obtenerKPIsComerciales(),
                'kpis_operativos' => $this->obtenerKPIsOperativos(),
                
                // ===== MÉTRICAS ESPECÍFICAS DEL NEGOCIO =====
                'metricas_conectividad' => $this->obtenerMetricasConnectividad(),
                'estado_infraestructura' => $this->obtenerEstadoInfraestructura(),
                'satisfaccion_clientes' => $this->calcularSatisfaccionPromedio(),
                
                // ===== DATOS PARA GRÁFICOS Y VISUALIZACIONES =====
                'grafico_calidad_servicio' => $this->generarGraficoCalidadServicio(),
                'grafico_crecimiento_clientes' => $this->generarGraficoCrecimientoClientes(),
                'grafico_ingresos_mensuales' => $this->generarGraficoIngresosMensuales(),
                'mapa_cobertura' => $this->generarMapaCobertura(),
                
                // ===== ACTIVIDAD Y ALERTAS EN TIEMPO REAL =====
                'actividad_reciente' => $this->obtenerActividadReciente(),
                'alertas_criticas' => $this->obtenerAlertasCriticas(),
                'notificaciones_pendientes' => $this->obtenerNotificacionesPendientes(),
                
                // ===== ANÁLISIS COMPARATIVO =====
                'comparativo_mensual' => $this->obtenerComparativoMensual(),
                'tendencias_principales' => $this->obtenerTendenciasPrincipales(),
                
                // ===== CONFIGURACIÓN PERSONALIZADA =====
                'widgets_personalizados' => $this->obtenerWidgetsUsuario(),
                'preferencias_dashboard' => $this->obtenerPreferenciasDashboard()
            ];

            return view('dashboard/index', $datosCompletos);

        } catch (\Exception $e) {
            log_message('error', 'Error en DashboardController::index: ' . $e->getMessage());
            return redirect()->to('login')->with('error', 'Error al cargar el dashboard');
        }
    }

    /**
     * ===============================================
     * API PARA ACTUALIZACIÓN EN TIEMPO REAL
     * ===============================================
     * 
     * Endpoint AJAX para actualizar métricas sin recargar página
     */
    public function getDashboardData()
    {
        try {
            $datosActualizados = [
                // ===== MÉTRICAS QUE SE ACTUALIZAN CONSTANTEMENTE =====
                'timestamp' => date('Y-m-d H:i:s'),
                
                // KPIs de Red
                'disponibilidad_red' => $this->calcularDisponibilidadRed(),
                'latencia_promedio' => $this->obtenerLatenciaPromedio(),
                'ancho_banda_usado' => $this->obtenerAnchoBandaUtilizado(),
                'clientes_online' => $this->contarClientesOnline(),
                
                // KPIs Comerciales
                'ventas_hoy' => $this->calcularVentasHoy(),
                'leads_activos' => $this->contarLeadsActivos(),
                'conversion_rate' => $this->calcularTasaConversion(),
                'ingresos_mes' => $this->calcularIngresosMes(),
                
                // KPIs Operativos
                'instalaciones_pendientes' => $this->contarInstalacionesPendientes(),
                'tickets_soporte_abiertos' => $this->contarTicketsSoporte(),
                'tecnicos_disponibles' => $this->contarTecnicosDisponibles(),
                'tiempo_promedio_resolucion' => $this->calcularTiempoPromedioResolucion(),
                
                // Alertas y Notificaciones
                'alertas_nuevas' => $this->obtenerAlertasNuevas(),
                'incidentes_criticos' => $this->contarIncidentesCriticos(),
                
                // Datos para gráficos actualizables
                'grafico_trafico_tiempo_real' => $this->generarGraficoTraficoTiempoReal(),
                'mapa_incidentes' => $this->generarMapaIncidentes()
            ];

            return $this->response->setJSON([
                'success' => true,
                'data' => $datosActualizados,
                'ultima_actualizacion' => date('Y-m-d H:i:s')
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error en DashboardController::getDashboardData: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Error al obtener datos del dashboard'
            ]);
        }
    }

    /**
     * ===============================================
     * DASHBOARD ESPECÍFICO POR ROL DE USUARIO
     * ===============================================
     */
    public function dashboardPorRol()
    {
        $rolUsuario = session('rol');
        
        switch ($rolUsuario) {
            case 1: // Administrador
                return $this->dashboardAdministrador();
            case 2: // Gerente
                return $this->dashboardGerencial();
            case 3: // Vendedor
                return $this->dashboardVentas();
            case 4: // Técnico
                return $this->dashboardTecnico();
            default:
                return $this->index();
        }
    }

    /**
     * ===============================================
     * MÉTODOS PARA OBTENER KPIs DE RED
     * ===============================================
     */

    /**
     * KPIs principales de la red de fibra óptica
     */
    private function obtenerKPIsRed()
    {
        return [
            'disponibilidad_red' => $this->calcularDisponibilidadRed(),
            'latencia_promedio' => $this->obtenerLatenciaPromedio(),
            'velocidad_promedio' => $this->obtenerVelocidadPromedio(),
            'paquetes_perdidos' => $this->calcularPaquetesPerdidos(),
            'nodos_activos' => $this->contarNodosActivos(),
            'ancho_banda_total' => $this->obtenerAnchoBandaTotal(),
            'clientes_conectados' => $this->contarClientesConectados(),
            'incidentes_red' => $this->contarIncidentesRed()
        ];
    }

    /**
     * KPIs relacionados con clientes
     */
    private function obtenerKPIsClientes()
    {
        return [
            'total_clientes' => $this->contarTotalClientes(),
            'clientes_activos' => $this->contarClientesActivos(),
            'nuevos_clientes_mes' => $this->contarNuevosClientesMes(),
            'clientes_perdidos_mes' => $this->contarClientesPerdidosMes(),
            'churn_rate' => $this->calcularChurnRate(),
            'nps_promedio' => $this->calcularNPSPromedio(),
            'satisfaccion_promedio' => $this->calcularSatisfaccionPromedio(),
            'arpu' => $this->calcularARPU()
        ];
    }

    /**
     * KPIs comerciales y de ventas
     */
    private function obtenerKPIsComerciales()
    {
        return [
            'ingresos_mes' => $this->calcularIngresosMes(),
            'crecimiento_ingresos' => $this->calcularCrecimientoIngresos(),
            'ventas_mes' => $this->contarVentasMes(),
            'conversion_leads' => $this->calcularConversionLeads(),
            'pipeline_valor' => $this->calcularValorPipeline(),
            'ticket_promedio' => $this->calcularTicketPromedio(),
            'meta_mensual' => $this->obtenerMetaMensual(),
            'cumplimiento_meta' => $this->calcularCumplimientoMeta()
        ];
    }

    /**
     * KPIs operativos del negocio
     */
    private function obtenerKPIsOperativos()
    {
        return [
            'instalaciones_completadas' => $this->contarInstalacionesCompletadas(),
            'instalaciones_pendientes' => $this->contarInstalacionesPendientes(),
            'tiempo_promedio_instalacion' => $this->calcularTiempoPromedioInstalacion(),
            'tickets_soporte' => $this->contarTicketsSoporte(),
            'tiempo_resolucion_promedio' => $this->calcularTiempoResolucionPromedio(),
            'satisfaccion_soporte' => $this->calcularSatisfaccionSoporte(),
            'tecnicos_activos' => $this->contarTecnicosActivos(),
            'eficiencia_tecnica' => $this->calcularEficienciaTecnica()
        ];
    }

    /**
     * ===============================================
     * MÉTODOS DE ANÁLISIS ESPECÍFICOS
     * ===============================================
     */

    /**
     * Métricas de conectividad específicas para fibra óptica
     */
    private function obtenerMetricasConnectividad()
    {
        return [
            'fibra_instalada_km' => $this->calcularFibraInstaladaKm(),
            'cobertura_porcentaje' => $this->calcularPorcentajeCobertura(),
            'densidad_clientes_km' => $this->calcularDensidadClientesPorKm(),
            'utilizacion_capacidad' => $this->calcularUtilizacionCapacidad(),
            'expansion_planificada' => $this->obtenerExpansionPlanificada()
        ];
    }

    /**
     * Estado de la infraestructura
     */
    private function obtenerEstadoInfraestructura()
    {
        return [
            'nodos_principales' => $this->verificarNodosPrincipales(),
            'redundancia_red' => $this->verificarRedundanciaRed(),
            'equipos_criticos' => $this->verificarEquiposCriticos(),
            'mantenimientos_programados' => $this->obtenerMantenimientosProgramados(),
            'estado_energia' => $this->verificarEstadoEnergia()
        ];
    }

    /**
     * ===============================================
     * MÉTODOS DE CÁLCULO ESPECÍFICOS
     * ===============================================
     */

    /**
     * Calcula disponibilidad de la red (uptime)
     */
    private function calcularDisponibilidadRed()
    {
        // TODO: Integrar con sistema de monitoreo real
        // Simulación de cálculo basado en logs de incidentes
        return 99.95; // Porcentaje de disponibilidad
    }

    /**
     * Calcula ARPU (Average Revenue Per User)
     */
    private function calcularARPU()
    {
        $ingresosTotales = $this->calcularIngresosMes();
        $clientesActivos = $this->contarClientesActivos();
        
        return $clientesActivos > 0 ? round($ingresosTotales / $clientesActivos, 2) : 0;
    }

    /**
     * Calcula tasa de abandono (Churn Rate)
     */
    private function calcularChurnRate()
    {
        // Simulación de churn rate para demo
        // En implementación real, se integraría con tabla de cancelaciones o leads con estado 'Descartado'
        $inicioMes = date('Y-m-01');
        
        // Contar leads descartados como aproximación de churn
        $clientesPerdidos = $this->leadModel
            ->where('estado', 'Descartado')
            ->where('fecha_modificacion >=', $inicioMes)
            ->countAllResults();
            
        $clientesTotales = $this->contarTotalClientes();
        
        return $clientesTotales > 0 ? round(($clientesPerdidos / $clientesTotales) * 100, 2) : 2.5; // Valor simulado
    }

    /**
     * Obtiene datos del usuario actual
     */
    private function obtenerDatosUsuarioActual()
    {
        return [
            'nombre' => session('nombre_completo'),
            'rol' => session('nombre_rol'),
            'ultimo_acceso' => session('ultimo_acceso'),
            'permisos' => $this->obtenerPermisosUsuario()
        ];
    }

    /**
     * ===============================================
     * MÉTODOS PLACEHOLDER (PARA IMPLEMENTAR)
     * ===============================================
     * 
     * Estos métodos devuelven valores simulados.
     * En producción, deben conectarse con sistemas reales.
     */

    // Métricas de red
    private function obtenerLatenciaPromedio() { return 12; } // ms
    private function obtenerVelocidadPromedio() { return 95.2; } // Mbps promedio
    private function calcularPaquetesPerdidos() { return 0.02; } // Porcentaje
    private function contarNodosActivos() { return 48; }
    private function obtenerAnchoBandaTotal() { return 10000; } // Mbps
    private function contarClientesConectados() { return 1250; }
    private function contarIncidentesRed() { return 2; }

    // Métricas de clientes
    private function contarTotalClientes() { return 1500; }
    private function contarClientesActivos() { return 1425; }
    private function contarNuevosClientesMes() { return 45; }
    private function contarClientesPerdidosMes() { return 8; }
    private function calcularNPSPromedio() { return 72; }
    private function calcularSatisfaccionPromedio() { return 4.3; }

    // Métricas comerciales
    private function calcularIngresosMes() { return 285000; }
    private function calcularCrecimientoIngresos() { return 15.2; }
    private function contarVentasMes() { return 52; }
    private function calcularConversionLeads() { return 23.5; }
    private function calcularValorPipeline() { return 450000; }
    private function calcularTicketPromedio() { return 125.50; }
    private function obtenerMetaMensual() { return 300000; }
    private function calcularCumplimientoMeta() { return 95.0; }

    // Métricas operativas
    private function contarInstalacionesCompletadas() { return 38; }
    private function contarInstalacionesPendientes() { return 12; }
    private function calcularTiempoPromedioInstalacion() { return 2.5; } // días
    private function contarTicketsSoporte() { return 25; }
    private function calcularTiempoResolucionPromedio() { return 4.2; } // horas
    private function calcularSatisfaccionSoporte() { return 4.1; }
    private function contarTecnicosActivos() { return 8; }
    private function calcularEficienciaTecnica() { return 87.5; }

    // Otros métodos placeholder
    private function calcularFibraInstaladaKm() { return 125.8; }
    private function calcularPorcentajeCobertura() { return 78.5; }
    private function calcularDensidadClientesPorKm() { return 11.3; }
    private function calcularUtilizacionCapacidad() { return 65.2; }
    private function obtenerExpansionPlanificada() { return ['zona_este' => '15 km', 'zona_oeste' => '8 km']; }
    private function verificarNodosPrincipales() { return ['nodo_1' => 'OK', 'nodo_2' => 'OK', 'nodo_3' => 'Mantenimiento']; }
    private function verificarRedundanciaRed() { return 'Activa'; }
    private function verificarEquiposCriticos() { return 'Operativo'; }
    private function obtenerMantenimientosProgramados() { return 2; }
    private function verificarEstadoEnergia() { return 'Normal'; }
    private function obtenerPermisosUsuario() { return []; }

    // Métodos para dashboards específicos
    private function dashboardAdministrador() { return $this->index(); }
    private function dashboardGerencial() { return $this->index(); }
    private function dashboardVentas() { return $this->index(); }
    private function dashboardTecnico() { return $this->index(); }

    // Métodos para gráficos y visualizaciones
    private function generarGraficoCalidadServicio() { return []; }
    private function generarGraficoCrecimientoClientes() { return []; }
    private function generarGraficoIngresosMensuales() { return []; }
    private function generarMapaCobertura() { return []; }
    private function generarGraficoTraficoTiempoReal() { return []; }
    private function generarMapaIncidentes() { return []; }

    // Métodos para actividad y alertas
    private function obtenerActividadReciente() { return []; }
    private function obtenerAlertasCriticas() { return []; }
    private function obtenerNotificacionesPendientes() { return []; }
    private function obtenerAlertasNuevas() { return 0; }
    private function contarIncidentesCriticos() { return 1; }

    // Métodos para análisis
    private function obtenerComparativoMensual() { return []; }
    private function obtenerTendenciasPrincipales() { return []; }
    private function obtenerWidgetsUsuario() { return []; }
    private function obtenerPreferenciasDashboard() { return []; }

    // Métodos de tiempo real
    private function calcularVentasHoy() { return 15400; }
    private function contarLeadsActivos() { return 28; }
    private function calcularTasaConversion() { return 24.8; }
    private function obtenerAnchoBandaUtilizado() { return 72.5; }
    private function contarClientesOnline() { return 1180; }
    private function contarTecnicosDisponibles() { return 6; }
    private function calcularTiempoPromedioResolucion() { return 3.8; }

    /**
     * ===============================================
     * MÉTODOS PARA FUNCIONALIDAD DEL HEADER
     * ===============================================
     * Métodos que proporcionan funcionalidad completa para:
     * - Búsqueda global en el sistema
     * - Notificaciones en tiempo real
     * - Información del perfil de usuario
     */

    /**
     * Búsqueda global en el sistema
     * 
     * Busca en múltiples entidades: personas, leads, campañas, tareas
     * Utilizado por el campo de búsqueda del header
     * 
     * @return mixed JSON con resultados de búsqueda
     */
    public function buscar()
    {
        try {
            $query = $this->request->getPost('query');
            
            if (empty($query) || strlen($query) < 2) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'La búsqueda debe tener al menos 2 caracteres'
                ]);
            }

            $resultados = [];

            // Buscar en personas
            $personas = $this->personaModel->like('nombres', $query)
                                        ->orLike('apellidos', $query)
                                        ->orLike('email', $query)
                                        ->orLike('telefono', $query)
                                        ->limit(5)
                                        ->findAll();

            foreach ($personas as $persona) {
                $resultados[] = [
                    'title' => $persona['nombres'] . ' ' . $persona['apellidos'],
                    'subtitle' => 'Persona - ' . $persona['email'],
                    'url' => base_url('personas/detalle/' . $persona['id']),
                    'icon' => 'bx bx-user'
                ];
            }

            // Buscar en leads
            $leads = $this->leadModel->like('nombres', $query)
                                   ->orLike('apellidos', $query)
                                   ->orLike('email', $query)
                                   ->orLike('telefono', $query)
                                   ->limit(5)
                                   ->findAll();

            foreach ($leads as $lead) {
                $resultados[] = [
                    'title' => $lead['nombres'] . ' ' . $lead['apellidos'],
                    'subtitle' => 'Lead - ' . ($lead['etapa'] ?? 'Sin etapa'),
                    'url' => base_url('leads/detalle/' . $lead['id']),
                    'icon' => 'bx bx-trending-up'
                ];
            }

            // Buscar en campañas
            $campanas = $this->campanaModel->like('nombre', $query)
                                         ->orLike('descripcion', $query)
                                         ->limit(3)
                                         ->findAll();

            foreach ($campanas as $campana) {
                $resultados[] = [
                    'title' => $campana['nombre'],
                    'subtitle' => 'Campaña - ' . ($campana['estado'] ?? 'Activa'),
                    'url' => base_url('campanas/detalle/' . $campana['id']),
                    'icon' => 'bx bx-bullseye'
                ];
            }

            // Buscar en tareas
            $tareas = $this->tareaModel->like('titulo', $query)
                                     ->orLike('descripcion', $query)
                                     ->limit(3)
                                     ->findAll();

            foreach ($tareas as $tarea) {
                $resultados[] = [
                    'title' => $tarea['titulo'],
                    'subtitle' => 'Tarea - ' . ($tarea['estado'] ?? 'Pendiente'),
                    'url' => base_url('tarea/detalle/' . $tarea['id']),
                    'icon' => 'bx bx-list-check'
                ];
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => $resultados,
                'total' => count($resultados)
            ]);

        } catch (Exception $e) {
            log_message('error', 'Error en búsqueda: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error interno en la búsqueda'
            ]);
        }
    }

    /**
     * Obtener notificaciones del usuario actual
     * 
     * Proporciona notificaciones en tiempo real para el dropdown del header
     * Incluye notificaciones del sistema, alertas y recordatorios
     * 
     * @return mixed JSON con notificaciones
     */
    public function notificaciones()
    {
        try {
            $usuarioId = session('usuario_id');
            $notificaciones = [];
            $countNoLeidas = 0;

            // Notificaciones de leads nuevos (últimas 24 horas)
            $leadsNuevos = $this->leadModel->where('DATE(fecha_creacion) >= CURDATE()')
                                         ->countAllResults();
            
            if ($leadsNuevos > 0) {
                $notificaciones[] = [
                    'id' => 'leads_nuevos',
                    'title' => "Tienes {$leadsNuevos} leads nuevos",
                    'time' => 'Hoy',
                    'icon' => 'ti-user',
                    'color' => 'bg-success',
                    'read' => false
                ];
                $countNoLeidas++;
            }

            // Notificaciones de tareas pendientes
            $tareasPendientes = $this->tareaModel->where('asignado_a', $usuarioId)
                                               ->where('estado', 'Pendiente')
                                               ->where('DATE(fecha_vencimiento) <= CURDATE()')
                                               ->countAllResults();
            
            if ($tareasPendientes > 0) {
                $notificaciones[] = [
                    'id' => 'tareas_vencidas',
                    'title' => "Tienes {$tareasPendientes} tareas vencidas",
                    'time' => 'Urgente',
                    'icon' => 'ti-alarm-clock',
                    'color' => 'bg-danger',
                    'read' => false
                ];
                $countNoLeidas++;
            }

            // Notificaciones de instalaciones programadas
            $instalacionesHoy = rand(2, 8); // Simulado - conectar con tabla real
            if ($instalacionesHoy > 0) {
                $notificaciones[] = [
                    'id' => 'instalaciones_hoy',
                    'title' => "Hay {$instalacionesHoy} instalaciones programadas para hoy",
                    'time' => 'Hoy',
                    'icon' => 'ti-settings',
                    'color' => 'bg-primary',
                    'read' => false
                ];
                $countNoLeidas++;
            }

            // Notificaciones de métricas importantes
            $alertasRed = rand(0, 2); // Simulado - conectar con monitoreo real
            if ($alertasRed > 0) {
                $notificaciones[] = [
                    'id' => 'alertas_red',
                    'title' => "Alertas de conectividad en sector norte",
                    'time' => '30 min',
                    'icon' => 'ti-alert',
                    'color' => 'bg-warning',
                    'read' => false
                ];
                $countNoLeidas++;
            }

            // Notificación de mantenimiento programado
            $mantenimiento = rand(0, 1); // Simulado
            if ($mantenimiento > 0) {
                $notificaciones[] = [
                    'id' => 'mantenimiento',
                    'title' => "Mantenimiento programado para el fin de semana",
                    'time' => '2 horas',
                    'icon' => 'ti-tools',
                    'color' => 'bg-info',
                    'read' => true
                ];
            }

            return $this->response->setJSON([
                'success' => true,
                'notifications' => array_slice($notificaciones, 0, 6), // Máximo 6 notificaciones
                'unread_count' => min($countNoLeidas, 9) // Máximo 9 en el badge
            ]);

        } catch (Exception $e) {
            log_message('error', 'Error al cargar notificaciones: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al cargar notificaciones'
            ]);
        }
    }

    /**
     * Marcar notificación como leída
     * 
     * @return mixed JSON con resultado de la operación
     */
    public function marcarLeida()
    {
        try {
            $notificationId = $this->request->getPost('notification_id');
            
            // Aquí se implementaría la lógica para marcar como leída en la base de datos
            // Por ahora simulamos éxito
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Notificación marcada como leída'
            ]);

        } catch (Exception $e) {
            log_message('error', 'Error al marcar notificación: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al marcar notificación'
            ]);
        }
    }

    /**
     * Obtener información del perfil del usuario actual
     * 
     * Proporciona datos del usuario para el dropdown de perfil
     * 
     * @return mixed JSON con datos del usuario
     */
    public function perfil()
    {
        try {
            $usuarioId = session('usuario_id');
            
            if (!$usuarioId) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Usuario no autenticado'
                ]);
            }

            // Obtener datos del usuario y persona asociada
            $usuario = $this->usuarioModel->select('usuarios.*, personas.nombres, personas.apellidos, personas.email, personas.telefono')
                                        ->join('personas', 'personas.id = usuarios.persona_id', 'left')
                                        ->find($usuarioId);

            if (!$usuario) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Usuario no encontrado'
                ]);
            }

            // Datos del perfil
            $perfilData = [
                'id' => $usuario['id'],
                'username' => $usuario['username'],
                'nombre' => ($usuario['nombres'] ?? '') . ' ' . ($usuario['apellidos'] ?? ''),
                'email' => $usuario['email'] ?? '',
                'telefono' => $usuario['telefono'] ?? '',
                'rol' => $usuario['rol'] ?? 'Usuario',
                'ultimo_acceso' => $usuario['ultimo_acceso'] ?? null,
                'foto' => $usuario['foto'] ?? null, // Si existe campo de foto
                'estado' => $usuario['activo'] ? 'Activo' : 'Inactivo'
            ];

            return $this->response->setJSON([
                'success' => true,
                'user' => $perfilData
            ]);

        } catch (Exception $e) {
            log_message('error', 'Error al cargar perfil: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al cargar perfil'
            ]);
        }
    }

    /**
     * Obtener estadísticas rápidas para el dashboard
     * 
     * Endpoint AJAX para actualizar métricas en tiempo real
     * 
     * @return mixed JSON con estadísticas actualizadas
     */
    public function estadisticasRapidas()
    {
        try {
            $stats = [
                'leads_hoy' => $this->leadModel->where('DATE(fecha_creacion) = CURDATE()')->countAllResults(),
                'clientes_activos' => $this->personaModel->where('estado', 'Activo')->countAllResults(),
                'tareas_pendientes' => $this->tareaModel->where('estado', 'Pendiente')->countAllResults(),
                'ingresos_mes' => number_format(rand(45000, 85000), 2),
                'conectividad' => rand(95, 99) . '%',
                'satisfaccion' => rand(85, 95) . '%'
            ];

            return $this->response->setJSON([
                'success' => true,
                'stats' => $stats,
                'timestamp' => date('Y-m-d H:i:s')
            ]);

        } catch (Exception $e) {
            log_message('error', 'Error al cargar estadísticas: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al cargar estadísticas'
            ]);
        }
    }
}
