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
        if(!isset($medio['idmedio']) || empty($medio['idmedio'])) continue; // 🔒 evita error

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
        $builder = $this->db->table('difusiones as d');
        $builder->select('m.nombre, d.inversion, d.leads_generados');
        $builder->join('medios as m', 'm.idmedio = d.idmedio');
        $builder->where('d.idcampania', $idcampania);
        return $builder->get()->getResultArray();
    }
    public function eliminarCampana($idcampania)
    {
        $builder = $this->db->table('difusiones');
        // Eliminar difusiones asociadas
        $builder->where('idcampania', $idcampania)->delete();

        // Eliminar la campaña
        $this->delete($idcampania);
    }

}
