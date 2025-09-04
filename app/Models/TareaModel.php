<?php
namespace App\Models;
use CodeIgniter\Model;

class TareaModel extends Model
{
    protected $table = 'tareas';
    protected $primaryKey = 'idtarea';
    protected $allowedFields = [
        'idlead', 'idusuario', 'descripcion', 'fecha_vencimiento', 'estado'
    ];

    public function getByLead($idlead)
    {
        return $this->db->table('tareas t')
            ->select('t.*, u.username, u.idusuario, CONCAT(p.nombres, " ", p.apellidos) as nombre_usuario')
            ->join('usuarios u', 'u.idusuario = t.idusuario')
            ->join('personas p', 'p.idpersona = u.idpersona')
            ->where('t.idlead', $idlead)
            ->get()
            ->getResult();
    }
}
