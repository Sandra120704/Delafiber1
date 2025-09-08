<?php

namespace App\Models;

use CodeIgniter\Model;

class EtapaModel extends Model
{
    protected $table = 'etapas';
    protected $primaryKey = 'idetapa';
    protected $allowedFields = ['nombre', 'orden', 'idpipeline', 'activo'];

    /**
     * Obtener todas las etapas de un pipeline específico
     */
    public function getEtapasPorPipeline($idpipeline)
    {
        return $this->where('idpipeline', $idpipeline)
                    ->where('activo', 1)
                    ->orderBy('orden', 'ASC')
                    ->findAll();
    }

    /**
     * Obtener la etapa inicial de un pipeline
     */
    public function getEtapaInicial($idpipeline)
    {
        return $this->where('idpipeline', $idpipeline)
                    ->where('activo', 1)
                    ->orderBy('orden', 'ASC')
                    ->first();
    }

    /**
     * Obtener todas las etapas activas (general)
     */
    public function getEtapasIniciales()
    {
        return $this->where('activo', 1)
                    ->orderBy('orden', 'ASC')
                    ->findAll();
    }
}
