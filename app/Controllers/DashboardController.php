<?php

namespace App\Controllers;

use App\Models\DashboardModel;
use App\Models\LeadModel;
use App\Models\CampanaModel;
use App\Models\TareaModel;

class DashboardController extends BaseController
{
    protected $dashboardModel;
    protected $leadModel;
    protected $campaniaModel;
    protected $tareaModel;

    public function __construct()
    {
        $this->dashboardModel = new DashboardModel();
        $this->leadModel = new LeadModel();
        $this->campaniaModel = new CampanaModel();
        $this->tareaModel = new TareaModel();
    }

    public function index()
    {
        // Datos para las tarjetas KPI
        $data = [
            'total_leads' => $this->dashboardModel->getTotalLeads(),
            'leads_convertidos' => $this->dashboardModel->getLeadsConvertidosEsteMes(),
            'campanias_activas' => $this->dashboardModel->getCampaniasActivas(),
            'tareas_pendientes' => $this->dashboardModel->getTareasPendientes(),
            
            // Datos para gráficos
            'pipeline_data' => $this->dashboardModel->getPipelineData(),
            'campanas_data' => $this->dashboardModel->getCampanasData(),
            
            // Actividad reciente
            'actividad_reciente' => $this->dashboardModel->getActividadReciente(),
            
            // Rendimiento por usuario
            'rendimiento_usuarios' => $this->dashboardModel->getRendimientoUsuarios(),
            
            // Headers y footers
            'header' => view('layouts/header'),
            'footer' => view('layouts/footer')
        ];

        return view('dashboard/index', $data);
    }

    // Método para obtener datos vía AJAX para actualización en tiempo real
    public function getDashboardData()
    {
        $data = [
            'total_leads' => $this->dashboardModel->getTotalLeads(),
            'leads_convertidos' => $this->dashboardModel->getLeadsConvertidosEsteMes(),
            'campanias_activas' => $this->dashboardModel->getCampaniasActivas(),
            'tareas_pendientes' => $this->dashboardModel->getTareasPendientes(),
            'pipeline_data' => $this->dashboardModel->getPipelineData(),
            'campanas_data' => $this->dashboardModel->getCampanasData(),
            'conversion_rate' => $this->dashboardModel->getConversionRate()
        ];

        return $this->response->setJSON($data);
    }
}
?>