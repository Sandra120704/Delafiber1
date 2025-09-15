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
        'idmodalidad',
        'idetapa',
        'idusuario',
        'idusuario_registro',
        'referido_por',
        'estado'
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
                modalidades.nombre AS modalidad,
                origenes.nombre AS origen,
                usuarios.usuario AS usuario_registro
            ')
            ->join('personas', 'personas.idpersona = leads.idpersona')
            ->join('usuarios', 'usuarios.idusuario = leads.idusuario_registro', 'left')
            ->join('origenes', 'origenes.idorigen = leads.idorigen', 'left')
            ->join('campanias', 'campanias.idcampania = leads.idcampania', 'left')
            ->join('modalidades', 'modalidades.idmodalidad = leads.idmodalidad', 'left')
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
                modalidades.nombre AS modalidad,
                origenes.nombre AS origen,
                usuarios.usuario AS usuario_registro
            ')
            ->join('personas', 'personas.idpersona = leads.idpersona')
            ->join('usuarios', 'usuarios.idusuario = leads.idusuario_registro', 'left')
            ->join('origenes', 'origenes.idorigen = leads.idorigen', 'left')
            ->join('campanias', 'campanias.idcampania = leads.idcampania', 'left')
            ->join('modalidades', 'modalidades.idmodalidad = leads.idmodalidad', 'left')
            ->orderBy('leads.idetapa, leads.idlead')
            ->findAll();

        $porEtapa = [];
        foreach ($leads as $lead) {
            $porEtapa[$lead['idetapa']][] = $lead;
        }
        return $porEtapa;
    }

    /**
     * Actualiza la etapa de un lead
     */
    public function actualizarEtapa($idlead, $idetapa)
    {
        return $this->update($idlead, ['idetapa' => $idetapa]);
    }
}
