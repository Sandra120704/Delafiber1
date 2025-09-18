<?php
namespace App\Models;
use CodeIgniter\Model;

class DistritoModel extends Model
{
    protected $table = 'distritos';
    protected $primaryKey = 'iddistrito';
    protected $allowedFields = ['nombre', 'idprovincia'];
    
    public function getDistritosConProvincia()
    {
        return $this->select('distritos.*, provincias.nombre as provincia')
                   ->join('provincias', 'provincias.idprovincia = distritos.idprovincia')
                   ->findAll();
    }
}