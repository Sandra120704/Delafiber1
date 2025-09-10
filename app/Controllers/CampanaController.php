<?php

namespace App\Controllers;
use App\Models\CampanaModel;
use App\Models\MedioModel;
use App\Models\UsuarioModel;

class CampanaController extends BaseController
{
    protected $campanaModel;
    protected $medioModel;
    protected $usuarioModel;

    public function __construct()
    {
        $this->campanaModel = new CampanaModel();
        $this->medioModel = new MedioModel();
        $this->usuarioModel = new UsuarioModel();
    }

    public function index()
    {
        $campanas = $this->campanaModel->findAll();
        $campanas_activas = $this->campanaModel->contarActivas();
        $presupuesto_total = $this->campanaModel->selectSum('presupuesto')->first()['presupuesto'] ?? 0;
        $total_leads = $this->campanaModel->totalLeads();

        $datos = [
            'header' => view('layouts/header'),
            'footer' => view('layouts/footer'),
            'campanas' => $campanas,
            'campanas_activas' => $campanas_activas,
            'presupuesto_total' => $presupuesto_total,
            'total_leads' => $total_leads
        ];

        return view('campanas/index', $datos);
    }

    public function crear($id = null)
    {
        $campana = null;
        $difusiones = [];

        if ($id) {
            $campana = $this->campanaModel->find($id);
            $difusiones = $this->campanaModel->getMedios($id);
        }

        $medios = $this->medioModel->findAll();

        $usuarioModel = new UsuarioModel(); 
        $usuarios = $usuarioModel->findAll();

        $datos = [
            'header' => view('layouts/header'),
            'footer' => view('layouts/footer'),
            'campana' => $campana,
            'medios' => $medios,
            'difusiones' => $difusiones,
            'usuarios' => $usuarios 
        ];

        return view('campanas/crear', $datos);
    }

    public function guardar()
    {
        $data = $this->request->getPost();

        $campanaData = [
            'nombre' => $data['nombre'],
            'descripcion' => $data['descripcion'],
            'fecha_inicio' => $data['fecha_inicio'],
            'fecha_fin' => $data['fecha_fin'],
            'presupuesto' => $data['presupuesto'],
            'estado' => 'Activo', // siempre activo al crear
            'segmento' => $data['segmento'] ?? null,
            'responsable' => session()->get('idusuario'),
            'objetivos' => $data['objetivos'] ?? null,
            'notas' => $data['notas'] ?? null
        ];

        if (!empty($data['idcampania'])) {
            // Al editar, no cambiamos fecha_creacion
            unset($campanaData['fecha_creacion']);
            $this->campanaModel->update($data['idcampania'], $campanaData);
            $idcampania = $data['idcampania'];
        } else {
            $campanaData['fecha_creacion'] = date('Y-m-d H:i:s');
            $idcampania = $this->campanaModel->insert($campanaData);
        }
        $this->campanaModel->guardarDifusiones($idcampania, $data['medios'] ?? []);

        return redirect()->to(site_url('campanas'));
    }

    public function detalle($id)
    {
        $campana = $this->campanaModel->find($id);
        if (!$campana) {
            return $this->response->setJSON(['error' => 'Campaña no encontrada']);
        }
        if (!empty($campana['responsable'])) {
            $usuario = $this->usuarioModel->find($campana['responsable']);
            $campana['responsable_nombre'] = $usuario['nombre'] ?? 'No asignado';
        } else {
            $campana['responsable_nombre'] = 'No asignado';
        }
            $campana['segmento'] = $campana['segmento'] ?? 'No definido';
            $campana['objetivos'] = $campana['objetivos'] ?? 'No definidos';
            $campana['notas'] = $campana['notas'] ?? 'Sin notas';

        $medios = $this->campanaModel->getMedios($id); 

        return $this->response->setJSON([
            'campana' => $campana,
            'medios'  => $medios
        ]);
    }

    public function cambiarEstado($id)
    {
        $estado = $this->request->getPost('estado');

        if (!in_array($estado, ['Activo', 'Inactivo'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Estado inválido'
            ])->setStatusCode(400);
        }

        $this->campanaModel->update($id, ['estado' => $estado]);

        return $this->response->setJSON([
            'success' => true,
            'estado' => $estado
        ]);
    }

    public function resumen()
    {
        return $this->response->setJSON([
            'success' => true,
            'activas' => $this->campanaModel->contarActivas(),
            'presupuesto_total' => $this->campanaModel->presupuestoTotal(),
            'total_leads' => $this->campanaModel->totalLeads()
        ]);
    }

    public function eliminar($id)
    {
        if ($id) {
            $this->campanaModel->eliminarCampana($id);
        }
        return redirect()->to(site_url('campanas'));
    }
}
