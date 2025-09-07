<?php
namespace App\Controllers;
use App\Models\ProvinciaModel;
use App\Models\DistritoModel;

class UbicacionController extends BaseController
{
    public function getProvincias($idDepartamento)
    {
        $model = new ProvinciaModel();
        return $this->response->setJSON(
            $model->where('iddepartamento', $idDepartamento)->findAll()
        );
    }

    public function getDistritos($idProvincia)
    {
        $model = new DistritoModel();
        return $this->response->setJSON(
            $model->where('idprovincia', $idProvincia)->findAll()
        );
    }
}
