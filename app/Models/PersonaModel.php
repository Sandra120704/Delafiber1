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
    public function obtenerPersona() {
        return $this->select('personas.*, distritos.distrito, provincias.provincia, departamentos.departamento')
                    ->join('distritos', 'personas.iddistrito = distritos.iddistrito')
                    ->join('provincias', 'distritos.idprovincia = provincias.idprovincia')
                    ->join('departamentos', 'provincias.iddepartamento = departamentos.iddepartamento')
                    ->findAll();
    }

}
