<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuarioModel extends Model
{
    protected $table      = 'usuarios';
    protected $primaryKey = 'idusuario';
    protected $allowedFields = ['usuario', 'clave', 'idrol', 'idpersona'];
    protected $useTimestamps = true;
    protected $createdField  = 'fecha_creacion'; // Si tienes estas columnas en tu tabla
    protected $updatedField  = 'fecha_modificacion'; // Si tienes estas columnas en tu tabla
}