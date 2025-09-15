<?php
namespace App\Models;

use CodeIgniter\Model;

class PersonaModel extends Model
{
    protected $table            = 'personas';
    protected $primaryKey       = 'idpersona';
    protected $useAutoIncrement = true;

    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = [
        'nombres',
        'apellidos',
        'dni',
        'correo',
        'telefono',
        'direccion',
        'referencias',
        'iddistrito',
    ];

    //Validaciones que se realizan automáticamente
    protected $validationRules = [
        'dni'        => 'required|exact_length[8]|is_unique[personas.dni,idpersona,{idpersona}]',
        'nombres'    => 'required|min_length[2]',
        'apellidos'  => 'required|min_length[2]',
        'correo'     => 'permit_empty|valid_email',
        'telefono'   => 'permit_empty|min_length[6]|max_length[9]',
        'iddistrito' => 'permit_empty|is_natural_no_zero',
    ];

    protected $validationMessages = [
        'dni' => [
            'required'    => 'El DNI es obligatorio.',
            'exact_length'=> 'El DNI debe tener exactamente 8 dígitos.',
            'is_unique'   => 'El DNI ya está registrado en otra persona.',
        ],
        'nombres' => [
            'required'   => 'El nombre es obligatorio.',
            'min_length' => 'El nombre debe tener al menos 2 caracteres.',
        ],
        'apellidos' => [
            'required'   => 'El apellido es obligatorio.',
            'min_length' => 'El apellido debe tener al menos 2 caracteres.',
        ],
        'correo' => [
            'valid_email' => 'Debes ingresar un correo válido.',
        ],
        'telefono' => [
            'min_length' => 'El teléfono debe tener al menos 6 dígitos.',
            'max_length' => 'El teléfono no debe superar los 15 caracteres.',
        ],
    ];

    protected $skipValidation = false;
}
