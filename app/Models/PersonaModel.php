<?php
namespace App\Models;

use CodeIgniter\Model;

class PersonaModel extends Model
{
    protected $table = 'personas';
    protected $primaryKey = 'idpersona';
    protected $allowedFields = [
        'nombres','apellidos','dni','correo','telefono','direccion','iddistrito'
    ];
}
