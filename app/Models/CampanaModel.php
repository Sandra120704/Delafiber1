<?php
namespace App\Models;
use CodeIgniter\Model;

class CampanaModel extends Model {
    protected $table = 'campanias';
    protected $primaryKey = 'idcampania';
    protected $allowedFields = ['nombre','descripcion','fecha_inicio','fecha_fin','presupuesto','estado'];
    protected $useTimestamps = true;
    
    // Obtener todas las campañas con conteo de leads
    public function getCampanasConLeads()
    {
        $builder = $this->db->table('campanias c');
        $builder->select('c.*, COUNT(l.idlead) AS total_leads');
        $builder->join('leads l', 'l.idcampania = c.idcampania', 'left');
        $builder->groupBy('c.idcampania');
        $builder->orderBy('c.fecha_inicio', 'DESC');
        return $builder->get()->getResultArray();
    }

    // Contar campañas activas
    public function contarActivas()
    {
        return $this->where('estado', 'Activo')->countAllResults();
    }

    // Sumar presupuesto total
    public function presupuestoTotal()
    {
        $result = $this->selectSum('presupuesto')->first();
        return $result['presupuesto'] ?? 0;
    }

    // Obtener campaña por ID
    public function getCampana($id)
    {
        return $this->find($id);
    }

}
