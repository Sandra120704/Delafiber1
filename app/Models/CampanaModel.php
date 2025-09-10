<?php

namespace App\Models;
use CodeIgniter\Model;

class CampanaModel extends Model
{
    protected $table = 'campanias';
    protected $primaryKey = 'idcampania';
    protected $allowedFields = ['nombre','descripcion','fecha_inicio','fecha_fin','presupuesto','estado','fecha_creacion'];

    public function guardarDifusiones($idcampania, $medios)
    {
        $builder = $this->db->table('difusiones');

        // Eliminar registros previos
        $builder->where('idcampania', $idcampania)->delete();

        // Insertar nuevos
        foreach($medios as $medio){
            if(!isset($medio['idmedio']) || empty($medio['idmedio'])) continue; 

            $builder->insert([
                'idcampania' => $idcampania,
                'idmedio' => $medio['idmedio'],
                'inversion' => $medio['inversion'] ?? 0,
                'leads_generados' => $medio['leads_generados'] ?? 0,
                'creado' => date('Y-m-d H:i:s')
            ]);
        }
    }

    public function getMedios($idcampania)
    {
        return $this->db->table('difusiones as d')
            ->select('m.nombre, d.inversion, d.leads_generados as leads')
            ->join('medios as m', 'm.idmedio = d.idmedio')
            ->where('d.idcampania', $idcampania)
            ->get()
            ->getResultArray();
    }

     public function eliminarCampana($idcampania)
    {
        $this->db->table('difusiones')->where('idcampania', $idcampania)->delete();
        $this->delete($idcampania);
    }

    public function contarActivas()
    {
        return $this->db->table($this->table)
            ->where('estado', 'Activo')
            ->countAllResults(false); 
    }
    /* public function eliminarCampana($idcampania)
    {
        $builder = $this->db->table('difusiones');
        // Eliminar difusiones asociadas
        $builder->where('idcampania', $idcampania)->delete();

        // Eliminar la campaña
        $this->delete($idcampania);
    } */
    public function presupuestoTotal()
    {
        return $this->selectSum('presupuesto')->get()->getRow()->presupuesto ?? 0;
    }

    public function totalLeads()
    {
        return $this->db->table('difusiones')
            ->selectSum('leads_generados')
            ->get()
            ->getRow()
            ->leads_generados ?? 0;
    }

    public function getDetalle($idcampania)
    {
        $campania = $this->find($idcampania);
        if ($campania) {
            $campania['medios'] = $this->getMedios($idcampania);
        }
        return $campania;
    }

    public function cambiarEstado($idcampania, $estado)
    {
        return $this->update($idcampania, ['estado' => $estado]);
    }
}
