<?php

namespace App\Models;

use CodeIgniter\Model;

class Origen extends Model
{
    protected $table      = 'origenes';      // Nombre de la tabla
    protected $primaryKey = 'idorigen';      // Clave primaria

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';      // Devuelve resultados como array
    protected $useSoftDeletes = false;

    protected $allowedFields = ['nombre'];    // Campos que se pueden insertar/actualizar

    protected $useTimestamps = false;         // No tiene created_at ni updated_at por defecto
}
