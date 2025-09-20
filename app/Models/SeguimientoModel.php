<?php

namespace App\Models;

use CodeIgniter\Model;

class SeguimientoModel extends Model
{
    protected $table = 'seguimiento'; 
    protected $primaryKey = 'idseguimiento';
    protected $allowedFields = [
        'idlead', 
        'idusuario', 
        'idmodalidad', 
        'nota', 
        'fecha'];
    protected $useTimestamps = false;
}