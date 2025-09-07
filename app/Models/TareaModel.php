<?php
namespace App\Models;
use CodeIgniter\Model;

class TareaModel extends Model
{
    protected $table = 'tareas';
    protected $primaryKey = 'idtarea';
    protected $allowedFields = [
        'idusuario', 'idlead', 'descripcion', 'fecha_programada', 'estado'
    ];
}
