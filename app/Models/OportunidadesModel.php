<?php

namespace App\Models;

use CodeIgniter\Model;

class OportunidadesModel extends Model
{
    protected $table      = 'oportunidades';
    protected $primaryKey = 'idoportunidad';
    protected $allowedFields = ['nombre', 'descripcion', 'idlead', 'valor_estimado', 'fecha_cierre', 'estado'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}