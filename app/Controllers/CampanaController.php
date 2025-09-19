<?php

namespace App\Controllers;
use App\Models\CampanaModel;
use App\Models\MedioModel;
use App\Models\UsuarioModel;
use Config\Database;

class CampanaController extends BaseController
{
    protected $campanaModel;
    protected $medioModel;
    protected $usuarioModel;
    protected $db;

    public function __construct()
    {
        $this->campanaModel = new CampanaModel();
        $this->medioModel = new MedioModel();
        $this->usuarioModel = new UsuarioModel();
        $this->db           = Database::connect();
    }

    /**
     * Dashboard principal con métricas y analytics
     */
    public function index()
    {
        $campanas = $this->campanaModel->getAllWithDetails();
        $metricas = $this->campanaModel->getMetricas();
        $analytics = $this->campanaModel->getAnalytics();

        $datos = [
            'header' => view('layouts/header'),
            'footer' => view('layouts/footer'),
            'campanas' => $campanas,
            'metricas' => $metricas,
            'analytics' => $analytics,
            'usuarios' => $this->usuarioModel->findAll()
        ];

        return view('campanas/index', $datos);
    }

    /**
     * API: Obtener campañas con filtros
     */
    public function getCampanas()
    {
        $filtros = [
            'search' => $this->request->getGet('search'),
            'status' => $this->request->getGet('status'),
            'priority' => $this->request->getGet('priority'),
            'responsible' => $this->request->getGet('responsible'),
            'budget_min' => $this->request->getGet('budget_min'),
            'budget_max' => $this->request->getGet('budget_max'),
            'date_start' => $this->request->getGet('date_start'),
            'date_end' => $this->request->getGet('date_end')
        ];

        $campanas = $this->campanaModel->getCampanasFiltered($filtros);
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $campanas,
            'total' => count($campanas)
        ]);
    }

    /**
     * Crear/Editar campaña
     */
    public function crear($id = null)
    {
        $campana = null;
        $difusiones = [];

        if ($id) {
            $campana = $this->campanaModel->find($id);
            $difusiones = $this->campanaModel->getMedios($id);
            
            if (!$campana) {
                return redirect()->to('campanas')->with('error', 'Campaña no encontrada');
            }
        }

        $datos = [
            'header' => view('layouts/header'),
            'footer' => view('layouts/footer'),
            'campana' => $campana,
            'medios' => $this->medioModel->findAll(),
            'difusiones' => $difusiones,
            'usuarios' => $this->usuarioModel->findAll(),
            'prioridades' => $this->campanaModel->getPrioridades(),
            'estados' => $this->campanaModel->getEstados()
        ];

        return view('campanas/crear', $datos);
    }

    /**
     * Guardar campaña (crear/actualizar)
     */
    public function guardar()
    {
        $validation = \Config\Services::validation();
        
        $rules = [
            'nombre' => 'required|min_length[3]|max_length[100]',
            'descripcion' => 'permit_empty|max_length[500]',
            'fecha_inicio' => 'required|valid_date',
            'fecha_fin' => 'required|valid_date',
            'presupuesto' => 'required|numeric|greater_than[0]',
            'prioridad' => 'required|in_list[alta,media,baja]',
            'responsable' => 'permit_empty|integer'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $validation->getErrors()
            ]);
        }

        $data = $this->request->getPost();
        
        // Validar fechas
        if (strtotime($data['fecha_fin']) <= strtotime($data['fecha_inicio'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'La fecha fin debe ser posterior a la fecha de inicio'
            ]);
        }

        $campanaData = [
            'nombre' => trim($data['nombre']),
            'descripcion' => trim($data['descripcion'] ?? ''),
            'fecha_inicio' => $data['fecha_inicio'],
            'fecha_fin' => $data['fecha_fin'],
            'presupuesto' => floatval($data['presupuesto']),
            'prioridad' => $data['prioridad'],
            'segmento' => trim($data['segmento'] ?? ''),
            'responsable' => !empty($data['responsable']) ? intval($data['responsable']) : null,
            'objetivos' => trim($data['objetivos'] ?? ''),
            'notas' => trim($data['notas'] ?? ''),
            'categoria' => $data['categoria'] ?? 'general',
            'tags' => $data['tags'] ?? ''
        ];

        try {
            if (!empty($data['idcampania'])) {
                // Actualizar
                $this->campanaModel->update($data['idcampania'], $campanaData);
                $idcampania = $data['idcampania'];
                $action = 'actualizada';
            } else {
                // Crear nueva
                $campanaData['estado'] = 'borrador'; // Empezar en borrador
                $campanaData['fecha_creacion'] = date('Y-m-d H:i:s');
                $campanaData['creado_por'] = session()->get('idusuario');
                $idcampania = $this->campanaModel->insert($campanaData);
                $action = 'creada';
            }

            // Guardar medios/difusiones
            if (!empty($data['medios'])) {
                $this->campanaModel->guardarDifusiones($idcampania, $data['medios']);
            }

            // Log de actividad
            $this->registrarActividad($idcampania, $action, $campanaData['nombre']);

            return $this->response->setJSON([
                'success' => true,
                'message' => "Campaña {$action} exitosamente",
                'campaign_id' => $idcampania
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al guardar la campaña: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Obtener detalle completo de una campaña
     */
    public function detalle($id)
    {
        try {
            $campana = $this->campanaModel->getDetalle($id);
            
            if (!$campana) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Campaña no encontrada'
                ]);
            }

            // Obtener datos del responsable
            if (!empty($campana['responsable'])) {
                $usuario = $this->usuarioModel->find($campana['responsable']);
                $campana['responsable_nombre'] = $usuario['nombre'] ?? 'No asignado';
                $campana['responsable_email'] = $usuario['email'] ?? '';
            } else {
                $campana['responsable_nombre'] = 'No asignado';
                $campana['responsable_email'] = '';
            }

            // Completar datos faltantes
            $campana['segmento'] = $campana['segmento'] ?: 'No definido';
            $campana['objetivos'] = $campana['objetivos'] ?: 'No definidos';
            $campana['notas'] = $campana['notas'] ?: 'Sin notas';
            $campana['categoria'] = $campana['categoria'] ?: 'general';

            // Obtener medios y métricas
            $medios = $this->campanaModel->getMedios($id);
            $metricas = $this->campanaModel->getMetricasCampana($id);
            $actividad = $this->campanaModel->getActividad($id);

            // Calcular ROI
            $inversion_total = array_sum(array_column($medios, 'inversion'));
            $leads_total = array_sum(array_column($medios, 'leads'));
            $roi = $inversion_total > 0 ? ($leads_total * 100) / $inversion_total : 0;

            return $this->response->setJSON([
                'success' => true,
                'campana' => $campana,
                'medios' => $medios,
                'metricas' => $metricas,
                'actividad' => $actividad,
                'roi' => round($roi, 2),
                'conversion_rate' => $this->calcularConversion($id)
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al obtener detalle: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Cambiar estado de campaña con validaciones
     */
    public function cambiarEstado($id)
    {
        $nuevoEstado = $this->request->getPost('estado');
        $estados_validos = ['borrador', 'activa', 'pausada', 'finalizada', 'cancelada'];

        if (!in_array($nuevoEstado, $estados_validos)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Estado inválido'
            ]);
        }

        try {
            $campana = $this->campanaModel->find($id);
            if (!$campana) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Campaña no encontrada'
                ]);
            }

            // Validaciones de transición de estado
            $error = $this->validarTransicionEstado($campana['estado'], $nuevoEstado);
            if ($error) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => $error
                ]);
            }

            // Actualizar estado
            $data = ['estado' => $nuevoEstado];
            
            if ($nuevoEstado === 'finalizada') {
                $data['fecha_finalizacion'] = date('Y-m-d H:i:s');
            }

            $this->campanaModel->update($id, $data);

            // Registrar actividad
            $this->registrarActividad($id, 'estado_cambiado', "Estado cambiado a: {$nuevoEstado}");

            return $this->response->setJSON([
                'success' => true,
                'estado' => $nuevoEstado,
                'message' => 'Estado actualizado correctamente'
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al cambiar estado: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Duplicar campaña
     */
    public function duplicar($id)
    {
        try {
            $campanaOriginal = $this->campanaModel->find($id);
            
            if (!$campanaOriginal) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Campaña no encontrada'
                ]);
            }

            // Preparar datos para duplicado
            unset($campanaOriginal['idcampania']);
            $campanaOriginal['nombre'] = $campanaOriginal['nombre'] . ' - Copia';
            $campanaOriginal['estado'] = 'borrador';
            $campanaOriginal['fecha_creacion'] = date('Y-m-d H:i:s');
            $campanaOriginal['creado_por'] = session()->get('idusuario');

            // Crear nueva campaña
            $nuevaId = $this->campanaModel->insert($campanaOriginal);

            // Copiar medios/difusiones
            $medios = $this->campanaModel->getMediosParaDuplicar($id);
            if ($medios) {
                $this->campanaModel->guardarDifusiones($nuevaId, $medios);
            }

            $this->registrarActividad($nuevaId, 'duplicada', "Duplicada desde campaña #{$id}");

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Campaña duplicada exitosamente',
                'nueva_id' => $nuevaId
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al duplicar: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Obtener métricas y analytics del dashboard
     */
    public function analytics()
    {
        $periodo = $this->request->getGet('periodo') ?? '30d';
        
        try {
            $datos = [
                'metricas_generales' => $this->campanaModel->getMetricas(),
                'rendimiento_temporal' => $this->campanaModel->getRendimientoTemporal($periodo),
                'distribucion_estados' => $this->campanaModel->getDistribucionEstados(),
                'top_campanas' => $this->campanaModel->getTopCampanas(5),
                'roi_por_medio' => $this->campanaModel->getROIPorMedio(),
                'tendencias' => $this->campanaModel->getTendencias($periodo)
            ];

            return $this->response->setJSON([
                'success' => true,
                'data' => $datos
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al obtener analytics: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Exportar datos de campañas
     */
    public function exportar()
    {
        $formato = $this->request->getGet('format') ?? 'excel';
        $filtros = $this->request->getGet();
        
        try {
            $campanas = $this->campanaModel->getCampanasFiltered($filtros);
            
            if ($formato === 'excel') {
                return $this->exportarExcel($campanas);
            } elseif ($formato === 'pdf') {
                return $this->exportarPDF($campanas);
            } else {
                return $this->exportarCSV($campanas);
            }

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al exportar: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Eliminar campaña con validaciones
     */
    public function eliminar($id)
    {
        try {
            $campana = $this->campanaModel->find($id);
            
            if (!$campana) {
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Campaña no encontrada'
                    ]);
                }
                return redirect()->to('campanas')->with('error', 'Campaña no encontrada');
            }

            // Validar si se puede eliminar
            if ($campana['estado'] === 'activa') {
                $mensaje = 'No se puede eliminar una campaña activa. Pausela primero.';
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => $mensaje
                    ]);
                }
                return redirect()->to('campanas')->with('error', $mensaje);
            }

            // Eliminar
            $this->campanaModel->eliminarCampana($id);
            
            $mensaje = 'Campaña eliminada exitosamente';
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => $mensaje
                ]);
            }
            
            return redirect()->to('campanas')->with('success', $mensaje);

        } catch (\Exception $e) {
            $mensaje = 'Error al eliminar: ' . $e->getMessage();
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => $mensaje
                ]);
            }
            return redirect()->to('campanas')->with('error', $mensaje);
        }
    }

    /**
     * API: Resumen para dashboard
     */
    public function resumen()
    {
        try {
            $metricas = $this->campanaModel->getMetricas();
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $metricas
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al obtener resumen: ' . $e->getMessage()
            ]);
        }
    }

    // ===== MÉTODOS PRIVADOS =====

    private function validarTransicionEstado($estadoActual, $nuevoEstado)
    {
        $transiciones_validas = [
            'borrador' => ['activa', 'cancelada'],
            'activa' => ['pausada', 'finalizada', 'cancelada'],
            'pausada' => ['activa', 'finalizada', 'cancelada'],
            'finalizada' => [], // No se puede cambiar
            'cancelada' => [] // No se puede cambiar
        ];

        if (!isset($transiciones_validas[$estadoActual])) {
            return 'Estado actual inválido';
        }

        if (!in_array($nuevoEstado, $transiciones_validas[$estadoActual])) {
            return "No se puede cambiar de '{$estadoActual}' a '{$nuevoEstado}'";
        }

        return null;
    }

    private function registrarActividad($campaignId, $accion, $descripcion)
    {
        $this->db->table('campana_actividad')->insert([
            'idcampania' => $campaignId,
            'usuario_id' => session()->get('idusuario'),
            'accion' => $accion,
            'descripcion' => $descripcion,
            'fecha' => date('Y-m-d H:i:s')
        ]);
    }

    private function calcularConversion($campaignId)
    {
        // Lógica para calcular tasa de conversión
        $leads = $this->campanaModel->getTotalLeads($campaignId);
        $conversiones = $this->campanaModel->getConversiones($campaignId);
        
        return $leads > 0 ? round(($conversiones / $leads) * 100, 2) : 0;
    }

    private function exportarExcel($data)
    {
        // Implementar exportación a Excel
        $filename = 'campanas_' . date('Y-m-d') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        // Aquí iría la lógica de generación del Excel
        echo "Excel export functionality"; // Placeholder
    }

    private function exportarPDF($data)
    {
        // Implementar exportación a PDF
        $filename = 'campanas_' . date('Y-m-d') . '.pdf';
        
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        // Aquí iría la lógica de generación del PDF
        echo "PDF export functionality"; // Placeholder
    }

    private function exportarCSV($data)
    {
        $filename = 'campanas_' . date('Y-m-d') . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // Headers
        fputcsv($output, [
            'ID', 'Nombre', 'Descripción', 'Estado', 'Prioridad',
            'Fecha Inicio', 'Fecha Fin', 'Presupuesto', 'Responsable'
        ]);
        
        // Data
        foreach ($data as $row) {
            fputcsv($output, [
                $row['idcampania'],
                $row['nombre'],
                $row['descripcion'],
                $row['estado'],
                $row['prioridad'],
                $row['fecha_inicio'],
                $row['fecha_fin'],
                $row['presupuesto'],
                $row['responsable_nombre']
            ]);
        }
        
        fclose($output);
    }
}