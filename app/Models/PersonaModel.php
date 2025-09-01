<?php

namespace App\Models;
use CodeIgniter\Model;

class PersonaModel extends Model
{
    public function obtenerPersona()
    {
        return $this->db->table('persona p')
                        ->select('p.idpersona, p.apellidos, p.nombres, d.distrito, pr.provincias, dep.departamento')
                        ->join('distritos d', 'p.iddistrito = d.iddistrito')
                        ->join('provincias pr', 'd.idprovincias = pr.idprovincias')
                        ->join('departamento dep', 'pr.iddepartamento = dep.idDepartamento') // 👈 ojo con mayúscula
                        ->get()
                        ->getResult();
    }
}
