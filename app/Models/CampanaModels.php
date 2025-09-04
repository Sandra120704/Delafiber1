<?php
namespace App\Models;

use CodeIgniter\Model;

class CampanaModels extends Model {
    protected $table = 'campanias';
    protected $primaryKey = 'idcampania';
    protected $allowedFields = ['nombre','descripcion','fechainicio','fechafin','inversion','estado'];
    protected $useTimestamps = true; // para los campos creado y modificado
    protected $createdField  = 'creado';
    protected $updatedField  = 'modificado';
}
