<?php

namespace App\Models;
use CodeIgniter\Model;

class CampanaModel extends Model
{
    protected $table = 'campanias';
    protected $primaryKey = 'idcampania';
    protected $allowedFields = ['nombre','descripcion','fecha_inicio','fecha_fin','presupuesto','estado'];

    // Guardar medios/difusiones de una campaña
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


    // Obtener medios/difusiones de una campaña
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

    //  Contar campañas activas
    public function contarActivas()
    {
        return $this->db->table($this->table)
            ->where('estado', 'Activo')
            ->countAllResults(false); // reset builder
    }

    /* public function eliminarCampana($idcampania)
    {
        $builder = $this->db->table('difusiones');
        // Eliminar difusiones asociadas
        $builder->where('idcampania', $idcampania)->delete();

        // Eliminar la campaña
        $this->delete($idcampania);
    } */

}
