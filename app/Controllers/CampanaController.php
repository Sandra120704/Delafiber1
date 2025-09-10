<?php

namespace App\Controllers;
use App\Models\CampanaModel;
use App\Models\MedioModel;

class CampanaController extends BaseController
{
    protected $campanaModel;
    protected $medioModel;

    public function __construct()
    {
        $this->campanaModel = new CampanaModel();
        $this->medioModel = new MedioModel();
    }

    // Listado de campañas con métricas
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

    // Formulario Crear / Editar
    public function crear($id = null)
    {
        $campana = null;
        $difusiones = [];

        if ($id) {
            $campana = $this->campanaModel->find($id);
            $difusiones = $this->campanaModel->getMedios($id);
        }

        $medios = $this->medioModel->findAll();

        $datos = [
            'header' => view('layouts/header'),
            'footer' => view('layouts/footer'),
            'campana' => $campana,
            'medios' => $medios,
            'difusiones' => $difusiones
        ];

        return view('campanas/crear', $datos);
    }

    // Guardar campaña
    public function guardar()
    {
        $data = $this->request->getPost();

        $campanaData = [
            'nombre' => $data['nombre'],
            'descripcion' => $data['descripcion'],
            'fecha_inicio' => $data['fecha_inicio'],
            'fecha_fin' => $data['fecha_fin'],
            'presupuesto' => $data['presupuesto'],
            'estado' => $data['estado']
        ];

        if (!empty($data['idcampania'])) {
            $this->campanaModel->update($data['idcampania'], $campanaData);
            $idcampania = $data['idcampania'];
        } else {
            $idcampania = $this->campanaModel->insert($campanaData);
        }

        // Guardar difusiones
        $this->campanaModel->guardarDifusiones($idcampania, $data['medios'] ?? []);

        return redirect()->to(site_url('campanas'));
    }

    // Detalle de campaña con medios
    public function detalle($idcampania)
    {
        $campana = $this->campanaModel->find($idcampania);

        if (!$campana) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Campaña no encontrada'
            ])->setStatusCode(404);
        }

        $medios = $this->campanaModel->getMedios($idcampania);

        return $this->response->setJSON([
            'success' => true,
            'campana' => $campana,
            'medios' => $medios
        ]);
    }

    // Cambiar estado (Activo/Inactivo)
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

    // Resumen general para dashboard
    public function resumen()
    {
        return $this->response->setJSON([
            'success' => true,
            'activas' => $this->campanaModel->contarActivas(),
            'presupuesto_total' => $this->campanaModel->presupuestoTotal(),
            'total_leads' => $this->campanaModel->totalLeads()
        ]);
    }

    // Eliminar campaña
    public function eliminar($id)
    {
        if ($id) {
            $this->campanaModel->eliminarCampana($id);
        }
        return redirect()->to(site_url('campanas'));
    }
}
