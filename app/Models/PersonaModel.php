<?php

namespace App\Models;
use CodeIgniter\Model;

class PersonaModel extends Model
{
    protected $table      = 'personas';
    protected $primaryKey = 'idpersona';
    protected $returnType = 'object';
    protected $allowedFields = [
        'apellidos',
        'nombres',
        'telprimario',
        'telalternativo',
        'email',
        'direccion',
        'referencia',
        'iddistrito'
    ];

    // Obtener personas con su distrito, provincia y departamento
    public function obtenerPersona()
        {
            return $this->select('p.idpersona, p.apellidos, p.nombres, p.telprimario, p.email, d.distrito, pr.provincia, dep.departamento')
                ->from('personas p')
                ->join('distritos d', 'p.iddistrito = d.iddistrito')
                ->join('provincias pr', 'd.idprovincia = pr.idprovincia')
                ->join('departamentos dep', 'pr.iddepartamento = dep.iddepartamento')
                ->groupBy('p.idpersona')  
                ->orderBy('p.idpersona', 'DESC')
                ->get()
                ->getResult();
    }
}
