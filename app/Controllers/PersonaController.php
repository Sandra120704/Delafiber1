<?php

namespace App\Controllers;

use App\Models\PersonaModel;
use App\Models\DepartamentoModel;
use App\Models\ProvinciaModel;
use App\Models\DistritoModels;

class PersonaController extends BaseController
{
    public function index()
    {
        $personaModel = new PersonaModel();
        $personas = $personaModel->obtenerPersona();

        $datos['header'] = view('Layouts/header');
        $datos['footer'] = view('Layouts/footer');
        $datos['personas'] = $personas;

        return view('Personas/index', $datos);
    }

    public function crear()
    {
        $departamentoModel = new DepartamentoModel();
        $departamentos = $departamentoModel->asObject()->findAll();

        $datos['header'] = view('Layouts/header');
        $datos['footer'] = view('Layouts/footer');
        $datos['departamentos'] = $departamentos;

        return view('Personas/crear', $datos);
    }
    public function form()
    {
        $departamentoModel = new DepartamentoModel();
        $departamentos = $departamentoModel->asObject()->findAll();

        return view('Personas/crear', [
            'departamentos' => $departamentos
        ]);
    }

    public function guardar()
    {
        $personaModel = new PersonaModel();

        $data = [
            'apellidos'      => $this->request->getPost('apellidos'),
            'nombres'        => $this->request->getPost('nombres'),
            'telprimario'    => $this->request->getPost('telprimario'),
            'telalternativo' => $this->request->getPost('telalternativo'),
            'email'          => $this->request->getPost('email'),
            'direccion'      => $this->request->getPost('direccion'),
            'referencia'     => $this->request->getPost('referencia'),
            'iddistrito'     => $this->request->getPost('iddistrito'),
        ];

        $personaModel->insert($data);

        return redirect()->to(base_url('personas'));
}

    // Para AJAX: obtener provincias de un departamento
    public function getProvincias($idDepartamento)
    {
        $provinciaModel = new ProvinciaModel();
        $provincias = $provinciaModel->where('iddepartamento', $idDepartamento)->findAll();
        return $this->response->setJSON($provincias);
    }

    // Para AJAX: obtener distritos de una provincia
    public function getDistritos($idProvincia)
    {
        $distritoModel = new DistritoModels();
        $distritos = $distritoModel->where('idprovincia', $idProvincia)->findAll();
        return $this->response->setJSON($distritos);
    }
    public function editar($idpersona)
    {
        $personaModel = new PersonaModel();
        $departamentosModel = new DepartamentoModel();

        // Traer persona con distrito, provincia y departamento
        $persona = $personaModel
            ->select('personas.*, distritos.idprovincia, provincias.iddepartamento')
            ->join('distritos', 'personas.iddistrito = distritos.iddistrito')
            ->join('provincias', 'distritos.idprovincia = provincias.idprovincia')
            ->where('personas.idpersona', $idpersona)
            ->asObject() // <-- fuerza objeto
            ->first();

        $data['persona'] = $persona;
        $data['departamentos'] = $departamentosModel->asObject()->findAll(); // asegurar objetos

        return view('Personas/editar', $data);
    }





    public function actualizar($idpersona)
    {
        $personaModel = new PersonaModel();
        $datos = $this->request->getPost();
        $datos['modificado'] = date('Y-m-d H:i:s');

        $personaModel->update($idpersona, $datos);

        return redirect()->to(base_url('persona'));
    }

}
