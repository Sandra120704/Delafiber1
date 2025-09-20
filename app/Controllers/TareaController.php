<?php

namespace App\Controllers;

use App\Models\TareaModel;
use App\Models\LeadModel;
use App\Models\PersonaModel;
use App\Models\UsuarioModel;
use Config\Database;

class TareaController extends BaseController
{
    protected $tareaModel;
    protected $leadModel;
    protected $personaModel;
    protected $usuarioModel;
    protected $db;

    public function __construct()
    {
        $this->tareaModel = new TareaModel();
        $this->leadModel = new LeadModel();
        $this->personaModel = new PersonaModel();
        $this->usuarioModel = new UsuarioModel();
        $this->db           = Database::connect();
    }

    public function calendario()
    {
        $mes = $this->request->getVar('mes') ?? date('m');
        $anio = $this->request->getVar('anio') ?? date('Y');

        $db = Database::connect();
        $builder = $db->table('tareas t');
        $builder->select('
            t.*,
            p.nombres as lead_nombres,
            p.apellidos as lead_apellidos
        ');
        $builder->join('leads l', 't.idlead = l.idlead', 'left');
        $builder->join('personas p', 'l.idpersona = p.idpersona', 'left');
        $builder->where('MONTH(t.fecha_inicio)', $mes);
        $builder->where('YEAR(t.fecha_inicio)', $anio);

        $tareas = $builder->get()->getResultArray();

        $data = [
            'header' => view('layouts/header'),
            'footer' => view('layouts/footer'),
            'tareas' => $tareas,
            'mes_actual' => $mes,
            'anio_actual' => $anio
        ];

        return view('tareas/calendario', $data);
    }

    public function index()
    {
        $filtro = $this->request->getVar('filtro') ?? 'todas';
        $usuarioId = session()->get('idusuario');

        // Obtener tareas con las informaciones de leads y personas
        $db = Database::connect();
        $builder = $db->table('tareas t');
        $builder->select('
            t.*,
            p.nombres as lead_nombres,
            p.apellidos as lead_apellidos,
            p.telefono,
            u.usuario as asignado_a
        ');
        $builder->join('leads l', 't.idlead = l.idlead', 'left');
        $builder->join('personas p', 'l.idpersona = p.idpersona', 'left');
        $builder->join('usuarios u', 't.idusuario = u.idusuario', 'left');

        // Aplicar filtros
        switch ($filtro) {
            case 'pendientes':
                $builder->where('t.estado', 'Pendiente');
                break;
            case 'proceso':
                $builder->where('t.estado', 'En progreso');
                break;
            case 'completadas':
                $builder->where('t.estado', 'Completada');
                break;
            case 'mis_tareas':
                if ($usuarioId) {
                    $builder->where('t.idusuario', $usuarioId);
                }
                break;
            case 'hoy':
                $builder->where('DATE(t.fecha_inicio)', date('Y-m-d'));
                break;
        }

        $builder->orderBy('t.fecha_inicio', 'ASC');
        $tareas = $builder->get()->getResultArray();

        // Estadísticas
        $estadisticas = [
            'total' => $db->table('tareas')->countAllResults(),
            'pendientes' => $db->table('tareas')->where('estado', 'Pendiente')->countAllResults(),
            'proceso' => $db->table('tareas')->where('estado', 'En progreso')->countAllResults(),
            'completadas' => $db->table('tareas')->where('estado', 'Completada')->countAllResults(),
            'mis_tareas' => $usuarioId ? $db->table('tareas')->where('idusuario', $usuarioId)->countAllResults() : 0
        ];

        $data = [
            'header' => view('layouts/header'),
            'footer' => view('layouts/footer'),
            'tareas' => $tareas,
            'filtro_actual' => $filtro,
            'estadisticas' => $estadisticas,
            'leads' => $this->obtenerLeadsParaSelect(),
            'usuarios' => $this->usuarioModel->findAll()
        ];

        return view('tarea/index', $data);
    }

    public function crear()
    {
        if ($this->request->getMethod() === 'POST') {
            return $this->guardar();
        }

        $data = [
            'header' => view('layouts/header'),
            'footer' => view('layouts/footer'),
            'leads' => $this->obtenerLeadsParaSelect(),
            'usuarios' => $this->usuarioModel->findAll()
        ];

        return view('tareas/crear', $data);
    }

    public function guardar()
    {
        $rules = [
            'idlead' => 'required|integer',
            'idusuario' => 'required|integer',
            'descripcion' => 'required|min_length[10]',
            'fecha_inicio' => 'required|valid_date',
            'fecha_fin' => 'required|valid_date',
            'estado' => 'required|in_list[Pendiente,En progreso,Completada]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $this->validator->getErrors()
            ]);
        }

        try {
            $data = [
                'idlead' => $this->request->getVar('idlead'),
                'idusuario' => $this->request->getVar('idusuario'),
                'descripcion' => $this->request->getVar('descripcion'),
                'fecha_inicio' => $this->request->getVar('fecha_inicio'),
                'fecha_fin' => $this->request->getVar('fecha_fin'),
                'estado' => $this->request->getVar('estado')
            ];

            $id = $this->tareaModel->insert($data);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Tarea creada correctamente',
                'idtarea' => $id
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al crear la tarea: ' . $e->getMessage()
            ]);
        }
    }

    public function editar($id)
    {
        $tarea = $this->tareaModel->find($id);
        if (!$tarea) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Tarea no encontrada');
        }

        if ($this->request->getMethod() === 'POST') {
            return $this->actualizar($id);
        }

        $data = [
            'header' => view('layouts/header'),
            'footer' => view('layouts/footer'),
            'tarea' => $tarea,
            'leads' => $this->obtenerLeadsParaSelect(),
            'usuarios' => $this->usuarioModel->findAll()
        ];

        return view('tareas/editar', $data);
    }

    public function actualizar($id)
    {
        $tarea = $this->tareaModel->find($id);
        if (!$tarea) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tarea no encontrada'
            ]);
        }

        $rules = [
            'idlead' => 'required|integer',
            'idusuario' => 'required|integer',
            'descripcion' => 'required|min_length[10]',
            'fecha_inicio' => 'required|valid_date',
            'fecha_fin' => 'required|valid_date',
            'estado' => 'required|in_list[Pendiente,En progreso,Completada]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $this->validator->getErrors()
            ]);
        }

        try {
            $data = [
                'idlead' => $this->request->getVar('idlead'),
                'idusuario' => $this->request->getVar('idusuario'),
                'descripcion' => $this->request->getVar('descripcion'),
                'fecha_inicio' => $this->request->getVar('fecha_inicio'),
                'fecha_fin' => $this->request->getVar('fecha_fin'),
                'estado' => $this->request->getVar('estado')
            ];

            $this->tareaModel->update($id, $data);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Tarea actualizada correctamente'
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al actualizar la tarea: ' . $e->getMessage()
            ]);
        }
    }

    public function eliminar($id)
    {
        try {
            $tarea = $this->tareaModel->find($id);
            if (!$tarea) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Tarea no encontrada'
                ]);
            }

            $this->tareaModel->delete($id);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Tarea eliminada correctamente'
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al eliminar la tarea: ' . $e->getMessage()
            ]);
        }
    }

    public function cambiarEstado($id)
    {
        $tarea = $this->tareaModel->find($id);
        if (!$tarea) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tarea no encontrada'
            ]);
        }

        $nuevoEstado = $this->request->getVar('estado');
        if (!in_array($nuevoEstado, ['Pendiente', 'En progreso', 'Completada'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Estado no válido'
            ]);
        }

        try {
            $this->tareaModel->update($id, ['estado' => $nuevoEstado]);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Estado actualizado correctamente',
                'nuevo_estado' => $nuevoEstado
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al cambiar el estado: ' . $e->getMessage()
            ]);
        }
    }

    // Método auxiliar para obtener leads con personas
    private function obtenerLeadsParaSelect()
    {
        $db = Database::connect();
        $builder = $db->table('leads l');
        $builder->select('l.idlead, p.nombres, p.apellidos, p.telefono');
        $builder->join('personas p', 'l.idpersona = p.idpersona');
        $builder->orderBy('p.nombres');
        
        return $builder->get()->getResultArray();
    }
    public function resumen()
    {
        // Para el panel flotante
        $data = [
            'pendientes_hoy' => $this->tareaModel->contarTareasHoy(),
            'vencidas' => $this->tareaModel->contarTareasVencidas(),
            'total_semana' => $this->tareaModel->contarTareasSemana(),
            'completadas_hoy' => $this->tareaModel->contarCompletadasHoy()
        ];
        return $this->response->setJSON($data);
    }
    public function obtenerTareasPorLead($idlead)
    {
        $tareas = $this->tareaModel->obtenerPorLead($idlead);
        return $this->response->setJSON(['success' => true, 'tareas' => $tareas]);
    }

    public function completar($idtarea)
    {
        $notas = $this->request->getVar('notas_resultado');
        $this->tareaModel->update($idtarea, [
            'estado' => 'completada',
            'fecha_completado' => date('Y-m-d H:i:s'),
            'notas_resultado' => $notas
        ]);
        return $this->response->setJSON(['success' => true]);
    }
}