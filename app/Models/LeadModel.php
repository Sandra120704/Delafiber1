<?php
namespace App\Models;

use CodeIgniter\Model;

class LeadModel extends Model
{
    protected $table      = 'leads';
    protected $primaryKey = 'idlead';
    protected $allowedFields = [
    'idpersona',
    'idcampania',
    'idmedio',
    'idetapa',
    'referido_por',
    'fecha_registro',
    'estado',
    'idusuario',
    'idusuario_registro'
];

    public $timestamps = false;
    public function getLeadsConUsuarioYPersona()
    {
        return $this->db->table('leads')
            ->select('leads.*, personas.nombres, personas.apellidos, usuarios.usuario as usuario_responsable')
            ->join('personas', 'personas.idpersona = leads.idpersona')
            ->join('usuarios', 'usuarios.idusuario = leads.idusuario')
            ->get()
            ->getResult();
    }
    public function getLeadsConTodo()
    {
        return $this->select('
                leads.idlead,
                leads.idetapa,
                personas.nombres,
                personas.apellidos,
                personas.telefono,
                personas.correo,
                campanias.nombre as campana,
                medios.nombre as medio,
                usuarios.usuario
            ')
            ->join('personas', 'personas.idpersona = leads.idpersona')
            ->join('usuarios', 'usuarios.idusuario = leads.idusuario')
            ->join('campanias', 'campanias.idcampania = leads.idcampania', 'left')
            ->join('medios', 'medios.idmedio = leads.idmedio', 'left')
            ->findAll();
    }

}
