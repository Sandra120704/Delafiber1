<?php
namespace App\Models;

use CodeIgniter\Model;

class LeadModel extends Model
{
    protected $table      = 'leads';
    protected $primaryKey = 'idlead';
    protected $allowedFields = ['idpersona', 'idcampania', 'idmedio', 'idetapa', 'idusuario', 'estado'];

    public function getLeadsConUsuarioYPersona()
    {
        return $this->db->table('leads')
            ->select('leads.*, personas.nombres, personas.apellidos, usuarios.usuario as usuario_responsable')
            ->join('personas', 'personas.idpersona = leads.idpersona')
            ->join('usuarios', 'usuarios.idusuario = leads.idusuario')
            ->get()
            ->getResult();
    }
}
