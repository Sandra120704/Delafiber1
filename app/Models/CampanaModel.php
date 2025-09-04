<?php
namespace App\Models;

use CodeIgniter\Model;

class CampanaModel extends Model {
    protected $table = 'campanias';
    protected $primaryKey = 'idcampania';
    protected $allowedFields = ['nombre','descripcion','fechainicio','fechafin','inversion','estado'];
    protected $useTimestamps = true;
    protected $createdField  = 'creado';
    protected $updatedField  = 'modificado';
    
    public function getCampanasConMedios() {
        return $this->select('campanias.idcampania, campanias.nombre, medios.medio AS medio, campanias.estado')
                    ->join('difusiones', 'campanias.idcampania = difusiones.idcampania')
                    ->join('medios', 'difusiones.idmedio = medios.idmedio')
                    ->findAll();
    }
}
