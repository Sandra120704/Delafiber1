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

    public function form($idpersona = null)
    {
        $departamentoModel = new DepartamentoModel();
        $departamentos = $departamentoModel->findAll();

        $data['departamentos'] = $departamentos;

        if ($idpersona) {
            $personaModel = new PersonaModel();
            $persona = $personaModel
                ->select('personas.*, distritos.idprovincia, provincias.iddepartamento')
                ->join('distritos', 'personas.iddistrito = distritos.iddistrito')
                ->join('provincias', 'distritos.idprovincia = provincias.idprovincia')
                ->where('personas.idpersona', $idpersona)
                ->first();

            if (is_array($persona)) $persona = (object) $persona;

            $data['persona'] = $persona;
        }

        return view('Personas/form', $data); // Unifica crear y editar
    }

   public function guardar()
    {
        $personaModel = new PersonaModel();
        $data = $this->request->getPost();

        $success = $personaModel->insert($data);

        return $this->response->setJSON([
            'success' => $success ? true : false,
            'mensaje' => $success ? 'Persona guardada correctamente' : 'Error al guardar'
        ]);
    }

    public function actualizar($idpersona)
    {
        $personaModel = new PersonaModel();
        $datos = $this->request->getPost();
        $datos['modificado'] = date('Y-m-d H:i:s');

        $success = $personaModel->update($idpersona, $datos);

        return $this->response->setJSON([
            'success' => $success ? true : false,
            'mensaje' => $success ? 'Persona actualizada correctamente' : 'Error al actualizar'
        ]);
    }



    public function eliminar()
    {
        $idpersona = $this->request->getPost('idpersona');
        $personaModel = new PersonaModel();
        $personaModel->delete($idpersona);

        return $this->response->setJSON(['mensaje' => 'Persona eliminada']);
    }

    public function getProvincias($idDepartamento)
    {
        $provinciaModel = new ProvinciaModel();
        $provincias = $provinciaModel->where('iddepartamento', $idDepartamento)->findAll();
        return $this->response->setJSON($provincias);
    }

    public function getDistritos($idProvincia)
    {
        $distritoModel = new DistritoModels();
        $distritos = $distritoModel->where('idprovincia', $idProvincia)->findAll();
        return $this->response->setJSON($distritos);
    }
}
