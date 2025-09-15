<?php

namespace App\Models;

use CodeIgniter\Model;

class DifunsionModel extends Model
{
    protected $table = 'difusiones';
    protected $primaryKey = 'iddifusion';
    protected $allowedFields = ['idcampania', 'idmedio', 'leads_generados'];

    public function getDifusionesCompletas()
    {
        return $this->db->table('difusiones d')
            ->select('d.iddifusion, c.nombre as campania_nombre, m.nombre as medio_nombre')
            ->join('campanias c', 'c.idcampania = d.idcampania', 'left')
            ->join('medios m', 'm.idmedio = d.idmedio', 'left')
            ->orderBy('c.nombre', 'ASC')
            ->orderBy('m.nombre', 'ASC')
            ->get()
            ->getResultArray();
    }
}
