<?php
namespace App\Models;

use CodeIgniter\Model;

class DifunsionModel extends Model
{
    protected $table = 'difusiones';
    protected $primaryKey = 'iddifusion';
    protected $allowedFields = ['idcampania', 'idmedio', 'descripcion'];

    public function getDifusionesCompletas()
    {
        return $this->db->table('difusiones d')
            ->select('d.iddifusion, c.nombre as campania_nombre, m.nombre as medio_nombre, d.descripcion')
            ->join('campanias c', 'c.idcampania = d.idcampania', 'left')
            ->join('medios m', 'm.idmedio = d.idmedio', 'left')
            ->orderBy('c.nombre', 'ASC')
            ->orderBy('m.nombre', 'ASC')
            ->get()
            ->getResultArray();
    }
}
