<?php
namespace App\Models;
use CodeIgniter\Model;

class TareaModel extends Model
{
    protected $table = 'tareas';
    protected $primaryKey = 'idtarea';
    protected $allowedFields = [
        'idlead', 'descripcion', 'idusuarioresponsable', 'fecha_limite', 'estado'
    ];

    // Obtener tareas de un lead
    public function getByLead($idlead)
    {
        return $this->where('idlead', $idlead)
                    ->join('usuarios', 'usuarios.idusuario = tareas.idusuarioresponsable')
                    ->select('tareas.*, usuarios.username as usuario')
                    ->findAll();
    }
}
