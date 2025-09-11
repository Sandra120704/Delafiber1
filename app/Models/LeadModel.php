<?php
namespace App\Models;

use CodeIgniter\Model;

class LeadModel extends Model
{
    protected $table      = 'leads';
    protected $primaryKey = 'idlead';
    protected $allowedFields = [
        'idpersona',
        'iddifusion',
        'idetapa',
        'idusuario',
        'idusuario_registro',
        'idmodalidad',
        'referido_por',
        'estado',
        'idreferido'
    ];

    public $timestamps = false;

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
                medios.nombre AS medio,
                usuarios.usuario
            ')
            ->join('personas', 'personas.idpersona = leads.idpersona')
            ->join('usuarios', 'usuarios.idusuario = leads.idusuario', 'left')
            ->join('difusiones', 'difusiones.iddifusion = leads.iddifusion', 'left')
            ->join('campanias', 'campanias.idcampania = difusiones.idcampania', 'left')
            ->join('medios', 'medios.idmedio = difusiones.idmedio', 'left')
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
                medios.nombre AS medio,
                usuarios.usuario,
                difusiones.descripcion AS difusion_descripcion
            ')
            ->join('personas', 'personas.idpersona = leads.idpersona')
            ->join('usuarios', 'usuarios.idusuario = leads.idusuario', 'left')
            ->join('difusiones', 'difusiones.iddifusion = leads.iddifusion', 'left')
            ->join('campanias', 'campanias.idcampania = difusiones.idcampania', 'left')
            ->join('medios', 'medios.idmedio = difusiones.idmedio', 'left')
            ->orderBy('leads.idetapa, leads.idlead')
            ->findAll();

        // Agrupar por etapa
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
