<?php
namespace App\Models;

use CodeIgniter\Model;

class LeadModel extends Model
{
    protected $table          = 'leads';
    protected $primaryKey     = 'idlead';
    protected $allowedFields = [
        'idpersona',
        'idorigen', 
        'idcampania', 
        'medio_comunicacion', 
        'idmodalidad',
        'idetapa',
        'idusuario',
        'idusuario_registro',
        'referido_por',
        'estado',
        'idreferido'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'fecha_creacion';
    protected $updatedField  = 'fecha_modificacion';

    public function getLeadsConTodo()
    {
        return $this->select('
                leads.idlead,
                leads.idetapa,
                personas.nombres,
                personas.apellidos,
                personas.telefono,
                personas.correo,
                campanias.nombre AS campana,
                difusiones.medio AS medio,
                origenes.nombre AS origen,
                usuarios.usuario
            ')
            ->join('personas', 'personas.idpersona = leads.idpersona')
            ->join('usuarios', 'usuarios.idusuario = leads.idusuario', 'left')
            ->join('origenes', 'origenes.idorigen = leads.idorigen', 'left') // Nueva unión
            ->join('campanias', 'campanias.idcampania = leads.idcampania', 'left') // Nueva unión
            ->join('difusiones', 'difusiones.iddifusion = leads.iddifusion', 'left')
            ->findAll();
    }

    public function getLeadsPorEtapa()
    {
        $leads = $this->select('
                leads.idlead,
                leads.idetapa,
                personas.nombres,
                personas.apellidos,
                personas.telefono,
                personas.correo,
                campanias.nombre AS campana,
                difusiones.descripcion AS difusion_descripcion,
                origenes.nombre AS origen,
                usuarios.usuario
            ')
            ->join('personas', 'personas.idpersona = leads.idpersona')
            ->join('usuarios', 'usuarios.idusuario = leads.idusuario', 'left')
            ->join('origenes', 'origenes.idorigen = leads.idorigen', 'left') // Nueva unión
            ->join('campanias', 'campanias.idcampania = leads.idcampania', 'left') // Nueva unión
            ->join('difusiones', 'difusiones.iddifusion = leads.iddifusion', 'left')
            ->orderBy('leads.idetapa, leads.idlead')
            ->findAll();

        $porEtapa = [];
        foreach ($leads as $lead) {
            $porEtapa[$lead['idetapa']][] = $lead;
        }
        return $porEtapa;
    }

    public function actualizarEtapa($idlead, $idetapa)
    {
        return $this->update($idlead, ['idetapa' => $idetapa]);
    }
}
