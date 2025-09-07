<?php
namespace App\Models;
use CodeIgniter\Model;

class MedioModel extends Model {
    protected $table = 'medios';
    protected $primaryKey = 'idmedio';
    protected $allowedFields = ['nombre','descripcion'];
    protected $useTimestamps = true;
    
}
