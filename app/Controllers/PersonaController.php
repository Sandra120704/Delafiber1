<?php

namespace App\Controllers;

use App\Models\DistritoModel;
use App\Models\PersonaModel;
use App\Models\DepartamentoModel;
use App\Models\ProvinciaModel;
use App\Models\DistritoModels;

class PersonaController extends BaseController
{
    public function index()
    {
        $personaModel = new PersonaModel();
        $personas = $personaModel->obtenerPersona(); // Método que devuelve todas las personas

        return view('Personas/index', [
            'header' => view('Layouts/header'),
            'footer' => view('Layouts/footer'),
            'personas' => $personas
        ]);
    }

    public function form($idpersona = null)
    {
        $departamentos = (new DepartamentoModel())->findAll();

        $data = ['departamentos' => $departamentos];

        if ($idpersona) {
            $persona = (new PersonaModel())
                ->select('personas.*, distritos.idprovincia, provincias.iddepartamento')
                ->join('distritos', 'personas.iddistrito = distritos.iddistrito')
                ->join('provincias', 'distritos.idprovincia = provincias.idprovincia')
                ->where('personas.idpersona', $idpersona)
                ->first();

            if (is_array($persona)) $persona = (object)$persona;

            $data['persona'] = $persona;
        }

        return view('Personas/form', $data);
    }

    public function guardar()
    {
        $data = $this->request->getPost();
        $personaModel = new PersonaModel();

        if (!empty($data['idpersona'])) {
            $data['modificado'] = date('Y-m-d H:i:s');
            $success = $personaModel->update($data['idpersona'], $data);
        } else {
            $success = $personaModel->insert($data);
        }

        return $this->response->setJSON([
            'success' => (bool)$success,
            'mensaje' => $success ? 'Persona guardada correctamente' : 'Error al guardar'
        ]);
    }

    public function eliminar()
    {
        $idpersona = $this->request->getPost('idpersona');
        $success = (new PersonaModel())->delete($idpersona);

        return $this->response->setJSON([
            'success' => (bool)$success,
            'mensaje' => $success ? 'Persona eliminada correctamente' : 'Error al eliminar'
        ]);
    }

    public function getProvincias($idDepartamento)
    {
        $provincias = (new ProvinciaModel())->where('iddepartamento', $idDepartamento)->findAll();
        return $this->response->setJSON($provincias);
    }

    public function getDistritos($idProvincia)
    {
        $distritos = (new DistritoModel())->where('idprovincia', $idProvincia)->findAll();
        return $this->response->setJSON($distritos);
    }
}
