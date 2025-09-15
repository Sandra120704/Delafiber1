<?php

namespace App\Models;

use CodeIgniter\Model;

class EtapaModel extends Model
{
    protected $table = 'etapas';
    protected $primaryKey = 'idetapa';
    protected $allowedFields = ['nombre', 'orden', 'idpipeline', 'activo'];

    public function getEtapasPorPipeline($idpipeline)
    {
        return $this->where('idpipeline', $idpipeline)
                    ->where('activo', 1)
                    ->orderBy('orden', 'ASC')
                    ->findAll();
    }
    
    public function getEtapasActivas()
    {
        return $this->orderBy('orden', 'ASC')->findAll();
    }

    public function getEtapaInicial($idpipeline)
    {
        return $this->where('idpipeline', $idpipeline)
                    ->where('activo', 1)
                    ->orderBy('orden', 'ASC')
                    ->first();
    }

    public function getEtapasIniciales()
    {
        return $this->where('activo', 1)
                    ->orderBy('orden', 'ASC')
                    ->findAll();
    }
}
