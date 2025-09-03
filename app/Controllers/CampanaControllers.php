<?php

namespace App\Controllers;
use App\Models\CampanaModels;

class CampanaControllers extends BaseController
{
    protected $campanaModel;

    public function __construct() {
        $this->campanaModel = new CampanaModels();
    }

    public function index() {
        $data['campanas'] = $this->campanaModel->findAll();
        $data['header'] = view('Layouts/header');
        $data['footer'] = view('Layouts/footer');

        return view('campanas/index', $data);
    }

    public function crear() {
        $data['header'] = view('Layouts/header');
        $data['footer'] = view('Layouts/footer');

        if ($this->request->getMethod() === 'post') {
            $this->campanaModel->save([
                'nombre' => $this->request->getPost('nombre'),
                'descripcion' => $this->request->getPost('descripcion'),
                'fechainicio' => $this->request->getPost('fechainicio'),
                'fechafin' => $this->request->getPost('fechafin'),
                'inversion' => $this->request->getPost('inversion'),
                'estado' => $this->request->getPost('estado')
            ]);
            return redirect()->to('/campanas');
        }

        return view('campanas/crear', $data);
    }
}
