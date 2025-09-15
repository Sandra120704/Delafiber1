<?php

namespace App\Models;

use CodeIgniter\Model;

class TareaModel extends Model
{
    protected $table      = 'tareas';
    protected $primaryKey = 'idtarea';
    protected $allowedFields = ['idlead', 'idusuario', 'descripcion', 'fecha_inicio', 'fecha_fin', 'estado'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}