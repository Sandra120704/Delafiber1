<?php

namespace App\Controllers;

use App\Models\DashboardModel;
use App\Models\LeadModel;
use App\Models\CampanaModel;
use App\Models\TareaModel;

/**
 * Controlador principal del Dashboard - Panel de control administrativo
 * Muestra estadísticas, gráficos y resúmenes importantes del negocio
 */
class DashboardController extends BaseController
{
    // Modelos necesarios para obtener datos del dashboard
    protected $modeloDashboard;
    protected $modeloLeads;
    protected $modeloCampanas;
    protected $modeloTareas;

    /**
     * Constructor - Inicializa todos los modelos necesarios
     */
    public function __construct()
    {
        // Crear instancias de los modelos que vamos a usar
        $this->modeloDashboard = new DashboardModel();
        $this->modeloLeads = new LeadModel();
        $this->modeloCampanas = new CampanaModel();
        $this->modeloTareas = new TareaModel();
    }

    /**
     * Página principal del dashboard
     * Recopila todos los datos estadísticos y los muestra
     */
    public function index()
    {
        // Preparar todos los datos para mostrar en el dashboard
        $datosDashboard = [
            // === TARJETAS DE ESTADÍSTICAS (KPIs) ===
            'totalLeads' => $this->modeloDashboard->getTotalLeads(),
            'leadsConvertidos' => $this->modeloDashboard->getLeadsConvertidosEsteMes(),
            'campanasActivas' => $this->modeloDashboard->getCampaniasActivas(),
            'tareasPendientes' => $this->modeloDashboard->getTareasPendientes(),
            
            // === INFORMACIÓN PARA GRÁFICOS ===
            'datosGraficoPipeline' => $this->modeloDashboard->getPipelineData(),
            'datosGraficoCampanas' => $this->modeloDashboard->getCampanasData(),
            
            // === ACTIVIDAD RECIENTE DEL SISTEMA ===
            'actividadReciente' => $this->modeloDashboard->getActividadReciente(),
            
            // === RENDIMIENTO DE CADA USUARIO ===
            'rendimientoUsuarios' => $this->modeloDashboard->getRendimientoUsuarios(),
            
            // === PLANTILLAS HTML (HEADER Y FOOTER) ===
            'header' => view('layouts/header'),  // Barra superior de navegación
            'footer' => view('layouts/footer')   // Pie de página con scripts
        ];

        // Mostrar la página del dashboard con todos los datos
        return view('dashboard/index', $datosDashboard);
    }

    /**
     * Método para obtener datos del dashboard vía AJAX
     * Útil para actualizar estadísticas en tiempo real sin recargar la página
     */
    public function getDashboardData()
    {
        // Recopilar solo los datos numéricos para actualización rápida
        $datosActualizados = [
            'totalLeads' => $this->modeloDashboard->getTotalLeads(),
            'leadsConvertidos' => $this->modeloDashboard->getLeadsConvertidosEsteMes(),
            'campanasActivas' => $this->modeloDashboard->getCampaniasActivas(),
            'tareasPendientes' => $this->modeloDashboard->getTareasPendientes(),
            'datosGraficoPipeline' => $this->modeloDashboard->getPipelineData(),
            'datosGraficoCampanas' => $this->modeloDashboard->getCampanasData(),
            'tasaConversion' => $this->modeloDashboard->getConversionRate()
        ];

        // Devolver los datos en formato JSON para consumo vía AJAX
        return $this->response->setJSON($datosActualizados);
    }
}
