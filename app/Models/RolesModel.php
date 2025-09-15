<?php

namespace App\Models;

use CodeIgniter\Model;

class RolesModel extends Model
{
    protected $table      = 'roles';
    protected $primaryKey = 'idrol';
    protected $allowedFields = ['nombre', 'descripcion'];
    protected $useTimestamps = false; 
}