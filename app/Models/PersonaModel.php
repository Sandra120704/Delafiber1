<?php

namespace App\Models;

use CodeIgniter\Model;

// Modelo para gestionar personas en el CRM
// TODO: Agregar validación para números de teléfono peruanos
class PersonaModel extends Model
{
    protected $table = 'personas';
    protected $primaryKey = 'idpersona';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'nombres',
        'apellidos', 
        'dni',
        'correo',
        'telefono',
        'direccion',
        'iddistrito',
        'referencias',
        'created_at',
        'updated_at'
    ];
    
    protected $useTimestamps = true; // Activa timestamps
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'dni' => 'required|exact_length[8]|is_unique[personas.dni,idpersona,{idpersona}]',
        'nombres' => 'required|max_length[100]',
        'apellidos' => 'required|max_length[100]',
        'correo' => 'permit_empty|valid_email|max_length[150]',
        'telefono' => 'required|max_length[20]',
        'iddistrito' => 'permit_empty|is_not_unique[distritos.iddistrito]'
    ];
    
    protected $validationMessages = [
        'dni' => [
            'required' => 'El DNI es obligatorio',
            'exact_length' => 'El DNI debe tener 8 dígitos',
            'is_unique' => 'Este DNI ya está registrado'
        ],
        'nombres' => [
            'required' => 'Los nombres son obligatorios',
            'max_length' => 'Los nombres no pueden exceder 100 caracteres'
        ],
        'apellidos' => [
            'required' => 'Los apellidos son obligatorios', 
            'max_length' => 'Los apellidos no pueden exceder 100 caracteres'
        ],
        'correo' => [
            'valid_email' => 'El formato del correo no es válido',
            'max_length' => 'El correo no puede exceder 150 caracteres'
        ],
        'telefono' => [
            'required' => 'El teléfono es obligatorio',
            'max_length' => 'El teléfono no puede exceder 20 caracteres'
        ]
    ];
    
    protected $skipValidation = false;
    protected $cleanValidationRules = true;
    
    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    // Buscar personas por término de búsqueda
    public function buscarPersonas($termino)
    {
        return $this->like('nombres', $termino)
                   ->orLike('apellidos', $termino)
                   ->orLike('dni', $termino)
                   ->orLike('telefono', $termino)
                   ->orLike('correo', $termino)
                   ->findAll();
    }

     //Obtener persona con información del distrito
    public function getPersonaConDistrito($idpersona)
    {
        return $this->select('personas.*, distritos.nombre as distrito_nombre, provincias.nombre as provincia_nombre, departamentos.nombre as departamento_nombre')
                   ->join('distritos', 'distritos.iddistrito = personas.iddistrito', 'left')
                   ->join('provincias', 'provincias.idprovincia = distritos.idprovincia', 'left')
                   ->join('departamentos', 'departamentos.iddepartamento = provincias.iddepartamento', 'left')
                   ->find($idpersona);
    }
    
    
    //Verificar si el DNI ya existe
    public function dniExiste($dni, $excluirId = null)
    {
        $builder = $this->where('dni', $dni);
        
        if ($excluirId) {
            $builder->where('idpersona !=', $excluirId);
        }
        
        return $builder->countAllResults() > 0;
    }
}