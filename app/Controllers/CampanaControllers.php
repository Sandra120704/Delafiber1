<?php

namespace App\Controllers;
use App\Models\CampanaModels;

class CampanaControllers extends BaseController
{
    protected $campanaModel;

    public function __construct() {
        $this->campanaModel = new CampanaModels();
    }

    public function index()
    {
        $model = new CampanaModels();
        $data['campanas'] = $model->findAll();
        return view('Campanas/index', $data);
    }

    public function crear()
    {
        $model = new CampanaModels();

        // Recibir datos
        $data = [
            'nombre'      => $this->request->getPost('nombre'),
            'descripcion' => $this->request->getPost('descripcion'),
            'fechainicio' => $this->request->getPost('fechainicio'),
            'fechafin'    => $this->request->getPost('fechafin'),
            'inversion'   => $this->request->getPost('inversion'),
            'estado'      => $this->request->getPost('estado'),
        ];

        // Guardar en DB
        if ($model->insert($data)) {
            $data['id'] = $model->getInsertID();

            // Respuesta JSON
            return $this->response->setJSON([
                'success' => true,
                'campana' => $data
            ]);
        }

        // Si falla
        return $this->response->setJSON([
            'success' => false,
            'mensaje' => 'No se pudo guardar la campaña'
        ]);
    }
}