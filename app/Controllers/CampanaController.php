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

    // Listado de campañas
    public function index()
    {
        $campanas = $this->campanaModel->findAll();
        $campanas_activas = $this->campanaModel->where('estado', 'Activo')->countAllResults();
        $presupuesto_total = $this->campanaModel->selectSum('presupuesto')->first()['presupuesto'] ?? 0;
        $total_leads = 0; // luego conectas con tabla leads

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
            $difusiones = $this->campanaModel->getMedios($id); // array de idmedio
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

        // Guardar difusiones (medios)
        $this->campanaModel->guardarDifusiones($idcampania, $data['medios'] ?? []);

        return redirect()->to(site_url('campanas'));
    }
    public function detalleMedios($idcampania)
    {
        $difusiones = $this->campanaModel->getMedios($idcampania); 
        // $difusiones debe traer un array con ['nombre' => ..., 'inversion' => ..., 'leads' => ...]

        return $this->response->setJSON($difusiones);
    }
    public function getMediosCampana($idcampania)
    {
        $medios = $this->campanaModel->getMedios($idcampania);
        return $this->response->setJSON($medios);
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
    public function resumen()
    {
        $campanaModel = new CampanaModel();
        $activas = $campanaModel->contarActivas();

        return $this->response->setJSON([
            'success' => true,
            'activas' => $activas
        ]);
    }

    public function estado($id)
    {
        $estado = $this->request->getPost('estado');

        $campanaModel = new CampanaModel();

        $update = $campanaModel->update($id, ['estado' => $estado]);

        if ($update) {
            return $this->response->setJSON([
                'success' => true,
                'estado'  => $estado
            ]);
        } else {
            return $this->response->setJSON(['success' => false]);
        }
    }

    // Eliminar campaña
    public function eliminar($id)
    {
        if($id){
            $this->campanaModel->eliminarCampana($id);
        }
        return redirect()->to(site_url('campanas'));
    }
}
