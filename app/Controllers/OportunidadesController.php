<?php

namespace App\Controllers;

use App\Models\PersonaModel;
use App\Models\LeadModel;
use App\Models\CampanaModel;

class OportunidadesController extends BaseController
{
    // ===== MODELOS PRINCIPALES =====
    protected $personaModel;      // Para datos de prospectos
    protected $oportunidadesModel; // Para oportunidades de venta
    protected $campanaModel;      // Para campañas comerciales

    /**
     * Constructor - Inicializa modelos necesarios
     */
    public function __construct()
    {
    $this->personaModel = new PersonaModel();
    $this->oportunidadesModel = new \App\Models\OportunidadesModel();
    $this->campanaModel = new CampanaModel();
    }

    public function index()
    {
        $datosOportunidades = [
            // ===== PLANTILLAS BASE =====
            'header' => view('Layouts/header'),
            'footer' => view('Layouts/footer'),
            
            // ===== MÉTRICAS CLAVE DEL NEGOCIO =====
            'oportunidadesAbiertas' => $this->contarOportunidadesAbiertas(),
            'valorTotalPipeline' => $this->calcularValorTotalPipeline(),
            'tasaConversionMes' => $this->calcularTasaConversion(),
            'metaMensual' => $this->obtenerMetaVentasMensual(),
            
            // ===== DATOS PARA GRÁFICOS =====
            'pipelinePorEtapa' => $this->obtenerPipelinePorEtapas(),
            'oportunidadesPorTerritorio' => $this->obtenerOportunidadesPorTerritorio(),
            'tendenciaVentas' => $this->obtenerTendenciaVentas(),
            
            // ===== LISTAS PRINCIPALES =====
            'oportunidadesDestacadas' => $this->obtenerOportunidadesDestacadas(),
            'proximosVencimientos' => $this->obtenerProximosVencimientos()
        ];

        return view('oportunidades/index', $datosOportunidades);
    }

    /**
     * ===============================================
     * CREAR NUEVA OPORTUNIDAD DE NEGOCIO
     * ===============================================
     */
    public function crear()
    {
        $datosCreacion = [
            'header' => view('Layouts/header'),
            'footer' => view('Layouts/footer'),
            'tiposServicio' => $this->obtenerTiposServicioFibraOptica(),
            'territorios' => $this->obtenerTerritoriosDisponibles(),
            'origenes' => $this->obtenerOrigenesOportunidad()
        ];

        return view('oportunidades/crear', $datosCreacion);
    }

    /**
     * ===============================================
     * GUARDAR NUEVA OPORTUNIDAD
     * ===============================================
     */
    public function guardar()
    {
        // Validar datos de entrada
        $validacion = $this->validarDatosOportunidad();
        
        if (!$validacion['valido']) {
            return redirect()->back()
                ->withInput()
                ->with('error', $validacion['mensaje']);
        }

        try {
            // Preparar datos para guardar
            $datosOportunidad = $this->prepararDatosOportunidad();
            
            // Guardar en base de datos
            $idOportunidad = $this->oportunidadesModel->insert($datosOportunidad);
            
            if ($idOportunidad) {
                return redirect()->to('oportunidades')
                    ->with('success', 'Oportunidad creada exitosamente');
            } else {
                throw new \Exception('Error al guardar la oportunidad');
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Error en OportunidadesController::guardar: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al crear la oportunidad: ' . $e->getMessage());
        }
    }

    /**
     * ===============================================
     * MÉTODOS DE CÁLCULO Y ESTADÍSTICAS
     * ===============================================
     */

    /**
     * Cuenta oportunidades abiertas (no cerradas)
     */
    private function contarOportunidadesAbiertas()
    {
        return $this->oportunidadesModel
            ->where('estado !=', 'cerrado')
            ->where('estado !=', 'perdido')
            ->countAllResults();
    }

    /**
     * Calcula valor total del pipeline de ventas
     */
    private function calcularValorTotalPipeline()
    {
        $resultado = $this->oportunidadesModel
            ->selectSum('valor_estimado')
            ->where('estado !=', 'cerrado')
            ->where('estado !=', 'perdido')
            ->get()
            ->getRow();
        return $resultado->valor_estimado ?? 0;
    }

    /**
     * Calcula tasa de conversión del mes actual
     */
    private function calcularTasaConversion()
    {
        $inicioMes = date('Y-m-01');
        // Oportunidades cerradas exitosamente este mes
        $cerradasGanadas = $this->oportunidadesModel
            ->where('estado', 'cerrado')
            ->where('fecha_cierre >=', $inicioMes)
            ->countAllResults();
        // Total de oportunidades trabajadas este mes
        $totalTrabajadas = $this->oportunidadesModel
            ->where('updated_at >=', $inicioMes)
            ->whereIn('estado', ['cerrado', 'perdido', 'en_proceso'])
            ->countAllResults();
        return $totalTrabajadas > 0 ? round(($cerradasGanadas / $totalTrabajadas) * 100, 2) : 0;
    }

    /**
     * Obtiene meta de ventas mensual (configurable)
     */
    private function obtenerMetaVentasMensual()
    {
        // TODO: Esto podría venir de una tabla de configuración
        return 50000; // Meta en soles para empresa de fibra óptica
    }

    /**
     * Obtiene pipeline organizado por etapas
     */
    private function obtenerPipelinePorEtapas()
    {
        return $this->oportunidadesModel
            ->select('estado, COUNT(*) as cantidad, SUM(valor_estimado) as valor_total')
            ->where('estado !=', 'cerrado')
            ->where('estado !=', 'perdido')
            ->groupBy('estado')
            ->findAll();
    }

    /**
     * Obtiene oportunidades por territorio/zona
     */
    private function obtenerOportunidadesPorTerritorio()
    {

        return [];
    }

    /**
     * Obtiene tendencia de ventas de últimos 6 meses
     */
    private function obtenerTendenciaVentas()
    {
        $seisMesesAtras = date('Y-m-01', strtotime('-6 months'));
        return $this->oportunidadesModel
            ->select('DATE_FORMAT(fecha_cierre, "%Y-%m") as mes, 
                     COUNT(*) as oportunidades_cerradas,
                     SUM(valor_estimado) as ingresos')
            ->where('estado', 'cerrado')
            ->where('fecha_cierre >=', $seisMesesAtras)
            ->groupBy('mes')
            ->orderBy('mes', 'ASC')
            ->findAll();
    }

    /**
     * Obtiene oportunidades más importantes/valiosas
     */
    private function obtenerOportunidadesDestacadas()
    {
        return $this->oportunidadesModel
            ->select('oportunidades.*, personas.nombres, personas.apellidos, personas.telefono')
            ->join('personas', 'oportunidades.idlead = personas.idpersona')
            ->where('oportunidades.estado !=', 'cerrado')
            ->where('oportunidades.estado !=', 'perdido')
            ->orderBy('oportunidades.valor_estimado', 'DESC')
            ->limit(10)
            ->findAll();
    }

    /**
     * Obtiene oportunidades próximas a vencer
     */
    private function obtenerProximosVencimientos()
    {
        $dentroDeUnaSemana = date('Y-m-d', strtotime('+7 days'));
        return $this->oportunidadesModel
            ->select('oportunidades.*, personas.nombres, personas.apellidos')
            ->join('personas', 'oportunidades.idlead = personas.idpersona')
            ->where('oportunidades.fecha_cierre <=', $dentroDeUnaSemana)
            ->where('oportunidades.estado !=', 'cerrado')
            ->orderBy('oportunidades.fecha_cierre', 'ASC')
            ->findAll();
    }

    /**
     * ===============================================
     * MÉTODOS DE CONFIGURACIÓN Y DATOS MAESTROS
     * ===============================================
     */

    /**
     * Obtiene tipos de servicio específicos para fibra óptica
     */
    private function obtenerTiposServicioFibraOptica()
    {
        return [
            'fibra_hogar_basico' => 'Fibra Hogar - Plan Básico (50 Mbps)',
            'fibra_hogar_premium' => 'Fibra Hogar - Plan Premium (200 Mbps)',
            'fibra_empresarial' => 'Fibra Empresarial - Dedicado',
            'internet_tv_combo' => 'Combo Internet + TV Digital',
            'telefonia_ip' => 'Telefonía IP Empresarial',
            'soporte_premium' => 'Soporte Técnico Premium 24/7'
        ];
    }

    /**
     * Obtiene territorios de cobertura
     */
    private function obtenerTerritoriosDisponibles()
    {
        return [
            'centro_lima' => 'Lima Centro',
            'lima_norte' => 'Lima Norte',
            'lima_sur' => 'Lima Sur',
            'lima_este' => 'Lima Este',
            'callao' => 'Callao',
            'empresarial' => 'Zona Empresarial'
        ];
    }

    /**
     * Obtiene orígenes de las oportunidades
     */
    private function obtenerOrigenesOportunidad()
    {
        return [
            'web' => 'Página Web',
            'redes_sociales' => 'Redes Sociales',
            'referidos' => 'Referidos',
            'campana_digital' => 'Campaña Digital',
            'telemarketing' => 'Telemarketing',
            'puerta_puerta' => 'Visita Puerta a Puerta',
            'evento' => 'Evento/Feria'
        ];
    }

    /**
     * ===============================================
     * MÉTODOS DE VALIDACIÓN
     * ===============================================
     */

    /**
     * Valida datos de nueva oportunidad
     */
    private function validarDatosOportunidad()
    {
        $datos = $this->request->getPost();
        
        // Validaciones básicas
        if (empty($datos['nombre_cliente'])) {
            return ['valido' => false, 'mensaje' => 'El nombre del cliente es obligatorio'];
        }
        
        if (empty($datos['tipo_servicio'])) {
            return ['valido' => false, 'mensaje' => 'Debe seleccionar un tipo de servicio'];
        }
        
        if (empty($datos['valor_estimado']) || $datos['valor_estimado'] <= 0) {
            return ['valido' => false, 'mensaje' => 'El valor estimado debe ser mayor a cero'];
        }
        
        return ['valido' => true, 'mensaje' => 'Datos válidos'];
    }

    /**
     * Prepara datos para insertar en base de datos
     */
    private function prepararDatosOportunidad()
    {
        $datos = $this->request->getPost();
        
        return [
            'nombre_cliente' => $datos['nombre_cliente'],
            'tipo_servicio' => $datos['tipo_servicio'],
            'valor_estimado' => $datos['valor_estimado'],
            'territorio' => $datos['territorio'] ?? 'centro_lima',
            'origen' => $datos['origen'] ?? 'web',
            'estado' => 'nuevo',
            'fecha_creacion' => date('Y-m-d H:i:s'),
            'fecha_vencimiento' => date('Y-m-d', strtotime('+30 days')),
            'observaciones' => $datos['observaciones'] ?? ''
        ];
    }
}