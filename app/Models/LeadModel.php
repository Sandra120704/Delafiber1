<?php
namespace App\Models;

use CodeIgniter\Model;

class LeadModel extends Model
{
    protected $table = 'leads';
    protected $primaryKey = 'idlead';
    protected $allowedFields = [
        'idpersona', 'iddifusion', 'idusuarioregistro', 'idusuarioresponsable',
        'idetapa', 'fechasignacion', 'estatus_global'
    ];
    protected $useTimestamps = false;

    // Devuelve una persona por ID
    public function getPersona($idpersona)
    {
        return $this->db->table('personas')
            ->where('idpersona', $idpersona)
            ->get()
            ->getRow();
    }

    // Traer todos los pipelines
    public function getPipelines()
    {
        return $this->db->table('pipelines')->get()->getResult();
    }

    // Traer todas las etapas
    public function getEtapas()
    {
        return $this->db->table('etapas')
            ->orderBy('idetapa', 'ASC')
            ->get()
            ->getResult();
    }

    // Traer todos los leads con datos de persona, difusión, campaña y medio
    public function getAllLeads()
    {
        $leads = $this->db->table('leads')
            ->select('leads.*, personas.nombres, personas.apellidos, personas.telprimario as telefono, personas.email, campanias.nombre as campaña, medios.medio as medio, estatus_global')
            ->join('personas', 'personas.idpersona = leads.idpersona')
            ->join('difusiones', 'difusiones.iddifusion = leads.iddifusion')
            ->join('campanias', 'campanias.idcampania = difusiones.idcampania')
            ->join('medios', 'medios.idmedio = difusiones.idmedio')
            ->get()
            ->getResult();

        // Asignar color según estatus_global
        foreach ($leads as $lead) {
            switch ($lead->estatus_global) {
                case 'nuevo':
                    $lead->estatus_color = '#f0ad4e';
                    break;
                case 'en proceso':
                    $lead->estatus_color = '#5bc0de';
                    break;
                case 'ganado':
                    $lead->estatus_color = '#5cb85c';
                    break;
                case 'perdido':
                    $lead->estatus_color = '#d9534f';
                    break;
                default:
                    $lead->estatus_color = '#ccc';
            }
        }

        return $leads;
    }

    // Traer detalles de un lead específico con persona
    public function getLeadConPersona($id)
    {
        $lead = $this->db->table('leads')
            ->select('leads.*, personas.nombres, personas.apellidos, personas.telprimario as telefono, personas.email, campanias.nombre as campaña, medios.medio as medio, estatus_global')
            ->join('personas', 'personas.idpersona = leads.idpersona')
            ->join('difusiones', 'difusiones.iddifusion = leads.iddifusion')
            ->join('campanias', 'campanias.idcampania = difusiones.idcampania')
            ->join('medios', 'medios.idmedio = difusiones.idmedio')
            ->where('leads.idlead', $id)
            ->get()
            ->getRow();

        if ($lead) {
            switch ($lead->estatus_global) {
                case 'nuevo':
                    $lead->estatus_color = '#f0ad4e';
                    break;
                case 'en proceso':
                    $lead->estatus_color = '#5bc0de';
                    break;
                case 'ganado':
                    $lead->estatus_color = '#5cb85c';
                    break;
                case 'perdido':
                    $lead->estatus_color = '#d9534f';
                    break;
                default:
                    $lead->estatus_color = '#ccc';
            }
        }

        return $lead;
    }

    // Traer personas para selects
    public function getPersonas()
    {
        return $this->db->table('personas')->get()->getResult();
    }

    // Traer difusiones para selects
    public function getDifusiones()
    {
        $builder = $this->db->table('difusiones d');
        $builder->select('d.iddifusion, c.nombre as campania, m.medio');
        $builder->join('campanias c', 'c.idcampania = d.idcampania');
        $builder->join('medios m', 'm.idmedio = d.idmedio');
        return $builder->get()->getResult();
    }

    // Traer usuarios para selects
    public function getUsuarios()
    {
        return $this->db->table('usuarios')
            ->select('usuarios.idusuario, usuarios.username, personas.nombres, personas.apellidos')
            ->join('personas', 'personas.idpersona = usuarios.idpersona')
            ->get()
            ->getResult();
    }
}
