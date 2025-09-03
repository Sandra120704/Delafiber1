<?php

namespace App\Controllers;
use App\Models\CampanaModel;

class CampanaController extends BaseController
{
    public function index()
    {
        $model = new CampanaModel();
        $campanas = $model->orderBy('creado', 'DESC')->findAll();

        // Convertir cada elemento en objeto
        $campanas = array_map(fn($c) => (object) $c, $campanas);

        $data['header'] = view('Layouts/header');
        $data['footer'] = view('Layouts/footer');
        $data['campanas'] = $campanas;

        return view('Campanas/index', $data);
    }
    public function form($idcampania = null)
    {
        $model = new CampanaModel();
        $data = [];

        if ($idcampania) {
            $campania = $model->find($idcampania);
            if (is_array($campania)) {
                $campania = (object) $campania; // <--- convertir a objeto
            }
            $data['campania'] = $campania;
        }

        return view('Campanas/crear', $data);
    }


    public function guardar()
    {
        $model = new CampanaModel();
        $data = $this->request->getPost();

        if (!empty($data['idcampania'])) {
            $data['modificado'] = date('Y-m-d H:i:s');
            $success = $model->update($data['idcampania'], $data);
        } else {
            $data['creado'] = date('Y-m-d H:i:s');
            $success = $model->insert($data);
        }

        return $this->response->setJSON([
            'success' => $success ? true : false,
            'mensaje' => $success ? 'Campaña guardada correctamente' : 'Error al guardar'
        ]);
    }

    public function eliminar()
    {
        $idcampania = $this->request->getPost('idcampania');
        $model = new CampanaModel();
        $success = $model->delete($idcampania);

        return $this->response->setJSON([
            'success' => $success ? true : false,
            'mensaje' => $success ? 'Campaña eliminada' : 'Error al eliminar'
        ]);
    }
    public function cambiarEstado()
    {
        $idcampania = $this->request->getPost('idcampania');
        $estado = $this->request->getPost('estado');

        $model = new CampanaModel();
        $success = $model->update($idcampania, ['estado' => $estado, 'modificado' => date('Y-m-d H:i:s')]);

        return $this->response->setJSON([
            'success' => $success ? true : false,
            'mensaje' => $success ? 'Estado actualizado' : 'Error al actualizar'
        ]);
    }

}
