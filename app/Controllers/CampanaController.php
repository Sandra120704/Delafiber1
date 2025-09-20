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
        $this->db = Database::connect();
    }

    public function index()
    {
        try {
            $campanas = $this->campanaModel->getAllWithDetails();
            $metricas = $this->campanaModel->getMetricas();
            $usuarios = $this->campanaModel->getUsuarios();

            $datos = [
                'header' => view('layouts/header'),
                'footer' => view('layouts/footer'),
                'campanas' => $campanas,
                'metricas' => $metricas,
                'usuarios' => $usuarios
            ];

            return view('campanas/index', $datos);
        } catch (\Exception $e) {
            // TODO: Implementar mejor logging aquí
            log_message('error', 'Error en CampanaController::index: ' . $e->getMessage());
            return redirect()->to('/')->with('error', 'Error al cargar las campañas');
        }
    }

    public function getCampanas()
    {
        try {
            $filtros = [
                'search' => $this->request->getGet('search'),
                'estado' => $this->request->getGet('estado'),
                'responsable' => $this->request->getGet('responsable'),
                'presupuesto_min' => $this->request->getGet('presupuesto_min'),
                'presupuesto_max' => $this->request->getGet('presupuesto_max'),
                'fecha_inicio' => $this->request->getGet('fecha_inicio'),
                'fecha_fin' => $this->request->getGet('fecha_fin')
            ];

            $campanas = $this->campanaModel->getCampanasFiltered($filtros);
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $campanas,
                'total' => count($campanas)
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al obtener campañas: ' . $e->getMessage()
            ]);
        }
    }

    public function crear($id = null)
    {
        try {
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
                'medios' => $this->campanaModel->getMediosDisponibles(),
                'difusiones' => $difusiones,
                'usuarios' => $this->campanaModel->getUsuarios(),
                'estados' => $this->campanaModel->getEstados()
            ];

            return view('campanas/crear', $datos);
        } catch (\Exception $e) {
            log_message('error', 'Error en CampanaController::crear: ' . $e->getMessage());
            return redirect()->to('campanas')->with('error', 'Error al cargar el formulario');
        }
    }

    public function guardar()
    {
        try {
            // Validar entrada
            $validation = \Config\Services::validation();
            
            $rules = [
                'nombre' => 'required|min_length[3]|max_length[150]',
                'descripcion' => 'permit_empty|max_length[1000]',
                'fecha_inicio' => 'permit_empty|valid_date',
                'fecha_fin' => 'permit_empty|valid_date',
                'presupuesto' => 'permit_empty|numeric|greater_than_equal_to[0]',
                'responsable' => 'permit_empty|integer'
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON([
                    'success' => false,
                    'errors' => $validation->getErrors()
                ]);
            }

            $data = $this->request->getPost();
            
            // Validar fechas si ambas están presentes
            if (!empty($data['fecha_inicio']) && !empty($data['fecha_fin'])) {
                $fechaValidation = $this->validarFechas($data['fecha_inicio'], $data['fecha_fin']);
                if (!$fechaValidation['valid']) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => $fechaValidation['message']
                    ]);
                }
            }

            // Preparar datos para el modelo
            $campanaData = [
                'nombre' => trim($data['nombre']),
                'descripcion' => trim($data['descripcion'] ?? ''),
                'fecha_inicio' => !empty($data['fecha_inicio']) ? $data['fecha_inicio'] : null,
                'fecha_fin' => !empty($data['fecha_fin']) ? $data['fecha_fin'] : null,
                'presupuesto' => !empty($data['presupuesto']) ? floatval($data['presupuesto']) : 0.00,
                'estado' => $data['estado'] ?? 'Activa',
                'responsable' => !empty($data['responsable']) ? intval($data['responsable']) : null
            ];

            // Guardar o actualizar
            if (!empty($data['idcampania'])) {
                // Actualizar campaña existente
                $result = $this->campanaModel->actualizarCampana($data['idcampania'], $campanaData);
                $idcampania = $data['idcampania'];
                $action = 'actualizada';
            } else {
                // Crear nueva campaña
                $idcampania = $this->campanaModel->crearCampana($campanaData);
                $action = 'creada';
            }

            if (!$idcampania) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Error al guardar la campaña'
                ]);
            }

            // Guardar medios/difusiones si existen
            if (!empty($data['medios']) && is_array($data['medios'])) {
                $medios = [];
                for ($i = 0; $i < count($data['medios']); $i++) {
                    if (!empty($data['medios'][$i])) {
                        $medios[] = [
                            'idmedio' => $data['medios'][$i],
                            'presupuesto' => floatval($data['inversion'][$i] ?? 0),
                            'leads_generados' => 0 // Inicialmente sin leads
                        ];
                    }
                }
                
                if (!empty($medios)) {
                    $this->campanaModel->guardarDifusiones($idcampania, $medios);
                }
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => "Campaña {$action} exitosamente",
                'campaign_id' => $idcampania
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error en CampanaController::guardar: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error interno del servidor: ' . $e->getMessage()
            ]);
        }
    }

    public function detalle($id)
    {
        try {
            // Verificar que el ID sea válido
            if (!is_numeric($id) || $id <= 0) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'ID de campaña inválido'
                ]);
            }

            $campana = $this->campanaModel->getDetalle($id);
            
            if (!$campana) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Campaña no encontrada'
                ]);
            }

            // Obtener medios asociados
            $medios = $this->campanaModel->getMedios($id);
            
            // Obtener métricas adicionales
            $metricas = $this->campanaModel->getMetricasCampana($id);

            return $this->response->setJSON([
                'success' => true,
                'campana' => $campana,
                'medios' => $medios,
                'metricas' => $metricas
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error en CampanaController::detalle: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al obtener detalle: ' . $e->getMessage()
            ]);
        }
    }

    public function estado($id)
    {
        try {
            log_message('info', "CampanaController::estado - ID recibido: {$id}");
            
            if (!is_numeric($id) || $id <= 0) {
                log_message('error', "ID de campaña inválido: {$id}");
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'ID de campaña inválido'
                ]);
            }

            $nuevoEstado = $this->request->getPost('estado');
            log_message('info', "Nuevo estado recibido: {$nuevoEstado}");
            
            $estados_validos = ['Activa', 'Inactiva'];

            if (!in_array($nuevoEstado, $estados_validos)) {
                log_message('error', "Estado inválido recibido: {$nuevoEstado}");
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Estado inválido. Debe ser "Activa" o "Inactiva"'
                ]);
            }

            // Verificar que la campaña existe
            $campana = $this->campanaModel->find($id);
            if (!$campana) {
                log_message('error', "Campaña no encontrada con ID: {$id}");
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Campaña no encontrada'
                ]);
            }

            log_message('info', "Actualizando campaña {$id} de '{$campana['estado']}' a '{$nuevoEstado}'");

            // Actualizar estado
            $resultado = $this->campanaModel->update($id, ['estado' => $nuevoEstado]);

            if ($resultado) {
                log_message('info', "Estado actualizado exitosamente para campaña {$id}");
                return $this->response->setJSON([
                    'success' => true,
                    'estado' => $nuevoEstado,
                    'message' => "Estado cambiado a {$nuevoEstado} correctamente"
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Error al actualizar el estado'
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'Error en CampanaController::estado: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error interno: ' . $e->getMessage()
            ]);
        }
    }

    public function eliminar($id)
    {
        try {
            if (!is_numeric($id) || $id <= 0) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'ID de campaña inválido'
                ]);
            }

            $campana = $this->campanaModel->find($id);
            
            if (!$campana) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Campaña no encontrada'
                ]);
            }

            // Validar si se puede eliminar (opcional)
            if ($campana['estado'] === 'Activa') {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'No se puede eliminar una campaña activa. Desactívela primero.'
                ]);
            }

            // Eliminar usando el método del modelo que maneja la transacción
            $resultado = $this->campanaModel->eliminarCampana($id);
            
            if ($resultado) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Campaña eliminada exitosamente'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Error al eliminar la campaña'
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'Error en CampanaController::eliminar: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error interno: ' . $e->getMessage()
            ]);
        }
    }

    public function resumen()
    {
        try {
            $metricas = $this->campanaModel->getMetricas();
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $metricas
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error en CampanaController::resumen: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al obtener resumen: ' . $e->getMessage()
            ]);
        }
    }

    public function exportar()
    {
        try {
            $formato = $this->request->getGet('format') ?? 'csv';
            $filtros = $this->request->getGet();
            
            // Obtener datos filtrados
            $campanas = $this->campanaModel->getCampanasFiltered($filtros);
            
            switch ($formato) {
                case 'excel':
                    return $this->exportarExcel($campanas);
                case 'pdf':
                    return $this->exportarPDF($campanas);
                default:
                    return $this->exportarCSV($campanas);
            }

        } catch (\Exception $e) {
            log_message('error', 'Error en CampanaController::exportar: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al exportar: ' . $e->getMessage()
            ]);
        }
    }

    private function exportarCSV($data)
    {
        try {
            $filename = 'campanas_' . date('Y-m-d_H-i-s') . '.csv';
            
            // Headers para descarga
            $this->response->setHeader('Content-Type', 'text/csv; charset=utf-8');
            $this->response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
            
            // Crear el CSV en memoria
            $output = fopen('php://temp', 'r+');
            
            // BOM para UTF-8 (para que Excel lo reconozca correctamente)
            fwrite($output, "\xEF\xBB\xBF");
            
            // Headers del CSV
            fputcsv($output, [
                'ID',
                'Nombre',
                'Descripción', 
                'Estado',
                'Fecha Inicio',
                'Fecha Fin',
                'Presupuesto',
                'Responsable',
                'Inversión Total',
                'Leads Total',
                'ROI (%)'
            ], ';'); // Usar punto y coma como separador
            
            // Datos
            foreach ($data as $row) {
                $roi = 0;
                if (!empty($row['inversion_total']) && $row['inversion_total'] > 0) {
                    $roi = round(($row['leads_total'] ?? 0) / $row['inversion_total'] * 100, 2);
                }
                
                fputcsv($output, [
                    $row['idcampania'] ?? '',
                    $row['nombre'] ?? '',
                    $row['descripcion'] ?? '',
                    $row['estado'] ?? '',
                    $row['fecha_inicio'] ?? '',
                    $row['fecha_fin'] ?? '',
                    number_format($row['presupuesto'] ?? 0, 2),
                    $row['responsable_nombre'] ?? 'No asignado',
                    number_format($row['inversion_total'] ?? 0, 2),
                    $row['leads_total'] ?? 0,
                    $roi
                ], ';');
            }
            
            // Enviar el archivo
            rewind($output);
            $csvContent = stream_get_contents($output);
            fclose($output);
            
            return $this->response->setBody($csvContent);

        } catch (\Exception $e) {
            log_message('error', 'Error al exportar CSV: ' . $e->getMessage());
            throw $e;
        }
    }

    private function exportarExcel($data)
    {
        // Placeholder para implementación futura con PhpSpreadsheet
        $filename = 'campanas_' . date('Y-m-d') . '.xlsx';
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Exportación a Excel no implementada aún. Use formato CSV.'
        ]);
    }

    private function exportarPDF($data)
    {
        // Placeholder para implementación futura con TCPDF o similar
        $filename = 'campanas_' . date('Y-m-d') . '.pdf';
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Exportación a PDF no implementada aún. Use formato CSV.'
        ]);
    }

    // Método auxiliar para calcular conversiones (si se necesita)
    private function calcularConversion($campaignId)
    {
        try {
            $leads = $this->campanaModel->getTotalLeads($campaignId);
            // Lógica básica: asumir 15% de conversión como ejemplo
            $conversiones = round($leads * 0.15);
            
            return $leads > 0 ? round(($conversiones / $leads) * 100, 2) : 0;
        } catch (\Exception $e) {
            log_message('error', 'Error al calcular conversión: ' . $e->getMessage());
            return 0;
        }
    }

    // Método auxiliar para validar fechas
    private function validarFechas($fecha_inicio, $fecha_fin)
    {
        try {
            $inicio = strtotime($fecha_inicio);
            $fin = strtotime($fecha_fin);
            
            if ($inicio === false || $fin === false) {
                return [
                    'valid' => false,
                    'message' => 'Formato de fecha inválido'
                ];
            }
            
            if ($fin <= $inicio) {
                return [
                    'valid' => false,
                    'message' => 'La fecha fin debe ser posterior a la fecha de inicio'
                ];
            }
            
            // Validar que las fechas no sean muy lejanas (opcional)
            $diferencia_anos = ($fin - $inicio) / (365 * 24 * 60 * 60);
            if ($diferencia_anos > 5) {
                return [
                    'valid' => false,
                    'message' => 'La campaña no puede durar más de 5 años'
                ];
            }
            
            return ['valid' => true, 'message' => 'Fechas válidas'];
            
        } catch (\Exception $e) {
            log_message('error', 'Error en validación de fechas: ' . $e->getMessage());
            return [
                'valid' => false,
                'message' => 'Error al validar las fechas'
            ];
        }
    }
}