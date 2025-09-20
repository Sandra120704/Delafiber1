<?php

namespace App\Controllers;
use App\Models\MedioModel;

class MedioController extends BaseController
{
    protected $medioModel;

    public function __construct()
    {
        $this->medioModel = new MedioModel();
    }

    public function guardarMedio()
    {
        $data = $this->request->getJSON(true); 
        if (empty($data['nombre'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'El nombre es obligatorio']);
        }

        $idmedio = $this->medioModel->insert([
            'nombre' => $data['nombre'],
            'descripcion' => $data['descripcion'] ?? ''
        ]);

        return $this->response->setJSON(['success' => true, 'idmedio' => $idmedio]);
    }
    
}
